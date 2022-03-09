<?php

namespace MattSu\ScheduleAssistant\Commands;

use Illuminate\Console\Command;
use \Cron\CronExpression;
use \Carbon\Carbon;
use Cache;
use MattSu\ScheduleAssistant\models\ScheduledAssistant;
use MattSu\ScheduleAssistant\models\ScheduledAssistantTask;
use MattSu\ScheduleAssistant\Events\ScheduleErrorEvent;

class ScheduleSupervisor extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:scheduleSupervisor';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'ScheduleSupervisor';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }
    // $this->date_time = $date_time;
    // $this->task_id = $task_id;
    // $this->task_command = $task_command;
    // $this->msg = $msg;
    // $this->state = $state;
    /**
     * Execute the console command.
     * 每分鐘執行
     * config 可以關掉要不要執行
     * 進階可以指定要監控的時間區間
     * 用config
     * //抓取dinstinct 的排程名稱 name 相同的
     *  理論上不同參數就是不同的東西
     *  可以指定要監控哪個排程
     *
     *  利用Service 判斷有沒有問題
     *  graceTime
      
     *  dispatch event 
     *  寫一個基本的通知listener
     *  config 可以設定 listener Name
     *  可以用mailtrap 做測試
     *  要一個錯誤blade
     * @return int
     */
    public function handle()
    {
        $ScheduledAssistantTasks = ScheduledAssistantTask::where('notTrack', 0)
            ->orderBy("id", "desc")
            ->get();
        foreach ($ScheduledAssistantTasks as $item) {
            //取出最新的start
            $scheduledAssistantStart = ScheduledAssistant::where('type', 'starting')
                ->where('scheduled_assistant_task_id', $item->id)
                ->orderBy("id", "desc")
                ->first();
            $item->state = "default";
            $item->msg = "";
            //沒有開始記錄 現在時間大於該開始時間
            if (empty($scheduledAssistantStart) and  (date('Y-m-d H:i:s') > $item->nextRunAt)) {
                //nextRunAt 如何更新?(listener 的 start處理)
                $item->state = "error";
                $item->msg = "notBegin";
                ScheduleErrorEvent::dispatch(date('Y-m-d H:i:s'), $item->id, $item->command, "notBegin", "error");
                continue;
            }
            // 沒有開始記錄 現在時間  > 如果紀錄start的next_run_at 	inactivated
            if (empty($scheduledAssistantStart)  and  (date('Y-m-d H:i:s') > $item->nextRunAt)) {
                $item->state = "normal";
                $item->msg = "inactivated";
                continue;
            }

            //取出結束的紀錄 (用uuid關聯)
            $ScheduledAssistant = ScheduledAssistant::where('type', 'finish')
                ->where('uuid', $scheduledAssistantStart->uuid)
                ->first();
            $finish_logged_at = $ScheduledAssistant->logged_at ?? "";
            if (!empty($ScheduledAssistant)) {
                //有開始有結束
                $item->state = "success";
                $item->msg = "normal";
                continue;
            } else {
                //最新log 開始記錄時間
                $dateTime = new \DateTime($scheduledAssistantStart->logged_at);
                $upperLimitsOfNormalMinutes = $item->upperLimitsOfNormalMinutes;
                if (empty($upperLimitsOfNormalMinutes)) {
                    $upperLimitsOfNormalMinutes = 10;
                }
                $dateTime->modify("+" . $upperLimitsOfNormalMinutes . " minutes");
                if (now() > $dateTime) {
                    $item->state = "error";
                    $item->msg = "run too long";
                    ScheduleErrorEvent::dispatch(date('Y-m-d H:i:s'), $item->id, $item->command, "run too long", "error");
                } else {
                    $item->state = "normal";
                    $item->msg = "in runing";
                }
            }
        }
    }
}
