<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateGroupTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('Group', function(Blueprint $table)
		{
			$table->increments('id');
			$table->string('name', 100);
			$table->enum('type', array('executive','national','fellow','volunteer','strat'));
			$table->enum('group_type', array('normal','hierarchy'))->default('normal');
			$table->integer('vertical_id')->unsigned()->index('vertical_id');
			$table->integer('region_id')->unsigned();
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
		Schema::drop('Group');
	}

}
