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
        $batch_id = array_rand($this->ideal_batchs);
        $batch = $batch->search(['id' => $batch_id]);
        $result = $batch->first();

        $this->assertEquals($result->id, $batch_id);
        $this->assertEquals($result->day, $this->ideal_batchs[$batch_id]['day']);
    }

    public function testBatchSearchWithLevel()
    {
        $batch = new Batch;
        $batch_id = array_rand($this->ideal_batch_level_user_mapping);

        $level_id = array_key_first($this->ideal_batch_level_user_mapping[$batch_id]);
        $batches = $batch->search(['level_id' => $level_id]);

        $result = $batches->first();

        $this->assertEquals($result->id, $batch_id);
        $this->assertEquals($result->day, $this->ideal_batchs[$batch_id]['day']);
    }

    public function testBatchSearchWithTeacher()
    {
        $batch = new Batch;
        $batch_id = array_keys($this->ideal_batch_level_user_mapping)[0];
        $teacher_id = array_values($this->ideal_batch_level_user_mapping[$batch_id])[0][0];
        $batches = $batch->search(['teacher_id' => $teacher_id]);

        $result = $batches->first();

        $this->assertEquals($result->id, $batch_id);
        $this->assertEquals($result->day, $this->ideal_batchs[$batch_id]['day']);
    }

    public function testBatchSearchWithProject()
    {
        $batch = new Batch;
        $batches = $batch->search(['project_id' => $this->ideal_project_id, 'center_id' => $this->ideal_center_id]);

        $batch_ids = array_keys($this->ideal_batchs);

        $found = 0;
        foreach ($batches as $bth) {
            if (in_array($bth->id, $batch_ids)) {
                $found++;
            }
        }
        $this->assertEquals($found, count($batch_ids));
    }

    public function testBatchName()
    {
        $batch = new Batch;
        $batch_id = array_rand($this->ideal_batchs);
        $name = $batch->find($batch_id)->name();

        $this->assertEquals($name, $this->ideal_batchs[$batch_id]['name']);
    }

    // :TODO: direction
}
