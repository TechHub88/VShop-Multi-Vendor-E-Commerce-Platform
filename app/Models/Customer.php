<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Customer extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        // Basic info
        'name',
        'email',
        'phone',
        'password',
        'status',
        'otp',
        'otp_generated_at',
        'type',
        'is_blocked',
        'is_active',
        'risk_level',

        // Profile & personal info
        'gender',
        'dob',
        'marital_status',
        'anniversary',
        'kids',
        'language',
        'occupation',
        'workplace',
        'photo',

        // Wallet & rewards
        'wallet_balance',
        'reward_points',
        'referral_code',
        'referred_by',

        // Settings
        'notification_preferences',
        'default_address_id',

        // Address / location
        'company_name',
        'gstin',
        'country',
        'state',
        'city',
        'address_line1',
        'pincode',
        'profile_picture',
        'address',

        // Relationships
        'brand_id',
        'branch_id',
        'seller_id',
        'source',
        'reference',
        'latitude',
        'longitude',
    ];

    /**
     * The attributes that should be hidden for serialization.
     */
    protected $hidden = [
        'password',
        'remember_token',
        'otp',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'otp_generated_at' => 'datetime',
        'dob' => 'date',
        'anniversary' => 'date',
        'kids' => 'integer',
        'wallet_balance' => 'decimal:2',
        'reward_points' => 'integer',
        'is_blocked' => 'boolean',
        'is_active' => 'boolean',
        'notification_preferences' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * The guard used for authentication.
     */
    protected $guard = 'customer';

    // ==================== RELATIONSHIPS ====================

    public function addresses()
    {
        return $this->hasMany(Address::class, 'customer_id');
    }

    public function defaultAddress()
    {
        return $this->belongsTo(Address::class, 'default_address_id');
    }

    public function orders()
    {
        return $this->hasMany(Order::class, 'customer_id');
    }

    /**
     * Customer notifications relationship
     */
    public function notifications()
    {
        return $this->hasMany(\App\Models\CustomerNotification::class, 'customer_id');
    }

    public function reviews()
    {
        return $this->hasMany(ProductReview::class, 'customer_id');
    }

    public function wishlist()
    {
        return $this->hasMany(Wishlist::class, 'customer_id');
    }

    public function wallet()
    {
        return $this->hasOne(Wallet::class, 'customer_id');
    }

    public function walletTransactions()
    {
        return $this->hasMany(WalletTransaction::class, 'customer_id');
    }

    public function referralsMade()
    {
        return $this->hasMany(Referral::class, 'customer_id');
    }

    public function referralsReceived()
    {
        return $this->hasMany(Referral::class, 'referred_customer_id');
    }

    public function membership()
    {
        return $this->hasOne(Membership::class, 'customer_id');
    }

    public function rewards()
    {
        return $this->hasMany(Reward::class, 'customer_id');
    }

    public function deviceSessions()
    {
        return $this->hasMany(DeviceSession::class, 'customer_id');
    }

    public function recentlyViewed()
    {
        return $this->belongsToMany(Product::class, 'customer_recently_viewed')
                    ->withPivot('viewed_at')
                    ->orderBy('pivot_viewed_at', 'desc')
                    ->limit(10);
    }

    public function enquiries()
    {
        return $this->hasMany(Enquiry::class, 'user_id');
    }

    public function seller()
    {
        return $this->belongsTo(Seller::class);
    }

    public function brand()
    {
        return $this->belongsTo(Brand::class, 'brand_id');
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class, 'branch_id');
    }

    public function refunds()
    {
        return $this->hasMany(Refund::class, 'customer_id');
    }

    /**
     * Get the customer's billing addresses.
     */
    public function billingAddresses()
    {
        return $this->hasMany(Address::class, 'customer_id')->where('address_type', 'billing');
    }

    /**
     * Get the customer's delivery addresses.
     */
    public function deliveryAddresses()
    {
        return $this->hasMany(Address::class, 'customer_id')->where('address_type', 'delivery');
    }

    /**
     * Get the first address (fallback).
     */
    public function firstAddress()
    {
        return $this->hasOne(Address::class, 'customer_id')->orderBy('id', 'asc');
    }

    // ==================== ACCESSORS & MUTATORS ====================

    /**
     * Get the customer's profile photo URL.
     */
    public function getPhotoAttribute($value)
    {
        $path = $value ?: $this->profile_picture;

        if ($path) {
            if (filter_var($path, FILTER_VALIDATE_URL)) {
                return $path;
            }
            if (file_exists(public_path($path))) {
                return asset($path);
            }
            if (file_exists(storage_path('app/public/' . $path))) {
                return asset('storage/' . $path);
            }
        }
        return asset('images/default-avatar.svg');
    }


    /**
     * Check if customer is active.
     */
    public function isActive()
    {
        return $this->status === 'active' && !$this->is_blocked;
    }

    /**
     * Check if profile completion criteria are met and award 5 points if not already done.
     */
    public function checkAndRewardProfileCompletion()
    {
        // Check if already rewarded for profile completion
        $alreadyRewarded = Reward::where('customer_id', $this->id)
            ->where('description', 'like', '%Profile completion%')
            ->exists();

        if (!$alreadyRewarded) {
            // Count completed optional fields
            $extraFields = 0;
            if (!empty($this->gender)) $extraFields++;
            if (!empty($this->dob)) $extraFields++;
            if (!empty($this->occupation)) $extraFields++;
            if (!empty($this->language)) $extraFields++;
            if (!empty($this->profile_picture) || !empty($this->photo)) $extraFields++;
            if (!empty($this->address_line1) || !empty($this->city) || !empty($this->state)) $extraFields++;

            // Also check if they have addresses in Address table
            $hasAddress = Address::where('customer_id', $this->id)->exists();
            if ($hasAddress) $extraFields++;

            // Core fields (name, email, phone) must be filled, plus at least 2 extra fields
            if (!empty($this->name) && !empty($this->email) && !empty($this->phone) && $extraFields >= 2) {
                Reward::create([
                    'customer_id' => $this->id,
                    'type'        => 'points',
                    'points'      => 5,
                    'amount'      => 0.50, // 5 points = ₹0.5
                    'description' => 'Reward points for Profile completion',
                    'status'      => 'active',
                    'expiry_date' => now()->addDays(60),
                ]);

                // Sync cumulative points
                $this->reward_points = Reward::getValidPoints($this->id);
                $this->save();
                return true;
            }
        }
        return false;
    }
}