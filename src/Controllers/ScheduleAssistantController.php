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
    public function dashBoard()
    {
        $ScheduledAssistant = ScheduledAssistant::all();
        return view('mattsu::scheduleDashboard', ['data' => $ScheduledAssistant]);
    }
}
