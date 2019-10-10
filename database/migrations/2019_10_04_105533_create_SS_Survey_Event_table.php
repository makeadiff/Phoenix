<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateSSSurveyEventTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('SS_Survey_Event', function(Blueprint $table)
		{
			$table->increments('id');
			$table->string('name', 200);
			$table->integer('cycle')->nullable();
			$table->integer('stage')->nullable();
			$table->integer('started_by_user_id')->unsigned()->index('started_by_user_id');
			$table->dateTime('added_on');
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
		Schema::drop('SS_Survey_Event');
	}

}
