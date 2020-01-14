<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddValuesToAddedOnMarkTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('Mark', function (Blueprint $table) {
            //
        });

        $rows = DB::table('Mark')->get(['id','exam_id']);
        $year_marker = 2012; //Exam ID will be added to year to get to actual year data;
        foreach ($rows as $row) {
          $exam_id = $row->exam_id;
          $added_on = ($year_marker+$exam_id).'-04-01 00:00:00';
          DB::table('Mark')
            ->where('id',$row->id)
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
        Schema::table('Mark', function (Blueprint $table) {
            //
        });
    }
}
