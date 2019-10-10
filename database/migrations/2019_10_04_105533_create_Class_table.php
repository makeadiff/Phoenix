<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateClassTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('Class', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('batch_id')->unsigned()->index('batch_id');
			$table->integer('level_id')->unsigned()->index('level_id');
			$table->integer('project_id')->unsigned()->index('project_id');
			$table->dateTime('class_on');
			$table->string('feedback');
			$table->integer('lesson_id')->unsigned()->index('lesson_id');
			$table->enum('class_type', array('scheduled','extra'))->default('scheduled');
			$table->integer('class_satisfaction');
			$table->enum('cancel_option', array('in-volunteer-unavailable','in-volunteer-engaged','in-volunteer-unassigned','in-other','ext-children-out','ext-children-doing-chores','ext-children-have-events','ext-children-unwell','ext-other','misc','ext-parent-visiting-shelter','ext-holiday','ext-out-school','ext-disturbances-city'))->default('in-volunteer-unavailable');
			$table->string('cancel_reason', 200);
			$table->integer('updated_by_mentor')->unsigned();
			$table->integer('updated_by_teacher')->unsigned();
			$table->enum('status', array('projected','happened','cancelled'))->default('projected');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('Class');
	}

}
