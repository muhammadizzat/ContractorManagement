<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDefectActivityImagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('defect_activity_images', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->unsignedBigInteger('defect_activity_id')->nullable();
            $table->foreign('defect_activity_id')->references('id')->on('defect_activities');
            
            $table->unsignedBigInteger('media_id')->nullable();
            $table->foreign('media_id')->references('id')->on('medias');

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
        Schema::dropIfExists('defect_activity_images');
    }
}
