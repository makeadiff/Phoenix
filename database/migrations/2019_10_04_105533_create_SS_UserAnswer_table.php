<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateSSUserAnswerTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('SS_UserAnswer', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('question_id')->unsigned()->index('question_id');
			$table->integer('user_id')->unsigned()->index('user_id');
			$table->string('answer', 100);
			$table->integer('survey_event_id')->unsigned();
			$table->text('comment', 65535);
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
		Schema::drop('SS_UserAnswer');
	}

}
