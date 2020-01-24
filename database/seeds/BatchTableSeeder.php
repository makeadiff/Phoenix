<?php

use Illuminate\Database\Seeder;

class BatchTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        \DB::table('Batch')->delete();
        
        \DB::table('Batch')->insert(array(
            0 =>
            array(
                'id' => 1,
                'day' => '0',
                'class_time' => '16:00:00',
                'batch_head_id' => 3,
                'center_id' => 220,
                'subject_id' => 0,
                'project_id' => 1,
                'year' => 2019,
                'status' => '1',
            ),
            1 =>
            array(
                'id' => 2,
                'day' => '6',
                'class_time' => '14:00:00',
                'batch_head_id' => 4,
                'center_id' => 220,
                'subject_id' => 0,
                'project_id' => 1,
                'year' => 2019,
                'status' => '1',
            ),
        ));
    }
}
