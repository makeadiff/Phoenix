<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateStudentClassTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('StudentClass', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('student_id')->unsigned()->index('student_id');
			$table->integer('class_id')->unsigned()->index('class_id');
			$table->enum('present', array('1','0'))->nullable();
			$table->integer('participation');
			$table->integer('check_for_understanding')->default(0);
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('StudentClass');
	}

}
