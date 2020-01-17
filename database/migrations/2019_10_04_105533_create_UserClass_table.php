<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateUserClassTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('UserClass', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->unsigned()->index('user_id');
            $table->integer('class_id')->unsigned()->index('class_id');
            $table->integer('substitute_id')->unsigned()->index('substitute_id');
            $table->enum('zero_hour_attendance', array('0','1'))->default('1');
            $table->enum('status', array('projected','confirmed','attended','absent','cancelled'))->nullable();
        });
    }


    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('UserClass');
    }
}
