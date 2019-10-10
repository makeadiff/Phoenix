<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateFAMParameterCategoryTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('FAM_Parameter_Category', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('stage_id')->unsigned()->index('stage_id');
			$table->integer('group_id')->default(0)->comment('\'0\' means applicable for all groups');
			$table->string('name', 100);
			$table->enum('status', array('0','1'))->nullable();
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('FAM_Parameter_Category');
	}

}
