<?php

use Illuminate\Database\Seeder;

class UserGroupTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        \DB::table('UserGroup')->delete();
        
        \DB::table('UserGroup')->insert(array(
            0 =>
            array(
                'id' => 1,
                'user_id' => 1,
                'group_id' => 388,
                'year' => 2019,
            ),
            1 =>
            array(
                'id' => 2,
                'user_id' => 1,
                'group_id' => 1,
                'year' => 2019,
            ),
            2 =>
            array(
                'id' => 7,
                'user_id' => 4,
                'group_id' => 8,
                'year' => 2019,
            ),
            3 =>
            array(
                'id' => 8,
                'user_id' => 5,
                'group_id' => 9,
                'year' => 2019,
            ),
            4 =>
            array(
                'id' => 11,
                'user_id' => 3,
                'group_id' => 378,
                'year' => 2019,
            ),
            5 =>
            array(
                'id' => 12,
                'user_id' => 3,
                'group_id' => 9,
                'year' => 2019,
            ),
            6 =>
            array(
                'id' => 14,
                'user_id' => 3,
                'group_id' => 8,
                'year' => 2019,
            ),
            7 =>
            array(
                'id' => 15,
                'user_id' => 6,
                'group_id' => 388,
                'year' => 2019,
            ),
            8 =>
            array(
                'id' => 16,
                'user_id' => 6,
                'group_id' => 9,
                'year' => 2019,
            ),
            9 =>
            array(
                'id' => 17,
                'user_id' => 2,
                'group_id' => 388,
                'year' => 2019,
            ),
            10 =>
            array(
                'id' => 18,
                'user_id' => 2,
                'group_id' => 8,
                'year' => 2019,
            ),
            11 =>
            array(
                'id' => 19,
                'user_id' => 2,
                'group_id' => 9,
                'year' => 2019,
            ),
        ));
    }
}
