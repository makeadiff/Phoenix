<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddPositiveOrNegativeToFrUsercreditTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('FR_UserCredit', function (Blueprint $table) {
            if (!Schema::hasColumn('FR_UserCredit', 'positive_or_negative')) {
                $table->enum('positive_or_negative', ['1', '-1'])->after('current_credit')->nullable();
            }
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('FR_UserCredit', function (Blueprint $table) {
            if (Schema::hasColumn('FR_UserCredit', 'positive_or_negative')) {
                $table->dropColumn('positive_or_negative');
            }
        });
    }
}
