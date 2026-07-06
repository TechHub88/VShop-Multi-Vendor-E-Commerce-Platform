<?php

namespace App\Mail;

/**
 * SellerOtpMail — Simple PHP mail() based OTP sender for Seller Portal
 * Same pattern as VshopOtpMail — No Laravel Mail facade, no SMTP — pure php mail()
 */
class SellerOtpMail
{
    /**
     * Send OTP email using php mail()
     *
     * @param  string  $to       Recipient email
     * @param  string  $name     Recipient name
     * @param  int     $otp      4-digit OTP
     * @param  string  $purpose  'registration' | 'login' | 'reset'
     * @return bool
     */
    public static function send(string $to, string $name, int $otp, string $purpose = 'registration'): bool
    {
        $fromEmail = env('MAIL_FROM_ADDRESS', 'noreply@zelofx.com');
        $fromName  = env('MAIL_FROM_NAME', 'VSHOP Seller');

        // Ensure the envelope sender is set for PHP mail() on Windows/CPanel environments
        ini_set('sendmail_from', $fromEmail);

        // Purpose-specific content
        switch ($purpose) {
            case 'login':
                $subject      = 'VSHOP Seller - Login OTP Verification';
                $purposeTitle = 'Login Verification';
                $purposeDesc  = 'verify your identity and log in to your Seller account';
                break;
            case 'reset':
                $subject      = 'VSHOP Seller - Password Reset OTP';
                $purposeTitle = 'Password Reset';
                $purposeDesc  = 'reset your Seller account password';
                break;
            case 'registration':
            default:
                $subject      = 'VSHOP Seller - Registration OTP Verification';
                $purposeTitle = 'Email Verification';
                $purposeDesc  = 'verify your email and complete your Seller registration';
                break;
        }

        // ── HTML body ────────────────────────────────────────────────────
        $html = '<!DOCTYPE html>
<html>
<head><meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1.0"></head>
<body style="margin:0;padding:0;background:#f4f6fb;font-family:Arial,sans-serif;">
<table width="100%" cellpadding="0" cellspacing="0" style="background:#f4f6fb;padding:30px 0;">
<tr><td align="center">
<table width="560" cellpadding="0" cellspacing="0" style="background:#ffffff;border-radius:12px;overflow:hidden;">

  <!-- HEADER -->
  <tr>
    <td style="background:linear-gradient(135deg, #233d79, #2c7fc3);padding:28px 40px;text-align:center;">
      <h1 style="margin:0;color:#ffffff;font-size:26px;font-weight:700;letter-spacing:1px;">VSHOP SELLER</h1>
      <p style="margin:6px 0 0;color:#d0e4ff;font-size:14px;">' . htmlspecialchars($purposeTitle) . '</p>
    </td>
  </tr>

  <!-- BODY -->
  <tr>
    <td style="padding:35px 40px;">
      <p style="margin:0 0 12px;color:#333333;font-size:16px;">Dear <strong>' . htmlspecialchars($name) . '</strong>,</p>
      <p style="margin:0 0 25px;color:#555555;font-size:15px;line-height:1.6;">
        We received a request to ' . htmlspecialchars($purposeDesc) . '.<br>Use the OTP below to proceed:
      </p>

      <!-- OTP BOX -->
      <div style="text-align:center;margin:0 0 25px;">
        <div style="display:inline-block;background:#f0f6ff;border:2px dashed #233d79;border-radius:10px;padding:20px 50px;">
          <p style="margin:0;font-size:12px;color:#888888;letter-spacing:2px;text-transform:uppercase;">Your OTP</p>
          <p style="margin:10px 0 0;font-size:44px;font-weight:700;color:#233d79;letter-spacing:14px;">' . $otp . '</p>
        </div>
      </div>

      <!-- WARNING -->
      <div style="background:#fff8e1;border-left:4px solid #f59e0b;border-radius:4px;padding:12px 16px;margin-bottom:25px;">
        <p style="margin:0;color:#92400e;font-size:14px;">
          &#x23F1; This OTP is valid for <strong>5 minutes</strong> only. Do not share it with anyone.
        </p>
      </div>

      <p style="margin:0;color:#999999;font-size:13px;">
        If you did not request this, please ignore this email. Your account remains safe.
      </p>
    </td>
  </tr>

  <!-- FOOTER -->
  <tr>
    <td style="background:#f8f9fa;padding:18px 40px;text-align:center;border-top:1px solid #eeeeee;">
      <p style="margin:0;color:#aaaaaa;font-size:12px;">
        &copy; 2025 VSHOP Seller Portal &mdash;
        <a href="https://vshop.zelofx.com/seller" style="color:#233d79;text-decoration:none;">vshop.zelofx.com/seller</a>
      </p>
      <p style="margin:5px 0 0;color:#cccccc;font-size:11px;">This is an automated email. Please do not reply.</p>
    </td>
  </tr>

</table>
</td></tr>
</table>
</body>
</html>';

        // ── Plain text fallback ──────────────────────────────────────────
        $plain = "Dear {$name},\r\n\r\n"
               . "Your VSHOP Seller {$purposeTitle} OTP is: {$otp}\r\n\r\n"
               . "This OTP is valid for 5 minutes only.\r\n"
               . "Do NOT share this OTP with anyone.\r\n\r\n"
               . "If you did not request this, please ignore this email.\r\n\r\n"
               . "Regards,\r\nVSHOP Seller Support\r\nhttps://vshop.zelofx.com/seller";

        // ── Send OTP via pure PHP mail() ───────────────────────
        $headers  = "MIME-Version: 1.0\r\n";
        $headers .= "Content-Type: text/html; charset=UTF-8\r\n";
        $headers .= "From: {$fromName} <{$fromEmail}>\r\n";
        $headers .= "Reply-To: {$fromEmail}\r\n";

        $mailSent = @mail($to, $subject, $html, $headers, "-f {$fromEmail}");

        if ($mailSent) {
            \Illuminate\Support\Facades\Log::info('SellerOtpMail: sent via pure PHP mail()', [
                'to'      => $to,
                'purpose' => $purpose,
            ]);
            return true;
        } else {
            \Illuminate\Support\Facades\Log::error('SellerOtpMail: PHP mail() failed', [
                'to'      => $to,
                'purpose' => $purpose,
            ]);
            return false;
        }
    }
}
