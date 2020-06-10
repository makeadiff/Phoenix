<?php

use Illuminate\Database\Seeder;

class ParameterTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        \DB::table('Parameter')->delete();
        
        \DB::table('Parameter')->insert([
            [
                'id' => 1,
                'name' => 'Credit gained for substituting',
                'description'   => '',
                'credit'        => 1.00,
                'added_on' => '2020-05-31 12:02:31',
                'updated_on' => '2020-05-31 12:02:31',
                'vertical_id' => 3,
                'status' => '1',
            ],[
                'id' => 2,
                'name' => 'Credit lost for getting a substitute',
                'description'   => '',
                'credit'        => -1.00,
                'added_on' => '2020-05-31 12:02:31',
                'updated_on' => '2020-05-31 12:02:31',
                'vertical_id' => 3,
                'status' => '1',
            ],[
                'id' => 3,
                'name' => 'Credit lost for missing zero hour',
                'description'   => '',
                'credit'        => -0.50,
                'added_on' => '2020-05-31 12:05:36',
                'updated_on' => '2020-05-31 12:05:36',
                'vertical_id' => 3,
                'status' => '1',
            ],[
                'id' => 4,
                'name' => 'Credit lost for missing class',
                'description'   => '',
                'credit'        => -2.00,
                'added_on' => '2020-05-31 12:05:36',
                'updated_on' => '2020-05-31 12:05:36',
                'vertical_id' => 3,
                'status' => '1',
            ],[
                'id' => 5,
                'name' => 'Credit gained for substituting',
                'description'   => '',
                'credit'        => 1.00,
                'added_on' => '2020-05-31 12:02:31',
                'updated_on' => '2020-05-31 12:02:31',
                'vertical_id' => 19,
                'status' => '1',
            ],[
                'id' => 6,
                'name' => 'Credit lost for getting a substitute',
                'description'   => '',
                'credit'        => -1.00,
                'added_on' => '2020-05-31 12:02:31',
                'updated_on' => '2020-05-31 12:02:31',
                'vertical_id' => 19,
                'status' => '1',
            ],[
                'id' => 7,
                'name' => 'Credit lost for missing zero hour',
                'description'   => '',
                'credit'        => -0.50,
                'added_on' => '2020-05-31 12:05:36',
                'updated_on' => '2020-05-31 12:05:36',
                'vertical_id' => 19,
                'status' => '1',
            ],[
                'id' => 8,
                'name' => 'Credit lost for missing class',
                'description'   => '',
                'credit'        => -2.00,
                'added_on' => '2020-05-31 12:05:36',
                'updated_on' => '2020-05-31 12:05:36',
                'vertical_id' => 19,
                'status' => '1',
            ]
        ]);
    }
}
