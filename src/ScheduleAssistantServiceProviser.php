<?php

namespace MattSu\ScheduleAssistant;

use Illuminate\Support\ServiceProvider;

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
        $this->loadRoutesFrom(__DIR__ . '/routes.php');
    }
}
