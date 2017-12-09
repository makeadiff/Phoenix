<?php
namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function add(Request $request)
    {
        $this->validate($request, [
            'name'      => 'required|max:50',
            'email'     => 'required|email|unique:User',
            'mad_email' => 'email|unique:User',
            'password'  => 'required',
            'phone'     => 'required|unique:User|regex:/[\+0-9]{10,13}/',
            'city_id'   => 'required|numeric'
        ]);

        $result = User::add($request->all());

        return response()->json($result);
    }

    public function edit(Request $request, $user_id)
    {
        $this->validate($request, [
            'name'      => 'required|max:50',
            'email'     => 'required|email|unique:User',
            'mad_email' => 'email|unique:User',
            'password'  => 'required',
            'phone'     => 'required|unique:User|regex:/[\+0-9]{10,13}/',
            'city_id'   => 'required|numeric'
        ]);

        $user = new User;
        $result = $user->edit($request->all(), $user_id);
        return response()->json($result);
    }
}
