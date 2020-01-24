<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateGroupPermissionTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('GroupPermission', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('group_id')->unsigned()->index('group_id');
            $table->integer('permission_id')->unsigned();
        });
    }


    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('GroupPermission');
    }
}
