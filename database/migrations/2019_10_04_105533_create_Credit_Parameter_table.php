<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateCreditParameterTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('Credit_Parameter', function(Blueprint $table)
		{
			$table->increments('id');
			$table->string('name', 100);
			$table->string('description');
			$table->float('positive', 10, 0);
			$table->float('negative', 10, 0);
			$table->integer('vertical_id')->unsigned()->index('vertical_id');
			$table->enum('status', array('0','1'))->nullable()->default('1');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('Credit_Parameter');
	}

}
