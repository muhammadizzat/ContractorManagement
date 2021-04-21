<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddClosedStatusToDefectsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('defects', function (Blueprint $table) {
            $table->string('closed_status', 50)->nullable();
            
            $table->unsignedBigInteger('duplicate_defect_id')->nullable();
            $table->foreign('duplicate_defect_id')->references('id')->on('defects');
            
            $table->string('reject_reason', 50)->nullable();
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
            $table->dropColumn('reject_reason');
            $table->dropForeign(['duplicate_defect_id']);
            $table->dropColumn('duplicate_defect_id');
            $table->dropColumn('closed_status');
        });
    }
}
