<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateISQuestionTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('IS_Question', function(Blueprint $table)
		{
			$table->increments('id');
			$table->string('question');
			$table->integer('vertical_id')->unsigned()->index('vertical_id');
			$table->enum('status', array('1','0'))->nullable();
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('IS_Question');
	}

}
