<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\ActivityLog;

class Order extends Model
{
    protected $fillable = [

        // ================= BASIC =================
        'seller_id',
        'customer_id',
        'user_id',
        'payment_method',
        'payment_status',
        'payment_attempts',
        'payment_type', 

        'sub_total',
        'gst_amount',
        'courier_charge',
        'total_amount',
        'paid_amount',
        'balance',

        'status',

      
        'delivery_address_id',
        'billing_address_id',
        'order_date',

       
        'cgst',
        'sgst',
        'igst',

        // ================= SHIPPING =================
        'shipping_method',
        'tracking_id',

        // ================= PAYMENT HANDLING =================
        'payment_attempts',
        'last_payment_attempt_at',

        // ================= REMINDER SYSTEM =================
        'reminder_sent_at',
        'reminder_count',
        'last_reminder_note',

        // ================= REFUND / RETURN =================
        'refund_status',            // initiated / processing / completed / failed
        'refund_amount',
        'refund_initiated_at',
        'refund_completed_at',

        'return_reason',
        'return_requested_at',

        // ================= SLA =================
        'sla_status',              // within / breached
        'sla_deadline',
        'sla_breached_at',

        // ================= WORKFLOW =================
        'approved_by',
        'start_date',
        'end_date',
        'remarks',
        'privacy',

        // ================= WALLET =================
        'wallet_balance',

        // ================= LEGACY =================
        'address_id','sr_order_id',
'sr_shipment_id',
'sr_awb_code',

'courier_name',
'courier_service',

'shipping_zone',
'shipping_cost',
'estimated_delivery_days',

'shipped_at',
'delivered_at',// Add these new columns
        'fraud_status',
        'fraud_score',
        'fraud_reason',
        'refund_amount',
        'refund_reason',
        'refunded_at','discount_amount',
        
        // ================= ESCALATION WORKFLOW =================
        'cancel_status',
        'return_status_workflow',
        'escalated_to_admin_at',
        'seller_action_deadline',
        
    ];

    /*
    |--------------------------------------------------------------------------
    | RELATIONS
    |--------------------------------------------------------------------------
    */
 public function seller()
    {
        return $this->belongsTo(Seller::class, 'seller_id');
    }
    public function shippingAddress()
    {
        return $this->belongsTo(Address::class, 'delivery_address_id');
    }

