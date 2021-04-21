<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddMediaIdToProjectsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->unsignedBigInteger('logo_media_id')->nullable();
            $table->foreign('logo_media_id')->references('id')->on('medias');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->dropForeign(['logo_media_id']);
            $table->dropColumn('logo_media_id');
        });
    }
}
