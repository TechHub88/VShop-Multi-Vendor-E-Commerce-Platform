<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MarketingGrowthController extends Controller
{
    // ========== EXISTING METHODS (unchanged) ==========
    public function referralSystem()
    {
        return view('admin.marketing.referrals');
    }

    public function referralHistory()
    {
        $referrals = \App\Models\ReferralHistory::with(['referrer', 'buyer', 'order.items.product', 'seller'])
            ->latest()
            ->paginate(20);

        return view('admin.marketing.referral-history', compact('referrals'));
    }

    public function getTransactions()
    {
        $txns = \App\Models\ReferralHistory::with(['referrer', 'buyer', 'seller'])
            ->orderBy('id', 'desc')
            ->limit(100)
            ->get();
            
        // Map data to match JS frontend expectations
        $mapped = $txns->map(function($t) {
            $referrerData = null;
            $referralType = 'Buyer to Buyer';
            $payoutType = 'Buyer Points';
            
            if ($t->buyer_id) {
                // Buyer-to-buyer
                $referrerData = $t->referrer ? ['name' => $t->referrer->name, 'email' => $t->referrer->email] : null;
                // Check if referrer is a seller
                if ($t->referrer) {
                    $isSeller = \App\Models\Seller::where('email', $t->referrer->email)
                        ->orWhere('phone', $t->referrer->phone)
                        ->exists();
                    if ($isSeller) {
                        $payoutType = 'Seller Wallet';
                    }
                }
            } else {
                // Seller-to-seller (referrer_id is seller ID)
                $referralType = 'Seller to Seller';
                $payoutType = 'Seller Wallet';
                $sellerReferrer = \App\Models\Seller::find($t->referrer_id);
                $referrerData = $sellerReferrer ? ['name' => $sellerReferrer->name ?? $sellerReferrer->org_name, 'email' => $sellerReferrer->email] : null;
            }

            return [
                'created_at' => $t->created_at,
                'referral_type' => $referralType,
                'payout_type' => $payoutType,
                'referrer' => $referrerData,
                'buyer' => $t->buyer ? ['name' => $t->buyer->name, 'email' => $t->buyer->email] : null,
                'seller' => $t->seller ? ['company_name' => $t->seller->name ?? $t->seller->org_name] : null,
                'order_id' => $t->order_id,
                'amount' => $t->amount,
                'status' => $t->status,
            ];
        });

        return response()->json($mapped);
    }

    public function getSystemStats()
    {
        $totalReferrals = \App\Models\Customer::whereNotNull('referred_by')->count();
        $activeReferrers = \App\Models\Customer::whereNotNull('referred_by')->distinct('referred_by')->count('referred_by');
        
        $ordersViaReferral = \App\Models\Order::whereHas('customer', function($q) {
            $q->whereNotNull('referred_by');
        })->count();
        
        $referralRevenue = \App\Models\Order::whereHas('customer', function($q) {
            $q->whereNotNull('referred_by');
        })->sum('grand_total');

        $payoutsMade = \Illuminate\Support\Facades\DB::table('seller_finances')
            ->where('transaction_type', 'referral')
            ->sum('credit');

        return response()->json([
            'total_referrals'     => number_format($totalReferrals),
            'active_referrers'    => number_format($activeReferrers),
            'orders_via_referral' => number_format($ordersViaReferral),
            'referral_revenue'    => '₹' . number_format($referralRevenue, 2),
            'payouts_made'        => '₹' . number_format($payoutsMade, 2),
            'referral_revenue_raw' => $referralRevenue,
            'payouts_made_raw'     => $payoutsMade
        ]);
    }

    public function getTopReferrers()
    {
        $topReferrerIds = \App\Models\Customer::whereNotNull('referred_by')
            ->select('referred_by', DB::raw('count(*) as total'))
            ->groupBy('referred_by')
            ->orderByDesc('total')
            ->limit(5)
            ->get();

        $topReferrers = [];
        foreach ($topReferrerIds as $ref) {
            $referrer = \App\Models\Customer::find($ref->referred_by);
            if ($referrer) {
                // Determine level based on total referrals (example logic)
                $level = 'Level 1';
                if ($ref->total >= 50) $level = 'Level 3';
                else if ($ref->total >= 10) $level = 'Level 2';

                $topReferrers[] = [
                    'user'   => $referrer->name,
                    'parent' => $referrer->email ?? 'N/A', // Using email as parent/contact info for display
                    'level'  => $level . ' (' . $ref->total . ' refs)'
                ];
            }
        }
        return response()->json($topReferrers);
    }

    public function getCampaigns()
    {
        $campaigns = \App\Models\PromotionCampaign::orderByDesc('id')->get()->map(function($c) {
            $meta = json_decode($c->meta_data, true) ?? [];
            return [
                'name' => $c->name,
                'type' => $c->type,
                'start_date' => $c->start_date,
                'end_date' => $c->end_date,
                'budget' => $meta['budget'] ?? 0,
                'conversions' => $c->used_count ?? 0,
                'status' => $c->status
            ];
        });
        return response()->json($campaigns);
    }

    public function storeCampaign(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'type' => 'required|string',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date',
            'budget' => 'nullable|numeric',
            'reward' => 'nullable|numeric',
        ]);

        \App\Models\PromotionCampaign::create([
            'name' => $request->name,
            'slug' => \Illuminate\Support\Str::slug($request->name) . '-' . time(),
            'type' => $request->type,
            'discount_type' => 'percentage',
            'discount_value' => $request->reward ?? 0,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'meta_data' => json_encode(['budget' => $request->budget ?? 0]),
            'status' => 'active'
        ]);
        
        return response()->json(['success' => true]);
    }

    public function campaignTracking()
    {
        $campaigns = \App\Models\PromotionCampaign::latest()->get();
        
        $totalClicks = $campaigns->sum('unique_users');
        $totalConversions = $campaigns->sum('total_orders');
        $totalSpend = $campaigns->sum('total_discount_given');
        
        $conversionRate = $totalClicks > 0 ? ($totalConversions / $totalClicks) * 100 : 0;
        $avgCost = $totalConversions > 0 ? $totalSpend / $totalConversions : 0;

        // Chart Data (Last 7 days mock/aggregate from created_at as an example)
        $chartDates = [];
        $chartClicks = [];
        $chartConversions = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i)->format('M d');
            $chartDates[] = $date;
            
            // For a fully dynamic chart from PromotionCampaign based on creation date:
            $dayCampaigns = $campaigns->filter(function($camp) use ($i) {
                return $camp->created_at && $camp->created_at->format('Y-m-d') === now()->subDays($i)->format('Y-m-d');
            });
            $chartClicks[] = $dayCampaigns->sum('unique_users');
            $chartConversions[] = $dayCampaigns->sum('total_orders');
        }

        // Helper to format numbers like 245.8K
        $formatNumber = function ($number) {
            if ($number >= 100000) {
                return round($number / 100000, 2) . 'L'; // Lakhs
            }
            if ($number >= 1000) {
                return round($number / 1000, 1) . 'K';
            }
            return number_format($number);
        };

        return view('admin.marketing.campaigns', compact(
            'campaigns', 
            'totalClicks', 
            'totalConversions', 
            'conversionRate', 
            'avgCost',
            'formatNumber',
            'chartDates',
            'chartClicks',
            'chartConversions'
        ));
    }

    public function rewardConfig()
    {
        return view('admin.marketing.reward-config');
    }

    // ========== DROPDOWNS ==========
    public function getCategories()
    {
        $cats = DB::table('marketplace_categories')
            ->select('name', \Illuminate\Support\Facades\DB::raw('GROUP_CONCAT(id) as ids'))
            ->groupBy('name')
            ->orderBy('name')
            ->get();
        return response()->json($cats);
    }

    public function getProducts()
    {
        return response()->json(
            DB::table('products')->select('id', 'name', 'category_id')->orderBy('name')->get()
        );
    }

    public function getSellers()
    {
        return response()->json(
            DB::table('sellers')->select('id', 'name', 'org_name')->orderBy('name')->get()
        );
    }

    // ========== CATEGORY REWARDS ==========
    public function categoryRewardsList()
    {
        $rows = DB::table('referral_configurations')
            ->where('referral_configurations.type', 'category_reward')
            ->join('marketplace_categories', 'referral_configurations.reference_id', '=', 'marketplace_categories.id')
            ->select(
                'referral_configurations.id',
                'marketplace_categories.id as category_id',
                'marketplace_categories.name as category_name',
                'referral_configurations.reward_type',
                'referral_configurations.reward_value',
                DB::raw("IF(referral_configurations.status = 1, 'active', 'inactive') as status")
            )
            ->orderBy('referral_configurations.id')
            ->get();

        return response()->json($rows);
    }

    public function storeCategoryReward(Request $request)
    {
        $request->validate([
            'category_id'  => 'required|exists:marketplace_categories,id',
            'reward_type'  => 'required|in:fixed,percentage',
            'reward_value' => 'required|numeric|min:0',
            'status'       => 'required|in:active,inactive',
        ]);

        DB::table('referral_configurations')->insert([
            'type'         => 'category_reward',
            'reference_id' => $request->category_id,
            'reward_type'  => $request->reward_type,
            'reward_value' => $request->reward_value,
            'status'       => $request->status === 'active' ? 1 : 0,
            'created_at'   => now(),
            'updated_at'   => now(),
        ]);

        return response()->json(['success' => true, 'message' => 'Category reward added.']);
    }

    public function updateCategoryReward(Request $request, $id)
    {
        $request->validate([
            'category_id'  => 'required|exists:marketplace_categories,id',
            'reward_type'  => 'required|in:fixed,percentage',
            'reward_value' => 'required|numeric|min:0',
            'status'       => 'required|in:active,inactive',
        ]);

        DB::table('referral_configurations')->where('id', $id)->update([
            'reference_id' => $request->category_id,
            'reward_type'  => $request->reward_type,
            'reward_value' => $request->reward_value,
            'status'       => $request->status === 'active' ? 1 : 0,
            'updated_at'   => now(),
        ]);

        return response()->json(['success' => true, 'message' => 'Category reward updated.']);
    }

    public function deleteCategoryReward($id)
    {
        DB::table('referral_configurations')->where('id', $id)->delete();
        return response()->json(['success' => true, 'message' => 'Category reward deleted.']);
    }

    // ========== LEVEL COMMISSIONS ==========
    public function levelsList()
    {
        $rows = DB::table('referral_configurations')
            ->where('type', 'level_commission')
            ->select(
                'id',
                DB::raw("CONCAT('Level ', reference_id) as level_name"),
                'commission_percentage',
                'description',
                DB::raw("IF(status = 1, 'active', 'inactive') as status")
            )
            ->orderBy('reference_id')
            ->get();

        return response()->json($rows);
    }

    public function storeLevel(Request $request)
    {
        $request->validate([
            'level_name'            => 'required|string',
            'commission_percentage' => 'required|numeric|min:0',
            'description'           => 'nullable|string',
            'status'                => 'required|in:active,inactive',
        ]);

        $levelNum = (int) filter_var($request->level_name, FILTER_SANITIZE_NUMBER_INT);
        if ($levelNum < 1) {
            $levelNum = (int)(DB::table('referral_configurations')
                ->where('type', 'level_commission')
                ->max('reference_id') ?? 0) + 1;
        }

        DB::table('referral_configurations')->insert([
            'type'                 => 'level_commission',
            'reference_id'         => $levelNum,
            'commission_percentage'=> $request->commission_percentage,
            'description'          => $request->description ?? $request->level_name,
            'status'               => $request->status === 'active' ? 1 : 0,
            'created_at'           => now(),
            'updated_at'           => now(),
        ]);

        return response()->json(['success' => true, 'message' => 'Level added.']);
    }

    public function updateLevel(Request $request, $id)
    {
        $request->validate([
            'level_name'            => 'required|string',
            'commission_percentage' => 'required|numeric|min:0',
            'description'           => 'nullable|string',
            'status'                => 'required|in:active,inactive',
        ]);

        $levelNum = (int) filter_var($request->level_name, FILTER_SANITIZE_NUMBER_INT);
        if ($levelNum < 1) {
            $levelNum = (int) DB::table('referral_configurations')->where('id', $id)->value('reference_id');
        }

        DB::table('referral_configurations')->where('id', $id)->update([
            'reference_id'         => $levelNum,
            'commission_percentage'=> $request->commission_percentage,
            'description'          => $request->description ?? $request->level_name,
            'status'               => $request->status === 'active' ? 1 : 0,
            'updated_at'           => now(),
        ]);

        return response()->json(['success' => true, 'message' => 'Level updated.']);
    }

    public function deleteLevel($id)
    {
        DB::table('referral_configurations')->where('id', $id)->delete();
        return response()->json(['success' => true, 'message' => 'Level deleted.']);
    }

    // ========== PRODUCT CAMPAIGNS ==========
    public function productCampaignsList()
    {
        $rows = DB::table('referral_configurations')
            ->where('referral_configurations.type', 'product_campaign')
            ->leftJoin('products', 'referral_configurations.reference_id', '=', 'products.id')
            ->select(
                'referral_configurations.id',
                'referral_configurations.reference_id as product_id',
                DB::raw("COALESCE(products.name, 'Deleted Product') as product_name"),
                'referral_configurations.reward_value',
                'referral_configurations.start_date',
                'referral_configurations.end_date',
                DB::raw("IF(referral_configurations.status = 1, 'active', 'inactive') as status")
            )
            ->orderBy('referral_configurations.id')
            ->get();

        return response()->json($rows);
    }

    public function storeProductCampaign(Request $request)
    {
        $request->validate([
            'product_id'   => 'required|exists:products,id',
            'reward_value' => 'required|numeric|min:0',
            'start_date'   => 'required|date',
            'end_date'     => 'required|date|after_or_equal:start_date',
            'status'       => 'required|in:active,inactive',
        ]);

        DB::table('referral_configurations')->insert([
            'type'         => 'product_campaign',
            'reference_id' => $request->product_id,
            'reward_value' => $request->reward_value,
            'start_date'   => $request->start_date,
            'end_date'     => $request->end_date,
            'status'       => $request->status === 'active' ? 1 : 0,
            'created_at'   => now(),
            'updated_at'   => now(),
        ]);

        return response()->json(['success' => true, 'message' => 'Product campaign added.']);
    }

    public function updateProductCampaign(Request $request, $id)
    {
        $request->validate([
            'product_id'   => 'required|exists:products,id',
            'reward_value' => 'required|numeric|min:0',
            'start_date'   => 'required|date',
            'end_date'     => 'required|date|after_or_equal:start_date',
            'status'       => 'required|in:active,inactive',
        ]);

        DB::table('referral_configurations')->where('id', $id)->update([
            'reference_id' => $request->product_id,
            'reward_value' => $request->reward_value,
            'start_date'   => $request->start_date,
            'end_date'     => $request->end_date,
            'status'       => $request->status === 'active' ? 1 : 0,
            'updated_at'   => now(),
        ]);

        return response()->json(['success' => true, 'message' => 'Product campaign updated.']);
    }

    public function deleteProductCampaign($id)
    {
        DB::table('referral_configurations')->where('id', $id)->delete();
        return response()->json(['success' => true, 'message' => 'Product campaign deleted.']);
    }

    // ========== REWARD REDEMPTION TIERS ==========
    public function rewardTiersList()
    {
        $rows = DB::table('reward_redemption_tiers')
            ->select(
                'id',
                'points_required',
                'discount_percentage',
                'expiry_days',
                'status'
            )
            ->orderBy('points_required')
            ->get();

        return response()->json($rows);
    }

    public function storeRewardTier(Request $request)
    {
        $request->validate([
            'points_required' => 'required|integer|min:1|unique:reward_redemption_tiers,points_required',
            'discount_percentage' => 'required|numeric|min:0|max:100',
            'expiry_days'         => 'required|integer|min:1',
            'status'              => 'required|in:active,inactive',
        ]);

        DB::table('reward_redemption_tiers')->insert([
            'points_required'     => $request->points_required,
            'discount_percentage' => $request->discount_percentage,
            'expiry_days'         => $request->expiry_days,
            'status'              => $request->status,
            'created_at'      => now(),
            'updated_at'      => now(),
        ]);

        return response()->json(['success' => true, 'message' => 'Reward tier added.']);
    }

    public function updateRewardTier(Request $request, $id)
    {
        $request->validate([
            'points_required' => 'required|integer|min:1|unique:reward_redemption_tiers,points_required,' . $id,
            'discount_percentage' => 'required|numeric|min:0|max:100',
            'expiry_days'         => 'required|integer|min:1',
            'status'              => 'required|in:active,inactive',
        ]);

        DB::table('reward_redemption_tiers')->where('id', $id)->update([
            'points_required'     => $request->points_required,
            'discount_percentage' => $request->discount_percentage,
            'expiry_days'         => $request->expiry_days,
            'status'              => $request->status,
            'updated_at'      => now(),
        ]);

        return response()->json(['success' => true, 'message' => 'Reward tier updated.']);
    }

    public function deleteRewardTier($id)
    {
        DB::table('reward_redemption_tiers')->where('id', $id)->delete();
        return response()->json(['success' => true, 'message' => 'Reward tier deleted.']);
    }

    // ========== PAYOUT SETTINGS ==========
    public function getPayoutSettings()
    {
        $settings = DB::table('referral_configurations')
            ->where('type', 'payout_setting')
            ->first();

        if (!$settings) {
            return response()->json([
                'min_payout_amount' => 500,
                'payout_frequency'  => 'Monthly',
                'payout_method'     => 'Bank Transfer',
                'auto_approve'      => true,
            ]);
        }

        $freqMap = [
            'monthly'   => 'Monthly',
            'weekly'    => 'Weekly',
            'bi-weekly' => 'Bi-Weekly',
        ];
        $methodMap = [
            'bank_transfer' => 'Bank Transfer',
            'upi'           => 'UPI',
            'wallet_credit' => 'Wallet Credit',
        ];

        return response()->json([
            'min_payout_amount' => $settings->min_payout_amount,
            'payout_frequency'  => $freqMap[$settings->payout_frequency] ?? 'Monthly',
            'payout_method'     => $methodMap[$settings->payout_method] ?? 'Bank Transfer',
            'auto_approve'      => (bool) $settings->auto_approve,
        ]);
    }

    public function savePayoutSettings(Request $request)
    {
        $request->validate([
            'min_payout_amount' => 'required|numeric|min:0',
            'payout_frequency'  => 'required|in:Monthly,Weekly,Bi-Weekly',
            'payout_method'     => 'required|in:Bank Transfer,UPI,Wallet Credit',
            'auto_approve'      => 'required|boolean',
        ]);

        $freqMap = [
            'Monthly'   => 'monthly',
            'Weekly'    => 'weekly',
            'Bi-Weekly' => 'bi-weekly',
        ];
        $methodMap = [
            'Bank Transfer' => 'bank_transfer',
            'UPI'           => 'upi',
            'Wallet Credit' => 'wallet_credit',
        ];

        $data = [
            'type'              => 'payout_setting',
            'min_payout_amount' => $request->min_payout_amount,
            'payout_frequency'  => $freqMap[$request->payout_frequency],
            'payout_method'     => $methodMap[$request->payout_method],
            'auto_approve'      => $request->auto_approve ? 1 : 0,
            'updated_at'        => now(),
        ];

        $existing = DB::table('referral_configurations')
            ->where('type', 'payout_setting')
            ->first();

        if ($existing) {
            DB::table('referral_configurations')->where('id', $existing->id)->update($data);
        } else {
            DB::table('referral_configurations')->insert(array_merge($data, ['created_at' => now()]));
        }

        return response()->json(['success' => true, 'message' => 'Payout settings updated successfully']);
    }

    public function getReferralConfigurations()
    {
        $configs = DB::table('referral_configurations')
            ->whereIn('type', ['buyer_to_buyer', 'seller_to_seller'])
            ->orderBy('id', 'desc')
            ->get();
        return response()->json($configs);
    }

    public function saveReferralConfigurations(Request $request)
    {
        $configs = $request->input('configs');
        
        if (!$configs || !is_array($configs)) {
            return response()->json(['success' => false, 'message' => 'No configurations provided']);
        }

        DB::beginTransaction();
        try {
            foreach ($configs as $config) {
                // Ensure required fields
                if (empty($config['type']) || empty($config['reference_id']) || !isset($config['commission_percentage'])) {
                    continue; // Skip invalid rows
                }

                $extraDesc = json_encode([
                    'referee_discount' => $config['referee_discount'] ?? 0
                ]);

                DB::table('referral_configurations')->insert([
                    'type' => $config['type'],
                    'reference_id' => $config['reference_id'], // Seller ID or Product ID
                    'reward_type' => 'percentage',
                    'commission_percentage' => $config['commission_percentage'], // Reward for Buyer A / Seller
                    'description' => $extraDesc,
                    'status' => 1,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
            DB::commit();
            return response()->json(['success' => true, 'message' => 'Referral Configurations saved successfully']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Failed to save configurations: ' . $e->getMessage()]);
        }
    }
}