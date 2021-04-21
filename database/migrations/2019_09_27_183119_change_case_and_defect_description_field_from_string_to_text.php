<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeCaseAndDefectDescriptionFieldFromStringToText extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('project_cases', function (Blueprint $table) {
            $table->text('description')->nullable()->change();
        });
        Schema::table('defects', function (Blueprint $table) {
            $table->text('description')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('project_cases', function (Blueprint $table) {
            $table->string('description')->nullable(false)->change();
        });
        Schema::table('defects', function (Blueprint $table) {
            $table->string('description')->nullable(false)->change();
        });
    }
}
