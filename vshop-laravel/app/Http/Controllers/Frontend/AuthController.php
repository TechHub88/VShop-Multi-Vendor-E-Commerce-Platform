<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Customer;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;

class AuthController extends Controller
{
    // Show VTRAVEL Login Form
    public function showVtravelLogin()
    {
        return view('frontend.vtravel.auth.login');
    }

    // Show VSHOP Login Form
    public function showVshopLogin()
    {
        return view('frontend.vshop-login');
    }

    // Handle VTRAVEL Login
    public function vtravelLogin(Request $request)
    {
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::guard('customer')->attempt(array_merge(
            $request->only('email', 'password'), 
            ['status' => 'approved', 'type' => 'travel']
        ))) {
            $request->session()->regenerate();
            return redirect()->route('vtravelhome');
        }

        return redirect()->back()->with('error', 'Invalid credentials OR your account is not approved yet.');
    }

    // Handle VSHOP Login
    public function vshopLogin(Request $request)
    {
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::guard('customer')->attempt(array_merge(
            $request->only('email', 'password'), 
            ['status' => 'approved', 'type' => 'product']
        ))) {
            $request->session()->regenerate();
            return redirect()->route('vshop.home');
        }

        return redirect()->back()->with('error', 'Invalid credentials OR your account is not approved yet.');
    }

    // Show frontend Register Form
    public function showRegisterForm()
    {
        if (request()->has('ref')) {
            session(['referral_code' => request()->query('ref')]);
        }
        $clients = User::where('role', 'manager')->get();
        return view('frontend.register', compact('clients'));
    }

    // Show VTRAVEL Register Form
    public function showVtravelRegister()
    {
        if (request()->has('ref')) {
            session(['referral_code' => request()->query('ref')]);
        }
        $clients = User::where('role', 'manager')->get();
        return view('frontend.vtravel-register', compact('clients'));
    }

    // Show VSHOP Register Form
    public function showVshopRegister()
    {
        if (request()->has('ref')) {
            session(['referral_code' => request()->query('ref')]);
        }
        $clients = User::where('role', 'manager')->get();
        return view('frontend.vshop-register', compact('clients'));
    }

    // Register VTRAVEL Customer
    public function vtravelRegister(Request $request)
    {
        $request->validate([
            'client_id' => 'required|exists:users,id',
            'name'      => 'required|string|max:255',
            'email'     => 'required|email|unique:customers,email',
            'phone'     => 'required|digits:10|unique:customers,phone',
        ]);

        $refCode = $request->input('referral_code') ?: session('referral_code');
        $referredBy = null;
        if ($refCode) {
            $referrer = Customer::where('referral_code', $refCode)->first();
            if ($referrer) {
                $referredBy = $referrer->id;
            }
        }

        $customer = Customer::create([
            'user_id' => $request->client_id,
            'name'    => $request->name,
            'email'   => $request->email,
            'phone'   => $request->phone,
            'type'    => 'travel',
            'status'  => 'not applied',
            'referred_by' => $referredBy,
        ]);

        // Generate OTP
        $otp = rand(100000, 999999);

        $customer->update([
            'otp' => $otp,
            'otp_generated_at' => now(),
        ]);

        return response()->json([
            'message' => 'Registration successful.',
            'otp'     => $otp,
            'phone'   => $customer->phone,
        ]);
    }

    // Register VSHOP Customer
    public function vshopRegister(Request $request)
    {
        $request->validate([
            'client_id' => 'required|exists:users,id',
            'name'      => 'required|string|max:255',
            'email'     => 'required|email|unique:customers,email',
            'phone'     => 'required|digits:10|unique:customers,phone',
        ]);

        $refCode = $request->input('referral_code') ?: session('referral_code');
        $referredBy = null;
        if ($refCode) {
            $referrer = Customer::where('referral_code', $refCode)->first();
            if ($referrer) {
                $referredBy = $referrer->id;
            }
        }

        $customer = Customer::create([
            'user_id' => $request->client_id,
            'name'    => $request->name,
            'email'   => $request->email,
            'phone'   => $request->phone,
            'type'    => 'product',
            'status'  => 'not applied',
            'referred_by' => $referredBy,
        ]);

        // Generate OTP
        $otp = rand(100000, 999999);

        $customer->update([
            'otp' => $otp,
            'otp_generated_at' => now(),
        ]);

        return response()->json([
            'message' => 'Registration successful.',
            'otp'     => $otp,
            'phone'   => $customer->phone,
        ]);
    }

    // Verify OTP (common for both)
    public function verifyRegisterOtp(Request $request)
    {
        $request->validate([
            'phone' => 'required|digits:10|exists:customers,phone',
            'otp'   => 'required|digits:6',
        ]);

        $customer = Customer::where('phone', $request->phone)->first();

        if (!$customer) {
            return response()->json(['message' => 'Customer not found'], 404);
        }

        if ($customer->otp == $request->otp &&
            now()->diffInMinutes($customer->otp_generated_at) <= 5) {

            $customer->update([
                'status' => 'approved',
                'otp' => null,
                'otp_generated_at' => null
            ]);

            \App\Models\Reward::processReferralReward($customer);

            Auth::guard('customer')->login($customer);

            // Redirect based on type
            $redirect = $customer->type == 'travel' ? route('vtravelhome') : route('vshop.home');

            return response()->json([
                'message' => 'OTP Verified Successfully!',
                'redirect' => $redirect
            ]);
        }

        return response()->json([
            'message' => 'Invalid or expired OTP.'
        ], 400);
    }

    // Logout
    public function logout(Request $request)
    {
        Auth::guard('customer')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/');
    }
}