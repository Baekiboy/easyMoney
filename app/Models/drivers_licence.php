<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class drivers_licence extends Model
{
    use HasFactory;
    protected $guarded = [];

    public function document()
    {
        return $this->belongsTo(document_id::class);
    }
}
