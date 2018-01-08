<?php
namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    private $validation_rules = [
            'name'      => 'required|max:50',
            'email'     => 'required|email|unique:User',
            'mad_email' => 'email|unique:User|regex:/.+\@makeadiff\.in$/',
            'password'  => 'required',
            'phone'     => 'required|unique:User|regex:/[\+0-9]{10,13}/',
            'city_id'   => 'required|numeric|exists:City,id'
        ];
    private $validation_messages = [
            'city_id.exists'    => "Can't find any city with that ID",
            'mad_email.regex'   => "The 'mad_email' you gave was not a makeadiff.in email. Enter this only if you are a fellow - and have a makeadiff.in email id."
        ];

    public function add(Request $request)
    {
        $validator = \Validator::make($request->all(), $this->validation_rules, $this->validation_messages);

        if ($validator->fails()) {
            return response()->json(array(
                    'success'   => false,
                    'status'    => 'fail',
                    'data'      => $validator->errors()), 400);
        }

        $result = User::add($request->all());
        return response()->json($result);
    }

    public function edit(Request $request, $user_id)
    {
        $validator = \Validator::make($request->all(), $this->validation_rules, $this->validation_messages);

        if ($validator->fails()) {
            return response()->json(array(
                    'success'   => false,
                    'status'    => 'fail',
                    'data'      => $validator->errors()), 400);
        }

        $user = new User;
        $result = $user->edit($request->all(), $user_id);
        return response()->json($result);
    }
}
