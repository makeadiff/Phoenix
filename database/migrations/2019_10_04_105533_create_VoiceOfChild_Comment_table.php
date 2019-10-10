<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateVoiceOfChildCommentTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('VoiceOfChild_Comment', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('added_by_user_id')->unsigned()->index('added_by_user_id');
			$table->integer('student_id')->unsigned()->index('student_id');
			$table->text('question', 16777215);
			$table->string('type', 100);
			$table->string('tags', 100)->nullable();
			$table->text('answer');
			$table->string('priority', 100)->nullable();
			$table->string('actionable', 100);
			$table->enum('escalation_status', array('open','closed','none'))->default('none');
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
		Schema::drop('VoiceOfChild_Comment');
	}

}
