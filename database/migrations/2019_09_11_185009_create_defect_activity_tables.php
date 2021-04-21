<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDefectActivityTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('defect_activities', function(Blueprint $table) {
            $table->bigIncrements('id');

            $table->unsignedBigInteger('defect_id');
            $table->foreign('defect_id')->references('id')->on('defects');

            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id')->references('id')->on('users');

            $table->string('type', 50);
            $table->string('content', 256);

            $table->softDeletes();
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
        Schema::dropIfExists('defect_activities');
    }
}
