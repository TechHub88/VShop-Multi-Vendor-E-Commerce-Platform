<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\SellerFinance;
use App\Models\Tax;
use App\Models\Invoice;
use App\Models\SellerCommission;
use App\Models\Order;
use App\Models\Seller;


use App\Models\GlobalRule;
use App\Models\Payout;
use App\Models\Grievance;
use App\Models\GlobalSetting;
use App\Models\CommissionLevel;
use App\Models\Product;
use App\Models\MarketplaceCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminFinanceController extends Controller
{
   
   
   public function index(Request $request)
{
    $currency = $request->get('currency', session('currency', 'INR'));
    session(['currency' => $currency]);
    $exchangeRate = $this->getExchangeRate($currency);
    
    // Dashboard Stats
    $totalRevenue = Order::where('payment_status', 'paid')->sum('total_amount') * $exchangeRate;
    $totalCommission = SellerCommission::sum('commission_amount') * $exchangeRate;
    $totalPayouts = SellerFinance::where('transaction_type', 'payout')->where('status', 'completed')->sum('debit') * $exchangeRate;
    $pendingPayouts = SellerFinance::where('transaction_type', 'payout')->where('status', 'pending')->sum('debit') * $exchangeRate;
    $refundAmount = SellerFinance::where('transaction_type', 'refund')->sum('credit') * $exchangeRate;
    $disputesCount = Grievance::count();
    $openDisputes = Grievance::where('status', 'open')->count();
    $resolvedDisputes = Grievance::where('status', 'close')->count();
    
    $netRevenue = $totalRevenue - $totalCommission - $refundAmount;
    $leadRevenue = SellerFinance::where('transaction_type', 'lead_fee')->sum('credit') * $exchangeRate;
    
    // Tax Collected
    $gstRate = (float) GlobalSetting::get('gst_rate', 18);
    $taxCollected = $totalCommission * ($gstRate / 100);
    
    // Active Hold Amount
    $activeHoldAmount = SellerFinance::where('transaction_type', 'hold')->where('status', 'pending')->sum('debit') * $exchangeRate;
    if ($activeHoldAmount == 0) $activeHoldAmount = $pendingPayouts * 0.35;
    
    // Today's Revenue
    $todayClickRevenue = SellerFinance::where('transaction_type', 'click')->whereDate('created_at', today())->sum('credit') * $exchangeRate;
    $todayEnquiryRevenue = SellerFinance::where('transaction_type', 'enquiry')->whereDate('created_at', today())->sum('credit') * $exchangeRate;
    
    // Top Lead Seller
    $topLeadSeller = Seller::select('sellers.id', 'sellers.name', DB::raw('SUM(seller_finances.credit) as total'))
        ->join('seller_finances', 'sellers.id', '=', 'seller_finances.seller_id')
        ->where('seller_finances.transaction_type', 'lead_fee')
        ->groupBy('sellers.id', 'sellers.name')
        ->orderBy('total', 'desc')
        ->first();
    
    // Revenue Breakdown
    $productRevenue = SellerCommission::where('product_service', 'product')->sum('order_amount') * $exchangeRate;
    $serviceRevenue = SellerCommission::where('product_service', 'service')->sum('order_amount') * $exchangeRate;
    
    // Revenue Mix for Doughnut Chart
    $subscriptionRevenue = SellerFinance::where('transaction_type', 'subscription')->sum('credit') * $exchangeRate;
    $referralRevenue = SellerFinance::where('transaction_type', 'referral')->sum('credit') * $exchangeRate;
    $otherRevenue = max(0, $totalRevenue - $productRevenue - $serviceRevenue - $leadRevenue - $subscriptionRevenue - $referralRevenue);
    
    $revenueMix = [
        'Direct Sales' => $productRevenue,
        'Service Revenue' => $serviceRevenue,
        'Lead Revenue' => $leadRevenue,
        'Subscription' => $subscriptionRevenue,
        'Referral' => $referralRevenue,
        'Other' => $otherRevenue,
    ];
    
    // App-wise Revenue
    $appWiseRevenue = [
        'vShop' => SellerCommission::whereHas('seller', fn($q) => $q->where('application', 'vshop'))->sum('order_amount') * $exchangeRate,
        'vTravel' => SellerCommission::whereHas('seller', fn($q) => $q->where('application', 'vtravel'))->sum('order_amount') * $exchangeRate,
        'vCampus' => SellerCommission::whereHas('seller', fn($q) => $q->where('application', 'vcampus'))->sum('order_amount') * $exchangeRate,
        'vEdge' => SellerCommission::whereHas('seller', fn($q) => $q->where('application', 'vedge'))->sum('order_amount') * $exchangeRate,
        'Wellness' => SellerCommission::whereHas('seller', fn($q) => $q->where('application', 'wellness'))->sum('order_amount') * $exchangeRate,
        'Marketplace' => SellerCommission::whereHas('seller', fn($q) => $q->where('application', 'marketplace'))->sum('order_amount') * $exchangeRate,
    ];
    
    // Commission Trends (last 6 months)
    $trendLabels = [];
    $trendData = [];
    $revenueTrendData = [];
    for ($i = 5; $i >= 0; $i--) {
        $month = now()->subMonths($i);
        $trendLabels[] = $month->format('M');
        $trendData[] = SellerCommission::whereYear('created_at', $month->year)->whereMonth('created_at', $month->month)->sum('commission_amount') * $exchangeRate;
        $revenueTrendData[] = Order::where('payment_status', 'paid')->whereYear('created_at', $month->year)->whereMonth('created_at', $month->month)->sum('total_amount') * $exchangeRate;
    }
    
    // Transaction Ledger KPIs
    $totalTransactions = SellerFinance::count();
    $totalCredited = SellerFinance::sum('credit') * $exchangeRate;
    $totalDebited = SellerFinance::sum('debit') * $exchangeRate;
    $closingBalance = $totalCredited - $totalDebited;
    
    // Multi-Level Commission Rules from database
    $commissionRules = DB::table('commission_management')->get()->map(function($item) {
        return (array) $item;
    })->toArray();
    if (empty($commissionRules)) {
        $commissionRules = [
            [
                'seller_id' => null,
                'app_type' => 'vshop',
                'sell_type' => 'direct',
                'total_sales' => 0.00,
                'commission_type' => 'percentage',
                'seller_commission' => 20,
                'referrer_commission' => 10,
                'platform_commission' => 70,
                'referral_id' => null
            ]
        ];
    }
    
    // Seller-wise Earnings
    $sellerEarnings = Seller::select('sellers.id', 'sellers.name', DB::raw('SUM(seller_commissions.net_earning) as earnings, SUM(seller_commissions.commission_amount) as commission'))
        ->join('seller_commissions', 'sellers.id', '=', 'seller_commissions.seller_id')
        ->groupBy('sellers.id', 'sellers.name')
        ->orderBy('earnings', 'desc')
        ->limit(10)
        ->get();
    
    // Platform Commission Tracking
    $platformCommission = [
        'this_month' => SellerCommission::whereYear('created_at', now()->year)->whereMonth('created_at', now()->month)->sum('commission_amount') * $exchangeRate,
        'last_month' => SellerCommission::whereYear('created_at', now()->subMonth()->year)->whereMonth('created_at', now()->subMonth()->month)->sum('commission_amount') * $exchangeRate,
    ];
    
    // Pending Payouts List
    $pendingPayoutsList = SellerFinance::with('seller')->where('transaction_type', 'payout')->where('status', 'pending')->orderBy('created_at', 'desc')->get();
    
    // Transaction Ledger
    $transactions = SellerFinance::with(['seller', 'order'])->orderBy('created_at', 'desc')->limit(100)->get();
    
    // Failed Payouts
    $failedPayouts = SellerFinance::where('transaction_type', 'payout')->where('status', 'failed')->orderBy('created_at', 'desc')->get();
    
    // Dispute Holds
    $disputeHolds = Grievance::with(['seller', 'buyer'])->orderBy('created_at', 'desc')->get();
    
    // Live Refund Orders list
    $refundOrders = Order::with(['seller', 'customer'])->whereNotNull('refund_status')->orWhere('return_requested', 1)->orderBy('updated_at', 'desc')->get();
    
    // Refund Reasons Analytics
    $refundReasonsRaw = Order::whereNotNull('return_reason')
        ->select('return_reason', DB::raw('count(*) as count'))
        ->groupBy('return_reason')
        ->get();
    
    $refundReasonsData = [];
    foreach ($refundReasonsRaw as $r) {
        $reasonLabel = $r->return_reason ?: 'Other';
        $refundReasonsData[$reasonLabel] = (int)$r->count;
    }
    if (empty($refundReasonsData)) {
        $refundReasonsData = [
            'Damaged' => 32,
            'Wrong Item' => 28,
            'Not as Described' => 40,
            'Late Delivery' => 12,
            'Other' => 8
        ];
    }
    
    // Settlement Hold Sellers
    $holdSellers = Seller::where('settlement_hold', true)->orWhere('is_new_seller', true)->get();
    
    // Refund Deduction Impact
    $refundImpact = SellerFinance::where('transaction_type', 'refund')->whereYear('created_at', now()->year)->whereMonth('created_at', now()->month)->sum('debit') * $exchangeRate;
    
    // Additional Refund Analytics dynamic stats
    $refundTxnsCount = SellerFinance::where('transaction_type', 'refund')->count();
    $avgRefundSla = Grievance::where('status', 'resolved')->whereNotNull('resolved_at')->avg(DB::raw('DATEDIFF(resolved_at, created_at)'));
    $recoveredAmount = SellerFinance::where('transaction_type', 'refund_recovered')->sum('credit') * $exchangeRate;
    $commissionReversedAmount = SellerFinance::where('transaction_type', 'commission_reversed')->sum('debit') * $exchangeRate;
    $leadRefundAmount = SellerFinance::where('transaction_type', 'lead_refund')->sum('credit') * $exchangeRate;
    $leadRefundCount = SellerFinance::where('transaction_type', 'lead_refund')->count();
    
    // Dispute Wallet Hold Tracker
    $disputeWalletHold = SellerFinance::where('dispute_status', 'pending')->where('status', 'pending')->sum('debit') * $exchangeRate;
    if ($disputeWalletHold == 0) $disputeWalletHold = $openDisputes * 100 * $exchangeRate;
    
    // App Wise Commission from settings
    $appWiseCommission = [
        'vShop' => GlobalSetting::get('commission_vshop', 25),
        'vTravel' => GlobalSetting::get('commission_vtravel', 20),
        'vEdge' => GlobalSetting::get('commission_vedge', 15),
        'vCampus' => GlobalSetting::get('commission_vcampus', 10),
    ];
    
    // Get all settings for the form
    $settings = [
                'hold_days' => GlobalSetting::get('hold_days', 7),
        'release_type' => GlobalSetting::get('release_type', 'auto'),
        'return_threshold' => GlobalSetting::get('return_threshold', 20),
        'new_seller_hold_days' => GlobalSetting::get('new_seller_hold_days', 30),
        'gst_rate' => GlobalSetting::get('gst_rate', 18),
        'refund_mode' => GlobalSetting::get('refund_mode', 'auto'),
        'partial_refund_enabled' => GlobalSetting::get('partial_refund_enabled', 1),
        'max_partial_percent' => GlobalSetting::get('max_partial_percent', 50),
        'refund_sla_days' => GlobalSetting::get('refund_sla_days', 7),
        'fraud_detection' => GlobalSetting::get('fraud_detection', 'advanced'),
        'rating_impact' => GlobalSetting::get('rating_impact', 'yes'),
        'reverse_commission' => GlobalSetting::get('reverse_commission', 1),
        'freeze_payout' => GlobalSetting::get('freeze_payout', 1),
        'freeze_duration' => GlobalSetting::get('freeze_duration', 14),
        'freeze_action' => GlobalSetting::get('freeze_action', 'hold'),
        'reverse_referral' => GlobalSetting::get('reverse_referral', 1),
        'refund_lead_charges' => GlobalSetting::get('refund_lead_charges', 1),
        'lead_fraud_threshold' => GlobalSetting::get('lead_fraud_threshold', 3),
        'lead_refund_type' => GlobalSetting::get('lead_refund_type', 'partial'),
        // New lead refund rule settings
        'duplicate_click_threshold' => GlobalSetting::get('duplicate_click_threshold', 5),
        'max_clicks_per_interval' => GlobalSetting::get('max_clicks_per_interval', 3),
        'same_ip_click_limit' => GlobalSetting::get('same_ip_click_limit', 20),
        'enable_fake_enquiry_check' => GlobalSetting::get('enable_fake_enquiry_check', true),
        'ppc_rate' => GlobalSetting::get('ppc_rate', 0.50),
        'ppl_rate' => GlobalSetting::get('ppl_rate', 5.00),
        'free_lead_limit' => GlobalSetting::get('free_lead_limit', 100),
        'paid_lead_threshold' => GlobalSetting::get('paid_lead_threshold', 0.50),
        'release_type' => GlobalSetting::get('release_type', 'auto'),
        'return_threshold' => GlobalSetting::get('return_threshold', 20),
        'new_seller_hold_days' => GlobalSetting::get('new_seller_hold_days', 30),
        'gst_rate' => GlobalSetting::get('gst_rate', 18),
        'refund_mode' => GlobalSetting::get('refund_mode', 'auto'),
        'partial_refund_enabled' => GlobalSetting::get('partial_refund_enabled', 1),
        'max_partial_percent' => GlobalSetting::get('max_partial_percent', 50),
        'refund_sla_days' => GlobalSetting::get('refund_sla_days', 7),
        'fraud_detection' => GlobalSetting::get('fraud_detection', 'advanced'),
        'rating_impact' => GlobalSetting::get('rating_impact', 'yes'),
        'reverse_commission' => GlobalSetting::get('reverse_commission', 1),
        'freeze_payout' => GlobalSetting::get('freeze_payout', 1),
        'freeze_duration' => GlobalSetting::get('freeze_duration', 14),
        'freeze_action' => GlobalSetting::get('freeze_action', 'hold'),
        'reverse_referral' => GlobalSetting::get('reverse_referral', 1),
        'refund_lead_charges' => GlobalSetting::get('refund_lead_charges', 1),
        'lead_fraud_threshold' => GlobalSetting::get('lead_fraud_threshold', 3),
        'lead_refund_type' => GlobalSetting::get('lead_refund_type', 'partial'),
        'ppc_rate' => GlobalSetting::get('ppc_rate', 0.50),
        'ppl_rate' => GlobalSetting::get('ppl_rate', 5.00),
        'free_lead_limit' => GlobalSetting::get('free_lead_limit', 100),
        'paid_lead_threshold' => GlobalSetting::get('paid_lead_threshold', 0.50),
        'dynamic_pricing_enabled' => GlobalSetting::get('dynamic_pricing_enabled', 0),
        'dynamic_pricing_strategy' => GlobalSetting::get('dynamic_pricing_strategy', 'demand'),
        'price_adjust_limit' => GlobalSetting::get('price_adjust_limit', 15),
        'price_update_freq' => GlobalSetting::get('price_update_freq', 'daily'),
        'discount_cap_enabled' => GlobalSetting::get('discount_cap_enabled', 1),
        'discount_cap_percent' => GlobalSetting::get('discount_cap_percent', 30),
        'discount_cap_action' => GlobalSetting::get('discount_cap_action', 'adjust'),
        'coupon_discount_cap' => GlobalSetting::get('coupon_discount_cap', 500),
    ];

    $sellers = Seller::all();
    $categories = MarketplaceCategory::all();
    $products = Product::select('id', 'name', 'price', 'category_id', 'platform')->get();
    
    // 🔥 ONLY THIS PART CHANGED - Empty array instead of demo data
    $globalRulesTable = json_decode(GlobalSetting::get('global_commission_rules_table'), true);
    if (empty($globalRulesTable)) {
        $globalRulesTable = [];  // ← EMPTY ARRAY - SAB BLANK HOGA
    }

    $earningsDetails = [];
    $earningTransactions = \App\Models\SellerFinance::where('transaction_type', 'earning')
        ->with(['seller', 'order', 'order.items.product'])
        ->orderBy('created_at', 'desc')
        ->get();

    foreach ($earningTransactions as $txn) {
        $seller = $txn->seller;
        if (!$seller) continue;
        
        $locationParts = array_filter([$seller->billing_city, $seller->billing_state, $seller->billing_country]);
        $location = !empty($locationParts) ? implode(', ', $locationParts) : 'Not specified';
        $app = $seller->application ?? 'vshop';
        
        // Find platform commission for this specific transaction
        $deductionTxn = \App\Models\SellerFinance::where('transaction_type', 'commission_deduction')
            ->where('order_id', $txn->order_id)
            ->where('seller_id', $txn->seller_id)
            ->where('note', 'like', '%Platform%')
            ->first();
            
        $platformCommission = $deductionTxn ? $deductionTxn->debit : 0;
        
        $sellerPayout = $txn->credit - $platformCommission;
        
        // Find products for this seller in this order
        $productDetails = [];
        if ($txn->order) {
            foreach ($txn->order->items as $item) {
                if ($item->product && $item->product->created_by_id == $seller->id) {
                    $thumb = $item->product->thumbnail;
                    if ($thumb) {
                        $thumbUrl = str_starts_with($thumb, 'http') ? $thumb : asset('storage/' . $thumb);
                    } else {
                        $thumbUrl = asset('assets/images/default-product.png');
                    }
                    $productDetails[] = [
                        'name' => $item->product->name,
                        'image' => $thumbUrl
                    ];
                }
            }
        }
        $productDetails = array_values($productDetails);
        
        // Hold days logic - calculate difference between release_date and created_at
        $holdDays = 0;
        if ($txn->release_date) {
            $created = \Carbon\Carbon::parse($txn->created_at)->startOfDay();
            $release = \Carbon\Carbon::parse($txn->release_date)->startOfDay();
            $holdDays = $created->diffInDays($release);
        }

        $earningsDetails[] = [
            'id' => $txn->id,
            'seller_id' => $seller->id,
            'seller_code' => 'S-' . (1000 + $seller->id),
            'name' => $seller->name,
            'location' => $location,
            'app' => count($productDetails) > 0 ? $productDetails : 'Unknown Product', // Array of products for UI
            'total_sales' => $txn->credit,
            'platform_commission' => $platformCommission,
            'seller_payout' => $sellerPayout,
            'hold_days' => $holdDays,
            'payout_schedule' => $seller->payout_frequency ?: 'Weekly',
            'payout_status' => ucfirst($txn->status),
            'referral_payout' => 0,
            'reff_schedule' => 'Monthly',
            'referral_status' => 'Approved',
            'est_tax' => $txn->tax_amount + $txn->gst_amount,
            'net_payout' => $sellerPayout,
            'remaining_balance' => 0,
            'per_enquiry_amount' => 0,
            'per_click_amount' => 0,
            'purchase_date' => $txn->created_at->format('Y-m-d'),
            'release_date' => $txn->release_date ? date('Y-m-d', strtotime($txn->release_date)) : $txn->created_at->format('Y-m-d'),
        ];
    }

    $pricingRulesTable = json_decode(GlobalSetting::get('app_lead_pricing_rules_table'), true);
    if (empty($pricingRulesTable)) {
        $pricingRulesTable = [
            ['app' => 'vShop', 'category' => 'Electronics', 'seller' => 'TechStore', 'click_price' => 0.75, 'enquiry_price' => 7.50],
            ['app' => 'vTravel', 'category' => 'Hotels', 'seller' => 'TravelAgency', 'click_price' => 1.00, 'enquiry_price' => 10.00],
            ['app' => 'vCampus', 'category' => 'Courses', 'seller' => 'EduSeller', 'click_price' => 0.20, 'enquiry_price' => 2.00],
            ['app' => 'vEdge', 'category' => 'Software', 'seller' => 'SoftSeller', 'click_price' => 0.50, 'enquiry_price' => 5.00]
        ];
    }

    $leadMonetizationTable = json_decode(GlobalSetting::get('lead_monetization_rules_table'), true);
    if (empty($leadMonetizationTable)) {
        $leadMonetizationTable = [
            ['category' => 'Electronics', 'click_charge' => 0.50, 'enquiry_charge' => 5.00, 'seller' => 'All Sellers'],
            ['category' => 'Apparel', 'click_charge' => 0.30, 'enquiry_charge' => 3.00, 'seller' => 'All Sellers']
        ];
    }

    $taxRegions = json_decode(GlobalSetting::get('tax_regions'), true);
    if (empty($taxRegions)) {
        $taxRegions = [
            ['region' => 'India', 'tax_name' => 'GST', 'rate' => 18],
            ['region' => 'USA - California', 'tax_name' => 'Sales Tax', 'rate' => 8.5],
            ['region' => 'United Kingdom', 'tax_name' => 'VAT', 'rate' => 20],
            ['region' => 'UAE', 'tax_name' => 'VAT', 'rate' => 5],
            ['region' => 'Australia', 'tax_name' => 'GST', 'rate' => 10]
        ];
    }

    $currenciesList = json_decode(GlobalSetting::get('currencies_list'), true);
    if (empty($currenciesList)) {
        $currenciesList = [
            ['currency' => 'USD', 'symbol' => '$', 'rate' => 1.0, 'auto_update' => 'Fixed'],
            ['currency' => 'INR', 'symbol' => '₹', 'rate' => 83.5, 'auto_update' => 'Daily API 🔄'],
            ['currency' => 'EUR', 'symbol' => '€', 'rate' => 0.92, 'auto_update' => 'Daily API 🔄'],
            ['currency' => 'GBP', 'symbol' => '£', 'rate' => 0.79, 'auto_update' => 'Daily API 🔄']
        ];
    }

    $invoiceTemplate = json_decode(GlobalSetting::get('invoice_template_settings'), true);
    if (empty($invoiceTemplate)) {
        $invoiceTemplate = [
            'style' => 'Professional',
            'companyName' => 'vShop Marketplace',
            'companyAddress' => '123 Business St, City',
            'companyEmail' => 'billing@company.com',
            'companyPhone' => '+1 234 567 8900',
            'companyGst' => '22AAAAA0000A1Z',
            'invoicePrefix' => 'INV-VS',
            'invoiceFormat' => '{prefix}{year}{month}{seq}',
            'invoiceFooter' => 'Thank you for your business!'
        ];
    }

    $autoEscalationRules = json_decode(GlobalSetting::get('auto_escalation_rules'), true);
    if (empty($autoEscalationRules)) {
        $autoEscalationRules = [
            ['condition' => 'Dispute open > 5 days', 'action' => 'Escalate to Senior Manager', 'timeframe' => '5 days', 'status' => 'Active'],
            ['condition' => 'High value dispute > $500', 'action' => 'Auto escalate to Finance Team', 'timeframe' => 'Immediate', 'status' => 'Active'],
            ['condition' => 'Seller rating < 2 stars', 'action' => 'Priority review', 'timeframe' => '24 hours', 'status' => 'Active']
        ];
    }
    
    // Lead Billing Register - Dynamic from SellerFinance
    $leadBillingData = SellerFinance::with('seller')
        ->whereIn('transaction_type', ['click', 'enquiry', 'lead_fee', 'lead_refund'])
        ->orderBy('created_at', 'desc')
        ->limit(50)
        ->get();
    
    // Refund Trend - Weekly data for last 4 weeks
    $refundTrendWeekly = [];
    for ($w = 3; $w >= 0; $w--) {
        $weekStart = now()->subWeeks($w)->startOfWeek();
        $weekEnd = now()->subWeeks($w)->endOfWeek();
        $refundTrendWeekly[] = SellerFinance::where('transaction_type', 'refund')
            ->whereBetween('created_at', [$weekStart, $weekEnd])
            ->sum('credit') * $exchangeRate;
    }
    
    // Recent Refund Activity
    $recentRefundActivity = SellerFinance::with('seller')
        ->where('transaction_type', 'refund')
        ->orderBy('created_at', 'desc')
        ->limit(10)
        ->get();
    
    // Tax Summary per Seller for export
    $sellerTaxSummary = [];
    foreach ($sellers as $seller) {
        $sellerSales = SellerCommission::where('seller_id', $seller->id)->sum('order_amount') * $exchangeRate;
        $sellerComm = SellerCommission::where('seller_id', $seller->id)->sum('commission_amount') * $exchangeRate;
        $sellerGst = $sellerComm * ($gstRate / 100);
        $sellerTaxSummary[] = [
            'seller_id' => $seller->id,
            'seller_name' => $seller->name,
            'email' => $seller->email,
            'total_sales' => $sellerSales,
            'commission' => $sellerComm,
            'gst_rate' => $gstRate,
            'gst_amount' => $sellerGst,
            'net_payout' => $sellerSales - $sellerComm - $sellerGst,
        ];
    }
    
    return view('admin.finance-management', compact(
        'currency', 'exchangeRate', 'totalRevenue', 'totalCommission', 'totalPayouts', 'pendingPayouts',
        'refundAmount', 'disputesCount', 'openDisputes', 'resolvedDisputes', 'netRevenue', 'leadRevenue',
        'taxCollected', 'activeHoldAmount', 'revenueMix', 'appWiseRevenue', 'revenueTrendData',
        'totalTransactions', 'totalCredited', 'totalDebited', 'closingBalance',
        'todayClickRevenue', 'todayEnquiryRevenue', 'topLeadSeller', 'productRevenue', 'serviceRevenue',
        'trendLabels', 'trendData', 'commissionRules', 'sellerEarnings', 'platformCommission',
        'pendingPayoutsList', 'transactions', 'failedPayouts', 'disputeHolds', 'holdSellers',
        'refundImpact', 'appWiseCommission', 'disputeWalletHold', 'settings',
        'sellers', 'categories', 'products', 'globalRulesTable', 'earningsDetails',
        'pricingRulesTable', 'leadMonetizationTable', 'taxRegions', 'currenciesList', 'invoiceTemplate', 'autoEscalationRules',
        'refundOrders', 'refundReasonsData',
        'refundTxnsCount', 'avgRefundSla', 'recoveredAmount', 'commissionReversedAmount', 'leadRefundAmount', 'leadRefundCount',
        'leadBillingData', 'refundTrendWeekly', 'recentRefundActivity', 'sellerTaxSummary', 'gstRate'
    ));
}
    
    public function updateCommission(Request $request)
    {
        try {
            DB::beginTransaction();
            
            // Clear existing commission levels
            CommissionLevel::query()->delete();
            
            // Insert new commission levels
            $levels = $request->input('levels', []);
            foreach ($levels as $level) {
                CommissionLevel::create([
                    'level' => $level['level'],
                    'role' => $level['role'],
                    'type' => $level['type'],
                    'value' => $level['value']
                ]);
            }
            
            DB::commit();
            return response()->json(['success' => true, 'message' => 'Commission structure saved successfully']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }
    
    public function approvePayout(Request $request)
    {
        try {
            $payoutId = $request->input('payout_id');
            $payout = SellerFinance::find($payoutId);
            
            // Fallback: If payout_id is actually a seller_id
            if (!$payout || $payout->transaction_type !== 'payout') {
                $payout = SellerFinance::where('seller_id', $payoutId)
                    ->where('transaction_type', 'payout')
                    ->orderBy('created_at', 'desc')
                    ->first();
                
                if (!$payout) {
                    $payout = new SellerFinance();
                    $payout->seller_id = $payoutId;
                    $payout->transaction_type = 'payout';
                    $payout->credit = 0;
                    $payout->debit = 0;
                    $payout->balance = 0;
                }
            }

            $customerState = $request->input('state');
            $taxRecord = Tax::where('region', $customerState)->first();
            $taxPercent = $taxRecord ? $taxRecord->rate : 0;
            if ($taxPercent <= 0) {
                $taxPercent = 0;
            }
            $lineTotal = $request->input('line_total', 0);
            $taxAmount = ($lineTotal * $taxPercent) / 100;

            $payout->tax_amount = $taxAmount;
            $payout->status = 'completed';
            $payout->save();

            return response()->json(['success' => true, 'message' => 'Payout approved successfully']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }
    
    public function bulkApprovePayouts(Request $request)
    {
        try {
            $ids = $request->input('payout_ids', []);
            if (!is_array($ids)) {
                $ids = [$ids];
            }
            
            // Try matching as transaction IDs
            $updatedCount = SellerFinance::whereIn('id', $ids)
                ->where('transaction_type', 'payout')
                ->update(['status' => 'completed']);
                
            // If no records updated, treat as seller_ids
            if ($updatedCount === 0) {
                foreach ($ids as $sellerId) {
                    $payout = SellerFinance::where('seller_id', $sellerId)
                        ->where('transaction_type', 'payout')
                        ->orderBy('created_at', 'desc')
                        ->first();
                        
                    if ($payout) {
                        $payout->status = 'completed';
                        $payout->save();
                    } else {
                        // Auto-create completed payout entry
                        $payout = new SellerFinance();
                        $payout->seller_id = $sellerId;
                        $payout->transaction_type = 'payout';
                        $payout->credit = 0;
                        $payout->debit = 0;
                        $payout->balance = 0;
                        $payout->status = 'completed';
                        $payout->save();
                    }
                }
            }
            
            return response()->json(['success' => true, 'message' => 'Payouts approved successfully']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }
    
 

    public function getProductsByCategory($category_id)
    {
        $products = Product::where('category_id', $category_id)->get(['id', 'name', 'price', 'platform']);
        return response()->json($products);
    }

    public function updateEarningsSettings(Request $request)
    {
        try {
            // ---- BULK UPDATE (multiple transactions) ----
            if ($request->has('transaction_ids') && is_array($request->transaction_ids)) {
                $transactionIds = $request->transaction_ids;
                $updates   = $request->input('updates', []);
                $count     = 0;

                foreach ($transactionIds as $tid) {
                    // Find the earning transaction
                    $txn = \App\Models\SellerFinance::find($tid);
                    if (!$txn || $txn->transaction_type !== 'earning') continue;

                    // Also find associated deductions to update them in sync
                    $relatedTxns = \App\Models\SellerFinance::where('order_id', $txn->order_id)
                                    ->where('seller_id', $txn->seller_id)
                                    ->where('status', 'hold')
                                    ->get();

                    if (!empty($updates['hold_days'])) {
                        // Calculate new release date based on created_at and new hold days
                        $newReleaseDate = \Carbon\Carbon::parse($txn->created_at)->startOfDay()->addDays($updates['hold_days'])->toDateString();
                        
                        foreach ($relatedTxns as $rel) {
                            $rel->release_date = $newReleaseDate;
                            
                            // Replace (On Hold for X days) in note
                            $rel->note = preg_replace('/\(On Hold for \d+ days\)/', "(On Hold for {$updates['hold_days']} days)", $rel->note);
                            $rel->save();
                        }
                    }
                    
                    // Note: payout_schedule isn't natively on transaction, skip or apply to seller if requested
                    
                    $count++;
                }
                return response()->json(['success' => true, 'message' => "Bulk update applied to {$count} transaction(s) successfully"]);
            }

            // ---- SINGLE SELLER UPDATE ----
            if ($request->has('seller_id')) {
                $seller = Seller::find($request->seller_id);
                if ($seller) {
                    if ($request->has('hold_days')) {
                        $seller->hold_days = $request->hold_days;
                    }
                    if ($request->has('payout_frequency')) {
                        $seller->payout_frequency = $request->payout_frequency;
                    }
                    $seller->save();

                    if ($request->has('purchase_date')) {
                        $latestOrder = Order::where('seller_id', $seller->id)->orderBy('created_at', 'desc')->first();
                        if ($latestOrder) {
                            $latestOrder->created_at = $request->purchase_date;
                            $latestOrder->save();
                        }
                    }
                    return response()->json(['success' => true, 'message' => 'Seller earnings settings updated successfully']);
                }
                return response()->json(['success' => false, 'message' => 'Seller not found']);
            }

            if ($request->has('earnings_settings')) {
                GlobalSetting::set('earnings_settings_data', json_encode($request->earnings_settings));
            }
            return response()->json(['success' => true, 'message' => 'Earnings settings saved successfully']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }


    public function updateLeadsBoostSettings(Request $request)
    {
        try {
            if ($request->has('pricing_rules_table')) {
                GlobalSetting::set('app_lead_pricing_rules_table', json_encode($request->pricing_rules_table));
            }
            if ($request->has('lead_monetization_rules_table')) {
                GlobalSetting::set('lead_monetization_rules_table', json_encode($request->lead_monetization_rules_table));
            }
            return response()->json(['success' => true, 'message' => 'Leads and Boost settings saved successfully']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }
    
    public function updatePricing(Request $request)
    {
        try {
            $key = 'pricing_' . str_slug($request->app . '_' . $request->category);
            GlobalSetting::set($key, json_encode([
                'click' => $request->click_price,
                'enquiry' => $request->enquiry_price
            ]));
            return response()->json(['success' => true, 'message' => 'Pricing saved successfully']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }



// In AdminFinanceController.php - Add/Update these functions

public function resolveDispute(Request $request)
{
    try {
        $dispute = Grievance::findOrFail($request->dispute_id);
        
        $dispute->update([
            'status' => 'resolved',
            'resolved_at' => now(),
            'resolution_note' => $request->resolution_note,
            'is_auto_escalated' => false
        ]);
        
        return response()->json([
            'success' => true,
            'message' => 'Dispute resolved successfully'
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => $e->getMessage()
        ], 500);
    }
}

public function escalateDispute(Request $request)
{
    try {
        $dispute = Grievance::findOrFail($request->dispute_id);
        
        $dispute->update([
            'assigned_to' => $request->escalate_to,
            'time_frame' => $request->time_frame,
            'escalated_to' => $request->escalate_to,
            'escalated_at' => now(),
            'escalation_level' => $dispute->escalation_level + 1,
            'status' => 'in_progress'
        ]);
        
        return response()->json([
            'success' => true,
            'message' => "Assigned to {$request->escalate_to}",
            'escalation_level' => $dispute->escalation_level
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => $e->getMessage()
        ], 500);
    }
}
    
    public function applyManualHold(Request $request)
    {
        try {
            $request->validate([
                'reason' => 'required|string',
                'seller_id' => 'sometimes|exists:sellers,id'
            ]);
            
            $sellerId = $request->seller_id ?? $request->input('seller_id');
            
            if ($sellerId) {
                $seller = Seller::find($sellerId);
                if ($seller) {
                    $seller->settlement_hold = true;
                    $seller->settlement_hold_reason = $request->reason;
                    $seller->settlement_hold_date = now();
                    $seller->save();
                    
                    // Create a finance record for the hold
                    SellerFinance::create([
                        'seller_id' => $seller->id,
                        'transaction_type' => 'hold',
                        'debit' => 0,
                        'credit' => 0,
                        'status' => 'pending',
                        'note' => 'Manual hold applied: ' . $request->reason,
                        'hold_until' => now()->addDays(GlobalSetting::get('hold_days', 7)),
                        'created_at' => now()
                    ]);
                }
            }
            
            return response()->json(['success' => true, 'message' => 'Manual hold applied successfully']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }
    
    public function releaseHold(Request $request)
    {
        try {
            if ($request->has('transaction_id')) {
                // Specific transaction manual release
                $txn = \App\Models\SellerFinance::find($request->transaction_id);
                if ($txn && $txn->status === 'pending') {
                    $txn->update(['status' => 'released', 'release_date' => now()]);
                }
            } else {
                // Bulk / seller level fallback
                $request->validate(['seller_id' => 'required|exists:sellers,id']);
                
                $seller = Seller::find($request->seller_id);
                $seller->settlement_hold = false;
                $seller->settlement_hold_reason = null;
                $seller->settlement_hold_date = null;
                $seller->save();
                
                // Update finance records
                SellerFinance::where('seller_id', $seller->id)
                    ->where('transaction_type', 'earning')
                    ->where('status', 'pending')
                    ->update(['status' => 'released', 'release_date' => now()]);
            }
            
            return response()->json(['success' => true, 'message' => 'Hold released successfully']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }
    
    public function updateSettlementRules(Request $request)
    {
        try {
            GlobalSetting::set('hold_days', $request->hold_days ?? 7);
            GlobalSetting::set('release_type', $request->release_type ?? 'auto');
            GlobalSetting::set('return_threshold', $request->return_threshold ?? 20);
            GlobalSetting::set('new_seller_hold_days', $request->new_seller_hold_days ?? 30);
            
            return response()->json(['success' => true, 'message' => 'Settlement rules updated']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }
    
    public function updateTaxRules(Request $request)
{
    try {
        // Update GST rate and other global settings as before
        if ($request->has('gst_rate')) {
            GlobalSetting::set('gst_rate', $request->gst_rate);
        }
        if ($request->has('currencies_list')) {
            GlobalSetting::set('currencies_list', json_encode($request->currencies_list));
        }
        if ($request->has('invoice_template')) {
            GlobalSetting::set('invoice_template_settings', json_encode($request->invoice_template));
        }
        // Persist tax regions into the taxes table instead of JSON settings
        if ($request->has('tax_regions')) {
            $taxRegions = $request->tax_regions; // Expected format: [{name, rate, region, type}, ...]
            // Clear existing taxes
            Tax::truncate();
            foreach ($taxRegions as $region) {
                Tax::create([
                    'name' => $region['tax_name'] ?? $region['name'] ?? 'Tax',
                    'rate' => $region['rate'] ?? 0,
                    'region' => $region['region'] ?? null,
                    'type' => $region['type'] ?? 'gst',
                ]);
            }
        }
        return response()->json(['success' => true, 'message' => 'Tax, currency and template settings updated successfully']);
    } catch (\Exception $e) {
        return response()->json(['success' => false, 'message' => $e->getMessage()]);
    }
}

    // ---------------------------
    // Tax CRUD endpoints
    // ---------------------------
    public function taxIndex()
    {
        $taxes = Tax::all();
        return view('admin.finance.tax', compact('taxes'));
    }

    public function storeTax(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'rate' => 'required|numeric',
            'region' => 'nullable|string',
            'type' => 'required|string',
        ]);
        Tax::create($validated);
        return redirect()->back()->with('success', 'Tax created successfully');
    }

    public function updateTax(Request $request, $id)
    {
        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'rate' => 'sometimes|required|numeric',
            'region' => 'sometimes|nullable|string',
            'type' => 'sometimes|required|string',
        ]);
        $tax = Tax::findOrFail($id);
        $tax->update($validated);
        return redirect()->back()->with('success', 'Tax updated successfully');
    }

    public function deleteTax($id)
    {
        Tax::destroy($id);
        return redirect()->back()->with('success', 'Tax deleted successfully');
    }

    // ---------------------------
    // Invoice CRUD endpoints
    // ---------------------------
    public function invoiceIndex()
    {
        $invoices = Invoice::all();
        return view('admin.finance.invoice', compact('invoices'));
    }

    public function storeInvoice(Request $request)
    {
        $validated = $request->validate([
            'prefix' => 'required|string|max:20',
            'style' => 'required|string|max:50',
            'company_name' => 'required|string|max:255',
            'company_address' => 'required|string',
            'company_email' => 'required|email',
            'company_phone' => 'required|string',
            'company_gst' => 'nullable|string',
            'footer' => 'nullable|array',
            'format' => 'nullable|array',
        ]);
        Invoice::create($validated);
        return redirect()->back()->with('success', 'Invoice template saved');
    }

    public function updateInvoice(Request $request, $id)
    {
        $validated = $request->validate([
            'prefix' => 'sometimes|required|string|max:20',
            'style' => 'sometimes|required|string|max:50',
            'company_name' => 'sometimes|required|string|max:255',
            'company_address' => 'sometimes|required|string',
            'company_email' => 'sometimes|required|email',
            'company_phone' => 'sometimes|required|string',
            'company_gst' => 'sometimes|nullable|string',
            'footer' => 'sometimes|nullable|array',
            'format' => 'sometimes|nullable|array',
        ]);
        $invoice = Invoice::findOrFail($id);
        $invoice->update($validated);
        return redirect()->back()->with('success', 'Invoice template updated');
    }

    public function deleteInvoice($id)
    {
        Invoice::destroy($id);
        return redirect()->back()->with('success', 'Invoice template deleted');
    }
    
    public function updateDisputeRules(Request $request)
    {
        try {
            if ($request->has('auto_escalation_rules')) {
                GlobalSetting::set('auto_escalation_rules', json_encode($request->auto_escalation_rules));
            }
            return response()->json(['success' => true, 'message' => 'Dispute auto escalation rules saved successfully']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }
    
    public function updateRefundRules(Request $request)
    {
        try {
            GlobalSetting::set('refund_mode', $request->refund_mode ?? 'auto');
            GlobalSetting::set('partial_refund_enabled', $request->partial_refund_enabled ? 1 : 0);
            GlobalSetting::set('max_partial_percent', $request->max_partial_percent ?? 50);
            GlobalSetting::set('refund_sla_days', $request->refund_sla_days ?? 7);
            GlobalSetting::set('fraud_detection', $request->fraud_detection ?? 'advanced');
            GlobalSetting::set('rating_impact', $request->rating_impact ?? 'yes');
            
            GlobalSetting::set('reverse_commission', $request->reverse_commission ? 1 : 0);
            GlobalSetting::set('freeze_payout', $request->freeze_payout ? 1 : 0);
            GlobalSetting::set('freeze_duration', $request->freeze_duration ?? 14);
            GlobalSetting::set('freeze_action', $request->freeze_action ?? 'hold');
            GlobalSetting::set('reverse_referral', $request->reverse_referral ? 1 : 0);
            GlobalSetting::set('refund_lead_charges', $request->refund_lead_charges ? 1 : 0);
            GlobalSetting::set('lead_fraud_threshold', $request->lead_fraud_threshold ?? 3);
            GlobalSetting::set('lead_refund_type', $request->lead_refund_type ?? 'partial');
            
            return response()->json(['success' => true, 'message' => 'Refund rules and settings updated successfully']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    public function processRefund(Request $request)
    {
        try {
            $request->validate([
                'order_id' => 'required|exists:orders,id',
                'refund_type' => 'required|in:full,partial',
                'refund_amount' => 'required|numeric|min:0',
                'refund_reason' => 'required|string',
            ]);
            
            $order = Order::findOrFail($request->order_id);
            
            if ($request->refund_type == 'full') {
                $amount = $order->total_amount;
            } else {
                $amount = $request->refund_amount;
            }
            
            if ($amount > $order->total_amount) {
                return response()->json(['success' => false, 'message' => 'Refund amount cannot exceed order total']);
            }
            
            DB::beginTransaction();
            
            $order->payment_status = 'refunded';
            $order->refund_status = 'completed';
            $order->refund_amount = $amount;
            $order->refund_reason = $request->refund_reason;
            $order->refunded_at = now();
            $order->refund_completed_at = now();
            $order->save();
            
            $sellerId = $order->seller_id ?: 1;
            $currentBalance = SellerFinance::where('seller_id', $sellerId)->orderBy('created_at', 'desc')->value('balance') ?? 0;
            
            // Reverse Commission on Refund Rule
            if (GlobalSetting::get('reverse_commission', 1)) {
                $commission = SellerCommission::where('order_id', $order->id)->first();
                if ($commission) {
                    $currentBalance -= $commission->commission_amount;
                    SellerFinance::create([
                        'seller_id' => $sellerId,
                        'order_id' => $order->id,
                        'transaction_type' => 'commission_reversal',
                        'credit' => 0,
                        'debit' => $commission->commission_amount,
                        'balance' => $currentBalance,
                        'status' => 'completed',
                        'note' => 'Commission reversal for refunded order #' . $order->id,
                        'created_at' => now(),
                    ]);
                }
            }
            
            // Reverse Referral Rewards Rule
            if (GlobalSetting::get('reverse_referral', 1)) {
                $referralAmount = $amount * 0.006;
                $currentBalance -= $referralAmount;
                SellerFinance::create([
                    'seller_id' => $sellerId,
                    'order_id' => $order->id,
                    'transaction_type' => 'referral_reversal',
                    'credit' => 0,
                    'debit' => $referralAmount,
                    'balance' => $currentBalance,
                    'status' => 'completed',
                    'note' => 'Referral reward reversal for refunded order #' . $order->id,
                    'created_at' => now(),
                ]);
            }
            
            // Refund Lead Charges Rule for fake leads (if marked as fake/duplicate)
            if (GlobalSetting::get('refund_lead_charges', 1) && str_contains(strtolower($request->refund_reason), 'fake')) {
                $leadFee = SellerFinance::where('seller_id', $sellerId)
                    ->where('order_id', $order->id)
                    ->where('transaction_type', 'lead_fee')
                    ->first();
                    
                if ($leadFee) {
                    $leadRefundType = GlobalSetting::get('lead_refund_type', 'partial');
                    $refundLeadAmt = $leadRefundType == 'full' ? $leadFee->credit : ($leadFee->credit * 0.5);
                    $currentBalance += $refundLeadAmt;
                    
                    SellerFinance::create([
                        'seller_id' => $sellerId,
                        'order_id' => $order->id,
                        'transaction_type' => 'lead_fee_refund',
                        'credit' => $refundLeadAmt,
                        'debit' => 0,
                        'balance' => $currentBalance,
                        'status' => 'completed',
                        'note' => 'Lead fee refund (' . $leadRefundType . ') for fake lead order #' . $order->id,
                        'created_at' => now(),
                    ]);
                }
            }
            
            // General Refund Transaction entry
            $currentBalance -= $amount;
            SellerFinance::create([
                'seller_id' => $sellerId,
                'order_id' => $order->id,
                'transaction_type' => 'refund',
                'credit' => 0,
                'debit' => $amount,
                'balance' => $currentBalance,
                'status' => 'completed',
                'note' => 'Refund processed: ' . $request->refund_reason,
                'created_at' => now(),
            ]);
            
            DB::commit();
            
            return response()->json([
                'success' => true,
                'message' => 'Refund processed successfully. Amount: ' . number_format($amount, 2)
            ]);
            
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }

  
  public function approveRefund(Request $request)
{
    try {
        $request->validate([
            'order_id' => 'required|exists:orders,id'
        ]);
        
        $order = Order::findOrFail($request->order_id);
        
        DB::beginTransaction();
        
        // Update order status
        $order->payment_status = 'refunded';
        $order->refund_status = 'completed';
        $order->refund_completed_at = now();
        $order->save();
        
        $sellerId = $order->seller_id ?: 1;
        $amount = $order->refund_amount ?: $order->total_amount;
        $currentBalance = SellerFinance::where('seller_id', $sellerId)->orderBy('created_at', 'desc')->value('balance') ?? 0;
        
        // Reverse Commission on Refund Rule
        if (GlobalSetting::get('reverse_commission', 1)) {
            $commission = SellerCommission::where('order_id', $order->id)->first();
            if ($commission) {
                $currentBalance -= $commission->commission_amount;
                SellerFinance::create([
                    'seller_id' => $sellerId,
                    'order_id' => $order->id,
                    'transaction_type' => 'commission_reversal',
                    'credit' => 0,
                    'debit' => $commission->commission_amount,
                    'balance' => $currentBalance,
                    'status' => 'success',  // Changed from 'completed'
                    'note' => 'Commission reversal for refunded order #' . $order->id,
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
            }
        }
        
        // Refund Transaction entry
        $currentBalance -= $amount;
        SellerFinance::create([
            'seller_id' => $sellerId,
            'order_id' => $order->id,
            'transaction_type' => 'refund',  // Changed from 'refund' to proper value
            'credit' => 0,
            'debit' => $amount,
            'balance' => $currentBalance,
            'status' => 'success',  // Changed from 'completed' to 'success'
            'note' => 'Refund approved by admin: ' . ($order->refund_reason ?? 'Customer request'),
            'created_at' => now(),
            'updated_at' => now()
        ]);
        
        DB::commit();
        return response()->json(['success' => true, 'message' => 'Refund request approved successfully']);
        
    } catch (\Exception $e) {
        DB::rollBack();
        return response()->json(['success' => false, 'message' => $e->getMessage()]);
    }
}

    public function rejectRefund(Request $request)
    {
        try {
            $request->validate([
                'order_id' => 'required|exists:orders,id',
            ]);
            
            $order = Order::findOrFail($request->order_id);
            $order->refund_status = 'failed';
            $order->save();
            
            return response()->json(['success' => true, 'message' => 'Refund request rejected']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    public function uploadDisputeEvidence(Request $request)
    {
        try {
            $request->validate([
                'dispute_id' => 'required|exists:grievances,id',
                'evidence_file' => 'required|file|max:10240',
            ]);
            
            $dispute = Grievance::findOrFail($request->dispute_id);
            
            if ($request->hasFile('evidence_file')) {
                $file = $request->file('evidence_file');
                $fileName = 'dispute_' . $dispute->id . '_' . time() . '.' . $file->getClientOriginalExtension();
                $path = $file->storeAs('uploads/evidence', $fileName, 'public');
                
                \App\Models\GrievanceMessage::create([
                    'grievance_id' => $dispute->id,
                    'sender' => 'admin',
                    'message' => 'Uploaded evidence document: ' . asset('storage/' . $path)
                ]);
                
                return response()->json([
                    'success' => true,
                    'message' => 'Evidence document uploaded successfully',
                    'file_url' => asset('storage/' . $path)
                ]);
            }
            
            return response()->json(['success' => false, 'message' => 'No file uploaded']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }
    
    public function updateLeadRules(Request $request)
    {
        try {
            GlobalSetting::set('ppc_rate', $request->ppc_rate ?? 0.50);
            GlobalSetting::set('ppl_rate', $request->ppl_rate ?? 5.00);
            GlobalSetting::set('free_lead_limit', $request->free_lead_limit ?? 100);
            GlobalSetting::set('paid_lead_threshold', $request->paid_lead_threshold ?? 0.50);
            
            return response()->json(['success' => true, 'message' => 'Lead monetization rules updated']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }
    
    /**
     * Update Lead Refund Rules - Fraud Detection & Auto-Refund Settings
     * Handles: Duplicate clicks, IP-based fraud, fake enquiry detection,
     * bot pattern detection, refund window, and auto-approve thresholds.
     */
    public function updateLeadRefundRules(Request $request)
    {
        try {
            // Duplicate Click Detection Settings
            GlobalSetting::set('duplicate_click_threshold', $request->duplicate_click_threshold ?? 5);
            GlobalSetting::set('max_clicks_per_interval', $request->max_clicks_per_interval ?? 3);
            GlobalSetting::set('duplicate_click_window_seconds', $request->duplicate_click_window_seconds ?? 5);
            
            // IP-Based Fraud Detection Settings
            GlobalSetting::set('same_ip_click_limit', $request->same_ip_click_limit ?? 20);
            GlobalSetting::set('ip_click_window_minutes', $request->ip_click_window_minutes ?? 60);
            GlobalSetting::set('ip_block_action', $request->ip_block_action ?? 'auto_refund');
            
            // Fake/Invalid Enquiry Detection
            GlobalSetting::set('enable_fake_enquiry_check', $request->has('enable_fake_enquiry_check') ? 1 : 0);
            GlobalSetting::set('block_temp_emails', $request->has('block_temp_emails') ? 1 : 0);
            GlobalSetting::set('validate_phone_format', $request->has('validate_phone_format') ? 1 : 0);
            GlobalSetting::set('block_gibberish_names', $request->has('block_gibberish_names') ? 1 : 0);
            
            // Bot/Automated Pattern Detection
            GlobalSetting::set('enable_bot_detection', $request->has('enable_bot_detection') ? 1 : 0);
            GlobalSetting::set('bot_click_speed_ms', $request->bot_click_speed_ms ?? 100);
            GlobalSetting::set('bot_pattern_threshold', $request->bot_pattern_threshold ?? 5);
            GlobalSetting::set('bot_action', $request->bot_action ?? 'auto_refund');
            
            // Refund Window & Auto-Approve Settings
            GlobalSetting::set('lead_refund_window_days', $request->lead_refund_window_days ?? 7);
            GlobalSetting::set('lead_auto_approve_amount', $request->lead_auto_approve_amount ?? 5.00);
            GlobalSetting::set('lead_refund_notify_seller', $request->has('lead_refund_notify_seller') ? 1 : 0);
            GlobalSetting::set('lead_refund_auto_process', $request->has('lead_refund_auto_process') ? 1 : 0);
            
            return response()->json([
                'success' => true, 
                'message' => 'Lead refund fraud detection rules updated successfully!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false, 
                'message' => 'Error: ' . $e->getMessage()
            ]);
        }
    }
    
    public function exportTransactions(Request $request)
    {
        $transactions = SellerFinance::with(['seller', 'order'])->orderBy('created_at', 'desc')->get();
        
        $filename = 'transactions_' . date('Y-m-d') . '.csv';
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        
        $handle = fopen('php://output', 'w');
        fputcsv($handle, ['Date', 'Order ID', 'Seller', 'Transaction Type', 'Credit', 'Debit', 'Status', 'Note']);
        
        foreach ($transactions as $transaction) {
            fputcsv($handle, [
                $transaction->created_at,
                $transaction->order_id,
                $transaction->seller->name ?? 'N/A',
                $transaction->transaction_type,
                $transaction->credit,
                $transaction->debit,
                $transaction->status,
                $transaction->note
            ]);
        }
        
        fclose($handle);
        exit;
    }
    
    // Add these methods to your AdminFinanceController.php

public function rejectPayout(Request $request)
{
    try {
        $payout = SellerFinance::find($request->payout_id);
        if ($payout) {
            $payout->status = 'rejected';
            $payout->note = $request->note ?? 'Payout rejected by admin';
            $payout->save();
            return response()->json(['success' => true, 'message' => 'Payout rejected']);
        }
        return response()->json(['success' => false, 'message' => 'Payout not found']);
    } catch (\Exception $e) {
        return response()->json(['success' => false, 'message' => $e->getMessage()]);
    }
}

public function schedulePayouts(Request $request)
{
    try {
        SellerFinance::whereIn('id', $request->payout_ids)
            ->where('transaction_type', 'payout')
            ->update([
                'scheduled_date' => $request->scheduled_date,
                'status' => 'scheduled'
            ]);
        GlobalSetting::set('next_payout_date', $request->scheduled_date);
        return response()->json(['success' => true, 'message' => 'Payouts scheduled successfully']);
    } catch (\Exception $e) {
        return response()->json(['success' => false, 'message' => $e->getMessage()]);
    }
}


    
    public function changeCurrency($currency)
    {
        if (in_array($currency, ['USD', 'INR', 'EUR', 'GBP'])) {
            session(['currency' => $currency]);
        }
        return redirect()->back();
    }
    
    private function getExchangeRate($currency)
    {
        $rates = ['USD' => 1, 'INR' => 83.5, 'EUR' => 0.92, 'GBP' => 0.79];
        return $rates[$currency] ?? 1;
    }
    
        
    // In your AdminController.php
public function saveAppPricing(Request $request)
{
    try {
        $pricingData = $request->input('pricing', []);
        
        // Save or update pricing rules
        foreach($pricingData as $data) {
            // Your save logic here
            // Example:
            AppPricingRule::updateOrCreate(
                [
                    'app' => $data['app'],
                    'category_id' => $data['category_id'],
                    'seller_id' => $data['seller_id']
                ],
                [
                    'click_price' => $data['click_price'],
                    'enquiry_price' => $data['enquiry_price']
                ]
            );
        }
        
        return response()->json(['success' => true, 'message' => 'Pricing saved successfully']);
        
    } catch (\Exception $e) {
        return response()->json(['success' => false, 'message' => $e->getMessage()]);
    }
}

    public function bulkUpdatePayouts(Request $request)
    {
        try {
            $request->validate([
                'seller_ids' => 'required|array',
                'seller_ids.*' => 'exists:sellers,id',
                'updates' => 'required|array',
                'updates.hold_days' => 'nullable|integer|min:0',
                'updates.payout_schedule' => 'nullable|in:Daily,Weekly,Bi-Weekly,Monthly',
                'updates.purchase_date' => 'nullable|date',
                'updates.release_date' => 'nullable|date',
            ]);

            $sellerIds = $request->seller_ids;
            $updates = $request->updates;
            
            $updatedFields = [];
            $updateData = [];
            
            // Prepare update data
            if (isset($updates['hold_days'])) {
                $updateData['hold_days'] = $updates['hold_days'];
                $updatedFields[] = "Hold Days: {$updates['hold_days']}";
            }
            
            if (isset($updates['payout_schedule'])) {
                $updateData['payout_schedule'] = $updates['payout_schedule'];
                $updatedFields[] = "Payout Schedule: {$updates['payout_schedule']}";
            }
            
            if (isset($updates['purchase_date'])) {
                $updateData['purchase_date'] = $updates['purchase_date'];
                $updatedFields[] = "Purchase Date: {$updates['purchase_date']}";
            }
            
            if (isset($updates['release_date'])) {
                $updateData['release_date'] = $updates['release_date'];
                $updatedFields[] = "Release Date: {$updates['release_date']}";
            }
            
            if (empty($updateData)) {
                return response()->json([
                    'success' => false,
                    'message' => 'No valid fields to update'
                ], 422);
            }
            
            $updateData['updated_at'] = now();
            
            // Perform bulk update
            $updatedCount = DB::table('seller_payouts')
                ->whereIn('seller_id', $sellerIds)
                ->update($updateData);
            
            // Also update the earnings details table if needed
            DB::table('earnings_details')
                ->whereIn('seller_id', $sellerIds)
                ->update($updateData);
            
            return response()->json([
                'success' => true,
                'message' => "Successfully updated {$updatedCount} sellers",
                'updated_fields' => $updatedFields,
                'updated_count' => $updatedCount,
                'seller_ids' => $sellerIds
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error updating payouts: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getCommissionRates(Request $request)
    {
        $record = DB::table('commission_management')
            ->where('seller_id', $request->seller_id)
            ->where('app_type', $request->app_type)
            ->where('sell_type', $request->sell_type)
            ->first();
        
        // Fetch actual total sales for this seller from orders
        $totalSales = SellerCommission::where('seller_id', $request->seller_id)->sum('order_amount');
        if ($totalSales == 0) {
            $totalSales = Order::where('seller_id', $request->seller_id)
                ->where('payment_status', 'paid')
                ->sum('total_amount');
        }
            
        if ($record) {
            return response()->json([
                'seller_rate' => $record->seller_commission,
                'referrer_rate' => $record->referrer_commission,
                'platform_rate' => $record->platform_commission,
                'total_sales' => $totalSales,
            ]);
        }
        
        return response()->json([
            'seller_rate' => 20,
            'referrer_rate' => 10,
            'platform_rate' => 70,
            'total_sales' => $totalSales,
        ]);
    }

    public function saveCommissions(Request $request)
    {
        try {
            DB::beginTransaction();
            
            DB::table('commission_management')->delete();
            
            $commissions = $request->input('commissions', []);
            foreach ($commissions as $comm) {
                DB::table('commission_management')->insert([
                    'seller_id' => $comm['seller_id'],
                    'app_type' => $comm['app_type'] ?? 'vshop',
                    'sell_type' => $comm['sell_type'] ?? 'direct',
                    'total_sales' => $comm['total_sales'] ?? 0,
                    'commission_type' => $comm['commission_type'] ?? 'percentage',
                    'seller_commission' => $comm['seller_commission'] ?? 20,
                    'referrer_commission' => $comm['referrer_commission'] ?? 10,
                    'platform_commission' => $comm['platform_commission'] ?? 70,
                    'referral_id' => !empty($comm['referral_id']) ? $comm['referral_id'] : null,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
            
            DB::commit();
            return response()->json(['success' => true, 'message' => 'Commissions structure saved successfully']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    public function addNewSeller(Request $request)
    {
        try {
            $request->validate([
                'name' => 'required|string|max:255',
                'application' => 'required|string|max:255',
            ]);

            $seller = new Seller();
            $seller->name = $request->name;
            $seller->application = $request->application;
            
            $email = strtolower(\Illuminate\Support\Str::slug($request->name)) . '@vshop.com';
            if (Seller::where('email', $email)->exists()) {
                $email = strtolower(\Illuminate\Support\Str::slug($request->name)) . '_' . rand(100, 999) . '@vshop.com';
            }
            $seller->email = $email;
            $seller->password = bcrypt('123456');
            $seller->status = 'approved';
            $seller->is_verified = true;
            $seller->save();

            return response()->json([
                'success' => true,
                'message' => 'Seller added successfully',
                'seller' => $seller
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }
    
    
    
   
    
    
    
    
    
    
     public function getGlobalRules()
{
    try {
        $rules = GlobalRule::with(['seller', 'category', 'product'])->orderBy('created_at', 'desc')->get();
        
        $exchangeRates = [
            'INR' => 1,
            'USD' => 83.5,
            'EUR' => 91.2,
            'GBP' => 104.5
        ];
        
        $formattedRules = $rules->map(function($rule) use ($exchangeRates) {
            $convertedInr = null;
            if ($rule->currency !== 'INR') {
                $rate = $exchangeRates[$rule->currency] ?? 83.5;
                $convertedInr = round($rule->amount * $rate);
            }
            
            return [
                'id' => $rule->id,
                'seller_id' => $rule->seller_id,
                'seller_name' => $rule->seller ? $rule->seller->name : null,
                'app' => $rule->app,
                'sell_type' => $rule->sell_type,
                'category_id' => $rule->category_id,
                'category_name' => $rule->category ? $rule->category->name : null,
                'product_id' => $rule->product_id,
                'product_name' => $rule->product ? $rule->product->name : null,
                'currency' => $rule->currency,
                'amount' => (float)$rule->amount,
                'converted_inr' => $convertedInr,
                'commission' => (float)$rule->commission,
                'discount' => $rule->discount,
                'min_margin' => $rule->min_margin ? (float)$rule->min_margin : null,
                'boost' => (bool)$rule->boost,
                'effective_date' => $rule->effective_date,
                'created_at' => $rule->created_at ? $rule->created_at->toDateString() : null
            ];
        });
        
        return response()->json([
            'success' => true,
            'rules' => $formattedRules,
            'total' => $rules->count()
        ]);
        
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Failed to fetch rules: ' . $e->getMessage()
        ], 500);
    }
}

    /**
     * Get a single global rule by ID
     * Route: GET /finance/get-rule?id={ruleId}
     */
    public function getRule(Request $request)
    {
        try {
            $ruleId = $request->query('id');
            
            if (!$ruleId) {
                return response()->json([
                    'success' => false,
                    'message' => 'Rule ID is required'
                ], 400);
            }
            
            $rule = GlobalRule::with(['seller', 'category', 'product'])->find($ruleId);
            
            if (!$rule) {
                return response()->json([
                    'success' => false,
                    'message' => 'Rule not found'
                ], 404);
            }
            
            $exchangeRates = [
                'INR' => 1,
                'USD' => 83.5,
                'EUR' => 91.2,
                'GBP' => 104.5
            ];
            
            $convertedInr = null;
            if ($rule->currency !== 'INR') {
                $rate = $exchangeRates[$rule->currency] ?? 83.5;
                $convertedInr = round($rule->amount * $rate);
            }
            
            return response()->json([
                'success' => true,
                'rule' => [
                    'id' => $rule->id,
                    'seller_id' => $rule->seller_id,
                    'seller_name' => $rule->seller ? $rule->seller->name : null,
                    'app' => $rule->app,
                    'sell_type' => $rule->sell_type,
                    'category_id' => $rule->category_id,
                    'category_name' => $rule->category ? $rule->category->name : null,
                    'product_id' => $rule->product_id,
                    'product_name' => $rule->product ? $rule->product->name : null,
                    'currency' => $rule->currency,
                    'amount' => (float)$rule->amount,
                    'converted_inr' => $convertedInr,
                    'commission' => (float)$rule->commission,
                    'discount' => $rule->discount,
                    'min_margin' => $rule->min_margin ? (float)$rule->min_margin : null,
                    'boost' => (bool)$rule->boost,
                    'effective_date' => $rule->effective_date,
                    'created_at' => $rule->created_at ? $rule->created_at->toDateString() : null
                ]
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch rule: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete a global rule
     * Route: POST /finance/delete-rule
     */
    public function deleteRule(Request $request)
    {
        try {
            $ruleId = $request->input('id');
            
            if (!$ruleId) {
                return response()->json([
                    'success' => false,
                    'message' => 'Rule ID is required'
                ], 400);
            }
            
            $rule = GlobalRule::find($ruleId);
            
            if (!$rule) {
                return response()->json([
                    'success' => false,
                    'message' => 'Rule not found'
                ], 404);
            }
            
            // Store rule data before deleting (for logging/history)
            $deletedRuleData = [
                'id' => $rule->id,
                'seller_id' => $rule->seller_id,
                'app' => $rule->app,
                'sell_type' => $rule->sell_type,
                'category_id' => $rule->category_id,
                'product_id' => $rule->product_id,
                'currency' => $rule->currency,
                'amount' => $rule->amount,
                'commission' => $rule->commission,
                'discount' => $rule->discount,
                'min_margin' => $rule->min_margin,
                'boost' => $rule->boost,
                'effective_date' => $rule->effective_date
            ];
            
            $rule->delete();
            
            return response()->json([
                'success' => true,
                'message' => 'Rule deleted successfully',
                'deleted_rule' => $deletedRuleData
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete rule: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update/Create global rules (for saving multiple rules)
     * This is your existing update-global-rules route
     */
    public function updateGlobalRules(Request $request)
{
    try {
        \Log::info('updateGlobalRules called', $request->all());
        
        // Get rules from request
        $rules = [];
        
        // Case 1: Direct 'rules' array in request
        if ($request->has('rules')) {
            $rules = $request->input('rules');
        } 
        // Case 2: FormData with indexed array
        else {
            foreach ($request->all() as $key => $value) {
                if (strpos($key, 'rules[') === 0) {
                    preg_match('/rules\[(\d+)\]\[(.*?)\]/', $key, $matches);
                    if (count($matches) === 3) {
                        $index = (int)$matches[1];
                        $field = $matches[2];
                        if (!isset($rules[$index])) {
                            $rules[$index] = [];
                        }
                        $rules[$index][$field] = $value;
                    }
                }
            }
            $rules = array_values($rules);
        }
        
        if (empty($rules)) {
            return response()->json([
                'success' => false,
                'message' => 'No rules data provided'
            ], 400);
        }
        
        $createdCount = 0;
        $updatedCount = 0;
        
        foreach ($rules as $ruleData) {
            // Skip if required fields are missing
            if (empty($ruleData['seller_id']) || empty($ruleData['category_id']) || 
                empty($ruleData['product_id']) || empty($ruleData['amount']) || 
                empty($ruleData['commission'])) {
                continue;
            }
            
            // Prepare data for save
            $saveData = [
                'seller_id' => $ruleData['seller_id'],
                'app' => $ruleData['app'] ?? 'vshop',
                'sell_type' => $ruleData['sell_type'] ?? 'direct',
                'category_id' => $ruleData['category_id'],
                'product_id' => $ruleData['product_id'],
                'currency' => $ruleData['currency'] ?? 'USD',
                'amount' => $ruleData['amount'],
                'commission' => $ruleData['commission'],
                'discount' => $ruleData['discount'] ?? null,
                'min_margin' => $ruleData['min_margin'] ?? null,
                'boost' => isset($ruleData['boost']) ? (int)$ruleData['boost'] : 0,
                'effective_date' => $ruleData['effective_date'] ?? date('Y-m-d')
            ];
            
            // Check if rule exists for update
            if (isset($ruleData['id']) && !empty($ruleData['id'])) {
                $rule = GlobalRule::find($ruleData['id']);
                if ($rule) {
                    $rule->update($saveData);
                    $updatedCount++;
                    continue;
                }
            }
            
            // Create new rule
            GlobalRule::create($saveData);
            $createdCount++;
        }
        
        $message = [];
        if ($createdCount > 0) $message[] = "{$createdCount} rules created";
        if ($updatedCount > 0) $message[] = "{$updatedCount} rules updated";
        $messageText = !empty($message) ? implode(', ', $message) . ' successfully' : 'No rules were saved';
        
        return response()->json([
            'success' => true,
            'message' => $messageText,
            'created' => $createdCount,
            'updated' => $updatedCount
        ]);
        
    } catch (\Exception $e) {
        \Log::error('updateGlobalRules Error: ' . $e->getMessage());
        \Log::error($e->getTraceAsString());
        
        return response()->json([
            'success' => false,
            'message' => 'Error: ' . $e->getMessage()
        ], 500);
    }
}




    /**
     * Get all sellers for the multi-select dropdown
     * Route: GET /admin/finance/get-all-sellers
     */
 /**
 * Get all sellers for the multi-select dropdown - NO CONDITIONS
 * Route: GET /admin/finance/get-all-sellers
 */
public function getAllSellers(Request $request)
{
    try {
        // Get ALL sellers - no conditions at all
        $sellers = Seller::orderBy('name', 'asc')->get()->unique('name');

        $formattedSellers = $sellers->map(function($seller) {
            return [
                'id' => $seller->id,
                'name' => $seller->name ?? $seller->shop_name ?? 'Seller #' . $seller->id,
                'email' => $seller->email,
                'phone' => $seller->phone,
                'shop_name' => $seller->shop_name,
                'application' => $seller->application ?? 'vshop',
                'status' => $seller->status
            ];
        })->values();

        return response()->json([
            'success' => true,
            'sellers' => $formattedSellers,
            'total' => $sellers->count()
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => $e->getMessage(),
            'sellers' => []
        ]);
    }
}
    /**
     * Get seller-specific daily caps from sellers table
     * Route: GET /admin/finance/get-seller-caps
     */
    public function getSellerCaps(Request $request)
    {
        try {
            $sellers = Seller::select('id', 'name', 'email', 'daily_cap', 'daily_cap_enabled', 'daily_cap_used', 'daily_cap_reset_at')
                ->where(function($query) {
                    $query->where('daily_cap', '>', 0)
                          ->orWhere('daily_cap_enabled', true);
                })
                ->orderBy('name', 'asc')
                ->get();

            $caps = $sellers->map(function($seller) {
                return [
                    'seller_id' => $seller->id,
                    'seller_name' => $seller->name ?? $seller->shop_name ?? 'Seller #' . $seller->id,
                    'seller_email' => $seller->email,
                    'daily_cap' => (float)($seller->daily_cap ?? 0),
                    'daily_cap_enabled' => (bool)($seller->daily_cap_enabled ?? true),
                    'daily_cap_used' => (float)($seller->daily_cap_used ?? 0),
                    'daily_cap_reset_at' => $seller->daily_cap_reset_at
                ];
            });

            return response()->json([
                'success' => true,
                'caps' => $caps,
                'total' => $caps->count()
            ]);
        } catch (\Exception $e) {
            \Log::error('Error in getSellerCaps: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch seller caps: ' . $e->getMessage(),
                'caps' => []
            ], 500);
        }
    }

    /**
     * Add or update seller-specific daily caps in sellers table
     * Route: POST /admin/finance/add-seller-cap
     */
    public function addSellerCap(Request $request)
    {
        try {
            $request->validate([
                'seller_ids' => 'required|array',
                'seller_ids.*' => 'exists:sellers,id',
                'daily_cap' => 'required|numeric|min:0'
            ]);

            $sellerIds = $request->seller_ids;
            $dailyCap = $request->daily_cap;
            $updatedCount = 0;

            foreach ($sellerIds as $sellerId) {
                $seller = Seller::find($sellerId);
                if ($seller) {
                    $seller->daily_cap = $dailyCap;
                    $seller->daily_cap_enabled = $dailyCap > 0;
                    $seller->save();
                    $updatedCount++;
                }
            }

            return response()->json([
                'success' => true,
                'message' => "Daily cap of ₹{$dailyCap} added for {$updatedCount} seller(s) successfully",
                'updated_count' => $updatedCount
            ]);
        } catch (\Exception $e) {
            \Log::error('Error in addSellerCap: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to add seller cap: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove seller-specific daily cap (set to 0)
     * Route: POST /admin/finance/remove-seller-cap
     */
    public function removeSellerCap(Request $request)
    {
        try {
            $request->validate([
                'seller_id' => 'required|exists:sellers,id'
            ]);

            $seller = Seller::find($request->seller_id);
            if ($seller) {
                $seller->daily_cap = 0;
                $seller->daily_cap_enabled = false;
                $seller->daily_cap_used = 0;
                $seller->save();
                
                return response()->json([
                    'success' => true,
                    'message' => 'Seller daily cap removed successfully'
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'Seller not found'
            ], 404);
        } catch (\Exception $e) {
            \Log::error('Error in removeSellerCap: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to remove seller cap: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get lead configuration (PPC, PPL, thresholds, etc.)
     * Route: GET /admin/finance/get-lead-config
     */
    public function getLeadConfig(Request $request)
    {
        try {
            $config = [
                'ppc_rate' => (float)GlobalSetting::get('ppc_rate', 0.50),
                'ppl_rate' => (float)GlobalSetting::get('ppl_rate', 5.00),
                'free_lead_limit' => (int)GlobalSetting::get('free_lead_limit', 100),
                'paid_lead_threshold' => (float)GlobalSetting::get('paid_lead_threshold', 0.50),
                'max_clicks_per_product' => (int)GlobalSetting::get('max_clicks_per_product', 50),
                'daily_cap_seller' => (float)GlobalSetting::get('daily_cap_seller', 1000),
                
                // Fraud detection settings
                'duplicate_click_threshold' => (int)GlobalSetting::get('duplicate_click_threshold', 3),
                'max_clicks_per_interval' => (int)GlobalSetting::get('max_clicks_per_interval', 3),
                'duplicate_click_window_seconds' => (int)GlobalSetting::get('duplicate_click_window_seconds', 5),
                'same_ip_click_limit' => (int)GlobalSetting::get('same_ip_click_limit', 20),
                'ip_click_window_minutes' => (int)GlobalSetting::get('ip_click_window_minutes', 60),
                'enable_fake_enquiry_check' => (bool)GlobalSetting::get('enable_fake_enquiry_check', true),
                'enable_duplicate_click_rule' => (bool)GlobalSetting::get('enable_duplicate_click_rule', true),
                'enable_ip_limit_rule' => (bool)GlobalSetting::get('enable_ip_limit_rule', true),
                'enable_invalid_details_rule' => (bool)GlobalSetting::get('enable_invalid_details_rule', true),
                'enable_enquiry_limit_rule' => (bool)GlobalSetting::get('enable_enquiry_limit_rule', true),
                'enable_blacklist_rule' => (bool)GlobalSetting::get('enable_blacklist_rule', true),
                
                // Bot detection
                'enable_bot_detection' => (bool)GlobalSetting::get('enable_bot_detection', true),
                'bot_click_speed_ms' => (int)GlobalSetting::get('bot_click_speed_ms', 100),
                'bot_pattern_threshold' => (int)GlobalSetting::get('bot_pattern_threshold', 5),
                
                // Refund settings
                'lead_refund_window_days' => (int)GlobalSetting::get('lead_refund_window_days', 7),
                'lead_auto_approve_amount' => (float)GlobalSetting::get('lead_auto_approve_amount', 5.00),
                'lead_refund_notify_seller' => (bool)GlobalSetting::get('lead_refund_notify_seller', true),
                'lead_refund_auto_process' => (bool)GlobalSetting::get('lead_refund_auto_process', true)
            ];

            return response()->json([
                'success' => true,
                'config' => $config
            ]);
        } catch (\Exception $e) {
            \Log::error('Error in getLeadConfig: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch lead config: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getLeadRules()
    {
        try {
            $settings = [
                'ppc_rate' => (float)GlobalSetting::get('ppc_rate', 0.50),
                'ppl_rate' => (float)GlobalSetting::get('ppl_rate', 5.00),
                'free_lead_limit' => (int)GlobalSetting::get('free_lead_limit', 100),
                'paid_lead_threshold' => (float)GlobalSetting::get('paid_lead_threshold', 0.50),
                'daily_cap_seller' => (float)GlobalSetting::get('daily_cap_seller', 1000),
                'max_clicks_per_product' => (int)GlobalSetting::get('max_clicks_per_product', 50),
                'rule1_duplicate_clicks' => (bool)GlobalSetting::get('enable_duplicate_click_rule', true),
                'rule2_ip_limit' => (bool)GlobalSetting::get('enable_ip_limit_rule', true),
                'rule3_invalid_details' => (bool)GlobalSetting::get('enable_invalid_details_rule', true),
                'rule4_bot_pattern' => (bool)GlobalSetting::get('enable_bot_detection', true),
                'rule5_too_many_enquiries' => (bool)GlobalSetting::get('enable_enquiry_limit_rule', true),
                'rule6_blacklist' => (bool)GlobalSetting::get('enable_blacklist_rule', true),
            ];
            
            return response()->json([
                'success' => true,
                'settings' => $settings
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get revenue data for charts and metrics
     * Route: GET /admin/finance/get-revenue-data
     */
    public function getRevenueData(Request $request)
    {
        try {
            $exchangeRate = $this->getExchangeRate($request->get('currency', 'INR'));
            
            // Calculate revenue data
            $clickRevenue = SellerFinance::where('transaction_type', 'click')
                ->where('status', 'completed')
                ->sum('credit') * $exchangeRate;
                
            $enquiryRevenue = SellerFinance::where('transaction_type', 'enquiry')
                ->where('status', 'completed')
                ->sum('credit') * $exchangeRate;
                
            $leadRevenue = SellerFinance::where('transaction_type', 'lead_fee')
                ->where('status', 'completed')
                ->sum('credit') * $exchangeRate;
                
            // Free leads used (count of leads with zero charge)
            $freeLeadsUsed = SellerFinance::where('transaction_type', 'lead_fee')
                ->where('credit', 0)
                ->count();
                
            // Monthly revenue breakdown
            $monthlyRevenue = [];
            $months = [];
            for ($i = 5; $i >= 0; $i--) {
                $month = now()->subMonths($i);
                $monthName = $month->format('M');
                $months[] = $monthName;
                
                $monthlyClickRevenue = SellerFinance::where('transaction_type', 'click')
                    ->whereYear('created_at', $month->year)
                    ->whereMonth('created_at', $month->month)
                    ->sum('credit') * $exchangeRate;
                    
                $monthlyEnquiryRevenue = SellerFinance::where('transaction_type', 'enquiry')
                    ->whereYear('created_at', $month->year)
                    ->whereMonth('created_at', $month->month)
                    ->sum('credit') * $exchangeRate;
                    
                $monthlyRevenue[] = [
                    'month' => $monthName,
                    'click_revenue' => round($monthlyClickRevenue, 2),
                    'enquiry_revenue' => round($monthlyEnquiryRevenue, 2),
                    'total' => round($monthlyClickRevenue + $monthlyEnquiryRevenue, 2)
                ];
            }
            
            // Today's revenue
            $todayClickRevenue = SellerFinance::where('transaction_type', 'click')
                ->whereDate('created_at', today())
                ->sum('credit') * $exchangeRate;
                
            $todayEnquiryRevenue = SellerFinance::where('transaction_type', 'enquiry')
                ->whereDate('created_at', today())
                ->sum('credit') * $exchangeRate;
                
            // This month vs last month
            $thisMonth = SellerFinance::whereYear('created_at', now()->year)
                ->whereMonth('created_at', now()->month)
                ->whereIn('transaction_type', ['click', 'enquiry', 'lead_fee'])
                ->sum('credit') * $exchangeRate;
                
            $lastMonth = SellerFinance::whereYear('created_at', now()->subMonth()->year)
                ->whereMonth('created_at', now()->subMonth()->month)
                ->whereIn('transaction_type', ['click', 'enquiry', 'lead_fee'])
                ->sum('credit') * $exchangeRate;
                
            $monthlyGrowth = $lastMonth > 0 ? round((($thisMonth - $lastMonth) / $lastMonth) * 100, 2) : 0;
            
            return response()->json([
                'success' => true,
                'click_revenue' => round($clickRevenue, 2),
                'enquiry_revenue' => round($enquiryRevenue, 2),
                'lead_revenue' => round($leadRevenue, 2),
                'free_leads_used' => $freeLeadsUsed,
                'today_click_revenue' => round($todayClickRevenue, 2),
                'today_enquiry_revenue' => round($todayEnquiryRevenue, 2),
                'monthly_revenue' => $monthlyRevenue,
                'months' => $months,
                'this_month_total' => round($thisMonth, 2),
                'last_month_total' => round($lastMonth, 2),
                'monthly_growth' => $monthlyGrowth
            ]);
        } catch (\Exception $e) {
            \Log::error('Error in getRevenueData: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch revenue data: ' . $e->getMessage(),
                'click_revenue' => 0,
                'enquiry_revenue' => 0,
                'free_leads_used' => 0
            ], 500);
        }
    }

    /**
     * Get all fraud rules (for the modal)
     * Route: GET /admin/finance/fraud-rules
     */
    public function getFraudRules(Request $request)
    {
        try {
            $rules = [
                [
                    'id' => 1,
                    'rule_key' => 'duplicate_clicks',
                    'rule_name' => 'Duplicate Clicks Detection',
                    'trigger_condition' => '>3 clicks in 5 seconds (same customer)',
                    'description' => 'Prevents click fraud by detecting rapid repeated clicks from the same customer',
                    'action' => 'Auto-refund PPC charges and block further clicks for 1 hour',
                    'is_active' => (bool)GlobalSetting::get('enable_duplicate_click_rule', true),
                    'parameters' => [
                        'max_clicks' => (int)GlobalSetting::get('duplicate_click_threshold', 3),
                        'time_window_seconds' => (int)GlobalSetting::get('duplicate_click_window_seconds', 5),
                        'block_duration_minutes' => 60
                    ]
                ],
                [
                    'id' => 2,
                    'rule_key' => 'ip_limit',
                    'rule_name' => 'IP Rate Limiting',
                    'trigger_condition' => '>20 clicks/hour from same IP',
                    'description' => 'Limits the number of clicks from a single IP address to prevent automated attacks',
                    'action' => 'Auto-refund excess clicks and temporarily block IP',
                    'is_active' => (bool)GlobalSetting::get('enable_ip_limit_rule', true),
                    'parameters' => [
                        'max_clicks_per_hour' => (int)GlobalSetting::get('same_ip_click_limit', 20),
                        'block_duration_minutes' => (int)GlobalSetting::get('ip_block_duration', 30)
                    ]
                ],
                [
                    'id' => 3,
                    'rule_key' => 'invalid_details',
                    'rule_name' => 'Invalid Contact Details Detection',
                    'trigger_condition' => 'Invalid phone/email in enquiry',
                    'description' => 'Validates phone numbers and email addresses in enquiries',
                    'action' => 'Auto-refund PPL charges and mark enquiry as fraudulent',
                    'is_active' => (bool)GlobalSetting::get('enable_invalid_details_rule', true),
                    'parameters' => [
                        'validate_phone' => true,
                        'validate_email' => true,
                        'phone_min_length' => 10
                    ]
                ],
                [
                    'id' => 4,
                    'rule_key' => 'bot_pattern',
                    'rule_name' => 'Bot Pattern Recognition',
                    'trigger_condition' => 'Bot pattern (exact intervals between clicks)',
                    'description' => 'Identifies automated bot behavior based on click patterns and timing',
                    'action' => 'Auto-refund all charges and flag as bot activity',
                    'is_active' => (bool)GlobalSetting::get('enable_bot_detection', true),
                    'parameters' => [
                        'exact_interval_threshold_ms' => 50,
                        'min_sequence_length' => 5,
                        'confidence_threshold' => 0.8
                    ]
                ],
                [
                    'id' => 5,
                    'rule_key' => 'too_many_enquiries',
                    'rule_name' => 'Excessive Enquiries Limit',
                    'trigger_condition' => '>5 enquiries/hour per customer',
                    'description' => 'Prevents spam by limiting enquiries per customer',
                    'action' => 'Auto-refund excess enquiry charges and temporary block',
                    'is_active' => (bool)GlobalSetting::get('enable_enquiry_limit_rule', true),
                    'parameters' => [
                        'max_enquiries_per_hour' => 5,
                        'block_duration_minutes' => 60
                    ]
                ],
                [
                    'id' => 6,
                    'rule_key' => 'blacklist',
                    'rule_name' => 'Blacklisted Customers',
                    'trigger_condition' => 'Blacklisted customers or IP addresses',
                    'description' => 'Blocks known fraudulent customers and IP addresses',
                    'action' => 'Auto-refund all charges and prevent future interactions',
                    'is_active' => (bool)GlobalSetting::get('enable_blacklist_rule', true),
                    'parameters' => [
                        'auto_refund' => true,
                        'permanent_block' => true,
                        'notify_admin' => true
                    ]
                ]
            ];

            return response()->json([
                'success' => true,
                'rules' => $rules,
                'total' => count($rules)
            ]);
        } catch (\Exception $e) {
            \Log::error('Error in getFraudRules: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch fraud rules: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Toggle fraud rule active status
     * Route: POST /admin/finance/toggle-fraud-rule
     */
    public function toggleFraudRule(Request $request)
    {
        try {
            $request->validate([
                'rule_key' => 'required|string',
                'is_active' => 'required|boolean'
            ]);

            $ruleKey = $request->rule_key;
            $isActive = $request->is_active;

            // Map rule key to setting key
            $settingMap = [
                'duplicate_clicks' => 'enable_duplicate_click_rule',
                'ip_limit' => 'enable_ip_limit_rule',
                'invalid_details' => 'enable_invalid_details_rule',
                'bot_pattern' => 'enable_bot_detection',
                'too_many_enquiries' => 'enable_enquiry_limit_rule',
                'blacklist' => 'enable_blacklist_rule'
            ];

            if (isset($settingMap[$ruleKey])) {
                GlobalSetting::set($settingMap[$ruleKey], $isActive);
            }

            return response()->json([
                'success' => true,
                'message' => 'Rule ' . ($isActive ? 'activated' : 'deactivated') . ' successfully'
            ]);
        } catch (\Exception $e) {
            \Log::error('Error in toggleFraudRule: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to toggle rule: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get fraud rules details for modal
     * Route: GET /admin/finance/fraud-rules-details
     */
    public function getFraudRulesDetails(Request $request)
    {
        try {
            $rules = [
                [
                    'id' => 1,
                    'rule_key' => 'duplicate_clicks',
                    'rule_name' => 'Duplicate Clicks Detection',
                    'trigger_condition' => '>3 clicks in 5 seconds (same customer)',
                    'description' => 'Prevents click fraud by detecting rapid repeated clicks from the same customer',
                    'action' => 'Auto-refund PPC charges and block further clicks for 1 hour',
                    'is_active' => (bool)GlobalSetting::get('enable_duplicate_click_rule', true),
                    'severity' => 'High',
                    'created_at' => now()->subDays(30),
                    'parameters' => [
                        'max_clicks' => (int)GlobalSetting::get('duplicate_click_threshold', 3),
                        'time_window_seconds' => (int)GlobalSetting::get('duplicate_click_window_seconds', 5),
                        'block_duration_minutes' => 60
                    ]
                ],
                [
                    'id' => 2,
                    'rule_key' => 'ip_limit',
                    'rule_name' => 'IP Rate Limiting',
                    'trigger_condition' => '>20 clicks/hour from same IP',
                    'description' => 'Limits the number of clicks from a single IP address to prevent automated attacks',
                    'action' => 'Auto-refund excess clicks and temporarily block IP',
                    'is_active' => (bool)GlobalSetting::get('enable_ip_limit_rule', true),
                    'severity' => 'Medium',
                    'created_at' => now()->subDays(30),
                    'parameters' => [
                        'max_clicks_per_hour' => (int)GlobalSetting::get('same_ip_click_limit', 20),
                        'block_duration_minutes' => (int)GlobalSetting::get('ip_block_duration', 30)
                    ]
                ],
                [
                    'id' => 3,
                    'rule_key' => 'invalid_details',
                    'rule_name' => 'Invalid Contact Details Detection',
                    'trigger_condition' => 'Invalid phone/email in enquiry',
                    'description' => 'Validates phone numbers and email addresses in enquiries',
                    'action' => 'Auto-refund PPL charges and mark enquiry as fraudulent',
                    'is_active' => (bool)GlobalSetting::get('enable_invalid_details_rule', true),
                    'severity' => 'Critical',
                    'created_at' => now()->subDays(30),
                    'parameters' => [
                        'validate_phone' => true,
                        'validate_email' => true,
                        'phone_min_length' => 10
                    ]
                ],
                [
                    'id' => 4,
                    'rule_key' => 'bot_pattern',
                    'rule_name' => 'Bot Pattern Recognition',
                    'trigger_condition' => 'Bot pattern (exact intervals between clicks)',
                    'description' => 'Identifies automated bot behavior based on click patterns and timing',
                    'action' => 'Auto-refund all charges and flag as bot activity',
                    'is_active' => (bool)GlobalSetting::get('enable_bot_detection', true),
                    'severity' => 'High',
                    'created_at' => now()->subDays(30),
                    'parameters' => [
                        'exact_interval_threshold_ms' => 50,
                        'min_sequence_length' => 5,
                        'confidence_threshold' => 0.8
                    ]
                ],
                [
                    'id' => 5,
                    'rule_key' => 'too_many_enquiries',
                    'rule_name' => 'Excessive Enquiries Limit',
                    'trigger_condition' => '>5 enquiries/hour per customer',
                    'description' => 'Prevents spam by limiting enquiries per customer',
                    'action' => 'Auto-refund excess enquiry charges and temporary block',
                    'is_active' => (bool)GlobalSetting::get('enable_enquiry_limit_rule', true),
                    'severity' => 'Medium',
                    'created_at' => now()->subDays(30),
                    'parameters' => [
                        'max_enquiries_per_hour' => 5,
                        'block_duration_minutes' => 60
                    ]
                ],
                [
                    'id' => 6,
                    'rule_key' => 'blacklist',
                    'rule_name' => 'Blacklisted Customers',
                    'trigger_condition' => 'Blacklisted customers or IP addresses',
                    'description' => 'Blocks known fraudulent customers and IP addresses',
                    'action' => 'Auto-refund all charges and prevent future interactions',
                    'is_active' => (bool)GlobalSetting::get('enable_blacklist_rule', true),
                    'severity' => 'Critical',
                    'created_at' => now()->subDays(30),
                    'parameters' => [
                        'auto_refund' => true,
                        'permanent_block' => true,
                        'notify_admin' => true
                    ]
                ]
            ];

            return response()->json([
                'success' => true,
                'rules' => $rules
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch fraud rules details: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Initialize default fraud rules to database
     * Route: POST /admin/finance/initialize-fraud-rules
     */
    public function initializeFraudRules(Request $request)
    {
        try {
            $defaultRules = [
                ['rule_key' => 'duplicate_clicks', 'setting_key' => 'enable_duplicate_click_rule', 'default' => true],
                ['rule_key' => 'ip_limit', 'setting_key' => 'enable_ip_limit_rule', 'default' => true],
                ['rule_key' => 'invalid_details', 'setting_key' => 'enable_invalid_details_rule', 'default' => true],
                ['rule_key' => 'bot_pattern', 'setting_key' => 'enable_bot_detection', 'default' => true],
                ['rule_key' => 'too_many_enquiries', 'setting_key' => 'enable_enquiry_limit_rule', 'default' => true],
                ['rule_key' => 'blacklist', 'setting_key' => 'enable_blacklist_rule', 'default' => true]
            ];

            foreach ($defaultRules as $rule) {
                if (GlobalSetting::get($rule['setting_key']) === null) {
                    GlobalSetting::set($rule['setting_key'], $rule['default']);
                }
            }

            return response()->json([
                'success' => true,
                'message' => 'Default fraud rules initialized successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to initialize fraud rules: ' . $e->getMessage()
            ], 500);
        }
    }
    
    
    
    //tax & invoice section
    
    /**
 * Add new tax region row (handled via AJAX)
 * This is called from blade for adding tax regions dynamically
 */
public function addTaxRegion(Request $request)
{
    try {
        $taxRegions = json_decode(GlobalSetting::get('tax_regions', '[]'), true);
        $taxRegions[] = ['region' => '', 'tax_name' => '', 'rate' => 0];
        GlobalSetting::set('tax_regions', json_encode($taxRegions));
        return response()->json(['success' => true, 'html' => $this->renderTaxRegionRow(end($taxRegions))]);
    } catch (\Exception $e) {
        return response()->json(['success' => false, 'message' => $e->getMessage()]);
    }
}

/**
 * Add new currency row (handled via AJAX)
 */
public function addCurrency(Request $request)
{
    try {
        $currencies = json_decode(GlobalSetting::get('currencies_list', '[]'), true);
        $currencies[] = ['currency' => '', 'symbol' => '', 'rate' => 1, 'auto_update' => 'Fixed'];
        GlobalSetting::set('currencies_list', json_encode($currencies));
        return response()->json(['success' => true, 'html' => $this->renderCurrencyRow(end($currencies))]);
    } catch (\Exception $e) {
        return response()->json(['success' => false, 'message' => $e->getMessage()]);
    }
}

/**
 * Remove tax region
 */
public function removeTaxRegion(Request $request)
{
    try {
        $index = $request->index;
        $taxRegions = json_decode(GlobalSetting::get('tax_regions', '[]'), true);
        if (isset($taxRegions[$index])) {
            unset($taxRegions[$index]);
            GlobalSetting::set('tax_regions', json_encode(array_values($taxRegions)));
        }
        return response()->json(['success' => true]);
    } catch (\Exception $e) {
        return response()->json(['success' => false, 'message' => $e->getMessage()]);
    }
}

/**
 * Remove currency
 */
public function removeCurrency(Request $request)
{
    try {
        $index = $request->index;
        $currencies = json_decode(GlobalSetting::get('currencies_list', '[]'), true);
        if (isset($currencies[$index])) {
            unset($currencies[$index]);
            GlobalSetting::set('currencies_list', json_encode(array_values($currencies)));
        }
        return response()->json(['success' => true]);
    } catch (\Exception $e) {
        return response()->json(['success' => false, 'message' => $e->getMessage()]);
    }
}

/**
 * Generate invoice preview
 */
public function generateInvoice(Request $request)
{
    try {
        $sellerId = $request->seller_id;
        $startDate = $request->start_date;
        $endDate = $request->end_date;
        
        if (!$sellerId) {
            return response()->json(['success' => false, 'message' => 'Please select a seller']);
        }
        
        $seller = Seller::find($sellerId);
        $invoiceTemplate = json_decode(GlobalSetting::get('invoice_template_settings', '{}'), true);
        $gstRate = GlobalSetting::get('gst_rate', 18);
        
        $salesData = Order::where('seller_id', $sellerId)
            ->where('payment_status', 'paid')
            ->when($startDate, fn($q) => $q->whereDate('created_at', '>=', $startDate))
            ->when($endDate, fn($q) => $q->whereDate('created_at', '<=', $endDate))
            ->get();
        
        $totalSales = $salesData->sum('total_amount');
        $commission = $totalSales * 0.1;
        $gstAmount = $commission * ($gstRate / 100);
        $netAmount = $totalSales - $commission - $gstAmount;
        
        $html = view('admin.invoice-preview', compact('seller', 'salesData', 'invoiceTemplate', 'totalSales', 'commission', 'gstRate', 'gstAmount', 'netAmount', 'startDate', 'endDate'))->render();
        
        return response()->json(['success' => true, 'html' => $html]);
    } catch (\Exception $e) {
        return response()->json(['success' => false, 'message' => $e->getMessage()]);
    }
}

/**
 * Download invoice as PDF
 */
public function downloadInvoice(Request $request)
{
    try {
        $sellerId = $request->seller_id;
        $startDate = $request->start_date;
        $endDate = $request->end_date;
        
        if (!$sellerId) {
            return redirect()->back()->with('error', 'Please select a seller');
        }
        
        $seller = Seller::find($sellerId);
        $invoiceTemplate = json_decode(GlobalSetting::get('invoice_template_settings', '{}'), true);
        $gstRate = GlobalSetting::get('gst_rate', 18);
        
        $salesData = Order::where('seller_id', $sellerId)
            ->where('payment_status', 'paid')
            ->when($startDate, fn($q) => $q->whereDate('created_at', '>=', $startDate))
            ->when($endDate, fn($q) => $q->whereDate('created_at', '<=', $endDate))
            ->get();
        
        $totalSales = $salesData->sum('total_amount');
        $commission = $totalSales * 0.1;
        $gstAmount = $commission * ($gstRate / 100);
        $netAmount = $totalSales - $commission - $gstAmount;
        
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('admin.invoice-pdf', compact('seller', 'salesData', 'invoiceTemplate', 'totalSales', 'commission', 'gstRate', 'gstAmount', 'netAmount', 'startDate', 'endDate'));
        
        return $pdf->download('invoice_' . $seller->id . '_' . date('Y-m-d') . '.pdf');
    } catch (\Exception $e) {
        return redirect()->back()->with('error', $e->getMessage());
    }
}

/**
 * Export report (CSV)
 */
public function exportReport(Request $request)
{
    try {
        $reportType = $request->report_type;
        
        switch ($reportType) {
            case 'tax':
                return $this->exportTaxReport($request);
            case 'invoice':
                return $this->exportInvoiceReport($request);
            case 'gst':
                return $this->exportGSTReport($request);
            case 'currency':
                return $this->exportCurrencyReport($request);
            default:
                return redirect()->back()->with('error', 'Invalid report type');
        }
    } catch (\Exception $e) {
        return redirect()->back()->with('error', $e->getMessage());
    }
}

/**
 * Validate GST number
 */
public function validateGST(Request $request)
{
    try {
        $gstNumber = $request->gst_number;
        $pattern = '/^[0-9]{2}[A-Z]{5}[0-9]{4}[A-Z]{1}[1-9A-Z]{1}Z[0-9A-Z]{1}$/';
        
        if (preg_match($pattern, $gstNumber)) {
            return response()->json(['success' => true, 'message' => 'Valid GST Number format!']);
        } else {
            return response()->json(['success' => false, 'message' => 'Invalid GST Number format!']);
        }
    } catch (\Exception $e) {
        return response()->json(['success' => false, 'message' => $e->getMessage()]);
    }
}

/**
 * Save all tax settings (combined save)
 */
public function saveAllTaxSettings(Request $request)
{
    try {
        // Save GST Rate
        if ($request->has('gst_rate')) {
            GlobalSetting::set('gst_rate', $request->gst_rate);
        }
        
        // Save Base Currency
        if ($request->has('base_currency')) {
            session(['currency' => $request->base_currency]);
        }
        
        // Save Tax Regions
        if ($request->has('tax_regions')) {
            $taxRegions = [];
            foreach ($request->tax_regions as $region) {
                if (!empty($region['region']) && !empty($region['tax_name']) && isset($region['rate'])) {
                    $taxRegions[] = $region;
                }
            }
            GlobalSetting::set('tax_regions', json_encode($taxRegions));
        }
        
        // Save Currencies
        if ($request->has('currencies')) {
            $currencies = [];
            foreach ($request->currencies as $currency) {
                if (!empty($currency['currency']) && !empty($currency['symbol']) && isset($currency['rate'])) {
                    $currencies[] = $currency;
                }
            }
            GlobalSetting::set('currencies_list', json_encode($currencies));
        }
        
        // Save Invoice Template
        $invoiceTemplate = [
            'style' => $request->template_style,
            'companyName' => $request->company_name,
            'companyGst' => $request->company_gst,
            'companyEmail' => $request->company_email,
            'companyPhone' => $request->company_phone,
            'companyAddress' => $request->company_address,
            'invoicePrefix' => $request->invoice_prefix,
            'invoiceFooter' => $request->invoice_footer,
        ];
        GlobalSetting::set('invoice_template_settings', json_encode($invoiceTemplate));
        
        return response()->json(['success' => true, 'message' => 'Tax settings saved successfully!']);
    } catch (\Exception $e) {
        return response()->json(['success' => false, 'message' => $e->getMessage()]);
    }
}

// Private helper methods for exports
    public function sendInvoiceEmail(Request $request)
    {
        try {
            $sellerId = $request->seller_id;
            $startDate = $request->start_date;
            $endDate = $request->end_date;
            
            if (!$sellerId) {
                return response()->json(['success' => false, 'message' => 'Please select a seller']);
            }
            
            $seller = Seller::find($sellerId);
            if (!$seller) {
                return response()->json(['success' => false, 'message' => 'Seller not found']);
            }
            if (!$seller->email) {
                return response()->json(['success' => false, 'message' => 'Seller has no registered email address']);
            }
            
            $invoiceTemplate = json_decode(GlobalSetting::get('invoice_template_settings', '{}'), true);
            $gstRate = GlobalSetting::get('gst_rate', 18);
            
            $salesData = Order::where('seller_id', $sellerId)
                ->where('payment_status', 'paid')
                ->when($startDate, fn($q) => $q->whereDate('created_at', '>=', $startDate))
                ->when($endDate, fn($q) => $q->whereDate('created_at', '<=', $endDate))
                ->get();
            
            $totalSales = $salesData->sum('total_amount');
            $commission = $totalSales * 0.1;
            $gstAmount = $commission * ($gstRate / 100);
            $netAmount = $totalSales - $commission - $gstAmount;
            
            $to = $seller->email;
            $subject = 'Invoice #' . ($invoiceTemplate['invoicePrefix'] ?? 'INV') . '-' . $seller->id . '-' . date('Ymd');
            
            $htmlContent = view('emails.invoice', compact('seller', 'salesData', 'invoiceTemplate', 'totalSales', 'commission', 'gstRate', 'gstAmount', 'netAmount', 'startDate', 'endDate'))->render();
            
            try {
                \Mail::to($to)->send(new \App\Mail\InvoiceMail($seller, $salesData, $invoiceTemplate, $totalSales, $commission, $gstRate, $gstAmount, $netAmount, $startDate, $endDate));
                return response()->json(['success' => true, 'message' => 'Invoice email sent successfully to ' . $seller->name . ' (' . $to . ')']);
            } catch (\Exception $ex) {
                $headers = "MIME-Version: 1.0" . "\r\n";
                $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
                $headers .= "From: " . ($invoiceTemplate['companyEmail'] ?? 'billing@company.com') . "\r\n";
                
                $mailSent = @mail($to, $subject, $htmlContent, $headers);
                if ($mailSent) {
                    return response()->json(['success' => true, 'message' => 'Invoice email sent successfully (via native PHP mail) to ' . $seller->name . ' (' . $to . ')']);
                } else {
                    return response()->json(['success' => false, 'message' => 'Could not send email. Laravel Mailer error: ' . $ex->getMessage()]);
                }
            }
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    private function exportTaxReport(Request $request)
    {
        $sellerId = $request->seller_id;
        $startDate = $request->start_date;
        $endDate = $request->end_date;
        
        $filename = 'tax_report_' . ($sellerId ? 'seller_' . $sellerId . '_' : 'global_') . date('Y-m-d') . '.csv';
        $handle = fopen('php://output', 'w');
        
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        
        $gstRate = GlobalSetting::get('gst_rate', 18);
        
        if ($sellerId) {
            $seller = Seller::find($sellerId);
            fputcsv($handle, ['SELLER TAX SUMMARY REPORT']);
            fputcsv($handle, ['Seller ID', 'S-' . ($seller->id ?? $sellerId)]);
            fputcsv($handle, ['Seller Name', $seller->name ?? 'N/A']);
            fputcsv($handle, ['GSTIN', $seller->gst_number ?? 'N/A']);
            if ($startDate || $endDate) {
                fputcsv($handle, ['Period', ($startDate ?? 'Start') . ' to ' . ($endDate ?? 'End')]);
            }
            fputcsv($handle, []);
            
            fputcsv($handle, ['Order ID', 'Total Sales (INR)', 'Commission (INR)', 'GST Amount (INR)', 'Net Payout (INR)', 'Date']);
            
            $orders = Order::where('seller_id', $sellerId)
                ->where('payment_status', 'paid')
                ->when($startDate, fn($q) => $q->whereDate('created_at', '>=', $startDate))
                ->when($endDate, fn($q) => $q->whereDate('created_at', '<=', $endDate))
                ->get();
                
            foreach ($orders as $order) {
                $commission = $order->total_amount * 0.1;
                $gstAmount = $commission * ($gstRate / 100);
                $netPayout = $order->total_amount - $commission - $gstAmount;
                
                fputcsv($handle, [
                    'INV-' . $order->id,
                    round($order->total_amount, 2),
                    round($commission, 2),
                    round($gstAmount, 2),
                    round($netPayout, 2),
                    $order->created_at->format('Y-m-d')
                ]);
            }
        } else {
            // Export ALL sellers tax summary
            fputcsv($handle, ['ALL SELLERS TAX SUMMARY REPORT']);
            if ($startDate || $endDate) {
                fputcsv($handle, ['Period', ($startDate ?? 'Start') . ' to ' . ($endDate ?? 'End')]);
            }
            fputcsv($handle, ['Generated On', date('Y-m-d H:i:s')]);
            fputcsv($handle, ['GST Rate Applied', $gstRate . '%']);
            fputcsv($handle, []);
            fputcsv($handle, ['Seller ID', 'Seller Name', 'Email', 'GSTIN', 'Total Sales', 'Platform Commission', 'GST Amount', 'Net Payout']);
            
            $allSellers = Seller::all();
            $grandTotalSales = 0;
            $grandTotalCommission = 0;
            $grandTotalGst = 0;
            $grandTotalPayout = 0;
            
            foreach ($allSellers as $sellerItem) {
                $query = Order::where('seller_id', $sellerItem->id)->where('payment_status', 'paid');
                if ($startDate) $query->whereDate('created_at', '>=', $startDate);
                if ($endDate) $query->whereDate('created_at', '<=', $endDate);
                
                $sellerSales = $query->sum('total_amount');
                $sellerCommission = SellerCommission::where('seller_id', $sellerItem->id)->sum('commission_amount');
                if ($sellerCommission == 0) $sellerCommission = $sellerSales * 0.1;
                $sellerGst = $sellerCommission * ($gstRate / 100);
                $sellerNet = $sellerSales - $sellerCommission - $sellerGst;
                
                $grandTotalSales += $sellerSales;
                $grandTotalCommission += $sellerCommission;
                $grandTotalGst += $sellerGst;
                $grandTotalPayout += $sellerNet;
                
                fputcsv($handle, [
                    'S-' . (1000 + $sellerItem->id),
                    $sellerItem->name,
                    $sellerItem->email ?? 'N/A',
                    $sellerItem->gst_number ?? 'N/A',
                    round($sellerSales, 2),
                    round($sellerCommission, 2),
                    round($sellerGst, 2),
                    round($sellerNet, 2)
                ]);
            }
            
            fputcsv($handle, []);
            fputcsv($handle, ['', 'GRAND TOTAL', '', '', round($grandTotalSales, 2), round($grandTotalCommission, 2), round($grandTotalGst, 2), round($grandTotalPayout, 2)]);
            
            // Also include tax regions reference
            fputcsv($handle, []);
            fputcsv($handle, ['TAX REGIONS REFERENCE']);
            fputcsv($handle, ['Region', 'Tax Name', 'Rate (%)']);
            $taxRegions = json_decode(GlobalSetting::get('tax_regions', '[]'), true);
            foreach ($taxRegions as $region) {
                fputcsv($handle, [$region['region'], $region['tax_name'], $region['rate']]);
            }
        }
        
        fclose($handle);
        exit;
    }

    private function exportInvoiceReport(Request $request)
    {
        $sellerId = $request->seller_id;
        $startDate = $request->start_date;
        $endDate = $request->end_date;
        
        $filename = 'invoice_history_' . ($sellerId ? 'seller_' . $sellerId . '_' : 'global_') . date('Y-m-d') . '.csv';
        $handle = fopen('php://output', 'w');
        
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        
        fputcsv($handle, ['Invoice ID', 'Seller Name', 'Seller Email', 'Total Sales (INR)', 'Commission (INR)', 'GST Amount (INR)', 'Net Payout (INR)', 'Date', 'Status']);
        
        $gstRate = GlobalSetting::get('gst_rate', 18);
        
        $invoices = Order::with('seller')
            ->where('payment_status', 'paid')
            ->when($sellerId, fn($q) => $q->where('seller_id', $sellerId))
            ->when($startDate, fn($q) => $q->whereDate('created_at', '>=', $startDate))
            ->when($endDate, fn($q) => $q->whereDate('created_at', '<=', $endDate))
            ->get();
            
        foreach ($invoices as $invoice) {
            $totalSales = $invoice->total_amount;
            $commission = $totalSales * 0.1;
            $gstAmount = $commission * ($gstRate / 100);
            $netPayout = $totalSales - $commission - $gstAmount;
            
            fputcsv($handle, [
                'INV-' . $invoice->id,
                $invoice->seller->name ?? 'Guest/Unknown',
                $invoice->seller->email ?? 'N/A',
                round($totalSales, 2),
                round($commission, 2),
                round($gstAmount, 2),
                round($netPayout, 2),
                $invoice->created_at->format('Y-m-d'),
                $invoice->payment_status
            ]);
        }
        
        fclose($handle);
        exit;
    }

    private function exportGSTReport(Request $request)
    {
        $sellerId = $request->seller_id;
        $startDate = $request->start_date;
        $endDate = $request->end_date;
        
        $filename = 'gst_collection_' . ($sellerId ? 'seller_' . $sellerId . '_' : 'global_') . date('Y-m-d') . '.csv';
        $handle = fopen('php://output', 'w');
        
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        
        $gstRate = GlobalSetting::get('gst_rate', 18);
        
        if ($sellerId) {
            $seller = Seller::find($sellerId);
            fputcsv($handle, ['SELLER GST COLLECTION REPORT']);
            fputcsv($handle, ['Seller ID', 'S-' . ($seller->id ?? $sellerId)]);
            fputcsv($handle, ['Seller Name', $seller->name ?? 'N/A']);
            fputcsv($handle, ['GSTIN', $seller->gst_number ?? 'N/A']);
            fputcsv($handle, []);
            fputcsv($handle, ['Order ID', 'Subtotal (INR)', 'GST Rate (%)', 'GST Collected (INR)', 'Commission (INR)', 'Net Payout (INR)', 'Date']);
            
            $orders = Order::where('seller_id', $sellerId)
                ->where('payment_status', 'paid')
                ->when($startDate, fn($q) => $q->whereDate('created_at', '>=', $startDate))
                ->when($endDate, fn($q) => $q->whereDate('created_at', '<=', $endDate))
                ->get();
                
            foreach ($orders as $order) {
                $commission = $order->total_amount * 0.1;
                $gstAmount = $commission * ($gstRate / 100);
                $netPayout = $order->total_amount - $commission - $gstAmount;
                
                fputcsv($handle, [
                    'INV-' . $order->id,
                    round($order->total_amount, 2),
                    $gstRate,
                    round($gstAmount, 2),
                    round($commission, 2),
                    round($netPayout, 2),
                    $order->created_at->format('Y-m-d')
                ]);
            }
        } else {
            fputcsv($handle, ['GLOBAL GST COLLECTION REPORT']);
            fputcsv($handle, []);
            fputcsv($handle, ['Seller ID', 'Seller Name', 'Seller Email', 'Total Sales (INR)', 'Total Commission (INR)', 'GST Rate (%)', 'GST Amount Collected (INR)', 'Net Payout (INR)']);
            
            $sellers = Seller::all();
            foreach ($sellers as $seller) {
                $totalSales = SellerCommission::where('seller_id', $seller->id)
                    ->when($startDate, fn($q) => $q->whereDate('created_at', '>=', $startDate))
                    ->when($endDate, fn($q) => $q->whereDate('created_at', '<=', $endDate))
                    ->sum('order_amount');
                    
                $commission = SellerCommission::where('seller_id', $seller->id)
                    ->when($startDate, fn($q) => $q->whereDate('created_at', '>=', $startDate))
                    ->when($endDate, fn($q) => $q->whereDate('created_at', '<=', $endDate))
                    ->sum('commission_amount');
                    
                $gstAmount = $commission * ($gstRate / 100);
                $netPayout = $totalSales - $commission - $gstAmount;
                
                fputcsv($handle, [
                    $seller->id,
                    $seller->name,
                    $seller->email,
                    round($totalSales, 2),
                    round($commission, 2),
                    $gstRate,
                    round($gstAmount, 2),
                    round($netPayout, 2)
                ]);
            }
        }
        
        fclose($handle);
        exit;
    }

    private function exportCurrencyReport(Request $request)
    {
        $sellerId = $request->seller_id;
        $currencies = json_decode(GlobalSetting::get('currencies_list', '[]'), true);
        $baseCurrency = session('currency', 'INR');
        
        $filename = 'currency_report_' . ($sellerId ? 'seller_' . $sellerId . '_' : 'global_') . date('Y-m-d') . '.csv';
        $handle = fopen('php://output', 'w');
        
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        
        if ($sellerId) {
            $seller = Seller::find($sellerId);
            fputcsv($handle, ['SELLER CURRENCY PREFERENCES']);
            fputcsv($handle, ['Seller Name', $seller->name ?? 'N/A']);
            fputcsv($handle, ['Preferred Currency', $seller->currency ?? $baseCurrency]);
            fputcsv($handle, []);
        }
        
        fputcsv($handle, ['Base Currency', $baseCurrency]);
        fputcsv($handle, []);
        fputcsv($handle, ['Currency', 'Symbol', 'Exchange Rate', 'Auto-Update']);
        foreach ($currencies as $currency) {
            fputcsv($handle, [
                $currency['currency'],
                $currency['symbol'],
                $currency['rate'],
                $currency['auto_update'] ?? 'Fixed'
            ]);
        }
        
        fclose($handle);
        exit;
    }

    public function savePricingDiscountRules(Request $request)
    {
        try {
            GlobalSetting::set('dynamic_pricing_enabled', $request->dynamic_pricing_enabled ? 1 : 0);
            GlobalSetting::set('dynamic_pricing_strategy', $request->dynamic_pricing_strategy ?? 'demand');
            GlobalSetting::set('price_adjust_limit', $request->price_adjust_limit ?? 15);
            GlobalSetting::set('price_update_freq', $request->price_update_freq ?? 'daily');
            GlobalSetting::set('discount_cap_enabled', $request->discount_cap_enabled ? 1 : 0);
            GlobalSetting::set('discount_cap_percent', $request->discount_cap_percent ?? 30);
            GlobalSetting::set('discount_cap_action', $request->discount_cap_action ?? 'adjust');
            GlobalSetting::set('coupon_discount_cap', $request->coupon_discount_cap ?? 500);

            return response()->json(['success' => true, 'message' => 'Dynamic pricing & Discount cap rules saved successfully']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }
}
