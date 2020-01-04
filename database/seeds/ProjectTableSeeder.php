<?php

use Illuminate\Database\Seeder;

class ProjectTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        \DB::table('Project')->delete();
        
        \DB::table('Project')->insert(array(
            0 =>
            array(
                'id' => 1,
                'name' => 'Ed Support',
                'added_on' => '2011-06-20 12:39:19',
                'vertical_id' => 3,
                'status' => '1',
            ),
            1 =>
            array(
                'id' => 2,
                'name' => 'Foundational Programme',
                'added_on' => '2018-07-06 16:10:33',
                'vertical_id' => 19,
                'status' => '1',
            ),
            2 =>
            array(
                'id' => 4,
                'name' => 'Transition Readiness - ASV',
                'added_on' => '2018-07-06 16:10:33',
                'vertical_id' => 5,
                'status' => '1',
            ),
            3 =>
            array(
                'id' => 5,
                'name' => 'Transition Readiness - Wingman',
                'added_on' => '2018-07-06 16:10:33',
                'vertical_id' => 5,
                'status' => '1',
            ),
            4 =>
            array(
                'id' => 6,
                'name' => 'Aftercare',
                'added_on' => '2019-05-09 09:03:44',
                'vertical_id' => 18,
                'status' => '1',
            ),
        ));
    }
}
