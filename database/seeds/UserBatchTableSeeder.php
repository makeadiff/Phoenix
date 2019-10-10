<?php

use Illuminate\Database\Seeder;

class UserBatchTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('UserBatch')->delete();
        
        \DB::table('UserBatch')->insert(array (
            0 => 
            array (
                'id' => 1,
                'user_id' => 2,
                'batch_id' => 1,
                'level_id' => 1,
                'requirement' => 0,
            ),
            1 => 
            array (
                'id' => 2,
                'user_id' => 3,
                'batch_id' => 1,
                'level_id' => 2,
                'requirement' => 0,
            ),
            2 => 
            array (
                'id' => 3,
                'user_id' => 5,
                'batch_id' => 2,
                'level_id' => 1,
                'requirement' => 0,
            ),
            3 => 
            array (
                'id' => 4,
                'user_id' => 6,
                'batch_id' => 2,
                'level_id' => 2,
                'requirement' => 0,
            ),
        ));
        
        
    }
}