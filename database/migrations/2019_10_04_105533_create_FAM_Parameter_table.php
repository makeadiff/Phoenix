<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateFAMParameterTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('FAM_Parameter', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('stage_id')->unsigned()->index('stage_id');
            $table->integer('category_id')->unsigned()->index('category_id');
            $table->string('name', 100);
            $table->enum('type', array('yes-no','1-5','text'))->nullable();
            $table->enum('required', array('1','0'))->default('1');
            $table->integer('sort');
            $table->enum('status', array('0','1'))->nullable();
        });
    }


    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('FAM_Parameter');
    }
}
