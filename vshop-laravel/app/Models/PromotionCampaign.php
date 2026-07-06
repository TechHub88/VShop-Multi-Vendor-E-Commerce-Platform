<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Str;

class PromotionCampaign extends Model
{
    use HasFactory;

    protected $table = 'promotion_campaigns';

    protected $fillable = [
        'name',
        'slug',
        'description',
        'type',
        'discount_type',
        'discount_value',
        'minimum_order_amount',
        'maximum_discount',
        'start_date',
        'end_date',
        'timezone',
        'app_type',
        'seller_ids',
        'category_ids',
        'product_ids',
        'customer_segments',
        'usage_limit_per_campaign',
        'usage_limit_per_user',
        'used_count',
        'status',
        'is_featured',
        'show_on_homepage',
        'banner_image',
        'banner_link',
        'total_orders',
        'total_revenue',
        'total_discount_given',
        'unique_users',
        'conversion_rate',
        'coupon_code',
        'is_auto_apply',
        'flash_sale_hours',
        'stock_limit',
        'meta_data',
        'settings',
        'created_by',
        'updated_by',
        'approved_by',
        'approved_at',
        'ended_at'
    ];

    protected $casts = [
        'seller_ids' => 'array',
        'category_ids' => 'array',
        'product_ids' => 'array',
        'customer_segments' => 'array',
        'meta_data' => 'array',
        'settings' => 'array',
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'approved_at' => 'datetime',
        'ended_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'discount_value' => 'decimal:2',
        'minimum_order_amount' => 'decimal:2',
        'maximum_discount' => 'decimal:2',
        'total_revenue' => 'decimal:2',
        'total_discount_given' => 'decimal:2',
        'conversion_rate' => 'decimal:2',
        'is_featured' => 'boolean',
        'show_on_homepage' => 'boolean',
        'is_auto_apply' => 'boolean',
        'used_count' => 'integer',
        'total_orders' => 'integer',
        'unique_users' => 'integer',
        'flash_sale_hours' => 'integer',
        'stock_limit' => 'integer',
        'usage_limit_per_campaign' => 'integer',
        'usage_limit_per_user' => 'integer'
    ];

    // Campaign Types
    const TYPE_DISCOUNT = 'discount';
    const TYPE_BOGO = 'bogo';
    const TYPE_FREE_SHIPPING = 'free_shipping';
    const TYPE_CASHBACK = 'cashback';
    const TYPE_COUPON = 'coupon';
    const TYPE_FLASH_SALE = 'flash_sale';
    const TYPE_FESTIVAL = 'festival';

    // Discount Types
    const DISCOUNT_PERCENTAGE = 'percentage';
    const DISCOUNT_FIXED = 'fixed';

    // Status Types
    const STATUS_ACTIVE = 'active';
    const STATUS_INACTIVE = 'inactive';
    const STATUS_SCHEDULED = 'scheduled';
    const STATUS_ENDED = 'ended';
    const STATUS_CANCELLED = 'cancelled';

    // App Types
    const APP_ALL = 'all';
    const APP_VSHOP = 'vshop';
    const APP_VTRAVEL = 'vtravel';
    const APP_VEDGE = 'vedge';
    const APP_VCAMPUS = 'vcampus';

