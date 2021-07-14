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
        // if (!$card) {
        //     return response()->json(['message' => 'no card'],404);
        // }
        return $card;
    }

    function make_card()
    {
        $user = Auth::user();
        if ($user->card()->exists()) {
            return response('card already exists', 409);
        }
        $faker = Faker::create();
        $number = '4' . $faker->numerify('###############');
        $cvv = $faker->numerify('###');
        $card = new Card([
            'number' => $number, 'cvv' => $cvv, 'exp_date' => Carbon::now()->addYears(3)->toDateString()
        ]);
        $user->card()->save($card);
        return response()->json(['card'=>$card,'message'=>'success']);
    }

    function conversion(Request $request)
    {
        $from = $request->from;
        $to = $request->to;
        return (Http::get('https://free.currconv.com/api/v7/convert?q=' . $from . '_' . $to . '&compact=ultra&apiKey=' . env('CONVERTER_API_KEY')));
    }

    function transfer(Request $req)
    {
        $user=Auth::user();
        $card = $user->card;
        if(!$card){
            return response('no card',405);
        }
        $amount = $req->amount;
        if ($card->amount < $amount) {
            return response('not enough money', 405);
        }
        $reciever = User::where('phone', $req->phone)->firstOrFail();
        if($reciever->id===$user->id){
            return response('not implemented', 405);
        }
        $transaction = $card->transactions()->create([
            'reciever_id' => $reciever->id,
            'amount' => $amount,
            'status' => 'pending',
            'verification_number' => rand(100, 999)
        ]);
        Mail::to($reciever)->queue(new TransferVerification($transaction));
        return response()->json(['message' => 'email sent', 'transaction' => $transaction],201);

    }
    function make_transfer(Request $req)
    {
        $user = Auth::user();
        $transaction = Transaction::find($req->transaction_id);
        $card = $user->card;
        if ($card->id != $transaction->card_id) {
            return abort(401);
        }
        if ($transaction->status === 'completed') {
            return response('The Transaction is already closed', 405);
        }
        if ($transaction->verification_number == $req->verification_number) {
            $amount = $transaction->amount;
            if ($card->amount < $amount) {
                return response('Insuffisant funds', 405);
            }
            $card->amount -= $amount;
            $card->save();
            $reciever = User::find($transaction->reciever_id);
            $reciver_card = $reciever->card;
            $reciver_card->amount = $reciver_card->amount+ $amount;
            $reciver_card->save();
            $card->save();
            $transaction->status = 'completed';
            $transaction->save();

            return response('done', 201);
        }
        return response('wrong verification code', 405);
    }


    function history(){
        $user=Auth::user();
        $transactions= $user->card->transactions->load('reciever:phone,id');
        $recieved=$user->transactions->load('card.user:id,phone');
        return response()->json(['transactions'=>$transactions,'recieved'=>$recieved]);

    }
}
