<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateProjectTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('Project', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name', 100);
            $table->dateTime('added_on');
            $table->integer('vertical_id')->unsigned()->index('vertical_id');
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
        Schema::drop('Project');
    }
}
