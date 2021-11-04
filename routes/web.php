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
    $model = new App\Models\Student;
    $data = $model->fetch(20631);
    // dump($data->levelByProject(1)->first()->grade);
    // $return = $data->classes()->first()->toArray();
    $level = $data->levels()->first();
    $return = $level->teachers()->get()->toArray();

    dump($return);

});
