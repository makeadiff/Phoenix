<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateParameterTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('Parameter', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name', 100);
            $table->string('description');
            $table->float('credit', 5, 2);
            $table->integer('vertical_id')->unsigned()->index('vertical_id');
            $table->dateTime('added_on');
            $table->dateTime('updated_on');
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
        Schema::dropIfExists('Parameter');
    }
}
