<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Reward extends Model
{
    use HasFactory;

    protected $fillable = [
        'customer_id',
        'type',
        'points',
        'amount',
        'description',
        'referral_id',
        'status',
        'expiry_date'
    ];

    protected $casts = [
        'expiry_date' => 'date',
        'points' => 'integer',
        'amount' => 'decimal:2'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get valid, unexpired reward points for a customer using FIFO.
     */
    public static function getValidPoints($customerId)
    {
        // Get all positive active points that have not expired yet
        $earned = self::where('customer_id', $customerId)
            ->where('type', 'points')
            ->where('points', '>', 0)
            ->where(function ($q) {
                $q->whereNull('expiry_date')
                  ->orWhere('expiry_date', '>=', now());
            })
            ->orderBy('id', 'asc')
            ->get();

        // Get total redeemed points (sum of negative points entries)
        $redeemed = abs(self::where('customer_id', $customerId)
            ->where('type', 'points')
            ->where('points', '<', 0)
            ->sum('points'));

        $validPoints = 0;
        foreach ($earned as $reward) {
            if ($redeemed >= $reward->points) {
                $redeemed -= $reward->points;
            } else {
                $validPoints += ($reward->points - $redeemed);
                $redeemed = 0;
            }
        }

        return $validPoints;
    }

    /**
     * Process referral reward for referrer when a referred customer registers/approves.
     */
    public static function processReferralReward($referredCustomer)
    {
        if ($referredCustomer->referred_by) {
            $alreadyRewarded = self::where('customer_id', $referredCustomer->referred_by)
                ->where('description', 'like', '%Referral successful bonus for referring Customer #' . $referredCustomer->id . '%')
                ->exists();

            if (!$alreadyRewarded) {
                $referrer = Customer::find($referredCustomer->referred_by);
                if ($referrer) {
                    Referral::create([
                        'customer_id' => $referrer->id,
                        'referred_customer_id' => $referredCustomer->id,
                        'referral_code' => $referrer->referral_code,
                        'status' => 'successful',
                        'reward_amount' => 1.00, // 10 points = ₹1
                        'completed_at' => now(),
                    ]);

                    self::create([
                        'customer_id' => $referrer->id,
                        'type'        => 'points',
                        'points'      => 10,
                        'amount'      => 1.00,
                        'description' => 'Referral successful bonus for referring Customer #' . $referredCustomer->id,
                        'status'      => 'active',
                        'expiry_date' => now()->addDays(60),
                    ]);

                    // Sync referrer's points
                    $referrer->reward_points = self::getValidPoints($referrer->id);
                    $referrer->save();
                }
            }
        }
    }

    public function referral()
    {
        return $this->belongsTo(Referral::class, 'referral_id');
    }
}