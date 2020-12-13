<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class RenameOnlineDonationField extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('Online_Donation', function(Blueprint $table) {
            // $table->renameColumn('fundraiser_id', 'fundraiser_user_id')->change();
            DB::statement("ALTER TABLE `Online_Donation` CHANGE `fundraiser_id` `fundraiser_user_id` BIGINT(20) NULL DEFAULT 0");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('Online_Donation', function(Blueprint $table) {
            // $table->renameColumn('fundraiser_user_id', 'fundraiser_id')->change();
            DB::statement("ALTER TABLE Online_Donation CHANGE `fundraiser_user_id` `fundraiser_id` BIGINT(20) NULL DEFAULT NULL");
        });
    }
}
