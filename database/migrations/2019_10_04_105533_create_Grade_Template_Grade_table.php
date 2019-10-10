<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateGradeTemplateGradeTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('Grade_Template_Grade', function(Blueprint $table)
		{
			$table->increments('id');
			$table->string('grade', 10);
			$table->integer('from_mark');
			$table->integer('to_mark');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('Grade_Template_Grade');
	}

}
