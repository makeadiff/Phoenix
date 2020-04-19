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

        $this->assertEquals($this->response_data->status, 'success');
        $this->assertEquals($this->response_data->data->batches->name, 'Sunday 12:00 AM');
        
        $this->assertEquals($this->response->getStatusCode(), 200);
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

        $this->load('/batches', 'POST', [
            'day'       => '0',
            'class_time'=> '15:00:13',
            'project_id'=> '1',
            'center_id' => '244'
        ]);

        $this->assertEquals($this->response_data->status, 'success');
        $created_batch_id = $this->response_data->data->batch->id;
        $this->assertEquals($this->response_data->data->batch->day, '0');
        $this->assertEquals($this->response_data->data->batch->year, $this->year);
        $this->assertEquals($this->response->getStatusCode(), 200);

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

        $this->load('/batches/' . $created_batch_id, 'POST', [
            'day'       => '1',
            'class_time'=> '15:00:13'
        ]);

        $this->assertEquals($this->response_data->status, 'success');
        $this->assertEquals($this->response_data->data->batch->day, '1');
        $this->assertEquals($this->response_data->data->batch->year, $this->year);
        $this->assertEquals($this->response->getStatusCode(), 200);
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
        if (!$created_batch_id) {
            $this->markTestSkipped("Can't find ID of batch created as test.");
        }

        $this->load('/batches/' . $created_batch_id, 'DELETE');
        $this->assertEquals($this->response->getStatusCode(), 200);

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

        $this->assertEquals($this->response_data->status, 'success');
        $search_for = 'Data';
        $found = false;
        foreach ($this->response_data->data->teachers as $key => $info) {
            if ($info->name == $search_for) {
                $found = true;
                break;
            }
        }
        $this->assertTrue($found);
        $this->assertEquals($this->response->getStatusCode(), 200);
    }

    /// Path: GET    /batches/{batch_id}/levels
    public function testGetBatchesLevelsList()
    {
        if ($this->only_priority_tests) {
            $this->markTestSkipped("Running only priority tests.");
        }

        $this->load('/batches/2608/levels');

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

    /// Path: POST    /batches/{batch_id}/levels/{level_id}/teachers
    public function testTeacherAssignment()
    {
        if ($this->only_priority_tests) {
            $this->markTestSkipped("Running only priority tests.");
        }
        if (!$this->write_to_db) {
            $this->markTestSkipped("Skipping as this test writes to the Database.");
        }

        $batch_id = 2610;
        $level_id = 7356;
        $non_teacher_user_id = 136214;
        $teacher_ids = [$non_teacher_user_id,142766];
        $this->load("/batches/$batch_id/levels/$level_id/teachers", 'POST', [
            'user_ids'  => implode(',', $teacher_ids)
        ]);

        $this->assertEquals($this->response_data->status, 'success');
        $this->assertEquals($this->response->getStatusCode(), 200);

        $found_teacher_count = 0;
        $teachers = app('db')->table('UserBatch')->select('user_id')->where('level_id', $level_id)->where('batch_id', $batch_id)->get();
        foreach ($teachers as $teach) {
            if (in_array($teach->user_id, $teacher_ids)) {
                $found_teacher_count++;
            }
        }
        $this->assertEquals($found_teacher_count, count($teacher_ids)); // Found both teachers assigned.

        // This teacher is not a 'ES Volunter' - the call should have made him one.
        $non_teacher = (new User)->find($non_teacher_user_id);
        $groups = $non_teacher->groups()->get();
        $teacher_group_found = false;
        $teacher_group_id = 9;
        foreach ($groups as $grp) {
            if ($grp->id == $teacher_group_id) {
                $teacher_group_found = true;
                break;
            }
        }
        $this->assertTrue($teacher_group_found);
    }

    /// Path: DELETE    /batches/{batch_id}/levels/{level_id}/teachers/{teacher_id}
    /**
     * @depends testTeacherAssignment
     */
    public function testTeacherDeassignment()
    {
        if ($this->only_priority_tests) {
            $this->markTestSkipped("Running only priority tests.");
        }
        if (!$this->write_to_db) {
            $this->markTestSkipped("Skipping as this test writes to the Database.");
        }

        $batch_id = 2610;
        $level_id = 7356;
        $teacher_id = 136214;
        $this->load("/batches/$batch_id/levels/$level_id/teachers/$teacher_id", 'DELETE');
        $this->assertEquals($this->response->getStatusCode(), 200);

        $teachers = app('db')->table('UserBatch')->select('id')->where('level_id', $level_id)->where('batch_id', $batch_id)->where('user_id', $teacher_id)->get();
        $this->assertEquals(count($teachers), 0);
    }

    /// Path: POST    /batches/{batch_id}/levels/{level_id}/teachers
    /**
     * @depends testTeacherDeassignment
     */
    public function testTeacherSubjectAssignment()
    {
        if ($this->only_priority_tests) {
            $this->markTestSkipped("Running only priority tests.");
        }
        if (!$this->write_to_db) {
            $this->markTestSkipped("Skipping as this test writes to the Database.");
        }

        $batch_id = 2610;
        $level_id = 7356;
        $teacher_ids = [142766];
        $subject_id = 8;
        $this->load("/batches/$batch_id/levels/$level_id/teachers", 'POST', [
            'user_ids'  => implode(',', $teacher_ids),
            'subject_id'=> $subject_id
        ]);

        $this->assertEquals($this->response_data->status, 'success');
        $this->assertEquals($this->response->getStatusCode(), 200);

        $found_teacher_count = 0;
        $teachers = app('db')->table('UserBatch')->select('user_id', 'subject_id')->where('level_id', $level_id)->where('batch_id', $batch_id)->get();
        foreach ($teachers as $teach) {
            if ($teach->subject_id == $subject_id) {
                $found_teacher_count++;
            }
        }
        $this->assertEquals($found_teacher_count, count($teacher_ids)); // Found both teachers assigned.
    }

    public function testMentorAssignment()
    {
        if ($this->only_priority_tests) {
            $this->markTestSkipped("Running only priority tests.");
        }
        if (!$this->write_to_db) {
            $this->markTestSkipped("Skipping as this test writes to the Database.");
        }

        $batch_id = 2610;
        $non_mentor_user_id = 142776;
        $mentor_ids = [$non_mentor_user_id,142783];
        $this->load("/batches/$batch_id/mentors", 'POST', [
            'mentor_user_ids'  => implode(',', $mentor_ids)
        ]);

        $this->assertEquals($this->response_data->status, 'success');
        $this->assertEquals($this->response->getStatusCode(), 200);

        $found_mentor_count = 0;
        $mentors = app('db')->table('UserBatch')->select('user_id')->where('batch_id', $batch_id)->where('role', 'mentor')->get();
        foreach ($mentors as $mentor) {
            if (in_array($mentor->user_id, $mentor_ids)) {
                $found_mentor_count++;
            }
        }
        $this->assertEquals($found_mentor_count, count($mentor_ids)); // Found both teachers assigned.

        // This teacher is not a 'ES Mentor' - the call should have made him one.
        $non_mentor = (new User)->find($non_mentor_user_id);
        $groups = $non_mentor->groups()->get();
        $mentor_group_found = false;
        $mentor_group_id = 8;
        foreach ($groups as $grp) {
            if ($grp->id == $mentor_group_id) {
                $mentor_group_found = true;
                break;
            }
        }
        $this->assertTrue($mentor_group_found);
    }

    /// Path: DELETE    /batches/{batch_id}/levels/{level_id}/teachers/{teacher_id}
    public function testStudentDeassignment()
    {
        if ($this->only_priority_tests) {
            $this->markTestSkipped("Running only priority tests.");
        }
        if (!$this->write_to_db) {
            $this->markTestSkipped("Skipping as this test writes to the Database.");
        }

        $batch_id = 2610;
        $level_id = 7356;
        $teacher_id = 136214;
        $this->load("/batches/$batch_id/levels/$level_id/teachers/$teacher_id", 'DELETE');
        $this->assertEquals($this->response->getStatusCode(), 200);

        $teachers = app('db')->table('UserBatch')->select('user_id')
            ->where('user_id', $teacher_id)->where('batch_id', $batch_id)->where('level_id', $level_id)->get();
        $this->assertEquals(0, count($teachers)); // That teacher shouldn't be found
    }

    // batchSearch(teacher_id: Int, level_id: Int, project_id: Int, center_id: Int, mentor_id: Int, class_status: String, direction: String, from_date: Date, limit: String): [Batch]
    public function testGraphQLBatchSearch()
    {
        if ($this->only_priority_tests) {
            $this->markTestSkipped("Running only priority tests.");
        }

        $this->graphql('{ batchSearch(teacher_id: 1, level_id: 7794, center_id: 184, project_id: 1) { id batch_name day }}');

        $db_batch_ids = app('db')->table('UserBatch AS UB')->join("Batch AS B", "UB.batch_id", "=", "B.id")
            ->select('B.id', 'B.day')->where('UB.user_id', 1)->where('B.year', $this->year)
            ->where("B.center_id", 184)->where("B.project_id", 1)->where("UB.level_id", 7794)
            ->get()->pluck('id')->toArray();
        $found = 0;

        foreach ($this->response_data->data->batchSearch as $batches) {
            if (in_array($batches->id, $db_batch_ids)) {
                $found ++;
            }
        }
        $this->assertEquals($found, count($db_batch_ids));
    }
}
