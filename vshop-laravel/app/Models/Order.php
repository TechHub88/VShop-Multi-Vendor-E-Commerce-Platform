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
        'payment_type', // full / partial

        'sub_total',
        'gst_amount',
        'courier_charge',
        'total_amount',
        'paid_amount',
        'balance',

        'status',

        // ================= CHECKOUT =================
        'delivery_address_id',
        'billing_address_id',
        'order_date',

        // ================= TAX =================
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
        'refunded_at','discount_amount'
        
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
        $sellerComm = 60;
        $referrerComm = 10;
        $platformComm = 30;

        $sellerId = $this->seller_id;
        
        // If order doesn't have seller_id directly, try to get it from the first item
        if (!$sellerId && $this->relationLoaded('items') && $this->items->isNotEmpty()) {
            $sellerId = $this->items->first()->product->created_by_id ?? null;
        } elseif (!$sellerId) {
            // Lazy load if not already loaded (less optimal but necessary if seller_id is missing)
            $firstItem = $this->items()->with('product')->first();
            $sellerId = $firstItem->product->created_by_id ?? null;
        }

        $appType = 'vshop'; // Default to vshop

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
                $sellerComm = $specificRule->seller_commission;
                $referrerComm = $specificRule->referrer_commission;
                $platformComm = $specificRule->platform_commission;
            } else {
                // Priority 2: Match by App Type with no specific seller
                $appRule = $rules->whereNull('seller_id')
                                 ->where('app_type', $appType)
                                 ->first();
                if ($appRule) {
                    $sellerComm = $appRule->seller_commission;
                    $referrerComm = $appRule->referrer_commission;
                    $platformComm = $appRule->platform_commission;
                } else {
                    // Priority 3: Generic fallback in DB
                    $generalRule = $rules->whereNull('seller_id')->first();
                    if ($generalRule) {
                        $sellerComm = $generalRule->seller_commission;
                        $referrerComm = $generalRule->referrer_commission;
                        $platformComm = $generalRule->platform_commission;
                    }
                }
            }
        } catch (\Exception $e) {
            // Ignore DB errors and use default
        }

        $totalAmount = $this->total_amount ?? 0;

        return (object) [
            'seller' => $totalAmount * ($sellerComm / 100),
            'referrer' => $totalAmount * ($referrerComm / 100),
            'platform' => $totalAmount * ($platformComm / 100),
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

        // 🔥 ORDER CREATED
        static::created(function ($order) {

            ActivityLog::create([
                'order_id'      => $order->id,
                'customer_id'   => $order->customer_id,
                'activity_type' => 'order_placed',
                'new_status'    => $order->status,
                'description'   => 'New order placed',
                'remarks'       => 'Order #' . $order->id . ' created',
            ]);

        });
    }
}
