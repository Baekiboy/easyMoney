<?php

namespace App\Http\Controllers;

use App\Models\Card;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Faker\Factory as Faker;
use Illuminate\Support\Facades\Http;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;

class CardConroller extends Controller
{

    function card()
    {
        $card = Auth::user()->card;
        if(!$card){
          return response()->json(['message'=>'no card'])  ;
        }
        return $card;
    }

    function make_card()
    {
        $user = Auth::user();
        if ($user->card()->exists()) {
            return response('card already exists', 201);
        }
        $faker = Faker::create();
        $number = '4' . $faker->numerify('###############');
        $cvv = $faker->numerify('###');
        $card = new Card([
            'number' => $number, 'cvv' => $cvv, 'exp_date' => Carbon::now()->addYears(3)
        ]);
        $user->card()->save($card);
        return response('success', 201);
    }

    function conversion(Request $request)
    {
        $from = $request->from;
        $to = $request->to;
        return (Http::get('https://free.currconv.com/api/v7/convert?q=' . $from . '_' . $to . '&compact=ultra&apiKey=' . env('CONVERTER_API_KEY')));
    }




}
