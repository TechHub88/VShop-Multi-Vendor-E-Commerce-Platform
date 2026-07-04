<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Customer;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use App\Mail\VshopOtpMail;

class EmployeeLoginController extends Controller
{
    // ========== LOGIN METHODS ==========
    
    // Show VTRAVEL Login Form
    public function showVtravelLogin()
    {
        return view('vtravel.auth.login');
    }

    // Show VSHOP Login Form
    public function showVshopLogin()
    {
        return view('vshop.vshop-login');
    }

    // VTRAVEL Login - Only travel type
    public function vtravelLogin(Request $request)
    {
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::guard('customer')->attempt([
            'email' => $request->email,
            'password' => $request->password,
            'type' => 'travel',
            'status' => 'approved'
        ])) {
            $request->session()->regenerate();
            return redirect()->route('vtravelhome');
        }

        return back()->with('error', 'Invalid credentials or account not approved');
    }

    // VSHOP Login - Only product type
    public function vshopLogin(Request $request)
    {
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::guard('customer')->attempt([
            'email' => $request->email,
            'password' => $request->password,
            'type' => 'product',
            'status' => 'approved'
        ])) {
            $request->session()->regenerate();
            return redirect()->route('home');
        }

        return back()->with('error', 'Invalid credentials or account not approved');
    }

    // ========== REGISTER METHODS ==========
    
    // Show VTRAVEL Register Form
    public function showVtravelRegister()
    {
        if (request()->has('ref')) {
            session(['referral_code' => request()->query('ref')]);
        }
        $clients = User::where('role', 'manager')->get();
        return view('vtravel.auth.register', compact('clients'));
    }

    // Show VSHOP Register Form
    public function showVshopRegister()
    {
        if (request()->has('ref')) {
            session(['referral_code' => request()->query('ref')]);
        }
        $clients = User::where('role', 'manager')->get();
        return view('vshop.vshop-register', compact('clients'));
    }

    // Generate OTP as 6-digit integer
    private function generateOtp(): int
    {
        // Ensure 6-digit OTP without leading zeros
        return rand(100000, 999999);
    }

   // VTRAVEL Register - Simple with auto login
public function vtravelRegister(Request $request)
{
    $request->validate([
        'name'     => 'required|string|max:255',
        'email'    => 'required|email|unique:customers,email',
        'phone'    => 'required|digits:10|unique:customers,phone',
        'password' => 'required|min:6|confirmed',
    ]);

    $refCode = $request->input('referral_code') ?: session('referral_code');
    $referredBy = null;
    if ($refCode) {
        $referrer = Customer::where('referral_code', $refCode)->first();
        if ($referrer) {
            $referredBy = $referrer->id;
        }
    }

    // Generate unique 4-digit referral code
    do {
        $myReferralCode = rand(1000, 9999);
    } while (Customer::where('referral_code', $myReferralCode)->exists());

    $customer = Customer::create([
        'name'     => $request->name,
        'email'    => $request->email,
        'phone'    => $request->phone,
        'password' => Hash::make($request->password),
        'type'     => 'travel',  // Fixed: travel type
        'status'   => 'approved', // Direct approved
        'referred_by' => $referredBy,
        'referral_code' => $myReferralCode,
    ]);

    if ($referredBy) {
        \App\Models\Reward::processReferralReward($customer);
    }

    // Auto login
    Auth::guard('customer')->login($customer);

    return redirect()->route('vtravelhome')->with('success', 'Registration successful!');
}

    // VSHOP Register - With Password + Email OTP
    public function vshopRegister(Request $request)
    {
        $request->validate([
            'name'      => 'required|string|max:255',
            'email'     => 'required|email|unique:customers,email',
            'phone'     => 'required|digits:10|unique:customers,phone',
            'password'  => 'required|min:6|confirmed',
        ]);

        $otp = $this->generateOtp();

        $refCode = $request->input('referral_code') ?: session('referral_code');
        $referredBy = null;
        if ($refCode) {
            $referrer = Customer::where('referral_code', $refCode)->first();
            if ($referrer) {
                $referredBy = $referrer->id;
            }
        }

        // Generate unique 4-digit referral code
        do {
            $myReferralCode = rand(1000, 9999);
        } while (Customer::where('referral_code', $myReferralCode)->exists());

        $customer = Customer::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'phone'    => $request->phone,
            'password' => Hash::make($request->password),
            'type'     => 'product',
            'status'   => 'not applied',
            'otp'      => $otp,
            'otp_generated_at' => now(),
            'referred_by' => $referredBy,
            'referral_code' => $myReferralCode,
        ]);

        // Send OTP via PHP mail()
        $this->sendOtpEmail($customer->email, $customer->name, $otp, 'registration');

        return response()->json([
            'success' => true,
            'message' => 'OTP sent to your email. Please verify to complete registration.',
            'email'   => $customer->email,
            'phone'   => $customer->phone,
        ]);
    }

    // ========== OTP VERIFICATION ==========
    
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

        if (now()->diffInMinutes($customer->otp_generated_at) > 5) {
            return response()->json(['message' => 'OTP expired'], 400);
        }

        // Compare OTP as integers
        if ((int)$customer->otp === (int)$request->otp) {
            $customer->update([
                'status' => 'approved',
                'otp' => null,
                'otp_generated_at' => null
            ]);

            \App\Models\Reward::processReferralReward($customer);

            Auth::guard('customer')->login($customer);

            $redirect = $customer->type == 'travel' 
                ? route('vtravelhome') 
                : route('home');

            return response()->json([
                'message' => 'OTP Verified Successfully!',
                'redirect' => $redirect
            ]);
        }

        return response()->json(['message' => 'Invalid OTP'], 400);
    }

    // Resend OTP via Email
    public function resendOtp(Request $request)
    {
        $request->validate([
            'phone' => 'required|digits:10|exists:customers,phone',
        ]);

        $customer = Customer::where('phone', $request->phone)->first();

        $otp = $this->generateOtp();

        $customer->update([
            'otp'              => $otp,
            'otp_generated_at' => now(),
        ]);

        // Send new OTP via email
        $this->sendOtpEmail($customer->email, $customer->name, $otp, 'registration');

        return response()->json([
            'success' => true,
            'message' => 'New OTP sent to your email.',
            'email'   => $customer->email,
            'phone'   => $customer->phone,
        ]);
    }

    /**
     * Send OTP email — delegates to VshopOtpMail (pure php mail())
     */
    private function sendOtpEmail(string $to, string $name, int $otp, string $purpose = 'registration'): bool
    {
        return VshopOtpMail::send($to, $name, $otp, $purpose);
    }

    // ========== LOGOUT ==========


