<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateLinkTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('Link', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name', 100);
            $table->string('url', 200);
            $table->text('text');
            $table->bigInteger('vertical_id')->unsigned()->default(0)->index('vertical_id');
            $table->bigInteger('group_id')->unsigned()->default(0)->index('group_id');
            $table->bigInteger('city_id')->unsigned()->default(0)->index('city_id');
            $table->bigInteger('center_id')->unsigned()->default(0)->index('center_id');
            $table->smallInteger('sort_order')->nullable();
            $table->dateTime('added_on');
            $table->dateTime('updated_on');
            $table->enum('status', ['0','1'])->default('1');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('Link');
    }
}
