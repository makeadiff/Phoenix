<?php

use Illuminate\Database\Seeder;

class StudentLevelTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('StudentLevel')->delete();
        
        \DB::table('StudentLevel')->insert(array (
            0 => 
            array (
                'id' => 1,
                'student_id' => 1,
                'level_id' => 1,
            ),
            1 => 
            array (
                'id' => 2,
                'student_id' => 2,
                'level_id' => 1,
            ),
            2 => 
            array (
                'id' => 3,
                'student_id' => 3,
                'level_id' => 1,
            ),
            3 => 
            array (
                'id' => 4,
                'student_id' => 4,
                'level_id' => 2,
            ),
            4 => 
            array (
                'id' => 5,
                'student_id' => 5,
                'level_id' => 2,
            ),
            5 => 
            array (
                'id' => 6,
                'student_id' => 7,
                'level_id' => 2,
            ),
            6 => 
            array (
                'id' => 7,
                'student_id' => 6,
                'level_id' => 2,
            ),
        ));
        
        
    }
}