public function logout(Request $request)
{
    // Logout customer
    Auth::guard('customer')->logout();

    // Destroy session
    $request->session()->invalidate();
    $request->session()->regenerateToken();

    // Redirect to VShop Login
    return redirect()->route('vshoplogin');
}



// ========== FORGOT PASSWORD METHODS ==========

// Show Forgot Password Form
public function showForgotForm()
{
    return view('vshop.forgot-password');
}

// Send Reset OTP via AJAX (returns JSON)
public function sendResetOtp(Request $request)
{
    $request->validate([
        'email' => 'required|email',
    ]);

    $customer = Customer::where('email', $request->email)
                ->where('type', 'product')
                ->first();

    if (!$customer) {
        return response()->json([
            'success' => false,
            'message' => 'No VSHOP account found with this email.',
        ], 404);
    }

    // Generate OTP
    $otp = $this->generateOtp();
    $customer->update([
        'otp'              => $otp,
        'otp_generated_at' => now(),
    ]);

    // Send OTP via email
    $mailSent = $this->sendOtpEmail($customer->email, $customer->name, $otp, 'reset');

    if (!$mailSent) {
        return response()->json([
            'success' => false,
            'message' => 'Unable to send OTP email. Please contact support.',
        ], 500);
    }

    return response()->json([
        'success' => true,
        'message' => 'OTP sent to ' . $customer->email,
    ]);
}

// Verify Reset OTP via AJAX (returns JSON)
public function verifyResetOtp(Request $request)
{
    $request->validate([
        'email' => 'required|email',
        'otp'   => 'required|digits:6',
    ]);

    $customer = Customer::where('email', $request->email)
                ->where('type', 'product')
                ->first();

    if (!$customer) {
        return response()->json([
            'success' => false,
            'message' => 'Account not found.',
        ], 404);
    }

    // Check OTP expiration (5 minutes)
    if (!$customer->otp_generated_at || now()->diffInMinutes($customer->otp_generated_at) > 5) {
        return response()->json([
            'success' => false,
            'message' => 'OTP has expired. Please request a new one.',
        ], 400);
    }

    // Compare OTP as integers
    if ((int)$customer->otp !== (int)$request->otp) {
        return response()->json([
            'success' => false,
            'message' => 'Invalid OTP. Please try again.',
        ], 400);
    }

    // OTP verified — set session flags
    session([
        'reset_email'  => $customer->email,
        'otp_verified' => true,
    ]);

    // Clear OTP from DB (already verified)
    $customer->update([
        'otp' => null,
        'otp_generated_at' => null,
    ]);

    return response()->json([
        'success'  => true,
        'message'  => 'OTP verified successfully!',
        'redirect' => route('vshop.reset.form'),
    ]);
}

// Show Reset Password Form (only after OTP is verified)
public function showResetForm()
{
    if (!session('otp_verified') || !session('reset_email')) {
        return redirect()->route('vshop.forgot.form')
            ->with('error', 'Please verify OTP first.');
    }
    return view('vshop.reset-password');
}

// Reset Password (OTP already verified — only password update)
public function resetPassword(Request $request)
{
    $request->validate([
        'password' => 'required|min:6|confirmed',
    ]);

    $email = session('reset_email');
    if (!$email || !session('otp_verified')) {
        return redirect()->route('vshop.forgot.form')
            ->with('error', 'Session expired. Please verify OTP again.');
    }

    $customer = Customer::where('email', $email)
                ->where('type', 'product')
                ->first();

    if (!$customer) {
        return redirect()->route('vshop.forgot.form')
            ->with('error', 'Account not found.');
    }

    // Update password
    $customer->update([
        'password' => Hash::make($request->password),
    ]);

    // Auto-login the customer after password reset
    Auth::guard('customer')->login($customer);

    // Clear session
    session()->forget(['reset_email', 'otp_verified']);

    return redirect()->route('home')
                     ->with('success', 'Password reset successful. You are now logged in.');
}
}
