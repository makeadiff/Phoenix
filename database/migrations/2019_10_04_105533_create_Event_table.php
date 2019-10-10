<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateEventTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('Event', function(Blueprint $table)
		{
			$table->increments('id');
			$table->string('name', 100);
			$table->text('description', 16777215);
			$table->dateTime('starts_on');
			$table->dateTime('ends_on')->nullable();
			$table->string('place', 200);
			$table->string('type', 200)->nullable()->default('others');
			$table->integer('city_id')->unsigned()->index('city_id');
			$table->integer('event_type_id');
			$table->integer('vertical_id')->unsigned()->index('vertical_id');
			$table->integer('template_event_id')->unsigned()->default(0);
			$table->text('user_selection_options', 65535)->nullable();
			$table->integer('created_by_user_id');
			$table->string('latitude', 50);
			$table->string('longitude', 50);
			$table->dateTime('created_on');
			$table->dateTime('updated_on');
			$table->integer('notification_status')->default(0);
			$table->enum('created_from', array('1','2'))->default('1');
			$table->enum('status', array('0','1'))->default('1');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('Event');
	}

}
