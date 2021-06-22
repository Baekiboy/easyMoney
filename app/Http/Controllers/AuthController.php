<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Throwable;

class AuthController extends Controller
{

    function index(Request $request)
    {
        $user = User::where('username', $request->username)->first();
        // print_r($data);
        if (!$user || !Hash::check($request->password, $user->password)) {
            return response([
                'message' => ['These credentials do not match our records.']
            ], 404);
        }

        $token = $user->createToken('my-app-token')->plainTextToken;

        $response = [
            'user' => $user,
            'token' => $token
        ];

        return response($response, 201);
    }


    function register(Request $request)
    {


        $validated = $request->validate([
            'email' => 'required|unique:users',
            'password' => 'required',
            'username' => 'required',
        ]);
        try {
            User::create([
                'email' => $request->email,
                'username' => $request->username,
                'password' => Hash::make($request->password),
            ]);
            return response()->json(['status' => 'true', 'message' => 'user created successfully']);
        } catch (Throwable $e) {
            return   response()->json(['status' => 'false', 'message' => 'error'], 501);
        }
    }

}
