<?php
// app/Http/Controllers/Admin/AdminReportAnalyticController.php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AdminReportAnalyticController extends Controller
{
    /**
     * Display the Reports & Analytics dashboard.
     */
    public function index(Request $request)
    {
        $period = $request->get('period', 'monthly');
        $currency = $request->get('currency', '$');
        
        // Fetch real data from database
        $data = [
            'revenueData' => $this->getRevenueData($period),
            'peakPeriodsData' => $this->getPeakPeriodsData(),
            'categorySalesData' => $this->getCategorySalesData(),
            'funnelData' => $this->getFunnelData(),
            'dropoffData' => $this->getDropoffData(),
            'segmentationData' => $this->getSegmentationData(),
            'topCampaigns' => $this->getTopCampaigns(),
            'topRoiSellers' => $this->getTopRoiSellers(),
            'boostedCategories' => $this->getBoostedCategories(),
            'cpaData' => $this->getCpaData(),
            'leadAnalytics' => $this->getLeadAnalytics(),
            'supplierPerformance' => $this->getSupplierPerformance(),
            'aiPredictions' => $this->getAiPredictions(),
            'aiInsights' => $this->getAiInsights(),
        ];
        
        return view('admin.reports-analytics.index', compact('data', 'period', 'currency'));
    }
    
    /**
     * Get revenue trends data.
     */
    private function getRevenueData($period)
    {
        $dates = $this->getDateRange($period);
        
        return DB::table('orders')
            ->select(DB::raw('DATE(created_at) as date'), DB::raw('SUM(total_amount) as revenue'))
            ->whereBetween('created_at', [$dates['start'], $dates['end']])
            ->where('status', '!=', 'cancelled')
            ->groupBy('date')
            ->orderBy('date')
            ->get();
    }
    
    /**
     * Get peak periods data (hourly order distribution).
     */
    private function getPeakPeriodsData()
    {
        return DB::table('orders')
            ->select(DB::raw('HOUR(created_at) as hour'), DB::raw('COUNT(*) as order_count'))
            ->whereDate('created_at', '>=', Carbon::now()->subDays(30))
            ->groupBy('hour')
            ->orderBy('hour')
            ->get();
    }
    
    /**
     * Get category-wise sales data.
     */
    private function getCategorySalesData()
    {
        return DB::table('order_items')
            ->join('products', 'order_items.product_id', '=', 'products.id')
            ->join('categories', 'products.category_id', '=', 'categories.id')
            ->select('categories.name', DB::raw('SUM(order_items.quantity * order_items.price) as total_sales'))
            ->groupBy('categories.id', 'categories.name')
            ->orderByDesc('total_sales')
            ->limit(5)
            ->get();
    }
    
    /**
     * Get funnel conversion data (enquiry to order).
     */
    private function getFunnelData()
    {
        $enquiries = DB::table('enquiries')->whereBetween('created_at', [Carbon::now()->subDays(30), Carbon::now()])->count();
        $orders = DB::table('orders')->whereBetween('created_at', [Carbon::now()->subDays(30), Carbon::now()])->count();
        
        return [
            'enquiries' => $enquiries,
            'orders' => $orders,
            'conversion_rate' => $enquiries > 0 ? round(($orders / $enquiries) * 100, 2) : 0
        ];
    }
    
    /**
     * Get drop-off points data.
     */
    private function getDropoffData()
    {
        return [
            'cart' => DB::table('cart_items')->where('created_at', '>=', Carbon::now()->subDays(30))->count(),
            
            'payment' => DB::table('payment_attempts')->where('status', 'failed')->where('created_at', '>=', Carbon::now()->subDays(30))->count(),
            'abandoned' => DB::table('cart_items')->where('status', 'abandoned')->where('updated_at', '>=', Carbon::now()->subDays(30))->count(),
        ];
    }
    
    /**
     * Get customer segmentation data.
     */
    private function getSegmentationData()
    {
        $totalCustomers = DB::table('users')->where('role', 'customer')->count();
        
        $newCustomers = DB::table('users')
            ->where('role', 'customer')
            ->where('created_at', '>=', Carbon::now()->subDays(30))
            ->count();
            
        $regularCustomers = DB::table('users')
            ->where('role', 'customer')
            ->whereBetween('created_at', [Carbon::now()->subDays(180), Carbon::now()->subDays(31)])
            ->count();
            
        $vipCustomers = DB::table('users')
            ->where('role', 'customer')
            ->whereExists(function($query) {
                $query->select(DB::raw(1))
                    ->from('orders')
                    ->whereColumn('orders.user_id', 'users.id')
                    ->having(DB::raw('SUM(orders.total_amount)'), '>', 5000);
            })
            ->count();
            
        return [
            'new' => $newCustomers,
            'regular' => $regularCustomers,
            'vip' => $vipCustomers,
        ];
    }
    
    /**
     * Get top performing campaigns.
     */
    private function getTopCampaigns()
    {
        return DB::table('campaigns')
            ->select('name', 'roi', 'spend', 'revenue')
            ->where('status', 'completed')
            ->orderByDesc('roi')
            ->limit(5)
            ->get();
    }
    
    /**
     * Get top ROI sellers.
     */
    private function getTopRoiSellers()
    {
        return DB::table('sellers')
            ->join('products', 'sellers.id', '=', 'products.created_by_id')
            ->join('order_items', 'products.id', '=', 'order_items.product_id')
            ->select('sellers.name', DB::raw('SUM(order_items.quantity * order_items.price) as revenue'), DB::raw('COUNT(DISTINCT orders.id) as orders_count'))
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->where('orders.status', 'completed')
            ->groupBy('sellers.id', 'sellers.name')
            ->orderByDesc('revenue')
            ->limit(5)
            ->get();
    }
    
    /**
     * Get boosted categories data.
     */
    private function getBoostedCategories()
    {
        return DB::table('boosted_listings')
            ->join('products', 'boosted_listings.product_id', '=', 'products.id')
            ->join('categories', 'products.category_id', '=', 'categories.id')
            ->select('categories.name', DB::raw('COUNT(*) as boost_count'), DB::raw('AVG(boosted_listings.impressions) as avg_impressions'))
            ->groupBy('categories.id', 'categories.name')
            ->orderByDesc('boost_count')
            ->get();
    }
    
    /**
     * Get CPA tracking data.
     */
    private function getCpaData()
    {
        $totalSpend = DB::table('campaigns')->sum('spend');
        $totalConversions = DB::table('campaign_conversions')->count();
        
        return [
            'overall_cpa' => $totalConversions > 0 ? round($totalSpend / $totalConversions, 2) : 0,
            'by_channel' => DB::table('campaigns')
                ->select('channel', DB::raw('SUM(spend) as total_spend'), DB::raw('COUNT(conversions.id) as conversions'))
                ->leftJoin('campaign_conversions as conversions', 'campaigns.id', '=', 'conversions.campaign_id')
                ->groupBy('channel')
                ->get()
        ];
    }
    
    /**
     * Get lead analytics data.
     */
    private function getLeadAnalytics()
    {
        return [
            'most_clicked_services' => DB::table('service_clicks')
                ->select('service_name', DB::raw('COUNT(*) as clicks'))
                ->groupBy('service_name')
                ->orderByDesc('clicks')
                ->limit(5)
                ->get(),
            'most_enquired_services' => DB::table('enquiries')
                ->select('service_name', DB::raw('COUNT(*) as enquiries'))
                ->groupBy('service_name')
                ->orderByDesc('enquiries')
                ->limit(5)
                ->get(),
            'top_lead_sellers' => DB::table('sellers')
                ->join('enquiries', 'sellers.id', '=', 'enquiries.seller_id')
                ->select('sellers.name', DB::raw('COUNT(enquiries.id) as total_leads'))
                ->groupBy('sellers.id', 'sellers.name')
                ->orderByDesc('total_leads')
                ->limit(5)
                ->get(),
        ];
    }
    
    /**
     * Get supplier performance for dropshipping.
     */
    private function getSupplierPerformance()
    {
        return DB::table('suppliers')
            ->select('suppliers.name', DB::raw('COUNT(orders.id) as total_orders'), DB::raw('AVG(CASE WHEN orders.delivery_status = "delivered" THEN 1 ELSE 0 END) * 100 as success_rate'))
            ->leftJoin('products', 'suppliers.id', '=', 'products.supplier_id')
            ->leftJoin('order_items', 'products.id', '=', 'order_items.product_id')
            ->leftJoin('orders', 'order_items.order_id', '=', 'orders.id')
            ->groupBy('suppliers.id', 'suppliers.name')
            ->get();
    }
    
    /**
     * Get AI predictions based on historical data.
     */
    private function getAiPredictions()
    {
        // AI logic implementation
        $avgConversion = DB::table('campaigns')->avg('conversion_rate') ?? 5;
        $avgReach = DB::table('campaigns')->avg('reach') ?? 10000;
        
        return [
            'recommended_spend' => $this->calculateRecommendedSpend(),
            'expected_reach' => round($avgReach * 1.2),
            'expected_leads' => round(($avgReach * 1.2) * ($avgConversion / 100)),
            'expected_conversion' => $avgConversion + 2,
            'best_cities' => $this->getBestCitiesForBoost(),
            'best_timing' => $this->getBestTiming(),
            'poor_campaigns' => DB::table('campaigns')
                ->where('roi', '<', 50)
                ->where('status', 'active')
                ->get(),
        ];
    }
    
    /**
     * Get AI insights for sales drop, growth, risks.
     */
    private function getAiInsights()
    {
        $lastMonthRevenue = DB::table('orders')->whereBetween('created_at', [Carbon::now()->subMonths(2), Carbon::now()->subMonths(1)])->sum('total_amount');
        $thisMonthRevenue = DB::table('orders')->whereBetween('created_at', [Carbon::now()->subMonths(1), Carbon::now()])->sum('total_amount');
        
        $salesDrop = $thisMonthRevenue < $lastMonthRevenue ? round((($lastMonthRevenue - $thisMonthRevenue) / $lastMonthRevenue) * 100, 2) : 0;
        
        return [
            'sales_drop_analysis' => $salesDrop > 0 ? "Sales dropped by {$salesDrop}% compared to last month." : "Sales are stable or growing.",
            'growth_recommendations' => $this->getGrowthRecommendations(),
            'product_seller_suggestions' => $this->getProductSellerSuggestions(),
            'risk_alerts' => $this->getRiskAlerts(),
        ];
    }
    
    /**
     * Helper: Get date range based on period.
     */
    private function getDateRange($period)
    {
        $end = Carbon::now();
        switch($period) {
            case 'daily': $start = Carbon::now()->subDays(7); break;
            case 'weekly': $start = Carbon::now()->subWeeks(4); break;
            case 'monthly': $start = Carbon::now()->subMonths(6); break;
            case 'quarterly': $start = Carbon::now()->subMonths(12); break;
            case 'yearly': $start = Carbon::now()->subYears(3); break;
            default: $start = Carbon::now()->subMonths(6);
        }
        return ['start' => $start, 'end' => $end];
    }
    
    private function calculateRecommendedSpend()
    {
        $avgCampaignSpend = DB::table('campaigns')->avg('spend') ?? 500;
        $topPerformingSpend = DB::table('campaigns')->where('roi', '>', 100)->avg('spend') ?? 1000;
        return round(($avgCampaignSpend + $topPerformingSpend) / 2, 2);
    }
    
    private function getBestCitiesForBoost()
    {
        return DB::table('orders')
            ->select('shipping_city', DB::raw('COUNT(*) as order_count'))
            ->whereNotNull('shipping_city')
            ->groupBy('shipping_city')
            ->orderByDesc('order_count')
            ->limit(5)
            ->pluck('shipping_city');
    }
    
    private function getBestTiming()
    {
        return DB::table('orders')
            ->select(DB::raw('DAYNAME(created_at) as day'), DB::raw('HOUR(created_at) as hour'), DB::raw('COUNT(*) as count'))
            ->groupBy('day', 'hour')
            ->orderByDesc('count')
            ->limit(3)
            ->get();
    }
    
    private function getGrowthRecommendations()
    {
        return [
            'Increase ad spend on top performing categories',
            'Launch retention campaign for high-value customers',
            'Optimize checkout flow to reduce drop-offs'
        ];
    }
    
    private function getProductSellerSuggestions()
    {
        return DB::table('products')
            ->select('products.name', 'sellers.name', DB::raw('COUNT(order_items.id) as sales_count'))
            ->join('sellers', 'products.created_by_id', '=', 'sellers.id')
            ->leftJoin('order_items', 'products.id', '=', 'order_items.product_id')
            ->groupBy('products.id', 'products.name', 'sellers.name')
            ->orderByDesc('sales_count')
            ->limit(5)
            ->get();
    }
    
    private function getRiskAlerts()
    {
        $alerts = [];
        
        // Check for sellers with high refund rates
        $highRefundSellers = DB::table('sellers')
            ->join('refunds', 'sellers.id', '=', 'refunds.seller_id')
            ->select('sellers.name', DB::raw('COUNT(refunds.id) as refund_count'))
            ->where('refunds.created_at', '>=', Carbon::now()->subDays(30))
            ->groupBy('sellers.id', 'sellers.name')
            ->having('refund_count', '>', 10)
            ->get();
            
        foreach($highRefundSellers as $seller) {
            $alerts[] = "⚠️ High refund rate for seller: {$seller->business_name}";
        }
        
        return $alerts;
    }
}