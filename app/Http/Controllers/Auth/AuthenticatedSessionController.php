<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthenticatedSessionController extends Controller
{
    /**
     * 🔹 Show Login Page
     */
    public function create()
    {
        // Default panel = product
        if (!session()->has('admin_panel')) {
            session(['admin_panel' => 'product']);
        }

        return view('auth.login');
    }

    /**
     * 🔹 Handle Login (with Multi-guard & 2FA support)
     */
    public function store(Request $request)
    {
        // Basic validation
        $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        $credentials = $request->only('email', 'password');
        $remember = $request->boolean('remember');

        // Check if 2FA is enabled globally
        $is2faEnabled = \App\Models\Setting::getByKey('security_2fa', false);

        if ($is2faEnabled) {
            // Validate credentials for User (web guard)
            if (Auth::guard('web')->validate($credentials)) {
                $user = \App\Models\User::where('email', $request->email)->first();
                if ($user) {
                    $otp = rand(100000, 999999);
                    session([
                        '2fa_temp_user_id' => $user->id,
                        '2fa_temp_user_type' => 'web',
                        '2fa_otp' => $otp,
                        '2fa_otp_expires_at' => now()->addMinutes(15),
                        '2fa_remember' => $remember
                    ]);
                    return redirect()->route('admin.2fa');
                }
            }

            // Validate credentials for AdminStaff (admin_staff guard)
            if (Auth::guard('admin_staff')->validate($credentials)) {
                $staff = \App\Models\AdminStaff::where('email', $request->email)->first();
                if ($staff) {
                    if ($staff->status !== 'Active') {
                        return back()->withErrors(['email' => 'Your staff account is ' . $staff->status . '. Please contact admin.'])->withInput();
                    }
                    $otp = rand(100000, 999999);
                    session([
                        '2fa_temp_user_id' => $staff->id,
                        '2fa_temp_user_type' => 'admin_staff',
                        '2fa_otp' => $otp,
                        '2fa_otp_expires_at' => now()->addMinutes(15),
                        '2fa_remember' => $remember
                    ]);
                    return redirect()->route('admin.2fa');
                }
            }
        } else {
            // Direct login (No 2FA)
            // 1. Try User
            if (Auth::guard('web')->attempt($credentials, $remember)) {
                $request->session()->regenerate();
                return redirect()->intended('/dashboard/index');
            }

            // 2. Try AdminStaff
            if (Auth::guard('admin_staff')->attempt($credentials, $remember)) {
                $staff = Auth::guard('admin_staff')->user();
                if ($staff->status !== 'Active') {
                    Auth::guard('admin_staff')->logout();
                    return back()->withErrors(['email' => 'Your staff account is ' . $staff->status . '. Please contact admin.'])->withInput();
                }
                $request->session()->regenerate();
                return redirect()->intended('/dashboard/index');
            }
        }

        return back()->withErrors([
            'email' => 'Invalid email or password'
        ])->withInput();
    }

    /**
     * 🔹 Show 2FA Form
     */
    public function show2faForm()
    {
        if (!session()->has('2fa_temp_user_id') || !session()->has('2fa_temp_user_type')) {
            return redirect()->route('admin.login');
        }

        return view('auth.2fa');
    }

    /**
     * 🔹 Verify 2FA
     */
    public function verify2fa(Request $request)
    {
        $request->validate([
            'otp' => 'required|string|size:6'
        ]);

        if (!session()->has('2fa_temp_user_id') || !session()->has('2fa_temp_user_type')) {
            return redirect()->route('admin.login');
        }

        $expectedOtp = session('2fa_otp');
        $expiresAt = session('2fa_otp_expires_at');

        if (!$expectedOtp || !$expiresAt || now()->greaterThan($expiresAt)) {
            return redirect()->route('admin.login')->withErrors(['email' => 'OTP has expired or is invalid. Please login again.']);
        }

        if ($request->input('otp') !== (string)$expectedOtp) {
            return back()->withErrors(['otp' => 'The entered OTP code is incorrect.']);
        }

        // OTP is correct! Clear temp session keys
        $userId = session('2fa_temp_user_id');
        $userType = session('2fa_temp_user_type');
        $remember = session('2fa_remember', false);

        session()->forget(['2fa_temp_user_id', '2fa_temp_user_type', '2fa_otp', '2fa_otp_expires_at', '2fa_remember']);

        if ($userType === 'web') {
            Auth::guard('web')->loginUsingId($userId, $remember);
        } else if ($userType === 'admin_staff') {
            Auth::guard('admin_staff')->loginUsingId($userId, $remember);
        }

        $request->session()->regenerate();

        return redirect()->intended('/dashboard/index');
    }

    /**
     * 🔹 Logout
     */
    public function destroy(Request $request)
    {
        Auth::guard('web')->logout();
        Auth::guard('admin_staff')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        // Always go to single login page
        return redirect('/admin/login');
    }

    /**
     * 🔹 Switch Panel (Vshop / Vtravel)
     */
    public function switchPanel($type)
    {
        if (!in_array($type, ['product','travel'])) {
            $type = 'product';
        }

        session(['admin_panel' => $type]);

        return back(); // stay on same page
    }
}