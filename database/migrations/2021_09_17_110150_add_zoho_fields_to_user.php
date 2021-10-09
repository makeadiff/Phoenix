<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddZohoFieldsToUser extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('User', function (Blueprint $table) {
            $table->string('zoho_message')->nullable()->default(null)->after('zoho_user_id');
            $table->enum('zoho_sync_status', ['insert-pending','update-pending','done','error'])->nullable()->default(null)->after('zoho_message');
        });

        Schema::table('User', function (Blueprint $table) {
            DB::statement("UPDATE User SET zoho_sync_status='insert-pending' WHERE user_type='applicant' AND joined_on > '2021-01-01 00:00:00' AND (zoho_user_id IS NULL OR zoho_user_id='')");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('user', function (Blueprint $table) {
            $table->dropColumn('zoho_message');
            $table->dropColumn('zoho_sync_status');
        });
    }
}
