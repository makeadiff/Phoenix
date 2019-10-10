<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateISResponseTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('IS_Response', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('is_event_id')->unsigned()->index('is_event_id');
			$table->integer('user_id')->unsigned()->index('user_id');
			$table->integer('question_id')->unsigned()->index('question_id');
			$table->integer('student_id')->unsigned()->index('student_id');
			$table->integer('response')->nullable();
			$table->dateTime('added_on');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('IS_Response');
	}

}
