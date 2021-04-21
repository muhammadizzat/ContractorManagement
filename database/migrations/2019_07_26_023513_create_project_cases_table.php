<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProjectCasesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('project_cases', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('unit_id');
            $table->foreign('unit_id')->references('id')->on('units');
            $table->unsignedBigInteger('assigned_cow_user_id')->nullable();
            $table->foreign('assigned_cow_user_id')->references('id')->on('users');
            $table->unsignedBigInteger('project_id');
            $table->foreign('project_id')->references('id')->on('projects');
            $table->unsignedBigInteger('developer_id');
            $table->foreign('developer_id', 'cases_developer_id_foreign')->references('id')->on('developers');

            $table->string('title',250);
            $table->string('status',25);
            $table->bigInteger('created_by');
            $table->timestamps();
            
            $table->softDeletes();

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('project_cases');
    }
}
