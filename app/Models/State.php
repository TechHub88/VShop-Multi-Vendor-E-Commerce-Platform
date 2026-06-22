<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class State extends Model
{
    protected $table = 'states';

    protected $primaryKey = 'id'; // ✅ VERY IMPORTANT

    public $timestamps = false; // अगर timestamps नहीं हैं

    protected $fillable = [
        'name',
        'country_id'
    ];

    /* ================= RELATIONS ================= */

    // State → Country
    public function country()
    {
        return $this->belongsTo(Country::class,'country_id','id');
    }

    // State → Cities (districts)
    public function cities()
    {
        return $this->hasMany(City::class,'state_id','id');
    }

    // State → Seller Areas
    public function areas()
    {
        return $this->hasMany(SellerOperationArea::class,'state_id','id');
    }

    // Optional
    public function taxRules()
    {
        return $this->hasMany(TaxRule::class);
    }
}