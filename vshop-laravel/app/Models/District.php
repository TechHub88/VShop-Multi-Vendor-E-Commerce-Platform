<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class District extends Model
{
    protected $table = 'districts';

    protected $fillable = [
        'state_id',
        'name'
    ];

    /* ================= RELATIONS ================= */

    // District → State
    public function state()
    {
        return $this->belongsTo(State::class,'state_id');
    }

    // District → Country (Through State)
    public function country()
    {
        return $this->state->country();
    }

    // District → Seller Areas
    public function areas()
    {
        return $this->hasMany(SellerOperationArea::class,'district_id');
    }
}
