<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Str;

class Seller extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $table = 'sellers';

    /*
    |--------------------------------------------------------------------------
    | MASS ASSIGNABLE FIELDS
    |--------------------------------------------------------------------------
    */

    protected $fillable = [

        /* BASIC */
        'name','parent_id',
        'email',
        'mobile',
        'password',
        'slug',
        'status',
        'is_verified',
        'otp',
        'otp_generated_at',
        'requested_platforms',
        'approved_platforms',
        'platform_request_time',

        /* ================= ORGANIZATION ================= */

        'application',
        'legal_name',
        'hide_price',

        'logo',
        'logo_trademark',
        'trademark_file',
        'trademark_status',

        'business_type',

        'pan_number',
        'pan_file',

        'gst_number',
        'gst_certificate',

        'has_iec',
        'iec_code',
        'iec_certificate',

        'billing_country',
        'billing_state',
        'billing_city',
        'billing_address',

        /* ================= CONTACT ================= */
  'org_name',
    'org_mobile',
    'org_email',
        'contact_pan_number',
        'contact_pan_file',

        'contact_aadhar_number',
        'contact_aadhar_file',

        'contact_address_proof',

        /* ================= BANK ================= */

        'bank_account_name',
        'bank_account_number',
        'bank_country',
         'bank_state',
    'bank_city',
        'ifsc_code',
        'swift_code',
        'bic_code',
        'cancel_cheque',

        /* ================= PAYMENT ================= */

        'paypal_email',

        /* ================= AGREEMENT ================= */

        'agreement_type',
        'agreement_file',
        'agreement_accepted_at','is_verified',// ================= NEW ADD =================

'cin',
'incorporation_date',
'company_certificate',
'company_status',

'billing_district',
'billing_zip',

'contact_country',
'contact_state',
'contact_district',
'contact_city',
'contact_zip',
'contact_address',

'iec_file_number',

'pan_status',
'gst_status',
'iec_status',

'contact_persons',

'bank_status',

'agreement_status',
'agreement_updated_at','phone',
    'phone_otp',
    'phone_verified',
    'email_verified','email','payout_frequency','payout_method','gst_expiry','pan_expiry','upi_id','branch_name','gateways',
'razorpay_key',
'razorpay_secret',
'stripe_key',
'stripe_secret','api_key','api_secret','manual_next_date','referral_settings','role','login_type',  'is_active',
        'block_metadata',
        'deactivated_reason',
        'deactivated_at','hold_days','referral_code','referred_by'

    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'agreement_accepted_at' => 'datetime',
        'is_verified' => 'boolean',
        'contact_persons' => 'array',
           // 🔥 ADD THIS
    'gateways' => 'array',
    'requested_platforms' => 'array',
    'approved_platforms' => 'array',
    'platform_request_time' => 'datetime',
        
        'pan_expiry' => 'datetime',
    'gst_expiry' => 'datetime',
    'referral_settings' => 'array',
     'permissions' => 'array',  
    ];

    /*
    |--------------------------------------------------------------------------
    | MODEL EVENTS
    |--------------------------------------------------------------------------
    */



public function seoData()
{
    return $this->hasMany(SellerSeo::class);
}

public function operationAreas()
{
    return $this->hasMany(SellerOperationArea::class);
}

public function banners()
{
    return $this->hasMany(SellerBanner::class);
}
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($seller) {

            // generate slug safely
            if (!empty($seller->shop_name)) {
                $seller->slug = Str::slug($seller->shop_name);
            } else {
                $seller->slug = Str::uuid();
            }

            // Auto-generate unique referral code
            if (empty($seller->referral_code)) {
                do {
                    $code = 'VSHOP-' . strtoupper(Str::random(6));
                } while (static::where('referral_code', $code)->exists());
                $seller->referral_code = $code;
            }

        });
    }

    /*
    |--------------------------------------------------------------------------
    | ACCESSORS
    |--------------------------------------------------------------------------
    */

    public function getIsApprovedAttribute()
    {
        return $this->status === 'approved';
    }

    /*
    |--------------------------------------------------------------------------
    | RELATIONSHIPS
    |--------------------------------------------------------------------------
    */

// In app/Models/Seller.php
public function products()
{
    return $this->hasMany(Product::class, 'created_by_id'); // Change 'seller_id' to 'created_by_id'
}

    public function orders()
    {
        return $this->hasMany(Order::class, 'seller_id');
    }

    /*
    |--------------------------------------------------------------------------
    | HELPERS
    |--------------------------------------------------------------------------
    */

    public function approve()
    {
        $this->update([
            'status' => 'approved',
            'is_verified' => true,
        ]);
    }

    public function reject()
    {
        $this->update([
            'status' => 'rejected',
        ]);
    }
    
    
    public function auditLogs()
    {
        return $this->hasMany(SellerAuditLog::class);
    }

    
    
    public function block($reason, $blockUntil = null)
    {
        $blockMetadata = [
            'blocked_by' => auth()->id(),
            'blocked_by_name' => auth()->user()->name,
            'reason' => $reason,
            'block_until' => $blockUntil,
            'blocked_at' => now(),
            'duration' => $blockUntil ? 'temp' : 'permanent'
        ];
        
        $this->update([
            'status' => 'blocked',
            'is_verified' => 0,
            'block_metadata' => json_encode($blockMetadata)
        ]);
    }
    
    public function unblock()
    {
        $this->update([
            'status' => 'pending',
            'block_metadata' => null
        ]);
    }
}