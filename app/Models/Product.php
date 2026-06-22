<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Product extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        // ================= ID / BASIC =================
        'type',
        'name',
        'product_type',
        'sku',
        'price',
        'boost_status',
        'boost_duration',
        'daily_budget',
        'daily_limit',
        'total_budget',
        'target_country',
        'target_state',
        'target_city',
        'age_min',
        'age_max',
        'language',
        'interests',
        'categories',
        'promotion_goal',
        'stop_daily_budget',
        'stop_total_budget',
        'notify_before_exhaust',
        'notify_percentage',
        'regular_price',
        'discount_price',
        'description',
        'category_id',
        'moq',
        'thumbnail',
        'images',
        'meta_title',
        'meta_keywords',
        'meta_description',
        'is_featured',
        'status',
        'enable_enquiry',
        'contact_visibility',
        'boost',
        'brand',
        'created_by_id',
        'product_code',
        'code',
        'faqs',
        'duration',
        'tour_type',
        'max_person',
        'short_summary',
        'highlights',
        'itinerary',
        'inclusions',
        'exclusions',
        'subcategory_id',
        'length',
        'breadth',
        'height',
        'weight',
        'video_urls',
        'brand_id',
        'popularity',
        'destination',
        'inventory_type',
        'platform',
        'service_type',
        'sold',
        'created_by',
        'brand_name',
        'manufactured_by',
        'manufacturing_date',
        'country_origin',
        'generic_name',
        'asin',
        'ingredients',
        'expiry_days',
        'storage_instruction',
        'features',
        'specification',
        'purpose',
        'refund_policy',
        'seo_tags',
        'bullet_points',
        'target_audience',
        'service_days',
        'start_time',
        'end_time',
        'duration_hours',
        'country',
        'state',
        'district',
        'guide_service_type',
        'kids_allowed',
        'kids_age_from',
        'kids_age_to',
        'adult_allowed',
        'adult_age_from',
        'adult_age_to',
        'views',
        'videos',
        'min_duration_hours',
        'payment_type',
        'tax_name',
        'tax_percentage',
        'service_charges',
        'download_title',
        'download_date',
        'download_url',
        'sample_language',
        'sample_ide',
        'sample_type',
        'sample_os',
        'sample_download_url',
        'sample_codes',
        'stock',
        'discount',
        'accommodation',
        'included_excluded',
        'luggage_capacity',
        'free_days_logic',
        'benefits',
        'research',
        'focus_keyword',
        'seo_slug',
        'adult_price',
        'child_price',
        'group_price',
        'professional_price',
        'price_type',
        'is_discount',
        'discount_type',
        'discount_value',
        'discount_start_date',
        'discount_end_date',
        'inventory',
        'track_stock',
        'supplier_name',
        'supplier_product_id',
        'supplier_cost',
        'shipping_time',
        'auto_order',
        'download_data',
        'city',
        'stock_qty',
        'low_stock_threshold',
        'auto_disable',
        'product_specifications',
        'net_total',
        'item_code',
        'color',
        'variant',
        'size',
        'quantity',
        'expiry_date',
        'batch_number',
        'dimensions',
        'delivery_time',
        'warranty',
        'service_duration',
        'session_type',
        'schedule_slots',
        'provider_name',
        'max_participants',
        'booking_required',
        'service_validity',
        'meeting_link',
        'materials_included',
        'delivery_hour',
        'professional_type',
        'session_duration',
        'session_mode',
        'max_booking',
        'cancel_policy_prof',
        'capacity',
        'inclusion',
        'availability',
        'cancel_policy_tour',
        'documents',
        'gov_fee',
        'cancel_policy_other',
        'adult_count',
        'kids_count',
        'free_days',
        'languages',
        'portfolio',
        'guide_category',
        'start_point',
        'end_point',
        'other_service_type',
        'processing_time',
        'validity',
        'organizer_name',
        'luggage',
        'tour_service_type',
        'listing_type',
        'fixed_type',
        'fixed_dates',
        'service_days_count',
        'service_nights',
        'travel_service_type',
        'travel_documents',
        'travel_pricing',
        'travel_policy_trip_types',
        'travel_license',
        'travel_rate_assistance_type',
        'travel_areas',
        'travel_emergency',
        'spiritual_dress_code',
        'spiritual_festivals',
        'spiritual_prasad_advisory_type',
        'spiritual_contact',
        'spiritual_guidelines_era',
        'spiritual_unesco',
        'spiritual_guide_type',
        'spiritual_duration',
        'spiritual_facilities',
        'spiritual_family_friendly',
        'activity_type',
        'activity_difficulty',
        'activity_equipment',
        'activity_safety',
        'activity_guide',
        'activity_group_food_name',
        'activity_restaurant',
        'activity_food_type',
        'activity_must_try',
        'activity_diet_options',
        'activity_takeaway',
        'vendor_category',
        'vendor_famous_for',
        'vendor_operational_areas',
        'vendor_payment_methods',
        'vendor_delivery',
        'vendor_customization',
        'verification_status',
        'verified_at',
        'verified_by',
        'verification_checklist',
        'force_disabled',
        'force_disabled_reason',
        'force_disabled_by',
        'force_disabled_at',
        'is_duplicate',
        'duplicate_flagged_at',
        'duplicate_flagged_by',
        'is_inappropriate',
        'inappropriate_reason',
        'admin_notes',
        'trademark_status',
        'service_languages',
        'is_sponsored',
        'is_pinned',
        'seller_removal_requested',
        'seller_removal_approved',
        'seller_removal_approved_at',
        'seller_removal_rejected_at',
        'rejection_reason',
        'boost_amount',
        'is_boosted',
        'boost_expires_at',
        // keep any extra fields you already had
        'flagged',
        'removed',
        'removal_reason',
        'removed_at',
        'removed_by',
        'removal_requested',
        'removal_rejected_at',
        'removal_rejected_by',
        'is_active',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'faqs' => 'array',
        'download_data' => 'array',
        'images' => 'array',
        'videos' => 'array',
        'service_days' => 'array',
        'payment_type' => 'array',
        'tax_name' => 'array',
        'tax_percentage' => 'array',
        'target_city' => 'array',
        'contact_visibility' => 'array',
        'interests' => 'array',
        'categories' => 'array',
        'inclusions' => 'array',
        'exclusions' => 'array',
        'highlights' => 'array',
        'itinerary' => 'array',
        'verification_checklist' => 'array',
        'is_discount' => 'boolean',
        'boost_status' => 'boolean',
        'enable_enquiry' => 'boolean',
        'force_disabled' => 'boolean',
        'is_duplicate' => 'boolean',
        'is_inappropriate' => 'boolean',
        'is_sponsored' => 'boolean',
        'is_pinned' => 'boolean',
        'is_boosted' => 'boolean',
        'is_featured' => 'boolean',
        'is_active' => 'boolean',
        'discount_start_date' => 'date',
        'discount_end_date' => 'date',
        'verified_at' => 'datetime',
        'force_disabled_at' => 'datetime',
        'duplicate_flagged_at' => 'datetime',
        'boost_expires_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /*
    |--------------------------------------------------------------------------
    | RELATIONSHIPS (unchanged)
    |--------------------------------------------------------------------------
    */

    public function versionHistory()
    {
        return $this->hasMany(ProductVersionHistory::class);
    }

    public function getFinalPriceAttribute()
    {
        $price = $this->price ?? 0;

        if (!empty($this->tax_percentage)) {
            $taxes = is_array($this->tax_percentage) ? $this->tax_percentage : json_decode($this->tax_percentage, true);
            if (empty($taxes) && is_numeric($this->tax_percentage)) {
                $taxes = [$this->tax_percentage];
            }
            if (!is_array($taxes) && is_numeric($taxes)) {
                $taxes = [$taxes];
            }
            if (is_array($taxes)) {
                foreach ($taxes as $tax) {
                    $taxValue = is_numeric($tax) ? $tax : 0;
                    $price += ($price * $taxValue) / 100;
                }
            }
        }
        return round($price, 2);
    }

    public function resolvedCategory()
    {
        if ($this->created_by === 'seller') {
            $sellerCategory = SellerMarketplaceCategory::where('seller_id', $this->created_by_id)->first();
            if ($sellerCategory) {
                return Category::find($sellerCategory->category_id);
            }
        }
        return $this->category;
    }

    public function sellerMarketplaceCategory()
    {
        return $this->hasOne(SellerMarketplaceCategory::class, 'seller_id', 'created_by_id');
    }

    public function items()
    {
        return $this->hasMany(\App\Models\OrderItem::class, 'product_id');
    }

    public function returns()
    {
        return $this->hasMany(OrderItem::class, 'product_id')->where('status', 'returned');
    }

    public function complaints()
    {
        return $this->hasMany(\App\Models\Grievance::class);
    }

    public function seller()
    {
        return $this->belongsTo(\App\Models\Seller::class, 'created_by_id');
    }

    public function attributes()
    {
        return $this->hasMany(ProductAttribute::class, 'proid');
    }

    public function samplePrices()
    {
        return $this->hasMany(\App\Models\ProductSamplePrice::class, 'product_id');
    }

    public function embroideryPrices()
    {
        return $this->hasMany(\App\Models\ProductEmbroideryPrice::class, 'product_id');
    }

    public function category()
    {
        return $this->belongsTo(MarketplaceCategory::class, 'category_id');
    }

    public function subcategory()
    {
        return $this->belongsTo(\App\Models\MarketplaceSubCategory::class, 'subcategory_id');
    }

    public function brand()
    {
        return $this->belongsTo(Brand::class, 'brand_id', 'id');
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class, 'branch_id', 'id');
    }

    public function comboProducts()
    {
        return $this->hasMany(ComboProduct::class, 'main_product_id');
    }

    public function recommendations()
    {
        return $this->hasMany(RecommendationRule::class, 'source', 'id')->where('type', 'product');
    }

    public function comboItems()
    {
        return $this->hasMany(ComboProductItem::class, 'product_id');
    }

    public function combos()
    {
        return $this->belongsToMany(ComboProduct::class, 'combo_product_items', 'product_id', 'combo_id')
                    ->withPivot('quantity', 'price')
                    ->withTimestamps();
    }

    public function reviews()
    {
        return $this->hasMany(ProductReview::class);
    }

    public function approvedReviews()
    {
        return $this->hasMany(ProductReview::class)->where('is_approved', true);
    }

    public function getAverageRatingAttribute()
    {
        return (float) $this->approvedReviews()->avg('rating') ?? 0;
    }

    public function getTotalReviewsAttribute()
    {
        return $this->approvedReviews()->count();
    }

    public function getRatingBreakdownAttribute()
    {
        $breakdown = [];
        for ($i = 5; $i >= 1; $i--) {
            $breakdown[$i] = $this->approvedReviews()->where('rating', $i)->count();
        }
        return $breakdown;
    }

    /**
     * Check if product is returnable.
     */
    public function isReturnable()
    {
        if ($this->refund_policy && stripos($this->refund_policy, 'non-returnable') !== false) {
            return false;
        }

        $categoryName = strtolower($this->category->name ?? '');
        $genericName = strtolower($this->generic_name ?? '');
        $productName = strtolower($this->name ?? '');

        $nonReturnableKeywords = ['wellness', 'medicine', 'mask', 'personal care', 'hygiene', 'food', 'snack', 'grocery', 'innerwear', 'lingerie', 'underwear', 'supplement'];
        foreach ($nonReturnableKeywords as $keyword) {
            if (str_contains($categoryName, $keyword) || str_contains($genericName, $keyword) || str_contains($productName, $keyword)) {
                return false;
            }
        }

        return true;
    }
}