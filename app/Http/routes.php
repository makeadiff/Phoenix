<?php
use App\Models\User;
use App\Models\Group;
use App\Models\City;
use App\Models\Center;
use App\Models\Student;
use App\Models\Batch;
use App\Models\Level;
use App\Models\Donation;
use App\Models\Deposit;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;

header("Content-type: application/json");
header("Access-Control-Allow-Origin: *");

$app->get('/', function () use ($app) {
	$result = [];

	$deposit = new Deposit;
	$result = $deposit->add(142509, 1, [15461]);

	if(!$result) var_dump($deposit->errors);

	// $result = $deposit->find(748)->approve(99917);

	return JSend::success(['data' => [
		'result'	=> $result,
    	'app'		=> 'Phoenix',
    	'framework' => $app->version()
    ]]);
});

$url_prefix = 'v1';

$app->post("/$url_prefix/users", ['middleware' => 'auth.basic', 'uses' => 'UserController@add']);
$app->post("/$url_prefix/users/{user_id}", ['middleware' => 'auth.basic', 'uses' => 'UserController@edit']);
$app->post("/$url_prefix/students", ['middleware' => 'auth.basic', 'uses' => 'StudentController@add']);
$app->post("/$url_prefix/students/{student_id}", ['middleware' => 'auth.basic', 'uses' => 'StudentController@edit']);

