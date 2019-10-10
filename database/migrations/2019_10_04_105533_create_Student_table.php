<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateStudentTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('Student', function(Blueprint $table)
		{
			$table->increments('id');
			$table->string('name', 100);
			$table->date('birthday');
			$table->enum('sex', array('m','f','u'))->default('u');
			$table->integer('center_id')->unsigned();
			$table->string('description');
			$table->string('photo', 100);
			$table->string('thumbnail', 200)->nullable()->default('');
			$table->dateTime('added_on');
			$table->text('reason_for_leaving', 16777215)->nullable();
			$table->enum('status', array('1','0'))->default('1');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('Student');
	}

}
