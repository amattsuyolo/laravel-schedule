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
        Schema::create('scheduled_assistant', function (Blueprint $table) {
            $table->increments('id');
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
        Schema::dropIfExists('scheduled_assistant');
    }
}
