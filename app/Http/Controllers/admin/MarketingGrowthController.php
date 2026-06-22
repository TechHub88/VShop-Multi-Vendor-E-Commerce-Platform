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
            'payouts_made'        => '₹' . number_format($payoutsMade, 2)
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
        if (\Illuminate\Support\Facades\Schema::hasTable('referral_campaigns')) {
            $campaigns = \App\Models\ReferralCampaign::orderByDesc('id')->get();
            return response()->json($campaigns);
        }
        return response()->json([]);
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

        if (\Illuminate\Support\Facades\Schema::hasTable('referral_campaigns')) {
            \App\Models\ReferralCampaign::create([
                'name' => $request->name,
                'type' => $request->type,
                'start_date' => $request->start_date,
                'end_date' => $request->end_date,
                'budget' => $request->budget ?? 0,
                'reward' => $request->reward ?? 0,
                'status' => 'active'
            ]);
            return response()->json(['success' => true]);
        }
        return response()->json(['success' => false, 'message' => 'Table not migrated']);
    }

    public function campaignTracking()
    {
        return view('admin.marketing.campaigns');
    }

    public function rewardConfig()
    {
        return view('admin.marketing.reward-config');
    }

    // ========== DROPDOWNS ==========
    public function getCategories()
    {
        return response()->json(
            DB::table('marketplace_categories')->select('id', 'name')->orderBy('name')->get()
        );
    }

    public function getProducts()
    {
        return response()->json(
            DB::table('products')->select('id', 'name')->orderBy('name')->get()
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
            $data['created_at'] = now();
            DB::table('referral_configurations')->insert($data);
        }

        return response()->json(['success' => true, 'message' => 'Payout settings saved.']);
    }
}