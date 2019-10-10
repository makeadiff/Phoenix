<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateFAMUserTaskTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('FAM_UserTask', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('user_id')->unsigned()->index('user_id');
			$table->text('common_task_url', 16777215);
			$table->text('common_task_files', 16777215)->nullable();
			$table->integer('preference_1_group_id')->unsigned()->index('preference_1_group_id');
			$table->text('preference_1_task_files', 16777215);
			$table->text('preference_1_video_files', 16777215)->nullable();
			$table->integer('preference_2_group_id')->unsigned()->nullable()->index('preference_2_group_id');
			$table->text('preference_2_task_files', 16777215);
			$table->text('preference_2_video_files', 16777215)->nullable();
			$table->integer('preference_3_group_id')->unsigned()->nullable()->index('preference_3_group_id');
			$table->text('preference_3_task_files', 16777215);
			$table->text('preference_3_video_files', 16777215)->nullable();
			$table->integer('year');
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
		Schema::drop('FAM_UserTask');
	}

}
