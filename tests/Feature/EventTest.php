<?php
namespace Tests\Feature;

use Tests\TestCase;

/**
 * @runTestsInSeparateProcesses
 */
class EventTest extends TestCase
{
    // protected $only_priority_tests = false;
    // protected $write_to_db = true;

    /// Path: GET    /events
    public function testGetEventsList()
    {
        if ($this->only_priority_tests) {
            $this->markTestSkipped("Running only priority tests.");
        }

        $this->load('/events?name=Test');

        $this->assertEquals($this->response_data->status, 'success');
        $search_for = 'Volunteer Social Test'; // This will get deleted at some point. Plan for it.
        $found = false;
        foreach ($this->response_data->data->events as $key => $info) {
            if ($info->name == $search_for) {
                $found = true;
                break;
            }
        }
        $this->assertTrue($found);
        $this->assertEquals($this->response->getStatusCode(), 200);
    }

    /// Path: GET    /events/{event_id}
    public function testGetEventsSingle()
    {
        if ($this->only_priority_tests) {
            $this->markTestSkipped("Running only priority tests.");
        }

        $this->load('/events/2069');

        $this->assertEquals($this->response_data->status, 'success');
        $this->assertEquals($this->response_data->data->event->name, 'Test');
        $this->assertEquals($this->response->getStatusCode(), 200);
    }

    /// Path: GET    /events/{event_id}/users
    public function testGetEventsUsersList()
    {
        if ($this->only_priority_tests) {
            $this->markTestSkipped("Running only priority tests.");
        }

        $this->load('/events/2330/users');

        $this->assertEquals($this->response_data->status, 'success');
        $search_for = 'Abhijith Gopakumar';
        $found = false;
        foreach ($this->response_data->data->users as $key => $info) {
            if ($info->name == $search_for) {
                $found = true;
                break;
            }
        }
        $this->assertTrue($found);
        $this->assertEquals($this->response->getStatusCode(), 200);
    }

    /// Path: GET    /events/{event_id}/users
    public function testGetEventUsersAbsentList()
    {
        if ($this->only_priority_tests) {
            $this->markTestSkipped("Running only priority tests.");
        }

        $this->load('/events/2328/users?present=0');

        $this->assertEquals($this->response_data->status, 'success');
        $search_for = 'Adarsh Jeyes';
        $found = false;
        foreach ($this->response_data->data->users as $key => $info) {
            if ($info->name == $search_for) {
                $found = true;
                break;
            }
        }
        $this->assertTrue($found);
        $this->assertEquals($this->response->getStatusCode(), 200);
    }

    /// Path: GET    /events/{event_id}/users
    public function testGetEventsUsersPresentList()
    {
        if ($this->only_priority_tests) {
            $this->markTestSkipped("Running only priority tests.");
        }

        $this->load('/events/2328/users?present=1');

        $this->assertEquals($this->response_data->status, 'success');
        $search_for = 'Devyani Mahadevan';
        $found = false;
        foreach ($this->response_data->data->users as $key => $info) {
            if ($info->name == $search_for) {
                $found = true;
                break;
            }
        }
        $this->assertTrue($found);
        $this->assertEquals($this->response->getStatusCode(), 200);
    }

    /// Path: GET    /events/{event_id}/users/{user_id}
    public function testGetEventUsersSingle()
    {
        if ($this->only_priority_tests) {
            $this->markTestSkipped("Running only priority tests.");
        }

        $this->load('/events/2330/users/70351');

        $this->assertEquals($this->response_data->status, 'success');
        $this->assertEquals($this->response_data->data->user->name, 'Shubhrav Phate');
        $this->assertEquals($this->response->getStatusCode(), 200);
    }

    // :TODO: Create, Edit events, mark attendance. Basically all POST, DELETE calls have to be tested.
}
