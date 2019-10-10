<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateDonutDepositTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('Donut_Deposit', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('collected_from_user_id')->unsigned()->index('collected_from_user_id');
			$table->integer('given_to_user_id')->unsigned()->index('given_to_user_id');
			$table->dateTime('added_on');
			$table->dateTime('reviewed_on')->nullable()->comment('Approved/Rejected Time');
			$table->float('amount', 10, 0)->nullable();
			$table->string('deposit_information', 200)->nullable();
			$table->enum('status', array('pending','approved','rejected'))->nullable()->default('pending');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('Donut_Deposit');
	}

}
