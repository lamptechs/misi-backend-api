<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ticket_history_activities', function (Blueprint $table) {
            $table->id();
            $table->integer('ticket_id')->nullable();
            $table->string('create_by')->nullable();
            $table->string('modified_by')->nullable();
            $table->string('assign_to_therapist')->nullable();
            $table->string('appointment_group')->nullable();
            $table->string('call_strike')->nullable();
            $table->string('strike_history')->nullable();
            $table->string('ticket_history')->nullable();
            $table->integer('status')->nullable();
            $table->string('language')->nullable();
            $table->date('create_time')->nullable();
            $table->date('update_time')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('ticket_history_activities');
    }
};
