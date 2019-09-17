<?php
namespace App\Models;

use App\Models\Common;

// NOT USED YET. Sample code
final class Credit extends Common
{
    protected $table = 'UserCredit';
    public $timestamps = false;

    protected $rules = [
        'ed'    => [
            'class' => [
                'miss_zero_hour'    => -0.5,
                'substitute'        => 1,
                'absent_with_substitute'    => -1,
                'absent_without_substitute' => -2
            ],
            'event' => [
                // 13 is 'event_type_id' - also, 13 is a sample. Not actual.
                13 => [
                    'absent_rsvped_no'  => -0.5,
                    'absent_rsvped_yes' => -1,
                    'present'           => 1
                ]
            ]
        ],
        /*
        'ac'    => [
            'event' => [
                aftercare_circle
            ]
        ]
         */
        // 'fr'    => [
        //     'event' => [
        //     ]
        // ]
    ];

    public function calculate($situation, $user_id, $situation_id)
    {
        // $situation - event,class
        // $situation_id - Class.id, Event.id
    }
}
