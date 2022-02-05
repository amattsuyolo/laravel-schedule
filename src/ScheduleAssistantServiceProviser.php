<?php

namespace MattSu\ScheduleAssistant;

use Illuminate\Support\ServiceProvider;
use Illuminate\Console\Scheduling\Event as ScheduleEvent;
use Illuminate\Console\Events\ScheduledTaskFailed;
use Illuminate\Console\Events\ScheduledTaskFinished;
use Illuminate\Console\Events\ScheduledTaskSkipped;
use Illuminate\Console\Events\ScheduledTaskStarting;
use Illuminate\Support\Facades\Event;
use MattSu\ScheduleAssistant\Listeners\RecordScheduleFinishedStatus;

class ScheduleAssistantServiceProviser extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $source = realpath($raw = __DIR__ . '/../config/schedule-assistant.php') ?: $raw;
        $this->publishes([
            $source => config_path('schedule-assistant.php'),
        ]);
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        $configPath = __DIR__ . '/../config/schedule-assistant.php';
        $this->mergeConfigFrom($configPath, 'schedule-assistant');

        if (config('schedule-assistant.open-schedule-route')) {
            $this->loadRoutesFrom(__DIR__ . '/routes.php');
        }
        ScheduleEvent::macro('notTrack', function ($track = true) {
            $this->notTrack = $track;

            return $this;
        });
        ScheduleEvent::macro('upperLimitsOfNormalMinutes', function ($minutes = 10) {
            $this->upperLimitsOfNormalMinutes = $minutes;

            return $this;
        });

        Event::listen(
            ScheduledTaskFinished::class,
            [RecordScheduleFinishedStatus::class, 'handle']
        );
    }
}
