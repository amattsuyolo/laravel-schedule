<?php

namespace MattSu\ScheduleAssistant\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use MattSu\ScheduleAssistant\models\ScheduledAssistant;

class ScheduleAssistantController extends Controller
{
    public function dashBoard()
    {
        $ScheduledAssistant = ScheduledAssistant::all();
        var_dump($ScheduledAssistant);
        return view('mattsu::scheduleDashboard');
    }
}
