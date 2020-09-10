<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});
Route::get('/testing', function () {
    $model = new \App\Models\User;
    $data = $model->search(['group_id' => 24, 'only_main_group' => '1']);
    return view('testing', [ 'data' => $data ]);
});
