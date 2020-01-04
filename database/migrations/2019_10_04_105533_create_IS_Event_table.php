<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateISEventTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('IS_Event', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name', 100);
            $table->dateTime('added_on');
            $table->enum('status', array('1','0'))->nullable();
            $table->integer('vertical_id')->unsigned()->index('vertical_id');
        });
    }


    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('IS_Event');
    }
}
