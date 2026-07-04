<?php
// app/Http/Controllers/Admin/AlertController.php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Seller;
use App\Models\Product;
use App\Models\Grievance;
use Illuminate\Support\Facades\DB;

class AlertController extends Controller
{
    /**
     * Get all alert counts for topbar dropdown
     * Route: admin.alerts.counts
     */
    public function getAlertCounts(Request $request)
    {
        try {
            // 1. Pending KYC from sellers table
            $kycPending = Seller::where('kyc_status', 'pending')
                ->orWhere('is_verified', 0)
                ->orWhere('status', 'pending')
                ->count();
            
            // 2. Product verification pending from products table
            $productPending = Product::where('status', 'pending')
                ->orWhereNull('status')
                ->count();
            
            // 3. High return products from order_returns table
            $highReturnProducts = DB::table('order_returns')
                ->whereIn('status', ['pending', 'requested', 'initiated'])
                ->count();
            
            // 4. Grievance tickets pending from grievances table
            $grievancePending = Grievance::whereIn('status', ['open', 'in_progress', 'pending'])
                ->count();
            
            // Recent data for dropdown preview
            $recentKYC = Seller::where(function($q) {
                    $q->where('kyc_status', 'pending')
                      ->orWhere('is_verified', 0)
                      ->orWhere('status', 'pending');
                })
                ->select('id', 'name', 'email', 'mobile', 'created_at')
                ->latest()
                ->limit(3)
                ->get();
            
            $recentProducts = Product::where(function($q) {
                    $q->where('status', 'pending')->orWhereNull('status');
                })
                ->select('id', 'name', 'product_code', 'created_at')
                ->with('seller:id,name')
                ->latest()
                ->limit(3)
                ->get();
            
            $recentReturns = DB::table('order_returns')
                ->select('id', 'order_id', 'reason', 'status', 'created_at')
                ->whereIn('status', ['pending', 'requested'])
                ->latest()
                ->limit(3)
                ->get();
            
            $recentGrievances = Grievance::with(['seller:id,name', 'buyer:id,name'])
                ->whereIn('status', ['open', 'in_progress', 'pending'])
                ->select('id', 'subject', 'priority', 'status', 'created_at')
                ->latest()
                ->limit(3)
                ->get();
            
            return response()->json([
                'success' => true,
                'data' => [
                    'kyc_pending' => $kycPending,
                    'product_pending' => $productPending,
                    'high_return_products' => $highReturnProducts,
                    'grievance_pending' => $grievancePending,
                    'recent' => [
                        'kyc' => $recentKYC,
                        'products' => $recentProducts,
                        'returns' => $recentReturns,
                        'grievances' => $recentGrievances,
                    ]
                ]
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'data' => [
                    'kyc_pending' => 0,
                    'product_pending' => 0,
                    'high_return_products' => 0,
                    'grievance_pending' => 0,
                ]
            ]);
        }
    }
    
