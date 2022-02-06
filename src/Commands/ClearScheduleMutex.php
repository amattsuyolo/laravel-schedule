<?php

namespace MattSu\ScheduleAssistant\Commands;

use Illuminate\Console\Command;
use Cache;
use MattSu\ScheduleAssistant\models\ScheduledAssistant;

class ClearScheduleMutex extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:clearScheduleMutex {command}';

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
        $command = $this->argument('command');
        $scheduledAssistantorderBy = ScheduledAssistant::where("command", $command)
            ->orderBy('id', 'desc')
            ->first();
        Cache::forget($scheduledAssistantorderBy->mutex_cache_key);
        $this->info("clear");
    }
}
