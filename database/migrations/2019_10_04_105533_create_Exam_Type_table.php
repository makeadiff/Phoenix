<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateExamTypeTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('Exam_Type', function(Blueprint $table)
		{
			$table->increments('id');
			$table->string('name', 100);
			$table->enum('ferquency', array('mothly','unit','year','semester','quarter','weekly','daily','other'))->nullable();
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('Exam_Type');
	}

}
