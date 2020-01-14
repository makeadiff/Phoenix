<?php
namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use App\Models\Batch;
use App\Models\User;

/**
 * @runTestsInSeparateProcesses
 */
class BatchTest extends TestCase
{
    use WithoutMiddleware;

    protected $only_priority_tests = true;
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

        $this->load('/batches/' . $created_batch_id,'POST', [
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


    /// Path: POST    /batches/{batch_id}/levels/{level_id}/teachers
    public function testTeacherAssignment()
    {
        // if ($this->only_priority_tests) {
        //     $this->markTestSkipped("Running only priority tests.");
        // }
        // if (!$this->write_to_db) {
        //     $this->markTestSkipped("Skipping as this test writes to the Database.");
        // }

        $batch_id = 2610;
        $level_id = 7356;
        $non_teacher_user_id = 136214;
        $teacher_ids = [$non_teacher_user_id,142766];
        $this->load("/batches/$batch_id/levels/$level_id/teachers",'POST', [
            'user_ids'  => implode(',', $teacher_ids)
        ]);
        $data = json_decode($this->response->getContent());

        $this->assertEquals($data->status, 'success');
        $this->response->assertStatus(200);

        $found_teacher_count = 0;
        $teachers = app('db')->table('UserBatch')->select('user_id')->where('level_id', $level_id)->where('batch_id', $batch_id)->get();
        foreach($teachers as $teach) {
            if(in_array($teach->user_id, $teacher_ids)) {
                $found_teacher_count++;
            }
        }
        $this->assertEquals($found_teacher_count, count($teacher_ids)); // Found both teachers assigned.

        // This teacher is not a 'ES Volunter' - the call should have made him one.
        $non_teacher = (new User)->find($non_teacher_user_id);
        $groups = $non_teacher->groups()->get();
        $teacher_group_found = false;
        $teacher_group_id = 9;
        foreach($groups as $grp) {
            if($grp->id == $teacher_group_id) {
                $teacher_group_found = true;
                break;
            }
        }
        $this->assertTrue($teacher_group_found);

    }
}
