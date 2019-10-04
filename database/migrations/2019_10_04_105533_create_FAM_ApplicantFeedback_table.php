<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateFAMApplicantFeedbackTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('FAM_ApplicantFeedback', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('applicant_user_id')->unsigned()->index('applicant_user_id');
			$table->integer('reviewer_user_id')->unsigned()->index('reviewer_user_id');
			$table->integer('question_id')->unsigned()->index('question_id');
			$table->text('feedback', 16777215);
			$table->text('comment', 16777215)->nullable();
			$table->dateTime('added_on');
			$table->enum('confidential', array('1','0','',''))->nullable();
			$table->integer('year');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('FAM_ApplicantFeedback');
	}

}
