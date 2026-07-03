<?php

namespace App\Http\Controllers\vtravel;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SpinHistory;
use App\Models\Reward;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class SpinController extends Controller
{
    public function spin(Request $request)
    {
        if (!Auth::check()) {
            return response()->json(['success' => false, 'message' => 'Please login to spin and win!', 'needs_login' => true]);
        }

        $userId = Auth::id();
        
        $spinCount = SpinHistory::where('user_id', $userId)->count();

        if ($spinCount >= 3) {
            return response()->json(['success' => false, 'message' => 'Your spin limit has expired. Better luck next time!', 'limit_reached' => true]);
        }

        $hasWon = SpinHistory::where('user_id', $userId)
            ->whereIn('outcome', ['reward_5', 'reward_3', 'coupon', 'campaign_offer'])
            ->exists();
        
        $isWin = false;
        
        if ($hasWon) {
            $isWin = false;
        } else {
            if ($spinCount == 2) {
                $isWin = true;
            } else {
                $isWin = (rand(1, 100) <= 33);
            }
        }

        if ($isWin) {
            $winSegments = [0, 2, 3, 4];
            $segment = $winSegments[array_rand($winSegments)];
            
            if ($segment == 0) {
                $outcomeStr = 'reward_5';
                Reward::create([
                    'customer_id' => $userId,
                    'type' => 'points',
                    'points' => 5,
                    'amount' => 0.50,
                    'description' => 'Won 5 points in Spin and Win',
                    'status' => 'active',
                    'expiry_date' => now()->addDays(30)
                ]);
                $msg = 'Congratulations! You won 5 Reward Points!';
            } elseif ($segment == 2) {
                $outcomeStr = 'reward_3';
                Reward::create([
                    'customer_id' => $userId,
                    'type' => 'points',
                    'points' => 3,
                    'amount' => 0.30,
                    'description' => 'Won 3 points in Spin and Win',
                    'status' => 'active',
                    'expiry_date' => now()->addDays(30)
                ]);
                $msg = 'Congratulations! You won 3 Reward Points!';
            } elseif ($segment == 3) {
                $outcomeStr = 'coupon';
                $coupon = DB::table('coupons')->where('status', 'active')->first();
                if ($coupon) {
                    $msg = 'Congratulations! You won a coupon: ' . $coupon->discount_code;
                } else {
                    $msg = 'Congratulations! You won a mystery coupon!';
                }
            } elseif ($segment == 4) {
                $outcomeStr = 'campaign_offer';
                $msg = 'Congratulations! You won an exclusive 20% Off Campaign Offer!';
            }
        } else {
            $segment = 1;
            $outcomeStr = 'better_luck';
            $msg = 'Better luck next time!';
        }

        SpinHistory::create([
            'user_id' => $userId,
            'outcome' => $outcomeStr
        ]);

        return response()->json([
            'success' => true,
            'segment' => $segment,
            'message' => $msg
        ]);
    }
}
