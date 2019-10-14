<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddParentGroupIdToGroupTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('Group', function (Blueprint $table) {
            $table->bigInteger('parent_group_id')->after('type')->default(0);
            $table->index('parent_group_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('Group', function (Blueprint $table) {
            $table->dropColumn('parent_group_id');
            $table->dropIndex('parent_group_id');
        });
    }
}
