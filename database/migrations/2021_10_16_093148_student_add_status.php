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
            // DB::statement("ALTER TABLE `Conversation` CHANGE `type` `type` SET('appreciation','check-in','developmental','exit') NOT NULL DEFAULT 'check-in';");
        });
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
