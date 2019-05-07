<?php
namespace Tests\Feature;

use Tests\TestCase;

/**
 * @runTestsInSeparateProcesses
 */
class CityTest extends TestCase
{
    // private $only_priority_tests = true;
    // private $write_to_db = true;

    /// Path: GET /cities/{city_id}
    public function testGetCitySingle()
    {
        if($this->only_priority_tests) $this->markTestSkipped("Running only priority tests.");

        $this->load('/cities/1');
        $data = json_decode($this->response->getContent());

        $this->assertEquals($data->status, 'success');
        $this->assertEquals($data->data->cities->name, 'Bangalore');
        $this->response->assertStatus(200);
    }

    /// Path: GET /cities/{city_id}     404
    public function testGetCitySingleNotFound()
    {
        if($this->only_priority_tests) $this->markTestSkipped("Running only priority tests.");

        $this->load('/cities/200');
        $data = json_decode($this->response->getContent());

        $this->assertEquals($data->status, 'fail');
        $this->assertEquals($data->data[0], "Can't find any city with the id 200");
        $this->response->assertStatus(404);
    }

    /// Path: GET /cities
    public function testGetCities() 
    {
        if($this->only_priority_tests) $this->markTestSkipped("Running only priority tests.");

        $this->load('/cities');
        $data = json_decode($this->response->getContent());

        $this->assertEquals($data->status, 'success');
        $this->assertEquals($data->data->cities[2]->name, "Bhopal");
        $this->response->assertStatus(200);
    }

    /// Path: GET /cities/{city_id}/users
    public function testGetCityUsersList()
    {
        if($this->only_priority_tests) $this->markTestSkipped("Running only priority tests.");

        $this->load('/cities/28/users');
        $data = json_decode($this->response->getContent());

        $this->assertEquals($data->status, 'success');
        $this->assertEquals($data->data->users[0]->name, 'Data');
        $this->response->assertStatus(200);
    }

    /// Path: GET /cities/{city_id}/users
    public function testGetCityUserList404()
    {
        if($this->only_priority_tests) $this->markTestSkipped("Running only priority tests.");

        $this->load('/cities/200/users');
        $data = json_decode($this->response->getContent());
        $this->assertEquals($data->status, 'fail');
        $this->assertEquals(404, $this->response->status());
    }

    /// Path: GET    /cities/{city_id}/teachers
    public function testGetCitiesTeachersList()
    {
        if($this->only_priority_tests) $this->markTestSkipped("Running only priority tests.");

        $this->load('/cities/28/teachers');
        $data = json_decode($this->response->getContent());

        $this->assertEquals($data->status, 'success');
        $search_for = 'Data';
        $found = false;
        foreach ($data->data->users as $key => $info) {
            if($info->name == $search_for) {
                $found = true;
                break;
            }
        }
        $this->assertTrue($found);
        $this->response->assertStatus(200);
    }

    /// Path: GET    /cities/{city_id}/fellows
    public function testGetCitiesFellowsList()
    {
        if($this->only_priority_tests) $this->markTestSkipped("Running only priority tests.");

        $this->load('/cities/28/fellows');
        $data = json_decode($this->response->getContent());

        $this->assertEquals($data->status, 'success');
        $search_for = 'humancapital.test@makeadiff.in';
        $found = false;
        foreach ($data->data->users as $key => $info) {
            if($info->email == $search_for) {
                $found = true;
                break;
            }
        }
        $this->assertTrue($found);
        $this->response->assertStatus(200);
    }

    /// Path: GET    /cities/{city_id}/centers
    public function testGetCitiesCentersList()
    {
        if($this->only_priority_tests) $this->markTestSkipped("Running only priority tests.");

        $this->load('/cities/1/centers');
        $data = json_decode($this->response->getContent());

        $this->assertEquals($data->status, 'success');
        $search_for = 'Angels';
        $found = false;
        foreach ($data->data->centers as $key => $info) {
            if($info->name == $search_for) {
                $found = true;
                break;
            }
        }
        $this->assertTrue($found);
        $this->response->assertStatus(200);
    }

    /// Path: GET    /cities/{city_id}/students
    public function testGetCitiesStudentsList()
    {
        if($this->only_priority_tests) $this->markTestSkipped("Running only priority tests.");

        $this->load('/cities/28/students');
        $data = json_decode($this->response->getContent());

        $this->assertEquals($data->status, 'success');
        $search_for = 'Leia';
        $found = false;
        foreach ($data->data->students as $key => $info) {
            if($info->name == $search_for) {
                $found = true;
                break;
            }
        }
        $this->assertTrue($found);
        $this->response->assertStatus(200);
    }


}
