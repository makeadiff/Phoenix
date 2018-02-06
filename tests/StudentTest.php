<?php

/**
 * @runTestsInSeparateProcesses
 */
class StudentTest extends TestCase
{
    // private $only_priority_tests = false;
    // private $write_to_db = true;

    /// Path: GET    /students
    public function testGetStudentsList()
    {
        if($this->only_priority_tests) $this->markTestSkipped("Running only priority tests.");

        $this->load('/students?center_id=220');
        $data = json_decode($this->response->getContent());

        $this->assertEquals($data->status, 'success');
        $search_for = 'Yoda';
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

    /// Path: GET    /students/{student_id}
    public function testGetStudentsSingle()
    {
        if($this->only_priority_tests) $this->markTestSkipped("Running only priority tests.");

        $this->load('/students/21932');
        $data = json_decode($this->response->getContent());

        $this->assertEquals($data->status, 'success');
        $this->assertEquals($data->data->student->name, 'Yoda');
        $this->assertEquals(200, $this->response->status());
    }
}
