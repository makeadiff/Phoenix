<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateFAMUserEvaluatorTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('FAM_UserEvaluator', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->unsigned()->index('user_id');
            $table->integer('evaluator_id')->unsigned()->index('evaluator_id');
            $table->integer('group_id')->unsigned()->index('group_id');
            $table->integer('year');
        });
    }


    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('FAM_UserEvaluator');
    }
}
