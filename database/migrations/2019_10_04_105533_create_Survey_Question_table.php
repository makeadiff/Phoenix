<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateSurveyQuestionTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('Survey_Question', function(Blueprint $table)
		{
			$table->increments('id');
			$table->string('name', 100)->nullable();
			$table->string('question');
			$table->string('description')->nullable();
			$table->integer('survey_question_category_id')->unsigned()->default(0)->index('survey_question_category_id');
			$table->integer('survey_template_id')->unsigned()->index('survey_template_id');
			$table->enum('response_type', array('text','choice','multi-choice','number','1-10','1-5','yes-no','date','datetime','longtext','file'))->default('text');
			$table->enum('required', array('1','0'))->default('0');
			$table->integer('sort_order')->nullable();
			$table->string('options')->nullable();
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
		Schema::drop('Survey_Question');
	}

}
