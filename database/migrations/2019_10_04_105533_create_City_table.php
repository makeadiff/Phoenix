<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateCityTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('City', function(Blueprint $table)
		{
			$table->increments('id');
			$table->string('name', 100);
			$table->integer('president_id')->unsigned()->index('president_id');
			$table->dateTime('added_on');
			$table->enum('classes_happening', array('1','0'))->default('1');
			$table->integer('region_id')->unsigned()->index('region_id');
			$table->string('latitude', 20);
			$table->string('longitude', 20);
			$table->enum('type', array('actual','virtual'))->default('actual');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('City');
	}

}
