<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateGradeTemplateCollectionTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('Grade_Template_Collection', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('grade_id')->unsigned()->index('grade_id');
			$table->integer('grade_template_id')->unsigned()->index('grade_template_id');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('Grade_Template_Collection');
	}

}
