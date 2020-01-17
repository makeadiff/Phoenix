<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateRetentionUserGroupPreferenceTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('Retention_UserGroupPreference', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->unsigned()->index('user_id');
            $table->integer('group_id')->unsigned()->index('group_id');
            $table->string('preference', 100);
            $table->integer('shelter_id')->unsigned()->index('shelter_id');
            $table->integer('city_id')->unsigned()->index('city_id');
            $table->integer('year');
            $table->dateTime('added_on');
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
        Schema::drop('Retention_UserGroupPreference');
    }
}
