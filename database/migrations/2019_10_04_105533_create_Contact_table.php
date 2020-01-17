<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateContactTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('Contact', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name', 100);
            $table->string('email', 100);
            $table->string('phone', 100);
            $table->enum('sex', array('m','f','o'))->default('m');
            $table->date('birthday');
            $table->enum('is_applicant', array('1','0'))->default('1');
            $table->enum('is_subscribed', array('1','0'))->default('1');
            $table->enum('is_care_collective', array('1','0'))->default('0');
            $table->integer('city_id')->unsigned()->index('city_id');
            $table->enum('source', array('friends','campaign','website','other'))->default('friends');
            $table->string('address');
            $table->string('company', 100);
            $table->string('latitude', 100);
            $table->string('longitute', 100);
            $table->enum('job_status', array('student','working','other'))->default('student');
            $table->string('why_mad', 100);
            $table->text('info', 16777215);
            $table->dateTime('added_on');
            $table->dateTime('updated_on');
            $table->enum('status', array('1','0'))->nullable()->default('1');
        });
    }


    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('Contact');
    }
}
