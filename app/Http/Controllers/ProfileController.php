<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Throwable;
use Illuminate\Support\Facades\DB;

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
            return response()->json(['status' => 'true', 'message' => 'user updated successfully']);
        } catch (Throwable $th) {
            return   response()->json(['status' => 'false', 'message' => 'error'], 501);
        }
    }

    function doc_verify(Request $request)
    {
        $type = $request->type;
        try {

            if ($type === 1) {
                $front_path = $request->file('front_image')->store('public');
                $back_path = $request->file('back_image')->store('public');
                DB::table('drivers_licences')->insert([
                    'user_id' => Auth::user()->id,
                    'front_photo_path' => $front_path,
                    'back_photo_path' => $back_path
                ]);
            }
            if ($type === 3) {
                $front_path = $request->file('front_image')->store('public');
                $back_path = $request->file('back_image')->store('public');
                DB::table('id_cards')->insert([
                    'user_id' => Auth::user()->id,
                    'front_photo_path' => $front_path,
                    'back_photo_path' => $back_path
                ]);
            }
            if ($type === 2) {
                $path = $request->file('image')->store('public');
                DB::table('passports')->insert([
                    'user_id' => Auth::user()->id,
                    'photo_path' => $path,

                ]);
            }
            DB::table('document_ids')->insert(['user_id'=> Auth::user()->id,'type'=>$type,'status'=>'waiting']);
            return response()->json(['status' => 201, 'message' => 'good']);
        } catch (Throwable $th) {
            return response()->json(['status' => 501, 'message' => 'error']);
        }
    }


    function current(){
        return  DB::table('document_ids')->where('user_id', Auth::user()->id)->first();
    }
    function bank_verify(){

    }
}
