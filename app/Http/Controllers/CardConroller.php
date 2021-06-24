<?php

namespace App\Http\Controllers;

use App\Mail\TransferVerification;
use App\Models\Card;
use App\Models\Transaction;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Faker\Factory as Faker;
use Illuminate\Support\Facades\Http;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;
use Illuminate\Support\Facades\Mail;

class CardConroller extends Controller
{

    function card()
    {
        $card = Auth::user()->card;
        if (!$card) {
            return response()->json(['message' => 'no card']);
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

    function transfer(Request $req)
    {
        $card = Auth::user()->card;
        $amount = $req->amount;
        if ($card->amount < $amount) {
            return response('not enough', 405);
        }
        $reciever = User::where('phone', $req->phone)->firstOrFail();
        $transaction = $card->transactions()->create([
            'reciever_id' => $reciever->id,
            'amount' => $amount,
            'status' => 'pending',
            'verification_number' => rand(100, 999)
        ]);
        Mail::to($reciever)->send(new TransferVerification($transaction));
        return response()->json(['message' => 'email sent', 'transaction' => $transaction]);
        // $card->amount-=$amount;
        // $card->save();
        // $reciver_card=$reciever->card;
        // $reciver_card->amount+=$amount;
        // $card->transactions()->create([
        //     'reciever_id'=>$reciever->id,
        //     'amount'=>$amount
        // ]);
        // $reciver_card->save();
        // $card->save();

        // return response('done',201);
    }
    function make_transfer(Request $req)
    {
        $user = Auth::user();
        $transaction = Transaction::find($req->transaction_id);
        if ($transaction->verification_number == $req->verification_number) {
            $card = $user->card;
            $amount = $transaction->amount;
            $card->amount -= $amount;
            $card->save();
            $reciever = User::find($transaction->reciever_id);
            $reciver_card = $reciever->card;
            $reciver_card->amount += $amount;
            $reciver_card->save();
            $card->save();

            return response('done', 201);
        }
        return response('wrong verification code', 405);
    }
}
