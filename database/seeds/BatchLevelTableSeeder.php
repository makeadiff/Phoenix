<?php

use Illuminate\Database\Seeder;

class BatchLevelTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('BatchLevel')->delete();
        
        \DB::table('BatchLevel')->insert(array (
            0 => 
            array (
                'id' => 1,
                'batch_id' => 1,
                'level_id' => 1,
                'year' => 2019,
            ),
            1 => 
            array (
                'id' => 2,
                'batch_id' => 1,
                'level_id' => 2,
                'year' => 2019,
            ),
            2 => 
            array (
                'id' => 3,
                'batch_id' => 2,
                'level_id' => 1,
                'year' => 2019,
            ),
            3 => 
            array (
                'id' => 4,
                'batch_id' => 2,
                'level_id' => 2,
                'year' => 2019,
            ),
        ));
        
        
    }
}