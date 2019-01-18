<?php
namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use JSend;

class UserController extends Controller
{   
    private $validation_messages = [
            'city_id.exists'    => "Can't find any city with that ID",
            'mad_email.regex'   => "The 'mad_email' you gave was not a makeadiff.in email. Enter this only if you are a fellow - and have a makeadiff.in email id."
        ];

    public function add(Request $request)
    {
        $validation_rules = [
            'name'      => 'required|max:50',
            'email'     => 'required|email|unique:User',
            'mad_email' => 'email|unique:User|regex:/.+\@makeadiff\.in$/',
            'password'  => 'required',
            'phone'     => 'required|unique:User|regex:/[\+0-9]{10,13}/',
            'city_id'   => 'required|numeric|exists:City,id'
        ];
        
        $validator = \Validator::make($request->all(), $validation_rules, $this->validation_messages);

        if ($validator->fails()) {
            return response(JSend::fail("Unable to create user - errors in input", $validator->errors()), 400);
        }

        $user = new User;
        $result = $user->add($request->all());

        return JSend::success("Created the user successfully", array('users' => $result));
    }

    public function edit(Request $request, $user_id)
    {
        $user = new User;
        $exists = $user->fetch($user_id, false);

        if(!$exists) {
            return response(JSend::fail("Can't find any user with the given ID"), 404);
        }

        $validation_rules = [
            'name'      => 'max:50',
            'email'     => 'email|unique:User',
            'mad_email' => 'email|unique:User|regex:/.+\@makeadiff\.in$/',
            'phone'     => 'unique:User|regex:/[\+0-9]{10,13}/',
            'city_id'   => 'numeric|exists:City,id'
        ];

        $validator = \Validator::make($request->all(), $validation_rules, $this->validation_messages);

        if ($validator->fails()) {
            return response(JSend::fail("Unable to create user - errors in input.", $validator->errors()), 400);
        }
        
        $result = $user->find($user_id)->edit($request->all());

        return JSend::success("Edited the user", array('users' => $result));
    }
}
