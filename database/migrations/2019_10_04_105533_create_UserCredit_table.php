<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateUserCreditTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('UserCredit', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('user_id')->unsigned()->index('user_id');
			$table->integer('credit')->nullable();
			$table->integer('credit_assigned_by_user_id')->unsigned()->index('credit_assigned_by_user_id');
			$table->string('comment', 250);
			$table->dateTime('added_on');
			$table->integer('year')->nullable();
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('UserCredit');
	}

}
