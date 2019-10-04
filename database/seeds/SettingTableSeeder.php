<?php

use Illuminate\Database\Seeder;

class SettingTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('Setting')->delete();
        
        \DB::table('Setting')->insert(array (
            0 => 
            array (
                'id' => 2,
                'name' => 'hr_email_city_common',
                'value' => 'shilpa.manari@makeadiff.in',
                'data' => '',
            ),
            1 => 
            array (
                'id' => 3,
                'name' => 'new_registration_welcome_message',
                'value' => '',
                'data' => 'Hi %FIRST_NAME%,

If you are seeing this email, that means you have successfully registered with us! Thank you for your interest in MAD. 

Make A Difference conducts its major recruitment drives in two phases in the year i.e one from June - August and the other from September - October!
In some cities we may hold a third drive based only on requirement.

The dates of the recruitment drive vary from city to city.
Once the recruitment drive is fixed for the city, our volunteers will get in touch with you with the details.

We look forward to work with you.


--
Shilpa Gangadharan Manari
Make A Difference
www.makeadiff.in | www.makeadiff.in/blog
Find us on Facebook: www.facebook.com/makeadiff
',
            ),
            2 => 
            array (
                'id' => 4,
                'name' => 'new_registration_notification',
                'value' => '',
                'data' => 'Hi Shilpa,

We have a new registration on MadApp. Details...

Name: %NAME%
City: %CITY%
Phone: %PHONE%
Email: %EMAIL%

--
MADBot',
            ),
            3 => 
            array (
                'id' => 5,
                'name' => 'temp',
                'value' => '',
                'data' => 'From 9873031617 at 1547063810556:
bangalore:DIVYANSHU:DIVYANSHU:DIVYANSHUSHUKL@GMAIL.COM
User exists in Database.
stdClass Object
(
[id] => 52694
[name] => Divyanshu shukla
[phone] => 7844919304
[email] => divyanshushukl@gmail.com
[city_id] => 20
[status] => 1
[user_type] => applicant
)
',
            ),
            4 => 
            array (
                'id' => 6,
                'name' => 'registeration_debug_info',
                'value' => '',
                'data' => 'Array
(
)
INSERT INTO `User` (`name`, `email`, `phone`, `address`, `sex`, `city_id`, `job_status`, `birthday`, `why_mad`, `source`, `user_type`, `status`, `password`, `joined_on`, `project_id`) VALUES (\'Manojkumar\', \'manojkumarnov23@gmail.com\', \'9952318334\', \'S,sowndharyam block,Shankar abodes,10 A Vasudevan street,Thiruvanaikoil, Trichy-620005\', \'m\', \'16\', \'student\', \'1997-11-23\', \'To help the people in need like me and to transfer the knowledge I gained from my elders\', \'college\', \'applicant\', \'1\', \'pass\', \'2018-03-24 10:13:25\', 1)Array
(
[name] => Manojkumar
[email] => manojkumarnov23@gmail.com
[phone] => 9952318334
[address] => S,sowndharyam block,Shankar abodes,10 A Vasudevan street,Thiruvanaikoil, Trichy-620005
[sex] => m
[city_id] => 16
[job_status] => student
[birthday] => 1997-11-23
[why_mad] => To help the people in need like me and to transfer the knowledge I gained from my elders
[source] => college
[user_type] => applicant
[status] => 1
[password] => pass
[joined_on] => 2018-03-24 10:13:25
[project_id] => 1
[id] => 155000
)
',
            ),
            5 => 
            array (
                'id' => 7,
                'name' => 'sms_registration_email',
                'value' => '',
                'data' => 'Hi %NAME%,

Thanks for registering with Make A Difference. Can you fill out the rest of your data here...
%LINK%

--
Make A Difference',
            ),
            6 => 
            array (
                'id' => 8,
                'name' => 'registeration_count',
                'value' => '209',
                'data' => '',
            ),
            7 => 
            array (
                'id' => 15,
                'name' => 'credit_lost_for_missing_zero_hour',
                'value' => '0',
                'data' => '',
            ),
            8 => 
            array (
                'id' => 9,
                'name' => 'credit_for_substituting',
                'value' => '1',
                'data' => '',
            ),
            9 => 
            array (
                'id' => 10,
                'name' => 'credit_for_substituting_in_same_level',
                'value' => '1',
                'data' => '',
            ),
            10 => 
            array (
                'id' => 11,
                'name' => 'credit_lost_for_missing_class',
                'value' => '-2',
                'data' => '',
            ),
            11 => 
            array (
                'id' => 12,
                'name' => 'credit_lost_for_getting_substitute',
                'value' => '-1',
                'data' => '',
            ),
            12 => 
            array (
                'id' => 13,
                'name' => 'beginning_credit',
                'value' => '3',
                'data' => '',
            ),
            13 => 
            array (
                'id' => 14,
                'name' => 'credit_lost_for_missing_avm',
                'value' => '0',
                'data' => '',
            ),
            14 => 
            array (
                'id' => 16,
                'name' => 'max_credit_threshold',
                'value' => '7',
                'data' => '',
            ),
            15 => 
            array (
                'id' => 17,
                'name' => 'credit_for_concurrent_classes',
                'value' => '1',
                'data' => '',
            ),
            16 => 
            array (
                'id' => 18,
                'name' => 'concurrent_classes_for_credit_count',
                'value' => '6',
                'data' => '',
            ),
            17 => 
            array (
                'id' => 19,
                'name' => 'fp_beginning_credit',
                'value' => '3',
                'data' => '',
            ),
            18 => 
            array (
                'id' => 20,
                'name' => 'fp_max_credit_threshold',
                'value' => '5',
                'data' => '',
            ),
            19 => 
            array (
                'id' => 21,
                'name' => 'fp_credit_lost_for_missing_zero_hour',
                'value' => '-0.5',
                'data' => '',
            ),
            20 => 
            array (
                'id' => 22,
                'name' => 'fp_credit_lost_for_getting_substitute',
                'value' => '-1',
                'data' => '',
            ),
            21 => 
            array (
                'id' => 23,
                'name' => 'fp_credit_lost_for_missing_class',
                'value' => '-1.5',
                'data' => '',
            ),
            22 => 
            array (
                'id' => 24,
                'name' => 'fp_credit_for_substituting',
                'value' => '1',
                'data' => '',
            ),
            23 => 
            array (
                'id' => 25,
                'name' => 'ed_credit_lost_for_missing_zero_hour',
                'value' => '-0.5',
                'data' => '',
            ),
            24 => 
            array (
                'id' => 26,
                'name' => 'ed_credit_for_substituting',
                'value' => '1',
                'data' => '',
            ),
            25 => 
            array (
                'id' => 27,
                'name' => 'ed_credit_lost_for_missing_class',
                'value' => '-1.5',
                'data' => '',
            ),
            26 => 
            array (
                'id' => 28,
                'name' => 'ed_credit_lost_for_getting_substitute',
                'value' => '-1',
                'data' => '',
            ),
            27 => 
            array (
                'id' => 29,
                'name' => 'ed_beginning_credit',
                'value' => '3',
                'data' => '',
            ),
            28 => 
            array (
                'id' => 30,
                'name' => 'ed_credit_lost_for_missing_avm',
                'value' => '0',
                'data' => '',
            ),
            29 => 
            array (
                'id' => 31,
                'name' => 'ed_max_credit_threshold',
                'value' => '5',
                'data' => '',
            ),
            30 => 
            array (
                'id' => 50,
                'name' => 'ac_credit_lost_for_missing_zero_hour',
                'value' => '0',
                'data' => '',
            ),
            31 => 
            array (
                'id' => 33,
                'name' => 'ac_credit_for_substituting',
                'value' => '1',
                'data' => '',
            ),
            32 => 
            array (
                'id' => 34,
                'name' => 'ac_credit_lost_for_missing_class',
                'value' => '-2',
                'data' => '',
            ),
            33 => 
            array (
                'id' => 35,
                'name' => 'ac_credit_lost_for_getting_substitute',
                'value' => '-1',
                'data' => '',
            ),
            34 => 
            array (
                'id' => 36,
                'name' => 'ac_beginning_credit',
                'value' => '3',
                'data' => '',
            ),
            35 => 
            array (
                'id' => 37,
                'name' => 'ac_max_credit_threshold',
                'value' => '7',
                'data' => '',
            ),
            36 => 
            array (
                'id' => 38,
                'name' => 'tr_wingman_credit_lost_for_missing_zero_hour',
                'value' => '0',
                'data' => '',
            ),
            37 => 
            array (
                'id' => 39,
                'name' => 'tr_wingman_credit_for_substituting',
                'value' => '1',
                'data' => '',
            ),
            38 => 
            array (
                'id' => 40,
                'name' => 'tr_wingman_credit_lost_for_missing_class',
                'value' => '-2',
                'data' => '',
            ),
            39 => 
            array (
                'id' => 41,
                'name' => 'tr_wingman_credit_lost_for_getting_substitute',
                'value' => '-1',
                'data' => '',
            ),
            40 => 
            array (
                'id' => 42,
                'name' => 'tr_wingman_beginning_credit',
                'value' => '3',
                'data' => '',
            ),
            41 => 
            array (
                'id' => 43,
                'name' => 'tr_wingman_max_credit_threshold',
                'value' => '7',
                'data' => '',
            ),
            42 => 
            array (
                'id' => 44,
                'name' => 'tr_asv_credit_lost_for_missing_zero_hour',
                'value' => '0',
                'data' => '',
            ),
            43 => 
            array (
                'id' => 45,
                'name' => 'tr_asv_credit_for_substituting',
                'value' => '1',
                'data' => '',
            ),
            44 => 
            array (
                'id' => 46,
                'name' => 'tr_asv_credit_lost_for_missing_class',
                'value' => '-2',
                'data' => '',
            ),
            45 => 
            array (
                'id' => 47,
                'name' => 'tr_asv_credit_lost_for_getting_substitute',
                'value' => '-1',
                'data' => '',
            ),
            46 => 
            array (
                'id' => 48,
                'name' => 'tr_asv_beginning_credit',
                'value' => '3',
                'data' => '',
            ),
            47 => 
            array (
                'id' => 49,
                'name' => 'tr_asv_max_credit_threshold',
                'value' => '7',
                'data' => '',
            ),
        ));
        
        
    }
}