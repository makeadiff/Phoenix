<?php
namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use JSend;
use Illuminate\Validation\Rule;

use App\Http\Resources\User as UserResource;

class UserController extends Controller
{
    private $validation_messages = [
        'city_id.exists'    => "Can't find any city with that ID",
        'mad_email.regex'   => "The 'mad_email' you gave was not a makeadiff.in email. Enter this only if you are a fellow - and have a makeadiff.in email id.",
        'email.unique'      => 'Entered Email ID already exists in the MAD database',
        'phone.unique'      => 'Entered Phone already exists in the MAD database',
        'sex.regex'         => "Sex field should have one of these values - 'm','f' or 'o'"
    ];

    public function add(Request $request)
    {
        $validation_rules = [
            'name'      => 'required|max:50',
            'email'     => 'required|email|unique:User,email',
            'mad_email' => 'email|regex:/.+\@makeadiff\.in$/',
            'password'  => 'required',
            'sex'       => 'regex:/^[mfo]$/',
            'phone'     => 'required|unique:User,phone|regex:/[\+\-0-9 ]{10,14}/',
            'city_id'   => 'required|numeric|exists:City,id'
        ];

        if($request->input('user_type') === 'applicant') {
            $validation_rules['email'] = 'required|email';
            $validation_rules['phone'] = 'required|regex:/[\+\-0-9 ]{10,14}/';
            $validator = \Validator::make($request->all(), $validation_rules, $this->validation_messages);

            if ($validator->fails()) {
                return JSend::fail("Unable to create user - errors in input", $validator->errors(), 400);
            }

            $user = new User;

            $user_exists = $user->search(['email' => $request->input('email'), 'not_user_type' => ['other']]); // The 'other' will get as all valid user type ka people. Slightly hacky for my taste.
            if(!$user_exists->count()) {
                $user_exists = $user->search(['phone' => $request->input('phone'), 'not_user_type' => ['other']]);
            }

            if($user_exists->count()) { // Custom rules if we found an existing application.
                $existing_user = $user_exists->first();

                if($existing_user->user_type === 'volunteer') { // If the found user is an active volunteer, don't make any changes.
                    return JSend::fail("The given details match an existing volunteer in our records.");

                } else { // If anything else(even let_go), mark them as applicant again with current joined_on date.
                    $data = $request->all(); 
                    $data['joined_on'] = date('Y-m-d H:i:s');
                    $user_data = $user->edit($data, $existing_user->id); // Also, update their row with latest details.

                    return JSend::success("Found an existing record with your details. We are marking you as an applicant again.", ['users' => $user_data]);
                }

            } else {
                $result = $user->add($request->all());

                return JSend::success("Created the user successfully", array('users' => $result));
            }
        } else {
            $validator = \Validator::make($request->all(), $validation_rules, $this->validation_messages);

            if ($validator->fails()) {
                return JSend::fail("Unable to create user - errors in input", $validator->errors(), 400);
            }

            $user = new User;
            $result = $user->add($request->all());

            return JSend::success("Created the user successfully", array('users' => $result));
        }
    }

    public function edit(Request $request, $user_id)
    {
        $user = new User;
        $exists = $user->fetch($user_id, false);

        if (!$exists) {
            return JSend::fail("Can't find any user with the given ID", [], 404);
        }

        $validation_rules = [
            'name'      => 'max:50',
            'email'     => ['email', Rule::unique('User')->ignore($user_id)],
            'mad_email' => ['email|nullable|regex:/.+\@makeadiff\.in$/', Rule::unique('User')->ignore($user_id)],
            'sex'       => 'regex:/^[mfo]$/',
            'phone'     => ['regex:/[\+0-9]{10,13}/', Rule::unique('User')->ignore($user_id)],
            'city_id'   => 'numeric|exists:City,id'
        ];

        $validator = \Validator::make($request->all(), $validation_rules, $this->validation_messages);

        if ($validator->fails()) {
            return JSend::fail("Unable to create user - errors in input.", $validator->errors(), 400);
        }

        $result = $user->find($user_id)->edit($request->all());

        return JSend::success("Edited the user", array('users' => $result));
    }

    public function index(Request $request)
    {
        $search_fields = ['id','user_id', 'identifier', 'name','phone','email','mad_email','any_email','group_id','group_in','group_type','vertical_id',
                            'city_id', 'city_in','user_type','center_id','project_id', 'not_user_type', 'credit', 'credit_lesser_than', 'credit_greater_than'];
        $search = [];
        foreach ($search_fields as $key) {
            if (!$request->has($key)) {
                continue;
            }

            if ($key == 'group_id') {
                $search['user_group'] = [$request->input('group_id')];
            } elseif ($key == 'group_in') {
                $search['user_group'] = explode(",", $request->input('group_in'));
            } elseif ($key == 'city_in') {
                $search['city_in'] = explode(",", $request->input('city_in'));
            } elseif ($key == 'not_user_type') {
                $search['not_user_type'] = explode(",", $request->input('not_user_type'));
            } else {
                $search[$key] = $request->input($key);
            }
        }
        if (!isset($search['project_id'])) {
            $search['project_id'] = 1;
        }

        $uri = $request->path();
        $paginated = false;
        if(stripos($uri, '_paginated')) $paginated = true;

        // dump($search); exit;

        $user = new User;
        $data = $user->search($search, $paginated);
        return JSend::success("Users", ['users' => $data]);
        // return UserResource::collection($data);
    }
}
