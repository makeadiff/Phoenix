<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateFAMUserGroupPreferenceTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('FAM_UserGroupPreference', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('user_id')->unsigned()->index('user_id');
			$table->integer('group_id')->unsigned()->index('group_id');
			$table->integer('evaluator_id')->unsigned()->index('evaluator_id');
			$table->integer('preference');
			$table->string('taskfolder_link', 100);
			$table->integer('city_id')->unsigned()->default(0);
			$table->dateTime('added_on');
			$table->integer('year');
			$table->enum('status', array('pending','selected','rejected','withdrawn'))->default('pending');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('FAM_UserGroupPreference');
	}

}
