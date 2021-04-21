<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFloorPlansTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('floor_plans', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('unit_type_id', false)->nullable();
            $table->foreign('unit_type_id')->references('id')->on('unit_types');
            $table->String('floor_level',50);
            $table->unsignedBigInteger('floor_plan_image_media_id', false)->nullable();
            $table->foreign('floor_plan_image_media_id')->references('id')->on('medias');
            $table->integer('sequence');
            $table->softDeletes();            
            $table->bigInteger('created_by');
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
        Schema::dropIfExists('floor_plans');
    }
}
