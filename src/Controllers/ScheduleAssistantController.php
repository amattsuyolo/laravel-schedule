<?php

namespace MattSu\ScheduleAssistant\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ScheduleAssistantController extends Controller
{
    public function dashBoard()
    {
        return view('mattsu::scheduleDashboard');
    }
}
