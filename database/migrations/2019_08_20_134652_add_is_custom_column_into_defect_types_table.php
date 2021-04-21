<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddIsCustomColumnIntoDefectTypesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('defect_types', function (Blueprint $table) {
            $table->boolean('is_custom')->default(false);
            $table->unsignedBigInteger('developer_id')->nullable();
            $table->foreign('developer_id')->references('id')->on('developers');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('defect_types', function (Blueprint $table) {

            $table->dropForeign(['developer_id']);
            $table->dropColumn('developer_id');
            $table->dropColumn('is_custom');
        });
    }
}
