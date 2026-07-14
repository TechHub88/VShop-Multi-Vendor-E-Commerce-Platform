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
        $customer = \App\Models\Customer::where('email', $seller->email)->orWhere('phone', $seller->mobile)->first();
        $referrerId = $customer ? $customer->id : -1;

        $referrals = ReferralHistory::with(['referrer', 'buyer', 'order.items.product'])
            ->where('referrer_id', $referrerId)
            ->latest()
            ->paginate(20);
            
        $totalCommission = ReferralHistory::where('referrer_id', $referrerId)
            ->where('status', 'paid')
            ->sum('amount');
            
        return view('vshop.seller.referrals.history', compact('referrals', 'totalCommission'));
    }
}
