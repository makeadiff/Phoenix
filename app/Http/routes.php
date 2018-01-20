<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/

// Responses are in JSend format - slightly modified. http://labs.omniti.com/labs/jsend
use App\Models\User;
use App\Models\Group;
use App\Models\City;
use App\Models\Center;
use App\Models\Student;
use App\Models\Batch;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;

header("Content-type: application/json");
header("Access-Control-Allow-Origin: *");

$app->get('/', function () use ($app) {
    return $app->version();
});

///////////////////////////////////////////////// City Calls ////////////////////////////////////////////
$app->get('/cities', function() use($app) {
	$cities = City::getAll();

	return JSend::success("All cities", array('cities' => $cities));
});

$app->get('/cities/{city_id}', function ($city_id) use ($app) {
	$city = City::fetch($city_id);
   	if(!$city) return response(JSend::fail("Can't find any city with the id $city_id"), 404);

    return JSend::success("Details for city $city_id", array('city' => $city));
});

$app->get('/cities/{city_id}/users', function ($city_id) use ($app) {
	$city = City::fetch($city_id);
	if(!$city) return response(JSend::fail("Can't find any city with ID $city_id"), 404);

	$user = new User;
    $users = $user->search(array('city_id' => $city_id));
    
    return JSend::success("List of users returned", array('users' => $users));
});

$app->get('/cities/{city_id}/teachers', function ($city_id) use ($app) {
	$city = City::fetch($city_id);
	if(!$city) return response(JSend::fail("Can't find any city with ID $city_id"), 404);

	$user = new User;
    $users = $user->search(array('city_id' => $city_id, 'user_group' => 9));
    
    return JSend::success("List of teachers returned", array('users' => $users));
});

$app->get('/cities/{city_id}/fellows', function ($city_id) use ($app) {
	$city = City::fetch($city_id);
	if(!$city) return response(JSend::fail("Can't find any city with ID $city_id"), 404);

	$user = new User;
    $users = $user->search(array('city_id' => $city_id, 'user_group_type' => 'fellow'));
    
    return JSend::success("List of fellows returned", array('users' => $users));
});

$app->get('/cities/{city_id}/centers', function ($city_id) use ($app) {
		$city = City::fetch($city_id);
		if(!$city) return response(JSend::fail("Can't find any city with ID $city_id"), 404);
 	   	$centers = Center::getAllInCity($city_id);

	   	return JSend::success("List of centers in city '$city[name]'", array('centers' => $centers));
});

$app->get('/cities/{city_id}/students', function ($city_id) use ($app) {
	$city = City::fetch($city_id);
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
	$group = Group::fetch($group_id);
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

////////////////////////////////////////////////////////// Batches ///////////////////////////////////////////
$app->get('/batches/{batch_id}', function($batch_id) use ($app) {
	$batch = (new Batch)->fetch($batch_id);
	if(!$batch) return response(JSend::fail("Can't find any batch with ID $batch_id"), 404);

	return JSend::success("Batch ID : $batch_id", array('batch' => $batch));
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
		return response(JSend::fail("Invalid username/password"), 400);
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

$app->post('/users','UserController@add');
$app->post('/users/{user_id}','UserController@edit');
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

