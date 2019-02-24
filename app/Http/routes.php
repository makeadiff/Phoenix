<?php
use App\Models\User;
use App\Models\Group;
use App\Models\Vertical;
use App\Models\City;
use App\Models\Center;
use App\Models\Student;
use App\Models\Batch;
use App\Models\Level;
use App\Models\Donation;
use App\Models\Deposit;
use App\Models\Event;
use App\Models\Data;
use App\Models\Notification;
use App\Models\Contact;

use App\Http\Controllers\UserController;
use Illuminate\Http\Request;

header("Content-type: application/json");
header("Access-Control-Allow-Origin: *");

$app->get('/', function () use ($app) {
	$result = [];

	return JSend::success(['data' => [
		'result'	=> $result,
    	'app'		=> 'Phoenix',
    	'framework' => $app->version()
    ]]);
});

$url_prefix = 'v1';

$app->group(['prefix' => $url_prefix, 'middleware' => 'auth.basic'], function($app) {

///////////////////////////////////////////////// City Calls ////////////////////////////////////////////
$app->get('/cities', function() use($app) {
	$cities = (new City)->getAll();

	return JSend::success("All cities", ['cities' => $cities]);
});

$app->get('/cities/{city_id}', function ($city_id) use ($app) {
	$city = (new City)->fetch($city_id);
   	if(!$city) return response(JSend::fail("Can't find any city with the id $city_id"), 404);

    return JSend::success("Details for city $city_id", ['cities' => $city]);
});

$app->get('/cities/{city_id}/users', function ($city_id) use ($app) {
	$city = (new City)->fetch($city_id);
	if(!$city) return response(JSend::fail("Can't find any city with ID $city_id"), 404);

    $users = (new User)->search(array('city_id' => $city_id));
    
    return JSend::success("List of users returned", ['users' => $users]);
});

$app->get('/cities/{city_id}/teachers', function ($city_id) use ($app) {
	$city = (new City)->fetch($city_id);
	if(!$city) return response(JSend::fail("Can't find any city with ID $city_id"), 404);

	$users = (new User)->search(array('city_id' => $city_id, 'user_group' => 9));
    
    return JSend::success("List of teachers returned", ['users' => $users]);
});

$app->get('/cities/{city_id}/fellows', function ($city_id) use ($app) {
	$city = (new City)->fetch($city_id);
	if(!$city) return response(JSend::fail("Can't find any city with ID $city_id"), 404);

	$users = (new User)->search(array('city_id' => $city_id, 'user_group_type' => 'fellow'));
    
    return JSend::success("List of fellows returned", ['users' => $users]);
});

$app->get('/cities/{city_id}/centers', function ($city_id) use ($app) {
		$city = (new City)->fetch($city_id);
		if(!$city) return response(JSend::fail("Can't find any city with ID $city_id"), 404);

 	   	$centers = (new Center)->inCity($city_id);

	   	return JSend::success("List of centers in city '$city[name]'", ['centers' => $centers]);
});

$app->get('/cities/{city_id}/students', function ($city_id) use ($app) {
	$city = (new City)->fetch($city_id);
	if(!$city) return response(JSend::fail("Can't find any city with ID $city_id"), 404);

	$student = new Student;
    $students = $student->search(array('city_id' => $city_id));
    
    return JSend::success("List of students in $city[name]", ['students' => $students]);
});

///////////////////////////////////////////////////// Groups /////////////////////////////////////////////////
$app->get('/groups', function(Request $request) use ($app) {
	$search = $request->only('id', 'name','type','vertical_id');
	$groups = Group::search($search);

	return JSend::success("User Groups", ['groups' => $groups]);
});

$app->get('/groups/{group_id}', function($group_id) use ($app) {
	$group = (new Group)->fetch($group_id);
	if(!$group) return response(JSend::fail("Can't find any group with ID $group_id"), 404);

	return JSend::success("User Group: $group_id", ['groups' => $group]);
});

$app->get('/verticals', function() use ($app) {
	$verticals = Vertical::getAll();
	return JSend::success("Verticals", ['verticals' => $verticals]);
});

$app->get('/projects', function() use ($app) {
	$projects = ['1' => 'Ed Support', '2' => 'Foundation'];
	return JSend::success("Verticals", ['projects' => $projects]);
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

	return JSend::success("Centers", ['centers' => $centers]);
});

$app->get('/centers/{center_id}', function($center_id) use ($app) {
	$center = (new Center)->fetch($center_id);
	if(!$center) return response(JSend::fail("Can't find any center with ID $center_id"), 404);

	return JSend::success("Center ID : $center_id", ['centers' => $center]);
});

$app->get('/centers/{center_id}/teachers', function($center_id) use ($app) {
	$center = (new Center)->fetch($center_id);
	if(!$center) return response(JSend::fail("Can't find any center with ID $center_id"), 404);

	$user = new User;
	$teachers = $user->search(['center_id' => $center_id]);

	return JSend::success("Teachers in Center $center_id", ['users' => $teachers]);
});

$app->get('/centers/{center_id}/students', function ($center_id) use ($app) {
	$center = (new Center)->fetch($center_id);
	if(!$center) return response(JSend::fail("Can't find any center with ID $center_id"), 404);

	$student = new Student;
    $students = $student->search(array('center_id' => $center_id));
    
    return JSend::success("List of students in $center[name]", ['students' => $students]);
});

$app->get('/centers/{center_id}/batches', function ($center_id, Request $request) use ($app) {
	$center = (new Center)->fetch($center_id);
	if(!$center) return response(JSend::fail("Can't find any center with ID $center_id"), 404);

	$project_id = $request->input('project_id');
	if(!$project_id) $project_id = 1;

    $batches = (new Batch)->search(['center_id' => $center_id, 'project_id' => $project_id]);
    return JSend::success("List of batches in $center[name]", ['batches' => $batches]);
});

$app->get('/centers/{center_id}/levels', function ($center_id, Request $request) use ($app) {
	$center = (new Center)->fetch($center_id);
	if(!$center) return response(JSend::fail("Can't find any center with ID $center_id"), 404);

	$project_id = $request->input('project_id');
	if(!$project_id) $project_id = 1;
 
    $levels = (new Level)->search(['center_id' => $center_id, 'project_id' => $project_id]);
    return JSend::success("List of levels in $center[name]", ['levels' => $levels]);
});

////////////////////////////////////////////////////////// Batches ///////////////////////////////////////////
$app->get('/batches/{batch_id}', function($batch_id) use ($app) {
	$batch = (new Batch)->fetch($batch_id, false);
	if(!$batch) return response(JSend::fail("Can't find any batch with ID $batch_id"), 404);

	return JSend::success("Batch ID : $batch_id", ['batches' => $batch]);
});
$app->get('/batches/{batch_id}/teachers', function($batch_id) use ($app) {
	$batch = (new Batch)->fetch($batch_id, false);
	if(!$batch) return response(JSend::fail("Can't find any batch with ID $batch_id"), 404);

	$teachers = (new User)->search(['batch_id' => $batch_id]);

	return JSend::success("Teachers in batch $batch_id", ['teachers' => $teachers]);
});
$app->get('/batches/{batch_id}/levels', function($batch_id) use ($app) {
	$batch = (new Batch)->fetch($batch_id, false);
	if(!$batch) return response(JSend::fail("Can't find any batch with ID $batch_id"), 404);

	$levels = (new Level)->search(['batch_id' => $batch_id]);

	return JSend::success("Levels in batch $batch_id", ['levels' => $levels]);
});

////////////////////////////////////////////////////////// Levels ///////////////////////////////////////////
$app->get('/levels/{level_id}', function($level_id) use ($app) {
	$level = (new Level)->fetch($level_id, false);
	if(!$level) return response(JSend::fail("Can't find any level with ID $level_id"), 404);

	return JSend::success("Level ID : $level_id", ['levels' => $level]);
});
$app->get('/levels/{level_id}/students', function($level_id) use ($app) {
	$level = (new Level)->fetch($level_id, false);
	if(!$level) return response(JSend::fail("Can't find any level with ID $level_id"), 404);

	$students = (new Student)->search(['level_id' => $level_id]);

	return JSend::success("Students in Level $level_id", ['students' => $students]);
});
$app->get('/levels/{level_id}/batches', function($level_id) use ($app) {
	$level = (new Level)->fetch($level_id, false);
	if(!$level) return response(JSend::fail("Can't find any level with ID $level_id"), 404);

	$batches = (new Batch)->search(['level_id' => $level_id]);

	return JSend::success("Levels in batch $level_id", ['batches' => $batches]);
});


/////////////////////////////////////////////////// Class ////////////////////////////////////////////////////
$app->get('/classes/{class_id}/data/{name}', function($class_id, $data_name) {
	$data = (new Data)->get('Class', $class_id, $data_name)->getData();
	if(!$data) return response(JSend::fail("Can't find any Data with class ID $class_id"), 404);

	return JSend::success("Data '$data_name' for class $class_id", ['data' => $data]);
});

$app->post('/classes/{class_id}/data/{name}', function(Request $request, $class_id, $data_name) {
	$data = $request->input('data');

	if($data) (new Data)->get('Class', $class_id, $data_name)->setData($data);

	return JSend::success("Data '$data_name' for class $class_id", ['data' => $data]);
});

////////////////////////////////////////////////// Auth //////////////////////////////////////////////////////
$app->addRoute(['POST','GET'], '/users/login', function(Request $request) use ($app) {
	$user = new User;
	$phone_or_email = $request->input('phone');
	if(!$phone_or_email) $phone_or_email = $request->input('email');
	if(!$phone_or_email) $phone_or_email = $request->input('identifier');

	if($request->input('password'))
		$data = $user->login($phone_or_email, $request->input('password'));
	elseif($request->input('auth_token')) 
		$data = $user->login($phone_or_email, false, $request->input('auth_token'));

	if(!$data) {
		$error = "Invalid username/password";
		if(count($user->errors)) $error = implode(", ", $user->errors);
		
		return response(JSend::fail($error), 400);
	}

	return JSend::success("Welcome back, $data[name]", ['users' => $data]);
});

///////////////////////////////////////////////////////// User Calls //////////////////////////////////////////////
// These calls are commented intentionally - the actual calls are at the end of this file. These lines are here to denote that there are more routes.
// $app->post('/users','UserController@add');
// $app->post('/users/{user_id}','UserController@edit');

$app->get('/users', function(Request $request) use ($app) {
	$search_fields = ['id','identifier', 'name','phone','email','mad_email','group_id','group_in','vertical_id','city_id','user_type','center_id','project_id', 'not_user_type'];
	$search = [];
	foreach ($search_fields as $key) {
		if(!$request->input($key)) continue;

		if($key == 'group_id') {
			$search['user_group'] = [$request->input('group_id')];
		} elseif ($key == 'group_in') {
			$search['user_group'] = explode(",", $request->input('group_in'));

		} elseif ($key == 'not_user_type') {
			$search['not_user_type'] = explode(",", $request->input('not_user_type'));
		} else {
			$search[$key] = $request->input($key);
		}
	}
	if(!isset($search['project_id'])) $search['project_id'] = 1;

	$user = new User;
	$data = $user->search($search);

	return JSend::success("Search Results", ['users' => $data]);
});

$app->get('/users/{user_id}', function($user_id) use ($app) {
	$user = new User;
	$details = $user->fetch($user_id);

	if(!$details) {
		return response(JSend::error("Can't find user with user id '$user_id'"), 404);
	}

	return JSend::success("User details for {$details->name}", ['users' => $details]);
});

$app->get('/users/{user_id}/credit', function($user_id) use ($app) {
	$user = new User;
	$info = $user->fetch($user_id);
	if(!$info) return response(JSend::error("Can't find user with user id '$user_id'"), 404);

	$credit = intval($info->credit);
	return JSend::success("Credits for user $user_id", ['credit' => $credit]);
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
	
	return JSend::success("Edit the credits for user $user_id", ['credit' => $request->input('credit')]);
});

$app->delete('/users/{user_id}', function($user_id) use ($app) {
	$user = new User;
	$info = $user->fetch($user_id, false);
	if(!$info) return response(JSend::error("Can't find user with user id '$user_id'"), 404);

	$info = $user->remove($user_id);

	return ""; // JSend::success("User deleted successfully", ['user' => $info]);
});

$app->get('/users/{user_id}/groups', function($user_id) use ($app) {
	$user = new User;
	$info = $user->fetch($user_id);
	if(!$info) return response(JSend::error("Can't find user with user id '$user_id'"), 404);

	return JSend::success("User Groups for user $user_id", ['groups' => $info->groups]);
});

$app->post('/users/{user_id}/groups/{group_id}', function($user_id, $group_id) use ($app) {
	$user = new User;
	$info = $user->fetch($user_id);
	if(!$info) return response(JSend::error("Can't find user with user id '$user_id'"), 404);

	$groups = $user->find($user_id)->addGroup($group_id);
	if(!$groups) return response(JSend::fail("User already has the given group"), 400);

	return JSend::success("Added user to the given group.", ['groups' => $groups]);
});

$app->delete('/users/{user_id}/groups/{group_id}', function($user_id, $group_id) use ($app) {
	$user = new User;
	$info = $user->fetch($user_id);
	if(!$info) return response(JSend::error("Can't find user with user id '$user_id'"), 404);

	$groups = $user->find($user_id)->removeGroup($group_id);
	if(!$groups) return response(JSend::fail("User don't have the given group"), 400);

	return ""; // JSend::success("Removed user from the given group.", ['groups' => $groups]);
});

//////////////////////////////////////////////////////// Contacts /////////////////////////////////
$app->post('/applicants', function (Request $request) {
	$contact = new Contact;
	$data = $request->all();
	$data['is_applicant'] = 1;

	$status = $contact->add($data);
	if(!$status) {
		return JSend::error("Could not create contact - errors in input", $contact->errors);
	}
	return JSend::success("Added the applicant successfully", ['applicant' => $status]);
});

$app->post('/contacts', function (Request $request) {
	$contact = new Contact;
	$data = $request->all();
	if(!isset($data['is_applicant'])) $data['is_applicant'] = 0;
	if(!isset($data['is_subscribed'])) $data['is_subscribed'] = 0;
	if(!isset($data['is_care_collective'])) $data['is_care_collective'] = 0;

	$status = $contact->add($data);
	if(!$status) {
		return JSend::error("Could not create contact - errors in input", $contact->errors);
	}
	return JSend::success("Added the contact successfully", ['contact' => $status]);
});
///////////////////////////////////////////////////////// Student Calls //////////////////////////////////////////////
// These calls are commented intentionally - the actual calls are at the end of this file. These lines are here to denote that there are more routes.
// $app->post('/students','StudentController@add');
// $app->post('/students/{student_id}','StudentController@edit');

$app->get('/students', function(Request $request) use ($app) {
	$search_fields = ['name','birthday', 'city_id','sex','center_id'];
	$search = [];
	foreach ($search_fields as $key) {
		if(!$request->input($key)) continue;
		$search[$key] = $request->input($key);
	}

	$student = new Student;
	$data = $student->search($search);

	return JSend::success("Search Results", ['students' => $data]);
});
$app->delete('/students/{student_id}', function($student_id) use ($app) {
	$student = new Student;
	$info = $student->fetch($student_id);
	if(!$info) return response(JSend::error("Can't find student with id '$student_id'"), 404);

	$info = $student->remove($student_id);

	return ""; // JSend::success("User deleted successfully", ['student' => $info]);
});

$app->get('/students/{student_id}', function($student_id) use ($app) {
	$student = new Student;
	$details = $student->fetch($student_id);

	if(!$details) return response(JSend::error("Can't find student with id '$student_id'"), 404);

	return JSend::success("Student details for {$details->name}", ['student' => $details]);
});

/////////////////////////////////////////////// Donations ///////////////////////////////////////////////////////
$app->get('/donations', function(Request $request) {
	$search_fields = ['deposit_status_in','deposit_status','approver_user_id','id','city_id','amount','status','fundraiser_user_id','updated_by_user_id', 'include_deposit_info', 'deposited', 'from', 'to'];
	$search = [];
	foreach ($search_fields as $key) {
		if(!$request->input($key)) continue;

		if ($key == 'deposit_status_in') {
			$search['deposit_status_in'] = explode(",", $request->input('deposit_status_in'));

		// Specific bolean cases. So that we can use the keyworld 'false' in the URL. Not really required, but looks slightly better this way.
		} elseif ($key == 'include_deposit_info' or $key == 'deposited') {
			$input = $request->input($key);

			if(strtolower($input) == 'false') $value = false;
			else $value = (boolean) $value;

			$search[$key] = $value;

		// Everything else.
		} else {
			$search[$key] = $request->input($key);
		}
	}

	$donation = new Donation;
	$data = $donation->search($search);

	return JSend::success("Donations", ['donations' => $data]);
});

$app->post('/donations', function(Request $request) {
	$donation_model = new Donation;
	$donation = $donation_model->add($request->all());

	if($donation) return JSend::success("Donation inserted succesfully : Donation ID '{$donation->id}'", ["donation" => $donation]);
	else return JSend::error("Failure in inserting donation at server. Try again after some time.", $donation_model->errors);
});

$app->post('/donations/validate', function(Request $request) {
	$donation = new Donation;
	$result = $donation->validate($request->all());

	if($result) return JSend::success("Validated successfully");
	else return JSend::error("Validation error");
});
$app->get('/donations/{donation_id}', function($donation_id) {
	$donation = new Donation;
	$data = $donation->fetch($donation_id);

	if(!$data) return response(JSend::fail("Can't find any donations with the ID $donation_id"), 404);

	return JSend::success("Donation Details for $donation_id", ['donation' => $data]);
});

$app->delete('/donations/{donation_id}', function($donation_id) {
	if(!$donation_id) return response(JSend::fail("Invalid donaiton ID - $donation_id"), 404);

	$donation = new Donation;
	$data = $donation->fetch($donation_id);

	if(!$data) return response(JSend::fail("Can't find any donations with the ID $donation_id"), 404);

	$donation->remove($donation_id);

	return ""; // JSend::success("Donation '$donation_id' deleted.", ['donation' => $data]); // DELETE return should be empty.
});

$app->get('/users/{user_id}/donations', function(Request $request, $fundraiser_user_id) {
	$search_fields = ['from', 'to', 'amount'];
	$search = ['fundraiser_user_id' => $fundraiser_user_id];

	foreach ($search_fields as $key) {
		if(!$request->input($key)) continue;

		$search[$key] = $request->input($key);
	}

	$donation = new Donation;
	$data = $donation->search($search);

	return JSend::success("Donations", ['donations' => $data]);
});

///////////////////////////// Deposits ////////////////////////
$app->post('/deposits', function(Request $request) {
	$deposit = new Deposit;
	$donation_ids = [];
	if($request->input('donation_ids')) {
		$donation_ids = $request->input('donation_ids');
		if(!is_array($donation_ids)) $donation_ids = explode(",", $donation_ids);
	}
	$deposit_info = $deposit->add($request->input('collected_from_user_id'), $request->input('given_to_user_id'), $donation_ids, $request->input('deposit_information'));

	if(!$deposit_info) {
		return response(JSend::fail("Error making the deposit", $deposit->errors), 400);
	}
	return JSend::success("Made the deposit", ['deposit' => $deposit_info]);
});

$app->get('/deposits', function(Request $request) {
	$search_fields = ['id', 'status', 'status_in', 'reviewer_user_id'];
	$search = [];
	foreach ($search_fields as $key) {
		if(!$request->input($key)) continue;

		$search[$key] = $request->input($key);
	}

	if(!count($search)) {
		return response(JSend::fail("Please provide some search parameters", ["Please provide some search parameters"]), 404);
	}

	$deposit = new Deposit;
	$data = $deposit->search($search);

	return JSend::success("Deposits matching criteria", ['deposits' => $data]);
});

$app->post('/deposits/{deposit_id}', function ($deposit_id, Request $request) {
	$reviewer_user_id = $request->input('reviewer_user_id');
	$status = $request->input('status');

	$deposit = new Deposit;
	$given_deposit = $deposit->find($deposit_id);

	if(!$given_deposit) return response(JSend::fail("Can't find any deposit with the given id.", $deposit->errors), 404);

	$data = false;
	if($status == 'approved') {
		$data = $given_deposit->approve($reviewer_user_id);

	} else if($status == 'rejected') {
		$data = $given_deposit->reject($reviewer_user_id);

	} else return response(JSend::error("Status should be 'approved' or 'rejected'"), 400);

	if(!$data) {
		return response(JSend::fail("Error approving deposit.", $given_deposit->errors), 400);
	}

	return JSend::success("Deposit updated", ['deposit' => $data]);
});


////////////////////////////////// Events ////////////////////////////////
// These calls are commented intentionally - the actual calls are at the end of this file. These lines are here to denote that there are more routes.
// $app->post('/events','EventController@add');
// $app->post('/events/{event_id}','EventController@edit');

$app->get('/events', function(Request $request) use ($app) {
	$search_fields = ['id', 'name', 'description', 'starts_on', 'place', 'city_id', 'event_type_id', 'created_by_user_id', 'status'];
	$search = [];
	foreach ($search_fields as $key) {
		if(!$request->input($key)) continue;

		$search[$key] = $request->input($key);
	}

	$event = new Event;
	$events = $event->search($search);

	return JSend::success("Events", ['events' => $events]);
});

$app->get('/events/{event_id}', function($event_id) use($app) {
	$event = new Event;

	$data = $event->fetch($event_id);
	if(!$data) return response(JSend::fail("Can't find event with ID $event_id", $event->errors), 404);

	return JSend::success("Event: $event_id", ['event' => $data]);
});

$app->delete('/events/{event_id}', function($event_id) use($app) {
	$event = new Event;

	$data = $event->fetch($event_id);
	if(!$data) return response(JSend::fail("Can't find event with ID $event_id", $event->errors), 404);

	$info = $event->remove($event_id);

	return "";
});

$app->get('/events/{event_id}/users', function($event_id, Request $request) use($app) {
	$event = new Event;

	$filter = $request->all();
	$data = $event->find($event_id)->users($filter);
	if(!$data) return response(JSend::fail("Can't find event with ID $event_id", $event->errors), 404);

	return JSend::success("Event: $event_id", ['users' => $data]);
});

/// Invite Users
$app->post('/events/{event_id}/users', function($event_id, Request $request) use($app) {
	$event = new Event;

	$user_ids_raw = $request->input('invite_user_ids');
	if(!is_array($user_ids_raw)) $user_ids = explode(",", $user_ids_raw);
	else $user_ids = $user_ids_raw;

	$event = $event->find($event_id);
	if(!$event) return response(JSend::fail("Can't find event with ID $event_id", $event->errors), 404);

	$send_invites = $request->input('send_invite_emails') == 'true' ? true : false;
	$event->invite($user_ids, $send_invites);
	
	$count = count($user_ids);

	return JSend::success( $count . " users invited to event", ['invited_count' => $count]);
});

$app->get('/events/{event_id}/attended', function($event_id) use($app) {
	$event = new Event;

	$data = $event->find($event_id)->users(['present' => '1']);
	if(!$data) return response(JSend::fail("Can't find event with ID $event_id", $event->errors), 404);

	return JSend::success("Event: $event_id", ['users' => $data]);
});

$app->get('/events/{event_id}/users/{user_id}', function($event_id, $user_id) use($app) {
	$event = new Event;
	$data = $event->find($event_id)->users(['user_id' => $user_id]);
	if(!count($data)) return response(JSend::fail("Can't find event with ID $event_id / User with ID $user_id", $event->errors), 404);

	return JSend::success("Event: $event_id", ['user' => $data[0]]);
});

$app->post('/events/{event_id}/users/{user_id}', function($event_id, $user_id, Request $request) use($app) {
	$event = new Event;
	$update = $event->find($event_id)->updateUserConnection($user_id, $request->all());
	// if(!$update) return response(JSend::fail("Error updating connection", $event->errors), 400); // If there is no change, this is getting triggered.

	$data = $event->find($event_id)->users(['user_id' => $user_id]);
	if(!count($data)) return response(JSend::fail("Can't find event with ID $event_id / User with ID $user_id", $event->errors), 404);

	return JSend::success("Event: $event_id", ['user' => $data[0]]);
});

$app->delete('/events/{event_id}/users/{user_id}', function($event_id, $user_id) use($app) {
	$event = new Event;

	$data = $event->find($event_id)->users(['user_id' => $user_id]);
	if(!count($data)) return response(JSend::fail("Can't find event with ID $event_id / User with ID $user_id", $event->errors), 404);

	$event->find($event_id)->deleteUserConnection($user_id);
	return ""; 
});

////////////////////////////////// Notifications //////////////////////////////
$app->post('/notifications', function(Request $request) use($app) {
	$notification_model = new Notification;
	$notification = $notification_model->add($request->all());

	return JSend::success("Notification created", ['notification' => $notification]);
});

$app->get('/notifications', function(Request $request) use($app) {
	$search_fields = ['id', 'user_id', 'phone', 'imei', 'fcm_regid', 'platform', 'app', 'status'];
	$search = [];
	foreach ($search_fields as $key) {
		if(!$request->input($key)) continue;
		$search[$key] = $request->input($key);
	}

	$notification = new Notification;
	$notifications = $notification->search($search);

	return JSend::success("Notifications", ['notifications' => $notifications]);
});


////////////////////////////////// Placeholders ///////////////////////////////
$app->get('/custom/video_analytics', function(Request $request) use($app) {

	return JSend::success("Data catured");
});
$app->get('/custom/care_collective_count', function(Request $request) use($app) {
	$contact = new Contact;
	return JSend::success("Care Collective Count", ['count' => $contact->getCount()]);
});

////////////////////////////////// Debug //////////////////////////
$app->get('/events/{event_id}/send_invites', function($event_id) use($app) {
	$event = new Event;
	$invited_users = $event->find($event_id)->users();

	foreach ($invited_users as $user) {
		$event->sendInvite($event_id, $user->id, $user->rsvp_auth_key, 'send');
	}

	return JSend::success("Sent event invites.", ['invited_user_count' => count($invited_users)]);
});

require base_path('app/Http/routes-surveys.php');
});

