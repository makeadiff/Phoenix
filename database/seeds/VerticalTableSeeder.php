<?php

use Illuminate\Database\Seeder;

class VerticalTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('Vertical')->delete();
        
        \DB::table('Vertical')->insert(array (
            0 => 
            array (
                'id' => 1,
                'key' => 'pres',
                'name' => 'City Team Lead',
                'status' => '1',
            ),
            1 => 
            array (
                'id' => 2,
                'key' => 'ch',
                'name' => 'Shelter Operations',
                'status' => '1',
            ),
            2 => 
            array (
                'id' => 3,
                'key' => 'ed',
                'name' => 'Ed Support',
                'status' => '1',
            ),
            3 => 
            array (
                'id' => 4,
                'key' => 'ops',
                'name' => 'Shelter Support',
                'status' => '1',
            ),
            4 => 
            array (
                'id' => 5,
                'key' => 'propel',
                'name' => 'Transition Readiness',
                'status' => '1',
            ),
            5 => 
            array (
                'id' => 6,
                'key' => 'discover',
                'name' => 'Discover',
                'status' => '0',
            ),
            6 => 
            array (
                'id' => 7,
                'key' => 'pr',
                'name' => 'Campaigns',
                'status' => '1',
            ),
            7 => 
            array (
                'id' => 8,
                'key' => 'hr',
                'name' => 'Human Capital',
                'status' => '1',
            ),
            8 => 
            array (
                'id' => 9,
                'key' => 'finance',
                'name' => 'Finance',
                'status' => '1',
            ),
            9 => 
            array (
                'id' => 10,
                'key' => 'events',
                'name' => 'Events',
                'status' => '0',
            ),
            10 => 
            array (
                'id' => 11,
                'key' => 'fh',
                'name' => 'Fundraising Head',
                'status' => '0',
            ),
            11 => 
            array (
                'id' => 12,
                'key' => 'cfr',
                'name' => 'Community Fund Raising',
                'status' => '0',
            ),
            12 => 
            array (
                'id' => 13,
                'key' => 'cr',
                'name' => 'Corporate Relations',
                'status' => '0',
            ),
            13 => 
            array (
                'id' => 14,
                'key' => 'national',
                'name' => 'National',
                'status' => '0',
            ),
            14 => 
            array (
                'id' => 15,
                'key' => 'pd',
                'name' => 'Problem Definition',
                'status' => '0',
            ),
            15 => 
            array (
                'id' => 16,
                'key' => 'mob',
                'name' => 'Mobilization',
                'status' => '0',
            ),
            16 => 
            array (
                'id' => 17,
                'key' => 'fr',
                'name' => 'Fundraising',
                'status' => '1',
            ),
            17 => 
            array (
                'id' => 18,
                'key' => 'ac',
                'name' => 'Aftercare',
                'status' => '1',
            ),
            18 => 
            array (
                'id' => 19,
                'key' => 'fp',
                'name' => 'Foundational Programme',
                'status' => '1',
            ),
            19 => 
            array (
                'id' => 20,
                'key' => 'tech',
                'name' => 'Technology',
                'status' => '1',
            ),
        ));
        
        
    }
}