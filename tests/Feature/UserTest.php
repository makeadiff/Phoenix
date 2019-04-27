<?php
namespace Tests\Feature;

use Tests\TestCase;

/**
 * @runTestsInSeparateProcesses
 */
class UserTest extends TestCase
{
    // private $only_priority_tests = false;
    // private $write_to_db = false;

    /// Path: GET    /users/{user_id}
    public function testGetUserSingle()
    {
        if($this->only_priority_tests) $this->markTestSkipped("Running only priority tests.");

        $this->load('/users/1');
        $data = json_decode($this->response->getContent());

        $this->assertEquals($data->status, 'success');
        $this->assertEquals($data->data->users->name, 'Binny V A');
        $this->response->assertStatus(200);
    }

    /// Path: GET    /users/{user_id}   404
    public function testGetUserSingleNotFound()
    {
        if($this->only_priority_tests) $this->markTestSkipped("Running only priority tests.");

        $this->load('/users/29');
        $data = json_decode($this->response->getContent());

        $this->assertEquals($data->status, 'error');
        $this->assertEquals($data->message, "Can't find user with user id '29'");
        $this->response->assertStatus(404);
    }

    /// Path: GET  /users
    public function testGetUsers() 
    {
        if($this->only_priority_tests) $this->markTestSkipped("Running only priority tests.");

        $this->load('/users?name=Binny');
        $data = json_decode($this->response->getContent());

        $this->assertEquals($data->status, 'success');
        $this->assertEquals($data->data->users[0]->name, "Binny V A");
        $this->response->assertStatus(200);

        $this->load('/users?phone=9746068565');
        $data = json_decode($this->response->getContent());

        $this->assertEquals($data->status, 'success');
        $this->assertEquals($data->data->users[0]->name, "Binny V A");
        $this->response->assertStatus(200);

        $this->load('/users?email=binnyva@gmail.com&mad_email=cto@makeadiff.in&city_id=26&user_type=volunteer');
        $data = json_decode($this->response->getContent());

        $this->assertEquals($data->status, 'success');
        $this->assertEquals($data->data->users[0]->name, "Binny V A");
        $this->response->assertStatus(200);

        // :TODO:
        // group_id
        // group_in
        // center_id
    }

    /// Path: POST /users
    public function testPostUsers()
    {
        if($this->only_priority_tests) $this->markTestSkipped("Running only priority tests.");
        if(!$this->write_to_db) $this->markTestSkipped("Skipping as this test writes to the Database.");

        $email = 'test.test_dxd3@gmail.com';
        // This will create a new user.
        $user = array(
            'name'  => 'Test Dude',
            'phone' => '10000000014',
            'email' => $email,
            'password'  => 'pass',
            'joined_on' => date('Y-m-d H:i:s'),
            'city_id'   => 28,
            'user_type' => 'volunteer'
        );

        $response = $this->load('/users', 'POST', $user);

        $data = json_decode($response->getContent());
        $this->assertEquals($data->status, 'success');
        $this->assertEquals($data->data->users->email, $email);
        $this->response->assertStatus(200);
        $this->seeInDatabase('User', array('email' => $email));

        // :TODO: DELETE FROM User WEHRE id=$data->data->user->id
    }

    /// Path: POST /users
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

        $response = $this->load('/users', 'POST', $user);

        $data = json_decode($response->getContent());
        $this->assertEquals($data->status, 'fail');
        $this->assertEquals($data->data->email[0], "The email has already been taken.");
        $this->response->assertStatus(400);
    }

    /// Path: DELETE    /users/{user_id}
    public function testDeleteUser() 
    {
        if($this->only_priority_tests) $this->markTestSkipped("Running only priority tests.");
        if(!$this->write_to_db) $this->markTestSkipped("Skipping as this test writes to the Database.");

        $response = $this->load('/users/6', 'DELETE');
        $this->response->assertStatus(200);
        $this->seeInDatabase('User', array('id' => '6', 'status' => '0'));
    }

    /// Path: GET  /users/login
    public function testGetUserLogin() 
    {
        if($this->only_priority_tests) $this->markTestSkipped("Running only priority tests.");

        $this->load('/users/login?email=sulu.simulation@makeadiff.in&password=pass');
        $data = json_decode($this->response->getContent());
        $this->assertEquals($data->status, 'success');
        $this->assertEquals($data->data->users->name, 'Sulu');
    }

    /// Path: GET   /users/{user_id}/groups
    public function testGetUserGroupList()
    {
        if($this->only_priority_tests) $this->markTestSkipped("Running only priority tests.");

        $this->load('/users/1/groups');
        $data = json_decode($this->response->getContent());

        $this->assertEquals($data->status, 'success');
        $search_for = 'ES Volunteer';
        $found = false;
        foreach ($data->data->groups as $key => $info) {
            if($info->name == $search_for) {
                $found = true;
                break;
            }
        }
        $this->assertTrue($found);
        $this->response->assertStatus(200);
    }

    /// Path: GET   /users/{user_id}/credit
    public function testGetUsersCreditSingle()
    {
        if($this->only_priority_tests) $this->markTestSkipped("Running only priority tests.");

        $this->load('/users/1/credit');
        $data = json_decode($this->response->getContent());

        $this->assertEquals($data->status, 'success');
        $this->assertTrue(is_numeric($data->data->credit));
        $this->response->assertStatus(200);
    }


}
