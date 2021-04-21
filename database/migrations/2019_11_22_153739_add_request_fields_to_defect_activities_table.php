<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddRequestFieldsToDefectActivitiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('defect_activities', function (Blueprint $table) {
            $table->string('request_type', 50)->nullable();
            $table->string('request_response', 50)->nullable();
            $table->unsignedBigInteger('request_response_user_id')->nullable();
            $table->foreign('request_response_user_id')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('defect_activities', function (Blueprint $table) {
            $table->dropForeign(['request_response_user_id']);
            $table->dropColumn('request_response_user_id');
            $table->dropColumn('request_response');
            $table->dropColumn('request_type');
        });
    }
}
