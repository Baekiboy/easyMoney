<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;
    protected $guarded=[];
    public function card(){
        return $this->belongsTo(Card::class);
    }
    public function reciever()
    {
        return $this->belongsTo(User::class);
    }
}
