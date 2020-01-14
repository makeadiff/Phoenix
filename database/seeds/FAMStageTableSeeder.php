<?php

use Illuminate\Database\Seeder;

class FAMStageTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        \DB::table('FAM_Stage')->delete();
        
        \DB::table('FAM_Stage')->insert(array(
            0 =>
            array(
                'id' => 1,
                'name' => 'Kindness Challenge',
                'status' => '0',
            ),
            1 =>
            array(
                'id' => 2,
                'name' => 'Applicant Feedback',
                'status' => '1',
            ),
            2 =>
            array(
                'id' => 3,
                'name' => 'Common Task',
                'status' => '1',
            ),
            3 =>
            array(
                'id' => 4,
                'name' => 'Personal Interview',
                'status' => '1',
            ),
            4 =>
            array(
                'id' => 5,
                'name' => 'Vertical Task',
                'status' => '1',
            ),
            5 =>
            array(
                'id' => 6,
                'name' => 'Volunteer Participation',
                'status' => '1',
            ),
        ));
    }
}
