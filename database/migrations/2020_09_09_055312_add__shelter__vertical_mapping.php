<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddShelterVerticalMapping extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('User', function (Blueprint $table) {
            $table->unsignedBigInteger('center_id')->after('city_id')->default('0');
        });

        Schema::table('UserGroup', function (Blueprint $table) {
            $table->enum('main', ['0', '1'])->default('0');
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
            $table->dropColumn('center_id');
        });

        Schema::table('User', function (Blueprint $table) {
            $table->dropColumn('main');
        });
    }
}
