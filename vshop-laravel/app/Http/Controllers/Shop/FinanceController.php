<?php
// App\Http\Controllers\Admin\AdminFinanceController.php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SellerFinance;
use App\Models\SellerCommission;
use App\Models\Order;
use App\Models\Seller;
use App\Models\Grievance;
use App\Models\GlobalSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminFinanceController extends Controller
{
    public function index(Request $request)
    {
        $currency = $request->get('currency', 'USD');
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
        
        // Commission Trends (Last 6 months)
        $trendLabels = [];
        $trendData = [];
        for ($i = 5; $i >= 0; $i--) {
            $month = now()->subMonths($i);
            $trendLabels[] = $month->format('M');
            $trendData[] = SellerCommission::whereYear('created_at', $month->year)->whereMonth('created_at', $month->month)->sum('commission_amount') * $exchangeRate;
        }
        
        // Multi-Level Commission Rules
        $commissionRules = DB::table('commission_levels')->orderBy('level')->get()->map(function($item) {
            return ['level' => $item->level, 'role' => $item->role, 'type' => $item->type, 'value' => $item->value];
        })->toArray();
        if(empty($commissionRules)) {
            $commissionRules = [
                ['level' => 1, 'role' => 'Seller', 'type' => '%', 'value' => 60],
                ['level' => 2, 'role' => 'Referrer', 'type' => '%', 'value' => 10],
                ['level' => 3, 'role' => 'Platform', 'type' => '%', 'value' => 30],
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
        $disputeHolds = Grievance::where('status', 'open')->orderBy('created_at', 'desc')->get();
        
        // Settlement Hold Sellers
        $holdSellers = Seller::where('settlement_hold', true)->orWhere('is_new_seller', true)->get();
        
        // Refund Deduction Impact (this month)
        $refundImpact = SellerFinance::where('transaction_type', 'refund')->whereYear('created_at', now()->year)->whereMonth('created_at', now()->month)->sum('debit') * $exchangeRate;
        
        // Dispute Wallet Hold Tracker
        $disputeWalletHold = SellerFinance::where('dispute_status', 'pending')->where('status', 'pending')->sum('debit') * $exchangeRate;
        if ($disputeWalletHold == 0) $disputeWalletHold = $openDisputes * 100 * $exchangeRate;
        
        // App Wise Commission
        $appWiseCommission = [
            'vShop' => GlobalSetting::get('commission_vshop', 25),
            'vTravel' => GlobalSetting::get('commission_vtravel', 20),
            'vEdge' => GlobalSetting::get('commission_vedge', 15),
            'vCampus' => GlobalSetting::get('commission_vcampus', 10),
        ];
        
        return view('admin.finance-management', compact(
            'currency', 'exchangeRate', 'totalRevenue', 'totalCommission', 'totalPayouts', 'pendingPayouts',
            'refundAmount', 'disputesCount', 'openDisputes', 'resolvedDisputes', 'netRevenue', 'leadRevenue',
            'todayClickRevenue', 'todayEnquiryRevenue', 'topLeadSeller', 'productRevenue', 'serviceRevenue',
            'trendLabels', 'trendData', 'commissionRules', 'sellerEarnings', 'platformCommission',
            'pendingPayoutsList', 'transactions', 'failedPayouts', 'disputeHolds', 'holdSellers',
            'refundImpact', 'appWiseCommission', 'disputeWalletHold'
        ));
    }
    
    public function updateCommission(Request $request)
    {
        DB::table('commission_levels')->truncate();
        foreach($request->levels as $level) {
            DB::table('commission_levels')->insert($level);
        }
        return response()->json(['success' => true]);
    }
    
    public function approvePayout(Request $request)
    {
        $payout = SellerFinance::find($request->payout_id);
        if ($payout) {
            $payout->status = 'completed';
            $payout->save();
            return response()->json(['success' => true]);
        }
        return response()->json(['success' => false]);
    }
    
    public function bulkApprovePayouts(Request $request)
    {
        SellerFinance::whereIn('id', $request->payout_ids)->where('transaction_type', 'payout')->update(['status' => 'completed']);
        return response()->json(['success' => true]);
    }
    
    public function updateGlobalRules(Request $request)
    {
        if($request->has('app_commission')) {
            foreach($request->app_commission as $app => $value) {
                GlobalSetting::set('commission_' . strtolower($app), $value);
            }
        }
        if($request->has('discount_cap')) GlobalSetting::set('discount_cap', $request->discount_cap);
        if($request->has('min_margin')) GlobalSetting::set('min_margin', $request->min_margin);
        if($request->has('effective_date')) GlobalSetting::set('commission_effective_date', $request->effective_date);
        if($request->has('auto_boost')) GlobalSetting::set('auto_boost', $request->auto_boost);
        return response()->json(['success' => true]);
    }
    
    public function updatePricing(Request $request)
    {
        GlobalSetting::set('pricing_' . str_slug($request->app . '_' . $request->category), json_encode(['click' => $request->click_price, 'enquiry' => $request->enquiry_price ?? 0]));
        return response()->json(['success' => true]);
    }
    
    public function resolveDispute(Request $request)
    {
        $dispute = Grievance::find($request->dispute_id);
        if ($dispute) {
            $dispute->status = 'close';
            $dispute->resolution_note = $request->resolution_note;
            $dispute->resolved_at = now();
            $dispute->save();
            // Release any held funds
            SellerFinance::where('dispute_id', $dispute->id)->update(['dispute_status' => 'resolved']);
            return response()->json(['success' => true]);
        }
        return response()->json(['success' => false]);
    }
    
    public function applyManualHold(Request $request)
    {
        // Apply hold to a specific seller (you can pass seller_id in future)
        return response()->json(['success' => true]);
    }
    
    public function exportTransactions(Request $request)
    {
        $transactions = SellerFinance::with(['seller', 'order'])->orderBy('created_at', 'desc')->get();
        $filename = 'transactions_' . date('Y-m-d') . '.csv';
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        $handle = fopen('php://output', 'w');
        fputcsv($handle, ['Date', 'Order ID', 'Seller', 'Transaction Type', 'Credit', 'Debit', 'Status']);
        foreach ($transactions as $transaction) {
            fputcsv($handle, [
                $transaction->created_at, $transaction->order_id, $transaction->seller->name ?? 'N/A',
                $transaction->transaction_type, $transaction->credit, $transaction->debit, $transaction->status
            ]);
        }
        fclose($handle);
        exit;
    }
    
    private function getExchangeRate($currency)
    {
        $rates = ['USD' => 1, 'INR' => 83.5, 'EUR' => 0.92, 'GBP' => 0.79];
        return $rates[$currency] ?? 1;
    }
}