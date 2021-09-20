<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class NullableDates extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('App_CSVGo', function (Blueprint $table) {
            // $table->dateTime('last_run_on')->nullable()->change();
            DB::statement("ALTER TABLE `App_CSVGo` CHANGE `last_run_on` `last_run_on` DATETIME NULL DEFAULT NULL");
            // DB::statement("UPDATE `App_CSVGo` SET `last_run_on` = NULL WHERE `last_run_on` = '0000-00-00 00:00:00'");
        });

        Schema::table('Student', function (Blueprint $table) {
            // $table->dateTime('birthday')->nullable()->change();
            DB::statement("ALTER TABLE `Student` CHANGE `birthday` `birthday` DATE NULL DEFAULT NULL, 
                                                 CHANGE `added_on` `added_on` DATE NULL DEFAULT NULL");
            // DB::statement("UPDATE `Student` SET `birthday` = NULL WHERE `birthday` = '0000-00-00'");
        });

        Schema::table('Center', function (Blueprint $table) {
            DB::statement("ALTER TABLE `Center` CHANGE `class_starts_on` `class_starts_on` DATETIME NULL DEFAULT NULL");
        });

        Schema::table('Contact', function (Blueprint $table) {
            DB::statement("ALTER TABLE `Contact` CHANGE `birthday` `birthday` DATETIME NULL DEFAULT NULL");
        });

        Schema::table('Event', function (Blueprint $table) {
            DB::statement("ALTER TABLE `Event` CHANGE `starts_on` `starts_on` DATE NULL DEFAULT NULL, 
                                               CHANGE `created_on` `created_on` DATE NULL DEFAULT NULL,
                                               CHANGE `updated_on` `updated_on` DATE NULL DEFAULT NULL");
        });

        Schema::table('FAM_UserGroupPreference', function (Blueprint $table) {
            DB::statement("ALTER TABLE `FAM_UserGroupPreference` CHANGE `added_on` `added_on` DATETIME NULL DEFAULT NULL");
        });

        Schema::table('UserData', function (Blueprint $table) {
            DB::statement("ALTER TABLE `UserData` CHANGE `added_on` `added_on` DATETIME NULL DEFAULT NULL");
        });

        Schema::table('UserEvent', function (Blueprint $table) {
            DB::statement("ALTER TABLE `UserEvent` CHANGE `created_on` `created_on` DATETIME NULL DEFAULT NULL");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
