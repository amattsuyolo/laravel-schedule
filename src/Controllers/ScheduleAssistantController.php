<?php

namespace MattSu\ScheduleAssistant\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use MattSu\ScheduleAssistant\models\ScheduledAssistant;

class ScheduleAssistantController extends Controller
{
    /**
     * 應該不能所有人都可以看
     * 用laravel 的auth?
     */
    public function dashBoardIndex()
    {
        $ScheduledAssistants = ScheduledAssistant::where('type', 'starting')
            ->orderBy("id", "desc")
            ->get()
            ->unique('command');
        foreach ($ScheduledAssistants as $item) {
            $item->state = "default";
            $item->msg = "";
            //情況一 已經啟動 但沒有結束
            $ScheduledAssistant = ScheduledAssistant::where('type', 'finish')
                ->where('uuid', $item->uuid)
                ->first();

            $finish_logged_at = $ScheduledAssistant->logged_at ?? "";
            if (!empty($ScheduledAssistant)) {
                $item->state = "success";
                $item->msg = "normal";
            } else {
                $dateTime = new \DateTime($item->logged_at);
                $upperLimitsOfNormalMinutes = $item->upperLimitsOfNormalMinutes;
                if (empty($upperLimitsOfNormalMinutes)) {
                    $upperLimitsOfNormalMinutes = 10;
                }
                $dateTime->modify("+" . $upperLimitsOfNormalMinutes . " minutes");
                if (now() > $dateTime) {
                    $item->state = "error";
                    $item->msg = "run too long";
                }
            }
            //情況二 應該要啟動 但沒有啟動 (處理 next_run_at)
            // 現在時間  > 如果紀錄start的next_run_at 	inactivated
            if (date('Y-m-d H:i:s') > $item->nextRunAt) {
                $item->state = "error";
                $item->msg = "inactivated";
            }
        }

        return view('mattsu::scheduleDashboardIndex', ['data' => $ScheduledAssistants]);
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
