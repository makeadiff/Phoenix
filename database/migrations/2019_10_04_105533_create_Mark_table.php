<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateMarkTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('Mark', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('student_id')->unsigned()->index('student_id');
			$table->integer('subject_id')->unsigned()->index('subject_id');
			$table->integer('exam_id')->unsigned()->index('exam_id');
			$table->float('marks', 10, 0)->nullable();
			$table->float('total', 10, 0)->nullable();
			$table->text('input_data', 65535);
			$table->enum('status', array('updated','not updated','not available','absent','others','fail','pass','re-exam'));
			$table->integer('template_id');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('Mark');
	}

}
