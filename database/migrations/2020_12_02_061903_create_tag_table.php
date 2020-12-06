<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateTagTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('Tag', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name',100);
            $table->dateTime('added_on');
        });

        Schema::create('TagItem', function (Blueprint $table) {
            $table->increments('id');
            $table->enum('item_type', ['User','Student','City','Center','Event','Comment','Class','Batch','Level']);
            $table->bigInteger('item_id')->unsigned()->index('item_id');
            $table->bigInteger('tag_id')->unsigned()->index('tag_id');
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
        Schema::drop('Tag');
        Schema::drop('TagItem');
    }
}
