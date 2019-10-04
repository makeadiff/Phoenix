<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateDonutDonationVersionTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('Donut_Donation_Version', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('donation_id')->unsigned()->index('donation_id');
			$table->enum('type', array('cash','cheque','ecs','globalgiving','online','other'))->default('cash');
			$table->integer('fundraiser_user_id')->unsigned()->index('fundraiser_user_id');
			$table->integer('donor_id')->unsigned()->index('donor_id');
			$table->enum('status', array('collected','deposited','receipted'));
			$table->float('amount', 10, 0)->nullable();
			$table->string('cheque_no', 100);
			$table->dateTime('created_on');
			$table->dateTime('updated_on');
			$table->string('comment', 100);
			$table->string('version', 100);
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('Donut_Donation_Version');
	}

}
