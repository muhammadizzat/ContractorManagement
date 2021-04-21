<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDefectTagsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('defect_tags', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->unsignedBigInteger('defect_id')->nullable();
            $table->foreign('defect_id')->references('id')->on('defects');
            $table->string('tag');

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
        Schema::dropIfExists('defect_tags');
    }
}
