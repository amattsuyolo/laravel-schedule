<?php

namespace MattSu\ScheduleAssistant\models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use MattSu\ScheduleAssistant\models\ScheduledAssistant;

class ScheduledAssistantTask extends Model
{
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
