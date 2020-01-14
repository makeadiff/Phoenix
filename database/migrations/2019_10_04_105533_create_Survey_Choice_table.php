<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateSurveyChoiceTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('Survey_Choice', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name', 200);
            $table->string('description')->nullable();
            $table->integer('value')->nullable();
            $table->integer('survey_question_id')->unsigned()->index('survey_question_id');
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
        Schema::drop('Survey_Choice');
    }
}
