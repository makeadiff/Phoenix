<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateDonutNetworkDataTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('Donut_NetworkData', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('donut_network_id')->unsigned()->index('donut_network_id');
			$table->string('name', 100);
			$table->string('value', 100);
			$table->text('data', 16777215)->nullable();
			$table->dateTime('added_on');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('Donut_NetworkData');
	}

}
