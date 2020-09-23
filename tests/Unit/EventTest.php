<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use App\Models\Event;
use App\Models\User;
use App\Models\Credit;

/**
 * @runInSeparateProcess
 */
class EventTest extends TestCase
{
    use WithoutMiddleware;
    public $event_id = 5009;

    // public function testEventCredit()
    // {
    //     $user_model = new User;
    //     $attender_credit = $user_model->find($this->ideal_user_id)->credit;

    //     $event_model = new Event;
    //     $event_model->updateUserConnection($this->ideal_user_id, ['present' => 3, 'rsvp' => 'no_data'], $this->event_id);

    //     $para_credit_lost_by_missing_ac_circle_without_informing = 11;
    //     $credit_model = new Credit;
    //     $credit_data = $credit_model->search([
    //             'user_id' => $this->ideal_user_id,
    //             'parameter_id' => $para_credit_lost_by_missing_ac_circle_without_informing,
    //             'item' => 'Event',
    //             'item_id' => $this->event_id
    //         ]);
    //     $this->assertEquals($credit_data[count($credit_data) - 1]->change, -2);

    //     $this->assertEquals($attender_credit - 2, $user_model->find($this->ideal_user_id)->credit); // For teacher
    // }

    // public function testEventEdit()
    // {
    //     $user_model = new User;
    //     $attender_credit = $user_model->find($this->ideal_user_id)->credit;

    //     $event_model = new Event;
    //     $event_model->updateUserConnection($this->ideal_user_id, ['present' => 3, 'rsvp' => 'cant_go'], $this->event_id);

    //     $para_credit_lost_by_missing_ac_circle_after_informing = 10;
    //     $credit_model = new Credit;
    //     $credit_data = $credit_model->search([
    //             'user_id' => $this->ideal_user_id,
    //             'parameter_id' => $para_credit_lost_by_missing_ac_circle_after_informing,
    //             'item' => 'Event',
    //             'item_id' => $this->event_id
    //         ]);
    //     $this->assertEquals($credit_data[count($credit_data) - 1]->change, -1);

    //     $this->assertEquals($attender_credit + 1, $user_model->find($this->ideal_user_id)->credit); // Yes - the credit goes UP
    // }

    public function testEventRecurring()
    {
        $event_model = new Event;
        $until_date = '2020-12-31';
        $frequency = 'weekly';

        $org_event = $event_model->find($this->event_id);
        $org_event_copy = clone $org_event; // Copy because the values were getting muted in the createReccuringInstances call.
        $instance_ids = $event_model->createRecurringInstances($org_event_copy, $frequency, $until_date);
 
        $this->assertNotFalse($instance_ids);

        // Make sure that the orginal event was modified correctly.
        $this->assertEquals($org_event_copy->frequency, $frequency);

        // Check if x new events created. Calculate the number of weeks between two dates
        $from_date = new \DateTime($org_event->starts_on);
        $to_date = new \DateTime($until_date);
        $difference_in_weeks = floor($from_date->diff($to_date)->days / 7);
        $this->assertEquals($difference_in_weeks, count($instance_ids));

        //  Check in field values of new events are correct.
        $random_instance_id = $instance_ids[array_rand($instance_ids)];
        $instance = $event_model->find($random_instance_id);
        $this->assertEquals($difference_in_weeks, count($instance_ids));

        //  Check if invited user list is correct.
        $this->assertEquals($instance->users()->pluck('id')->all(), $org_event->users()->pluck('id')->all());

        // Make sure events after End date is NOT created. :TODO:
    }
}
