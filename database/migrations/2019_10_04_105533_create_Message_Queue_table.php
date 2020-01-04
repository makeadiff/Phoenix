<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateMessageQueueTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('Message_Queue', function (Blueprint $table) {
            $table->increments('id');
            $table->enum('type', array('email','sms'))->default('email');
            $table->string('to', 100);
            $table->string('from', 100);
            $table->string('subject', 100);
            $table->text('body', 65535);
            $table->text('images', 65535);
            $table->text('attachments', 65535);
            $table->text('info', 16777215);
            $table->dateTime('added_on');
            $table->enum('status', array('pending','sent'))->nullable()->default('pending');
            $table->dateTime('sent_on')->nullable();
        });
    }


    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('Message_Queue');
    }
}
