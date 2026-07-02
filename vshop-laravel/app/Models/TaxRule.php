<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TaxRule extends Model
{
    protected $fillable = [
        'state_id', 'gst', 'cgst', 'sgst', 'igst'
    ];

    public function state()
    {
        return $this->belongsTo(State::class);
    }
}



