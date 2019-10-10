<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateCenterTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('Center', function(Blueprint $table)
		{
			$table->increments('id');
			$table->string('name', 100);
			$table->integer('city_id')->unsigned()->index('city_id');
			$table->integer('center_head_id')->unsigned()->index('center_head_id');
			$table->date('class_starts_on')->nullable();
			$table->enum('medium', array('vernacular','english'))->default('english');
			$table->enum('preferred_gender', array('male','female','any'))->default('any');
			$table->string('latitude', 20);
			$table->string('longitude', 20);
			$table->integer('authority_id')->nullable();
			$table->enum('type', array('trust','ngo','government_home','school','private_run','semi_government','aftercare','test'))->nullable();
			$table->integer('year_undertaking')->nullable();
			$table->string('phone', 20)->nullable();
			$table->dateTime('updated_on')->nullable();
			$table->enum('status', array('1','0'))->default('1');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('Center');
	}

}
