<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateExamTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('Exam', function(Blueprint $table)
		{
			$table->increments('id');
			$table->string('name', 100);
			$table->dateTime('exam_on');
			$table->integer('exam_type_id')->unsigned()->index('exam_type_id');
			$table->enum('status', array('0','1'))->nullable();
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
		Schema::drop('Exam');
	}

}
