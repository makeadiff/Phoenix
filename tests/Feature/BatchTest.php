<?php
namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use App\Models\Batch;

/**
 * @runTestsInSeparateProcesses
 */
class BatchTest extends TestCase
{
    use WithoutMiddleware;

    // protected $only_priority_tests = true;
    // protected $write_to_db = false;

    /// Path: GET    /batches/{batch_id}
    public function testGetBatchesSingle()
    {
        if ($this->only_priority_tests) {
            $this->markTestSkipped("Running only priority tests.");
        }

        $this->load('/batches/1971');
        $data = json_decode($this->response->getContent());

        $this->assertEquals($data->status, 'success');
        $this->assertEquals($data->data->batches->name, 'Sunday 12:00 AM');
        $this->response->assertStatus(200);
    }

    /// Path: POST    /batches
    public function testCreateBatch()
    {
        if ($this->only_priority_tests) {
            $this->markTestSkipped("Running only priority tests.");
        }
        if (!$this->write_to_db) {
            $this->markTestSkipped("Skipping as this test writes to the Database.");
        }

        $this->load('/batches','POST', [
            'day'       => '0',
            'class_time'=> '15:00:13',
            'project_id'=> '1',
            'center_id' => '244'
        ]);
        $data = json_decode($this->response->getContent());

        $this->assertEquals($data->status, 'success');
        $created_batch_id = $data->data->batch->id;
        $this->assertEquals($data->data->batch->day, '0');
        $this->assertEquals($data->data->batch->year, $this->year);
        $this->response->assertStatus(200);

        return $created_batch_id;
    }

    /// Path: POST    /batches/{batch_id}
    /**
     * @depends testCreateBatch
     */
    public function testEditBatch($created_batch_id)
    {
        if ($this->only_priority_tests) {
            $this->markTestSkipped("Running only priority tests.");
        }
        if (!$this->write_to_db) {
            $this->markTestSkipped("Skipping as this test writes to the Database.");
        }

        $this->load('/batches/2608','POST', [
            'day'       => '1',
            'class_time'=> '15:00:13'
        ]);
        $data = json_decode($this->response->getContent());

        $this->assertEquals($data->status, 'success');
        $this->assertEquals($data->data->batch->day, '1');
        $this->assertEquals($data->data->batch->year, $this->year);
        $this->response->assertStatus(200);
    }

    /// Path: DELETE    /batches/{batch_id}
    /**
     * @depends testCreateBatch
     */
    public function testDeleteBatch($created_batch_id)
    {
        if ($this->only_priority_tests) {
            $this->markTestSkipped("Running only priority tests.");
        }
        if (!$this->write_to_db) {
            $this->markTestSkipped("Skipping as this test writes to the Database.");
        }
        if(!$created_batch_id) $this->markTestSkipped("Can't find ID of batch created as test.");

        $this->load('/batches/' . $created_batch_id, 'DELETE');
        $this->response->assertStatus(200);

        $batch_model = new Batch;
        $batch_info = $batch_model->find($created_batch_id);
        $this->assertEquals($batch_info->status, '0'); // Its actually deleted.
    }   

    /// Path: GET    /batches/{batch_id}/teachers
    public function testGetBatchesTeachersList()
    {
        if ($this->only_priority_tests) {
            $this->markTestSkipped("Running only priority tests.");
        }

        $this->load('/batches/1973/teachers');
        $data = json_decode($this->response->getContent());

        $this->assertEquals($data->status, 'success');
        $search_for = 'Data';
        $found = false;
        foreach ($data->data->teachers as $key => $info) {
            if ($info->name == $search_for) {
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
        if ($this->only_priority_tests) {
            $this->markTestSkipped("Running only priority tests.");
        }

        $this->load('/batches/2608/levels');
        print $this->response->getContent();
        $data = json_decode($this->response->getContent());

        $this->assertEquals($data->status, 'success');
        $search_for = '7 A';
        $found = false;
        foreach ($data->data->levels as $key => $info) {
            if ($info->name == $search_for) {
                $found = true;
                break;
            }
        }
        $this->assertTrue($found);
        $this->response->assertStatus(200);
    }
}
