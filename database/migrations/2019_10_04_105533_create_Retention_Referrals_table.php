<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateRetentionReferralsTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('Retention_Referrals', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name', 100);
            $table->string('email', 100);
            $table->string('phone', 100);
            $table->integer('city_id')->unsigned()->index('city_id');
            $table->integer('referer_user_id')->unsigned()->index('referer_user_id');
            $table->integer('year');
            $table->dateTime('added_on');
        });
    }


    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('Retention_Referrals');
    }
}
