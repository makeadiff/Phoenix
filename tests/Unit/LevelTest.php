<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use App\Models\Level;

/**
 * @runInSeparateProcess
 */
class LevelTest extends TestCase
{
    use WithoutMiddleware;

    public function testLevelSearch()
    {
        // if($this->only_priority_tests) $this->markTestSkipped("Running only priority tests.");

        $model = new Level;
        $level_id = array_rand($this->ideal_levels);
        $data = $model->search(['id'=> $level_id]);

        $result = $data->first();

        $this->assertEquals($result->id, $level_id);
        $this->assertEquals($result->name, $this->ideal_levels[$level_id]['level_name']);
    }

    public function testLevelSearchBatch()
    {
        // if($this->only_priority_tests) $this->markTestSkipped("Running only priority tests.");

        $model = new Level;
        
        $batch_id = array_rand($this->ideal_batch_level_user_mapping);
        $level_id = array_key_first($this->ideal_batch_level_user_mapping[$batch_id]);

        $data = $model->search(['batch_id'=> $batch_id]);
        $result = $data->first();

        $this->assertEquals($result->id, $level_id);
    }

    public function testLevelinCenter()
    {
        // if($this->only_priority_tests) $this->markTestSkipped("Running only priority tests.");

        $model = new Level;
        $data = $model->inCenter($this->ideal_center_id);
        $level_ids = [10056, 10057];

        $found = 0;
        foreach($data as $level) {
            if(in_array($level->id, $level_ids)) {
                $found++;
            }
        }
        $this->assertEquals($found, count($level_ids));
    }
}
