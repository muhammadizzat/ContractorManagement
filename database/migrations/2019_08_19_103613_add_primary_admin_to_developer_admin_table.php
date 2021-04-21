<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddPrimaryAdminToDeveloperAdminTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('developer_admins', function (Blueprint $table) {
            $table->boolean('primary_admin')->default(false);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('developer_admins', function (Blueprint $table) {
            $table->dropColumn('primary_admin');
        });
    }
}
