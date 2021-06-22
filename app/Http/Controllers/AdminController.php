<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Throwable;

class AdminController extends Controller
{
    function users()
    {
        if(!Auth::user()->hasRole('admin')){
            abort(401);
        }
        return User::with('document_id')->get();
    }

    function user(Request $request)
    {
        if (!Auth::user()->hasRole('admin')) {
            abort(401);
        }
        $user = User::find($request->user_id);
        $doc_id = $user->document_id;
        if ($doc_id->type === 1) {
            return  DB::table('drivers_licences')->where('user_id', $user->id)->first();
        }

        if ($doc_id->type === 2) {
            return  DB::table('passports')->where('user_id', $user->id)->first();
        }

        if ($doc_id->type === 3) {
            return  DB::table('id_cards')->where('user_id', $user->id)->first();
        }
    }

    function verify(Request $request){
        if (!Auth::user()->hasRole('admin')) {
            abort(401);
        }
        try{

            $user=User::find($request->user_id);
            $doc= $user->document_id;
            $doc->status='verified';
            $doc->save();
            return response()->json(['message'=>'success']);
        }catch(Throwable $th){
            return $th;
        }
    }
}
