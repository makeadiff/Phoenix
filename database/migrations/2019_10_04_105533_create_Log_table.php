<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateLogTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('Log', function(Blueprint $table)
		{
			$table->increments('id');
			$table->string('name', 100);
			$table->string('log', 200);
			$table->integer('user_id')->unsigned()->index('user_id');
			$table->dateTime('added_on');
			$table->enum('level', array('info','warning','error','critical'))->default('info');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('Log');
	}

}