    /**
     * Get KYC pending sellers list
     * Route: admin.alerts.kyc
     */
    public function getKycPending(Request $request)
    {
        $sellers = Seller::where(function($q) {
                $q->where('kyc_status', 'pending')
                  ->orWhere('is_verified', 0)
                  ->orWhere('status', 'pending');
            })
            ->orderBy('created_at', 'desc')
            ->paginate(20);
        
        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'data' => $sellers
            ]);
        }
        
        return view('admin.alerts.kyc-pending', compact('sellers'));
    }
    
    /**
     * Get product verification pending list
     * Route: admin.alerts.products
     */
    public function getProductsPending(Request $request)
    {
        $products = Product::where(function($q) {
                $q->where('status', 'pending')->orWhereNull('status');
            })
            ->with('seller')
            ->orderBy('created_at', 'desc')
            ->paginate(20);
        
        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'data' => $products
            ]);
        }
        
        return view('admin.alerts.products-pending', compact('products'));
    }
    
    /**
     * Get high return products list
     * Route: admin.alerts.returns
     */
    public function getHighReturns(Request $request)
    {
        $returns = DB::table('order_returns')
            ->select(
                'order_returns.*',
                'products.name as product_name',
                'products.product_code',
                'orders.order_number',
                'sellers.name as seller_name'
            )
            ->leftJoin('products', 'order_returns.product_id', '=', 'products.id')
            ->leftJoin('orders', 'order_returns.order_id', '=', 'orders.id')
            ->leftJoin('sellers', 'products.created_by_id', '=', 'sellers.id')
            ->whereIn('order_returns.status', ['pending', 'requested', 'initiated'])
            ->orderBy('order_returns.created_at', 'desc')
            ->paginate(20);
        
        $stats = [
            'total_pending' => DB::table('order_returns')->whereIn('status', ['pending', 'requested'])->count(),
            'total_approved' => DB::table('order_returns')->where('status', 'approved')->count(),
            'total_rejected' => DB::table('order_returns')->where('status', 'rejected')->count(),
        ];
        
        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'data' => $returns,
                'stats' => $stats
            ]);
        }
        
        return view('admin.alerts.returns-high', compact('returns', 'stats'));
    }
    
    /**
     * Get grievances pending list
     * Route: admin.alerts.grievances
     */
    public function getGrievancesPending(Request $request)
    {
        $grievances = Grievance::with(['seller', 'buyer'])
            ->whereIn('status', ['open', 'in_progress', 'pending'])
            ->orderBy('priority', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate(20);
        
        $priorityCounts = [
            'high' => Grievance::whereIn('status', ['open', 'in_progress', 'pending'])->where('priority', 'high')->count(),
            'medium' => Grievance::whereIn('status', ['open', 'in_progress', 'pending'])->where('priority', 'medium')->count(),
            'low' => Grievance::whereIn('status', ['open', 'in_progress', 'pending'])->where('priority', 'low')->count(),
        ];
        
        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'data' => $grievances,
                'priority_counts' => $priorityCounts
            ]);
        }
        
        return view('admin.alerts.grievances-pending', compact('grievances', 'priorityCounts'));
    }
    
    /**
     * Get single alert count for auto-refresh
     * Route: admin.alerts.count/{type}
     */
    public function getSingleCount($type, Request $request)
    {
        $count = 0;
        
        switch ($type) {
            case 'kyc':
                $count = Seller::where('kyc_status', 'pending')
                    ->orWhere('is_verified', 0)
                    ->orWhere('status', 'pending')
                    ->count();
                break;
            case 'product':
                $count = Product::where('status', 'pending')
                    ->orWhereNull('status')
                    ->count();
                break;
            case 'return':
                $count = DB::table('order_returns')
                    ->whereIn('status', ['pending', 'requested'])
                    ->count();
                break;
            case 'grievance':
                $count = Grievance::whereIn('status', ['open', 'in_progress', 'pending'])
                    ->count();
                break;
        }
        
        return response()->json([
            'success' => true,
            'type' => $type,
            'count' => $count
        ]);
    }
    
    /**
     * Dismiss/acknowledge an alert (optional - update a flag in existing tables)
     * Route: admin.alerts.dismiss
     */
    public function dismissAlert(Request $request)
    {
        $request->validate([
            'type' => 'required|in:kyc,product,return,grievance',
            'id' => 'required|integer'
        ]);
        
        try {
            // You can add a 'alert_dismissed_at' column to your existing tables
            // or just return success without storing (for UI only)
            
            // Option 1: If you added dismissed_at column to tables
            switch ($request->type) {
                case 'kyc':
                    Seller::where('id', $request->id)->update(['alert_dismissed_at' => now()]);
                    break;
                case 'product':
                    Product::where('id', $request->id)->update(['alert_dismissed_at' => now()]);
                    break;
                case 'return':
                    DB::table('order_returns')->where('id', $request->id)->update(['alert_dismissed_at' => now()]);
                    break;
                case 'grievance':
                    Grievance::where('id', $request->id)->update(['alert_dismissed_at' => now()]);
                    break;
            }
            
            return response()->json(['success' => true]);
            
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }
}