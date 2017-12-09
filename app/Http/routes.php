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

use App\Models\User;
use App\Http\Controllers\UserController;
// header("Content-type: application/json");

$app->get('/', function () use ($app) {
    return $app->version();
});

$app->get('/foo', function () use ($app) {
    return "Please work.";
});

$app->get('/cities/{city_id}/users', function ($city_id) use ($app) {
	$user = new User;
    $users = $user->search(array('city_id' => $city_id));
    
    return json_encode($users);
});

$app->get('/users/{user_id}', function($user_id) use ($app) {
	$user = new User;
	$details = $user->fetch($user_id);

	// $details = $user->find($user_id)->city->name; //();

	// dd($details);
	return json_encode($details);
});

$app->get('/users/{user_id}/credits', function($user_id) use ($app) {
	$user = new User;
	$details = intval($user->fetch($user_id)->credit);
	return json_encode($details);
});

$app->post('/users','UserController@add');
$app->post('/users/{user_id}','UserController@edit');



