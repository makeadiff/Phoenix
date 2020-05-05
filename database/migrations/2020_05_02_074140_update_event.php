<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateEvent extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('Event', function (Blueprint $table) {
            $table->dropColumn('vertical_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('Event', function (Blueprint $table) {
            $table->bigInteger('vertical_id')->after('event_type_id')->default(0);
        });
    }
}
