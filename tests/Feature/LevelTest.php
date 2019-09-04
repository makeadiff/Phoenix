<?php
namespace Tests\Feature;

use Tests\TestCase;

/**
 * @runTestsInSeparateProcesses
 */
class LevelTest extends TestCase
{
    // private $only_priority_tests = true;
    // private $write_to_db = true;

    /// Path: GET    /levels/{level_id}
    public function testGetLevelsSingle()
    {
        if($this->only_priority_tests) $this->markTestSkipped("Running only priority tests.");

        $this->load('/levels/4852');
        $data = json_decode($this->response->getContent());

        $this->assertEquals($data->status, 'success');
        $this->assertEquals($data->data->levels->name, '6 A');
        $this->response->assertStatus(200);
    }

    /// Path: GET    /levels/{level_id}/students
    public function testGetLevelsStudentsList()
    {
        if($this->only_priority_tests) $this->markTestSkipped("Running only priority tests.");

        $this->load('/levels/4852/students');
        $data = json_decode($this->response->getContent());

        $this->assertEquals($data->status, 'success');
        $search_for = 'Jar Jar';
        $found = false;
        foreach ($data->data->students as $key => $info) {
            if($info->name == $search_for) {
                $found = true;
                break;
            }
        }
        $this->assertTrue($found);
        $this->response->assertStatus(200);
    }

    /// Path: GET    /levels/{level_id}/batches
    public function testGetLevelsBatchesList()
    {
        if($this->only_priority_tests) $this->markTestSkipped("Running only priority tests.");

        $this->load('/levels/7355/batches');
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
        $this->response->assertStatus(200);
    }


}
