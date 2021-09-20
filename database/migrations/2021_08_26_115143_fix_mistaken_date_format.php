<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class FixMistakenDateFormat extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('Student', function (Blueprint $table) {
            // $table->dateTime('birthday')->nullable()->change();
            DB::statement("ALTER TABLE `Student` CHANGE `added_on` `added_on` DATETIME NULL DEFAULT NULL");
        });

        Schema::table('Event', function (Blueprint $table) {
            DB::statement("ALTER TABLE `Event` CHANGE `starts_on` `starts_on` DATETIME NULL DEFAULT NULL, 
                                               CHANGE `created_on` `created_on` DATETIME NULL DEFAULT NULL,
                                               CHANGE `updated_on` `updated_on` DATETIME NULL DEFAULT NULL");
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
