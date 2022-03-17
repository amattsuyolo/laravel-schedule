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
use MattSu\ScheduleAssistant\Listeners\RecordScheduleStartingStatus;
use MattSu\ScheduleAssistant\Listeners\RecordScheduleFailedStatus;
use MattSu\ScheduleAssistant\models\ScheduledAssistant;
use MattSu\ScheduleAssistant\Commands\ClearScheduleMutex;
use MattSu\ScheduleAssistant\Commands\ScheduleSupervisor;
use MattSu\ScheduleAssistant\Commands\SyncCommand;
use MattSu\ScheduleAssistant\models\ScheduledAssistantTask;

class ScheduleAssistantServiceProviser extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(ScheduledAssistant::class, MattSu\ScheduleAssistant\models\ScheduledAssistant::class);
        $this->app->bind(ScheduledAssistantTask::class, MattSu\ScheduleAssistant\models\ScheduledAssistantTask::class);
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        $source = realpath($raw = __DIR__ . '/../config/schedule-assistant.php') ?: $raw;
        $this->publishes([
            $source => config_path('schedule-assistant.php'),
        ]);

        $this->loadViewsFrom(__DIR__ . '/views', 'mattsu');
        $this->loadMigrationsFrom(__DIR__ . '/migrations');

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
        ScheduleEvent::macro('setUUID', function ($uuid) {
            $this->uuid = $uuid;

            return $this;
        });
        Event::listen(
            ScheduledTaskStarting::class,
            [RecordScheduleStartingStatus::class, 'handle']
        );
        Event::listen(
            ScheduledTaskFinished::class,
            [RecordScheduleFinishedStatus::class, 'handle']
        );
        Event::listen(
            ScheduledTaskFailed::class,
            [RecordScheduleFailedStatus::class, 'handle']
        );

        if ($this->app->runningInConsole()) {
            if (config('schedule-assistant.open-clear-schedule-mutex-command')) {
                $this->commands([
                    ClearScheduleMutex::class,
                ]);
            }
            $this->commands([
                ScheduleSupervisor::class,
            ]);
            $this->commands([
                SyncCommand::class,
            ]);
        }
    }
}
