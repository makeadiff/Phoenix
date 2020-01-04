<?php

use Illuminate\Database\Seeder;

class StudentTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        \DB::table('Student')->delete();
        
        \DB::table('Student')->insert(array(
            0 =>
            array(
                'id' => 1,
                'name' => 'Anakin',
                'birthday' => '0000-00-00',
                'sex' => 'm',
                'center_id' => 220,
                'description' => '',
                'photo' => '',
                'thumbnail' => '',
                'added_on' => '2019-10-08 11:10:27',
                'reason_for_leaving' => null,
                'status' => '1',
            ),
            1 =>
            array(
                'id' => 2,
                'name' => 'Chewbacca',
                'birthday' => '0000-00-00',
                'sex' => 'm',
                'center_id' => 220,
                'description' => '',
                'photo' => '',
                'thumbnail' => '',
                'added_on' => '2019-10-08 11:10:37',
                'reason_for_leaving' => null,
                'status' => '1',
            ),
            2 =>
            array(
                'id' => 3,
                'name' => 'Han',
                'birthday' => '0000-00-00',
                'sex' => 'm',
                'center_id' => 220,
                'description' => '',
                'photo' => '',
                'thumbnail' => '',
                'added_on' => '2019-10-08 11:10:47',
                'reason_for_leaving' => null,
                'status' => '1',
            ),
            3 =>
            array(
                'id' => 4,
                'name' => 'Leia',
                'birthday' => '0000-00-00',
                'sex' => 'm',
                'center_id' => 220,
                'description' => '',
                'photo' => '',
                'thumbnail' => '',
                'added_on' => '2019-10-08 11:10:57',
                'reason_for_leaving' => null,
                'status' => '1',
            ),
            4 =>
            array(
                'id' => 5,
                'name' => 'Luke',
                'birthday' => '0000-00-00',
                'sex' => 'm',
                'center_id' => 220,
                'description' => '',
                'photo' => '',
                'thumbnail' => '',
                'added_on' => '2019-10-08 11:11:05',
                'reason_for_leaving' => null,
                'status' => '1',
            ),
            5 =>
            array(
                'id' => 6,
                'name' => 'Yoda',
                'birthday' => '0000-00-00',
                'sex' => 'm',
                'center_id' => 220,
                'description' => '',
                'photo' => '',
                'thumbnail' => '',
                'added_on' => '2019-10-08 11:11:16',
                'reason_for_leaving' => null,
                'status' => '1',
            ),
            6 =>
            array(
                'id' => 7,
                'name' => 'Obiwan Kenobi',
                'birthday' => '0000-00-00',
                'sex' => 'm',
                'center_id' => 220,
                'description' => '',
                'photo' => '',
                'thumbnail' => '',
                'added_on' => '2019-10-08 11:11:33',
                'reason_for_leaving' => null,
                'status' => '1',
            ),
        ));
    }
}
