<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateFAMEvaluationTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('FAM_Evaluation', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->unsigned()->index('user_id');
            $table->integer('parameter_id')->unsigned()->index('parameter_id');
            $table->integer('evaluator_id')->unsigned()->index('evaluator_user_id');
            $table->text('response', 16777215);
            $table->dateTime('added_on');
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
        Schema::drop('FAM_Evaluation');
    }
}
