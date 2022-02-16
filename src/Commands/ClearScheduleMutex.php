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
        //補錯誤處理
        $command = $this->argument('commandName');
        try {
            $scheduledAssistant = ScheduledAssistant::where("command", $command)
                ->orderBy('id', 'desc')
                ->first();
            if (empty($scheduledAssistant)) {
                $this->info("Command name not found!");
                return;
            }
            Cache::forget($scheduledAssistant->mutex_cache_key);
        } catch (\Exception $e) {
            echo $e->getMessage();
        }

        $this->info("Clear the mutex cache ");
        $this->info("Command Name : " . $command);
    }
}
