<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class StudentAddStatus extends Migration
{
    public function __construct()
    {
        DB::getDoctrineSchemaManager()->getDatabasePlatform()->registerDoctrineTypeMapping('enum', 'string');
    }

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('Student', function (Blueprint $table) {
            $table->enum('student_type', ['active', 'active_away', 'alumni', 'alumni_no_contact', 'alumni_dead', 'other'])
                        ->nullable()->default(null)->after('reason_for_leaving');
        });
        DB::statement("UPDATE Student SET student_type = 'active' WHERE center_id IN (SELECT id FROM Center WHERE status = '1' AND city_id != 14) AND status = '1'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('Student', function (Blueprint $table) {
            $table->dropColumn('student_type');
        });
    }
}
