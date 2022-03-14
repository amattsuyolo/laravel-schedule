<?php

namespace MattSu\ScheduleAssistant\Commands;

use Illuminate\Console\Command;
use MattSu\ScheduleAssistant\models\ScheduledAssistant;
use MattSu\ScheduleAssistant\models\ScheduledAssistantTask;
use MattSu\ScheduleAssistant\Service\ScheduleAssistantService;
use \Cron\CronExpression;
use \Carbon\Carbon;
use Cache;
use DB;

class SyncCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'scheduleCommand:sync';

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
        //取出所有kernel中的command
        $allCommandInSchedule = $scheduleAssistantService->getAllCommandInSchedule();
        //取出所有在table中的command
        $scheduledAssistantTasks = ScheduledAssistantTask::all();
        $temp_array = [];
        $allCommandInSchedule->each(function ($item, $key) use (&$scheduledAssistantTasks, &$temp_array) {
            $item = (object)$item;
            $command = trim($item->command);
            array_push($temp_array, $command);
            //撈出資料
            if (!$item->notTrack) {
                $ScheduledAssistantTask = $scheduledAssistantTasks->where("command", $command)->all();
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
                $ScheduledAssistantTask = $scheduledAssistantTasks->where("command", $command)->first();
                if ($ScheduledAssistantTask->notTrack == 0) {
                    $ScheduledAssistantTask->notTrack = 1;
                    $ScheduledAssistantTask->save();
                }
            }
        });
        //BINARY 可以把大小寫當作不同
        //把SCHEDULE KERNEL 裡面不存在的資料全部更新成notTrack 為 true
        ScheduledAssistantTask::whereNotIn(DB::raw('BINARY `command`'), $temp_array)
            ->update(['notTrack' => 1]);
    }
}
