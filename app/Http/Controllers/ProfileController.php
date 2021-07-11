<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Throwable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;

class ProfileController extends Controller
{
    function index(Request $request)
    {
        try {
            $user = Auth::user();
            $user->first_name = $request->first_name;
            $user->last_name = $request->last_name;
            $user->birthday = $request->birthday;
            $user->country = $request->country;
            $user->gender = $request->gender;
            $user->address = $request->address;
            $user->phone = $request->phone;
            $user->save();
            return response()->json(['status' => 'true', 'message' => 'user updated successfully', 'user' => $user]);
        } catch (Throwable $th) {
            return   response()->json(['status' => 'false', 'message' => 'error'], 501);
        }
    }
    function current_user(){
        return Auth::user();
    }
    function doc_verify(Request $request)
    {
        $user = Auth::user();
        $type = $request->type;
        $user_doc = $user->document_id()->create(['type' => $type, 'status' => 'waiting']);
        try {
            if ($type == 'driving licence') {
                $front_path = $request->file('front_image')->store('public');
                $back_path = $request->file('back_image')->store('public');
                $user_doc->drivers_licence()->create([
                    'front_photo_path' => $front_path,
                    'back_photo_path' => $back_path
                ]);
            }
            if ($type == 'ID card') {
                $front_path = $request->file('front_image')->store('public');
                $back_path = $request->file('back_image')->store('public');
                $user_doc->id_card()->create([
                    'front_photo_path' => $front_path,
                    'back_photo_path' => $back_path
                ]);
            }
            if ($type == 'passport') {
                $path = $request->file('image')->store('public');
                $user_doc->passport()->create([
                    'photo_path' => $path,
                ]);
            }
            return response()->json(['status' => 201, 'message' => 'done']);
        } catch (Throwable $th) {
            return response()->json(['status' => 501, 'message' => 'error']);
        }
    }


    function current()
    {
        return Auth::user()->document_id;
    }
    function bank_verify()
    {
    }
    function change_username(Request $req)
    {
        $validator = Validator::make($req->all(), [
            'username' => 'required|unique:users'
        ])->validate();

        $user  = Auth::user();
        try {
            $user->username = $req->username;
            $user->save();
            return response('done');
        } catch (Throwable $th) {
            return response($th);
        }
    }
    function change_email(Request $req)
    {
        $validator = Validator::make($req->all(), [
            'last_email' => 'required',
            'email' => 'required|unique:users'
        ])->validate();

        $user  = Auth::user();
        if ($user->email != $req->last_email) {
            return abort(401);
        }

        try {
            $user->email = $req->email;
            $user->save();
            return response('done');
        } catch (Throwable $th) {
            return response($th);
        }
    }
    function change_password(Request $req)
    {
        $validator = Validator::make($req->all(), [
            'password' => 'password|required',
            'new_password' => 'required|confirmed'
        ])->validate();

        $user  = Auth::user();
        try {
            $user->password = Hash::make($req->new_password);
            $user->save();
            return response('done');
        } catch (Throwable $th) {
            return response($th);
        }
    }
}
