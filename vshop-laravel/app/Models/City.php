<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class City extends Model
{
    protected $table = 'cities';

    protected $primaryKey = 'id'; // ✅ MUST

    public $timestamps = false;

    protected $fillable = [
        'country_id',
        'state_id',
        'name'
    ];

    /* ================= RELATIONS ================= */

    public function country()
    {
        return $this->belongsTo(Country::class,'country_id','id');
    }

    public function state()
    {
        return $this->belongsTo(State::class,'state_id','id');
    }
}
