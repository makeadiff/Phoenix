<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateFRUserCreditTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('FR_UserCredit', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('user_id')->unsigned()->index('user_id');
			$table->integer('credit_parameter_id')->unsigned()->index('credit_parameter_id');
			$table->float('change', 10, 0);
			$table->float('current_credit', 10, 0);
			$table->string('comment');
			$table->dateTime('week_start_on');
			$table->dateTime('added_on');
			$table->integer('marked_by_user_id')->unsigned()->index('marked_by_user_id');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('FR_UserCredit');
	}

}
