<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateExamSubjectTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ExamSubject', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('exam_id')->unsigned()->index('exam_id');
            $table->integer('subject_id')->unsigned()->index('subject_id');
        });
    }


    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('ExamSubject');
    }
}
