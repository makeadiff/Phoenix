<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateBatchTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('Batch', function(Blueprint $table)
		{
			$table->increments('id');
			$table->enum('day', array('0','1','2','3','4','5','6'))->default('0');
			$table->time('class_time');
			$table->integer('batch_head_id')->unsigned()->index('batch_head_id');
			$table->integer('center_id')->unsigned()->index('center_id');
			$table->integer('subject_id')->unsigned();
			$table->integer('project_id')->unsigned()->index('project_id');
			$table->integer('year');
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
		Schema::drop('Batch');
	}

}