$app->post("/$url_prefix/users", ['middleware' => 'auth.basic', 'uses' => 'UserController@add']);
$app->post("/$url_prefix/users/{user_id}", ['middleware' => 'auth.basic', 'uses' => 'UserController@edit']);
$app->post("/$url_prefix/students", ['middleware' => 'auth.basic', 'uses' => 'StudentController@add']);
$app->post("/$url_prefix/students/{student_id}", ['middleware' => 'auth.basic', 'uses' => 'StudentController@edit']);
$app->post("/$url_prefix/events", ['middleware' => 'auth.basic', 'uses' => 'EventController@add']);
$app->post("/$url_prefix/events/{event_id}", ['middleware' => 'auth.basic', 'uses' => 'EventController@edit']);

$app->post("/$url_prefix/survey_templates", ['middleware' => 'auth.basic', 'uses' => 'SurveyController@addSurveyTemplate']);
$app->post("/$url_prefix/survey_templates/{survey_template_id}/questions", ['middleware' => 'auth.basic', 'uses' => 'SurveyController@addQuestion']);
$app->post("/$url_prefix/survey_templates/{survey_template_id}/questions/{survey_question_id}/choices", ['middleware' => 'auth.basic', 'uses' => 'SurveyController@addChoice']);
$app->post("/$url_prefix/surveys/{survey_id}/responses", ['middleware' => 'auth.basic', 'uses' => 'SurveyController@addResponse']);
$app->post("/$url_prefix/surveys/{survey_id}/questions/{survey_question_id}/responses", ['middleware' => 'auth.basic', 'uses' => 'SurveyController@addQuestionResponse']);

