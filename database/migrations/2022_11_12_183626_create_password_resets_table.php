<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePasswordResetsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::dropIfExists('password_resets');
        Schema::create('password_resets', function (Blueprint $table) {
            $table->id();
            $table->string("tableable")->nullable();
            $table->unsignedBigInteger("tableable_id")->nullable();
            $table->string("email");
            $table->string("token");
            $table->boolean("is_used")->default(false);
            $table->dateTime("expire_at")->nullable();
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
        Schema::dropIfExists('password_resets');
    }
}
