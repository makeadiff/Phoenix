<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateSurveyQuestionCategoryTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('Survey_Question_Category', function(Blueprint $table)
		{
			$table->increments('id');
			$table->string('name', 100);
			$table->integer('survey_template_id')->unsigned()->index('survey_template_id');
			$table->integer('sort_order')->nullable();
			$table->enum('status', array('0','1'))->nullable()->default('1');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('Survey_Question_Category');
	}

}
