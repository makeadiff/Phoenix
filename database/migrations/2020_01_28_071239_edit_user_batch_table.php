<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class EditUserBatchTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('UserBatch', function (Blueprint $table) {
            $table->datetime('added_on')->nullable();
            $table->enum('role', ['teacher','mentor','wingman'])->default('teacher');
        });
        $rows = DB::table('UserBatch')->join('Batch', 'Batch.id', '=', 'UserBatch.batch_id')->get(['UserBatch.id as id','year']);
        foreach ($rows as $row) {
            $added_on = ($row->year).'-06-01 00:00:00';
            DB::table('UserBatch')
            ->where('id', $row->id)
            ->update(['added_on'=>$added_on]);
        }
    }
    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('UserBatch', function (Blueprint $table) {
            $table->dropColumn('added_on');
            $table->dropColumn('role');
        });
    }
}
