<?php

/**
 * @runTestsInSeparateProcesses
 */
class CityTest extends TestCase
{
    private $only_priority_tests = true;
    private $write_to_db = true;

    /// Path: GET /cities/{city_id}
    public function testGetCitySingle()
    {
        if($this->only_priority_tests) $this->markTestSkipped("Running only priority tests.");

        $this->get('/cities/1');
        $data = json_decode($this->response->getContent());

        $this->assertEquals($data->status, 'success');
        $this->assertEquals($data->data->city->name, 'Bangalore');
        $this->assertEquals(200, $this->response->status());
    }

    /// Path: GET /cities/{city_id}
    public function testGetCitySingleNotFound()
    {
        if($this->only_priority_tests) $this->markTestSkipped("Running only priority tests.");

        $this->get('/cities/200');
        $data = json_decode($this->response->getContent());

        $this->assertEquals($data->status, 'fail');
        $this->assertEquals($data->data[0], "Can't find any city with the id 200");
        $this->assertEquals(404, $this->response->status());
    }

    /// Path: GET /cities/
    public function testGetCities() 
    {
        if($this->only_priority_tests) $this->markTestSkipped("Running only priority tests.");

        $this->get('/cities');
        $data = json_decode($this->response->getContent());

        $this->assertEquals($data->status, 'success');
        $this->assertEquals($data->data->cities[2]->name, "Bhopal");
        $this->assertEquals(200, $this->response->status());
    }

    /// Path: GET /cities/{city_id}/users
    public function testGetCityUsersList()
    {
        if($this->only_priority_tests) $this->markTestSkipped("Running only priority tests.");

        $this->get('/cities/1/users');
        $data = json_decode($this->response->getContent());

        $this->assertEquals($data->status, 'success');
        $this->assertEquals($data->data->users[0]->name, 'AADAM ILLIAS');
        $this->assertEquals(200, $this->response->status());
    }

    /// Path: GET /cities/{city_id}/users
    public function testGetCityUserList404()
    {
        if($this->only_priority_tests) $this->markTestSkipped("Running only priority tests.");

        $this->get('/cities/200/users');
        $data = json_decode($this->response->getContent());
        $this->assertEquals($data->status, 'error');
        $this->assertEquals(404, $this->response->status());
    }
    
}
