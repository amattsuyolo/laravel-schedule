<?php

namespace MattSu\ScheduleAssistant\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ScheduleErrorEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * The order instance.
     *
     * @var \App\Models\Order
     */

    public  $date_time;
    public  $task_id;
    public  $task_command;
    public  $msg;
    public  $state;
    /**
     * Create a new event instance.
     *
     * @param  \App\Models\Order  $order
     * @return void
     */
    public function __construct($date_time, $task_id, $task_command, $msg, $state)
    {
        $this->date_time = $date_time;
        $this->task_id = $task_id;
        $this->task_command = $task_command;
        $this->msg = $msg;
        $this->state = $state;
    }
}
