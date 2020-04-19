<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddAddedOnToBatchTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('Batch', function (Blueprint $table) {
            $table->datetime('added_on')->nullable();
            $table->datetime('updated_on')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('Batch', function (Blueprint $table) {
            $table->dropColumn('added_on');
            $table->dropColumn('updated_on');
        });
    }
}
