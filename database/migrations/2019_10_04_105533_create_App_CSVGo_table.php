<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateAppCSVGoTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('App_CSVGo', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name', 100)->unique('name');
            $table->string('description', 250);
            $table->text('query', 16777215);
            $table->string('db', 50);
            $table->dateTime('added_on');
            $table->dateTime('last_run_on');
            $table->integer('vertical_id')->unsigned();
            $table->enum('status', array('0','1'))->default('1');
        });
    }


    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('App_CSVGo');
    }
}
