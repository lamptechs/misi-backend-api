<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAppointmentUploadsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        
            Schema::create('appointment_uploads', function (Blueprint $table) {
                $table->id();
                $table->foreignId("appointment_id")->references("id")->on("appointmnets")->cascadeOnDelete();
                $table->string("file_name");
                $table->string("file_url");
                $table->timestamps();
                $table->softDeletes();
            });
        
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('appointment_uploads');
    }
}
