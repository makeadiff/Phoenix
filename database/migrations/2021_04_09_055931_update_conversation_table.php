<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateConversationTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('Conversation', function (Blueprint $table) {
            // $table->enum('type', ['check-in', 'developmental', 'exit', 'appriciation'])->change();
            DB::statement("ALTER TABLE `Conversation` CHANGE `type` `type` SET('appreciation','check-in','developmental','exit') NOT NULL DEFAULT 'check-in';");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('Conversation', function (Blueprint $table) {
            // $table->enum('type', ['check-in', 'developmental', 'exit'])->change();
            DB::statement("ALTER TABLE `Conversation` CHANGE `type` `type` SET('check-in','developmental','exit') NOT NULL DEFAULT 'check-in';");
        });
    }
}
