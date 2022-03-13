<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddMoreClassCancelReasons extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('Class', function (Blueprint $table) {
            DB::statement("ALTER TABLE `Class` CHANGE `cancel_option` `cancel_option` ENUM('in-volunteer-unavailable','in-volunteer-engaged','in-volunteer-unassigned','in-other','ext-children-out','ext-children-doing-chores','ext-children-have-events','ext-children-unwell','ext-other','misc','ext-parent-visiting-shelter','ext-holiday','ext-out-school','ext-disturbances-city', 'ext-shelter-internet-issue','in-infra-internet-issue', 'in-infra-power-issue', 'in-infra-device-issue','ext-shelter-annual-event','ext-shelter-religious-event','ext-shelter-medical-event','ext-shelter-workshop','ext-shelter-cwc-visit','ext-children-exam-prep') NULL DEFAULT NULL;");
        });
        DB::statement("UPDATE Class SET cancel_option = 'in-infra-internet-issue' WHERE cancel_option = 'ext-shelter-internet-issue'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('Class', function (Blueprint $table) {
            DB::statement("ALTER TABLE `Class` CHANGE `cancel_option` `cancel_option` ENUM('in-volunteer-unavailable','in-volunteer-engaged','in-volunteer-unassigned','in-other','ext-children-out','ext-children-doing-chores','ext-children-have-events','ext-children-unwell','ext-other','misc','ext-parent-visiting-shelter','ext-holiday','ext-out-school','ext-disturbances-city', 'ext-shelter-internet-issue') NULL DEFAULT NULL;");
        });
    }
}
