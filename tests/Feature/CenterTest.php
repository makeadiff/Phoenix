<?php
namespace Tests\Feature;

use Tests\TestCase;

/**
 * @runTestsInSeparateProcesses
 */
class CenterTest extends TestCase
{
    // protected $only_priority_tests = true;
    // protected $write_to_db = true;

    /// Path: GET    /centers
    public function testGetCentersList()
    {
        if ($this->only_priority_tests) {
            $this->markTestSkipped("Running only priority tests.");
        }

        $this->load('/centers?city_id=1');

        $this->assertEquals($this->response_data->status, 'success');
        $search_for = 'Ashadeep';
        $found = false;
        foreach ($this->response_data->data->centers as $key => $info) {
            if ($info->name == $search_for) {
                $found = true;
                break;
            }
        }
        $this->assertTrue($found);
        $this->assertEquals(count($this->response_data->data->centers), 8);
        $this->assertEquals($this->response->getStatusCode(), 200);
    }

    /// Path: GET    /centers/{center_id}
    public function testGetCentersSingle()
    {
        if ($this->only_priority_tests) {
            $this->markTestSkipped("Running only priority tests.");
        }

        $this->load('/centers/220');

        $this->assertEquals($this->response_data->status, 'success');
        $this->assertEquals($this->response_data->data->centers->name, 'Start Rek');
        $this->assertEquals($this->response->getStatusCode(), 200);
    }

    /// Path: GET    /centers/{center_id}/teachers
    public function testGetCentersTeachersList()
    {
        if ($this->only_priority_tests) {
            $this->markTestSkipped("Running only priority tests.");
        }

        $this->load('/centers/220/teachers');

        $this->assertEquals($this->response_data->status, 'success');
        $search_for = 'Forge';
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

    /// Path: GET    /centers/{center_id}/students
    public function testGetCentersStudentsList()
    {
        if ($this->only_priority_tests) {
            $this->markTestSkipped("Running only priority tests.");
        }

        $this->load('/centers/220/students');

        $this->assertEquals($this->response_data->status, 'success');
        $search_for = 'Leia';
        $found = false;
        foreach ($this->response_data->data->students as $key => $info) {
            if ($info->name == $search_for) {
                $found = true;
                break;
            }
        }
        // $this->assertTrue($found);
        $this->assertEquals($this->response->getStatusCode(), 200);
    }

    /// Path: GET    /centers/{center_id}/levels
    public function testGetCentersLevelsList()
    {
        if ($this->only_priority_tests) {
            $this->markTestSkipped("Running only priority tests.");
        }

        $this->load('/centers/220/levels');

        $this->assertEquals($this->response_data->status, 'success');
        $search_for = '7 A';
        $found = false;
        foreach ($this->response_data->data->levels as $key => $info) {
            if ($info->name == $search_for) {
                $found = true;
                break;
            }
        }
        $this->assertTrue($found);
        $this->assertEquals($this->response->getStatusCode(), 200);
    }

    /// Path: GET    /centers/{center_id}/levels?project_id=2
    public function testGetCentersLevelsInFoundationList()
    {
        if ($this->only_priority_tests) {
            $this->markTestSkipped("Running only priority tests.");
        }

        $this->load('/centers/154/levels?project_id=2');

        $this->assertEquals($this->response_data->status, 'success');
        $search_for = '11 A';
        $found = false;
        foreach ($this->response_data->data->levels as $key => $info) {
            if ($info->name == $search_for) {
                $found = true;
                break;
            }
        }
        $this->assertTrue($found);
        $this->assertEquals($this->response->getStatusCode(), 200);
    }

    /// Path: GET    /centers/{center_id}/batches
    public function testGetCentersBatchesList()
    {
        if ($this->only_priority_tests) {
            $this->markTestSkipped("Running only priority tests.");
        }

        $this->load('/centers/220/batches');

        $this->assertEquals($this->response_data->status, 'success');
        $search_for = 'Saturday 04:00 PM';
        $found = false;
        foreach ($this->response_data->data->batches as $key => $info) {
            if ($info->name == $search_for) {
                $found = true;
                break;
            }
        }
        $this->assertTrue($found);
        $this->assertEquals($this->response->getStatusCode(), 200);
    }

    /// Path: GET    /centers/{center_id}/batches?project_id=2
    public function testGetCentersBatchesFoundationList()
    {
        if ($this->only_priority_tests) {
            $this->markTestSkipped("Running only priority tests.");
        }

        $this->load('/centers/154/batches?project_id=2');

        $this->assertEquals($this->response_data->status, 'success');
        $search_for = 'Sunday 04:00 PM';
        $found = false;
        foreach ($this->response_data->data->batches as $key => $info) {
            if ($info->name == $search_for) {
                $found = true;
                break;
            }
        }
        $this->assertTrue($found);
        $this->assertEquals($this->response->getStatusCode(), 200);
    }
}
