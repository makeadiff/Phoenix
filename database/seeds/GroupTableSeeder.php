<?php

use Illuminate\Database\Seeder;

class GroupTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        \DB::table('Group')->delete();
        
        \DB::table('Group')->insert(array(
            0 =>
            array(
                'id' => 1,
                'name' => 'Leadership Team',
                'type' => 'national',
                'group_type' => 'normal',
                'vertical_id' => 0,
                'region_id' => 0,
                'status' => '1',
            ),
            1 =>
            array(
                'id' => 2,
                'name' => 'City Team Lead',
                'type' => 'fellow',
                'group_type' => 'normal',
                'vertical_id' => 1,
                'region_id' => 0,
                'status' => '1',
            ),
            2 =>
            array(
                'id' => 3,
                'name' => 'All Access',
                'type' => 'national',
                'group_type' => 'normal',
                'vertical_id' => 20,
                'region_id' => 0,
                'status' => '0',
            ),
            3 =>
            array(
                'id' => 4,
                'name' => 'Shelter Support Fellow',
                'type' => 'fellow',
                'group_type' => 'normal',
                'vertical_id' => 4,
                'region_id' => 0,
                'status' => '1',
            ),
            4 =>
            array(
                'id' => 5,
                'name' => 'Human Capital Fellow',
                'type' => 'fellow',
                'group_type' => 'normal',
                'vertical_id' => 8,
                'region_id' => 0,
                'status' => '1',
            ),
            5 =>
            array(
                'id' => 10,
                'name' => 'CR Volunteer',
                'type' => 'volunteer',
                'group_type' => 'normal',
                'vertical_id' => 17,
                'region_id' => 0,
                'status' => '0',
            ),
            6 =>
            array(
                'id' => 8,
                'name' => 'ES Mentors',
                'type' => 'volunteer',
                'group_type' => 'normal',
                'vertical_id' => 3,
                'region_id' => 0,
                'status' => '1',
            ),
            7 =>
            array(
                'id' => 9,
                'name' => 'ES Volunteer',
                'type' => 'volunteer',
                'group_type' => 'normal',
                'vertical_id' => 3,
                'region_id' => 0,
                'status' => '1',
            ),
            8 =>
            array(
                'id' => 11,
                'name' => 'Campaigns Fellow',
                'type' => 'fellow',
                'group_type' => 'normal',
                'vertical_id' => 7,
                'region_id' => 0,
                'status' => '1',
            ),
            9 =>
            array(
                'id' => 12,
                'name' => 'Discover Fellow',
                'type' => 'fellow',
                'group_type' => 'normal',
                'vertical_id' => 6,
                'region_id' => 0,
                'status' => '0',
            ),
            10 =>
            array(
                'id' => 14,
                'name' => 'Intern',
                'type' => 'volunteer',
                'group_type' => 'normal',
                'vertical_id' => 0,
                'region_id' => 0,
                'status' => '0',
            ),
            11 =>
            array(
                'id' => 15,
                'name' => 'Finance Fellow',
                'type' => 'fellow',
                'group_type' => 'normal',
                'vertical_id' => 9,
                'region_id' => 0,
                'status' => '1',
            ),
            12 =>
            array(
                'id' => 19,
                'name' => 'Ed Support Fellow',
                'type' => 'fellow',
                'group_type' => 'normal',
                'vertical_id' => 3,
                'region_id' => 0,
                'status' => '1',
            ),
            13 =>
            array(
                'id' => 18,
                'name' => 'Library',
                'type' => 'volunteer',
                'group_type' => 'normal',
                'vertical_id' => 0,
                'region_id' => 0,
                'status' => '0',
            ),
            14 =>
            array(
                'id' => 20,
                'name' => 'Events Fellow',
                'type' => 'fellow',
                'group_type' => 'normal',
                'vertical_id' => 17,
                'region_id' => 0,
                'status' => '0',
            ),
            15 =>
            array(
                'id' => 21,
                'name' => 'Events Volunteer',
                'type' => 'volunteer',
                'group_type' => 'normal',
                'vertical_id' => 17,
                'region_id' => 0,
                'status' => '0',
            ),
            16 =>
            array(
                'id' => 22,
                'name' => 'Library Fellow',
                'type' => 'fellow',
                'group_type' => 'normal',
                'vertical_id' => 0,
                'region_id' => 0,
                'status' => '0',
            ),
            17 =>
            array(
                'id' => 23,
                'name' => 'Discover Volunteer',
                'type' => 'volunteer',
                'group_type' => 'normal',
                'vertical_id' => 6,
                'region_id' => 0,
                'status' => '0',
            ),
            18 =>
            array(
                'id' => 24,
                'name' => 'Executive Team',
                'type' => 'executive',
                'group_type' => 'normal',
                'vertical_id' => 1,
                'region_id' => 0,
                'status' => '1',
            ),
            19 =>
            array(
                'id' => 304,
                'name' => 'Discover Fellow, Central',
                'type' => 'fellow',
                'group_type' => 'hierarchy',
                'vertical_id' => 6,
                'region_id' => 4,
                'status' => '0',
            ),
            20 =>
            array(
                'id' => 303,
                'name' => 'Discover Fellow, Deccan',
                'type' => 'fellow',
                'group_type' => 'hierarchy',
                'vertical_id' => 6,
                'region_id' => 3,
                'status' => '0',
            ),
            21 =>
            array(
                'id' => 302,
                'name' => 'Discover Fellow, North',
                'type' => 'fellow',
                'group_type' => 'hierarchy',
                'vertical_id' => 6,
                'region_id' => 2,
                'status' => '0',
            ),
            22 =>
            array(
                'id' => 301,
                'name' => 'Discover Fellow, South',
                'type' => 'fellow',
                'group_type' => 'hierarchy',
                'vertical_id' => 6,
                'region_id' => 1,
                'status' => '0',
            ),
            23 =>
            array(
                'id' => 300,
                'name' => 'Propel Fellow, Central',
                'type' => 'fellow',
                'group_type' => 'hierarchy',
                'vertical_id' => 5,
                'region_id' => 4,
                'status' => '0',
            ),
            24 =>
            array(
                'id' => 299,
                'name' => 'Propel Fellow, Deccan',
                'type' => 'fellow',
                'group_type' => 'hierarchy',
                'vertical_id' => 5,
                'region_id' => 3,
                'status' => '0',
            ),
            25 =>
            array(
                'id' => 298,
                'name' => 'Propel Fellow, North',
                'type' => 'fellow',
                'group_type' => 'hierarchy',
                'vertical_id' => 5,
                'region_id' => 2,
                'status' => '0',
            ),
            26 =>
            array(
                'id' => 297,
                'name' => 'Propel Fellow, South',
                'type' => 'fellow',
                'group_type' => 'hierarchy',
                'vertical_id' => 5,
                'region_id' => 1,
                'status' => '0',
            ),
            27 =>
            array(
                'id' => 296,
                'name' => 'CS Fellow, Central',
                'type' => 'fellow',
                'group_type' => 'hierarchy',
                'vertical_id' => 4,
                'region_id' => 4,
                'status' => '0',
            ),
            28 =>
            array(
                'id' => 295,
                'name' => 'CS Fellow, Deccan',
                'type' => 'fellow',
                'group_type' => 'hierarchy',
                'vertical_id' => 4,
                'region_id' => 3,
                'status' => '0',
            ),
            29 =>
            array(
                'id' => 294,
                'name' => 'CS Fellow, North',
                'type' => 'fellow',
                'group_type' => 'hierarchy',
                'vertical_id' => 4,
                'region_id' => 2,
                'status' => '0',
            ),
            30 =>
            array(
                'id' => 293,
                'name' => 'CS Fellow, South',
                'type' => 'fellow',
                'group_type' => 'hierarchy',
                'vertical_id' => 4,
                'region_id' => 1,
                'status' => '0',
            ),
            31 =>
            array(
                'id' => 292,
                'name' => 'ES Fellow, Central',
                'type' => 'fellow',
                'group_type' => 'hierarchy',
                'vertical_id' => 3,
                'region_id' => 4,
                'status' => '0',
            ),
            32 =>
            array(
                'id' => 291,
                'name' => 'ES Fellow, Deccan',
                'type' => 'fellow',
                'group_type' => 'hierarchy',
                'vertical_id' => 3,
                'region_id' => 3,
                'status' => '0',
            ),
            33 =>
            array(
                'id' => 290,
                'name' => 'ES Fellow, North',
                'type' => 'fellow',
                'group_type' => 'hierarchy',
                'vertical_id' => 3,
                'region_id' => 2,
                'status' => '0',
            ),
            34 =>
            array(
                'id' => 289,
                'name' => 'ES Fellow, South',
                'type' => 'fellow',
                'group_type' => 'hierarchy',
                'vertical_id' => 3,
                'region_id' => 1,
                'status' => '0',
            ),
            35 =>
            array(
                'id' => 288,
                'name' => 'CH Fellow, Central',
                'type' => 'fellow',
                'group_type' => 'hierarchy',
                'vertical_id' => 16,
                'region_id' => 4,
                'status' => '0',
            ),
            36 =>
            array(
                'id' => 287,
                'name' => 'CH Fellow, Deccan',
                'type' => 'fellow',
                'group_type' => 'hierarchy',
                'vertical_id' => 16,
                'region_id' => 3,
                'status' => '0',
            ),
            37 =>
            array(
                'id' => 286,
                'name' => 'CH Fellow, North',
                'type' => 'fellow',
                'group_type' => 'hierarchy',
                'vertical_id' => 16,
                'region_id' => 2,
                'status' => '0',
            ),
            38 =>
            array(
                'id' => 285,
                'name' => 'CH Fellow, South',
                'type' => 'fellow',
                'group_type' => 'hierarchy',
                'vertical_id' => 16,
                'region_id' => 1,
                'status' => '0',
            ),
            39 =>
            array(
                'id' => 284,
                'name' => 'CTL, Central',
                'type' => 'fellow',
                'group_type' => 'hierarchy',
                'vertical_id' => 16,
                'region_id' => 4,
                'status' => '0',
            ),
            40 =>
            array(
                'id' => 283,
                'name' => 'CTL, Deccan',
                'type' => 'fellow',
                'group_type' => 'hierarchy',
                'vertical_id' => 16,
                'region_id' => 3,
                'status' => '0',
            ),
            41 =>
            array(
                'id' => 282,
                'name' => 'CTL, North',
                'type' => 'fellow',
                'group_type' => 'hierarchy',
                'vertical_id' => 16,
                'region_id' => 2,
                'status' => '0',
            ),
            42 =>
            array(
                'id' => 281,
                'name' => 'CTL, South',
                'type' => 'fellow',
                'group_type' => 'hierarchy',
                'vertical_id' => 16,
                'region_id' => 1,
                'status' => '0',
            ),
            43 =>
            array(
                'id' => 280,
                'name' => 'CR Fellow',
                'type' => 'fellow',
                'group_type' => 'normal',
                'vertical_id' => 17,
                'region_id' => 0,
                'status' => '0',
            ),
            44 =>
            array(
                'id' => 279,
                'name' => 'CFR Fellow',
                'type' => 'fellow',
                'group_type' => 'normal',
                'vertical_id' => 17,
                'region_id' => 0,
                'status' => '0',
            ),
            45 =>
            array(
                'id' => 355,
                'name' => 'Ed Support Strat',
                'type' => 'strat',
                'group_type' => 'normal',
                'vertical_id' => 3,
                'region_id' => 0,
                'status' => '1',
            ),
            46 =>
            array(
                'id' => 356,
                'name' => 'Discover Strat',
                'type' => 'strat',
                'group_type' => 'normal',
                'vertical_id' => 6,
                'region_id' => 0,
                'status' => '0',
            ),
            47 =>
            array(
                'id' => 350,
                'name' => 'Strat',
                'type' => 'strat',
                'group_type' => 'normal',
                'vertical_id' => 0,
                'region_id' => 0,
                'status' => '1',
            ),
            48 =>
            array(
                'id' => 354,
                'name' => 'Campaign Strat',
                'type' => 'strat',
                'group_type' => 'normal',
                'vertical_id' => 7,
                'region_id' => 0,
                'status' => '1',
            ),
            49 =>
            array(
                'id' => 272,
                'name' => 'Transition Readiness Fellow',
                'type' => 'fellow',
                'group_type' => 'normal',
                'vertical_id' => 5,
                'region_id' => 0,
                'status' => '1',
            ),
            50 =>
            array(
                'id' => 358,
                'name' => 'Shelter Support Strat',
                'type' => 'strat',
                'group_type' => 'normal',
                'vertical_id' => 4,
                'region_id' => 0,
                'status' => '1',
            ),
            51 =>
            array(
                'id' => 357,
                'name' => 'HC Strat',
                'type' => 'strat',
                'group_type' => 'normal',
                'vertical_id' => 8,
                'region_id' => 0,
                'status' => '1',
            ),
            52 =>
            array(
                'id' => 269,
                'name' => 'Shelter Operations Fellow',
                'type' => 'fellow',
                'group_type' => 'normal',
                'vertical_id' => 2,
                'region_id' => 0,
                'status' => '1',
            ),
            53 =>
            array(
                'id' => 267,
                'name' => 'Center Head, Central',
                'type' => 'fellow',
                'group_type' => 'hierarchy',
                'vertical_id' => 0,
                'region_id' => 4,
                'status' => '0',
            ),
            54 =>
            array(
                'id' => 266,
                'name' => 'Center Head, Deccan',
                'type' => 'fellow',
                'group_type' => 'hierarchy',
                'vertical_id' => 0,
                'region_id' => 3,
                'status' => '0',
            ),
            55 =>
            array(
                'id' => 265,
                'name' => 'Center Head, North',
                'type' => 'fellow',
                'group_type' => 'hierarchy',
                'vertical_id' => 0,
                'region_id' => 2,
                'status' => '0',
            ),
            56 =>
            array(
                'id' => 264,
                'name' => 'Center Head, South',
                'type' => 'fellow',
                'group_type' => 'hierarchy',
                'vertical_id' => 0,
                'region_id' => 1,
                'status' => '0',
            ),
            57 =>
            array(
                'id' => 360,
                'name' => 'Finance Strat',
                'type' => 'strat',
                'group_type' => 'normal',
                'vertical_id' => 9,
                'region_id' => 0,
                'status' => '1',
            ),
            58 =>
            array(
                'id' => 262,
                'name' => 'Fund Raising Head',
                'type' => 'fellow',
                'group_type' => 'normal',
                'vertical_id' => 17,
                'region_id' => 0,
                'status' => '0',
            ),
            59 =>
            array(
                'id' => 261,
                'name' => 'CTL, Central',
                'type' => 'fellow',
                'group_type' => 'hierarchy',
                'vertical_id' => 0,
                'region_id' => 4,
                'status' => '0',
            ),
            60 =>
            array(
                'id' => 260,
                'name' => 'CTL, Deccan',
                'type' => 'fellow',
                'group_type' => 'hierarchy',
                'vertical_id' => 0,
                'region_id' => 3,
                'status' => '0',
            ),
            61 =>
            array(
                'id' => 259,
                'name' => 'CTL, North',
                'type' => 'fellow',
                'group_type' => 'hierarchy',
                'vertical_id' => 0,
                'region_id' => 2,
                'status' => '0',
            ),
            62 =>
            array(
                'id' => 258,
                'name' => 'CTL, South',
                'type' => 'fellow',
                'group_type' => 'hierarchy',
                'vertical_id' => 0,
                'region_id' => 1,
                'status' => '0',
            ),
            63 =>
            array(
                'id' => 256,
                'name' => 'CR Strat, Central',
                'type' => 'strat',
                'group_type' => 'hierarchy',
                'vertical_id' => 17,
                'region_id' => 4,
                'status' => '0',
            ),
            64 =>
            array(
                'id' => 255,
                'name' => 'CR Strat, Deccan',
                'type' => 'strat',
                'group_type' => 'hierarchy',
                'vertical_id' => 17,
                'region_id' => 3,
                'status' => '0',
            ),
            65 =>
            array(
                'id' => 254,
                'name' => 'CR Strat, North',
                'type' => 'strat',
                'group_type' => 'hierarchy',
                'vertical_id' => 17,
                'region_id' => 2,
                'status' => '0',
            ),
            66 =>
            array(
                'id' => 253,
                'name' => 'CR Strat, South',
                'type' => 'strat',
                'group_type' => 'hierarchy',
                'vertical_id' => 17,
                'region_id' => 1,
                'status' => '0',
            ),
            67 =>
            array(
                'id' => 252,
                'name' => 'Program Director, Corporate Relations',
                'type' => 'national',
                'group_type' => 'normal',
                'vertical_id' => 17,
                'region_id' => 0,
                'status' => '0',
            ),
            68 =>
            array(
                'id' => 251,
                'name' => 'CFR Strat, Central',
                'type' => 'strat',
                'group_type' => 'hierarchy',
                'vertical_id' => 17,
                'region_id' => 4,
                'status' => '0',
            ),
            69 =>
            array(
                'id' => 250,
                'name' => 'CFR Strat, Deccan',
                'type' => 'strat',
                'group_type' => 'hierarchy',
                'vertical_id' => 17,
                'region_id' => 3,
                'status' => '0',
            ),
            70 =>
            array(
                'id' => 249,
                'name' => 'CFR Strat, North',
                'type' => 'strat',
                'group_type' => 'hierarchy',
                'vertical_id' => 17,
                'region_id' => 2,
                'status' => '0',
            ),
            71 =>
            array(
                'id' => 248,
                'name' => 'CFR Strat',
                'type' => 'strat',
                'group_type' => 'normal',
                'vertical_id' => 17,
                'region_id' => 0,
                'status' => '0',
            ),
            72 =>
            array(
                'id' => 247,
                'name' => 'Program Director, Community Fund Raising',
                'type' => 'national',
                'group_type' => 'normal',
                'vertical_id' => 17,
                'region_id' => 0,
                'status' => '0',
            ),
            73 =>
            array(
                'id' => 351,
                'name' => 'Events Strat',
                'type' => 'strat',
                'group_type' => 'normal',
                'vertical_id' => 17,
                'region_id' => 0,
                'status' => '0',
            ),
            74 =>
            array(
                'id' => 352,
                'name' => 'CR Strat',
                'type' => 'strat',
                'group_type' => 'normal',
                'vertical_id' => 17,
                'region_id' => 0,
                'status' => '0',
            ),
            75 =>
            array(
                'id' => 353,
                'name' => 'Operations Director',
                'type' => 'national',
                'group_type' => 'normal',
                'vertical_id' => 2,
                'region_id' => 0,
                'status' => '1',
            ),
            76 =>
            array(
                'id' => 242,
                'name' => 'Program Director, Fundraising Head',
                'type' => 'national',
                'group_type' => 'normal',
                'vertical_id' => 17,
                'region_id' => 0,
                'status' => '1',
            ),
            77 =>
            array(
                'id' => 241,
                'name' => 'Events Strat, Central',
                'type' => 'strat',
                'group_type' => 'hierarchy',
                'vertical_id' => 17,
                'region_id' => 4,
                'status' => '0',
            ),
            78 =>
            array(
                'id' => 240,
                'name' => 'Events Strat, Deccan',
                'type' => 'strat',
                'group_type' => 'hierarchy',
                'vertical_id' => 17,
                'region_id' => 3,
                'status' => '0',
            ),
            79 =>
            array(
                'id' => 239,
                'name' => 'Events Strat, North',
                'type' => 'strat',
                'group_type' => 'hierarchy',
                'vertical_id' => 17,
                'region_id' => 2,
                'status' => '0',
            ),
            80 =>
            array(
                'id' => 238,
                'name' => 'Events Strat, South',
                'type' => 'strat',
                'group_type' => 'hierarchy',
                'vertical_id' => 17,
                'region_id' => 1,
                'status' => '0',
            ),
            81 =>
            array(
                'id' => 237,
                'name' => 'Program Director, Events',
                'type' => 'national',
                'group_type' => 'normal',
                'vertical_id' => 17,
                'region_id' => 0,
                'status' => '0',
            ),
            82 =>
            array(
                'id' => 236,
                'name' => 'Finance Strat, Central',
                'type' => 'strat',
                'group_type' => 'hierarchy',
                'vertical_id' => 9,
                'region_id' => 4,
                'status' => '0',
            ),
            83 =>
            array(
                'id' => 235,
                'name' => 'Finance Strat, Deccan',
                'type' => 'strat',
                'group_type' => 'hierarchy',
                'vertical_id' => 9,
                'region_id' => 3,
                'status' => '0',
            ),
            84 =>
            array(
                'id' => 234,
                'name' => 'Finance Strat, North',
                'type' => 'strat',
                'group_type' => 'hierarchy',
                'vertical_id' => 9,
                'region_id' => 2,
                'status' => '0',
            ),
            85 =>
            array(
                'id' => 233,
                'name' => 'Finance Strat, South',
                'type' => 'strat',
                'group_type' => 'hierarchy',
                'vertical_id' => 9,
                'region_id' => 1,
                'status' => '0',
            ),
            86 =>
            array(
                'id' => 232,
                'name' => 'Program Director, Finance',
                'type' => 'national',
                'group_type' => 'normal',
                'vertical_id' => 9,
                'region_id' => 0,
                'status' => '1',
            ),
            87 =>
            array(
                'id' => 231,
                'name' => 'HR Strat, Central',
                'type' => 'strat',
                'group_type' => 'hierarchy',
                'vertical_id' => 8,
                'region_id' => 4,
                'status' => '0',
            ),
            88 =>
            array(
                'id' => 230,
                'name' => 'HR Strat, Deccan',
                'type' => 'strat',
                'group_type' => 'hierarchy',
                'vertical_id' => 8,
                'region_id' => 3,
                'status' => '0',
            ),
            89 =>
            array(
                'id' => 229,
                'name' => 'HR Strat, North',
                'type' => 'strat',
                'group_type' => 'hierarchy',
                'vertical_id' => 8,
                'region_id' => 2,
                'status' => '0',
            ),
            90 =>
            array(
                'id' => 228,
                'name' => 'HR Strat, South',
                'type' => 'strat',
                'group_type' => 'hierarchy',
                'vertical_id' => 8,
                'region_id' => 1,
                'status' => '0',
            ),
            91 =>
            array(
                'id' => 227,
                'name' => 'Program Director, Human Capital',
                'type' => 'national',
                'group_type' => 'normal',
                'vertical_id' => 8,
                'region_id' => 0,
                'status' => '1',
            ),
            92 =>
            array(
                'id' => 226,
                'name' => 'PR Strat, Central',
                'type' => 'strat',
                'group_type' => 'hierarchy',
                'vertical_id' => 7,
                'region_id' => 4,
                'status' => '0',
            ),
            93 =>
            array(
                'id' => 225,
                'name' => 'PR Strat, Deccan',
                'type' => 'strat',
                'group_type' => 'hierarchy',
                'vertical_id' => 7,
                'region_id' => 3,
                'status' => '0',
            ),
            94 =>
            array(
                'id' => 224,
                'name' => 'PR Strat, North',
                'type' => 'strat',
                'group_type' => 'hierarchy',
                'vertical_id' => 7,
                'region_id' => 2,
                'status' => '0',
            ),
            95 =>
            array(
                'id' => 223,
                'name' => 'PR Strat, South',
                'type' => 'strat',
                'group_type' => 'hierarchy',
                'vertical_id' => 7,
                'region_id' => 1,
                'status' => '0',
            ),
            96 =>
            array(
                'id' => 222,
                'name' => 'Program Director, Public Relations',
                'type' => 'national',
                'group_type' => 'normal',
                'vertical_id' => 7,
                'region_id' => 0,
                'status' => '1',
            ),
            97 =>
            array(
                'id' => 221,
                'name' => 'Discover Strat, Central',
                'type' => 'strat',
                'group_type' => 'hierarchy',
                'vertical_id' => 6,
                'region_id' => 4,
                'status' => '0',
            ),
            98 =>
            array(
                'id' => 220,
                'name' => 'Discover Strat, Deccan',
                'type' => 'strat',
                'group_type' => 'hierarchy',
                'vertical_id' => 6,
                'region_id' => 3,
                'status' => '0',
            ),
            99 =>
            array(
                'id' => 219,
                'name' => 'Discover Strat, North',
                'type' => 'strat',
                'group_type' => 'hierarchy',
                'vertical_id' => 6,
                'region_id' => 2,
                'status' => '0',
            ),
            100 =>
            array(
                'id' => 218,
                'name' => 'Discover Strat, South',
                'type' => 'strat',
                'group_type' => 'hierarchy',
                'vertical_id' => 6,
                'region_id' => 1,
                'status' => '0',
            ),
            101 =>
            array(
                'id' => 217,
                'name' => 'Program Director, Discover',
                'type' => 'national',
                'group_type' => 'normal',
                'vertical_id' => 6,
                'region_id' => 0,
                'status' => '0',
            ),
            102 =>
            array(
                'id' => 216,
                'name' => 'Propel Strat, Central',
                'type' => 'strat',
                'group_type' => 'hierarchy',
                'vertical_id' => 5,
                'region_id' => 4,
                'status' => '0',
            ),
            103 =>
            array(
                'id' => 215,
                'name' => 'Propel Strat, Deccan',
                'type' => 'strat',
                'group_type' => 'hierarchy',
                'vertical_id' => 5,
                'region_id' => 3,
                'status' => '0',
            ),
            104 =>
            array(
                'id' => 214,
                'name' => 'Propel Strat, North',
                'type' => 'strat',
                'group_type' => 'hierarchy',
                'vertical_id' => 5,
                'region_id' => 2,
                'status' => '0',
            ),
            105 =>
            array(
                'id' => 213,
                'name' => 'Propel Strat, South',
                'type' => 'strat',
                'group_type' => 'hierarchy',
                'vertical_id' => 5,
                'region_id' => 1,
                'status' => '0',
            ),
            106 =>
            array(
                'id' => 212,
                'name' => 'Program Director, Transition Readiness',
                'type' => 'national',
                'group_type' => 'normal',
                'vertical_id' => 5,
                'region_id' => 0,
                'status' => '1',
            ),
            107 =>
            array(
                'id' => 211,
                'name' => 'CS Strat, Central',
                'type' => 'strat',
                'group_type' => 'hierarchy',
                'vertical_id' => 4,
                'region_id' => 4,
                'status' => '0',
            ),
            108 =>
            array(
                'id' => 210,
                'name' => 'CS Strat, Deccan',
                'type' => 'strat',
                'group_type' => 'hierarchy',
                'vertical_id' => 4,
                'region_id' => 3,
                'status' => '0',
            ),
            109 =>
            array(
                'id' => 209,
                'name' => 'CS Strat, North',
                'type' => 'strat',
                'group_type' => 'hierarchy',
                'vertical_id' => 4,
                'region_id' => 2,
                'status' => '0',
            ),
            110 =>
            array(
                'id' => 208,
                'name' => 'CS Strat, South',
                'type' => 'strat',
                'group_type' => 'hierarchy',
                'vertical_id' => 4,
                'region_id' => 1,
                'status' => '0',
            ),
            111 =>
            array(
                'id' => 207,
                'name' => 'Program Director, Shelter Support',
                'type' => 'national',
                'group_type' => 'normal',
                'vertical_id' => 4,
                'region_id' => 0,
                'status' => '1',
            ),
            112 =>
            array(
                'id' => 206,
                'name' => 'ES Strat, Central',
                'type' => 'strat',
                'group_type' => 'hierarchy',
                'vertical_id' => 3,
                'region_id' => 4,
                'status' => '0',
            ),
            113 =>
            array(
                'id' => 205,
                'name' => 'ES Strat, Deccan',
                'type' => 'strat',
                'group_type' => 'hierarchy',
                'vertical_id' => 3,
                'region_id' => 3,
                'status' => '0',
            ),
            114 =>
            array(
                'id' => 204,
                'name' => 'ES Strat, North',
                'type' => 'strat',
                'group_type' => 'hierarchy',
                'vertical_id' => 3,
                'region_id' => 2,
                'status' => '0',
            ),
            115 =>
            array(
                'id' => 203,
                'name' => 'ES Strat, South',
                'type' => 'strat',
                'group_type' => 'hierarchy',
                'vertical_id' => 3,
                'region_id' => 1,
                'status' => '0',
            ),
            116 =>
            array(
                'id' => 202,
                'name' => 'Program Director, Ed Support',
                'type' => 'national',
                'group_type' => 'normal',
                'vertical_id' => 3,
                'region_id' => 0,
                'status' => '1',
            ),
            117 =>
            array(
                'id' => 201,
                'name' => 'CH Strat, Central',
                'type' => 'strat',
                'group_type' => 'hierarchy',
                'vertical_id' => 16,
                'region_id' => 4,
                'status' => '0',
            ),
            118 =>
            array(
                'id' => 200,
                'name' => 'CH Strat, Deccan',
                'type' => 'strat',
                'group_type' => 'hierarchy',
                'vertical_id' => 16,
                'region_id' => 3,
                'status' => '0',
            ),
            119 =>
            array(
                'id' => 199,
                'name' => 'CH Strat, North',
                'type' => 'strat',
                'group_type' => 'hierarchy',
                'vertical_id' => 16,
                'region_id' => 2,
                'status' => '0',
            ),
            120 =>
            array(
                'id' => 198,
                'name' => 'CH Strat, South',
                'type' => 'strat',
                'group_type' => 'hierarchy',
                'vertical_id' => 16,
                'region_id' => 1,
                'status' => '0',
            ),
            121 =>
            array(
                'id' => 359,
                'name' => 'Transition Readiness Strat',
                'type' => 'strat',
                'group_type' => 'normal',
                'vertical_id' => 5,
                'region_id' => 0,
                'status' => '1',
            ),
            122 =>
            array(
                'id' => 191,
                'name' => 'Operations Director, Central',
                'type' => 'national',
                'group_type' => 'hierarchy',
                'vertical_id' => 16,
                'region_id' => 4,
                'status' => '0',
            ),
            123 =>
            array(
                'id' => 190,
                'name' => 'Operations Director, Deccan',
                'type' => 'national',
                'group_type' => 'hierarchy',
                'vertical_id' => 16,
                'region_id' => 3,
                'status' => '0',
            ),
            124 =>
            array(
                'id' => 189,
                'name' => 'Operations Director, North',
                'type' => 'national',
                'group_type' => 'hierarchy',
                'vertical_id' => 16,
                'region_id' => 2,
                'status' => '0',
            ),
            125 =>
            array(
                'id' => 188,
                'name' => 'Operations Director, South',
                'type' => 'national',
                'group_type' => 'hierarchy',
                'vertical_id' => 16,
                'region_id' => 1,
                'status' => '0',
            ),
            126 =>
            array(
                'id' => 305,
                'name' => 'PR Fellow, South',
                'type' => 'fellow',
                'group_type' => 'hierarchy',
                'vertical_id' => 7,
                'region_id' => 1,
                'status' => '0',
            ),
            127 =>
            array(
                'id' => 306,
                'name' => 'PR Fellow, North',
                'type' => 'fellow',
                'group_type' => 'hierarchy',
                'vertical_id' => 7,
                'region_id' => 2,
                'status' => '0',
            ),
            128 =>
            array(
                'id' => 307,
                'name' => 'PR Fellow, Deccan',
                'type' => 'fellow',
                'group_type' => 'hierarchy',
                'vertical_id' => 7,
                'region_id' => 3,
                'status' => '0',
            ),
            129 =>
            array(
                'id' => 308,
                'name' => 'PR Fellow, Central',
                'type' => 'fellow',
                'group_type' => 'hierarchy',
                'vertical_id' => 7,
                'region_id' => 4,
                'status' => '0',
            ),
            130 =>
            array(
                'id' => 309,
                'name' => 'HR Fellow, South',
                'type' => 'fellow',
                'group_type' => 'hierarchy',
                'vertical_id' => 8,
                'region_id' => 1,
                'status' => '0',
            ),
            131 =>
            array(
                'id' => 310,
                'name' => 'HR Fellow, North',
                'type' => 'fellow',
                'group_type' => 'hierarchy',
                'vertical_id' => 8,
                'region_id' => 2,
                'status' => '0',
            ),
            132 =>
            array(
                'id' => 311,
                'name' => 'HR Fellow, Deccan',
                'type' => 'fellow',
                'group_type' => 'hierarchy',
                'vertical_id' => 8,
                'region_id' => 3,
                'status' => '0',
            ),
            133 =>
            array(
                'id' => 312,
                'name' => 'HR Fellow, Central',
                'type' => 'fellow',
                'group_type' => 'hierarchy',
                'vertical_id' => 8,
                'region_id' => 4,
                'status' => '0',
            ),
            134 =>
            array(
                'id' => 313,
                'name' => 'Finance Fellow, South',
                'type' => 'fellow',
                'group_type' => 'hierarchy',
                'vertical_id' => 9,
                'region_id' => 1,
                'status' => '0',
            ),
            135 =>
            array(
                'id' => 314,
                'name' => 'Finance Fellow, North',
                'type' => 'fellow',
                'group_type' => 'hierarchy',
                'vertical_id' => 9,
                'region_id' => 2,
                'status' => '0',
            ),
            136 =>
            array(
                'id' => 315,
                'name' => 'Finance Fellow, Deccan',
                'type' => 'fellow',
                'group_type' => 'hierarchy',
                'vertical_id' => 9,
                'region_id' => 3,
                'status' => '0',
            ),
            137 =>
            array(
                'id' => 316,
                'name' => 'Finance Fellow, Central',
                'type' => 'fellow',
                'group_type' => 'hierarchy',
                'vertical_id' => 9,
                'region_id' => 4,
                'status' => '0',
            ),
            138 =>
            array(
                'id' => 317,
                'name' => 'Events Fellow, South',
                'type' => 'fellow',
                'group_type' => 'hierarchy',
                'vertical_id' => 17,
                'region_id' => 1,
                'status' => '0',
            ),
            139 =>
            array(
                'id' => 318,
                'name' => 'Events Fellow, North',
                'type' => 'fellow',
                'group_type' => 'hierarchy',
                'vertical_id' => 17,
                'region_id' => 2,
                'status' => '0',
            ),
            140 =>
            array(
                'id' => 319,
                'name' => 'Events Fellow, Deccan',
                'type' => 'fellow',
                'group_type' => 'hierarchy',
                'vertical_id' => 17,
                'region_id' => 3,
                'status' => '0',
            ),
            141 =>
            array(
                'id' => 320,
                'name' => 'Events Fellow, Central',
                'type' => 'fellow',
                'group_type' => 'hierarchy',
                'vertical_id' => 17,
                'region_id' => 4,
                'status' => '0',
            ),
            142 =>
            array(
                'id' => 321,
                'name' => 'FH, South',
                'type' => 'fellow',
                'group_type' => 'hierarchy',
                'vertical_id' => 17,
                'region_id' => 1,
                'status' => '0',
            ),
            143 =>
            array(
                'id' => 322,
                'name' => 'FH, North',
                'type' => 'fellow',
                'group_type' => 'hierarchy',
                'vertical_id' => 17,
                'region_id' => 2,
                'status' => '0',
            ),
            144 =>
            array(
                'id' => 323,
                'name' => 'FH, Deccan',
                'type' => 'fellow',
                'group_type' => 'hierarchy',
                'vertical_id' => 17,
                'region_id' => 3,
                'status' => '0',
            ),
            145 =>
            array(
                'id' => 324,
                'name' => 'FH, Central',
                'type' => 'fellow',
                'group_type' => 'hierarchy',
                'vertical_id' => 17,
                'region_id' => 4,
                'status' => '0',
            ),
            146 =>
            array(
                'id' => 325,
                'name' => 'CFR Fellow, South',
                'type' => 'fellow',
                'group_type' => 'hierarchy',
                'vertical_id' => 17,
                'region_id' => 1,
                'status' => '0',
            ),
            147 =>
            array(
                'id' => 326,
                'name' => 'CFR Fellow, North',
                'type' => 'fellow',
                'group_type' => 'hierarchy',
                'vertical_id' => 17,
                'region_id' => 2,
                'status' => '0',
            ),
            148 =>
            array(
                'id' => 327,
                'name' => 'CFR Fellow, Deccan',
                'type' => 'fellow',
                'group_type' => 'hierarchy',
                'vertical_id' => 17,
                'region_id' => 3,
                'status' => '0',
            ),
            149 =>
            array(
                'id' => 328,
                'name' => 'CFR Fellow, Central',
                'type' => 'fellow',
                'group_type' => 'hierarchy',
                'vertical_id' => 17,
                'region_id' => 4,
                'status' => '0',
            ),
            150 =>
            array(
                'id' => 329,
                'name' => 'CR Fellow, South',
                'type' => 'fellow',
                'group_type' => 'hierarchy',
                'vertical_id' => 17,
                'region_id' => 1,
                'status' => '0',
            ),
            151 =>
            array(
                'id' => 330,
                'name' => 'CR Fellow, North',
                'type' => 'fellow',
                'group_type' => 'hierarchy',
                'vertical_id' => 17,
                'region_id' => 2,
                'status' => '0',
            ),
            152 =>
            array(
                'id' => 331,
                'name' => 'CR Fellow, Deccan',
                'type' => 'fellow',
                'group_type' => 'hierarchy',
                'vertical_id' => 17,
                'region_id' => 3,
                'status' => '0',
            ),
            153 =>
            array(
                'id' => 332,
                'name' => 'CR Fellow, Central',
                'type' => 'fellow',
                'group_type' => 'hierarchy',
                'vertical_id' => 17,
                'region_id' => 4,
                'status' => '0',
            ),
            154 =>
            array(
                'id' => 370,
                'name' => 'Fundraising Fellow',
                'type' => 'fellow',
                'group_type' => 'normal',
                'vertical_id' => 17,
                'region_id' => 0,
                'status' => '1',
            ),
            155 =>
            array(
                'id' => 363,
                'name' => 'National Intern',
                'type' => 'national',
                'group_type' => 'normal',
                'vertical_id' => 0,
                'region_id' => 0,
                'status' => '1',
            ),
            156 =>
            array(
                'id' => 336,
                'name' => 'CS Volunteer',
                'type' => 'volunteer',
                'group_type' => 'normal',
                'vertical_id' => 4,
                'region_id' => 0,
                'status' => '0',
            ),
            157 =>
            array(
                'id' => 364,
                'name' => 'Shelter Operations Strat',
                'type' => 'strat',
                'group_type' => 'normal',
                'vertical_id' => 2,
                'region_id' => 0,
                'status' => '1',
            ),
            158 =>
            array(
                'id' => 339,
                'name' => 'PR Volunteer',
                'type' => 'volunteer',
                'group_type' => 'normal',
                'vertical_id' => 7,
                'region_id' => 0,
                'status' => '1',
            ),
            159 =>
            array(
                'id' => 340,
                'name' => 'HC Volunteer',
                'type' => 'volunteer',
                'group_type' => 'normal',
                'vertical_id' => 8,
                'region_id' => 0,
                'status' => '1',
            ),
            160 =>
            array(
                'id' => 341,
                'name' => 'Finance Volunteer',
                'type' => 'volunteer',
                'group_type' => 'normal',
                'vertical_id' => 9,
                'region_id' => 0,
                'status' => '0',
            ),
            161 =>
            array(
                'id' => 369,
                'name' => 'FR Volunteer',
                'type' => 'volunteer',
                'group_type' => 'normal',
                'vertical_id' => 17,
                'region_id' => 0,
                'status' => '1',
            ),
            162 =>
            array(
                'id' => 344,
                'name' => 'CFR Volunteer',
                'type' => 'volunteer',
                'group_type' => 'normal',
                'vertical_id' => 17,
                'region_id' => 0,
                'status' => '0',
            ),
            163 =>
            array(
                'id' => 348,
                'name' => 'Transition Readiness Wingman',
                'type' => 'volunteer',
                'group_type' => 'normal',
                'vertical_id' => 5,
                'region_id' => 0,
                'status' => '1',
            ),
            164 =>
            array(
                'id' => 349,
                'name' => 'Transition Readiness ASV',
                'type' => 'volunteer',
                'group_type' => 'normal',
                'vertical_id' => 5,
                'region_id' => 0,
                'status' => '1',
            ),
            165 =>
            array(
                'id' => 366,
                'name' => 'Program Director, Foundational Programme',
                'type' => 'national',
                'group_type' => 'normal',
                'vertical_id' => 19,
                'region_id' => 0,
                'status' => '1',
            ),
            166 =>
            array(
                'id' => 361,
                'name' => 'Program Director, Problem Definition',
                'type' => 'national',
                'group_type' => 'normal',
                'vertical_id' => 15,
                'region_id' => 0,
                'status' => '1',
            ),
            167 =>
            array(
                'id' => 362,
                'name' => 'Problem Definition Fellow',
                'type' => 'fellow',
                'group_type' => 'normal',
                'vertical_id' => 15,
                'region_id' => 0,
                'status' => '0',
            ),
            168 =>
            array(
                'id' => 368,
                'name' => 'ES Trained',
                'type' => 'volunteer',
                'group_type' => 'normal',
                'vertical_id' => 3,
                'region_id' => 0,
                'status' => '1',
            ),
            169 =>
            array(
                'id' => 371,
                'name' => 'FR Strat',
                'type' => 'strat',
                'group_type' => 'normal',
                'vertical_id' => 17,
                'region_id' => 0,
                'status' => '1',
            ),
            170 =>
            array(
                'id' => 372,
                'name' => 'Mobilisation Strat',
                'type' => 'strat',
                'group_type' => 'normal',
                'vertical_id' => 8,
                'region_id' => 0,
                'status' => '1',
            ),
            171 =>
            array(
                'id' => 365,
                'name' => 'Aftercare Wingman',
                'type' => 'volunteer',
                'group_type' => 'normal',
                'vertical_id' => 18,
                'region_id' => 0,
                'status' => '1',
            ),
            172 =>
            array(
                'id' => 367,
                'name' => 'Program Director, Aftercare',
                'type' => 'national',
                'group_type' => 'normal',
                'vertical_id' => 19,
                'region_id' => 0,
                'status' => '1',
            ),
            173 =>
            array(
                'id' => 373,
                'name' => 'Tech',
                'type' => 'executive',
                'group_type' => 'normal',
                'vertical_id' => 0,
                'region_id' => 0,
                'status' => '1',
            ),
            174 =>
            array(
                'id' => 374,
                'name' => 'Dream Camp Lead',
                'type' => 'volunteer',
                'group_type' => 'normal',
                'vertical_id' => 0,
                'region_id' => 0,
                'status' => '1',
            ),
            175 =>
            array(
                'id' => 375,
                'name' => 'Foundational Fellow',
                'type' => 'fellow',
                'group_type' => 'normal',
                'vertical_id' => 19,
                'region_id' => 0,
                'status' => '1',
            ),
            176 =>
            array(
                'id' => 376,
                'name' => 'Foundational Skills Volunteer',
                'type' => 'volunteer',
                'group_type' => 'normal',
                'vertical_id' => 19,
                'region_id' => 0,
                'status' => '1',
            ),
            177 =>
            array(
                'id' => 377,
                'name' => 'Aftercare ASV',
                'type' => 'volunteer',
                'group_type' => 'normal',
                'vertical_id' => 18,
                'region_id' => 0,
                'status' => '1',
            ),
            178 =>
            array(
                'id' => 378,
                'name' => 'Aftercare Fellow',
                'type' => 'fellow',
                'group_type' => 'normal',
                'vertical_id' => 18,
                'region_id' => 0,
                'status' => '1',
            ),
            179 =>
            array(
                'id' => 381,
                'name' => 'Evaluator',
                'type' => 'strat',
                'group_type' => 'normal',
                'vertical_id' => 0,
                'region_id' => 0,
                'status' => '0',
            ),
            180 =>
            array(
                'id' => 382,
                'name' => 'Fellowship Evaluator',
                'type' => 'volunteer',
                'group_type' => 'normal',
                'vertical_id' => 0,
                'region_id' => 0,
                'status' => '1',
            ),
            181 =>
            array(
                'id' => 383,
                'name' => 'CTL Strat',
                'type' => 'strat',
                'group_type' => 'normal',
                'vertical_id' => 1,
                'region_id' => 0,
                'status' => '1',
            ),
            182 =>
            array(
                'id' => 385,
                'name' => 'Meta User',
                'type' => 'executive',
                'group_type' => 'normal',
                'vertical_id' => 0,
                'region_id' => 0,
                'status' => '1',
            ),
            183 =>
            array(
                'id' => 386,
                'name' => 'Foundation Mentor',
                'type' => 'volunteer',
                'group_type' => 'normal',
                'vertical_id' => 19,
                'region_id' => 0,
                'status' => '1',
            ),
            184 =>
            array(
                'id' => 387,
                'name' => 'Foundation Trained',
                'type' => 'volunteer',
                'group_type' => 'normal',
                'vertical_id' => 19,
                'region_id' => 0,
                'status' => '1',
            ),
            185 =>
            array(
                'id' => 388,
                'name' => ' Developer',
                'type' => 'volunteer',
                'group_type' => 'normal',
                'vertical_id' => 0,
                'region_id' => 0,
                'status' => '1',
            ),
            186 =>
            array(
                'id' => 389,
                'name' => 'Aftercare Coordinator',
                'type' => 'fellow',
                'group_type' => 'normal',
                'vertical_id' => 18,
                'region_id' => 0,
                'status' => '1',
            ),
            187 =>
            array(
                'id' => 390,
                'name' => 'Aftercare Strat',
                'type' => 'strat',
                'group_type' => 'normal',
                'vertical_id' => 18,
                'region_id' => 0,
                'status' => '1',
            ),
        ));
    }
}
