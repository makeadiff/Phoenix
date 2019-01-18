<?php

/**
 * @runTestsInSeparateProcesses
 */
class CenterTest extends TestCase
{
    // private $only_priority_tests = false;
    // private $write_to_db = true;

    /// Path: GET    /centers
    public function testGetCentersList()
    {
        if($this->only_priority_tests) $this->markTestSkipped("Running only priority tests.");

        $this->load('/centers?city_id=1');
        $data = json_decode($this->response->getContent());

        $this->assertEquals($data->status, 'success');
        $search_for = 'Ashadeep';
        $found = false;
        foreach ($data->data->centers as $key => $info) {
            if($info->name == $search_for) {
                $found = true;
                break;
            }
        }
        $this->assertTrue($found);
        $this->assertEquals(count($data->data->centers), 8);
        $this->assertEquals(200, $this->response->status());
    }

    /// Path: GET    /centers/{center_id}
    public function testGetCentersSingle()
    {
        if($this->only_priority_tests) $this->markTestSkipped("Running only priority tests.");

        $this->load('/centers/220');
        $data = json_decode($this->response->getContent());

        $this->assertEquals($data->status, 'success');
        $this->assertEquals($data->data->centers->name, 'Start Rek');
        $this->assertEquals(200, $this->response->status());
    }

    /// Path: GET    /centers/{center_id}/teachers
    public function testGetCentersTeachersList()
    {
        if($this->only_priority_tests) $this->markTestSkipped("Running only priority tests.");

        $this->load('/centers/220/teachers');
        $data = json_decode($this->response->getContent());

        $this->assertEquals($data->status, 'success');
        $search_for = 'Forge';
        $found = false;

        foreach ($data->data->users as $key => $info) {
            if($info->name == $search_for) {
                $found = true;
                break;
            }
        }
        $this->assertTrue($found);
        $this->assertEquals(200, $this->response->status());
    }

    /// Path: GET    /centers/{center_id}/students
    public function testGetCentersStudentsList()
    {
        if($this->only_priority_tests) $this->markTestSkipped("Running only priority tests.");

        $this->load('/centers/220/students');
        $data = json_decode($this->response->getContent());

        $this->assertEquals($data->status, 'success');
        $search_for = 'Leia';
        $found = false;
        foreach ($data->data->students as $key => $info) {
            if($info->name == $search_for) {
                $found = true;
                break;
            }
        }
        $this->assertTrue($found);
        $this->assertEquals(200, $this->response->status());
    }

    /// Path: GET    /centers/{center_id}/levels
    public function testGetCentersLevelsList()
    {
        if($this->only_priority_tests) $this->markTestSkipped("Running only priority tests.");

        $this->load('/centers/220/levels');
        $data = json_decode($this->response->getContent());

        $this->assertEquals($data->status, 'success');
        $search_for = '6 A';
        $found = false;
        foreach ($data->data->levels as $key => $info) {
            if($info->name == $search_for) {
                $found = true;
                break;
            }
        }
        $this->assertTrue($found);
        $this->assertEquals(200, $this->response->status());
    }

    /// Path: GET    /centers/{center_id}/levels?project_id=2
    public function testGetCentersLevelsInFoundationList()
    {
        if($this->only_priority_tests) $this->markTestSkipped("Running only priority tests.");

        $this->load('/centers/154/levels?project_id=2');
        $data = json_decode($this->response->getContent());

        $this->assertEquals($data->status, 'success');
        $search_for = '10 M';
        $found = false;
        foreach ($data->data->levels as $key => $info) {
            if($info->name == $search_for) {
                $found = true;
                break;
            }
        }
        $this->assertTrue($found);
        $this->assertEquals(200, $this->response->status());
    }

    /// Path: GET    /centers/{center_id}/batches
    public function testGetCentersBatchesList()
    {
        if($this->only_priority_tests) $this->markTestSkipped("Running only priority tests.");

        $this->load('/centers/220/batches');
        $data = json_decode($this->response->getContent());

        $this->assertEquals($data->status, 'success');
        $search_for = 'Saturday 04:00 PM';
        $found = false;
        foreach ($data->data->batches as $key => $info) {
            if($info->name == $search_for) {
                $found = true;
                break;
            }
        }
        $this->assertTrue($found);
        $this->assertEquals(200, $this->response->status());
    }

    /// Path: GET    /centers/{center_id}/batches?project_id=2
    public function testGetCentersBatchesFoundationList()
    {
        if($this->only_priority_tests) $this->markTestSkipped("Running only priority tests.");

        $this->load('/centers/154/batches?project_id=2');
        $data = json_decode($this->response->getContent());

        $this->assertEquals($data->status, 'success');
        $search_for = 'Sunday 04:00 PM';
        $found = false;
        foreach ($data->data->batches as $key => $info) {
            if($info->name == $search_for) {
                $found = true;
                break;
            }
        }
        $this->assertTrue($found);
        $this->assertEquals(200, $this->response->status());
    }
}
