<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateDonutDeletedDonationTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('Donut_Deleted_Donation', function(Blueprint $table)
		{
			$table->increments('id');
			$table->enum('type', array('cash','cheque','nach','globalgiving','giveindia','online','other'))->default('cash');
			$table->integer('fundraiser_user_id')->unsigned()->index('fundraiser_user_id');
			$table->integer('donor_id')->unsigned()->index('donor_id');
			$table->enum('status', array('collected','deposited','receipted'));
			$table->float('amount', 10, 0)->nullable();
			$table->dateTime('nach_start_on')->nullable();
			$table->dateTime('nach_end_on')->nullable();
			$table->string('cheque_no', 100)->nullable();
			$table->dateTime('added_on');
			$table->dateTime('updated_on');
			$table->integer('updated_by_user_id')->unsigned()->index('updated_by_user_id');
			$table->integer('with_user_id')->unsigned()->index('with_user_id');
			$table->string('comment', 100);
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('Donut_Deleted_Donation');
	}

}
