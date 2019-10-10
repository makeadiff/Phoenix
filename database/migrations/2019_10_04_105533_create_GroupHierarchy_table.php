<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateGroupHierarchyTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('GroupHierarchy', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('group_id')->unsigned()->index('group_id');
			$table->integer('reports_to_group_id')->unsigned()->index('reports_to_group_id');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('GroupHierarchy');
	}

}
