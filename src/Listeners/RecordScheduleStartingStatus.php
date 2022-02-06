<?php

namespace MattSu\ScheduleAssistant\Listeners;

use Illuminate\Console\Events\ScheduledTaskFailed;
use Illuminate\Console\Events\ScheduledTaskFinished;
use Illuminate\Console\Events\ScheduledTaskSkipped;
use Illuminate\Console\Events\ScheduledTaskStarting;
use MattSu\ScheduleAssistant\models\ScheduledAssistant;

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
        // Access the order using $event->order...
        // $this->task = $task;
        // $this->runtime = $runtime;
        logger("我是RecordScheduleStartingStatus listener command:");
        logger($event->task->command);
        logger("我是RecordScheduleStartingStatus listener expression:");
        logger($event->task->expression);
        logger("我是RecordScheduleStartingStatus listener notTrack:");
        logger($event->task->notTrack);
        logger("我是RecordScheduleStartingStatus listener upperLimitsOfNormalMinutes:");
        logger($event->task->upperLimitsOfNormalMinutes);
        //Schedule 下 Withoutoverlapping 如果有排程死掉，
        //但是排程手動下能正常執行時，
        //到資料表scheduled_events找出key然後可以利用 php artisan tinker 直接下Cache::forget($mutex_cache_key);
        // $mutex_cache_key = 'framework' . DIRECTORY_SEPARATOR . 'schedule-' . sha1($event->expression . $event->command);
        $command = substr($event->task->command, strpos($event->task->command, 'artisan') + strlen('artisan') + 1);
        $mutexName = $event->task->mutexName();
        $scheduledAssistant = ScheduledAssistant::create([
            'type' => 'starting',
            'command' => $command,
            'logged_at' => date("Y-m-d H:i:s"),
            'mutex_cache_key' => $mutexName
        ]);
    }
}
