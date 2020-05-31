<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCreditTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('Credit', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('user_id')->unsigned()->index('user_id');
            // $table->integer('credit_parameter_id')->unsigned()->index('credit_parameter_id');
            $table->integer('parameter_id')->unsigned()->index('parameter_id');
            $table->float('change', 5, 2);
            $table->float('current_credit', 5, 2)->default(0);
            // $table->enum('positive_or_negative', ['1', '-1'])->nullable();
            $table->string('item')->nullable();
            $table->integer('item_id')->unsigned()->nullable();
            $table->string('comment')->nullable();
            $table->dateTime('added_on');
            $table->integer('added_by_user_id')->unsigned()->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('Credit');
    }
}
