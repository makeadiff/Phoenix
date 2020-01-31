<?php
namespace Tests\Feature;

use Tests\TestCase;

/**
 * @runTestsInSeparateProcesses
 */
class UserTest extends TestCase
{
    // protected $only_priority_tests = true;
    // protected $write_to_db = true;

    /// Path: GET    /users/{user_id}
    public function testGetUserSingle()
    {
        if ($this->only_priority_tests) {
            $this->markTestSkipped("Running only priority tests.");
        }

        $this->load('/users/1');

        $this->assertEquals($this->response_data->status, 'success');
        $this->assertEquals($this->response_data->data->users->name, 'Binny V A');
        $this->response->assertStatus(200);
    }

    /// Path: GET    /users/{user_id}   404
    public function testGetUserSingleNotFound()
    {
        if ($this->only_priority_tests) {
            $this->markTestSkipped("Running only priority tests.");
        }

        $this->load('/users/29');

        $this->assertEquals($this->response_data->status, 'fail');
        $this->assertEquals($this->response_data->data[0], "Can't find user with user id '29'");
        $this->response->assertStatus(404);
    }

    /// Path: GET  /users
    public function testGetUsers()
    {
        if ($this->only_priority_tests) {
            $this->markTestSkipped("Running only priority tests.");
        }

        $this->load('/users?name=Binny');

        $this->assertEquals($this->response_data->status, 'success');
        $this->assertEquals($this->response_data->data->users[0]->name, "Binny V A");
        $this->response->assertStatus(200);

        $this->load('/users?phone=9746068565');

        $this->assertEquals($this->response_data->status, 'success');
        $this->assertEquals($this->response_data->data->users[0]->name, "Binny V A");
        $this->response->assertStatus(200);

        $this->load('/users?email=binnyva@gmail.com&mad_email=cto@makeadiff.in&city_id=26&user_type=volunteer');

        $this->assertEquals($this->response_data->status, 'success');
        $this->assertEquals($this->response_data->data->users[0]->name, "Binny V A");
        $this->response->assertStatus(200);

        // :TODO:
        // group_id
        // group_in
        // center_id
    }

    /// Path: POST /users
    public function testPostUsers()
    {
        if ($this->only_priority_tests) {
            $this->markTestSkipped("Running only priority tests.");
        }
        if (!$this->write_to_db) {
            $this->markTestSkipped("Skipping as this test writes to the Database.");
        }

        $number = rand(0,9999);
        $uniquer = str_pad($number,4,0,STR_PAD_LEFT);

        $email = "test_user_$uniquer@makeadiff.in";
        // This will create a new user.
        $user = array(
            'name'      => 'Test Dude',
            'phone'     => '1000000' . $uniquer,
            'email'     => $email,
            'password'  => 'test-pass',
            'joined_on' => date('Y-m-d H:i:s'),
            'city_id'   => 28,
            'profile'   => 'teacher',
            'user_type' => 'volunteer'
        );

        $this->load('/users', 'POST', $user);

        $this->assertEquals($this->response_data->status, 'success');
        $this->assertEquals($this->response_data->data->users->email, $email);
        $this->response->assertStatus(200);
        $this->assertDatabaseHas('User', array('email' => $email));

        $created_user_id = $this->response_data->data->users->id;
        return $created_user_id;
    }

    /// Path: POST /users
    public function testPostUsersExisting()
    {
        if ($this->only_priority_tests) {
            $this->markTestSkipped("Running only priority tests.");
        }
        // Should attempt create a duplicate
        $user = array(
            'name'      => 'Binny V A',
            'phone'     => '9746068565',
            'email'     => 'binnyva@gmail.com',
            'joined_on' => date('Y-m-d H:i:s'),
            'password'  => 'test-pass',
            'city_id'   => 28,
            'user_type' => 'volunteer'
        );

        $this->load('/users', 'POST', $user);

        $this->assertEquals($this->response_data->status, 'fail');
        $this->assertEquals($this->response_data->data->email[0], "Entered Email ID already exists in the MAD System");
        $this->response->assertStatus(400);
    }

    /// Path: POST /users
    /**
     * @depe nds testPostUsers
     */
    public function testPostUsersEdit($created_user_id = 198344)
    {
        if ($this->only_priority_tests) {
            $this->markTestSkipped("Running only priority tests.");
        }
        $user = [
            'name'      => 'New Name',
            'phone'     => '9340567890',
        ];
        $this->load('/users/' . $created_user_id, 'POST', $user);
        // dd($this->response_data);

        $this->assertEquals($this->response_data->status, 'success');
        $this->response->assertStatus(200);
        $this->assertDatabaseHas('User', [
            'id'    => $created_user_id, 
            'name'  => 'New Name'
        ]);
    }

    /// Path: DELETE    /users/{user_id}
    /**
     * @depends testPostUsers
     */
    public function testDeleteUser($created_user_id)
    {
        if ($this->only_priority_tests) {
            $this->markTestSkipped("Running only priority tests.");
        }
        if (!$this->write_to_db) {
            $this->markTestSkipped("Skipping as this test writes to the Database.");
        }

        $this->load('/users/' . $created_user_id, 'DELETE');
        $this->response->assertStatus(200);
        $this->assertDatabaseHas('User', array('id' => $created_user_id, 'status' => '0'));
    }

    /// Path: POST  /users/login
    public function testGetUserLogin()
    {
        if ($this->only_priority_tests) {
            $this->markTestSkipped("Running only priority tests.");
        }

        $this->load('/users/login?email=sulu.simulation@makeadiff.in&password=pass');
        $this->assertEquals($this->response_data->status, 'success');
        $this->assertEquals($this->response_data->data->users->name, 'Sulu');
    }

    /// Path: GET   /users/{user_id}/groups
    public function testGetUserGroupList()
    {
        if ($this->only_priority_tests) {
            $this->markTestSkipped("Running only priority tests.");
        }

        $this->load('/users/1/groups');

        $this->assertEquals($this->response_data->status, 'success');
        $search_for = 'ES Volunteer';
        $found = false;
        foreach ($this->response_data->data->groups as $key => $info) {
            if ($info->name == $search_for) {
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
        if ($this->only_priority_tests) {
            $this->markTestSkipped("Running only priority tests.");
        }

        $this->load('/users/1/credit');

        $this->assertEquals($this->response_data->status, 'success');
        $this->assertTrue(is_numeric($this->response_data->data->credit));
        $this->response->assertStatus(200);
    }
}
