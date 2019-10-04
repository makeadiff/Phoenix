<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateSubjectTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('Subject', function(Blueprint $table)
		{
			$table->increments('id');
			$table->string('name', 100);
			$table->string('unit_count', 100);
			$table->integer('city_id')->unsigned()->index('city_id');
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
		Schema::drop('Subject');
	}

}
