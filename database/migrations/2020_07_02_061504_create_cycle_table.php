<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCycleTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('SSG_Cycle', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name', 100);
            $table->date('start_on');
            $table->date('end_on');
            $table->integer('ssg_level_id')->unsigned()->index('ssg_level_id');
            $table->dateTime('added_on');
            $table->dateTime('updated_on');
            $table->integer('added_by_user_id')->unsigned()->nullable();
            $table->enum('status', array('1','0'))->default('1');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('SSG_Cycle');
    }
}
// SSG_Cycle
//     id
//     name
//     start_on
//     end_on
//     ssg_level_id # Because SSG groups are stored in Level table.
//     added_on
//     updated_on
//     added_by_user_id