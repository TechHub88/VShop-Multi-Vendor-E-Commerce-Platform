<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SellerOperationArea extends Model
{
    protected $table = 'seller_operation_areas';

    protected $fillable = [
        'seller_id',
        'country_id',
        'state_id',
        'district_id',
        'location_type','city_id','group_id'
    ];
  public function seller()
    {
        return $this->belongsTo(Seller::class, 'seller_id', 'id');
    }

public function country()
{
    return $this->belongsTo(Country::class,'country_id');
}

public function state()
{
    return $this->belongsTo(State::class,'state_id');
}


public function city()
{
    return $this->belongsTo(City::class,'city_id');
}
}