<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnIntoTherapistTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table("therapists", function(Blueprint $table){
            $table->string("profile_pic")->nullable()->after("country_id");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table("therapists", function(Blueprint $table){
            $table->dropColumn("profile_pic");
        });
    }
}
