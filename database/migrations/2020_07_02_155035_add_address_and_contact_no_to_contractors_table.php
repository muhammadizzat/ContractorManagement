<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddAddressAndContactNoToContractorsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('contractors', function (Blueprint $table) {
            $table->renameColumn('address', 'address_1');
            $table->string('address_2');
            $table->string('city');
            $table->string('state');
            $table->integer('postal_code');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('contractors', function (Blueprint $table) {
            $table->renameColumn('address_1', 'address');
            $table->dropColumn('address_2');
            $table->dropColumn('city');
            $table->dropColumn('state');
            $table->dropColumn('postal_code');
        });
    }
}
