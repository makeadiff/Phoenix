<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateUserEventTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('UserEvent', function(Blueprint $table)
		{
			$table->integer('id', true);
			$table->integer('user_id')->unsigned()->index('user_id');
			$table->integer('event_id')->unsigned()->index('event_id');
			$table->enum('present', array('0','1','3'))->nullable()->comment('1- present, 3- missed');
			$table->enum('late', array('0','1'))->default('0');
			$table->enum('user_choice', array('0','1','2','3'))->default('0')->comment('0-Default,1-Go,2-Maybe,3-Cant Go');
			$table->text('reason', 65535)->nullable()->comment('If Cant Go, need reason');
			$table->enum('created_from', array('1','2'))->default('1')->comment('1-Web,2-App');
			$table->integer('type')->default(0)->comment('1- Invite Events, 2 - Mark attendance');
			$table->dateTime('created_on');
			$table->string('rsvp_auth_key')->default('');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('UserEvent');
	}

}
