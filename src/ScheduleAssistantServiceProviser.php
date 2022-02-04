<?php

namespace MattSu\ScheduleAssistant;

use Illuminate\Support\ServiceProvider;
use Illuminate\Console\Scheduling\Event;

class ScheduleAssistantServiceProviser extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $source = realpath($raw = __DIR__.'/../config/schedule-assistant.php') ?: $raw;
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
        
        if (config('schedule-assistant.open-schedule-route')){
            $this->loadRoutesFrom(__DIR__ . '/routes.php');
        }
        Event::macro('notTrack', function () {
            $this->notTrack = true;

            return $this;
        });
        Event::macro('upperLimitsOfNormalMinutes', function ($minutes) {
            $this->upperLimitsOfNormalMinutes = $minutes;

            return $this;
        });
    }
}
