<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use App\Models\Batch;

// You'll have to disable header() calls in api.php in routes for this to work.

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

    public function testBatchSearchWithTeacher()
    {
        $batch = new Batch;
        $batches = $batch->search(['teacher_id' => 1]);

        $result = $batches->first();

        $this->assertSame($result->id, 2652);
        $this->assertSame($result->day, '0');
    }

    public function testBatchSearchWithProject()
    {
        $batch = new Batch;
        $batches = $batch->search(['project_id' => 2, 'center_id' => 240]);

        $result = $batches->first();

        $this->assertSame($result->id, 2980);
        $this->assertSame($result->day, '0');
    }

    public function testBatchName()
    {
        $batch = new Batch;
        $name = $batch->find(2609)->name();

        $this->assertEquals($name, 'Sunday 12:00 AM');
    }

    // :TODO: direction
}
