<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateUserTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('User', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name', 100);
            $table->string('title', 100)->nullable();
            $table->string('email', 100);
            $table->string('mad_email', 100)->nullable();
            $table->string('phone', 11);
            $table->enum('sex', array('m','f','o'))->default('f');
            $table->string('password', 100)->nullable();
            $table->string('password_hash', 150)->nullable();
            $table->string('auth_token', 100)->nullable();
            $table->string('photo', 200)->nullable();
            $table->dateTime('joined_on');
            $table->string('address')->nullable();
            $table->text('bio', 16777215)->nullable();
            $table->string('facebook_id', 20)->nullable();
            $table->string('verification_status', 250)->nullable();
            $table->integer('profile_progress')->default(0);
            $table->enum('source', array('friends','college','media','internet','sms','other'))->default('other');
            $table->string('source_other')->nullable();
            $table->date('birthday')->nullable();
            $table->enum('job_status', array('working','student','other','high school','college','self-employed'))->default('student');
            $table->string('edu_institution', 225)->nullable();
            $table->string('company', 225)->nullable();
            $table->enum('preferred_day', array('flexible','weekday','weekend'))->default('flexible');
            $table->string('applied_role', 100)->nullable();
            $table->text('why_mad', 16777215)->nullable();
            $table->date('left_on')->nullable();
            $table->dateTime('added_on');
            $table->dateTime('updated_on');
            $table->text('reason_for_leaving', 16777215)->nullable();
            $table->enum('induction_status', array('0','1'))->default('0');
            $table->enum('teacher_training_status', array('0','1'))->default('0');
            $table->integer('center_id')->unsigned()->nullable()->index('center_id');
            $table->string('city_other', 50)->nullable();
            $table->integer('city_id')->unsigned()->index('city_id');
            $table->integer('subject_id')->unsigned()->nullable()->index('subject_id');
            $table->integer('project_id')->unsigned()->nullable();
            $table->enum('user_type', array('applicant','volunteer','well_wisher','alumni','let_go','left_before_induction','other'))->default('volunteer');
            $table->float('credit', 10, 0)->default(3);
            $table->integer('consecutive_credit')->nullable();
            $table->integer('admin_credit')->nullable();
            $table->string('campaign', 100)->nullable();
            $table->string('zoho_user_id', 100)->nullable();
            $table->enum('status', array('1','0'))->default('1');
            $table->string('app_version', 150)->nullable();
        });
    }


    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('User');
    }
}
