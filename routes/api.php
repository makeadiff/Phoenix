<?php
use App\Models\User;
use App\Models\Group;
use App\Models\Vertical;
use App\Models\City;
use App\Models\Center;
use App\Models\Classes;
use App\Models\Student;
use App\Models\Batch;
use App\Models\Allocation;
use App\Models\Level;
use App\Models\Subject;
use App\Models\Donation;
use App\Models\Deposit;
use App\Models\Event;
use App\Models\Event_Type;
use App\Models\Data;
use App\Models\Comment;
use App\Models\Notification;
use App\Models\Contact;
use App\Models\Alert;
use App\Models\Device;
use App\Models\CenterProject;
use App\Models\Log;

use App\Http\Controllers\UserController;
use App\Http\Controllers\DonationController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

Route::get('/', function () {
    $result = [];

    return JSend::success(['data' => [
        'result'	=> $result,
        'app'		=> 'Phoenix'
    ]]);
});

$url_prefix = 'v1';

// Pubilc functions - these can be called without JWT authentication
Route::group([
    'prefix' => $url_prefix, 
    'middleware' => ['auth.basic'] // , 'log.call']
], function () {
    /*
    Route::post('/users/login', function(Request $request) {  // - This line is here to get this call picked up the the all_call.php monitor.
    */
    Route::addRoute(['POST','GET'], '/users/login', function (Request $request) {
        $user = new User;
        $phone_or_email = $request->input('phone');
        if (!$phone_or_email) {
            $phone_or_email = $request->input('email');
        }
        if (!$phone_or_email) {
            $phone_or_email = $request->input('identifier');
        }

        if ($request->input('password')) {
            $data = $user->login($phone_or_email, $request->input('password'));
        } elseif ($request->input('auth_token')) {
            $data = $user->login($phone_or_email, false, $request->input('auth_token'));
        }

        if (!$data) {
            $error = "Invalid username/password";
            if (count($user->errors)) {
                $error = implode(", ", $user->errors);
            }

            return JSend::fail($error, [], 400);
        }

        return JSend::success("Welcome back, $data[name]", ['users' => $data]);
    });
    Route::post("/users", ['uses' => 'UserController@add']);
});


$middleware = ['auth.jwt_or_basic', 'json.output']; //, 'log.call'];

