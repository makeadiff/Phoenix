<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AllocationUpgrade extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('UserBatch', function (Blueprint $table) {
            $table->dropColumn('requirement');
            $table->bigInteger('subject_id')->after('level_id')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('UserBatch', function (Blueprint $table) {
            $table->integer("requirement");
            $table->dropColumn('subject_id');
        });
    }
}
