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
    // $model = new \App\Models\User;
    // $data = $model->search(['group_id' => 24, 'only_main_group' => '1']);
    // return view('testing', [ 'data' => $data ]);


    $tag_model = new \App\Models\Tag;
    // $test = $tag_model->fetch('test');
    // // dump($test);
    // $users = $test->items('User'); // Should return everything tagged with 'test'
    // dump($users->first());

    // $comment_model = new \App\Models\Comment;
    // $comment = $comment_model->find(3959);
    // $tags = $comment->tags()->get(); // Return all tags for this comment.
    // dump($tags);


    $ret = $tag_model->tagItem('Comment', 3960, 'fun'); // Tag a comment.
    $ret = $tag_model->tagItem('Comment', [3961, 3962, 3963], 'cool'); // Tag a comment.

    dump($ret);


});
