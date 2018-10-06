<?php

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
        if($this->only_priority_tests) $this->markTestSkipped("Running only priority tests.");

        $this->load('/groups');
        $data = json_decode($this->response->getContent());

        $this->assertEquals($data->status, 'success');
        $search_for = 'ES Mentors';
        $found = false;
        foreach ($data->data->groups as $key => $info) {
            if($info->name == $search_for and $info->id == 8) {
                $found = true;
                break;
            }
        }
        $this->assertTrue($found);
        $this->assertEquals(200, $this->response->status());
    }

    /// Path: GET    /groups
    public function testGetGroupsSearchList()
    {
        if($this->only_priority_tests) $this->markTestSkipped("Running only priority tests.");

        $this->load('/groups?vertical_id=8');
        $data = json_decode($this->response->getContent());

        $this->assertEquals($data->status, 'success');
        $search_for = 'Human Capital Fellow';
        $found = false;
        foreach ($data->data->groups as $key => $info) {
            if($info->name == $search_for) {
                $found = true;
                break;
            }
        }
        $this->assertTrue($found, "Couldn't find '$search_for' in the groups.");
        $this->assertEquals(count($data->data->groups), 5);
        $this->assertEquals(200, $this->response->status());
    }

    /// Path: GET    /groups/{group_id}
    public function testGetGroupsSingle()
    {
        if($this->only_priority_tests) $this->markTestSkipped("Running only priority tests.");

        $this->load('/groups/9');
        $data = json_decode($this->response->getContent());

        $this->assertEquals($data->status, 'success');
        $this->assertEquals($data->data->group->name, 'ES Volunteer');
        $this->assertEquals(200, $this->response->status());
    }

}
