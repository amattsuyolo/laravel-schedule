<?php

namespace MattSu\ScheduleAssistant\Commands;

use Illuminate\Console\Command;
use MattSu\ScheduleAssistant\models\ScheduledAssistant;
use \Cron\CronExpression;
use \Carbon\Carbon;
use Cache;

class ClearScheduleMutex extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:clearScheduleMutex {commandName}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'clearScheduleMutex';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $command = trim($this->argument('commandName'));
        try {
            $scheduledAssistant = ScheduledAssistant::where("command", $command)
                ->orderBy('id', 'desc')
                ->first();
            $mutex_cache_key = $scheduledAssistant->mutex_cache_key ?? "";
            $mutex_cache_key = (empty($mutex_cache_key)) ? $this->getMutex($command) : $mutex_cache_key;
            if (empty($mutex_cache_key)) {
                $this->info("Command name not found!");
                return;
            }
            Cache::forget($mutex_cache_key);
        } catch (\Exception $e) {
            echo $e->getMessage();
        }

        $this->info("Clear the mutex cache ");
        $this->info("Command Name : " . $command);
    }
    /**
     * 
     */
    public function getMutex($command)
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
                'mutex_name' => $event->mutexName()
            ];
        });
        $events = $events->groupBy('command')->toArray();
        return optional($events[$command])[0]['mutex_name'];
    }
}
