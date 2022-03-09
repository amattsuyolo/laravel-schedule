<?php

namespace MattSu\ScheduleAssistant\Commands;

use Illuminate\Console\Command;
use MattSu\ScheduleAssistant\models\ScheduledAssistant;
use MattSu\ScheduleAssistant\models\ScheduledAssistantTask;
use MattSu\ScheduleAssistant\Service\ScheduleAssistantService;
use \Cron\CronExpression;
use \Carbon\Carbon;
use Cache;

class SyncCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:syncScheduleCommand';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'syncScheduleCommand';

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
     * 同步所有的command
     * @return int
     */
    public function handle(ScheduleAssistantService $scheduleAssistantService)
    {
        //取出所有command
        $allCommandInSchedule = $scheduleAssistantService->getAllCommandInSchedule();
        // var_dump($allCommandInSchedule);
        $allCommandInSchedule->each(function ($item, $key) {
            $item = (object)$item;
            $command = trim($item->command);
            //撈出資料
            if (!$item->notTrack) {
                $ScheduledAssistantTask = ScheduledAssistantTask::where("command", $command)
                    ->orderBy("id", "desc")
                    ->first();
                //沒有的新增
                if (empty($ScheduledAssistantTask)) {
                    $ScheduledAssistantTask = new ScheduledAssistantTask;
                    $ScheduledAssistantTask->command = $command;
                    $ScheduledAssistantTask->mutex_cache_key = $item->mutex_name;
                    $ScheduledAssistantTask->upperLimitsOfNormalMinutes = $item->upperLimitsOfNormalMinutes;
                    $ScheduledAssistantTask->notTrack = ($item->notTrack) ? 1 : 0;
                    $ScheduledAssistantTask->nextRunAt = $item->next_run_at;
                    $ScheduledAssistantTask->save();
                }
            } else {
                $ScheduledAssistantTask = ScheduledAssistantTask::where("command", $command)
                    ->orderBy("id", "desc")
                    ->first();
                if ($ScheduledAssistantTask->notTrack == 0) {
                    $ScheduledAssistantTask->notTrack = 1;
                    $ScheduledAssistantTask->save();
                }
            }
        });
    }
}
