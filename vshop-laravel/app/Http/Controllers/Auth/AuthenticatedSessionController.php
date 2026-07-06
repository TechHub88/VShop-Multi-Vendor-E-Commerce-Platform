<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthenticatedSessionController extends Controller
{
    /**
     * 🔹 Helper for Activity Logging
     */
    private function logActivity($type, $email, $user = null)
    {
        try {
            $name = $user ? ($user->name ?? 'User') : $email;
            $desc = $name;
            if (str_contains($type, 'failed')) {
                $desc .= ' failed to log in';
            } else {
                $desc .= ' logged in';
            }

            \App\Models\ActivityLog::create([
                'user_id' => $user ? $user->id : null,
                'activity_type' => $type,
                'description' => $desc,
                'remarks' => 'IP: ' . request()->ip() . ', User Agent: ' . request()->userAgent(),
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent()
            ]);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Login activity log failed: ' . $e->getMessage());
        }
    }
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

        // 1. Check if it's a main Admin User (web guard)
        if (Auth::guard('web')->validate($credentials)) {
            $user = \App\Models\User::where('email', $request->email)->first();
            if ($user) {
                // If global 2FA is enabled, use standard 2FA flow
                $is2faEnabled = \App\Models\Setting::getByKey('security_2fa', false);
                if ($is2faEnabled) {
                    $otp = rand(100000, 999999);
                    session([
                        '2fa_temp_user_id' => $user->id,
                        '2fa_temp_user_type' => 'web',
                        '2fa_otp' => $otp,
                        '2fa_otp_expires_at' => now()->addMinutes(15),
                        '2fa_remember' => $remember
                    ]);
                    return $request->ajax() 
                        ? response()->json(['redirect' => route('admin.2fa')])
                        : redirect()->route('admin.2fa');
                } else {
                    // No 2FA -> Direct login
                    Auth::guard('web')->login($user, $remember);
                    $this->logActivity('admin_login', $request->email, $user);
                    $request->session()->regenerate();
                    return $request->ajax()
                        ? response()->json(['redirect' => url('/dashboard/index')])
                        : redirect()->intended('/dashboard/index');
                }
            }
        }

        // 2. Check if it's an Admin Staff (ALWAYS require OTP, return JSON for AJAX)
        if (Auth::guard('admin_staff')->validate($credentials)) {
            $staff = \App\Models\AdminStaff::where('email', $request->email)->first();
            if ($staff) {
                if ($staff->status !== 'Active') {
                    $msg = 'Your staff account is ' . $staff->status . '. Please contact admin.';
                    return $request->ajax()
                        ? response()->json(['message' => $msg], 422)
                        : back()->withErrors(['email' => $msg])->withInput();
                }

                // Generate 4-digit OTP for staff
                $otp = rand(1000, 9999);
                session([
                    'staff_temp_id' => $staff->id,
                    'staff_otp' => $otp,
                    'staff_otp_expires' => now()->addMinutes(15),
                    'staff_remember' => $remember
                ]);

                // Send Email via pure PHP mail()
                try {
                    $to = $staff->email;
                    $subject = 'VSHOP Admin - Login Verification OTP';
                    $html = "<h3>VSHOP Admin Login</h3><p>Dear {$staff->name},</p><p>Your login OTP is: <b><span style='font-size:24px'>{$otp}</span></b></p><p>This is valid for 15 minutes.</p>";
                    
                    $fromEmail = env('MAIL_FROM_ADDRESS', 'no-reply@vridheewellness.com');
                    $fromName  = env('MAIL_FROM_NAME', 'VSHOP Admin');
                    ini_set('sendmail_from', $fromEmail);
                    
                    // ── Build multipart MIME ─────────────────────────────────────────
                    $boundary = '----VSHOP_OTP_' . md5(uniqid(rand(), true));

                    $body  = "--{$boundary}\r\n";
                    $body .= "Content-Type: text/plain; charset=UTF-8\r\n";
                    $body .= "Content-Transfer-Encoding: 8bit\r\n\r\n";
                    $body .= strip_tags(str_replace(['<br>', '</p>'], "\r\n", $html)) . "\r\n";
                    $body .= "--{$boundary}\r\n";
                    $body .= "Content-Type: text/html; charset=UTF-8\r\n";
                    $body .= "Content-Transfer-Encoding: 8bit\r\n\r\n";
                    $body .= $html . "\r\n";
                    $body .= "--{$boundary}--";

                    $headers  = "MIME-Version: 1.0\r\n";
                    $headers .= "Content-Type: multipart/alternative; boundary=\"{$boundary}\"\r\n";
                    $headers .= "From: {$fromName} <{$fromEmail}>\r\n";
                    $headers .= "Reply-To: {$fromEmail}\r\n";
                    $headers .= "Return-Path: {$fromEmail}\r\n";
                    $headers .= "X-Mailer: VSHOP/PHP-" . phpversion() . "\r\n";
                    $headers .= "X-Priority: 1\r\n";
                    
                    @mail($to, $subject, $body, $headers, "-f {$fromEmail}");
                } catch (\Exception $e) {
                    \Illuminate\Support\Facades\Log::error('Staff OTP Email Failed: ' . $e->getMessage());
                    // We can still proceed, maybe log it or show a specific dev OTP if needed
                }

                if ($request->ajax()) {
                    return response()->json([
                        'otp_required' => true,
                        'message' => 'OTP has been sent to your registered email.',
                        'dev_otp' => env('APP_ENV') === 'local' ? $otp : null // Optional: remove in prod
                    ]);
                } else {
                    // Fallback if somehow not AJAX (though we will update the view)
                    return redirect()->back()->with('status', 'OTP sent to email. Please verify.');
                }
            }
        }

        // Check if the user exists but failed password, or just log generally
        $user = \App\Models\User::where('email', $request->email)->first();
        if ($user) {
            $this->logActivity('admin_login_failed', $request->email, $user);
        } else {
            $staff = \App\Models\AdminStaff::where('email', $request->email)->first();
            if ($staff) {
                $this->logActivity('staff_login_failed', $request->email, $staff);
            } else {
                $this->logActivity('login_failed', $request->email, null);
            }
        }

        $errorMsg = 'Invalid email or password';
        return $request->ajax()
            ? response()->json(['message' => $errorMsg], 422)
            : back()->withErrors(['email' => $errorMsg])->withInput();
    }

    /**
     * 🔹 Verify Staff OTP (AJAX)
     */
    public function verifyStaffOtp(Request $request)
    {
        $request->validate([
            'otp' => 'required|string|size:4'
        ]);

        if (!session()->has('staff_temp_id')) {
            return response()->json(['message' => 'Session expired. Please login again.'], 422);
        }

        $expectedOtp = session('staff_otp');
        $expiresAt = session('staff_otp_expires');

        if (!$expectedOtp || !$expiresAt || now()->greaterThan($expiresAt)) {
            return response()->json(['message' => 'OTP has expired. Please resend.'], 422);
        }

        if ($request->otp !== (string)$expectedOtp) {
            return response()->json(['message' => 'Incorrect OTP code.'], 422);
        }

        // OTP is correct
        $staffId = session('staff_temp_id');
        $remember = session('staff_remember', false);
        $staff = \App\Models\AdminStaff::find($staffId);

        session()->forget(['staff_temp_id', 'staff_otp', 'staff_otp_expires', 'staff_remember']);

        Auth::guard('admin_staff')->loginUsingId($staffId, $remember);
        if ($staff) {
            $this->logActivity('staff_login', $staff->email, $staff);
        }
        $request->session()->regenerate();

        return response()->json([
            'redirect' => url('/dashboard/index')
        ]);
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
            $user = \App\Models\User::find($userId);
            if ($user) $this->logActivity('admin_login', $user->email, $user);
        } else if ($userType === 'admin_staff') {
            Auth::guard('admin_staff')->loginUsingId($userId, $remember);
            $staff = \App\Models\AdminStaff::find($userId);
            if ($staff) $this->logActivity('staff_login', $staff->email, $staff);
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
     * 🔹 Switch Panel (Vshop / Vtravel / Vcampus / Vedge)
     */
    public function switchPanel($type)
    {
        if (!in_array($type, ['product', 'travel', 'campus', 'edge', 'vcampus', 'vedge'])) {
            $type = 'product';
        }
        if ($type === 'vcampus') $type = 'campus';
        if ($type === 'vedge') $type = 'edge';

        // Retrieve current active panels from session
        $activePanels = session('admin_panels', ['product']);

        if (in_array($type, $activePanels)) {
            // Remove it if it exists and we have at least 1 other active panel
            if (count($activePanels) > 1) {
                $activePanels = array_diff($activePanels, [$type]);
            }
        } else {
            // Add it if it's not active
            $activePanels[] = $type;
        }

        session(['admin_panels' => array_values($activePanels)]);

        // For backward compatibility
        session(['admin_panel' => reset($activePanels) ?: 'product']);

        session()->save();

        // Return JSON for AJAX requests, redirect for normal requests
        if (request()->ajax() || request()->wantsJson() || request()->header('X-Requested-With') === 'XMLHttpRequest') {
            return response()->json([
                'success' => true,
                'activePanels' => array_values($activePanels),
            ]);
        }

        return back(); // stay on same page
    }
}
