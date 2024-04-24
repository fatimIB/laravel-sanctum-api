<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;

class Auth extends Controller
{
    public function register(Request $request)
    {
        $fields = $request->validate([
            'firstname' => 'required|string',
            'lastname' => 'required|string',
            'email' => 'required|email|unique:users,email',
            'address' => 'required|string',
            'phone' => 'required|digits:10|numeric',
            'password' => 'required|min:6|regex:/^(?=.*[A-Za-z])(?=.*\d)(?=.*[@$!%*#?&])[A-Za-z\d@$!%*#?&]{6,}$/',
        ]);
        $user = User::create([
            'firstname' => $fields['firstname'],
            'lastname' => $fields['lastname'],
            'email' => $fields['email'],
            'address' => $fields['address'],
            'phone' => $fields['phone'],
            'password' => bcrypt($fields['password']),
        ]);

        $token = $user->createToken('mytoken')->plainTextToken;

        $response = [
            'user' => $user,
            'token' => $token
        ];
        return response($response, 201);
    }


    public function login(Request $request)
    {
        $fields = $request->validate([
            'email' => 'required|email',
            'password' => 'required|min:6|regex:/^(?=.*[A-Za-z])(?=.*\d)(?=.*[@$!%*#?&])[A-Za-z\d@$!%*#?&]{6,}$/',
        ]);
        $user = User::where('email',$fields['email'])->first();

        if(!$user || !Hash::check($fields['password'],$user->password)){
            return response([
                'message'=>'wrong creds'
            ], 401);
        }

        $token = $user->createToken('mytoken')->plainTextToken;

        $response = [
            'user' => $user,
            'token' => $token
        ];
        return response($response, 201);
    }

    public function logout(Request $request)
    {
        $request->user()->tokens()->delete();

        return [
            'message' => 'Logged out'
        ];
    }
}
