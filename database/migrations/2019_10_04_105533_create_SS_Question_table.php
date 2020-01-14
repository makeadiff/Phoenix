<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateSSQuestionTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('SS_Question', function (Blueprint $table) {
            $table->increments('id');
            $table->string('question');
            $table->enum('status', array('0','1'))->nullable();
            $table->integer('survey_event_id')->unsigned();
        });
    }


    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('SS_Question');
    }
}
