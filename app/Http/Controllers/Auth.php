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
        $fields['role'] = 'user';

        $user = User::create([
            'firstname' => $fields['firstname'],
            'lastname' => $fields['lastname'],
            'email' => $fields['email'],
            'address' => $fields['address'],
            'phone' => $fields['phone'],
            'password' => bcrypt($fields['password']),
            'role' => $fields['role'],
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
        $user = User::where('email', $fields['email'])->first();

        if (!$user || !Hash::check($fields['password'], $user->password)) {
            return response([
                'message' => 'wrong creds'
            ], 401);
        }

        if ($user->role === 'admin') {
            return response()->json(['message' => 'Admins are not allowed to log in to this app'], 403);
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

    public function updateProfile(Request $request)
    {
        $user = $request->user(); // Récupérer l'utilisateur authentifié

        $requestData = $request->validate([
            'firstname' => 'string',
            'lastname' => 'string',
            'email' => 'email|unique:users,email,' . $user->id,
            'address' => 'string',
            'phone' => 'digits:10|numeric',
            'password' => 'min:6|regex:/^(?=.*[A-Za-z])(?=.*\d)(?=.*[@$!%*#?&])[A-Za-z\d@$!%*#?&]{6,}$/',
        ]);

        if (isset($requestData['password'])) {
            $requestData['password'] = bcrypt($requestData['password']);
        }

        $user->fill($requestData);
        $user->save();

        return response()->json(['message' => 'User profile updated successfully!']);
    }

    public function getProfile(Request $request)
    {
        $user = $request->user();
        return response()->json($user);
    }

    public function destroy($id)
    {
        $user = User::findOrFail($id);
        $user->delete();
        return response()->json(['message' => 'User deleted successfully!']);
    }
    public function checkPassword(Request $request)
    {
        $user = $request->user(); // Get the authenticated user

        $request->validate([
            'password' => 'required|min:6|regex:/^(?=.*[A-Za-z])(?=.*\d)(?=.*[@$!%*#?&])[A-Za-z\d@$!%*#?&]{6,}$/',
        ]);

        if (Hash::check($request->password, $user->password)) {
            return response()->json(['message' => 'Password is correct'], 200);
        } else {
            return response()->json(['message' => 'Password is incorrect'], 400);
        }
    }
    public function changePassword(Request $request)
    {
        $user = $request->user(); // Get the authenticated user

        $request->validate([
            'password' => 'required|min:6|regex:/^(?=.*[A-Za-z])(?=.*\d)(?=.*[@$!%*#?&])[A-Za-z\d@$!%*#?&]{6,}$/',
        ]);

        $user->password = bcrypt($request->password);
        $user->save();

        return response()->json(['message' => 'Password changed successfully!']);
    }
}
