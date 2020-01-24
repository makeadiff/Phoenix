<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateBatchLevelTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('BatchLevel', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('batch_id')->unsigned()->index('batch_id');
            $table->integer('level_id')->unsigned()->index('level_id');
            $table->integer('year');
            $table->unique(['batch_id','level_id','year'], 'uniqification');
        });
    }


    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('BatchLevel');
    }
}
