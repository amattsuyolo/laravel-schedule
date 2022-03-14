<?php

namespace MattSu\ScheduleAssistant\models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use MattSu\ScheduleAssistant\models\ScheduledAssistant;

class ScheduledAssistantTask extends Model
{
    const STATE = [
        "default" => 0,
        "inactivated" => 1,
        "has_begin_and_finish" => 2,
        "in_running" => 3,
        "run_too_long" => 4,
        "has_failed" => 5,
        "not_begin" => 6
    ];
    const NORMAL_STATE = [
        1, 2, 3
    ];
    use HasFactory;
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'scheduled_assistant_task';
    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;
    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = [];
    /**
     * Get the comments for the blog post.
     */
    public function logs()
    {
        return $this->hasMany(ScheduledAssistant::class);
    }
}
