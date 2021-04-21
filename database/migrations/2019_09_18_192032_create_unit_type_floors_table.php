<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUnitTypeFloorsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('unit_type_floors', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->string('name', 50);

            $table->unsignedBigInteger('unit_type_id');
            $table->foreign('unit_type_id')->references('id')->on('unit_types');

            $table->unsignedBigInteger('floor_plan_media_id')->nullable();
            $table->foreign('floor_plan_media_id')->references('id')->on('medias');

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
        Schema::dropIfExists('unit_type_floors');
    }
}