    public function billingAddress()
    {
        return $this->belongsTo(Address::class, 'billing_address_id');
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id')
                    ->with('firstAddress');
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class)->with([
            'product',
            'color',
            'size'
        ]);
    }

    protected $casts = [
        'order_date' => 'datetime',
        'shipped_at' => 'datetime',
        'delivered_at' => 'datetime',
        'estimated_delivery_date' => 'datetime',
        'sla_deadline' => 'datetime',
    ];

    public function returns()
    {
        return $this->hasMany(OrderReturn::class);
    }

    public function addresses()
    {
        return $this->hasMany(Address::class, 'customer_id', 'id');
    }

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    public function activityLogs()
    {
        return $this->hasMany(ActivityLog::class, 'order_id')->orderBy('created_at', 'asc');
    }


    /*
    |--------------------------------------------------------------------------
    | DYNAMIC COMMISSION SPLIT
    |--------------------------------------------------------------------------
    */

    public function getCommissionSplitAttribute()
    {
        // Default fallbacks
        $sellerComm = 20;
        $referrerComm = 10;
        $platformComm = 70;

        $sellerId = $this->seller_id;
        
        // If order doesn't have seller_id directly, try to get it from the first item
        if (!$sellerId && $this->relationLoaded('items') && $this->items->isNotEmpty()) {
            $sellerId = $this->items->first()->product->created_by_id ?? null;
        }
        $appType = strtolower($this->app_type ?? 'v-shop');
        
        $sellerComm = 100;
        $referrerComm = 0;
        $platformComm = 0;

        try {
            static $rules = null;
            if ($rules === null) {
                $rules = \Illuminate\Support\Facades\DB::table('commission_management')->get();
            }
            
            // Priority 1: Exact Match
            $specificRule = $rules->first(function ($rule) use ($sellerId, $appType) {
                return $rule->seller_id == $sellerId && strtolower($rule->app_type) == $appType;
            });
                                  
            if ($specificRule) {
                $platformComm = $specificRule->platform_commission;
            } else {
                // Priority 2: Match by App Type with no specific seller
                $appRule = $rules->whereNull('seller_id')
                                 ->where('app_type', $appType)
                                 ->first();
                if ($appRule) {
                    $platformComm = $appRule->platform_commission;
                } else {
                    // Priority 3: Generic fallback in DB
                    $generalRule = $rules->whereNull('seller_id')->first();
                    if ($generalRule) {
                        $platformComm = $generalRule->platform_commission;
                    }
                }
            }
        } catch (\Exception $e) {
            // Ignore DB errors and use default
            $platformComm = 1.5;
        }
        
        $totalAmount = (float) ($this->total_amount ?? 0);
        
        // Use the actual referral commission stored on the order!
        if (!empty($this->referrer_id) || !empty($this->referred_by)) {
            $referrerComm = (float) ($this->referral_commission_percent ?? 0);
            $referrerAmt = (float) ($this->referral_commission_amount ?? 0);
        } else {
            $referrerComm = 0;
            $referrerAmt = 0;
        }

        $platformAmt = $totalAmount * ($platformComm / 100);
        
        // Final Seller share is whatever is left after taking out platform and referral commissions
        $sellerAmt = $totalAmount - $platformAmt - $referrerAmt;
        $sellerComm = 100 - $platformComm - $referrerComm;

        return (object) [
            'seller' => max(0, $sellerAmt),
            'referrer' => $referrerAmt,
            'platform' => $platformAmt,
            'seller_pct' => $sellerComm,
            'referrer_pct' => $referrerComm,
            'platform_pct' => $platformComm,
            'debug_seller_id' => $sellerId,
            'debug_app_type' => $appType,
        ];
    }

    public function getSellerShareAttribute() { return $this->commission_split->seller; }
    public function getReferrerShareAttribute() { return $this->commission_split->referrer; }
    public function getPlatformShareAttribute() { return $this->commission_split->platform; }


    public function getSellerNameAttribute() {
        if ($this->seller) return $this->seller->name;
        
        // Try getting from items if order has no direct seller
        $firstItem = $this->items()->with('product.seller')->first();
        if ($firstItem && $firstItem->product && $firstItem->product->seller) {
            return $firstItem->product->seller->name;
        }
        return 'N/A';
    }

    public function getReferrerNameAttribute() {
        // Find referrer if exists, assuming customer -> referrer or similar logic. 
        // Returning N/A for now if not available to fix the N/A display issue.
        return 'N/A'; 
    }

    /*
    |--------------------------------------------------------------------------
    | MODEL EVENTS (AUTO LOGGING)
    |--------------------------------------------------------------------------
    */

    protected static function boot()
    {
        parent::boot();

        // 🔥 STATUS CHANGE LOG
        static::updating(function ($order) {

            if ($order->isDirty('status')) {

                ActivityLog::create([
                    'order_id'      => $order->id,
                    'customer_id'   => $order->customer_id,
                    'activity_type' => 'status_change',
                    'old_status'    => $order->getOriginal('status'),
                    'new_status'    => $order->status,
                    'description'   => 'Order status changed',
                    'remarks'       => 'Changed from ' . $order->getOriginal('status') . ' to ' . $order->status,
                ]);
            }

            // 🔥 PAYMENT STATUS CHANGE
            if ($order->isDirty('payment_status')) {

                ActivityLog::create([
                    'order_id'      => $order->id,
                    'customer_id'   => $order->customer_id,
                    'activity_type' => 'payment_status_change',
                    'old_status'    => $order->getOriginal('payment_status'),
                    'new_status'    => $order->payment_status,
                    'description'   => 'Payment status updated',
                    'remarks'       => 'Payment changed from ' . $order->getOriginal('payment_status') . ' to ' . $order->payment_status,
                ]);
            }

            // 🔥 REFUND STATUS CHANGE
            if ($order->isDirty('refund_status')) {

                ActivityLog::create([
                    'order_id'      => $order->id,
                    'customer_id'   => $order->customer_id,
                    'activity_type' => 'refund_update',
                    'new_status'    => $order->refund_status,
                    'description'   => 'Refund status updated',
                ]);
            }

        });
    }

    /**
     * Reverse all payouts and referral commission, and refund the buyer if they have paid.
     */
    public static function reverseOrderFunds(Order $order)
    {
        if ($order->refund_status === 'completed') {
            return;
        }

        $order->loadMissing('items.product');
        
        \Illuminate\Support\Facades\DB::transaction(function () use ($order) {
            $totalSellerEarning = 0;
            $totalReferralEarning = 0;
            $totalPlatformEarning = 0;

            foreach ($order->items as $item) {
                $sellerId = $order->seller_id ?? ($item->product->created_by_id ?? null);
                if (!$sellerId) {
                    continue;
                }

                $totalPrice = (float) $item->total;
                $sellerEarning = (float) $item->seller_earning;
                $referralAmount = (float) $item->referral_earning;
                $platformAmount = (float) $item->platform_earning;

                $totalSellerEarning += $sellerEarning;
                $totalReferralEarning += $referralAmount;
                $totalPlatformEarning += $platformAmount;

                // --- 1. REVERSE SELLER EARNING ---
                $sellerWallet = \App\Models\Wallet::firstOrCreate(
                    ['user_type' => 'seller', 'seller_id' => $sellerId, 'customer_id' => null],
                    ['balance' => 0]
                );
                
                // Deduct seller payout
                $sellerWallet->balance -= $sellerEarning;
                $sellerWallet->save();

                // Log individual seller reversals matching original deductions
                // Order sale earning reversal (debit)
                \App\Models\WalletTransaction::create([
                    'wallet_id' => $sellerWallet->id,
                    'seller_id' => $sellerId,
                    'user_type' => 'seller',
                    'transaction_type' => 'refund_deduction',
                    'credit' => 0,
                    'debit' => $totalPrice,
                    'balance_after' => $sellerWallet->balance + $platformAmount + $referralAmount,
                    'description' => "Order sale reversal for cancelled Order #{$order->id}",
                    'reference_id' => 'ORD-REV-' . $order->id,
                    'status' => 'completed'
                ]);

                // Platform fee reversal (credit)
                \App\Models\WalletTransaction::create([
                    'wallet_id' => $sellerWallet->id,
                    'seller_id' => $sellerId,
                    'user_type' => 'seller',
                    'transaction_type' => 'refund_reversal',
                    'credit' => $platformAmount,
                    'debit' => 0,
                    'balance_after' => $sellerWallet->balance + $referralAmount,
                    'description' => "Platform fee reversal for cancelled Order #{$order->id}",
                    'reference_id' => 'ORD-REV-' . $order->id,
                    'status' => 'completed'
                ]);

                // Referral commission reversal (credit)
                if ($referralAmount > 0) {
                    \App\Models\WalletTransaction::create([
                        'wallet_id' => $sellerWallet->id,
                        'seller_id' => $sellerId,
                        'user_type' => 'seller',
                        'transaction_type' => 'refund_reversal',
                        'credit' => $referralAmount,
                        'debit' => 0,
                        'balance_after' => $sellerWallet->balance,
                        'description' => "Referral commission reversal for cancelled Order #{$order->id}",
                        'reference_id' => 'ORD-REV-' . $order->id,
                        'status' => 'completed'
                    ]);
                }

                // Log refund deduction in seller_finances table so seller sees it in their finance panel
                \Illuminate\Support\Facades\DB::table('seller_finances')->insert([
                    'seller_id'         => $sellerId,
                    'order_id'          => $order->id,
                    'transaction_type'  => 'refund_deduction',
                    'reference_no'      => 'ORD-' . $order->id,
                    'credit'            => 0,
                    'debit'             => $sellerEarning,
                    'commission_amount' => 0,
                    'status'            => 'success',
                    'note'              => 'Refund deduction due to order cancellation',
                    'created_at'        => now(),
                    'updated_at'        => now()
                ]);

                // --- 2. REVERSE REFERRAL COMMISSION ---
                if ($referralAmount > 0 && $order->referrer_id) {
                    $referrerCustomer = \App\Models\Customer::find($order->referrer_id);
                    if ($referrerCustomer) {
                        $referrerSeller = \App\Models\Seller::where('email', $referrerCustomer->email)
                            ->orWhere('mobile', $referrerCustomer->phone)
                            ->first();

                        if ($referrerSeller) {
                            // Referrer is listed in seller panel -> reverse cash from seller wallet
                            $referrerWallet = \App\Models\Wallet::where('user_type', 'seller')
                                ->where('seller_id', $referrerSeller->id)
                                ->first();

                            if ($referrerWallet) {
                                $referrerWallet->balance -= $referralAmount;
                                $referrerWallet->save();

                                \App\Models\WalletTransaction::create([
                                    'wallet_id' => $referrerWallet->id,
                                    'seller_id' => $referrerSeller->id,
                                    'user_type' => 'seller',
                                    'transaction_type' => 'refund_deduction',
                                    'credit' => 0,
                                    'debit' => $referralAmount,
                                    'balance_after' => $referrerWallet->balance,
                                    'description' => "Referral commission reversed for cancelled Order #{$order->id}",
                                    'reference_id' => 'ORD-REV-' . $order->id,
                                    'status' => 'completed'
                                ]);

                                \Illuminate\Support\Facades\DB::table('seller_finances')->insert([
                                    'seller_id'         => $referrerSeller->id,
                                    'order_id'          => $order->id,
                                    'transaction_type'  => 'referral_reversal',
                                    'reference_no'      => 'ORD-' . $order->id,
                                    'credit'            => 0,
                                    'debit'             => $referralAmount,
                                    'commission_amount' => 0,
                                    'status'            => 'success',
                                    'note'              => 'Referral commission reversed due to order cancellation',
                                    'created_at'        => now(),
                                    'updated_at'        => now()
                                ]);
                            }
                        } else {
                            // Referrer is normal buyer -> reverse reward points
                            $rewardRecord = \App\Models\Reward::where('customer_id', $referrerCustomer->id)
                                ->where('description', 'like', "%Referral commission points%Order #{$order->id}%")
                                ->where('points', '>', 0)
                                ->first();
                            $pointsToReverse = $rewardRecord ? $rewardRecord->points : (int) $referralAmount;

                            $referrerCustomer->decrement('reward_points', $pointsToReverse);

                            \App\Models\Reward::create([
                                'customer_id' => $referrerCustomer->id,
                                'type' => 'points',
                                'points' => (int) -$pointsToReverse,
                                'amount' => -$referralAmount,
                                'description' => "Referral commission points reversed for cancelled Order #{$order->id}",
                                'status' => 'active',
                                'expiry_date' => now()->addDays(365)
                            ]);

                            $referrerCustomerWallet = \App\Models\Wallet::where('user_type', 'customer')
                                ->where('customer_id', $referrerCustomer->id)
                                ->first();

                            if ($referrerCustomerWallet) {
                                \App\Models\WalletTransaction::create([
                                    'wallet_id' => $referrerCustomerWallet->id,
                                    'customer_id' => $referrerCustomer->id,
                                    'user_type' => 'customer',
                                    'transaction_type' => 'refund_deduction',
                                    'credit' => 0,
                                    'debit' => 0,
                                    'balance_after' => $referrerCustomerWallet->balance,
                                    'description' => "Referral commission points reversed for cancelled Order #{$order->id}",
                                    'reference_id' => 'ORD-REV-' . $order->id,
                                    'status' => 'completed'
                                ]);
                            }
                        }
                    }
                }
            }

            // Mark any referral histories for this order as 'reversed'
            \Illuminate\Support\Facades\DB::table('referral_histories')
                ->where('order_id', $order->id)
                ->update(['status' => 'reversed']);

            // --- 3. REFUND BUYER ---
            if (strtolower($order->payment_status) === 'paid') {
                $buyer = \App\Models\Customer::find($order->customer_id);
                if ($buyer) {
                    $buyer->wallet_balance = ($buyer->wallet_balance ?? 0) + $order->total_amount;
                    $buyer->save();

                    $buyerWallet = \App\Models\Wallet::firstOrCreate(
                        ['user_type' => 'customer', 'customer_id' => $order->customer_id, 'seller_id' => null],
                        ['balance' => 0]
                    );
                    $buyerWallet->balance += $order->total_amount;
                    $buyerWallet->save();

                    \App\Models\WalletTransaction::create([
                        'wallet_id' => $buyerWallet->id,
                        'customer_id' => $order->customer_id,
                        'user_type' => 'customer',
                        'transaction_type' => 'refund',
                        'credit' => $order->total_amount,
                        'debit' => 0,
                        'balance_after' => $buyer->wallet_balance,
                        'description' => "Refund for cancelled Order #{$order->id}",
                        'reference_id' => 'ORD-REF-' . $order->id,
                        'status' => 'completed'
                    ]);
                }
            }

            // --- 4. UPDATE ADMIN ESCROW/PLATFORM WALLET & LEDGER ---
            $platformWallet = \App\Models\Wallet::firstOrCreate(
                ['user_type' => 'platform', 'seller_id' => null, 'customer_id' => null],
                ['balance' => 0]
            );

            $orderTotal = (float) $order->total_amount;

            // Refund payout to buyer (debit from platform wallet)
            if (strtolower($order->payment_status) === 'paid') {
                $platformWallet->decrement('balance', $orderTotal);
                \App\Models\WalletTransaction::create([
                    'wallet_id' => $platformWallet->id,
                    'user_type' => 'platform',
                    'transaction_type' => 'refund',
                    'credit' => 0,
                    'debit' => $orderTotal,
                    'balance_after' => $platformWallet->balance,
                    'description' => "Refund payout to Customer for cancelled Order #{$order->id}",
                    'reference_id' => 'ORD-REF-' . $order->id,
                    'status' => 'completed'
                ]);
            }

            // Recovery from seller (credit back to platform wallet)
            $platformWallet->increment('balance', $totalSellerEarning);
            \App\Models\WalletTransaction::create([
                'wallet_id' => $platformWallet->id,
                'user_type' => 'platform',
                'transaction_type' => 'refund',
                'credit' => $totalSellerEarning,
                'debit' => 0,
                'balance_after' => $platformWallet->balance,
                'description' => "Recovered Seller payout for cancelled Order #{$order->id}",
                'reference_id' => 'ORD-REF-' . $order->id,
                'status' => 'completed'
            ]);

            // Recovery from referral (credit back to platform wallet)
            if ($totalReferralEarning > 0) {
                $platformWallet->increment('balance', $totalReferralEarning);
                \App\Models\WalletTransaction::create([
                    'wallet_id' => $platformWallet->id,
                    'user_type' => 'platform',
                    'transaction_type' => 'refund',
                    'credit' => $totalReferralEarning,
                    'debit' => 0,
                    'balance_after' => $platformWallet->balance,
                    'description' => "Recovered Referral commission for cancelled Order #{$order->id}",
                    'reference_id' => 'ORD-REF-' . $order->id,
                    'status' => 'completed'
                ]);
            }

            $order->refund_status = 'completed';
            $order->refund_completed_at = now();
            $order->referral_paid = false;
            $order->save();
        });
    }
}

