<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateDonutDonationDepositTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('Donut_DonationDeposit', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('deposit_id')->unsigned()->index('deposit_id');
			$table->integer('donation_id')->unsigned()->index('donation_id');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('Donut_DonationDeposit');
	}

}
