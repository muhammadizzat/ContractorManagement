<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCaseTagsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('case_tags', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->unsignedBigInteger('case_id')->nullable();
            $table->foreign('case_id')->references('id')->on('project_cases');
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
        Schema::dropIfExists('case_tags');
    }
}
