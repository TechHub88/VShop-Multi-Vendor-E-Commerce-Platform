<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DeviceSession extends Model
{
    protected $fillable = [
        'customer_id',
        'session_id',
        'device',
        'ip_address',
        'user_agent',
        'last_active'
    ];

    protected $casts = [
        'last_active' => 'datetime'
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }
}
