<?php
namespace Tests\Feature;

use Tests\TestCase;

/**
 * @runTestsInSeparateProcesses
 */
class CityTest extends TestCase
{
    // protected $only_priority_tests = false;
    // protected $write_to_db = true;

    /// Path: GET /cities/{city_id}
    public function testGetCitySingle()
    {
        if ($this->only_priority_tests) {
            $this->markTestSkipped("Running only priority tests.");
        }

        $this->load('/cities/1');

        $this->assertEquals($this->response_data->status, 'success');
        $this->assertEquals($this->response_data->data->cities->name, 'Bangalore');

        $this->assertEquals($this->response->getStatusCode(), 200);
    }

    /// Path: GET /cities/{city_id}     404
    public function testGetCitySingleNotFound()
    {
        if ($this->only_priority_tests) {
            $this->markTestSkipped("Running only priority tests.");
        }

        $this->load('/cities/200');

        $this->assertEquals($this->response_data->status, 'fail');
        $this->assertEquals($this->response_data->data[0], "Can't find any city with the id 200");
        $this->assertEquals($this->response->getStatusCode(), 404);
    }

    /// Path: GET /cities
    public function testGetCities()
    {
        if ($this->only_priority_tests) {
            $this->markTestSkipped("Running only priority tests.");
        }

        $this->load('/cities');

        $this->assertEquals($this->response_data->status, 'success');
        $this->assertEquals($this->response_data->data->cities[2]->name, "Bhopal");
        $this->assertEquals($this->response->getStatusCode(), 200);
    }

    /// Path: GET /cities/{city_id}/users
    public function testGetCityUsersList()
    {
        if ($this->only_priority_tests) {
            $this->markTestSkipped("Running only priority tests.");
        }

        $this->load('/cities/28/users');

        $this->assertEquals($this->response_data->status, 'success');
        $this->assertEquals($this->response_data->data->users[0]->name, 'Data');
        $this->assertEquals($this->response->getStatusCode(), 200);
    }

    /// Path: GET /cities/{city_id}/users
    public function testGetCityUserList404()
    {
        if ($this->only_priority_tests) {
            $this->markTestSkipped("Running only priority tests.");
        }

        $this->load('/cities/200/users');
        $this->assertEquals($this->response_data->status, 'fail');
        $this->assertEquals(404, $this->response->getStatusCode());
    }

    /// Path: GET    /cities/{city_id}/teachers
    public function testGetCitiesTeachersList()
    {
        if ($this->only_priority_tests) {
            $this->markTestSkipped("Running only priority tests.");
        }

        $this->load('/cities/28/teachers');

        $this->assertEquals($this->response_data->status, 'success');
        $search_for = 'Riker';
        $found = false;
        foreach ($this->response_data->data->users as $key => $info) {
            if ($info->name == $search_for) {
                $found = true;
                break;
            }
        }
        $this->assertTrue($found);
        $this->assertEquals($this->response->getStatusCode(), 200);
    }

    /// Path: GET    /cities/{city_id}/fellows
    public function testGetCitiesFellowsList()
    {
        if ($this->only_priority_tests) {
            $this->markTestSkipped("Running only priority tests.");
        }

        $this->load('/cities/28/fellows');

        $this->assertEquals($this->response_data->status, 'success');
        $search_for = 'humancapital.test@makeadiff.in';
        $found = false;
        foreach ($this->response_data->data->users as $key => $info) {
            if ($info->email == $search_for) {
                $found = true;
                break;
            }
        }
        $this->assertTrue($found);
        $this->assertEquals($this->response->getStatusCode(), 200);
    }

    /// Path: GET    /cities/{city_id}/centers
    public function testGetCitiesCentersList()
    {
        if ($this->only_priority_tests) {
            $this->markTestSkipped("Running only priority tests.");
        }

        $this->load('/cities/1/centers');

        $this->assertEquals($this->response_data->status, 'success');
        $search_for = 'Angels';
        $found = false;
        foreach ($this->response_data->data->centers as $key => $info) {
            if ($info->name == $search_for) {
                $found = true;
                break;
            }
        }
        $this->assertTrue($found);
        $this->assertEquals($this->response->getStatusCode(), 200);
    }

    /// Path: GET    /cities/{city_id}/students
    public function testGetCitiesStudentsList()
    {
        if ($this->only_priority_tests) {
            $this->markTestSkipped("Running only priority tests.");
        }

        $this->load('/cities/28/students');

        $this->assertEquals($this->response_data->status, 'success');
        $search_for = 'Leia';
        $found = false;
        foreach ($this->response_data->data->students as $key => $info) {
            if ($info->name == $search_for) {
                $found = true;
                break;
            }
        }
        $this->assertTrue($found);
        $this->assertEquals($this->response->getStatusCode(), 200);
    }
}
