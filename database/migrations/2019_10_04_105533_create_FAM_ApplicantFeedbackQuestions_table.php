<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateFAMApplicantFeedbackQuestionsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('FAM_ApplicantFeedbackQuestions', function(Blueprint $table)
		{
			$table->increments('id');
			$table->text('question', 16777215);
			$table->enum('type', array('radio','scale','text'))->default('radio');
			$table->enum('target', array('all','fellow','volunteer','strat'));
			$table->text('description', 16777215)->nullable();
			$table->text('comment', 16777215)->nullable();
			$table->enum('status', array('0','1'))->nullable()->default('1');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('FAM_ApplicantFeedbackQuestions');
	}

}