    // Relationships
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', self::STATUS_ACTIVE)
            ->where('start_date', '<=', now())
            ->where('end_date', '>=', now());
    }

    public function scopeScheduled($query)
    {
        return $query->where('status', self::STATUS_SCHEDULED)
            ->where('start_date', '>', now());
    }

    public function scopeEnded($query)
    {
        return $query->where(function($q) {
            $q->where('status', self::STATUS_ENDED)
              ->orWhere('end_date', '<', now());
        });
    }

    public function scopeForApp($query, $appType)
    {
        if ($appType && $appType != 'all') {
            return $query->where(function($q) use ($appType) {
                $q->where('app_type', self::APP_ALL)->orWhere('app_type', $appType);
            });
        }
        return $query;
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }

    // Accessors
    public function getFormattedStatusAttribute()
    {
        $statuses = [
            self::STATUS_ACTIVE => 'Active',
            self::STATUS_INACTIVE => 'Inactive',
            self::STATUS_SCHEDULED => 'Scheduled',
            self::STATUS_ENDED => 'Ended',
            self::STATUS_CANCELLED => 'Cancelled'
        ];
        
        return $statuses[$this->status] ?? ucfirst($this->status);
    }

    public function getStatusColorAttribute()
    {
        $colors = [
            self::STATUS_ACTIVE => 'success',
            self::STATUS_INACTIVE => 'secondary',
            self::STATUS_SCHEDULED => 'warning',
            self::STATUS_ENDED => 'danger',
            self::STATUS_CANCELLED => 'dark'
        ];
        
        return $colors[$this->status] ?? 'secondary';
    }

    public function getFormattedTypeAttribute()
    {
        $types = [
            self::TYPE_DISCOUNT => 'Discount',
            self::TYPE_BOGO => 'Buy One Get One',
            self::TYPE_FREE_SHIPPING => 'Free Shipping',
            self::TYPE_CASHBACK => 'Cashback',
            self::TYPE_COUPON => 'Coupon',
            self::TYPE_FLASH_SALE => 'Flash Sale',
            self::TYPE_FESTIVAL => 'Festival Offer'
        ];
        
        return $types[$this->type] ?? ucfirst($this->type);
    }

    public function getDiscountTextAttribute()
    {
        if ($this->discount_type == self::DISCOUNT_PERCENTAGE) {
            return $this->discount_value . '% OFF';
        } else {
            return '₹' . number_format($this->discount_value, 2) . ' OFF';
        }
    }

    public function getDurationAttribute()
    {
        return $this->start_date->format('d M Y') . ' - ' . $this->end_date->format('d M Y');
    }

    public function getIsActiveAttribute()
    {
        return $this->status == self::STATUS_ACTIVE && 
               $this->start_date <= now() && 
               $this->end_date >= now();
    }

    public function getIsExpiredAttribute()
    {
        return $this->end_date < now();
    }

    public function getUsagePercentageAttribute()
    {
        if ($this->usage_limit_per_campaign && $this->usage_limit_per_campaign > 0) {
            return min(100, round(($this->used_count / $this->usage_limit_per_campaign) * 100, 1));
        }
        return 0;
    }

    // Helper Methods
    public function isActive()
    {
        return $this->status === self::STATUS_ACTIVE && 
               $this->start_date <= now() && 
               $this->end_date >= now();
    }

    public function isScheduled()
    {
        return $this->status === self::STATUS_SCHEDULED && $this->start_date > now();
    }

    public function isExpired()
    {
        return $this->end_date < now() || $this->status === self::STATUS_ENDED;
    }

    public function hasReachedLimit()
    {
        if ($this->usage_limit_per_campaign) {
            return $this->used_count >= $this->usage_limit_per_campaign;
        }
        return false;
    }

    public function canBeUsedByUser($userId)
    {
        // Check user usage limit
        $userUsage = CampaignUsage::where('campaign_id', $this->id)
            ->where('user_id', $userId)
            ->count();
        
        return $userUsage < $this->usage_limit_per_user;
    }

    public function incrementUsage($userId = null)
    {
        $this->increment('used_count');
        
        if ($userId) {
            CampaignUsage::create([
                'campaign_id' => $this->id,
                'user_id' => $userId,
                'used_at' => now()
            ]);
        }
    }

    public function updateAnalytics($orderAmount, $discountGiven)
    {
        $this->increment('total_orders');
        $this->increment('total_revenue', $orderAmount);
        $this->increment('total_discount_given', $discountGiven);
        
        if ($this->total_orders > 0) {
            $this->conversion_rate = ($this->total_orders / $this->unique_users) * 100;
        }
        
        $this->save();
    }

    public function applyDiscount($amount)
    {
        $discount = 0;
        
        if ($this->discount_type == self::DISCOUNT_PERCENTAGE) {
            $discount = ($amount * $this->discount_value) / 100;
        } else {
            $discount = $this->discount_value;
        }
        
        // Apply maximum discount limit
        if ($this->maximum_discount && $discount > $this->maximum_discount) {
            $discount = $this->maximum_discount;
        }
        
        // Check minimum order amount
        if ($amount < $this->minimum_order_amount) {
            $discount = 0;
        }
        
        return min($discount, $amount);
    }

    public function getApplicableProducts()
    {
        if ($this->product_ids && is_array($this->product_ids)) {
            return Product::whereIn('id', $this->product_ids)->get();
        }
        return collect();
    }

    public function getApplicableCategories()
    {
        if ($this->category_ids && is_array($this->category_ids)) {
            return Category::whereIn('id', $this->category_ids)->get();
        }
        return collect();
    }

    public function getApplicableSellers()
    {
        if ($this->seller_ids && is_array($this->seller_ids)) {
            return Seller::whereIn('id', $this->seller_ids)->get();
        }
        return collect();
    }

    // Boot method
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($campaign) {
            if (empty($campaign->slug)) {
                $campaign->slug = Str::slug($campaign->name . '-' . uniqid());
            }
            
            if (empty($campaign->coupon_code) && $campaign->type == self::TYPE_COUPON) {
                $campaign->coupon_code = strtoupper(Str::random(10));
            }
        });

        static::updating(function ($campaign) {
            if ($campaign->end_date < now() && $campaign->status == self::STATUS_ACTIVE) {
                $campaign->status = self::STATUS_ENDED;
                $campaign->ended_at = now();
            }
        });
    }
}
