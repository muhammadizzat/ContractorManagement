<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUnitsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('units', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('project_id', false)->nullable();
            $table->foreign('project_id')->references('id')->on('projects');
            $table->String('unit_no',50);
            $table->String('owner_name',100)->nullable();
            $table->String('owner_contact_no',50)->nullable();
            $table->String('owner_email',100)->nullable();
            $table->unsignedBigInteger('unit_type_id', false)->nullable();
            $table->foreign('unit_type_id')->references('id')->on('unit_types');
            $table->softDeletes();
            $table->bigInteger('created_by');
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
        Schema::dropIfExists('units');
    }
}
