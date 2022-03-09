<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateScheduledAssistantTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('scheduled_assistant_task', function (Blueprint $table) {
            $table->increments('id');
            $table->string('command');
            $table->string('mutex_cache_key')->nullable();
            $table->tinyInteger('upperLimitsOfNormalMinutes')->nullable();
            $table->tinyInteger('notTrack')
                ->default(0)
                ->nullable()
                ->comment('0:false;1:true');
            $table->dateTime('nextRunAt')->nullable();
            $table->dateTime('logged_at')->nullable();
        });
        Schema::create('scheduled_assistant_log', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('scheduled_assistant_task_id');
            $table->foreign('scheduled_assistant_task_id')->references('id')->on('scheduled_assistant_task')->cascadeOnDelete();
            //可以補關聯
            $table->string('uuid');
            $table->string('type');
            $table->string('command');
            $table->string('mutex_cache_key')->nullable();
            $table->text('output')->nullable();
            $table->tinyInteger('upperLimitsOfNormalMinutes')->nullable();
            $table->tinyInteger('notTrack')
                ->default(0)
                ->nullable()
                ->comment('0:false;1:true');
            $table->dateTime('nextRunAt')->nullable();
            $table->dateTime('logged_at');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // $table->dropForeign('scheduled_assistant_task_id');
        Schema::dropIfExists('scheduled_assistant_task');
        Schema::dropIfExists('scheduled_assistant_log');
    }
}
