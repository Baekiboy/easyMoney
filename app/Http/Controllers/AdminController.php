<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
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
    function users_list(){
        $users=User::all();
        return view('admin.home',compact('users'));
    }

    function waiting_users(){
        $users=User::whereHas('document_id', function (Builder $query) {
            $query->where('status',  'like','waiting');
        })->with(['document_id','document_id.passport', 'document_id.id_card', 'document_id.drivers_licence'])->get();
        // return $users;
        return view("admin.waiting",compact('users'));
    }
    function accept_user($id){
        try{
            $user=User::findOrFail($id);
            $doc=$user->document_id;
            $doc->status='completed';
            $user->doc_verified=1;
            $user->save();
            $doc->save();
            return redirect('/waiting');
        }catch(Throwable $th){
            return
            redirect('/waiting');

        }

    }
    function refuse_user($id){
        try{
            $user=User::findOrFail($id);
            $doc=$user->document_id;
            $doc->status='refused';
            $doc->save();
            return redirect('/waiting');
        }catch(Throwable $th){
            return response('error',405);

        }

    }

}
