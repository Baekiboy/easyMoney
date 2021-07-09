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
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Support\Str;
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
            Mail::to($user)->queue(new EmailVerification($user));
            return response()->json(['status' => 'true', 'message' => 'user created successfully']);
        } catch (Throwable $e) {
            return   response()->json(['status' => 'false', 'message' => 'error'], 501);
        }
    }
    function verify(Request $req){
        $user=User::find($req->id);
        if($user){
            $user->verified=1;
            $user->save();
            return response('done');
        }
    }

    function reset_email(Request $request){
        $validator = Validator::make($request->all(), [
            'email' => 'required',
        ])->validate();
        $status= Password::sendResetLink($request->only('email'));
        return $status ;
    }
    function reset(Request $request){

       $request->validate([
        'token' => 'required',
        'email' => 'required|email',
        'password' => 'required',
    ]);

    $status = Password::reset(
        $request->only('email', 'password', 'password_confirmation', 'token'),
        function ($user, $password) {
            $user->forceFill([
                'password' => Hash::make($password)
            ])->setRememberToken(Str::random(60));

            $user->save();

            event(new PasswordReset($user));
        }
    );

    return
        $status === Password::PASSWORD_RESET
            ? response('done',200)
            : response('error',405);
    }

}
