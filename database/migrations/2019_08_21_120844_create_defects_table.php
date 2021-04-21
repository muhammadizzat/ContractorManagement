<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDefectsTable extends Migration
{
    public function up()
    {
        Schema::create('defects', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->unsignedBigInteger('developer_id');
            $table->foreign('developer_id')->references('id')->on('developers');

            $table->unsignedBigInteger('project_id');
            $table->foreign('project_id')->references('id')->on('projects');

            $table->unsignedBigInteger('case_id');
            $table->foreign('case_id')->references('id')->on('project_cases');

            $table->unsignedBigInteger('defect_type_id');
            $table->foreign('defect_type_id')->references('id')->on('defect_types');

            $table->unsignedBigInteger('assigned_contractor_user_id')->nullable();
            $table->foreign('assigned_contractor_user_id')->references('id')->on('users');

            $table->string('title',250);
            $table->string('description',250)->nullable();
            $table->date('due_date')->nullable();
            $table->string('status',25);

            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('defects');
    }
}
