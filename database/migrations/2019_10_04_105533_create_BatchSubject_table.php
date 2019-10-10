<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateBatchSubjectTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('BatchSubject', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('batch_id')->unsigned()->index('batch_id');
			$table->integer('subject_id')->unsigned()->index('subject_id');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('BatchSubject');
	}

}
