<?php

use Illuminate\Database\Seeder;

class SubjectTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        \DB::table('Subject')->delete();
        
        \DB::table('Subject')->insert(array(
            0 =>
            array(
                'id' => 1,
                'name' => 'CES - English',
                'unit_count' => '20',
                'city_id' => 0,
                'status' => '0',
            ),
            1 =>
            array(
                'id' => 2,
                'name' => 'State Board - English',
                'unit_count' => '20',
                'city_id' => 0,
                'status' => '1',
            ),
            2 =>
            array(
                'id' => 3,
                'name' => 'State Board - Math',
                'unit_count' => '20',
                'city_id' => 0,
                'status' => '1',
            ),
            3 =>
            array(
                'id' => 4,
                'name' => 'State Board - Science',
                'unit_count' => '20',
                'city_id' => 0,
                'status' => '1',
            ),
            4 =>
            array(
                'id' => 5,
                'name' => 'NCERT - English',
                'unit_count' => '20',
                'city_id' => 0,
                'status' => '1',
            ),
            5 =>
            array(
                'id' => 6,
                'name' => 'NCERT - Maths',
                'unit_count' => '20',
                'city_id' => 0,
                'status' => '1',
            ),
            6 =>
            array(
                'id' => 7,
                'name' => 'NCERT - Science',
                'unit_count' => '20',
                'city_id' => 0,
                'status' => '1',
            ),
            7 =>
            array(
                'id' => 8,
                'name' => 'English',
                'unit_count' => '',
                'city_id' => 0,
                'status' => '1',
            ),
            8 =>
            array(
                'id' => 9,
                'name' => 'Math',
                'unit_count' => '',
                'city_id' => 0,
                'status' => '1',
            ),
            9 =>
            array(
                'id' => 10,
                'name' => 'Science',
                'unit_count' => '',
                'city_id' => 0,
                'status' => '1',
            ),
            10 =>
            array(
                'id' => 11,
                'name' => 'Chemistry',
                'unit_count' => '',
                'city_id' => 0,
                'status' => '1',
            ),
            11 =>
            array(
                'id' => 13,
                'name' => 'Computer Science',
                'unit_count' => '',
                'city_id' => 0,
                'status' => '1',
            ),
            12 =>
            array(
                'id' => 14,
                'name' => 'Physics',
                'unit_count' => '',
                'city_id' => 0,
                'status' => '1',
            ),
            13 =>
            array(
                'id' => 16,
                'name' => 'Economics',
                'unit_count' => '',
                'city_id' => 0,
                'status' => '1',
            ),
            14 =>
            array(
                'id' => 17,
                'name' => 'Accountancy',
                'unit_count' => '',
                'city_id' => 0,
                'status' => '1',
            ),
            15 =>
            array(
                'id' => 18,
                'name' => 'Business Studies',
                'unit_count' => '',
                'city_id' => 0,
                'status' => '1',
            ),
            16 =>
            array(
                'id' => 19,
                'name' => 'Computer Applications',
                'unit_count' => '',
                'city_id' => 0,
                'status' => '1',
            ),
            17 =>
            array(
                'id' => 20,
                'name' => 'Geography',
                'unit_count' => '',
                'city_id' => 0,
                'status' => '1',
            ),
            18 =>
            array(
                'id' => 21,
                'name' => 'Biology',
                'unit_count' => '',
                'city_id' => 0,
                'status' => '1',
            ),
            19 =>
            array(
                'id' => 22,
                'name' => 'History',
                'unit_count' => '',
                'city_id' => 0,
                'status' => '1',
            ),
            20 =>
            array(
                'id' => 23,
                'name' => 'Kannada',
                'unit_count' => '',
                'city_id' => 0,
                'status' => '1',
            ),
            21 =>
            array(
                'id' => 24,
                'name' => 'Botany',
                'unit_count' => '',
                'city_id' => 0,
                'status' => '1',
            ),
            22 =>
            array(
                'id' => 25,
                'name' => 'Zoology',
                'unit_count' => '',
                'city_id' => 0,
                'status' => '1',
            ),
            23 =>
            array(
                'id' => 26,
                'name' => 'Political Science',
                'unit_count' => '',
                'city_id' => 0,
                'status' => '1',
            ),
            24 =>
            array(
                'id' => 27,
                'name' => 'Sociology',
                'unit_count' => '',
                'city_id' => 0,
                'status' => '1',
            ),
            25 =>
            array(
                'id' => 28,
                'name' => 'Psychology',
                'unit_count' => '',
                'city_id' => 0,
                'status' => '1',
            ),
            26 =>
            array(
                'id' => 29,
                'name' => 'Statistics',
                'unit_count' => '',
                'city_id' => 0,
                'status' => '1',
            ),
            27 =>
            array(
                'id' => 30,
                'name' => 'Hindi',
                'unit_count' => '',
                'city_id' => 0,
                'status' => '1',
            ),
        ));
    }
}
