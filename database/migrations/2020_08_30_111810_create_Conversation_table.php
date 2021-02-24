<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateConversationTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('Conversation', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('assigned_to_user_id');
            $table->enum('type', ['check-in', 'developmental', 'exit']);
            $table->enum('stage', ['scheduled', 'done'])->default('scheduled');

            $table->date('scheduled_on')->nullable();
            $table->mediumText('comment')->nullable();
            $table->unsignedBigInteger('followup_to_conversation_id')->default(0)->nullable();

            $table->unsignedBigInteger('added_by_user_id');
            $table->dateTime('added_on');
            $table->dateTime('updated_on');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('Conversation');
    }
}
