<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddUnitTypeFloorToDefectsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('defects', function (Blueprint $table) {
            $table->unsignedBigInteger('unit_type_floor_id')->nullable();
            $table->foreign('unit_type_floor_id')->references('id')->on('unit_type_floors');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('defects', function (Blueprint $table) {
            $table->dropForeign(['unit_type_floor_id']);
            $table->dropColumn('unit_type_floor_id');
        });
    }
}
