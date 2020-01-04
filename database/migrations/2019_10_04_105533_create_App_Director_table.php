<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateAppDirectorTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('App_Director', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name', 100);
            $table->string('role', 100);
            $table->string('email', 100);
            $table->string('linkedin', 100);
            $table->string('image', 200);
            $table->text('description', 16777215);
            $table->integer('sort');
            $table->integer('user_id')->unsigned();
            $table->enum('status', array('1','0'))->default('1');
        });
    }


    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('App_Director');
    }
}
