<?php

namespace MattSu\ScheduleAssistant\Listeners;

use Illuminate\Console\Events\ScheduledTaskFailed;
use Illuminate\Console\Events\ScheduledTaskFinished;
use Illuminate\Console\Events\ScheduledTaskSkipped;
use Illuminate\Console\Events\ScheduledTaskStarting;
use MattSu\ScheduleAssistant\models\ScheduledAssistant;
use MattSu\ScheduleAssistant\models\ScheduledAssistantTask;
use \Carbon\Carbon;
use \Cron\CronExpression;

class RecordScheduleStartingStatus
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  \App\Events\OrderShipped  $event
     * @return void
     */
    public function handle(ScheduledTaskStarting $event)
    {

        //Schedule 下 Withoutoverlapping 如果有排程死掉，
        //但是排程手動下能正常執行時，
        //到資料表scheduled_events找出key然後可以利用 php artisan tinker 直接下Cache::forget($mutex_cache_key);
        // $mutex_cache_key = 'framework' . DIRECTORY_SEPARATOR . 'schedule-' . sha1($event->expression . $event->command);
        $notTrack = $event->task->notTrack ?? 0;
        $command = substr($event->task->command, strpos($event->task->command, 'artisan') + strlen('artisan') + 1);
        $command = trim($command);
        $mutexName = $event->task->mutexName();
        $fileName = substr($command, strpos($command, ':') + 1);

        //拿冒號後的
        if (!file_exists(storage_path('logs/' . $fileName . '.log'))) {
            fopen(storage_path('logs/' . $fileName . '.log'), "w");
        }
        $path = storage_path('logs/' . $fileName . '.log');
        $event->task->sendOutputTo($path);
        $cron = CronExpression::factory($event->task->expression);
        $date = Carbon::now();
        if ($event->task->timezone) {
            $date->setTimezone($event->task->timezone);
        }
        $uuid = uniqid();
        $event->task->setUUID($uuid);
        $curTime = new \DateTime();
        $created_at = $curTime->format("Y-m-d H:i:s");
        $upperLimitsOfNormalMinutes = $event->task->upperLimitsOfNormalMinutes;
        $nextRunAt = $cron->getNextRunDate()->format('Y-m-d H:i:s');
        $scheduledAssistantTask = ScheduledAssistantTask::where("command", $command)->first();
        if (empty($scheduledAssistantTask)) {
            $scheduledAssistantTask = new ScheduledAssistantTask;
            $scheduledAssistantTask->command = $command;
            $scheduledAssistantTask->mutex_cache_key = $mutexName;
            $scheduledAssistantTask->upperLimitsOfNormalMinutes = $upperLimitsOfNormalMinutes;
            $scheduledAssistantTask->notTrack = $notTrack;
            $scheduledAssistantTask->nextRunAt = $nextRunAt;
            $scheduledAssistantTask->save();
        } else {
            $scheduledAssistantTask = ScheduledAssistantTask::find($scheduledAssistantTask->id);
            $scheduledAssistantTask->nextRunAt = $nextRunAt;
            $scheduledAssistantTask->save();
        }
        //更新nextRunAt
        $scheduledAssistant = ScheduledAssistant::create([
            'scheduled_assistant_task_id' => $scheduledAssistantTask->id,
            'type' => 'starting',
            'command' => $command,
            'logged_at' => $created_at,
            'mutex_cache_key' => $mutexName,
            // 'output' =>  file_get_contents($event->task->output),
            'output' =>  '',
            'upperLimitsOfNormalMinutes' => $event->task->upperLimitsOfNormalMinutes,
            'notTrack' => $notTrack,
            'nextRunAt' => $cron->getNextRunDate()->format('Y-m-d H:i:s'),
            'uuid' => $uuid
        ]);
    }
}
