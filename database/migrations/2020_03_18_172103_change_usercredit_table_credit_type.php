<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeUsercreditTableCreditType extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('UserCredit', function (Blueprint $table) {
            $table->float('credit')->default(3)->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('UserCredit', function (Blueprint $table) {
            $table->integer('credit')->nullable()->change();
        });
    }
}
