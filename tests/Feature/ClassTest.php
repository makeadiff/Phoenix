<?php
namespace Tests\Feature;

use Tests\TestCase;

/**
 * @runTestsInSeparateProcesses
 */
class ClassTest extends TestCase
{
    // protected $only_priority_tests = true;
    // protected $write_to_db = true;

    // classSearch(teacher_id: Int, level_id: Int, project_id: Int, status: String, batch_id: Int, 
    //	    class_date: Date, direction: String, from_date: Date, limit: String): [Class]
    public function testGraphQLClassSearch()
    {
        if ($this->only_priority_tests) {
            $this->markTestSkipped("Running only priority tests.");
        }

        $this->graphql('{ classSearch( teacher_id:1, level_id:7794, project_id: 1, batch_id: 2652) { id class_on class_type }}');

        $class_ids = app('db')->table('Class AS C')->join("Batch AS B", "C.batch_id", "=", "B.id")
            ->join("UserBatch AS UB", "UB.batch_id", "=", "B.id")
            ->select('C.id', 'C.class_on')->where('B.year', $this->year)
            ->where("C.project_id", 1)->where("C.level_id", 7794)->where("UB.level_id", 7794)
            ->where("C.batch_id", 2652)->where("UB.user_id", 1)
            ->get()->pluck('id')->toArray();
        $found = 0;

        foreach($this->response_data->data->classSearch as $class) {
            if(in_array($class->id, $class_ids)) {
                $found ++;
            }
        }
        $this->assertEquals($found, count($class_ids));
    }

    public function testGraphQLTeacherClassConnection()
    {
        if ($this->only_priority_tests) {
            $this->markTestSkipped("Running only priority tests.");
        }

        $this->graphql('{ teacherClassConnection( teacher_id:1) { class_id class_on level_id }}');

        $class_ids = app('db')->table('Class AS C')->join("Level AS L", "C.level_id", "=", "L.id")
            ->join("UserBatch AS UB", "UB.level_id", "=", "L.id")
            ->select('C.id', 'C.class_on', 'C.level_id')
            ->where('L.year', $this->year)->where("UB.user_id", 1)->where("C.status", "projected")
            ->get()->pluck('id')->toArray();
        $found = 0;

        foreach($this->response_data->data->teacherClassConnection as $class) {
            if(in_array($class->class_id, $class_ids)) {
                $found ++;
            }
        }
        
        $this->assertEquals($found, count($class_ids));
    }

    public function testGraphQLMentorClassConnection()
    {
        if ($this->only_priority_tests) {
            $this->markTestSkipped("Running only priority tests.");
        }

        $this->graphql('{ mentorClassConnection( mentor_id:1) { batch_id class_on }}');

        $batch_ids = array_unique(app('db')->table('Class AS C')->join("Batch AS B", "C.batch_id", "=", "B.id")
            // ->join("UserBatch AS UB", "UB.batch_id", "=", "B.id")
            ->select('C.batch_id', 'C.class_on')
            ->where('B.year', $this->year)->where("B.batch_head_id", 1)->where("C.class_on", "<=", date('Y-m-d H:i:s'))
            ->get()->pluck('batch_id')->toArray());
        $found = 0;

        foreach($this->response_data->data->mentorClassConnection as $batch) {
            if(in_array($batch->batch_id, $batch_ids)) {
                $found ++;
            }
        }
        
        $this->assertEquals($found, count($batch_ids));
    }
}
