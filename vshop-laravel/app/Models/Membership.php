<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Membership extends Model
{
    use HasFactory;

    protected $table = 'memberships';

    protected $fillable = [
        'customer_id',
        'plan',
        'status',
        'start_date',
        'expiry_date',
        'auto_renew',
        'amount_paid',
        'payment_method'
    ];

    protected $casts = [
        'start_date' => 'date',
        'expiry_date' => 'date',
        'auto_renew' => 'boolean',
        'amount_paid' => 'decimal:2'
    ];

    /**
     * Get the customer that owns the membership.
     */
    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    /**
     * Check if membership is active.
     */
    public function isActive()
    {
        return $this->status === 'active' && $this->expiry_date >= now();
    }

    protected static function boot()
    {
        parent::boot();

        static::saved(function ($membership) {
            if ($membership->status === 'active' && ($membership->isDirty('status') || $membership->wasRecentlyCreated)) {
                $recentRewardExists = Reward::where('customer_id', $membership->customer_id)
                    ->where('description', 'like', '%Premium Membership%')
                    ->where('created_at', '>=', now()->subMinute())
                    ->exists();

                if (!$recentRewardExists) {
                    Reward::create([
                        'customer_id' => $membership->customer_id,
                        'type'        => 'points',
                        'points'      => 150,
                        'amount'      => 15.00,
                        'description' => 'Reward points for Premium Membership purchase',
                        'status'      => 'active',
                        'expiry_date' => now()->addDays(60),
                    ]);

                    $customer = $membership->customer;
                    if ($customer) {
                        $customer->reward_points = Reward::getValidPoints($customer->id);
                        $customer->save();
                    }
                }
            }
        });
    }
}
