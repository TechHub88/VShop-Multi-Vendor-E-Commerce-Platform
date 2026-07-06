<?php

namespace App\Mail;

/**
 * VshopOtpMail — Simple PHP mail() based OTP sender
 * No Laravel Mail facade, no SMTP — pure php mail()
 */
class VshopOtpMail
{
    /**
     * Send OTP email using php mail()
     *
     * @param  string  $to       Recipient email
     * @param  string  $name     Recipient name
     * @param  int     $otp      6-digit OTP
     * @param  string  $purpose  'registration' | 'reset'
     * @return bool
     */
    public static function send(string $to, string $name, int $otp, string $purpose = 'registration'): bool
    {
        $fromEmail = env('MAIL_FROM_ADDRESS', 'no-reply@vridheewellness.com');
        $fromName  = env('MAIL_FROM_NAME', 'VSHOP');

        // Ensure the envelope sender is set for PHP mail() on Windows/CPanel environments
        ini_set('sendmail_from', $fromEmail);

        $subject = ($purpose === 'registration')
            ? 'VSHOP - Email Verification OTP'
            : 'VSHOP - Password Reset OTP';

        $purposeTitle = ($purpose === 'registration')
            ? 'Email Verification'
            : 'Password Reset';

        $purposeDesc = ($purpose === 'registration')
            ? 'verify your email and complete your VSHOP registration'
            : 'reset your VSHOP account password';

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
    <td style="background:#1a73e8;padding:28px 40px;text-align:center;">
      <h1 style="margin:0;color:#ffffff;font-size:26px;font-weight:700;letter-spacing:1px;">VSHOP</h1>
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
        <div style="display:inline-block;background:#f0f6ff;border:2px dashed #1a73e8;border-radius:10px;padding:20px 50px;">
          <p style="margin:0;font-size:12px;color:#888888;letter-spacing:2px;text-transform:uppercase;">Your OTP</p>
          <p style="margin:10px 0 0;font-size:44px;font-weight:700;color:#1a73e8;letter-spacing:14px;">' . $otp . '</p>
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
        &copy; 2026 VRIDHEE INNOVATIONS PRIVATE LIMITED &mdash;
        <a href="https://vshop.zelofx.com" style="color:#1a73e8;text-decoration:none;">vshop.zelofx.com</a>
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
               . "Your VSHOP {$purposeTitle} OTP is: {$otp}\r\n\r\n"
               . "This OTP is valid for 5 minutes only.\r\n"
               . "Do NOT share this OTP with anyone.\r\n\r\n"
               . "If you did not request this, please ignore this email.\r\n\r\n"
               . "Regards,\r\nVSHOP Support Team\r\nhttps://vshop.zelofx.com";

        // ── Build multipart MIME ─────────────────────────────────────────
        $boundary = '----VSHOP_OTP_' . md5(uniqid(rand(), true));

        $body  = "--{$boundary}\r\n";
        $body .= "Content-Type: text/plain; charset=UTF-8\r\n";
        $body .= "Content-Transfer-Encoding: 8bit\r\n\r\n";
        $body .= $plain . "\r\n";
        $body .= "--{$boundary}\r\n";
        $body .= "Content-Type: text/html; charset=UTF-8\r\n";
        $body .= "Content-Transfer-Encoding: 8bit\r\n\r\n";
        $body .= $html . "\r\n";
        $body .= "--{$boundary}--";

        // ── Headers ──────────────────────────────────────────────────────
        $headers  = "MIME-Version: 1.0\r\n";
        $headers .= "Content-Type: multipart/alternative; boundary=\"{$boundary}\"\r\n";
        $headers .= "From: {$fromName} <{$fromEmail}>\r\n";
        $headers .= "Reply-To: {$fromEmail}\r\n";
        $headers .= "Return-Path: {$fromEmail}\r\n";
        $headers .= "X-Mailer: VSHOP/PHP-" . phpversion() . "\r\n";
        $headers .= "X-Priority: 1\r\n";

        $mailSent = @mail($to, $subject, $body, $headers, "-f {$fromEmail}");

        if ($mailSent) {
            \Illuminate\Support\Facades\Log::info('VshopOtpMail: sent via pure PHP mail()', [
                'to'      => $to,
                'purpose' => $purpose,
            ]);
            return true;
        } else {
            \Illuminate\Support\Facades\Log::error('VshopOtpMail: PHP mail() failed', [
                'to'      => $to,
                'purpose' => $purpose,
            ]);
            return false;
        }
    }
}
