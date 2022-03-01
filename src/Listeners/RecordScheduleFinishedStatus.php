<?php

namespace MattSu\ScheduleAssistant\Listeners;

use Illuminate\Console\Events\ScheduledTaskFailed;
use Illuminate\Console\Events\ScheduledTaskFinished;
use Illuminate\Console\Events\ScheduledTaskSkipped;
use Illuminate\Console\Events\ScheduledTaskStarting;
use MattSu\ScheduleAssistant\models\ScheduledAssistant;

class RecordScheduleFinishedStatus
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
    public function handle(ScheduledTaskFinished $event)
    {
        //Schedule 下 Withoutoverlapping 如果有排程死掉，
        //但是排程手動下能正常執行時，
        //到資料表scheduled_events找出key然後可以利用 php artisan tinker 直接下Cache::forget($mutex_cache_key);
        // $mutex_cache_key = 'framework' . DIRECTORY_SEPARATOR . 'schedule-' . sha1($event->expression . $event->command);
        $command = substr($event->task->command, strpos($event->task->command, 'artisan') + strlen('artisan') + 1);

        $output = file_get_contents($event->task->output);
        $curTime = new \DateTime();
        $created_at = $curTime->format("Y-m-d H:i:s");
        $scheduledAssistant = ScheduledAssistant::create([
            'type' => 'finish',
            'command' => $command,
            'logged_at' => $created_at,
            'output' => $output,
            'uuid' => $event->task->uuid
        ]);
    }
}
