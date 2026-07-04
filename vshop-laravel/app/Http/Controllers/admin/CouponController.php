<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Coupon;
use App\Models\Partner;
use App\Models\Category;
use App\Models\Product;
use App\Models\Seller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CouponController extends Controller
{
    /**
     * Display coupons based on user type
     */
    public function index(Request $request)
    {
        $user = auth()->user();
        $userType = $user->type;
        
        $query = Coupon::query();
        
        // Filter by user type for restricted admins
        if ($userType == 'product') {
            $query->where('type', 'product');
        } elseif ($userType == 'travel') {
            $query->where('type', 'travel');
        }
        
        // Stats
        $stats = [
            'total' => (clone $query)->count(),
            'active' => (clone $query)->where('status', 'active')->count(),
            'scheduled' => (clone $query)->where('status', 'inactive')->where('start_date', '>', now())->count(),
            'used_today' => 0, // Placeholder
            'revenue_generated' => 0, // Placeholder
            'expired' => (clone $query)->where('expiry_date', '<', now())->count(),
        ];
        
        // Tab Filtering
        $tab = $request->get('tab', 'all');
        if ($tab == 'active') {
            $query->where('status', 'active');
        } elseif ($tab == 'scheduled') {
            $query->where('status', 'inactive')->where('start_date', '>', now());
        } elseif ($tab == 'expired') {
            $query->where('expiry_date', '<', now());
        }

        // Apply filters
        if ($request->filled('type_filter') && $request->type_filter !== 'All') {
            $query->where('discount_type', $request->type_filter);
        }
        if ($request->filled('status_filter') && $request->status_filter !== 'All') {
            $query->where('status', $request->status_filter);
        }
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('discount_code', 'like', "%{$search}%")
                  ->orWhere('name', 'like', "%{$search}%");
            });
        }
        
        $coupons = $query->latest()->paginate(10);
        
        return view('admin.coupon.index', compact('coupons', 'stats', 'tab'));
    }

    /**
     * Show form to create new coupon
     */
    public function create()
    {
        $user = auth()->user();
        $userType = $user->type;
        
        $partners = Partner::all();
        $categories = Category::all();
        $products = Product::all();
        $sellers = Seller::all();
        
        return view('admin.coupon.create', compact('userType', 'partners', 'categories', 'products', 'sellers'));
    }

    /**
     * Store a new coupon
     */
    public function store(Request $request)
    {
        $user = auth()->user();
        $userType = $user->type;
        
        $type = $request->type ?? 'all';
        if ($userType == 'product' || $userType == 'travel') {
            $type = $userType; 
        }
        
        // Validate request
        $validationRules = [
            'discount_code' => 'required|unique:coupons,discount_code|string|max:255',
            'name' => 'nullable|string|max:255',
            'coupon_amount' => 'required|numeric|min:0',
            'discount_type' => 'required|in:percentage,fixed',
            'start_date' => 'nullable|date',
            'expiry_date' => 'required|date|after_or_equal:start_date',
            'minimum_spend' => 'nullable|numeric|min:0',
            'max_discount_amount' => 'nullable|numeric|min:0',
            'status' => 'required|in:active,inactive',
            'usage_limit' => 'required|integer|min:1',
            'per_user_limit' => 'nullable|integer|min:1',
            'applicable_on' => 'nullable|in:all,categories,products,sellers',
            'applicable_ids' => 'nullable|array',
            'apply_on' => 'nullable|in:vshop,vtravel,both',
        ];
        
        $request->validate($validationRules);

        // Create coupon
        Coupon::create([
            'discount_code' => strtoupper($request->discount_code),
            'name' => $request->name,
            'type' => $type,
            'coupon_amount' => $request->coupon_amount,
            'discount_type' => $request->discount_type,
            'start_date' => $request->start_date,
            'expiry_date' => $request->expiry_date,
            'minimum_spend' => $request->minimum_spend ?? 0,
            'max_discount_amount' => $request->max_discount_amount,
            'status' => $request->status,
            'usage_limit' => $request->usage_limit,
            'per_user_limit' => $request->per_user_limit,
            'applicable_on' => $request->applicable_on ?? 'all',
            'applicable_ids' => $request->applicable_ids,
            'apply_on' => $request->apply_on ?? 'both',
            'stackable' => $request->has('stackable'),
            'show_in_public' => $request->has('show_in_public'),
            'used_count' => 0,
        ]);

        return redirect()->route('admin.coupons.index')
            ->with('success', 'Coupon Created Successfully');
    }

    /**
     * Show form to edit coupon
     */
    public function edit($id)
    {
        $user = auth()->user();
        $userType = $user->type;
        
        $coupon = Coupon::findOrFail($id);
        
        // Check if user has permission to edit this coupon
        if (($userType == 'product' && $coupon->type != 'product') || 
            ($userType == 'travel' && $coupon->type != 'travel')) {
            return redirect()->route('admin.coupons.index')
                ->with('error', 'You do not have permission to edit this coupon.');
        }
        
        $partners = Partner::all();
        $categories = Category::all();
        $products = Product::all();
        $sellers = Seller::all();
        
        return view('admin.coupon.edit', compact('coupon', 'userType', 'partners', 'categories', 'products', 'sellers'));
    }

    /**
     * Update coupon
     */
    public function update(Request $request, $id)
    {
        $coupon = Coupon::findOrFail($id);
        $user = auth()->user();
        $userType = $user->type;
        
        // Check if user has permission to update this coupon
        if (($userType == 'product' && $coupon->type != 'product') || 
            ($userType == 'travel' && $coupon->type != 'travel')) {
            return redirect()->route('admin.coupons.index')
                ->with('error', 'You do not have permission to update this coupon.');
        }
        
        // Determine type - product/travel admins can't change type
        $type = $coupon->type;
        if ($userType == 'admin') {
            $type = $request->type;
        }

        // Validate request
        $validationRules = [
            'discount_code' => 'required|unique:coupons,discount_code,' . $id . '|string|max:255',
            'coupon_amount' => 'required|numeric|min:0',
            'description' => 'nullable|string',
            'discount_type' => 'required|in:percentage,fixed',
            'start_date' => 'required|date',
            'expiry_date' => 'required|date|after:start_date',
            'minimum_spend' => 'nullable|numeric|min:0',
            'max_discount_amount' => 'nullable|numeric|min:0',
            'status' => 'required|in:active,inactive',
            'usage_limit' => 'required|integer|min:1',
            'coupon_type' => 'required|in:public,partner',
            'partner_id' => 'nullable|required_if:coupon_type,partner|exists:partners,id',
            'applicable_on' => 'nullable|in:all,categories,products,sellers',
            'applicable_ids' => 'nullable|array',
            'apply_on' => 'nullable|in:vshop,vtravel,both',
        ];
        
        // Only super admin can change type
        if ($userType == 'admin') {
            $validationRules['type'] = 'required|in:product,travel,all';
        }
        
        $request->validate($validationRules);

        // Update coupon
        $coupon->update([
            'discount_code' => strtoupper($request->discount_code),
            'type' => $type,
            'coupon_amount' => $request->coupon_amount,
            'coupon_type' => $request->coupon_type,
            'description' => $request->description,
            'discount_type' => $request->discount_type,
            'start_date' => $request->start_date,
            'expiry_date' => $request->expiry_date,
            'minimum_spend' => $request->minimum_spend ?? 0,
            'max_discount_amount' => $request->max_discount_amount,
            'status' => $request->status,
            'usage_limit' => $request->usage_limit,
            'partner_id' => $request->coupon_type == 'partner' ? $request->partner_id : null,
            'applicable_on' => $request->applicable_on ?? 'all',
            'applicable_ids' => $request->applicable_ids,
            'apply_on' => $request->apply_on ?? 'both',
        ]);

        $typeLabel = ucfirst($type);
        return redirect()->route('admin.coupons.index')
            ->with('success', $typeLabel . ' Coupon Updated Successfully');
    }

    /**
     * Delete coupon
     */
    public function destroy($id)
    {
        $coupon = Coupon::findOrFail($id);
        $user = auth()->user();
        $userType = $user->type;
        
        // Check if user has permission to delete this coupon
        if (($userType == 'product' && $coupon->type != 'product') || 
            ($userType == 'travel' && $coupon->type != 'travel')) {
            return back()->with('error', 'You do not have permission to delete this coupon.');
        }
        
        // Delete coupon
        $coupon->delete();

        return back()->with('success', 'Coupon Deleted Successfully');
    }

    /**
     * Toggle coupon status
     */
    public function toggleStatus(Request $request, $id)
    {
        $coupon = Coupon::findOrFail($id);
        $user = auth()->user();
        $userType = $user->type;
        
        // Check if user has permission to update this coupon
        if (($userType == 'product' && $coupon->type != 'product') || 
            ($userType == 'travel' && $coupon->type != 'travel')) {
            return response()->json([
                'success' => false,
                'message' => 'You do not have permission to update this coupon.'
            ], 403);
        }
        
        $request->validate([
            'status' => 'required|in:active,inactive',
        ]);

        $coupon->update([
            'status' => $request->status,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Coupon status updated successfully',
            'status' => $coupon->status,
        ]);
    }
}