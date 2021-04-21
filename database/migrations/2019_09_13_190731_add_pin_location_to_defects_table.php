<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddPinLocationToDefectsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('defects', function (Blueprint $table) {
            $table->double('pin_location_x', 8, 3)->nullable();
            $table->double('pin_location_y', 8, 3)->nullable();
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
            $table->dropColumn('pin_location_x');
            $table->dropColumn('pin_location_y');
        });
    }
}
