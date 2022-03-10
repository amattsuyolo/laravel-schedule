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
        //取得所有unique command 理論所有就是允許被記錄的
        //沒有開始怎麼辦? (根本不太可能)
        //每分鐘的排程也會陣亡
        //認真要解決的話 每分鐘的排程要獨立於排程器(google?)
        //可以做一個小工具的包 專門讀SQL 排程Table
        //應該要全部的資料都可以判斷?
        //但dashBoard 應該是最新的


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
                continue;
            }
            // 沒有開始記錄 現在時間  > 如果紀錄start的next_run_at 	inactivated
            if (empty($scheduledAssistantStart)  and  (date('Y-m-d H:i:s') < $item->nextRunAt)) {
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
                } else {
                    $item->state = "normal";
                    $item->msg = "in runing";
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
