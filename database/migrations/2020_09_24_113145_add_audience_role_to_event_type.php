<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddAudienceRoleToEventType extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('Event_Type', function (Blueprint $table) {
            $table->enum('audience', ['vertical','city','center'])->nullable()->default(null)->after('vertical_id');
            $table->enum('role', ['volunteer','fellow','strat', 'national'])->nullable()->default(null)->after('vertical_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('Event_Type', function (Blueprint $table) {
            $table->dropColumn('audience');
            $table->dropColumn('role');
        });
    }
}
