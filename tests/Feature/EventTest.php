<?php
namespace Tests\Feature;

use Tests\TestCase;

/**
 * @runTestsInSeparateProcesses
 */
class EventTest extends TestCase
{
    /// Path: GET    /events 
    public function testGetEventsList()
    {
        // if($this->only_priority_tests) $this->markTestSkipped("Running only priority tests.");

        $this->load('/events?name=Test');
        $data = json_decode($this->response->getContent());

        $this->assertEquals($data->status, 'success');
        $search_for = 'test sid and mo'; // This will get deleted at some point. Plan for it.
        $found = false;
        foreach ($data->data->events as $key => $info) {
            if($info->name == $search_for) {
                $found = true;
                break;
            }
        }
        $this->assertTrue($found);
        $this->response->assertStatus(200);
    }

    /// Path: GET    /events/{event_id}
    public function testGetEventsSingle()
    {
        // if($this->only_priority_tests) $this->markTestSkipped("Running only priority tests.");

        $this->load('/events/2069');
        $data = json_decode($this->response->getContent());

        $this->assertEquals($data->status, 'success');
        $this->assertEquals($data->data->event->name, 'Test');
        $this->response->assertStatus(200);
    }

    /// Path: GET    /events/{event_id}/users
    public function testGetEventsUsersList()
    {
        // if($this->only_priority_tests) $this->markTestSkipped("Running only priority tests.");

        $this->load('/events/2330/users');
        $data = json_decode($this->response->getContent());

        $this->assertEquals($data->status, 'success');
        $search_for = 'Abhijith Gopakumar';
        $found = false;
        foreach ($data->data->users as $key => $info) {
            if($info->name == $search_for) {
                $found = true;
                break;
            }
        }
        $this->assertTrue($found);
        $this->response->assertStatus(200);
    }

    /// Path: GET    /events/{event_id}/users
    public function testGetEventUsersAbsentList()
    {
        // if($this->only_priority_tests) $this->markTestSkipped("Running only priority tests.");

        $this->load('/events/2328/users?present=0');
        $data = json_decode($this->response->getContent());

        $this->assertEquals($data->status, 'success');
        $search_for = 'Adarsh Jeyes';
        $found = false;
        foreach ($data->data->users as $key => $info) {
            if($info->name == $search_for) {
                $found = true;
                break;
            }
        }
        $this->assertTrue($found);
        $this->response->assertStatus(200);
    }

    /// Path: GET    /events/{event_id}/users
    public function testGetEventsUsersPresentList()
    {
        // if($this->only_priority_tests) $this->markTestSkipped("Running only priority tests.");

        $this->load('/events/2328/users?present=1');
        $data = json_decode($this->response->getContent());

        $this->assertEquals($data->status, 'success');
        $search_for = 'Devyani Mahadevan';
        $found = false;
        foreach ($data->data->users as $key => $info) {
            if($info->name == $search_for) {
                $found = true;
                break;
            }
        }
        $this->assertTrue($found);
        $this->response->assertStatus(200);
    }

    /// Path: GET    /events/{event_id}/users/{user_id}
    public function testGetEventUsersSingle()
    {
        // if($this->only_priority_tests) $this->markTestSkipped("Running only priority tests.");

        $this->load('/events/2330/users/70351');
        $data = json_decode($this->response->getContent());

        $this->assertEquals($data->status, 'success');
        $this->assertEquals($data->data->user->name, 'Shubhrav Phate');
        $this->response->assertStatus(200);
    }


}
