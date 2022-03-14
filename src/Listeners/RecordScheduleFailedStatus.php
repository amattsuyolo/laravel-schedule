<?php

namespace MattSu\ScheduleAssistant\Listeners;

use Illuminate\Console\Events\ScheduledTaskFailed;
use Illuminate\Console\Events\ScheduledTaskFinished;
use Illuminate\Console\Events\ScheduledTaskSkipped;
use Illuminate\Console\Events\ScheduledTaskStarting;
use MattSu\ScheduleAssistant\models\ScheduledAssistant;
use MattSu\ScheduleAssistant\models\ScheduledAssistantTask;

class RecordScheduleFailedStatus
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
    public function handle(ScheduledTaskFailed $event)
    {
        //Schedule 下 Withoutoverlapping 如果有排程死掉，
        //但是排程手動下能正常執行時，
        //到資料表scheduled_events找出key然後可以利用 php artisan tinker 直接下Cache::forget($mutex_cache_key);
        // $mutex_cache_key = 'framework' . DIRECTORY_SEPARATOR . 'schedule-' . sha1($event->expression . $event->command);
        $command = substr($event->task->command, strpos($event->task->command, 'artisan') + strlen('artisan') + 1);
        $command = trim($command);
        $curTime = new \DateTime();
        $created_at = $curTime->format("Y-m-d H:i:s");
        $scheduledAssistantTask = ScheduledAssistantTask::where("command", $command)
            ->orderBy('id', 'desc')
            ->first();
        $scheduled_assistant_task_id = empty($scheduledAssistantTask) ? 0 : $scheduledAssistantTask->id;
        $scheduledAssistant = ScheduledAssistant::create([
            'type' => 'failed',
            'command' => $command,
            'logged_at' => $created_at,
            'output' =>  file_get_contents($event->task->output),
            'scheduled_assistant_task_id' =>  $scheduled_assistant_task_id,
            'uuid' => $event->task->uuid,
        ]);
    }
}
