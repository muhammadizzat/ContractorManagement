<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddProjectIdToUnitTypesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('unit_types', function (Blueprint $table) {
            $table->unsignedBigInteger('project_id')->nullable();
            $table->foreign('project_id')->references('id')->on('projects');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('unit_types', function (Blueprint $table) {
            $table->dropForeign(['project_id']);
            $table->dropColumn('project_id');
        });
    }
}
