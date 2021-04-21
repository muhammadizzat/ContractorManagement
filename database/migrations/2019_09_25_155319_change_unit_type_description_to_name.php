<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeUnitTypeDescriptionToName extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('unit_types', function (Blueprint $table) {
            $table->renameColumn('description', 'name');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('unit_types', function (Blueprint $table) {
            $table->renameColumn('name', 'description');
        });
    }
}
