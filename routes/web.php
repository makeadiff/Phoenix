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
    // $data = $model->fetch(4);

    $return = $model->search(['project_id' => 1, 'center_id' => 154]);
    dump($return);

    // $return = [];
    // foreach ($alloc as $al) {
    //     $usrs = $al->users();
    //     $return = $usrs->get()->toArray();
    //     dump($return);
    // }

});
