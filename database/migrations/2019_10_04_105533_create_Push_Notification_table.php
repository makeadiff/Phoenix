<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreatePushNotificationTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('Push_Notification', function(Blueprint $table)
		{
			$table->integer('id', true);
			$table->integer('user_id')->index('user_id');
			$table->string('imei_no', 100)->index('imei_no');
			$table->string('hash_key', 300)->default('')->index('hash_key');
			$table->string('fcm_regid', 300)->nullable();
			$table->integer('app_version')->nullable();
			$table->integer('status')->index('status')->comment('1 - Active,   0 - Inactive');
			$table->dateTime('created_on');
			$table->timestamp('updated_on')->default(DB::raw('CURRENT_TIMESTAMP'));
			$table->enum('platform', array('Web','Android','Ios'))->nullable();
			$table->enum('app', array('Website','UPMA','Donut',''))->nullable();
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('Push_Notification');
	}

}
