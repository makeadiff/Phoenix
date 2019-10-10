<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateLevelTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('Level', function(Blueprint $table)
		{
			$table->increments('id');
			$table->string('name', 100);
			$table->integer('grade');
			$table->integer('center_id')->unsigned()->index('center_id');
			$table->enum('medium', array('vernacular','english'))->default('english');
			$table->enum('preferred_gender', array('male','female','any'))->default('any');
			$table->integer('medium_id')->index('medium_id');
			$table->integer('project_id')->unsigned();
			$table->integer('book_id')->unsigned()->index('book_id');
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
		Schema::drop('Level');
	}

}
