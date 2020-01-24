<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateSurveyTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('Survey', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name', 100)->nullable();
            $table->integer('survey_template_id')->unsigned()->index('survey_template_id');
            $table->integer('added_by_user_id')->unsigned()->index('added_by_user_id');
            $table->dateTime('added_on');
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
        Schema::drop('Survey');
    }
}
