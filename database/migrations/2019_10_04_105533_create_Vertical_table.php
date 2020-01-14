<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateVerticalTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('Vertical', function (Blueprint $table) {
            $table->increments('id');
            $table->string('key', 100);
            $table->string('name', 100);
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
        Schema::drop('Vertical');
    }
}
