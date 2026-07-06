<?php
// app/Models/FraudSystem.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class FraudSystem extends Model
{
    protected $table = 'fraud_system';
    protected $primaryKey = 'id';
    public $timestamps = false;
    
    protected $fillable = [
        'record_type', 'rule_name', 'rule_type', 'threshold_value', 
        'time_window_seconds', 'refund_percentage', 'sliding_scale_rules',
        'daily_cap_product', 'daily_cap_seller', 'customer_id', 'customer_ip',
        'customer_email', 'customer_phone', 'seller_id', 'product_id',
        'click_time', 'is_fraud', 'fraud_reason', 'charge_amount',
        'refund_processed', 'blacklist_reason', 'flagged_by', 'created_at'
    ];
    
    protected $casts = [
        'is_fraud' => 'boolean',
        'refund_processed' => 'boolean',
        'click_time' => 'datetime',
        'created_at' => 'datetime'
    ];
    
    // ========== RULE METHODS ==========
    
    // Get all active rules
    public static function getAllRules()
    {
        return self::where('record_type', 'rule')->get();
    }
    
    // Get rule by type
    public static function getRuleByType($ruleType)
    {
        return self::where('record_type', 'rule')
                   ->where('rule_type', $ruleType)
                   ->first();
    }
    
    // Update rule dynamically
    public static function updateRule($ruleType, $data)
    {
        return self::where('record_type', 'rule')
                   ->where('rule_type', $ruleType)
                   ->update($data);
    }
    
    // Get global caps
    public static function getGlobalCaps()
    {
        $rule = self::where('record_type', 'rule')->first();
        return [
            'daily_cap_product' => $rule->daily_cap_product ?? 3,
            'daily_cap_seller' => $rule->daily_cap_seller ?? 50
        ];
    }
    
    // ========== CLICK METHODS ==========
    
    // Save click record
    public static function saveClick($data)
    {
        $data['record_type'] = 'click';
        $data['click_time'] = now();
        $data['created_at'] = now();
        
        return self::create($data);
    }
    
    // Get customer recent clicks
    public static function getRecentClicks($customerId, $seconds = 5)
    {
        return self::where('record_type', 'click')
                   ->where('customer_id', $customerId)
                   ->where('click_time', '>=', now()->subSeconds($seconds))
                   ->get();
    }
    
    // Get clicks by IP
    public static function getClicksByIp($ip, $seconds = 3600)
    {
        return self::where('record_type', 'click')
                   ->where('customer_ip', $ip)
                   ->where('click_time', '>=', now()->subSeconds($seconds))
                   ->get();
    }
    
    // Get customer enquiries count
    public static function getEnquiryCount($email, $phone, $seconds = 3600)
    {
        return self::where('record_type', 'click')
                   ->where(function($q) use ($email, $phone) {
                       if ($email) $q->orWhere('customer_email', $email);
                       if ($phone) $q->orWhere('customer_phone', $phone);
                   })
                   ->where('click_time', '>=', now()->subSeconds($seconds))
                   ->count();
    }
    
    // Get bot pattern clicks
    public static function getLastClicksForPattern($customerId, $limit = 5)
    {
        return self::where('record_type', 'click')
                   ->where('customer_id', $customerId)
                   ->orderBy('click_time', 'desc')
                   ->limit($limit)
                   ->get(['click_time']);
    }
    
    // Check daily product clicks
    public static function getProductClicksToday($productId)
    {
        return self::where('record_type', 'click')
                   ->where('product_id', $productId)
                   ->whereDate('click_time', today())
                   ->count();
    }
    
    // Check daily seller charges
    public static function getSellerChargesToday($sellerId)
    {
        return self::where('record_type', 'click')
                   ->where('seller_id', $sellerId)
                   ->whereDate('click_time', today())
                   ->sum('charge_amount');
    }
    
    // ========== BLACKLIST METHODS ==========
    
    // Add to blacklist
    public static function addToBlacklist($email, $phone, $ip, $reason, $adminId)
    {
        // Check if already blacklisted
        $exists = self::isBlacklisted($email, $phone, $ip);
        if ($exists) {
            return false;
        }
        
        return self::create([
            'record_type' => 'blacklist',
            'customer_email' => $email,
            'customer_phone' => $phone,
            'customer_ip' => $ip,
            'blacklist_reason' => $reason,
            'flagged_by' => $adminId,
            'created_at' => now()
        ]);
    }
    
    // Check if customer is blacklisted
    public static function isBlacklisted($email = null, $phone = null, $ip = null)
    {
        $query = self::where('record_type', 'blacklist');
        
        if ($email) {
            $query->where(function($q) use ($email, $phone, $ip) {
                $q->where('customer_email', $email);
                if ($phone) $q->orWhere('customer_phone', $phone);
                if ($ip) $q->orWhere('customer_ip', $ip);
            });
        } elseif ($phone) {
            $query->where(function($q) use ($phone, $ip) {
                $q->where('customer_phone', $phone);
                if ($ip) $q->orWhere('customer_ip', $ip);
            });
        } elseif ($ip) {
            $query->where('customer_ip', $ip);
        }
        
        return $query->exists();
    }
    
    // Get all blacklisted customers
    public static function getBlacklistedCustomers()
    {
        return self::where('record_type', 'blacklist')
                   ->orderBy('created_at', 'desc')
                   ->get();
    }
    
    // Remove from blacklist
    public static function removeFromBlacklist($id)
    {
        return self::where('record_type', 'blacklist')
                   ->where('id', $id)
                   ->delete();
    }
    
    // ========== STATISTICS METHODS ==========
    
    // Get fraud statistics
    public static function getFraudStats($startDate = null, $endDate = null)
    {
        $query = self::where('record_type', 'click');
        
        if ($startDate) {
            $query->whereDate('click_time', '>=', $startDate);
        }
        if ($endDate) {
            $query->whereDate('click_time', '<=', $endDate);
        }
        
        $totalClicks = clone $query;
        $fraudClicks = clone $query;
        
        return [
            'total_clicks' => $totalClicks->count(),
            'fraud_clicks' => $fraudClicks->where('is_fraud', 1)->count(),
            'total_charges' => $query->sum('charge_amount'),
            'total_refunds' => $query->where('refund_processed', 1)->sum('charge_amount'),
            'fraud_percentage' => $totalClicks->count() > 0 ? 
                round(($fraudClicks->where('is_fraud', 1)->count() / $totalClicks->count()) * 100, 2) : 0
        ];
    }
    
    // Get fraud by reason
    public static function getFraudByReason()
    {
        return self::where('record_type', 'click')
                   ->where('is_fraud', 1)
                   ->select('fraud_reason', DB::raw('COUNT(*) as total'))
                   ->groupBy('fraud_reason')
                   ->get();
    }
    
    // Get top fraud IPs
    public static function getTopFraudIps($limit = 10)
    {
        return self::where('record_type', 'click')
                   ->where('is_fraud', 1)
                   ->select('customer_ip', DB::raw('COUNT(*) as fraud_count'))
                   ->whereNotNull('customer_ip')
                   ->groupBy('customer_ip')
                   ->orderBy('fraud_count', 'desc')
                   ->limit($limit)
                   ->get();
    }
}
