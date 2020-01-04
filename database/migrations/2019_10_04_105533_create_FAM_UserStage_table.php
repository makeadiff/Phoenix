<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateFAMUserStageTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('FAM_UserStage', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->unsigned()->index('user_id');
            $table->integer('group_id')->unsigned()->index('group_id');
            $table->integer('stage_id')->unsigned()->index('stage_id');
            $table->integer('evaluator_id')->unsigned()->index('evaluator_id');
            $table->text('comment', 16777215);
            $table->integer('year');
            $table->enum('status', array('pending','selected','rejected','maybe','free-pool'))->nullable()->default('pending');
            $table->integer('shelter_id')->nullable();
        });
    }


    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('FAM_UserStage');
    }
}
