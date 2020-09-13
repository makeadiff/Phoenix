<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Http\Request;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use App\Models\User;
use App\Http\Controllers\UserController;

/**
 * @runInSeparateProcess
 */
class UserTest extends TestCase
{
    use WithoutMiddleware;
    
    protected $only_priority_tests = false;
    protected $write_to_db = true;

    public function testUserSearchId()
    {
        if ($this->only_priority_tests) {
            $this->markTestSkipped("Running only priority tests.");
        }

        $model = new User;
        $data = $model->search(['id' => $this->ideal_user_id]);

        $result = $data->first();
        $this->assertEquals($result->name, $this->ideal_user['name']);
        $this->assertEquals($result->center_id, $this->ideal_user['center_id']);
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

    // Risky tests. Data can change.
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

    /// See if found user have a group with main role. Risky. 
    public function testUserMainGroup() 
    {
        if($this->only_priority_tests) {
            $this->markTestSkipped("Running only priority tests.");
        }

        $model = new User;
        $data = $model->search(['user_id' => $this->ideal_user_id]);

        $result = $data->first();

        $this->assertEquals($result->id, '1');
        $found = 0;
        foreach($result->groups as $grp) {
            if($grp->main) $found = $grp->id;
        }
        $this->assertEquals($found, '24');
    }

    // Search by main role
    public function testUserSearchOnlyMainGroup() 
    {
        if($this->only_priority_tests) {
            $this->markTestSkipped("Running only priority tests.");
        }

        $model = new User;
        $data = $model->search(['group_id' => 24, 'only_main_group' => '1']);

        $found = 0;
        foreach($data as $usr) {
            foreach($usr->groups as $grp) {
                if($grp->main == "1" and $grp->id == 24) { // Check if all the returned users have the group 24 as main group.
                    $found++;
                    break;
                }
            }
        }

        $this->assertEquals(count($data), $found);
    }

    // Edit user and give center_id
    public function testUserEdit()
    {
        if ($this->only_priority_tests) {
            $this->markTestSkipped("Running only priority tests.");
        }
        if (!$this->write_to_db) {
            $this->markTestSkipped("Running only tests that don't change DB.");
        }

        $model = new User;
        $model->edit(['center_id' => 154], $this->ideal_user_id);
        $user_center_id = app('db')->table('User')->where('id', $this->ideal_user_id)->first()->center_id;
        $this->assertEquals($user_center_id, 154);
    }

    public function testUserGroupAddAndRemove()
    {
        if ($this->only_priority_tests) {
            $this->markTestSkipped("Running only priority tests.");
        }
        if (!$this->write_to_db) {
            $this->markTestSkipped("Running only tests that don't change DB.");
        }

        $model = new User;
        $hc_strat_id =  357;
        $model->addGroup($hc_strat_id, '1', $this->ideal_user_id);

        $user_main_group = app('db')->table('UserGroup')->where('user_id', $this->ideal_user_id)->where('main','1')->where('year',$this->year)->get();

        $this->assertEquals(count($user_main_group), 1); // There should be just 1 main group.
        $this->assertEquals($user_main_group->first()->group_id, $hc_strat_id);

        $model->removeGroup($hc_strat_id, $this->ideal_user_id);

        $user_main_group = app('db')->table('UserGroup')->where('user_id', $this->ideal_user_id)->where('group_id', $hc_strat_id)->where('year',$this->year)->get();

        $this->assertEquals(count($user_main_group), 0); // There should be just 1 main group.
    }

    public function testUserNewRegisteration()
    {
        if ($this->only_priority_tests) {
            $this->markTestSkipped("Running only priority tests.");
        }
        if (!$this->write_to_db) {
            $this->markTestSkipped("Running only tests that don't change DB.");
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
            'profile'   => 'teacher',
            'source'    => 'internet',
            'user_type' => 'applicant'
        );

        $request = Request::create('/users', 'POST', $user);
        $user_controller = new UserController;
        $response = $user_controller->add($request);

        $this->assertEquals(200, $response->getStatusCode());
        $json = json_decode($response->content());
        $this->assertEquals("success", $json->status);
        $user_id = $json->data->users->id;

        $inserted_user = app('db')->table('User')->where('id', $user_id)->first();
        $this->assertEquals($inserted_user->email, $email);
    }
}
