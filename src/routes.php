<?php

use Illuminate\Support\Facades\Route;
use MattSu\ScheduleAssistant\Controllers;
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

// Route::get('/schedule-assistant-dashboard', function () {
//     return "hello";
// });
Route::get('/schedule-assistant-dashboard', [ScheduleAssistantController::class, 'dashBoard']);
