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
        $data = $model->search(['id'=> 7355]);

        $result = $data->first();

        $this->assertEquals($result->id, '7355');
        $this->assertEquals($result->name, '7 A');
    }

    public function testLevelSearchBatch() 
    {
        // if($this->only_priority_tests) $this->markTestSkipped("Running only priority tests.");

        $model = new Level;
        $data = $model->search(['batch_id'=> 2609]);

        $result = $data->first();

        $this->assertEquals($result->id, '7355');
    }

    public function testLevelinCenter() 
    {
        // if($this->only_priority_tests) $this->markTestSkipped("Running only priority tests.");

        $model = new Level;
        $data = $model->inCenter(220);

        $result = $data->first();

        $this->assertEquals($result->id, '7903');
    }

}
