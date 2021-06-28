<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class document_id extends Model
{
    use HasFactory;
    protected $guarded=[];


    public function user(){
        return $this->belongsTo(User::class);
    }
    public function passport(){
        return $this->hasOne(Passport::class,'doc_id');
    }
    public function id_card()
    {
        return $this->hasOne(id_card::class, 'doc_id');
    }
    public function drivers_licence()
    {
        return $this->hasOne(drivers_licence::class, 'doc_id');
    }

}
