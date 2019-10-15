<?php
use App\Models\User;
use App\Models\Group;
use App\Models\Vertical;
use App\Models\City;
use App\Models\Center;
use App\Models\Classes;
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

// CORS handling. These have to be disabled if UnitTest have to be run. :TODO: 
// header("Access-Control-Allow-Origin: *");
// header('Access-Control-Allow-Headers: Authorization,Content-Type,Origin,Accept');

Route::get('/', function () {
    $result = [];

    return JSend::success(['data' => [
        'result'	=> $result,
        'app'		=> 'Phoenix'
    ]]);
});

$url_prefix = 'v1';

Route::group(['prefix' => $url_prefix, 'middleware' => ['auth.basic']], function () {

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
        $search = $request->only('id', 'name', 'type', 'vertical_id');
        $groups = Group::search($search);

        return JSend::success("User Groups", ['groups' => $groups]);
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
            if (!$request->input($key)) {
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
        $teachers = $user->search(['center_id' => $center_id]);

        return JSend::success("Teachers in Center $center_id", ['users' => $teachers]);
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
    Route::get('/batches/{batch_id}/levels', function ($batch_id) {
        $batch = (new Batch)->fetch($batch_id, false);
        if (!$batch) {
            return JSend::fail("Can't find any batch with ID $batch_id");
        }

        $levels = (new Level)->search(['batch_id' => $batch_id]);

        return JSend::success("Levels in batch $batch_id", ['levels' => $levels]);
    });

    ////////////////////////////////////////////////////////// Levels ///////////////////////////////////////////
    Route::get('/levels/{level_id}', function ($level_id) {
        $level = (new Level)->fetch($level_id, false);
        if (!$level) {
            return JSend::fail("Can't find any level with ID $level_id", []);
        }

        return JSend::success("Level ID : $level_id", ['levels' => $level]);
    });
    Route::get('/levels/{level_id}/students', function ($level_id) {
        $level = (new Level)->fetch($level_id, false);
        if (!$level) {
            return JSend::fail("Can't find any level with ID $level_id", []);
        }

        $students = (new Student)->search(['level_id' => $level_id]);

        return JSend::success("Students in Level $level_id", ['students' => $students]);
    });
    Route::get('/levels/{level_id}/batches', function ($level_id) {
        $level = (new Level)->fetch($level_id, false);
        if (!$level) {
            return JSend::fail("Can't find any level with ID $level_id", []);
        }

        $batches = (new Batch)->search(['level_id' => $level_id]);

        return JSend::success("Levels in batch $level_id", ['batches' => $batches]);
    });

    ///////////////////////////////////////////////// Classes /////////////////////////////////////
    Route::get('/classes', function (Request $request) {
        $search_fields = ['id','teacher_id', 'substitute_id', 'batch_id', 'level_id', 'project_id', 'status', 'class_date', 'direction', 'project_id'];
        $search = [];
        foreach ($search_fields as $key) {
            if (!$request->input($key)) {
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

    ////////////////////////////////////////////////// Auth //////////////////////////////////////////////////////
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

    ///////////////////////////////////////////////////////// User Calls //////////////////////////////////////////////
    Route::get('/users', function (Request $request) {
        $search_fields = ['id','user_id', 'identifier', 'name','phone','email','mad_email','group_id','group_in','vertical_id','city_id','user_type','center_id','project_id', 'not_user_type'];
        $search = [];
        foreach ($search_fields as $key) {
            if (!$request->input($key)) {
                continue;
            }

            if ($key == 'group_id') {
                $search['user_group'] = [$request->input('group_id')];
            } elseif ($key == 'group_in') {
                $search['user_group'] = explode(",", $request->input('group_in'));
            } elseif ($key == 'not_user_type') {
                $search['not_user_type'] = explode(",", $request->input('not_user_type'));
            } else {
                $search[$key] = $request->input($key);
            }
        }
        if (!isset($search['project_id'])) {
            $search['project_id'] = 1;
        }

        $user = new User;
        $data = $user->search($search);

        return JSend::success("Search Results", ['users' => $data]);
    });

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

    Route::post('/users/{user_id}/groups/{group_id}', function ($user_id, $group_id) {
        $user = new User;
        $info = $user->fetch($user_id);
        if (!$info) {
            return JSend::fail("Can't find user with user id '$user_id'");
        }

        $groups = $user->find($user_id)->addGroup($group_id);
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

    Route::get('/students', function (Request $request) {
        $search_fields = ['name','birthday', 'city_id','sex','center_id'];
        $search = [];
        foreach ($search_fields as $key) {
            if (!$request->input($key)) {
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
            if (!$request->input($key)) {
                continue;
            }

            if ($key == 'deposit_status_in') {
                $search['deposit_status_in'] = explode(",", $request->input('deposit_status_in'));

            // Specific bolean cases. So that we can use the keyworld 'false' in the URL. Not really required, but looks slightly better this way.
            } elseif ($key == 'include_deposit_info' or $key == 'deposited') {
                $input = $request->input($key);

                if (strtolower($input) == 'false') {
                    $value = false;
                } else {
                    $value = (boolean) $value;
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
        $search_fields = ['from', 'to', 'amount'];
        $search = ['fundraiser_user_id' => $fundraiser_user_id];

        foreach ($search_fields as $key) {
            if (!$request->input($key)) {
                continue;
            }

            $search[$key] = $request->input($key);
        }

        $donation = new Donation;
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
            if (!$request->input($key)) {
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
        $search_fields = ['id', 'name', 'description', 'starts_on', 'place', 'city_id', 'event_type_id', 'created_by_user_id', 'status'];
        $search = [];
        foreach ($search_fields as $key) {
            if (!$request->input($key)) {
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
        $event = new Event;

        $user_ids_raw = $request->input('invite_user_ids');
        if (!is_array($user_ids_raw)) {
            $user_ids = explode(",", $user_ids_raw);
        } else {
            $user_ids = $user_ids_raw;
        }

        $event = $event->find($event_id);
        if (!$event) {
            return JSend::fail("Can't find event with ID $event_id", $event->errors);
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

    ////////////////////////////////// Notifications //////////////////////////////
    Route::post('/notifications', function (Request $request) {
        $notification_model = new Notification;
        $notification = $notification_model->add($request->all());

        return JSend::success("Notification created", ['notification' => $notification]);
    });

    Route::get('/notifications', function (Request $request) {
        $search_fields = ['id', 'user_id', 'phone', 'imei', 'fcm_regid', 'platform', 'app', 'status'];
        $search = [];
        foreach ($search_fields as $key) {
            if (!$request->input($key)) {
                continue;
            }
            $search[$key] = $request->input($key);
        }

        $notification = new Notification;
        $notifications = $notification->search($search);

        return JSend::success("Notifications", ['notifications' => $notifications]);
    });


    ////////////////////////////////// Placeholders ///////////////////////////////
    Route::post('/custom/video_analytics', function (Request $request) {
        // $file = $request->file("image");
        // $status = $file->store('uploads');
  
        $data = $request->all();

        $status = $data['image']->store('uploads');
        dd($data['image'], $status);

        return JSend::success("Data catured");
    });
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
    // Route::get('/test', function() {
    //     $group = new Group;
    //     $es_trained = $group->find(368)->permissions();

    //     dump($es_trained);
    // });

    require base_path('routes/api-surveys.php');
});

Route::post("/users", ['middleware' => ['auth.basic', 'json.output'], 'uses' => 'UserController@add', 'prefix' => $url_prefix]);
Route::post("/users/{user_id}", ['middleware' => ['auth.basic', 'json.output'], 'uses' => 'UserController@edit', 'prefix' => $url_prefix]);
Route::post("/students", ['middleware' => ['auth.basic', 'json.output'], 'uses' => 'StudentController@add', 'prefix' => $url_prefix]);
Route::post("/students/{student_id}", ['middleware' => ['auth.basic', 'json.output'], 'uses' => 'StudentController@edit', 'prefix' => $url_prefix]);
Route::post("/events", ['middleware' => ['auth.basic', 'json.output'], 'uses' => 'EventController@add', 'prefix' => $url_prefix]);
Route::post("/events/{event_id}", ['middleware' => ['auth.basic', 'json.output'], 'uses' => 'EventController@edit', 'prefix' => $url_prefix]);

Route::post("/survey_templates", ['middleware' => ['auth.basic', 'json.output'], 'uses' => 'SurveyController@addSurveyTemplate', 'prefix' => $url_prefix]);
Route::post("/survey_templates/{survey_template_id}/questions", ['middleware' => ['auth.basic', 'json.output'], 'uses' => 'SurveyController@addQuestion', 'prefix' => $url_prefix]);
Route::post("/survey_templates/{survey_template_id}/questions/{survey_question_id}/choices", ['middleware' => ['auth.basic', 'json.output'], 'uses' => 'SurveyController@addChoice', 'prefix' => $url_prefix]);
Route::post("/surveys/{survey_id}/responses", ['middleware' => ['auth.basic', 'json.output'], 'uses' => 'SurveyController@addResponse', 'prefix' => $url_prefix]);
Route::post("/surveys/{survey_id}/questions/{survey_question_id}/responses", ['middleware' => ['auth.basic', 'json.output'], 'uses' => 'SurveyController@addQuestionResponse', 'prefix' => $url_prefix]);
