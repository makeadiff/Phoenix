<?php

use Illuminate\Database\Seeder;

class LevelTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        \DB::table('Level')->delete();
        
        \DB::table('Level')->insert(array(
            0 =>
            array(
                'id' => 1,
                'name' => 'A',
                'grade' => 5,
                'center_id' => 220,
                'medium' => 'english',
                'preferred_gender' => 'any',
                'medium_id' => 0,
                'project_id' => 1,
                'book_id' => 0,
                'year' => 2019,
                'status' => '1',
            ),
            1 =>
            array(
                'id' => 2,
                'name' => 'A',
                'grade' => 6,
                'center_id' => 220,
                'medium' => 'english',
                'preferred_gender' => 'any',
                'medium_id' => 0,
                'project_id' => 1,
                'book_id' => 0,
                'year' => 2019,
                'status' => '1',
            ),
        ));
    }
}
