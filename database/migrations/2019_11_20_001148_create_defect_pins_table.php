<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDefectPinsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('defect_pins', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->unsignedBigInteger('defect_id')->nullable();
            $table->foreign('defect_id')->references('id')->on('defects');

            $table->string('label', 50)->nullable();
            $table->double('x', 8, 3)->nullable();
            $table->double('y', 8, 3)->nullable();

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
        Schema::dropIfExists('defect_pins');
    }
}
