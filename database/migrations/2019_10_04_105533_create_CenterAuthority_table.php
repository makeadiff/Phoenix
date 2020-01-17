<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateCenterAuthorityTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('CenterAuthority', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('center_id')->unsigned()->index('center_id');
            $table->string('name', 100);
            $table->string('phone', 100);
            $table->string('email', 100)->nullable();
            $table->enum('status', array('1','0'))->nullable()->default('1');
        });
    }


    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('CenterAuthority');
    }
}
