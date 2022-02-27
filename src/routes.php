<?php

use Illuminate\Support\Facades\Route;
use MattSu\ScheduleAssistant\Controllers\ScheduleAssistantController;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/


Route::get('/schedule-assistant-dashboard/{command}', [ScheduleAssistantController::class, 'dashBoardDetail']);


Route::get('/schedule-assistant-dashboard', [ScheduleAssistantController::class, 'dashBoardIndex']);
