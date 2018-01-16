<?php

/**
 * @runTestsInSeparateProcesses
 */
class UserTest extends TestCase
{
    private $only_priority_tests = false;
    private $write_to_db = true;

    public function testGetUserSingle()
    {
        if($this->only_priority_tests) $this->markTestSkipped("Running only priority tests.");

        $this->get('/users/1');
        $data = json_decode($this->response->getContent());

        $this->assertEquals($data->status, 'success');
        $this->assertEquals($data->data->user->name, 'Binny V A');
        $this->assertEquals(200, $this->response->status());
    }

    public function testGetUserSingleNotFound()
    {
        if($this->only_priority_tests) $this->markTestSkipped("Running only priority tests.");

        $this->get('/users/2');
        $data = json_decode($this->response->getContent());

        $this->assertEquals($data->status, 'error');
        $this->assertEquals($data->message, "Can't find user with user id '2'");
        $this->assertEquals(404, $this->response->status());
    }

    ///     GET /users
    public function testGetUsers() 
    {
        if($this->only_priority_tests) $this->markTestSkipped("Running only priority tests.");

        $this->get('/users?name=Binny');
        $data = json_decode($this->response->getContent());

        $this->assertEquals($data->status, 'success');
        $this->assertEquals($data->data->users[0]->name, "Binny V A");
        $this->assertEquals(200, $this->response->status());

        $this->get('/users?phone=9746068565');
        $data = json_decode($this->response->getContent());

        $this->assertEquals($data->status, 'success');
        $this->assertEquals($data->data->users[0]->name, "Binny V A");
        $this->assertEquals(200, $this->response->status());

        $this->get('/users?email=binnyva@gmail.com&mad_email=cto@makeadiff.in&city_id=26&user_type=volunteer');
        $data = json_decode($this->response->getContent());

        $this->assertEquals($data->status, 'success');
        $this->assertEquals($data->data->users[0]->name, "Binny V A");
        $this->assertEquals(200, $this->response->status());

        // :TODO:
        // group_id
        // group_in
        // center_id
    }

    ///     POST /users
    public function testPostUsers()
    {
        if($this->only_priority_tests) $this->markTestSkipped("Running only priority tests.");
        if(!$this->write_to_db) $this->markTestSkipped("Skipping as this test writes to the Database.");

        // This will create a new user.
        $user = array(
            'name'  => 'Test Dude',
            'phone' => '10000000001',
            'email' => 'test.test_dude@gmail.com',
            'password'  => 'pass',
            'joined_on' => date('Y-m-d H:i:s'),
            'city_id'   => 28,
            'user_type' => 'volunteer'
        );

        $response = $this->call('POST', '/users', $user);

        $data = json_decode($response->getContent());
        $this->assertEquals($data->status, 'success');
        $this->assertEquals($data->data->user->email, "test.test_dude@gmail.com");
        $this->assertEquals(200, $this->response->status());
        $this->seeInDatabase('User', array('email' => 'test.test_dude@gmail.com'));

        // :TODO: DELETE FROM User WEHRE id=$data->data->user->id
    }

    public function testPostUsersExisting()
    {
        if($this->only_priority_tests) $this->markTestSkipped("Running only priority tests.");
        // Should attempt create a duplicate 
        $user = array(
            'name'  => 'Binny V A',
            'phone' => '9746068565',
            'email' => 'binnyva@gmail.com',
            'joined_on' => date('Y-m-d H:i:s'),
            'password'  => 'pass',
            'city_id'   => 28,
            'user_type' => 'volunteer'
        );

        $response = $this->call('POST', '/users', $user);

        $data = json_decode($response->getContent());
        $this->assertEquals($data->status, 'fail');
        $this->assertEquals($data->data->email[0], "The email has already been taken.");
        $this->assertEquals(400, $this->response->status());
    }

    public function testDeleteUser() 
    {
        if($this->only_priority_tests) $this->markTestSkipped("Running only priority tests.");
        if(!$this->write_to_db) $this->markTestSkipped("Skipping as this test writes to the Database.");

        $response = $this->call('DELETE', '/users/6');
        $data = json_decode($response->getContent());
        $this->assertEquals($data->status, 'success');
        $this->seeInDatabase('User', array('id' => '6', 'status' => '0'));
    }

    public function testGetUserLogin() {
        $this->get('/users/login?email=test.tester_dude@gmail.com&password=pass');
        $data = json_decode($this->response->getContent());
        $this->assertEquals($data->status, 'success');
        $this->assertEquals($data->data->user->name, 'Test Dude');
    }

}
