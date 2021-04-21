<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDeveloperContractorAssociationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('developer_contractor_associations', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('contractor_id');
            $table->foreign('contractor_id')->references('id')->on('contractors');
            $table->unsignedBigInteger('developer_id');
            $table->foreign('developer_id')->references('id')->on('developers');
            $table->unique(['contractor_id', 'developer_id'], 'contractor_developer_index_unique');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('developer_contractor_associations');
    }
}
