<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateDonutDonorTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('Donut_Donor', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->string('phone', 100);
            $table->string('email', 100);
            $table->string('address')->nullable();
            $table->string('donor_finance_id', 30)->nullable();
            $table->integer('added_by_user_id')->unsigned()->nullable()->index('added_by_user_id');
            $table->dateTime('added_on');
            $table->dateTime('updated_on')->nullable();
        });
    }


    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('Donut_Donor');
    }
}
