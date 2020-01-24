<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateSurveyTemplateTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('Survey_Template', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name', 100);
            $table->text('description', 16777215)->nullable();
            $table->dateTime('added_on');
            $table->integer('vertical_id')->unsigned()->default(0)->index('vertical_id');
            $table->enum('responder', array('Student','User','Center'))->default('Student');
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
        Schema::drop('Survey_Template');
    }
}
