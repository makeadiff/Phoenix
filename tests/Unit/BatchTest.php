<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use App\Models\Batch;

/**
 * @runInSeparateProcess
 */
class BatchTest extends TestCase
{
	use WithoutMiddleware;

    public function testBatchSearch()
    {
        $batch = new Batch;
        $batches = $batch->search(['id' => 2610]);

        $result = $batches->first();

        $this->assertSame($result->id, 2610);
        $this->assertSame($result->day, '0');
    }

    public function testBatchSearchWithLevel()
    {
        $batch = new Batch;
        $batches = $batch->search(['level_id' => 7355]);

        $result = $batches->first();

        $this->assertSame($result->id, 2609);
        $this->assertSame($result->day, '0');
    }

    // :TODO: teacher_id, direction, project_id



}
