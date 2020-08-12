<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddFrequencyRepeatUntilToEvent extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('Event', function (Blueprint $table) {
            $table->date('repeat_until')->nullable();
            $table->enum('frequency',array('monthly','weekly','none'))->default('none');            
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('Event', function (Blueprint $table) {
            $table->dropColumn('repeat_until');
            $table->dropColumn('frequency');
        });
    }
}
