<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDcAssocDefectTypesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('dc_assoc_defect_types', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('dca_id')->nullable();
            $table->foreign('dca_id')->references('id')->on('developer_contractor_associations');
            $table->unsignedBigInteger('defect_type_id')->nullable();
            $table->foreign('defect_type_id')->references('id')->on('defect_types');
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
        Schema::dropIfExists('dc_assoc_defect_types');
    }
}
