<?php
namespace Tests\Feature;

use Tests\TestCase;

/**
 * @runTestsInSeparateProcesses
 */
class BatchTest extends TestCase
{
    // private $only_priority_tests = false;
    // private $write_to_db = true;

    /// Path: GET    /batches/{batch_id}
    public function testGetBatchesSingle()
    {
        if($this->only_priority_tests) $this->markTestSkipped("Running only priority tests.");

        $this->load('/batches/1971');
        $data = json_decode($this->response->getContent());

        $this->assertEquals($data->status, 'success');
        $this->assertEquals($data->data->batches->name, 'Sunday 12:00 AM');
        $this->response->assertStatus(200);
    }

    /// Path: GET    /batches/{batch_id}/teachers
    public function testGetBatchesTeachersList()
    {
        if($this->only_priority_tests) $this->markTestSkipped("Running only priority tests.");

        $this->load('/batches/1973/teachers');
        $data = json_decode($this->response->getContent());

        $this->assertEquals($data->status, 'success');
        $search_for = 'Data';
        $found = false;
        foreach ($data->data->teachers as $key => $info) {
            if($info->name == $search_for) {
                $found = true;
                break;
            }
        }
        $this->assertTrue($found);
        $this->response->assertStatus(200);
    }

    /// Path: GET    /batches/{batch_id}/levels
    public function testGetBatchesLevelsList()
    {
        if($this->only_priority_tests) $this->markTestSkipped("Running only priority tests.");

        $this->load('/batches/2608/levels');
        $data = json_decode($this->response->getContent());

        $this->assertEquals($data->status, 'success');
        $search_for = '7 A';
        $found = false;
        foreach ($data->data->levels as $key => $info) {
            if($info->name == $search_for) {
                $found = true;
                break;
            }
        }
        $this->assertTrue($found);
        $this->response->assertStatus(200);
    }
}
