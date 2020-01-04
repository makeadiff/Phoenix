<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateCommentTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('Comment', function (Blueprint $table) {
            $table->increments('id');
            $table->enum('item', array('User','Student','City','Center','Event'))->default('User');
            $table->integer('item_id')->unsigned()->index('item_id');
            $table->text('comment', 16777215);
            $table->dateTime('added_on');
            $table->integer('added_by_user_id')->unsigned()->index('added_by_user_id');
        });
    }


    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('Comment');
    }
}
