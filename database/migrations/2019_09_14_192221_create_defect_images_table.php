<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDefectImagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('defect_images', function (Blueprint $table) {
            $table->bigIncrements('id');
            
            $table->unsignedBigInteger('defect_id')->nullable();
            $table->foreign('defect_id')->references('id')->on('defects');
            
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
        Schema::dropIfExists('defect_images');
    }
}
