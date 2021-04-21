<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeContractorAssociationsForeignKeyFromContractorIdToUserId extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('developer_contractor_associations', function (Blueprint $table) {
            $table->dropForeign(['contractor_id']);
            $table->dropUnique('contractor_developer_index_unique');
            $table->dropColumn('contractor_id');
            $table->unsignedBigInteger('contractor_user_id');
            $table->foreign('contractor_user_id')->references('id')->on('users');
            $table->unique(['contractor_user_id', 'developer_id'], 'contractor_developer_index_unique');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('developer_contractor_associations', function (Blueprint $table) {
            $table->dropForeign(['contractor_user_id']);
            $table->dropUnique('contractor_developer_index_unique');
            $table->dropColumn('contractor_user_id');
            $table->unsignedBigInteger('contractor_id');
            $table->foreign('contractor_id')->references('id')->on('contractors');
            $table->unique(['contractor_id', 'developer_id'], 'contractor_developer_index_unique');
        });
    }
}
