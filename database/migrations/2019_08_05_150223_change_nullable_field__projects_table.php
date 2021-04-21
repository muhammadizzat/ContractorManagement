<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeNullableFieldProjectsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->string('address2',100)->nullable()->change();
            $table->string('address3',100)->nullable()->change();
            $table->dropColumn('contact_no');
            
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('project', function (Blueprint $table) {
            Schema::dropIfExists('contact_no');
        });
    }
}
