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
        $this->assertEquals($this->response->getStatusCode(), 200);
    }

    /// GraphQL user(id:1)
    public function testGraphQLUserSingle()
    {
        if ($this->only_priority_tests) {
            $this->markTestSkipped("Running only priority tests.");
        }

        $this->graphql("{user(id:1) { id name }}");
        $this->assertEquals($this->response_data->data->user->name, 'Binny V A');
        $this->assertEquals($this->response->getStatusCode(), 200);
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
        $this->assertEquals($this->response->getStatusCode(), 404);
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
        $this->assertEquals($this->response->getStatusCode(), 200);

        $this->load('/users?phone=9746068565');

        $this->assertEquals($this->response_data->status, 'success');
        $this->assertEquals($this->response_data->data->users[0]->name, "Binny V A");
        $this->assertEquals($this->response->getStatusCode(), 200);

        $this->load('/users?email=binnyva@gmail.com&mad_email=cto@makeadiff.in&city_id=26&user_type=volunteer');

        $this->assertEquals($this->response_data->status, 'success');
        $this->assertEquals($this->response_data->data->users[0]->name, "Binny V A");
        $this->assertEquals($this->response->getStatusCode(), 200);

        // :TODO:
        // group_id
        // group_in
        // center_id
    }

    /// GraphQL: users(name:"Binny")
    public function testGraphQLUsers()
    {
        if ($this->only_priority_tests) {
            $this->markTestSkipped("Running only priority tests.");
        }

        $this->graphql('{users(name:"Binny%") { id name }}');
        $this->assertEquals($this->response_data->data->users[0]->name, "Binny V A");
        $this->assertEquals($this->response->getStatusCode(), 200);

        $this->graphql('{users(phone:"9746068565") { id name }}');

        $this->assertEquals($this->response_data->data->users[0]->name, "Binny V A");
        $this->assertEquals($this->response->getStatusCode(), 200);

        $this->graphql('{userSearch(email:"binnyva@gmail.com", mad_email:"cto@makeadiff.in", city_id:26, user_type:"volunteer") { id name }}');

        $this->assertEquals($this->response_data->data->userSearch[0]->name, "Binny V A");
        $this->assertEquals($this->response->getStatusCode(), 200);

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

        $number = rand(0, 9999);
        $uniquer = str_pad($number, 4, 0, STR_PAD_LEFT);

        $email = "test_user_$uniquer@makeadiff.in";
        // This will create a new user.
        $user = array(
            'name'      => 'Test Dude',
            'phone'     => '1000000' . $uniquer,
            'email'     => $email,
            'password'  => 'test-pass',
            'joined_on' => date('Y-m-d H:i:s'),
            'city_id'   => 28,
            'profile'   => 'teaching',
            'user_type' => 'volunteer'
        );

        $this->load('/users', 'POST', $user);

        $this->assertEquals($this->response_data->status, 'success');
        $this->assertEquals($this->response_data->data->users->email, $email);
        $this->assertEquals($this->response->getStatusCode(), 200);
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
        $this->assertEquals($this->response_data->data->email[0], "Entered Email ID already exists in the MAD database");
        $this->assertEquals($this->response->getStatusCode(), 400);
    }

    /// Path: POST /users
    /**
     * @depends testPostUsers
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
        $this->assertEquals($this->response->getStatusCode(), 200);
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
        $this->assertEquals($this->response->getStatusCode(), 200);
        $this->assertDatabaseHas('User', array('id' => $created_user_id, 'status' => '0'));
    }

    /// Path: POST  /users/login
    public function testPostUserLogin()
    {
        if ($this->only_priority_tests) {
            $this->markTestSkipped("Running only priority tests.");
        }

        $this->load('/users/login', 'POST', ['email' => 'sulu.simulation@makeadiff.in', 'password' => 'pass'], 'basic');
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
        $this->assertEquals($this->response->getStatusCode(), 200);
    }

    /// GraphQL: user(id: 1) { groups { id name }}
    public function testGraphQLUserGroupList()
    {
        if ($this->only_priority_tests) {
            $this->markTestSkipped("Running only priority tests.");
        }

        $this->graphql('{ user(id: 1) { groups { id name }} }');

        $search_for = 'ES Volunteer';
        $found = false;
        foreach ($this->response_data->data->user->groups as $key => $info) {
            if ($info->name == $search_for) {
                $found = true;
                break;
            }
        }
        $this->assertTrue($found);
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
        $this->assertEquals($this->response->getStatusCode(), 200);
    }

    /// Path: GET   /users/{user_id}/devices
    public function testGetUsersDevices()
    {
        if ($this->only_priority_tests) {
            $this->markTestSkipped("Running only priority tests.");
        }

        $this->load('/users/1/devices');

        $this->assertEquals($this->response_data->status, 'success');
        $this->assertEquals($this->response->getStatusCode(), 200);

        $tokens = $this->db->table('Device')->select('token')->where('user_id', 1)->where('status', 1)->get()->pluck('token')->toArray();
        $found = 0;

        foreach ($this->response_data->data->devices as $device) {
            if (in_array($device->token, $tokens)) {
                $found ++;
            }
        }
        $this->assertEquals($found, count($tokens));
    }

    /// GraphQL: { user(id: 1) { devices { token }}}
    public function testGraphQLUsersDevices()
    {
        if ($this->only_priority_tests) {
            $this->markTestSkipped("Running only priority tests.");
        }

        $this->graphql('{ user(id: 1) { devices { token }} }');

        $tokens = $this->db->table('Device')->select('token')->where('user_id', 1)->where('status', 1)->get()->pluck('token')->toArray();
        $found = 0;

        foreach ($this->response_data->data->user->devices as $device) {
            if (in_array($device->token, $tokens)) {
                $found ++;
            }
        }
        $this->assertEquals($found, count($tokens));
    }

    /// Path: POST   /users/{user_id}/devices/{token}
    public function testPostUsersDevices()
    {
        if ($this->only_priority_tests) {
            $this->markTestSkipped("Running only priority tests.");
        }
        if (!$this->write_to_db) {
            $this->markTestSkipped("Skipping as this test writes to the Database.");
        }

        $this->load('/users/1/devices/test-token-that-should-be-deleted', 'POST');

        $this->assertEquals($this->response_data->status, 'success');
        $this->assertEquals($this->response->getStatusCode(), 200);

        $tokens = $this->db->table('Device')->select('token')->where('user_id', 1)->where('status', 1)->get()->pluck('token')->toArray();
        $found = false;

        foreach ($tokens as $tok) {
            if ($tok == "test-token-that-should-be-deleted") {
                $found = true;
            }
        }
        $this->assertTrue($found);

        return $this->response_data->data->device->id;
    }

    /// Path: POST   /users/{user_id}/devices/{token}
    /**
     * @depends testPostUsersDevices
     */
    public function testDeleteUsersDevices($device_id)
    {
        if ($this->only_priority_tests) {
            $this->markTestSkipped("Running only priority tests.");
        }
        if (!$this->write_to_db) {
            $this->markTestSkipped("Skipping as this test writes to the Database.");
        }

        $this->load('/users/1/devices/test-token-that-should-be-deleted', 'DELETE');
        $this->assertEquals($this->response->getStatusCode(), 200);

        $deleted_device = $this->db->table('Device')->select('status')->where('id', $device_id)->first();
        $this->assertEquals($deleted_device->status, '0');
    }

    public function testGetUserAlerts()
    {
        if ($this->only_priority_tests) {
            $this->markTestSkipped("Running only priority tests.");
        }
        if (!$this->write_to_db) {
            $this->markTestSkipped("Skipping as this test writes to the Database.");
        }

        // Delete Binny's CPP Signing to test this...
        $user_id = 1;
        $this->db->table("UserData")->where('name', 'child_protection_policy_signed')->where('user_id', $user_id)->delete();

        $this->load("/users/$user_id/alerts", 'GET');
        $this->assertEquals($this->response->getStatusCode(), 200);
        $search_for = "CPP Not Signed";
        $found = false;
        foreach ($this->response_data->data->alerts as $alert) {
            if ($alert->name == $search_for) {
                $found = true;
            }
        }
        $this->assertTrue($found);

        // :TODO: Test Student data not entered, Teacher data not entered.
    }
}
