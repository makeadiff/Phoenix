<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateCenterDataTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('CenterData', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('center_id')->unsigned()->index('center_id');
			$table->string('name', 100);
			$table->integer('year');
			$table->string('value');
			$table->text('data', 16777215);
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('CenterData');
	}

}
