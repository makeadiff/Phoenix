<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateClassCancelReasons extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('Class', function (Blueprint $table) {
            DB::statement("ALTER TABLE `Class` CHANGE `cancel_option` `cancel_option` ENUM('in-volunteer-unavailable','in-volunteer-engaged','in-volunteer-unassigned','in-other','ext-children-out','ext-children-doing-chores','ext-children-have-events','ext-children-unwell','ext-other','misc','ext-parent-visiting-shelter','ext-holiday','ext-out-school','ext-disturbances-city', 'ext-shelter-internet-issue') NULL DEFAULT NULL;");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('Class', function (Blueprint $table) {
            DB::statement("ALTER TABLE `Class` CHANGE `cancel_option` `cancel_option` ENUM('in-volunteer-unavailable','in-volunteer-engaged','in-volunteer-unassigned','in-other','ext-children-out','ext-children-doing-chores','ext-children-have-events','ext-children-unwell','ext-other','misc','ext-parent-visiting-shelter','ext-holiday','ext-out-school','ext-disturbances-city') NULL DEFAULT NULL;");
        });
    }
}
