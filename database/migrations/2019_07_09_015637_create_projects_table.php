<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProjectsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('projects', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('developer_id', false)->nullable();
            $table->foreign('developer_id')->references('id')->on('developers');
            $table->string('address',100);
            $table->string('address2',100);
            $table->string('address3',100);
            $table->string('contact_no',15);
            $table->text('description');
            $table->string('status', 50);
            $table->date('expiry_date')->nullable;
            $table->date('start_date')->nullable;
            $table->string('name', 100);
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
        Schema::dropIfExists('projects');
    }
}
