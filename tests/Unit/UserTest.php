<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use App\Models\User;

/**
 * @runInSeparateProcess
 */
class UserTest extends TestCase
{
    use WithoutMiddleware;
    // :TODO: Rewrite these using SQL that live pulls the data instead of hard coding. These will break when year changes(mostly.)
    
    protected $only_priority_tests = false;
    protected $write_to_db = false;

    public function testUserSearchId()
    {
        if ($this->only_priority_tests) {
            $this->markTestSkipped("Running only priority tests.");
        }

        $model = new User;
        $data = $model->search(['id' => $this->ideal_user_id]);

        $result = $data->first();

        $this->assertEquals($result->name, $this->ideal_user['name']);
    }

    public function testUserSearchByIdentifier()
    {
        if ($this->only_priority_tests) {
            $this->markTestSkipped("Running only priority tests.");
        }

        $model = new User;
        $data = $model->search(['identifier' => $this->ideal_user['email']]);

        $result = $data->first();

        $this->assertEquals($result->id, $this->ideal_user['id']);
    }

    public function testUserSearchByCenterId()
    {
        if ($this->only_priority_tests) {
            $this->markTestSkipped("Running only priority tests.");
        }

        $model = new User;
        $data = $model->search(['center_id' => $this->ideal_center_id]);

        $teacher_ids = [203356, 203355, 203354, 203353];
        $found = 0;
        foreach ($data as $usr) {
            if (in_array($usr->id, $teacher_ids)) {
                $found++;
            }
        }
        $this->assertEquals($found, count($teacher_ids));
    }


    // Risky tests. Data can change.
    public function testUserSearchByVertical()
    {
        if ($this->only_priority_tests) {
            $this->markTestSkipped("Running only priority tests.");
        }

        $model = new User;
        $data = $model->search(['vertical_id' => 1]);

        $result = $data->first();
        $this->assertEquals($result->id, '169630');
    }


    public function testUserSearchByGroupType()
    {
        if ($this->only_priority_tests) {
            $this->markTestSkipped("Running only priority tests.");
        }

        $model = new User;
        $data = $model->search(['user_group_type' => 'national']);

        $result = $data->first();

        $this->assertEquals($result->id, '154737');
    }
}
