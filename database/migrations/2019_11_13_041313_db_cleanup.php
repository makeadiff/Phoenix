<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/// Make all changes that was decided during the holy month period. Just doing what ever the DB.txt says.
class DbCleanup extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('App_CSVGo', function (Blueprint $table) {
            $table->dropColumn('db');
        });
        Schema::drop('App_Event_Mail_Tracker');
        Schema::table('Batch', function (Blueprint $table) {
            $table->dropColumn('subject_id');
        });

        Schema::table('City', function (Blueprint $table) {
            $table->dropColumn(['classes_happening', 'region_id']);
        });

        Schema::table('Class', function (Blueprint $table) {
            $table->dropColumn(['feedback']);
        });
        Schema::drop('ContactApplication');

        Schema::table('Group', function (Blueprint $table) {
            $table->dropColumn(['region_id']);
        });
        Schema::drop('GroupHierarchy');

        Schema::table('Level', function (Blueprint $table) {
            $table->dropColumn(['book_id']);
        });
        Schema::drop('Region');
        Schema::drop('SC_student_shelter_mapping');
        Schema::drop('SC_teacher_shelter_mapping');
        Schema::drop('State');

        Schema::table('Subject', function (Blueprint $table) {
            $table->dropColumn(['unit_count', 'city_id']);
        });

        Schema::drop('Temp_Alumni');

        Schema::table('User', function (Blueprint $table) {
            $table->dropColumn(['induction_status', 'teacher_training_status', 'center_id','consecutive_credit','admin_credit']);
        });

        Schema::table('Vertical', function (Blueprint $table) {
            $table->dropColumn(['key']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Not doing a reversal. This change will distroy data. There will be a specific backup take before running this on production.
    }
}
