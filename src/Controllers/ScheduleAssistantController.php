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
        $ScheduledAssistant = ScheduledAssistant::where('type', 'starting')
            ->orderBy("id", "desc")
            ->get()
            ->unique('command');
        return view('mattsu::scheduleDashboard', ['data' => $ScheduledAssistant]);
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
