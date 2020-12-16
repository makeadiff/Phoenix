<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddParentDonationFieldToOnlineDonation extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('Online_Donation', function (Blueprint $table) {
            $table->unsignedBigInteger('parent_donation_id')->nullable()->default(null);
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
            $table->dropColumn('parent_donation_id');
        });
    }
}
