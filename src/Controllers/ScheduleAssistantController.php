<?php

namespace MattSu\ScheduleAssistant\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use MattSu\ScheduleAssistant\models\ScheduledAssistant;
use MattSu\ScheduleAssistant\models\ScheduledAssistantTask;

class ScheduleAssistantController extends Controller
{
    /**
     * 應該不能所有人都可以看
     * 用laravel 的auth?
     * 把判斷有問題的區塊 抽成一個Service
     * 之後應該要變成存API
     */
    public function dashBoardIndex()
    {

        $ScheduledAssistantTasks = ScheduledAssistantTask::where('notTrack', 0)
            ->orderBy("id", "desc")
            ->get();
        foreach ($ScheduledAssistantTasks as $item) {
            $item->last_start_at = "";
            $item->last_finish_at = "";
            $item->last_fail_at = "";
            //取出最新的start
            $scheduledAssistantStart = ScheduledAssistant::where('type', 'starting')
                ->where('scheduled_assistant_task_id', $item->id)
                ->orderBy("id", "desc")
                ->first();
            $item->state = ScheduledAssistantTask::STATE["default"];
            //沒有開始記錄 現在時間大於該開始時間
            if (empty($scheduledAssistantStart) and  (date('Y-m-d H:i:s') > $item->nextRunAt)) {
                //nextRunAt 如何更新?(listener 的 start處理)
                $item->state = ScheduledAssistantTask::STATE["not_begin"];
                continue;
            }
            // 沒有開始記錄 現在時間  > 如果紀錄start的next_run_at 	inactivated
            if (empty($scheduledAssistantStart)  and  (date('Y-m-d H:i:s') < $item->nextRunAt)) {
                $item->state = ScheduledAssistantTask::STATE["inactivated"];
                continue;
            }

            //取出結束的紀錄 (用uuid關聯)
            $scheduledAssistantFinish = ScheduledAssistant::where('type', 'finish')
                ->where('uuid', $scheduledAssistantStart->uuid)
                ->first();
            //取出失敗的紀錄 (用uuid關聯)
            $scheduledAssistantFail = ScheduledAssistant::where('type', 'failed')
                ->where('uuid', $scheduledAssistantStart->uuid)
                ->first();
            if (!empty($scheduledAssistantFinish)) {
                //有開始有結束
                $item->last_start_at = $scheduledAssistantStart->logged_at;
                $item->last_finish_at = $scheduledAssistantFinish->logged_at;
                $item->state = ScheduledAssistantTask::STATE["has_begin_and_finish"];
                continue;
            } else if (!empty($scheduledAssistantFail)) {
                $item->last_start_at = $scheduledAssistantStart->logged_at;
                $item->last_fail_at = $scheduledAssistantFail->logged_at;
                $item->state = ScheduledAssistantTask::STATE["has_failed"];
            } else {
                //最新log 開始記錄時間
                $dateTime = new \DateTime($scheduledAssistantStart->logged_at);
                $upperLimitsOfNormalMinutes = $item->upperLimitsOfNormalMinutes;
                if (empty($upperLimitsOfNormalMinutes)) {
                    $upperLimitsOfNormalMinutes = 10;
                }
                $dateTime->modify("+" . $upperLimitsOfNormalMinutes . " minutes");
                if (now() > $dateTime) {
                    $item->last_start_at = $scheduledAssistantStart->logged_at;
                    $item->state = ScheduledAssistantTask::STATE["run_too_long"];
                } else {
                    $item->last_start_at = $scheduledAssistantStart->logged_at;
                    $item->state = ScheduledAssistantTask::STATE["in_running"];
                }
            }
        }

        return view('mattsu::scheduleDashboardIndex', ['data' => $ScheduledAssistantTasks]);
    }
    /**
     * 詳細歷史資訊
     */
    public function dashBoardDetail($command)
    {
        $ScheduledAssistant = ScheduledAssistant::where('command', $command)
            ->orderBy("id", "desc")
            ->get();
        return view('mattsu::scheduleDashboard', ['data' => $ScheduledAssistant]);
    }
}
