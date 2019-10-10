<?php

use Illuminate\Database\Seeder;

class EventTypeTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('Event_Type')->delete();
        
        \DB::table('Event_Type')->insert(array (
            0 => 
            array (
                'id' => 1,
                'name' => 'Volunteer Leadership Circle',
                'status' => '1',
            ),
            1 => 
            array (
                'id' => 2,
                'name' => 'Shelter Circle',
                'status' => '0',
            ),
            2 => 
            array (
                'id' => 3,
                'name' => 'CFR War Room',
                'status' => '1',
            ),
            3 => 
            array (
                'id' => 4,
                'name' => 'Fellow Team Meet',
                'status' => '1',
            ),
            4 => 
            array (
                'id' => 5,
                'name' => 'Shelter Sensitization 1.0',
                'status' => '1',
            ),
            5 => 
            array (
                'id' => 6,
                'name' => 'Volunteer Event',
                'status' => '1',
            ),
            6 => 
            array (
                'id' => 7,
                'name' => 'Recruitment Workshop',
                'status' => '1',
            ),
            7 => 
            array (
                'id' => 8,
                'name' => 'City Circle 1',
                'status' => '1',
            ),
            8 => 
            array (
                'id' => 9,
                'name' => 'City Circle 2',
                'status' => '1',
            ),
            9 => 
            array (
                'id' => 10,
                'name' => 'City Circle 3',
                'status' => '1',
            ),
            10 => 
            array (
                'id' => 11,
                'name' => 'Ed Circle',
                'status' => '1',
            ),
            11 => 
            array (
                'id' => 12,
                'name' => 'Transition Readiness Circle',
                'status' => '1',
            ),
            12 => 
            array (
                'id' => 13,
                'name' => 'Mentor Circle',
                'status' => '1',
            ),
            13 => 
            array (
                'id' => 14,
                'name' => 'Fundraising Circle',
                'status' => '1',
            ),
            14 => 
            array (
                'id' => 16,
                'name' => 'Fundraising Event',
                'status' => '1',
            ),
            15 => 
            array (
                'id' => 17,
                'name' => 'Edsupport ASV Training 1',
                'status' => '1',
            ),
            16 => 
            array (
                'id' => 18,
                'name' => 'Monthly Fellow Meeting',
                'status' => '1',
            ),
            17 => 
            array (
                'id' => 19,
                'name' => 'Weekly Fellow Onground ',
                'status' => '1',
            ),
            18 => 
            array (
                'id' => 20,
                'name' => 'Weekly Fellow Zoom Call',
                'status' => '1',
            ),
            19 => 
            array (
                'id' => 21,
                'name' => 'Transition Readiness Wingmen Training 1',
                'status' => '1',
            ),
            20 => 
            array (
                'id' => 22,
                'name' => 'Ed Support Training',
                'status' => '0',
            ),
            21 => 
            array (
                'id' => 23,
                'name' => 'Ecosystem Meeting',
                'status' => '0',
            ),
            22 => 
            array (
                'id' => 24,
                'name' => 'Stakeholder Engagement ',
                'status' => '1',
            ),
            23 => 
            array (
                'id' => 25,
                'name' => 'Weekly Strat Call',
                'status' => '1',
            ),
            24 => 
            array (
                'id' => 26,
                'name' => 'Placements Training 1',
                'status' => '1',
            ),
            25 => 
            array (
                'id' => 27,
                'name' => 'Foundational Programme Training 1',
                'status' => '1',
            ),
            26 => 
            array (
                'id' => 28,
                'name' => 'Training',
                'status' => '0',
            ),
            27 => 
            array (
                'id' => 29,
                'name' => 'Foundational Programme Training 2',
                'status' => '1',
            ),
            28 => 
            array (
                'id' => 30,
                'name' => 'Aftercare Wingmen Training 1',
                'status' => '1',
            ),
            29 => 
            array (
                'id' => 31,
                'name' => 'Fundraising Training 1',
                'status' => '1',
            ),
            30 => 
            array (
                'id' => 32,
                'name' => 'Aftercare Circle',
                'status' => '1',
            ),
            31 => 
            array (
                'id' => 33,
                'name' => 'Aftercare - Self Support Group Meet',
                'status' => '1',
            ),
            32 => 
            array (
                'id' => 34,
                'name' => 'Aftercare - Self Support Group Contribution',
                'status' => '1',
            ),
            33 => 
            array (
                'id' => 35,
                'name' => 'Shelter Sensitization 2.0',
                'status' => '1',
            ),
            34 => 
            array (
                'id' => 36,
                'name' => 'City Social',
                'status' => '1',
            ),
        ));
        
        
    }
}