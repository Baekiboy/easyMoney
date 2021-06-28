<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class id_card extends Model
{
    use HasFactory;
    protected $guarded = [];

    public function document()
    {
        return $this->belongsTo(document_id::class);
    }
}
