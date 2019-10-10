<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateFAMReferralTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('FAM_Referral', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('referer_user_id')->unsigned()->index('referer_user_id');
			$table->integer('referee_user_id')->unsigned()->index('referee_user_id');
			$table->integer('group_id')->unsigned()->index('group_id');
			$table->dateTime('created_at');
			$table->integer('year');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('FAM_Referral');
	}

}
