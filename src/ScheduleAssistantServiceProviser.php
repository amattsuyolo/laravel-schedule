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

        $this->loadViewsFrom(__DIR__ . '/views', 'mattsu');
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
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

        $this->app->bind(ScheduledAssistant::class, MattSu\ScheduleAssistant\models\ScheduledAssistant::class);

        if ($this->app->runningInConsole() and config('schedule-assistant.open-clear-schedule-mutex-command')) {
            $this->commands([
                ClearScheduleMutex::class,
            ]);
        }
        app()->make(\Illuminate\Contracts\Console\Kernel::class);

        $schedule = app()->make(\Illuminate\Console\Scheduling\Schedule::class);

        $events = collect($schedule->events())->map(function ($event) {
            $filename = $event->command . date('Y-m-d H:i:s') . uniqid() . '.log';
            $path = storage_path('logs/' . $filename);
            $event->appendOutputTo($path);
        });
    }
}
