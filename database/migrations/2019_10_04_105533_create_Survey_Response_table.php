<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateSurveyResponseTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('Survey_Response', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('survey_id')->unsigned()->index('survey_id');
			$table->integer('responder_id')->unsigned()->index('responder_user_id');
			$table->integer('survey_question_id')->unsigned()->index('survey_question_id');
			$table->integer('survey_choice_id')->unsigned()->default(0)->index('survey_choice_id');
			$table->text('response', 16777215)->nullable();
			$table->dateTime('added_on');
			$table->integer('added_by_user_id')->unsigned()->default(0)->index('added_by_user_id');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('Survey_Response');
	}

}