Route::group([
    'prefix' => $url_prefix, 
    'middleware' => $middleware
], function () {

///////////////////////////////////////////////// City Calls ////////////////////////////////////////////
    Route::get('/cities', function () {
        $cities = (new City)->getAll();

        return JSend::success("All cities", ['cities' => $cities]);
    });

    Route::get('/cities/{city_id}', function ($city_id) {
        $city = (new City)->fetch($city_id);
        if (!$city) {
            return JSend::fail("Can't find any city with the id $city_id");
        }

        return JSend::success("Details for city $city_id", ['cities' => $city]);
    });

    Route::get('/cities/{city_id}/users', function ($city_id) {
        $city = (new City)->fetch($city_id);
        if (!$city) {
            return JSend::fail("Can't find any city with ID $city_id");
        }

        $users = (new User)->search(array('city_id' => $city_id));

        return JSend::success("List of users returned", ['users' => $users]);
    });

    Route::get('/cities/{city_id}/teachers', function ($city_id, Request $request) {
        $city = (new City)->fetch($city_id);
        if (!$city) {
            return JSend::fail("Can't find any city with ID $city_id");
        }
        $teacher_user_group_id = config('constants.group.ed.teacher.id');

        $project_id = $request->input('project_id');
        if ($project_id == config('constants.project.fp.id')) {
            $teacher_user_group_id = config('constants.group.fp.teacher.id');
        }

        $users = (new User)->search(array('city_id' => $city_id, 'user_group' => $teacher_user_group_id));

        return JSend::success("List of teachers returned", ['users' => $users]);
    });

    Route::get('/cities/{city_id}/fellows', function ($city_id) {
        $city = (new City)->fetch($city_id);
        if (!$city) {
            return JSend::fail("Can't find any city with ID $city_id");
        }

        $users = (new User)->search(array('city_id' => $city_id, 'user_group_type' => 'fellow'));

        return JSend::success("List of fellows returned", ['users' => $users]);
    });

    Route::get('/cities/{city_id}/centers', function ($city_id) {
        $city = (new City)->fetch($city_id);
        if (!$city) {
            return JSend::fail("Can't find any city with ID $city_id");
        }

        $centers = (new Center)->inCity($city_id);

        return JSend::success("List of centers in city '$city[name]'", ['centers' => $centers]);
    });

    Route::get('/cities/{city_id}/students', function ($city_id) {
        $city = (new City)->fetch($city_id);
        if (!$city) {
            return JSend::fail("Can't find any city with ID $city_id");
        }

        $student = new Student;
        $students = $student->search(array('city_id' => $city_id));

        return JSend::success("List of students in $city[name]", ['students' => $students]);
    });

    ///////////////////////////////////////////////////// Groups /////////////////////////////////////////////////
    Route::get('/groups', function (Request $request) {
        $search = $request->only('id', 'name', 'type', 'vertical_id', 'type_in');
        $groups = Group::search($search);

        return JSend::success("User Groups", ['groups' => $groups]);
    });

    Route::get('/group_types', function (Request $request) {
        $types = Group::getTypes();
        return JSend::success('Group Types', ['types' => $types]);
    });

    Route::get('/groups/{group_id}', function ($group_id) {
        $group = (new Group)->fetch($group_id);
        if (!$group) {
            return JSend::fail("Can't find any group with ID $group_id");
        }

        return JSend::success("User Group: $group_id", ['groups' => $group]);
    });

    Route::get('/verticals', function () {
        $verticals = Vertical::getAll();
        return JSend::success("Verticals", ['verticals' => $verticals]);
    });

    Route::get('/projects', function () {
        $projects = ['1' => 'Ed Support', '2' => 'Foundation'];
        return JSend::success("Verticals", ['projects' => $projects]);
    });


    ///////////////////////////////////////////////////// Centers /////////////////////////////////////////////////
    Route::get('/centers', function (Request $request) {
        $search_fields = ['id', 'name', 'city_id'];
        $search = [];
        foreach ($search_fields as $key) {
            if (!$request->has($key)) {
                continue;
            }

            $search[$key] = $request->input($key);
        }

        $centers = Center::search($search);

        return JSend::success("Centers", ['centers' => $centers]);
    });

    Route::get('/centers/{center_id}', function ($center_id) {
        $center = (new Center)->fetch($center_id);
        if (!$center) {
            return JSend::fail("Can't find any center with ID $center_id");
        }

        return JSend::success("Center ID : $center_id", ['centers' => $center]);
    });

    Route::get('/centers/{center_id}/teachers', function ($center_id) {
        $center = (new Center)->fetch($center_id);
        if (!$center) {
            return JSend::fail("Can't find any center with ID $center_id");
        }

        $user = new User;
        $teachers = $user->search(['teaching_in_center_id' => $center_id]);

        return JSend::success("Teachers in Center $center_id", ['users' => $teachers]);
    });

    Route::get('/centers/{center_id}/users', function ($center_id) {
        $center = (new Center)->fetch($center_id);
        if (!$center) {
            return JSend::fail("Can't find any center with ID $center_id");
        }

        $user = new User;
        $vols = $user->search(['center_id' => $center_id]);

        return JSend::success("Volunteers in Center $center_id", ['users' => $vols]);
    });

    Route::get('/centers/{center_id}/students', function ($center_id) {
        $center = (new Center)->fetch($center_id);
        if (!$center) {
            return JSend::fail("Can't find any center with ID $center_id");
        }

        $student = new Student;
        $students = $student->search(array('center_id' => $center_id));

        return JSend::success("List of students in $center[name]", ['students' => $students]);
    });

    Route::get('/centers/{center_id}/batches', function ($center_id, Request $request) {
        $center = (new Center)->fetch($center_id);
        if (!$center) {
            return JSend::fail("Can't find any center with ID $center_id");
        }

        $project_id = $request->input('project_id');
        if (!$project_id) {
            $project_id = 1;
        }

        $batches = (new Batch)->search(['center_id' => $center_id, 'project_id' => $project_id]);
        return JSend::success("List of batches in $center[name]", ['batches' => $batches]);
    });

    Route::get('/centers/{center_id}/levels', function ($center_id, Request $request) {
        $center = (new Center)->fetch($center_id);
        if (!$center) {
            return JSend::fail("Can't find any center with ID $center_id");
        }

        $project_id = $request->input('project_id');
        if (!$project_id) {
            $project_id = 1;
        }

        $levels = (new Level)->search(['center_id' => $center_id, 'project_id' => $project_id]);
        return JSend::success("List of levels in $center[name]", ['levels' => $levels]);
    });

    ////////////////////////////////////////////////////////// Batches ///////////////////////////////////////////
    Route::get('/batches/{batch_id}', function ($batch_id) {
        $batch = (new Batch)->fetch($batch_id, false);
        if (!$batch) {
            return JSend::fail("Can't find any batch with ID $batch_id");
        }
        $mentors = (new User)->search(['batch_id' => $batch_id, 'batch_role' => 'mentor']);
        if ($mentors) {
            $batch['mentors'] = $mentors;
        }

        return JSend::success("Batch ID : $batch_id", ['batches' => $batch]);
    });
    Route::get('/batches/{batch_id}/teachers', function ($batch_id) {
        $batch = (new Batch)->fetch($batch_id, false);
        if (!$batch) {
            return JSend::fail("Can't find any batch with ID $batch_id");
        }

        $teachers = (new User)->search(['batch_id' => $batch_id]);

        return JSend::success("Teachers in batch $batch_id", ['teachers' => $teachers]);
    });
    Route::get('/batches/{batch_id}/mentors', function ($batch_id) {
        $batch = (new Batch)->fetch($batch_id, false);
        if (!$batch) {
            return JSend::fail("Can't find any batch with ID $batch_id");
        }

        $mentors = (new User)->search(['batch_id' => $batch_id, 'batch_role' => 'mentor']);

        return JSend::success("Mentors in batch $batch_id", ['mentors' => $mentors]);
    });
    Route::get('/batches/{batch_id}/levels', function ($batch_id) {
        $batch = (new Batch)->fetch($batch_id, false);
        if (!$batch) {
            return JSend::fail("Can't find any batch with ID $batch_id");
        }

        $levels = (new Level)->search(['batch_id' => $batch_id]);

        return JSend::success("Levels in batch $batch_id", ['levels' => $levels]);
    });
    Route::delete('/batches/{batch_id}', function ($batch_id) {
        $batch = new Batch;
        $info = $batch->fetch($batch_id);
        if (!$info) {
            return JSend::fail("Can't find batch with batch id '$batch_id'");
        }

        $batch->remove($batch_id);

        return ""; // Deletes should return empty data with status 200
    });

    Route::get("/batches/{batch_id}/levels/{level_id}/teachers", function ($batch_id, $level_id) {
        $user_model = new User;
        $teachers = $user_model->search(['batch_id' => $batch_id, 'level_id' => $level_id]);

        return JSend::success("Teachers in level/batch", ['teachers' => $teachers]);
    });

    Route::post("/batches/{batch_id}/levels/{level_id}/teachers/{teacher_id}", function ($batch_id, $level_id, $teacher_id, Request $request) {
        $allocation_model = new Allocation;
        $subject_id = $request->input('subject_id', 0);
        $allocation_status = $allocation_model->assignTeacher($batch_id, $level_id, $teacher_id, $subject_id);

        if (!$allocation_status) {
            return JSend::fail("Error creating the assignment");
        }
        $user_model = new User;
        $teachers = $user_model->search(['batch_id' => $batch_id, 'level_id' => $level_id]);

        return JSend::success("Teacher added successfully", ['teachers' => $teachers]);
    });

    Route::delete("/batches/{batch_id}/levels/{level_id}/teachers/{teacher_id}", function ($batch_id, $level_id, $teacher_id) {
        $allocation_model = new Allocation;
        $delete_status = $allocation_model->deleteTeacherAssignment($batch_id, $level_id, $teacher_id);

        if (!$delete_status) {
            return JSend::fail("Error deleting the assignment");
        }

        return ""; // JSend::success("Teacher removed from batch_id:".$batch_id." & level_id:".$level_id);
    });

    Route::delete("/batches/{batch_id}/mentors/{mentor_user_id}", function ($batch_id, $mentor_id) {
        $allocation_model = new Allocation;
        $delete_status = $allocation_model->deleteMentorAssignment($batch_id, $mentor_id);

        if (!$delete_status) {
            return JSend::fail("Error deleting the assignment");
        }

        return ""; //JSend::success("Mentor (user_id: ".$mentor_id.") removed from batch_id:".$batch_id);
    });

    ////////////////////////////////////////////////////////// Levels ///////////////////////////////////////////
    Route::get('/levels/{level_id}', function ($level_id) {
        $level = (new Level)->fetch($level_id); // There was a ',false' parameter here - that will return deleted levels too. Removed it - might cause issues later.
        if (!$level) {
            return JSend::fail("Can't find any level with ID $level_id", []);
        }

        return JSend::success("Level ID : $level_id", ['levels' => $level]);
    });
    Route::get('/levels/{level_id}/students', function ($level_id) {
        $level = (new Level)->fetch($level_id); // There was a ',false' parameter here - that will return deleted levels too. Removed it - might cause issues later.
        if (!$level) {
            return JSend::fail("Can't find any level with ID $level_id", []);
        }

        $students = (new Student)->search(['level_id' => $level_id]);

        return JSend::success("Students in Level $level_id", ['students' => $students]);
    });
    Route::get('/levels/{level_id}/batches', function ($level_id) {
        $level = (new Level)->fetch($level_id); // There was a ',false' parameter here - that will return deleted levels too. Removed it - might cause issues later.
        if (!$level) {
            return JSend::fail("Can't find any level with ID $level_id", []);
        }

        $batches = (new Batch)->search(['level_id' => $level_id]);

        return JSend::success("Levels in batch $level_id", ['batches' => $batches]);
    });
    Route::delete('/levels/{level_id}', function ($level_id) {
        $level = new Level;
        $info = $level->fetch($level_id);
        if (!$info) {
            return JSend::fail("Can't find batch with batch id '$level_id'");
        }

        $level->remove($level_id);

        return ""; // Deletes should return empty data with status 200
    });

    Route::delete("/levels/{level_id}/students/{student_id}", function ($level_id, $student_id) {
        $level_model = new Level;
        $delete_status = $level_model->unassignStudent($level_id, $student_id);

        if (!$delete_status) {
            return JSend::fail("Error deleting the assignment");
        }

        return "";
    });

    Route::get("/subjects", function () {
        $subjects_model = new Subject;
        $subjects = $subjects_model->getAll();

        return JSend::success("All Subjects", ['subjects' => $subjects]);
    });

    ///////////////////////////////////////////////// Classes /////////////////////////////////////
    Route::get('/classes', function (Request $request) {
        $search_fields = ['id','teacher_id', 'substitute_id', 'batch_id', 'level_id', 'project_id', 'status', 'class_date', 'direction', 'project_id'];
        $search = [];
        foreach ($search_fields as $key) {
            if (!$request->has($key)) {
                continue;
            }

            $search[$key] = $request->input($key);

            // if($key == 'group_id') {
            // 	$search['user_group'] = [$request->input('group_id')];
            // } elseif ($key == 'group_in') {
            // 	$search['user_group'] = explode(",", $request->input('group_in'));

            // } elseif ($key == 'not_user_type') {
            // 	$search['not_user_type'] = explode(",", $request->input('not_user_type'));
            // } else {
            // 	$search[$key] = $request->input($key);
            // }
        }
        if (!isset($search['project_id'])) {
            $search['project_id'] = 1;
        }

        $classes = new Classes;
        $data = $classes->search($search);

        return JSend::success("Search Results", ['classes' => $data]);
    });

    Route::get('/users/{user_id}/classes', function (Request $request, $user_id) {
        $search = [];
        $search['teacher_id'] = $user_id;
        $search['class_date_to'] = date('Y-m-d H:i:s');
        $search['past'] = false;

        $classes = new Classes;
        $data = $classes->search($search);

        return JSend::success("Search Results", ['classes' => $data]);
    });

    Route::get('/users/{user_id}/past_classes', function (Request $request, $user_id) {
        $search = [];
        $search['teacher_id'] = $user_id;
        $search['class_date_to'] = date('Y-m-d H:i:s');
        $search['past'] = true;

        $classes = new Classes;
        $data = $classes->search($search);

        return JSend::success("Search Results", ['classes' => $data]);
    });

    Route::get('/users/{user_id}/sourcing_campaign', function (Request $request, $user_id) {
        $user_model = new User;
        $campaign_id = $user_model->getSourcingCampaignId($user_id);
        $sourced_applicants = $user_model->getSourcedApplicants();
        return JSend::success("Sourcing Campaign", ['campaign_id' => $campaign_id, 'sourced_applicants' => $sourced_applicants]);
    });

    ///////////////////////////////////////////////// Data ////////////////////////////////////////
    if (!function_exists('getData')) { // It was causing some wierd issues in 'php artisan config:cache' command.
        function getData($item, $item_id, $data_name)
        {
            $data = (new Data)->get($item, $item_id, $data_name)->getData();
            if (!$data) {
                return JSend::fail("Can't find any Data with $item ID $item_id", []);
            }

            return JSend::success("Data '$data_name' for $item $item_id", ['data' => $data]);
        }
        function postData($item, $item_id, $data_name, $request)
        {
            $data = $request->input('data');
            if ($data) {
                (new Data)->get($item, $item_id, $data_name)->setData($data);
            }

            return JSend::success("Data '$data_name' for $item $item_id", ['data' => $data]);
        }
        function deleteData($item, $item_id, $data_name)
        {
            (new Data)->get($item, $item_id, $data_name)->remove();

            return ""; // JSend::success("Data '$data_name' for $item $item_id has been deleted");
        }
    }

    Route::get('/classes/{class_id}/data/{data_name}', function ($item_id, $data_name) {
        return getData('Class', $item_id, $data_name);
    });
    Route::post('/classes/{class_id}/data/{data_name}', function (Request $request, $item_id, $data_name) {
        return postData('Class', $item_id, $data_name, $request);
    });
    Route::delete('/classes/{class_id}/data/{data_name}', function ($item_id, $data_name) {
        return deleteData('Class', $item_id, $data_name);
    });

    Route::get('/users/{user_id}/data/{data_name}', function ($item_id, $data_name) {
        return getData('User', $item_id, $data_name);
    });
    Route::post('/users/{user_id}/data/{data_name}', function (Request $request, $item_id, $data_name) {
        return postData('User', $item_id, $data_name, $request);
    });
    Route::delete('/users/{user_id}/data/{data_name}', function ($item_id, $data_name) {
        return deleteData('User', $item_id, $data_name);
    });

    Route::get('/students/{student_id}/data/{data_name}', function ($item_id, $data_name) {
        return getData('Student', $item_id, $data_name);
    });
    Route::post('/students/{student_id}/data/{data_name}', function (Request $request, $item_id, $data_name) {
        return postData('Student', $item_id, $data_name, $request);
    });
    Route::delete('/students/{student_id}/data/{data_name}', function ($item_id, $data_name) {
        return deleteData('Student', $item_id, $data_name);
    });

    Route::get('/centers/{center_id}/data/{data_name}', function ($item_id, $data_name) {
        return getData('Center', $item_id, $data_name);
    });
    Route::post('/centers/{center_id}/data/{data_name}', function (Request $request, $item_id, $data_name) {
        return postData('Center', $item_id, $data_name, $request);
    });
    Route::delete('/centers/{center_id}/data/{data_name}', function ($item_id, $data_name) {
        return deleteData('Center', $item_id, $data_name);
    });

    Route::post("/centers/{center_id}/projects", function ($center_id, Request $request) {
        $center_project_model = new CenterProject;
        $project_ids_raw = $request->input('project_ids');
        if (!is_array($project_ids_raw)) 
        {
            $project_ids = explode(",", $project_ids_raw);
        } else 
        {
            $project_ids = $project_ids_raw;
        }

        $assign_status = $center_project_model->assignProject($center_id, $project_ids);

        if (!$assign_status) {
            return JSend::fail("Error assigning projects");
        }
    });

    ///////////////////////////////////// Comments /////////////////////////////////////

    if (!function_exists('getComments')) { // It was causing some wierd issues in 'php artisan config:cache' command.
        function getComments($item, $item_id)
        {
            $class_name = "App\Models\\$item";
            $model = new $class_name;
            $item_row = $model->find($item_id);
            if (!$item_row) {
                return JSend::fail("Can't find any $item with ID $item_id", []);
            }
            $comments = $item_row->comments()->select('id', 'comment', 'added_on', 'added_by_user_id')->get();

            return JSend::success("Comments for $item ID:$item_id", ['comments' => $comments]);
        }
        function addComment($item_type, $item_id, $request)
        {
            $class_name = "App\Models\\$item_type";
            $item_model = new $class_name;
            $item_row = $item_model->find($item_id);
            if (!$item_row) {
                return JSend::fail("Can't find any $item_type with ID $item_id", []);
            }

            $model = new Comment;
            if ($item_type and $item_id and $request->input('comment')) {
                $comment = $model->add([
                    'item_type'	=> $item_type,
                    'item_id'	=> $item_id,
                    'comment'	=> $request->input('comment'),
                    'added_by_user_id'	=> $request->input('added_by_user_id') ? $request->input('added_by_user_id') : 0
                ]);
                return JSend::success("Added a comment for $item_type $item_id", ['comment' => $comment]);
            }

            return JSend::fail("Error in input.");
        }
        function deleteComment($comment_id)
        {
            (new Comment)->remove($comment_id);

            return "";
        }
    }

    Route::get('/centers/{center_id}/comments', function ($item_id) {
        return getComments("Center", $item_id);
    });
    Route::post('/centers/{center_id}/comments', function (Request $request, $item_id) {
        return addComment('Center', $item_id, $request);
    });
    Route::delete('/centers/{center_id}/comments/{comment_id}', function ($item_id, $comment_id) {
        return deleteComment($comment_id);
    });

    Route::get('/students/{student_id}/comments', function ($item_id) {
        return getComments("Student", $item_id);
    });
    Route::post('/students/{student_id}/comments', function (Request $request, $item_id) {
        return addComment('Student', $item_id, $request);
    });
    Route::delete('/students/{student_id}/comments/{comment_id}', function ($item_id, $comment_id) {
        return deleteComment($comment_id);
    });

    ///////////////////////////////////////////////////////// User Calls //////////////////////////////////////////////
    Route::get('/users', 'UserController@index');
    Route::get('/users_paginated', 'UserController@index');

    Route::get('/users/{user_id}', function ($user_id) {
        $user = new User;
        $details = $user->fetch($user_id, false); // Right now this returns applicants as well - it was needed for Zoho. Migt be a problem later on. :TODO:

        if (!$details) {
            return JSend::fail("Can't find user with user id '$user_id'");
        }

        return JSend::success("User details for {$details->name}", ['users' => $details]);
    });

    Route::get('/users/{user_id}/credit', function ($user_id) {
        $user = new User;
        $info = $user->fetch($user_id);
        if (!$info) {
            return JSend::fail("Can't find user with user id '$user_id'");
        }

        $credit = intval($info->credit);
        return JSend::success("Credits for user $user_id", ['credit' => $credit]);
    });
    Route::post('/users/{user_id}/credit', function ($user_id, Request $request) {
        $user = new User;
        $info = $user->fetch($user_id);
        if (!$info) {
            return JSend::fail("Can't find user with user id '$user_id'");
        }

        $validator = \Validator::make($request->all(), [
            'credit'      			=> 'required|numeric',
            'updated_by_user_id'    => 'required|numeric|exists:User,id',
            'reason' 				=> 'required'
        ]);

        if ($validator->fails()) {
            return JSend::fail("Unable to edit the credit - errors in input", $validator->errors(), 400);
        }

        $user->find($user_id)->editCredit($request->input('credit'), $request->input('updated_by_user_id'), $request->input('reason'));

        return JSend::success("Edit the credits for user $user_id", ['credit' => $request->input('credit')]);
    });

    Route::delete('/users/{user_id}', function ($user_id) {
        $user = new User;
        $info = $user->fetch($user_id, false);
        if (!$info) {
            return JSend::fail("Can't find user with user id '$user_id'");
        }

        $info = $user->remove($user_id);

        return ""; // Deletes should return empty data with status 200
    });

    Route::get('/users/{user_id}/groups', function ($user_id) {
        $user = new User;
        $info = $user->fetch($user_id);
        if (!$info) {
            return JSend::fail("Can't find user with user id '$user_id'");
        }

        return JSend::success("User Groups for user $user_id", ['groups' => $info->groups]);
    });

    Route::get('/users/{user_id}/past_groups', function ($user_id) {
        $user = new User;
        $info = $user->fetch($user_id);
        if (!$info) {
            return JSend::fail("Can't find user with user id '$user_id'");
        }

        $past_groups = $info->pastGroups()->get();

        $groups_by_year = [];

        foreach ($past_groups as $grp) {
    	    if(!isset($groups_by_year[$grp->year])){
                $groups_by_year[$grp->year] = [$grp];
            }
    	    else {
                $groups_by_year[$grp->year][] = $grp;
            }
        }

        return JSend::success("User Groups for user $user_id using `past_grous`", ['groups' => $groups_by_year]);
    });

    Route::post('/users/{user_id}/groups', function ($user_id, Request $request) {
        $user = new User;
        $info = $user->fetch($user_id);
        if (!$info) {
            return JSend::fail("Can't find user with user id '$user_id'");
        }

        // Get groups as JSON and update it 
        $body = $request->getContent();
        $groups = [];
        if ($body) {
            $data = json_decode($body);
            $groups = $user->setGroups($data, $user_id);
        } else {
            return JSend::fail("Did not receive the group ids as a valid JSON in the body of the request");
        }

        return JSend::success("Added user to the given group.", ['groups' => $groups]);
    });

    Route::post('/users/{user_id}/groups/{group_id}', function ($user_id, $group_id, Request $request) {
        $user = new User;
        $info = $user->fetch($user_id);
        if (!$info) {
            return JSend::fail("Can't find user with user id '$user_id'");
        }

        $main = $request->input('main');
        if(!$main) $main = 0;

        $groups = $user->find($user_id)->addGroup($group_id, $main);
        if (!$groups) {
            return JSend::fail("User already has the given group", [], 400);
        }

        return JSend::success("Added user to the given group.", ['groups' => $groups]);
    });

    Route::delete('/users/{user_id}/groups/{group_id}', function ($user_id, $group_id) {
        $user = new User;
        $info = $user->fetch($user_id);
        if (!$info) {
            return JSend::fail("Can't find user with user id '$user_id'");
        }

        $groups = $user->find($user_id)->removeGroup($group_id);
        if (!$groups) {
            return JSend::fail("User don't have the given group", [], 400);
        }

        return "";
    });

    Route::get('/users/{user_id}/alerts', function ($user_id) {
        $user = new User;
        $info = $user->fetch($user_id);

        if (!$info) {
            return JSend::fail("Can't find user with user id '$user_id'");
        }

        $alerts = (new Alert)->generate($user_id);
        return JSend::success("Alerts for {$info->name}", ['alerts' => $alerts]);
    });

    /// Devices
    Route::get('/users/{user_id}/devices', function ($user_id, Request $request) {
        $search_fields = ['user_id', 'name', 'token', 'status'];
        $search = [];
        foreach ($search_fields as $key) {
            if (!$request->has($key)) {
                continue;
            }
            $search[$key] = $request->input($key);
        }

        $device_model = new Device;
        $devices = $device_model->search($search);

        return JSend::success("Devices", ['devices' => $devices]);
    });

    Route::post('/users/{user_id}/devices', function ($user_id, Request $request) {
        $device_model = new Device;
        $device = $device_model->addOrActivate(array_merge($request->all(), ['user_id' => $user_id]));

        return JSend::success("Device created", ['device' => $device]);
    });

    Route::post('/users/{user_id}/devices/{token}', function ($user_id, $token) {
        $device_model = new Device;
        $device = $device_model->addOrActivate(['user_id' => $user_id, 'token' => $token]);

        return JSend::success("Device created", ['device' => $device]);
    });

    Route::delete('/users/{user_id}/devices/{token}', function ($user_id, $token) {
        $device_model = new Device;
        $device = $device_model->search(['user_id' => $user_id, 'token' => $token]);
        if (count($device)) {
            foreach ($device as $d) {
                $device_model->remove($d->id);
            }
            return "";
        }

        return JSend::fail("Can't find device of user $user_id with given token");
    });
    
    Route::get('/users/{user_id}/links', function ($user_id) {
        $user_model = new User;
        $user = $user_model->fetch($user_id);

        if (!$user) {
            return JSend::fail("Can't find user with user id '$user_id'");
        }

        $links = $user->links()->get();

        return JSend::success("Links for {$user->name}", ['links' => $links]);
    });

    Route::get('/users/{user_id}/grouped_links', function ($user_id) {
        $user_model = new User;
        $user = $user_model->fetch($user_id);

        if (!$user) {
            return JSend::fail("Can't find user with user id '$user_id'");
        }

        $all_links = $user->links()->get();

        $grouped_links = [
            'general'   => [
                'name'      => 'General',
                'links'     => []
            ],
            'city'  => [
                'name'      => 'City',
                'links'     => []
            ],
            'center'   => [
                'name'      => 'Shelter',
                'centers'   => []
            ],
            'vertical'   => [
                'name'      => 'Vertical',
                'verticals' => []
            ],
            'group'   => [
                'name'      => 'Role',
                'groups'    => []
            ],
        ];

        foreach ($all_links as $l) {
            $lnk = $l->only('id', 'name', 'url', 'text', 'sort_order');
            if (!$l->center_id and !$l->vertical_id and !$l->group_id and !$l->city_id) {
                $grouped_links['general']['links'][] = $lnk;
                continue;
            }

            if ($l->city_id) {
                $grouped_links['city']['name'] = (new City)->fetch($l->city_id)->name;
                $grouped_links['city']['links'][] = $lnk;
            }

            if ($l->center_id) {
                if (!isset($grouped_links['center']['centers'][$l->center_id])) {
                    $grouped_links['center']['centers'][$l->center_id] = [
                        'name'  => (new Center)->fetch($l->center_id)->name,
                        'links' => [$lnk]
                    ];
                } else {
                    $grouped_links['center']['centers'][$l->center_id]['links'][] = $lnk;
                }
            }

            if ($l->vertical_id) {
                if (!isset($grouped_links['vertical']['verticals'][$l->vertical_id])) {
                    $grouped_links['vertical']['verticals'][$l->vertical_id] = [
                        'name'  => (new Vertical)->fetch($l->vertical_id)->name,
                        'links' => [$lnk]
                    ];
                } else {
                    $grouped_links['vertical']['verticals'][$l->vertical_id]['links'][] = $lnk;
                }
            }

            if ($l->group_id) {
                if (!isset($grouped_links['group']['groups'][$l->group_id])) {
                    $grouped_links['group']['groups'][$l->group_id] = [
                        'name'  => (new Group)->fetch($l->group_id)->name,
                        'links' => [$lnk]
                    ];
                } else {
                    $grouped_links['group']['groups'][$l->group_id]['links'][] = $lnk;
                }
            }
        }
        
        return JSend::success("Links for {$user->name}", ['links' => $grouped_links]);
    });

    //////////////////////////////////////////////////////// Contacts /////////////////////////////////
    Route::post('/applicants', function (Request $request) {
        $contact = new Contact;
        $data = $request->all();
        $data['is_applicant'] = 1;

        $status = $contact->add($data);
        if (!$status) {
            return JSend::fail("Could not create contact - errors in input", $contact->errors, 400);
        }
        return JSend::success("Added the applicant successfully", ['applicant' => $status]);
    });

    Route::post('/contacts', function (Request $request) {
        $contact = new Contact;
        $data = $request->all();
        // if(!isset($data['is_applicant'])) $data['is_applicant'] = 0;
        // if(!isset($data['is_subscribed'])) $data['is_subscribed'] = 0;
        // if(!isset($data['is_care_collective'])) $data['is_care_collective'] = 0;

        $status = $contact->add($data);
        if (!$status) {
            return JSend::fail("Could not create contact - errors in input", $contact->errors, 400);
        }
        return JSend::success("Added the contact successfully", ['contact' => $status]);
    });
    ///////////////////////////////////////////////////////// Student Calls //////////////////////////////////////////////
    // These calls are commented intentionally - the actual calls are at the end of this file. These lines are here to denote that there are more routes.
    // Route::post('/students','StudentController@add');
    // Route::post('/students/{student_id}','StudentController@edit');
    
    Route::get('/students_paginated','StudentController@index');

    Route::get('/students', function (Request $request) {
        $search_fields = ['name','birthday', 'city_id','sex','center_id', 'student_type', 'student_type_in', 'not_student_type'];
        $search = [];
        foreach ($search_fields as $key) {
            if (!$request->has($key)) {
                continue;
            }
            $search[$key] = $request->input($key);
        }

        $student = new Student;
        $data = $student->search($search);

        return JSend::success("Search Results", ['students' => $data]);
    });
    Route::delete('/students/{student_id}', function ($student_id) {
        $student = new Student;
        $info = $student->fetch($student_id);
        if (!$info) {
            return JSend::fail("Can't find student with id '$student_id'");
        }

        $info = $student->remove($student_id);

        return ""; // JSend::success("User deleted successfully", ['student' => $info]);
    });

    Route::get('/students/{student_id}', function ($student_id) {
        $student = new Student;
        $details = $student->fetch($student_id);

        if (!$details) {
            return JSend::fail("Can't find student with id '$student_id'");
        }

        return JSend::success("Student details for {$details->name}", ['student' => $details]);
    });

    /////////////////////////////////////////////// Donations ///////////////////////////////////////////////////////
    Route::get('/donations', function (Request $request) {
        $search_fields = ['deposit_status_in','deposit_status','approver_user_id','id','city_id','amount','status','fundraiser_user_id','updated_by_user_id', 'include_deposit_info', 'deposited', 'from', 'to'];
        $search = [];
        foreach ($search_fields as $key) {
            if (!$request->has($key)) {
                continue;
            }

            if ($key == 'deposit_status_in') {
                $search['deposit_status_in'] = explode(",", $request->input('deposit_status_in'));

            // Specific bolean cases. So that we can use the keyworld 'false' in the URL. Not really required, but looks slightly better this way.
            } elseif ($key == 'include_deposit_info' or $key == 'deposited') {
                $input = $request->input($key);

                if (strtolower($input) === 'false' or $input == '0') {
                    $value = false;
                } else {
                    $value = true;
                }

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

    Route::post('/donations', function (Request $request) {
        $donation_model = new Donation;
        $donation = $donation_model->add($request->all());

        if ($donation) {
            return JSend::success("Donation inserted succesfully : Donation ID '{$donation->id}'", ["donation" => $donation]);
        } else {
            return JSend::error("Failure in inserting donation at server. Try again after some time.", $donation_model->errors);
        }
    });

    Route::post('/donations/validate', function (Request $request) {
        $donation = new Donation;
        $result = $donation->validate($request->all());

        if ($result) {
            return JSend::success("Validated successfully");
        } else {
            return JSend::error("Validation error");
        }
    });
    Route::get('/donations/{donation_id}', function ($donation_id) {
        $donation = new Donation;
        $data = $donation->fetch($donation_id);

        if (!$data) {
            return JSend::fail("Can't find any donations with the ID $donation_id");
        }

        return JSend::success("Donation Details for $donation_id", ['donation' => $data]);
    });

    // DO NOT Document this call yet. Can be used to 'fake' the receipt. Ideally this should only be run for donatations that are approved by finance team.
    Route::get('/donations/{donation_id}/receipt.pdf', function ($donation_id) {
        $donation = new Donation;
        $data = $donation->fetch($donation_id);

        if (!$data) {
            return JSend::fail("Can't find any donations with the ID $donation_id");
        }

        $receipt_path = $donation->generateReceipt($donation_id);
        header("Content-Type: application/pdf");
        // header("Content-disposition: attachment; filename=receipt.pdf");
        readfile($receipt_path);
    });

    Route::delete('/donations/{donation_id}', function ($donation_id) {
        if (!$donation_id) {
            return JSend::fail("Invalid donaiton ID - $donation_id");
        }

        $donation = new Donation;
        $data = $donation->fetch($donation_id);

        if (!$data) {
            return JSend::fail("Can't find any donations with the ID $donation_id");
        }

        $donation->remove($donation_id);

        return ""; // JSend::success("Donation '$donation_id' deleted.", ['donation' => $data]); // DELETE return should be empty.
    });

    Route::get('/users/{user_id}/donations', function (Request $request, $fundraiser_user_id) {
        $search_fields = ['from', 'to', 'amount', 'type'];
        $search = ['fundraiser_user_id' => $fundraiser_user_id];

        foreach ($search_fields as $key) {
            if (!$request->has($key)) {
                continue;
            }

            $search[$key] = $request->input($key);
        }

        $donation = new DonationController;
        $data = $donation->search($search);

        return JSend::success("Donations", ['donations' => $data]);
    });

    ///////////////////////////// Deposits ////////////////////////
    Route::post('/deposits', function (Request $request) {
        $deposit = new Deposit;
        $donation_ids = [];
        if ($request->input('donation_ids')) {
            $donation_ids = $request->input('donation_ids');
            if (!is_array($donation_ids)) {
                $donation_ids = explode(",", $donation_ids);
            }
        }
        $deposit_info = $deposit->add($request->input('collected_from_user_id'), $request->input('given_to_user_id'), $donation_ids, $request->input('deposit_information'));

        if (!$deposit_info) {
            return JSend::fail("Error making the deposit", $deposit->errors, 400);
        }
        return JSend::success("Made the deposit", ['deposit' => $deposit_info]);
    });

    Route::get('/deposits', function (Request $request) {
        $search_fields = ['id', 'status', 'status_in', 'reviewer_user_id'];
        $search = [];
        foreach ($search_fields as $key) {
            if (!$request->has($key)) {
                continue;
            }

            $search[$key] = $request->input($key);
        }

        if (!count($search)) {
            return JSend::fail("Please provide some search parameters", ["Please provide some search parameters"], 400);
        }

        $deposit = new Deposit;
        $data = $deposit->search($search);

        return JSend::success("Deposits matching criteria", ['deposits' => $data]);
    });

    Route::post('/deposits/{deposit_id}', function ($deposit_id, Request $request) {
        $reviewer_user_id = $request->input('reviewer_user_id');
        $status = $request->input('status');

        $deposit = new Deposit;
        $given_deposit = $deposit->find($deposit_id);

        if (!$given_deposit) {
            return JSend::fail("Can't find any deposit with the given id.", $deposit->errors);
        }

        $data = false;
        if ($status == 'approved') {
            $data = $given_deposit->approve($reviewer_user_id);
        } elseif ($status == 'rejected') {
            $data = $given_deposit->reject($reviewer_user_id);
        } else {
            return JSend::error("Status should be 'approved' or 'rejected'", [], 400);
        }

        if (!$data) {
            return JSend::fail("Error approving deposit.", $given_deposit->errors, 400);
        }

        return JSend::success("Deposit updated", ['deposit' => $data]);
    });


    ////////////////////////////////// Events ////////////////////////////////
    // These calls are commented intentionally - the actual calls are at the end of this file. These lines are here to denote that there are more routes.
    // Route::post('/events','EventController@add');
    // Route::post('/events/{event_id}','EventController@edit');

    Route::get('/events', function (Request $request) {
        $search_fields = ['id', 'name', 'description', 'starts_on', 'date', 'from_date', 'to_date', 'place',
                        'city_id', 'event_type_id', 'created_by_user_id', 'status', 'invited_user_id'];
        $search = [];
        foreach ($search_fields as $key) {
            if (!$request->has($key)) {
                continue;
            }

            $search[$key] = $request->input($key);
        }

        $event = new Event;
        $events = $event->search($search);

        return JSend::success("Events", ['events' => $events]);
    });

    Route::get('/events/{event_id}', function ($event_id) {
        $event = new Event;
        
        $data = $event->fetch($event_id);
        $event_type = $data->event_type()->first();
        if (!empty($event_type)) {
            $data->type = $event_type->name;
        }
        if (!$data) {
            return JSend::fail("Can't find event with ID $event_id", $event->errors);
        }

        return JSend::success("Event: $event_id", ['event' => $data]);
    });

    Route::delete('/events/{event_id}', function ($event_id) {
        $event = new Event;

        $data = $event->fetch($event_id);
        if (!$data) {
            return JSend::fail("Can't find event with ID $event_id", $event->errors);
        }

        $event->remove($event_id);

        return "";
    });

    Route::get('/events/{event_id}/users', function ($event_id, Request $request) {
        $event = new Event;

        $filter = $request->all();
        $data = $event->find($event_id)->users($filter);
        if (!$data) {
            return JSend::fail("Can't find event with ID $event_id", $event->errors);
        }

        return JSend::success("Event: $event_id", ['users' => $data]);
    });

    /// Invite Users
    Route::post('/events/{event_id}/users', function ($event_id, Request $request) {
        $event_model = new Event;

        $user_ids_raw = $request->input('invite_user_ids');
        if (!is_array($user_ids_raw)) {
            $user_ids = explode(",", $user_ids_raw);
        } else {
            $user_ids = $user_ids_raw;
        }

        $event = $event_model->find($event_id);
        if (!$event) {
            return JSend::fail("Can't find event with ID $event_id", $event_model->errors);
        }

        $send_invites = $request->input('send_invite_emails') == 'true' ? true : false;
        $event->invite($user_ids, $send_invites);

        $count = count($user_ids);

        return JSend::success($count . " users invited to event", ['invited_count' => $count]);
    });

    Route::get('/events/{event_id}/attended', function ($event_id) {
        $event = new Event;

        $data = $event->find($event_id)->users(['present' => '1']);
        if (!$data) {
            return JSend::fail("Can't find event with ID $event_id", $event->errors);
        }

        return JSend::success("Event: $event_id", ['users' => $data]);
    });

    Route::get('/events/{event_id}/users/{user_id}', function ($event_id, $user_id) {
        $event = new Event;
        $data = $event->find($event_id)->users(['user_id' => $user_id]);
        if (!count($data)) {
            return JSend::fail("Can't find event with ID $event_id / User with ID $user_id", $event->errors);
        }

        return JSend::success("Event: $event_id", ['user' => $data[0]]);
    });

    Route::post('/events/{event_id}/attended', function ($event_id, Request $request) {
        $event = new Event;
        $data = $event->find($event_id);
        $user_ids_raw = $request->input('attendee_user_ids');
    
        if (!is_array($user_ids_raw) && $user_ids_raw!= null) {
            $user_ids = explode(",", $user_ids_raw);
        } else {
            $user_ids = $user_ids_raw;
        }

        if ($user_ids == null || !count($user_ids)) {
            return JSend::fail("No UserID Passed for $event_id");
        } else {
            $event->updateAttendance($user_ids, $event_id);
            return JSend::success("Event: $event_id", ['user_ids_updated' => $user_ids]);
        }
    });

    Route::post('/events/{event_id}/users/{user_id}', function ($event_id, $user_id, Request $request) {
        $event = new Event;
        $update = $event->find($event_id)->updateUserConnection($user_id, $request->all());
        // if(!$update) return JSend::fail("Error updating connection", $event->errors, 400); // If there is no change, this is getting triggered.

        $data = $event->find($event_id)->users(['user_id' => $user_id]);
        if (!count($data)) {
            return JSend::fail("Can't find event with ID $event_id / User with ID $user_id", $event->errors);
        }

        return JSend::success("Event: $event_id", ['user' => $data[0]]);
    });

    Route::delete('/events/{event_id}/users/{user_id}', function ($event_id, $user_id) {
        $event = new Event;

        $data = $event->find($event_id)->users(['user_id' => $user_id]);
        if (!count($data)) {
            return JSend::fail("Can't find event with ID $event_id / User with ID $user_id", $event->errors);
        }

        $event->find($event_id)->deleteUserConnection($user_id);
        return "";
    });

    Route::post('/events/{event_id}/recur', function ($event_id, Request $request) {
        $event = new Event;
        $event_data = $event->find($event_id);
        if (empty($event_data)) {
            return JSend::fail("Can't find event with ID: $event_id");
        }
        $frequency = $event_data->frequency;
        if ($request->input('frequency')) {
            $frequency = $request->input('frequency');
        }

        $repeat_until = $event_data->repeat_until;
        if ($request->input('repeat_until')) {
            $repeat_until = $request->input('repeat_until');
        }

        $recurring = $event->createRecurringInstances($event_data, $frequency, $repeat_until);
        if (!$recurring) {
            return JSend::fail("Invalid Frequency entered to repeat the event $event_id");
        }
        return JSend::success(count($recurring)." Event Instances created for $event_id", ['event_ids' => $recurring]);
    });

    Route::get('/event_types', function () {
        $eventtypes = Event_Type::getAll();
        return JSend::success("Event_Types", ['event_types' => $eventtypes]);
    });


    // Notifications
    // :TODO: This might be depricated soon. We are moving to the 'Device' Model.
    Route::post('/notifications', function (Request $request) {
        $notification_model = new Notification;
        $notification = $notification_model->add($request->all());

        return JSend::success("Notification created", ['notification' => $notification]);
    });
    

    Route::get('/notifications', function (Request $request) {
        $search_fields = ['id', 'user_id', 'phone', 'imei', 'fcm_regid', 'platform', 'app', 'status'];
        $search = [];
        foreach ($search_fields as $key) {
            if (!$request->has($key)) {
                continue;
            }
            $search[$key] = $request->input($key);
        }

        $notification = new Notification;
        $notifications = $notification->search($search);

        return JSend::success("Notifications", ['notifications' => $notifications]);
    });

    Route::post('/logs', function (Request $request) {
        $log_model = new Log;
        $log = $log_model->add($request->all());

        return JSend::success("Log item created", ['log' => $log]);
    });


    ////////////////////////////////// Placeholders ///////////////////////////////
    Route::get('/custom/care_collective_count', function (Request $request) {
        $contact = new Contact;
        return JSend::success("Care Collective Count", ['count' => $contact->getCount()]);
    });

    ////////////////////////////////// Debug //////////////////////////
    Route::get('/events/{event_id}/send_invites', function ($event_id) {
        $event = new Event;
        $invited_users = $event->find($event_id)->users();

        foreach ($invited_users as $user) {
            $event->sendInvite($event_id, $user->id, $user->rsvp_auth_key, 'send');
        }

        return JSend::success("Sent event invites.", ['invited_user_count' => count($invited_users)]);
    });
    Route::get('/donations/{donation_id}/send_receipt', function ($donation_id) {
        $donation = new Donation;
        $donation->sendReceipt('send', $donation_id); // If you want this to work, change this function to public in the Donation model

        return JSend::success("Sent the receipt.");
    });

    // Use this to Debug/test things
    Route::get('/test', function () {
        $model = new Student;
        $data = $model->fetch(11448);
        dump($data->level());
    });

    require_once base_path('routes/api-surveys.php');
});

Route::post("/users/{user_id}", ['uses' => 'UserController@edit', 'prefix' => $url_prefix, 'middleware' => $middleware]);
Route::post("/students", ['uses' => 'StudentController@add', 'prefix' => $url_prefix, 'middleware' => $middleware]);
Route::post("/students/{student_id}", ['uses' => 'StudentController@edit', 'prefix' => $url_prefix, 'middleware' => $middleware]);
Route::post("/events", ['uses' => 'EventController@add', 'prefix' => $url_prefix, 'middleware' => $middleware]);
Route::post("/events/{event_id}", ['uses' => 'EventController@edit', 'prefix' => $url_prefix, 'middleware' => $middleware]);
Route::post("/batches", ['uses' => 'BatchController@add', 'prefix' => $url_prefix, 'middleware' => $middleware]);
Route::post("/batches/{batch_id}", ['uses' => 'BatchController@edit', 'prefix' => $url_prefix, 'middleware' => $middleware]);
Route::post("/batches/{batch_id}/levels/{level_id}/teachers", ['uses' => 'BatchController@assignTeachers', 'prefix' => $url_prefix, 'middleware' => $middleware]);
Route::post("/batches/{batch_id}/mentors", ['uses' => 'BatchController@assignMentors', 'prefix' => $url_prefix, 'middleware' => $middleware]);
Route::post("/levels", ['uses' => 'LevelController@add', 'prefix' => $url_prefix, 'middleware' => $middleware]);
Route::post("/levels/{level_id}", ['uses' => 'LevelController@edit', 'prefix' => $url_prefix, 'middleware' => $middleware]);
Route::post("/levels/{level_id}/students", ['uses' => 'LevelController@assignStudents', 'prefix' => $url_prefix, 'middleware' => $middleware]);

Route::post("/survey_templates", ['uses' => 'SurveyController@addSurveyTemplate', 'prefix' => $url_prefix, 'middleware' => $middleware]);
Route::post("/survey_templates/{survey_template_id}/questions", ['uses' => 'SurveyController@addQuestion', 'prefix' => $url_prefix, 'middleware' => $middleware]);
Route::post("/survey_templates/{survey_template_id}/questions/{survey_question_id}/choices", ['uses' => 'SurveyController@addChoice', 'prefix' => $url_prefix, 'middleware' => $middleware]);
Route::post("/surveys/{survey_id}/responses", ['uses' => 'SurveyController@addResponse', 'prefix' => $url_prefix, 'middleware' => $middleware]);
Route::post("/surveys/{survey_id}/questions/{survey_question_id}/responses", ['uses' => 'SurveyController@addQuestionResponse', 'prefix' => $url_prefix, 'middleware' => $middleware]);
