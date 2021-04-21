<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddExtendedCountFieldInDefectsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('defects', function (Blueprint $table) {
            $table->integer('extended_count')->nullable();
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
            $table->dropColumn('extended_count');
        });
    }
}
