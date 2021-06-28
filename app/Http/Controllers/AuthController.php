<?php

namespace App\Http\Controllers;

use App\Mail\EmailVerification;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Throwable;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Password;

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

        $validator=Validator::make($request->all(), [
            'email' => 'required|unique:users',
            'password' => 'required',
            'username' => 'required|unique:users',
        ]);
        // $validator = $request->validate([
        //     'email' => 'required|unique:users',
        //     'password' => 'required',
        //     'username' => 'required|unique:users',
        // ]);
            if($validator->fails()){
                return response()->json($validator->getMessageBag());
            }
        try {
            $user=User::create([
                'email' => $request->email,
                'username' => $request->username,
                'password' => Hash::make($request->password),
            ]);
            Mail::to($user)->send(new EmailVerification($user));
            return response()->json(['status' => 'true', 'message' => 'user created successfully']);
        } catch (Throwable $e) {
            return   response()->json(['status' => 'false', 'message' => 'error'], 501);
        }
    }
    function verify(User $user){
        if($user){
            $user->verified=0;
            $user->save();
            return redirect('http://127.0.0.1:3000/');
        }
    }

    function reset_email(Request $request){
        $validator = Validator::make($request->all(), [
            'email' => 'required|unique:users',
        ]);
        $status= Password::sendResetLink($request->only('email'));
        return $status ;
    }
    function reset(){
        return redirect('http://127.0.0.1:5500/');
    }

}
