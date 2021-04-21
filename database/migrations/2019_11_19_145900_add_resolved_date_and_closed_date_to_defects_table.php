<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddResolvedDateAndClosedDateToDefectsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('defects', function (Blueprint $table) {
            $table->timestamp('resolved_date')->nullable();
            $table->timestamp('closed_date')->nullable();
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
            $table->dropColumn('resolved_date');
            $table->dropColumn('closed_date');
        });
    }
}

