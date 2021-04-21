<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMediasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('medias', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('category', 100)->nullable();
            $table->string('filename', 100)->nullable();
            $table->string('mimetype', 100)->nullable();
            $table->binary('data');
            $table->unsignedInteger('size');			
            $table->bigInteger('created_by');
            $table->timestamps();
        });

        DB::statement("ALTER TABLE medias MODIFY data MEDIUMBLOB");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('medias');
    }
}
