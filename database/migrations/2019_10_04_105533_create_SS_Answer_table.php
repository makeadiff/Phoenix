<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateSSAnswerTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('SS_Answer', function(Blueprint $table)
		{
			$table->increments('id');
			$table->string('answer');
			$table->integer('question_id')->unsigned()->index('question_id');
			$table->string('level', 100);
			$table->enum('status', array('0','1'))->nullable();
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('SS_Answer');
	}

}
