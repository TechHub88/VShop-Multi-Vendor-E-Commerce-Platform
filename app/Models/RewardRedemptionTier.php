<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RewardRedemptionTier extends Model
{
    protected $fillable = [
        'points_required',
        'discount_amount',
        'status',
    ];
}
