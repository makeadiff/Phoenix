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

    public function testUserSearchId()
    {
        // if($this->only_priority_tests) $this->markTestSkipped("Running only priority tests.");

        $model = new User;
        $data = $model->search(['id' => 1]);

        $result = $data->first();

        $this->assertEquals($result->name, 'Binny V A');
    }

    public function testUserSearchByIdentifier()
    {
        // if($this->only_priority_tests) $this->markTestSkipped("Running only priority tests.");

        $model = new User;
        $data = $model->search(['identifier' => 'binnyva@gmail.com']);

        $result = $data->first();

        $this->assertEquals($result->id, '1');
    }

    public function testUserSearchByVertical()
    {
        // if($this->only_priority_tests) $this->markTestSkipped("Running only priority tests.");

        $model = new User;
        $data = $model->search(['vertical_id' => 1]);

        $result = $data->first();

        $this->assertEquals($result->id, '166483');
    }

    public function testUserSearchByCenterId()
    {
        // if($this->only_priority_tests) $this->markTestSkipped("Running only priority tests.");

        $model = new User;
        $data = $model->search(['center_id' => 220]);

        $result = $data->first();

        $this->assertEquals($result->id, '106634');
    }

    public function testUserSearchByGroupType()
    {
        // if($this->only_priority_tests) $this->markTestSkipped("Running only priority tests.");

        $model = new User;
        $data = $model->search(['user_group_type' => 'national']);

        $result = $data->first();

        $this->assertEquals($result->id, '154737');
    }
}
