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
    $model = new App\Models\Batch;
    $data = $model->fetch(3592);

    $alloc = $data->allocations()->get()[0];
    dump($alloc);
    $usrs = $alloc->users()->get()->toArray();
    dump($usrs);

    // $return = [];
    // foreach ($alloc as $al) {
    //     $usrs = $al->users();
    //     $return = $usrs->get()->toArray();
    //     dump($return);
    // }

});
