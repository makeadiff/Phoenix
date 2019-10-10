<?php

use Illuminate\Database\Seeder;

class CityTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('City')->delete();
        
        \DB::table('City')->insert(array (
            0 => 
            array (
                'id' => 1,
                'name' => 'Bangalore',
                'president_id' => 5,
                'added_on' => '2011-07-20 03:05:39',
                'classes_happening' => '1',
                'region_id' => 1,
                'latitude' => '12.971599',
                'longitude' => '77.594563',
                'type' => 'actual',
            ),
            1 => 
            array (
                'id' => 2,
                'name' => 'Mangalore',
                'president_id' => 12,
                'added_on' => '2011-07-20 03:06:11',
                'classes_happening' => '1',
                'region_id' => 1,
                'latitude' => '12.914142',
                'longitude' => '74.855957',
                'type' => 'actual',
            ),
            2 => 
            array (
                'id' => 3,
                'name' => 'Trivandrum',
                'president_id' => 18,
                'added_on' => '2011-07-20 03:31:13',
                'classes_happening' => '1',
                'region_id' => 1,
                'latitude' => '8.524139',
                'longitude' => '76.936638',
                'type' => 'actual',
            ),
            3 => 
            array (
                'id' => 4,
                'name' => 'Mumbai',
                'president_id' => 24,
                'added_on' => '2011-07-20 03:59:57',
                'classes_happening' => '1',
                'region_id' => 4,
                'latitude' => '19.075984',
                'longitude' => '72.877656',
                'type' => 'actual',
            ),
            4 => 
            array (
                'id' => 5,
                'name' => 'Pune',
                'president_id' => 25,
                'added_on' => '2011-07-20 04:00:13',
                'classes_happening' => '1',
                'region_id' => 4,
                'latitude' => '18.520430',
                'longitude' => '73.856744',
                'type' => 'actual',
            ),
            5 => 
            array (
                'id' => 6,
                'name' => 'Chennai',
                'president_id' => 39,
                'added_on' => '2011-07-20 05:07:56',
                'classes_happening' => '1',
                'region_id' => 1,
                'latitude' => '13.082680',
                'longitude' => '80.270718',
                'type' => 'actual',
            ),
            6 => 
            array (
                'id' => 8,
                'name' => 'Vellore',
                'president_id' => 50,
                'added_on' => '2011-07-20 10:24:49',
                'classes_happening' => '1',
                'region_id' => 1,
                'latitude' => '12.916517',
                'longitude' => '79.132499',
                'type' => 'actual',
            ),
            7 => 
            array (
                'id' => 10,
                'name' => 'Cochin',
                'president_id' => 49,
                'added_on' => '2011-07-20 10:25:22',
                'classes_happening' => '1',
                'region_id' => 1,
                'latitude' => '9.931233',
                'longitude' => '76.267304',
                'type' => 'actual',
            ),
            8 => 
            array (
                'id' => 11,
                'name' => 'Hyderabad',
                'president_id' => 51,
                'added_on' => '2011-07-20 10:25:48',
                'classes_happening' => '1',
                'region_id' => 3,
                'latitude' => '17.385044',
                'longitude' => '78.486671',
                'type' => 'actual',
            ),
            9 => 
            array (
                'id' => 12,
                'name' => 'Delhi',
                'president_id' => 73,
                'added_on' => '2011-07-20 11:43:41',
                'classes_happening' => '1',
                'region_id' => 2,
                'latitude' => '28.704059',
                'longitude' => '77.102490',
                'type' => 'actual',
            ),
            10 => 
            array (
                'id' => 13,
                'name' => 'Chandigarh',
                'president_id' => 75,
                'added_on' => '2011-07-20 12:04:23',
                'classes_happening' => '1',
                'region_id' => 2,
                'latitude' => '30.733315',
                'longitude' => '76.779418',
                'type' => 'actual',
            ),
            11 => 
            array (
                'id' => 14,
                'name' => 'Kolkata',
                'president_id' => 76,
                'added_on' => '2011-07-20 12:04:35',
                'classes_happening' => '1',
                'region_id' => 4,
                'latitude' => '',
                'longitude' => '',
                'type' => 'actual',
            ),
            12 => 
            array (
                'id' => 15,
                'name' => 'Nagpur',
                'president_id' => 74,
                'added_on' => '2011-07-20 12:04:55',
                'classes_happening' => '1',
                'region_id' => 4,
                'latitude' => '21.145800',
                'longitude' => '79.088155',
                'type' => 'actual',
            ),
            13 => 
            array (
                'id' => 16,
                'name' => 'Coimbatore',
                'president_id' => 1,
                'added_on' => '2011-08-02 09:15:24',
                'classes_happening' => '1',
                'region_id' => 3,
                'latitude' => '11.016844',
                'longitude' => '76.955832',
                'type' => 'actual',
            ),
            14 => 
            array (
                'id' => 17,
                'name' => 'Vizag',
                'president_id' => 846,
                'added_on' => '2011-08-02 09:16:01',
                'classes_happening' => '1',
                'region_id' => 3,
                'latitude' => '17.686816',
                'longitude' => '83.218482',
                'type' => 'actual',
            ),
            15 => 
            array (
                'id' => 18,
                'name' => 'Vijayawada',
                'president_id' => 845,
                'added_on' => '2011-08-02 09:16:20',
                'classes_happening' => '1',
                'region_id' => 3,
                'latitude' => '16.506174',
                'longitude' => '80.648015',
                'type' => 'actual',
            ),
            16 => 
            array (
                'id' => 19,
                'name' => 'Gwalior',
                'president_id' => 1231,
                'added_on' => '2011-08-08 22:01:34',
                'classes_happening' => '1',
                'region_id' => 2,
                'latitude' => '26.218287',
                'longitude' => '78.182831',
                'type' => 'actual',
            ),
            17 => 
            array (
                'id' => 20,
                'name' => 'Lucknow',
                'president_id' => 1639,
                'added_on' => '2011-08-19 04:34:29',
                'classes_happening' => '1',
                'region_id' => 2,
                'latitude' => '26.846694',
                'longitude' => '80.946166',
                'type' => 'actual',
            ),
            18 => 
            array (
                'id' => 21,
                'name' => 'Bhopal',
                'president_id' => 2984,
                'added_on' => '2011-09-04 21:57:17',
                'classes_happening' => '1',
                'region_id' => 4,
                'latitude' => '23.259933',
                'longitude' => '77.412615',
                'type' => 'actual',
            ),
            19 => 
            array (
                'id' => 22,
                'name' => 'Mysore',
                'president_id' => 10256,
                'added_on' => '2012-05-14 08:05:03',
                'classes_happening' => '1',
                'region_id' => 3,
                'latitude' => '12.295810',
                'longitude' => '76.639381',
                'type' => 'actual',
            ),
            20 => 
            array (
                'id' => 23,
                'name' => 'Guntur',
                'president_id' => 10257,
                'added_on' => '2012-05-14 08:08:36',
                'classes_happening' => '1',
                'region_id' => 3,
                'latitude' => '16.306652',
                'longitude' => '80.436540',
                'type' => 'actual',
            ),
            21 => 
            array (
                'id' => 24,
                'name' => 'Ahmedabad',
                'president_id' => 1,
                'added_on' => '2012-06-21 05:44:33',
                'classes_happening' => '1',
                'region_id' => 4,
                'latitude' => '23.022505',
                'longitude' => '72.571362',
                'type' => 'actual',
            ),
            22 => 
            array (
                'id' => 25,
                'name' => 'Dehradun',
                'president_id' => 670,
                'added_on' => '2012-06-21 05:55:54',
                'classes_happening' => '1',
                'region_id' => 2,
                'latitude' => '30.316495',
                'longitude' => '78.032192',
                'type' => 'actual',
            ),
            23 => 
            array (
                'id' => 26,
                'name' => 'Leadership',
                'president_id' => 0,
                'added_on' => '2012-10-04 08:08:42',
                'classes_happening' => '1',
                'region_id' => 5,
                'latitude' => '12.971599',
                'longitude' => '77.594563',
                'type' => 'actual',
            ),
            24 => 
            array (
                'id' => 28,
                'name' => 'Test',
                'president_id' => 0,
                'added_on' => '2014-08-22 15:06:15',
                'classes_happening' => '1',
                'region_id' => 1,
                'latitude' => '',
                'longitude' => '',
                'type' => 'virtual',
            ),
            25 => 
            array (
                'id' => 0,
                'name' => 'National',
                'president_id' => 0,
                'added_on' => '2019-08-14 00:00:00',
                'classes_happening' => '1',
                'region_id' => 1,
                'latitude' => '',
                'longitude' => '',
                'type' => 'actual',
            ),
        ));
        
        
    }
}