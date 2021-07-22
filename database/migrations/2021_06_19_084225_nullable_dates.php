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
            DB::statement("ALTER TABLE `App_CSVGo` CHANGE `last_run_on` `last_run_on` DATETIME NULL;");
            // DB::statement("UPDATE `App_CSVGo` SET `last_run_on` = NULL WHERE `last_run_on` = '0000-00-00 00:00:00'");
        });

        Schema::table('Student', function (Blueprint $table) {
            // $table->dateTime('birthday')->nullable()->change();
            DB::statement("ALTER TABLE `Student` CHANGE `birthday` `birthday` DATE NULL;");
            // DB::statement("UPDATE `Student` SET `birthday` = NULL WHERE `birthday` = '0000-00-00'");
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
