<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateDataTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('Data', function (Blueprint $table) {
            $table->increments('id');
            $table->enum('item', array('User','Student','City','Center','Event','Class','Batch','Level'))->default('User');
            $table->integer('item_id')->unsigned()->index('item_id');
            $table->string('name', 100);
            $table->text('data', 65535);
            $table->integer('year');
            $table->dateTime('added_on');
            $table->integer('added_by_user_id')->unsigned()->default(0)->index('added_by_user_id');
        });
    }


    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('Data');
    }
}
