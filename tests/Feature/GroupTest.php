<?php
namespace Tests\Feature;

use Tests\TestCase;

/**
 * @runTestsInSeparateProcesses
 */
class GroupTest extends TestCase
{
    // private $only_priority_tests = false;
    // private $write_to_db = true;

    /// Path: GET    /groups
    public function testGetGroupsList()
    {
        if ($this->only_priority_tests) {
            $this->markTestSkipped("Running only priority tests.");
        }

        $this->load('/groups');

        $this->assertEquals($this->response_data->status, 'success');
        $search_for = 'ES Mentors';
        $found = false;
        foreach ($this->response_data->data->groups as $key => $info) {
            if ($info->name == $search_for and $info->id == 8) {
                $found = true;
                break;
            }
        }
        $this->assertTrue($found);
        $this->assertEquals($this->response->getStatusCode(), 200);
    }

    /// Path: GET    /groups
    public function testGetGroupsSearchList()
    {
        if ($this->only_priority_tests) {
            $this->markTestSkipped("Running only priority tests.");
        }

        $this->load('/groups?vertical_id=8');

        $this->assertEquals($this->response_data->status, 'success');
        $search_for = 'Human Capital Fellow';
        $found = false;
        foreach ($this->response_data->data->groups as $key => $info) {
            if ($info->name == $search_for) {
                $found = true;
                break;
            }
        }
        $this->assertTrue($found, "Couldn't find '$search_for' in the groups.");
        $this->assertEquals(count($this->response_data->data->groups), 4);
        $this->assertEquals($this->response->getStatusCode(), 200);
    }

    /// Path: GET    /groups/{group_id}
    public function testGetGroupsSingle()
    {
        if ($this->only_priority_tests) {
            $this->markTestSkipped("Running only priority tests.");
        }

        $this->load('/groups/9');

        $this->assertEquals($this->response_data->status, 'success');
        $this->assertEquals($this->response_data->data->groups->name, 'ES Volunteer');
        $this->assertEquals($this->response->getStatusCode(), 200);
    }
}
