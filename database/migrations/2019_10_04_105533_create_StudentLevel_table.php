<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateStudentLevelTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('StudentLevel', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('student_id')->unsigned()->index('student_id');
			$table->integer('level_id')->unsigned()->index('level_id');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('StudentLevel');
	}

}
