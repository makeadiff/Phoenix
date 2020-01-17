<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateCenterProjectTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('CenterProject', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('center_id')->unsigned()->index('center_id');
            $table->integer('project_id')->unsigned()->index('project_id');
            $table->string('year', 100);
            $table->dateTime('added_on');
        });
    }


    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('CenterProject');
    }
}
