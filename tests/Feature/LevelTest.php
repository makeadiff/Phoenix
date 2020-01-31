<?php
namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use App\Models\Level;

/**
 * @runTestsInSeparateProcesses
 */
class LevelTest extends TestCase
{
    use WithoutMiddleware;

    // protected $only_priority_tests = true;
    // protected $write_to_db = true;

    /// Path: GET    /levels/{level_id}
    public function testGetLevelsSingle()
    {
        if ($this->only_priority_tests) {
            $this->markTestSkipped("Running only priority tests.");
        }

        $this->load('/levels/7357');
        $data = json_decode($this->response->getContent());

        $this->assertEquals($data->status, 'success');
        $this->assertEquals($data->data->levels->name, '9 B');
        $this->response->assertStatus(200);
    }

    /// Path: POST    /levels
    public function testCreateLevel()
    {
        if ($this->only_priority_tests) {
            $this->markTestSkipped("Running only priority tests.");
        }
        if (!$this->write_to_db) {
            $this->markTestSkipped("Skipping as this test writes to the Database.");
        }

        $this->load('/levels', 'POST', [
            'grade'     => '7',
            'name'      => 'C',
            'project_id'=> '1',
            'center_id' => '244'
        ]);
        $data = json_decode($this->response->getContent());

        $this->assertEquals($data->status, 'success');
        $created_level_id = $data->data->level->id;
        $this->assertEquals($data->data->level->grade, '7');
        $this->assertEquals($data->data->level->year, $this->year);
        $this->response->assertStatus(200);

        return $created_level_id;
    }

    /// Path: POST    /levels/{level_id}
    /**
     * @depends testCreateLevel
     */
    public function testEditLevel($created_level_id)
    {
        if ($this->only_priority_tests) {
            $this->markTestSkipped("Running only priority tests.");
        }
        if (!$this->write_to_db) {
            $this->markTestSkipped("Skipping as this test writes to the Database.");
        }

        $this->load("/levels/$created_level_id", 'POST', [
            'grade' => '8',
            'name'  => 'F'
        ]);
        $data = json_decode($this->response->getContent());

        $this->assertEquals($data->status, 'success');
        $this->assertEquals($data->data->level->grade, '8');
        $this->assertEquals($data->data->level->year, $this->year);
        $this->response->assertStatus(200);

        // DB  Check
        $level_model = new Level;
        $level_info = $level_model->find($created_level_id);
        $this->assertEquals($level_info->name, 'F');
    }

    /// Path: DELETE    /levels/{level_id}
    /**
     * @depends testCreateLevel
     */
    public function testDeleteLevel($created_level_id)
    {
        if ($this->only_priority_tests) {
            $this->markTestSkipped("Running only priority tests.");
        }
        if (!$this->write_to_db) {
            $this->markTestSkipped("Skipping as this test writes to the Database.");
        }
        if (!$created_level_id) {
            $this->markTestSkipped("Can't find ID of level created as test.");
        }

        $this->load('/levels/' . $created_level_id, 'DELETE');
        $this->response->assertStatus(200);

        $level_model = new Level;
        $level_info = $level_model->find($created_level_id);
        $this->assertEquals($level_info->status, '0'); // Its actually deleted.
    }

    /// Path: GET    /levels/{level_id}/students
    public function testGetLevelsStudentsList()
    {
        if ($this->only_priority_tests) {
            $this->markTestSkipped("Running only priority tests.");
        }

        $this->load('/levels/7354/students');
        $data = json_decode($this->response->getContent());

        $this->assertEquals($data->status, 'success');
        $search_for = 'Jar Jar';
        $found = false;
        foreach ($data->data->students as $key => $info) {
            if ($info->name == $search_for) {
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
        if ($this->only_priority_tests) {
            $this->markTestSkipped("Running only priority tests.");
        }

        $this->load('/levels/7355/batches');
        $data = json_decode($this->response->getContent());

        $this->assertEquals($data->status, 'success');
        $search_for = 'Saturday 04:00 PM';
        $found = false;
        foreach ($data->data->batches as $key => $info) {
            if ($info->name == $search_for) {
                $found = true;
                break;
            }
        }
        $this->assertTrue($found);
        $this->response->assertStatus(200);
    }

    /// Path: POST    /levels/{level_id}/students
    public function testStudentAssignment()
    {
        // if ($this->only_priority_tests) {
        //     $this->markTestSkipped("Running only priority tests.");
        // }
        // if (!$this->write_to_db) {
        //     $this->markTestSkipped("Skipping as this test writes to the Database.");
        // }

        $level_id = 7356;
        $student_ids = [21930, 21918];
        $this->load("/levels/$level_id/students", 'POST', [
            'student_ids'  => implode(',', $student_ids)
        ]);
        $data = json_decode($this->response->getContent());

        $this->assertEquals($data->status, 'success');
        $this->response->assertStatus(200);

        $found_student_count = 0;
        $students = app('db')->table('StudentLevel')->select('student_id')->where('level_id', $level_id)->get();
        foreach ($students as $student) {
            if (in_array($student->student_id, $student_ids)) {
                $found_student_count++;
            }
        }
        $this->assertEquals($found_student_count, count($student_ids)); // Found both students assigned.
    }

    /// Path: DELETE    /levels/{level_id}/students/{student_id}
    public function testStudentDeassignment()
    {
        // if ($this->only_priority_tests) {
        //     $this->markTestSkipped("Running only priority tests.");
        // }
        // if (!$this->write_to_db) {
        //     $this->markTestSkipped("Skipping as this test writes to the Database.");
        // }

        $level_id = 7356;
        $student_id = 21930;
        $this->load("/levels/$level_id/students/$student_id", 'DELETE');
        $data = json_decode($this->response->getContent());
        $this->response->assertStatus(200);

        $students = app('db')->table('StudentLevel')->select('student_id')->where('student_id', $student_id)->where('level_id', $level_id)->get();
        $this->assertEquals(0, count($students)); // That student shouldn't be found
    }
}