$app->group(['prefix' => $url_prefix, 'middleware' => 'auth.basic'], function($app) {
///////////////////////////////////////////////// City Calls ////////////////////////////////////////////
$app->get('/cities', function() use($app) {
	$cities = (new City)->getAll();

	return JSend::success("All cities", array('cities' => $cities));
});

$app->get('/cities/{city_id}', function ($city_id) use ($app) {
	$city = (new City)->fetch($city_id);
   	if(!$city) return response(JSend::fail("Can't find any city with the id $city_id"), 404);

    return JSend::success("Details for city $city_id", array('city' => $city));
});

$app->get('/cities/{city_id}/users', function ($city_id) use ($app) {
	$city = (new City)->fetch($city_id);
	if(!$city) return response(JSend::fail("Can't find any city with ID $city_id"), 404);

    $users = (new User)->search(array('city_id' => $city_id));
    
    return JSend::success("List of users returned", array('users' => $users));
});

$app->get('/cities/{city_id}/teachers', function ($city_id) use ($app) {
	$city = (new City)->fetch($city_id);
	if(!$city) return response(JSend::fail("Can't find any city with ID $city_id"), 404);

	$users = (new User)->search(array('city_id' => $city_id, 'user_group' => 9));
    
    return JSend::success("List of teachers returned", array('users' => $users));
});

$app->get('/cities/{city_id}/fellows', function ($city_id) use ($app) {
	$city = (new City)->fetch($city_id);
	if(!$city) return response(JSend::fail("Can't find any city with ID $city_id"), 404);

	$users = (new User)->search(array('city_id' => $city_id, 'user_group_type' => 'fellow'));
    
    return JSend::success("List of fellows returned", array('users' => $users));
});

$app->get('/cities/{city_id}/centers', function ($city_id) use ($app) {
		$city = (new City)->fetch($city_id);
		if(!$city) return response(JSend::fail("Can't find any city with ID $city_id"), 404);

 	   	$centers = (new Center)->inCity($city_id);

	   	return JSend::success("List of centers in city '$city[name]'", array('centers' => $centers));
});

$app->get('/cities/{city_id}/students', function ($city_id) use ($app) {
	$city = (new City)->fetch($city_id);
	if(!$city) return response(JSend::fail("Can't find any city with ID $city_id"), 404);

	$student = new Student;
    $students = $student->search(array('city_id' => $city_id));
    
    return JSend::success("List of students in $city[name]", array('students' => $students));
});

///////////////////////////////////////////////////// Groups /////////////////////////////////////////////////
$app->get('/groups', function(Request $request) use ($app) {
	$search_fields = ['id', 'name','type','vertical_id'];
	$search = [];
	foreach ($search_fields as $key) {
		if(!$request->input($key)) continue;

		$search[$key] = $request->input($key);
	}

	$groups = Group::search($search);

	return JSend::success("User Groups", array('groups' => $groups));
});

$app->get('/groups/{group_id}', function($group_id) use ($app) {
	$group = (new Group)->fetch($group_id);
	if(!$group) return response(JSend::fail("Can't find any group with ID $group_id"), 404);

	return JSend::success("User Group: $group_id", array('group' => $group));
});

///////////////////////////////////////////////////// Centers /////////////////////////////////////////////////
$app->get('/centers', function(Request $request) use ($app) {
	$search_fields = ['id', 'name', 'city_id'];
	$search = [];
	foreach ($search_fields as $key) {
		if(!$request->input($key)) continue;

		$search[$key] = $request->input($key);
	}

	$centers = Center::search($search);

	return JSend::success("Centers", array('centers' => $centers));
});

$app->get('/centers/{center_id}', function($center_id) use ($app) {
	$center = (new Center)->fetch($center_id);
	if(!$center) return response(JSend::fail("Can't find any center with ID $center_id"), 404);

	return JSend::success("Center ID : $center_id", array('center' => $center));
});

$app->get('/centers/{center_id}/teachers', function($center_id) use ($app) {
	$center = (new Center)->fetch($center_id);
	if(!$center) return response(JSend::fail("Can't find any center with ID $center_id"), 404);

	$user = new User;
	$teachers = $user->search(['center_id' => $center_id]);

	return JSend::success("Teachers in Center $center_id", array('teachers' => $teachers));
});

$app->get('/centers/{center_id}/students', function ($center_id) use ($app) {
	$center = (new Center)->fetch($center_id);
	if(!$center) return response(JSend::fail("Can't find any center with ID $center_id"), 404);

	$student = new Student;
    $students = $student->search(array('center_id' => $center_id));
    
    return JSend::success("List of students in $center[name]", array('students' => $students));
});

$app->get('/centers/{center_id}/batches', function ($center_id) use ($app) {
	$center = (new Center)->fetch($center_id);
	if(!$center) return response(JSend::fail("Can't find any center with ID $center_id"), 404);
 
    $batches = (new Batch)->search(['center_id' => $center_id]);
    return JSend::success("List of batches in $center[name]", array('batches' => $batches));
});

$app->get('/centers/{center_id}/levels', function ($center_id) use ($app) {
	$center = (new Center)->fetch($center_id);
	if(!$center) return response(JSend::fail("Can't find any center with ID $center_id"), 404);
 
    $levels = (new Level)->search(['center_id' => $center_id]);
    return JSend::success("List of levels in $center[name]", array('levels' => $levels));
});

////////////////////////////////////////////////////////// Batches ///////////////////////////////////////////
$app->get('/batches/{batch_id}', function($batch_id) use ($app) {
	$batch = (new Batch)->fetch($batch_id);
	if(!$batch) return response(JSend::fail("Can't find any batch with ID $batch_id"), 404);

	return JSend::success("Batch ID : $batch_id", array('batch' => $batch));
});
$app->get('/batches/{batch_id}/teachers', function($batch_id) use ($app) {
	$batch = (new Batch)->fetch($batch_id);
	if(!$batch) return response(JSend::fail("Can't find any batch with ID $batch_id"), 404);

	$teachers = (new User)->search(['batch_id' => $batch_id]);

	return JSend::success("Teachers in batch $batch_id", array('teachers' => $teachers));
});
$app->get('/batches/{batch_id}/levels', function($batch_id) use ($app) {
	$batch = (new Batch)->fetch($batch_id);
	if(!$batch) return response(JSend::fail("Can't find any batch with ID $batch_id"), 404);

	$levels = (new Level)->search(['batch_id' => $batch_id]);

	return JSend::success("Levels in batch $batch_id", array('levels' => $levels));
});

////////////////////////////////////////////////////////// Levels ///////////////////////////////////////////
$app->get('/levels/{level_id}', function($level_id) use ($app) {
	$level = (new Level)->fetch($level_id);
	if(!$level) return response(JSend::fail("Can't find any level with ID $level_id"), 404);

	return JSend::success("Level ID : $level_id", array('level' => $level));
});
$app->get('/levels/{level_id}/students', function($level_id) use ($app) {
	$level = (new Level)->fetch($level_id);
	if(!$level) return response(JSend::fail("Can't find any level with ID $level_id"), 404);

	$students = (new Student)->search(['level_id' => $level_id]);

	return JSend::success("Students in Level $level_id", array('students' => $students));
});
$app->get('/levels/{level_id}/batches', function($level_id) use ($app) {
	$level = (new Level)->fetch($level_id);
	if(!$level) return response(JSend::fail("Can't find any level with ID $level_id"), 404);

	$batches = (new Batch)->search(['level_id' => $level_id]);

	return JSend::success("Levels in batch $level_id", array('batches' => $batches));
});
///////////////////////////////////////////////////////// User Calls //////////////////////////////////////////////
$app->get('/users', function(Request $request) use ($app) {
	$search_fields = ['name','phone','email','mad_email','group_id','group_in','city_id','user_type','center_id'];
	$search = [];
	foreach ($search_fields as $key) {
		if(!$request->input($key)) continue;

		if($key == 'group_id') {
			$search['user_group'] = array($request->input('group_id'));
		} elseif ($key == 'group_in') {
			$search['user_group'] = explode(",", $request->input('group_in'));
		} else {
			$search[$key] = $request->input($key);
		}
	}

	$user = new User;
	$data = $user->search($search);

	return JSend::success("Search Results", array('users' => $data));
});

$app->get('/users/login', function(Request $request) use ($app) {
	$user = new User;
	$data = $user->login($request->input('email'), $request->input('password'));

	if(!$data) {
		$error = "Invalid username/password";
		if(count($user->errors)) $error = implode(", ", $user->errors);
		
		return response(JSend::fail($error), 400);
	}

	return JSend::success("Welcome back, $data[name]", array('user' => $data));
});

$app->get('/users/{user_id}', function($user_id) use ($app) {
	$user = new User;
	$details = $user->fetch($user_id);

	if(!$details) {
		return response(JSend::error("Can't find user with user id '$user_id'"), 404);
	}

	return JSend::success("User details for {$details->name}", array('user' => $details));
});

$app->get('/users/{user_id}/credit', function($user_id) use ($app) {
	$user = new User;
	$info = $user->fetch($user_id);
	if(!$info) return response(JSend::error("Can't find user with user id '$user_id'"), 404);

	$credit = intval($info->credit);
	return JSend::success("Credits for user $user_id", array('credit' => $credit));
});
$app->post('/users/{user_id}/credit', function($user_id, Request $request) use ($app) {
	$user = new User;
	$info = $user->fetch($user_id);
	if(!$info) return response(JSend::error("Can't find user with user id '$user_id'"), 404);

	$validator = \Validator::make($request->all(), [
		'credit'      			=> 'required|numeric',
        'updated_by_user_id'    => 'required|numeric|exists:User,id',
        'reason' 				=> 'required'
	]);

    if ($validator->fails()) {
        return response(JSend::fail("Unable to edit the credit - errors in input", $validator->errors()), 400);
    }

	$user->find($user_id)->editCredit($request->input('credit'), $request->input('updated_by_user_id'), $request->input('reason'));
	
	return JSend::success("Edit the credits for user $user_id", array('credit' => $request->input('credit')));
});

// $app->post('/users','UserController@add');
// $app->post('/users/{user_id}','UserController@edit');
$app->delete('/users/{user_id}', function($user_id) use ($app) {
	$user = new User;
	$info = $user->fetch($user_id);
	if(!$info) return response(JSend::error("Can't find user with user id '$user_id'"), 404);

	$info = $user->remove($user_id);

	return JSend::success("User deleted successfully", array('user' => $info));
});

$app->get('/users/{user_id}/groups', function($user_id) use ($app) {
	$user = new User;
	$info = $user->fetch($user_id);
	if(!$info) return response(JSend::error("Can't find user with user id '$user_id'"), 404);

	return JSend::success("User Groups for user $user_id", array('groups' => $info->groups));
});

$app->post('/users/{user_id}/groups/{group_id}', function($user_id, $group_id) use ($app) {
	$user = new User;
	$info = $user->fetch($user_id);
	if(!$info) return response(JSend::error("Can't find user with user id '$user_id'"), 404);

	$groups = $user->find($user_id)->addGroup($group_id);
	if(!$groups) return response(JSend::fail("User already has the given group"), 400);

	return JSend::success("Added user to the given group.", array('groups' => $groups));
});

$app->delete('/users/{user_id}/groups/{group_id}', function($user_id, $group_id) use ($app) {
	$user = new User;
	$info = $user->fetch($user_id);
	if(!$info) return response(JSend::error("Can't find user with user id '$user_id'"), 404);

	$groups = $user->find($user_id)->removeGroup($group_id);
	if(!$groups) return response(JSend::fail("User don't have the given group"), 400);

	return JSend::success("Removed user from the given group.", array('groups' => $groups));
});

///////////////////////////////////////////////////////// Student Calls //////////////////////////////////////////////
$app->get('/students', function(Request $request) use ($app) {
	$search_fields = ['name','birthday', 'city_id','sex','center_id'];
	$search = [];
	foreach ($search_fields as $key) {
		if(!$request->input($key)) continue;
		$search[$key] = $request->input($key);
	}

	$student = new Student;
	$data = $student->search($search);

	return JSend::success("Search Results", array('students' => $data));
});
$app->delete('/students/{student_id}', function($student_id) use ($app) {
	$student = new Student;
	$info = $student->fetch($student_id);
	if(!$info) return response(JSend::error("Can't find student with id '$student_id'"), 404);

	$info = $student->remove($student_id);

	return JSend::success("User deleted successfully", array('student' => $info));
});

$app->get('/students/{student_id}', function($student_id) use ($app) {
	$student = new Student;
	$details = $student->fetch($student_id);

	if(!$details) return response(JSend::error("Can't find student with id '$student_id'"), 404);

	return JSend::success("Student details for {$details->name}", array('student' => $details));
});
// $app->post('/students','StudentController@add');
// $app->post('/students/{student_id}','StudentController@edit');

/////////////////////////////////////////////// Donations ///////////////////////////////////////////////////////
$app->get('/donations', function(Request $request) {
	$search_fields = ['deposit_status_in','deposit_status','approver_id','id','city_id','amount','status','fundraiser_user_id','updated_by_user_id', 'include_deposit_info', 'deposited'];
	$search = [];
	foreach ($search_fields as $key) {
		if(!$request->input($key)) continue;

		if ($key == 'deposit_status_in') {
			$search['deposit_status_in'] = explode(",", $request->input('deposit_status_in'));
		} else {
			$search[$key] = $request->input($key);
		}
	}

	$donation = new Donation;
	$data = $donation->search($search);

	return JSend::success("Donations", ['donations' => $data]);
});

$app->post('/donations', function(Request $request) {
	$donation = new Donation;
	$donation = $donation->add($request->all());

	if($donation) return JSend::success("Donation inserted succesfully : Donation ID '{$donation->id}'", array("donation" => $donation));
	else return JSend::error("Failure in inserting donation at server. Try again after some time.", $donation->errors);
});

$app->get('/donations/{donation_id}', function($donation_id) {
	$donation = new Donation;
	$data = $donation->fetch($donation_id);

	if(!$data) return response(JSend::fail("Can't find any donations with the ID $donation_id"), 404);

	return JSend::success("Donation Details for $donation_id", ['donation' => $data]);
});

$app->delete('/donations/{donation_id}', function($donation_id) {
	$donation = new Donation;
	$data = $donation->fetch($donation_id);

	if(!$data) return response(JSend::fail("Can't find any donations with the ID $donation_id"), 404);

	$donation->remove($donation_id);

	return ""; // JSend::success("Donation '$donation_id' deleted.", ['donation' => $data]); // DELETE return should be empty.
});

$app->get('/users/{user_id}/donations', function($fundraiser_user_id) {
	$donation = new Donation;
	$data = $donation->search(['fundraiser_user_id' => $fundraiser_user_id]);

	return JSend::success("Donations", ['donations' => $data]);
});

///////////////////////////// Deposits ////////////////////////
$app->post('/deposits', function(Request $request) {
	$deposit = new Deposit;
	$deposit_info = $deposit->add($request->input('collected_from_user_id'), $request->input('given_to_user_id'), $request->input('donation_ids'));

	if(!$deposit_info) {
		return response(JSend::fail("Error making the deposit", $deposit->errors), 400);
	}
	return JSend::success("Made the deposit", ['deposit' => $deposit_info]);
});

$app->get('/deposits', function(Request $request) {
	$search_fields = ['id', 'status', 'status_in', 'reviewer_id'];
	$search = [];
	foreach ($search_fields as $key) {
		if(!$request->input($key)) continue;

		$search[$key] = $request->input($key);
	}

	$deposit = new Deposit;
	$data = $deposit->search($search);

	return JSend::success("Deposits matching criteria", ['deposits' => $data]);
});

$app->post('/deposits/{deposit_id}', function ($deposit_id, Request $request) {
	$reviewer_id = $request->input('reviewer_id');
	$status = $request->input('status');

	$deposit = new Deposit;
	$data = false;
	if($status == 'approve')
		$data = $deposit->find($deposit_id)->approve($reviewer_id);
	else if($status == 'reject')
		$data = $deposit->find($deposit_id)->reject($reviewer_id);

	if(!$data) return response(JSend::fail("Error approving deposit.", $depsit->errors), 400);

	return JSend::success("Deposit updated", ['deposit' => $data]);
});

});
