<?php

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ReferralHistory;
use Illuminate\Support\Facades\Auth;

class SellerReferralController extends Controller
{
    public function history()
    {
        $seller = Auth::guard('seller')->user();
        
        $referrals = ReferralHistory::with(['referrer', 'buyer', 'order.items.product'])
            ->where('seller_id', $seller->id)
            ->latest()
            ->paginate(20);
            
        $totalCommission = ReferralHistory::where('seller_id', $seller->id)
            ->where('status', 'paid')
            ->sum('amount');
            
        return view('vshop.seller.referrals.history', compact('referrals', 'totalCommission'));
    }
}
