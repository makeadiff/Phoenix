<?php

use Illuminate\Database\Seeder;

class FAMParameterCategoryTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('FAM_Parameter_Category')->delete();
        
        \DB::table('FAM_Parameter_Category')->insert(array (
            0 => 
            array (
                'id' => 1,
                'stage_id' => 1,
                'group_id' => 0,
                'name' => 'Day 1',
                'status' => '1',
            ),
            1 => 
            array (
                'id' => 2,
                'stage_id' => 1,
                'group_id' => 0,
                'name' => 'Day 2',
                'status' => '1',
            ),
            2 => 
            array (
                'id' => 3,
                'stage_id' => 1,
                'group_id' => 0,
                'name' => 'Day 3',
                'status' => '1',
            ),
            3 => 
            array (
                'id' => 4,
                'stage_id' => 1,
                'group_id' => 0,
                'name' => 'Day 4',
                'status' => '1',
            ),
            4 => 
            array (
                'id' => 5,
                'stage_id' => 1,
                'group_id' => 0,
                'name' => 'Day 5',
                'status' => '1',
            ),
            5 => 
            array (
                'id' => 6,
                'stage_id' => 3,
                'group_id' => 0,
                'name' => 'Video Task',
                'status' => '1',
            ),
            6 => 
            array (
                'id' => 7,
                'stage_id' => 3,
                'group_id' => 0,
                'name' => 'Written Task',
                'status' => '1',
            ),
            7 => 
            array (
                'id' => 8,
                'stage_id' => 3,
                'group_id' => 0,
                'name' => 'Comments',
                'status' => '1',
            ),
            8 => 
            array (
                'id' => 9,
                'stage_id' => 5,
                'group_id' => 2,
                'name' => 'Written Task',
                'status' => '1',
            ),
            9 => 
            array (
                'id' => 10,
                'stage_id' => 5,
                'group_id' => 2,
                'name' => 'Video Task',
                'status' => '0',
            ),
            10 => 
            array (
                'id' => 11,
                'stage_id' => 5,
                'group_id' => 11,
                'name' => 'Task 1',
                'status' => '1',
            ),
            11 => 
            array (
                'id' => 12,
                'stage_id' => 5,
                'group_id' => 11,
                'name' => 'Task 2',
                'status' => '1',
            ),
            12 => 
            array (
                'id' => 13,
                'stage_id' => 5,
                'group_id' => 272,
                'name' => 'Written task',
                'status' => '1',
            ),
            13 => 
            array (
                'id' => 14,
                'stage_id' => 5,
                'group_id' => 4,
                'name' => 'Written Task',
                'status' => '1',
            ),
            14 => 
            array (
                'id' => 15,
                'stage_id' => 4,
                'group_id' => 4,
                'name' => 'Role Play',
                'status' => '1',
            ),
            15 => 
            array (
                'id' => 16,
                'stage_id' => 5,
                'group_id' => 269,
                'name' => 'Written Task',
                'status' => '1',
            ),
            16 => 
            array (
                'id' => 17,
                'stage_id' => 5,
                'group_id' => 5,
                'name' => 'Question 1',
                'status' => '1',
            ),
            17 => 
            array (
                'id' => 18,
                'stage_id' => 5,
                'group_id' => 370,
                'name' => 'Written Task 1',
                'status' => '1',
            ),
            18 => 
            array (
                'id' => 19,
                'stage_id' => 5,
                'group_id' => 15,
                'name' => 'Written Task 1',
                'status' => '1',
            ),
            19 => 
            array (
                'id' => 20,
                'stage_id' => 5,
                'group_id' => 15,
                'name' => 'Written Task 2',
                'status' => '1',
            ),
            20 => 
            array (
                'id' => 21,
                'stage_id' => 5,
                'group_id' => 19,
                'name' => 'Team Building and Mobilisation',
                'status' => '1',
            ),
            21 => 
            array (
                'id' => 22,
                'stage_id' => 5,
                'group_id' => 19,
                'name' => 'Project Management',
                'status' => '1',
            ),
            22 => 
            array (
                'id' => 23,
                'stage_id' => 5,
                'group_id' => 375,
                'name' => 'Written task 1',
                'status' => '1',
            ),
            23 => 
            array (
                'id' => 24,
                'stage_id' => 5,
                'group_id' => 375,
                'name' => 'Written task 2',
                'status' => '1',
            ),
            24 => 
            array (
                'id' => 25,
                'stage_id' => 5,
                'group_id' => 2,
                'name' => 'Written Task',
                'status' => '0',
            ),
            25 => 
            array (
                'id' => 26,
                'stage_id' => 5,
                'group_id' => 378,
                'name' => 'Written task 1 & 2',
                'status' => '1',
            ),
            26 => 
            array (
                'id' => 27,
                'stage_id' => 5,
                'group_id' => 370,
                'name' => 'Written Task 2',
                'status' => '1',
            ),
            27 => 
            array (
                'id' => 28,
                'stage_id' => 5,
                'group_id' => 5,
                'name' => 'Question 2',
                'status' => '1',
            ),
            28 => 
            array (
                'id' => 29,
                'stage_id' => 5,
                'group_id' => 8,
                'name' => 'Written Task',
                'status' => '1',
            ),
            29 => 
            array (
                'id' => 30,
                'stage_id' => 5,
                'group_id' => 8,
                'name' => 'Role Play 2',
                'status' => '1',
            ),
        ));
        
        
    }
}