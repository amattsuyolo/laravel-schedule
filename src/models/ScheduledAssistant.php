<?php

namespace MattSu\ScheduleAssistant\models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use MattSu\ScheduleAssistant\models\ScheduledAssistantTask;

class ScheduledAssistant extends Model
{
    use HasFactory;
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'scheduled_assistant_log';
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
     * Get the post that owns the comment.
     */
    public function task()
    {
        return $this->belongsTo(ScheduledAssistantTask::class);
    }
}
