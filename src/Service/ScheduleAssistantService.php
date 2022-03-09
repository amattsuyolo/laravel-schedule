<?php

namespace MattSu\ScheduleAssistant\Service;

use MattSu\ScheduleAssistant\models\ScheduledAssistant;
use \Cron\CronExpression;
use \Carbon\Carbon;

class ScheduleAssistantService
{
    public function test()
    {
        echo "hello";
    }
    public function getAllCommandInSchedule()
    {
        //It will broke without below
        app()->make(\Illuminate\Contracts\Console\Kernel::class);

        $schedule = app()->make(\Illuminate\Console\Scheduling\Schedule::class);

        $events = collect($schedule->events())->map(function ($event) {
            $cron = CronExpression::factory($event->expression);
            $date = Carbon::now();
            if ($event->timezone) {
                $date->setTimezone($event->timezone);
            }
            $command = trim(substr($event->command, strpos($event->command, 'artisan') + strlen('artisan') + 1));
            return [
                'expression' => $event->expression,
                'command' => $command,
                'next_run_at' => $cron->getNextRunDate()->format('Y-m-d H:i:s'),
                'mutex_name' => $event->mutexName(),
                'notTrack' => $event->notTrack ?? false,
                'upperLimitsOfNormalMinutes' => $event->upperLimitsOfNormalMinutes ?? 5
            ];
        });
        return $events;
        // $events = $events->groupBy('command')->toArray();
        // return optional($events[$command])[0]['mutex_name'];
    }
}
