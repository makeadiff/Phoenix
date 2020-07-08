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
    public $user_id = 1;

    public function testEventCredit()
    {
        $user_model = new User;
        $attender_credit = $user_model->find($this->user_id)->credit;

        $event_model = new Event;
        $event_model->updateUserConnection($this->user_id, ['present' => 3, 'rsvp' => 'no_data'], $this->event_id);

        $para_credit_lost_by_missing_ac_circle_without_informing = 11;
        $credit_model = new Credit;
        $credit_data = $credit_model->search([
                'user_id' => $this->user_id, 
                'parameter_id' => $para_credit_lost_by_missing_ac_circle_without_informing, 
                'item' => 'Event', 
                'item_id' => $this->event_id
            ]);
        $this->assertEquals($credit_data[count($credit_data) - 1]->change, -2);

        $this->assertEquals($attender_credit - 2, $user_model->find($this->user_id)->credit); // For teacher
    }

    public function testEventEdit()
    {
        $user_model = new User;
        $attender_credit = $user_model->find($this->user_id)->credit;

        $event_model = new Event;
        $event_model->updateUserConnection($this->user_id, ['present' => 3, 'rsvp' => 'cant_go'], $this->event_id);

        $para_credit_lost_by_missing_ac_circle_after_informing = 10;
        $credit_model = new Credit;
        $credit_data = $credit_model->search([
                'user_id' => $this->user_id, 
                'parameter_id' => $para_credit_lost_by_missing_ac_circle_after_informing, 
                'item' => 'Event', 
                'item_id' => $this->event_id
            ]);
        $this->assertEquals($credit_data[count($credit_data) - 1]->change, -1);

        $this->assertEquals($attender_credit + 1, $user_model->find($this->user_id)->credit); // Yes - the credit goes UP
    }
}
