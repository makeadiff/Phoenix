<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UserFieldsUpdate extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('User', function (Blueprint $table) {
            DB::statement("ALTER TABLE `User` CHANGE `sex` `sex` SET('m', 'f', 'o', 'non-binary', 'not-given') NOT NULL DEFAULT 'f';");
            $table->string('edu_course', 200)->nullable()->after('edu_institution')->default(null);
            $table->integer('edu_year')->nullable()->after('edu_course')->default(null);
            $table->string('applied_role_secondary', 200)->nullable()->after('applied_role')->default(null);
            $table->mediumText('volunteering_experience')->nullable()->after('why_mad')->default(null);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('User', function (Blueprint $table) {
            DB::statement("ALTER TABLE `User` CHANGE `sex` `sex` SET('m', 'f', 'o') NOT NULL DEFAULT 'f';");
            $table->dropColumn(['edu_course', 'edu_year', 'applied_role_secondary', 'volunteering_experience']);
        });
    }
}
