<?php

use Illuminate\Database\Seeder;

class MediumTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('Medium')->delete();
        
        \DB::table('Medium')->insert(array (
            0 => 
            array (
                'id' => 1,
                'name' => 'Bengali',
                'status' => '1',
            ),
            1 => 
            array (
                'id' => 2,
                'name' => 'English',
                'status' => '1',
            ),
            2 => 
            array (
                'id' => 3,
                'name' => 'Gujarati',
                'status' => '1',
            ),
            3 => 
            array (
                'id' => 4,
                'name' => 'Hindi',
                'status' => '1',
            ),
            4 => 
            array (
                'id' => 5,
                'name' => 'Kannada',
                'status' => '1',
            ),
            5 => 
            array (
                'id' => 6,
                'name' => 'Malayalam',
                'status' => '1',
            ),
            6 => 
            array (
                'id' => 7,
                'name' => 'Marathi',
                'status' => '1',
            ),
            7 => 
            array (
                'id' => 8,
                'name' => 'Tamil',
                'status' => '1',
            ),
            8 => 
            array (
                'id' => 9,
                'name' => 'Telegu',
                'status' => '1',
            ),
            9 => 
            array (
                'id' => 10,
                'name' => 'Urdu',
                'status' => '1',
            ),
        ));
        
        
    }
}