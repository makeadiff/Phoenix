<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddDonationFrequencyToOnlineDonationTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('Online_Donation', function (Blueprint $table) {
            $table->enum('frequency', ['one-time','recurring'])->default('one-time');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('Online_Donation', function (Blueprint $table) {
            $table->dropColumn('frequency');
        });
    }
}
