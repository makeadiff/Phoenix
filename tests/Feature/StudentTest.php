<?php
namespace Tests\Feature;

use Tests\TestCase;

/**
 * @runTestsInSeparateProcesses
 */
class StudentTest extends TestCase
{
    // protected $only_priority_tests = false;
    // protected $write_to_db = true;

    /// Path: GET    /students
    public function testGetStudentsList()
    {
        if ($this->only_priority_tests) {
            $this->markTestSkipped("Running only priority tests.");
        }

        $this->load('/students?center_id=220');

        $this->assertEquals($this->response_data->status, 'success');
        $search_for = 'Yoda';
        $found = false;
        foreach ($this->response_data->data->students as $key => $info) {
            if ($info->name == $search_for) {
                $found = true;
                break;
            }
        }
        $this->assertTrue($found);
        $this->assertEquals($this->response->getStatusCode(), 200);
    }

    /// Path: GET    /students
    public function testGetActiveStudentsList()
    {
        if ($this->only_priority_tests) {
            $this->markTestSkipped("Running only priority tests.");
        }

        $this->load('/students?center_id=220&student_type=active');

        $this->assertEquals($this->response_data->status, 'success');
        $search_for = 'Yoda';
        $found = false;
        foreach ($this->response_data->data->students as $key => $info) {
            if ($info->name == $search_for) {
                $found = true;
                break;
            }
        }
        $this->assertTrue($found);
        $this->assertEquals($this->response->getStatusCode(), 200);
    }

    /// Path: GET    /students/{student_id}
    public function testGetStudentsSingle()
    {
        if ($this->only_priority_tests) {
            $this->markTestSkipped("Running only priority tests.");
        }

        $this->load('/students/21932');

        $this->assertEquals($this->response_data->status, 'success');
        $this->assertEquals($this->response_data->data->student->name, 'Yoda');
        $this->assertEquals($this->response->getStatusCode(), 200);
    }
}
