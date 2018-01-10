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
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;

header("Content-type: application/json");
header("Access-Control-Allow-Origin: *");

$app->get('/', function () use ($app) {
    return $app->version();
});

$app->get('/foo', function () use ($app) {
    return "Please work.";
});

$app->get('/cities/{city_id}/users', function ($city_id) use ($app) {
	$user = new User;
    $users = $user->search(array('city_id' => $city_id));
    
    return showSuccess("List of users returned", array('users' => $users));
});

$app->get('/users/', function(Request $request) use ($app) {
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

	return showSuccess("Search Results", array('users' => $data));
});

$app->get('/users/{user_id}', function($user_id) use ($app) {
	$user = new User;
	$details = $user->fetch($user_id);

	if(!$details) {
		return response(showError("Can't find user with user id '$user_id'"), 404);
	}

	return showSuccess("User details for {$details->name}", array('user' => $details));
});

$app->get('/users/{user_id}/groups', function($user_id) use ($app) {
	$user = new User;
	$info = $user->fetch($user_id);
	if(!$info) return response(showError("Can't find user with user id '$user_id'"), 404);

	$groups = $info->groups;
	return showSuccess("Credits for user $user_id", array('groups' => $groups));
});

$app->get('/users/{user_id}/credit', function($user_id) use ($app) {
	$user = new User;
	$info = $user->fetch($user_id);
	if(!$info) return response(showError("Can't find user with user id '$user_id'"), 404);

	$details = intval($info->credit);
	return showSuccess("Credits for user $user_id", array('credit' => $details));
});

$app->post('/users','UserController@add');
$app->post('/users/{user_id}','UserController@edit');



function showSuccess($message, $data = array()) {
	return showSituation('success', $message, $data);
}

function showFail($message, $data = array()) {
	return showSituation('fail', $message, $data);
}

function showError($message, $data = array()) {
	return showSituation('error', $message, $data);
}

function showSituation($status, $message, $data) {
	$template = array(
		'success'	=> true,
		'error'		=> false,
		'status'	=> 'success',
		'data'		=> null
	);

	if($status == 'error') {
		$template['error'] = true;
		$template['success'] = false;
		$template['fail'] = false;

		$template['message'] = $message;

	} else if($status == 'fail') {
		$template['error'] = true;
		$template['success'] = false;
		$template['fail'] = true;

		$template['data'] = array($message);
	}

	$template['status'] = $status;

	if(is_string($message)) {
		$template[$status] = $message;

	} elseif(is_array($message)) {
		$template = array_merge($template, $message);
	} 

	if($data)
		$template['data'] = $data;

	return json_encode($template);
}

