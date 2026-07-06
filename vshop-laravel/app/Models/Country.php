<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Country extends Model
{
    protected $table = 'countries';

    protected $fillable = [
        'name',
        'code'
    ];

    /* ================= RELATIONS ================= */

    // Country → States
    public function states()
    {
        return $this->hasMany(State::class,'country_id');
    }

    // Country → Seller Areas
    public function areas()
    {
        return $this->hasMany(SellerOperationArea::class,'country_id');
    }
}
