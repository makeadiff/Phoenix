<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddPanToDonor extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('Donut_Donor', function (Blueprint $table) {
            $table->string('pan', 20)->nullable()->after('address');
            $table->string('nationality', 50)->nullable()->after('address');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('Donut_Donor', function (Blueprint $table) {
            $table->dropColumn('nationality');
            $table->dropColumn('pan');
        });
    }
}
