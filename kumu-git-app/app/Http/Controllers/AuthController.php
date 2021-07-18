<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function register(Request $request) {
        $fields = $request->validate([
            'name'=> 'required|string',
            'email' => 'required|string|unique:users,email',
            'github_username' => 'required|string:unique:users,github_username',
            'password' => 'required|string|confirmed'
        ]);

        $user = User::create([
            'name' => $fields['name'],
            'email' => $fields['email'],
            'github_username' => $fields['github_username'],
            'password' => bcrypt($fields['password'])
        ]);

        $token = $user->createToken('userToken')->plainTextToken;

        $user_obj = [
            'user' => $user,
        ];

        $response = [
            'result' => $user_obj,
            'token' => $token,
            'status' => 'success',
            'message' => 'Registration successful.'
        ];

        return response($response,201);

    }



    public function login(Request $request) {
        $fields = $request->validate([
            'email' => 'required|string',
            'password' => 'required|string'
        ]);
        
        $user = User::where('email', $fields['email'])->first();

        if (!$user || !Hash::check($fields['password'], $user->password)) {
            return response([
                'message' => 'Bad credentials.'
            ], 401);
        }

        $token = $user->createToken('userToken')->plainTextToken;

        $user_obj = [
            'user' => $user,
        ];

        $response = [
            'result' => $user_obj,
            'token' => $token,
            'status' => 'success',
            'message' => 'Login successful.'
        ];

        return response($response,201);
    }


    public function logout(Request $request) {
        auth()->user()->tokens()->delete();

        $response = [
            'status' => 'success',
            'message' => 'Logged out successful'
        ];

        return response($response,200);
    }
}
