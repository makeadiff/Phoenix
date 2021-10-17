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

    /// Path: POST    /students
    public function testPostStudent()
    {
        if ($this->only_priority_tests) {
            $this->markTestSkipped("Running only priority tests.");
        }
        if (!$this->write_to_db) {
            $this->markTestSkipped("Skipping as this test writes to the Database.");
        }

        $number = rand(0, 9999);
        $uniquer = str_pad($number, 4, 0, STR_PAD_LEFT);
        $name = 'Test Student ' . $uniquer;

        $student = [
            'name'  => $name,
            'center_id'  => $this->ideal_center_id,
            'sex'   => 'm',
            'student_type' => 'active'
        ];

        $this->load('/students', 'POST', $student);

        $this->assertEquals($this->response_data->status, 'success');
        $this->assertEquals($this->response_data->data->student->name, $name);
        $this->assertEquals($this->response->getStatusCode(), 200);
        $this->assertDatabaseHas('Student', array('name' => $name));

        $created_student_id = $this->response_data->data->student->id;
        return $created_student_id;
    }

    /// Path: DELETE    /students/{student_id}
    /**
     * @depends testPostStudent
     */
    public function testPostStudentEdit($created_student_id)
    {
        if ($this->only_priority_tests) {
            $this->markTestSkipped("Running only priority tests.");
        }
        if (!$this->write_to_db) {
            $this->markTestSkipped("Skipping as this test writes to the Database.");
        }
        $student = [
            'name'      => 'New Student Name',
        ];
        $this->load('/students/' . $created_student_id, 'POST', $student);
        // dd($this->response_data);

        $this->assertEquals($this->response_data->status, 'success');
        $this->assertEquals($this->response->getStatusCode(), 200);
        $this->assertDatabaseHas('Student', [
            'id'    => $created_student_id,
            'name'  => $student['name'],
        ]);
    }

    /// Path: DELETE    /students/{student_id}
    /**
     * @depends testPostStudent
     */
    public function testDeletestudent($created_student_id)
    {
        if ($this->only_priority_tests) {
            $this->markTestSkipped("Running only priority tests.");
        }
        if (!$this->write_to_db) {
            $this->markTestSkipped("Skipping as this test writes to the Database.");
        }

        $this->load('/students/' . $created_student_id, 'DELETE');
        $this->assertEquals($this->response->getStatusCode(), 200);
        $this->assertDatabaseHas('Student', array('id' => $created_student_id, 'status' => '0'));
    }
}
