<?php

namespace App\Mail;

/**
 * SellerApprovalMail — Simple PHP mail() based Approval sender for Seller Portal
 * Same pattern as SellerOtpMail — No Laravel Mail facade, no SMTP — pure php mail()
 */
class SellerApprovalMail
{
    /**
     * Send Account Approval email using php mail()
     *
     * @param  string  $to       Recipient email
     * @param  string  $name     Recipient name
     * @return bool
     */
    public static function send(string $to, string $name, string $purpose = 'account'): bool
    {
        $fromEmail = env('MAIL_FROM_ADDRESS', 'noreply@zelofx.com');
        $fromName  = env('MAIL_FROM_NAME', 'VSHOP Seller');

        // Ensure the envelope sender is set for PHP mail() on Windows/CPanel environments
        ini_set('sendmail_from', $fromEmail);

        switch ($purpose) {
            case 'platforms':
                $subject      = 'VSHOP Seller - Platform Request Approved';
                $purposeTitle = 'Platform Request Approved';
                $purposeDesc  = 'Your request for platform access has been approved by the admin. You can now login to your chosen platforms.';
                break;
            case 'account':
            default:
                $subject      = 'VSHOP Seller - Account Approved';
                $purposeTitle = 'Permission Approved';
                $purposeDesc  = 'Your permission has been approved. You can now login to your seller dashboard and start using the platform.';
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
    <td style="background:linear-gradient(135deg, #28a745, #218838);padding:28px 40px;text-align:center;">
      <h1 style="margin:0;color:#ffffff;font-size:26px;font-weight:700;letter-spacing:1px;">VSHOP SELLER</h1>
      <p style="margin:6px 0 0;color:#d4edda;font-size:14px;">' . htmlspecialchars($purposeTitle) . '</p>
    </td>
  </tr>

  <!-- BODY -->
  <tr>
    <td style="padding:35px 40px;">
      <p style="margin:0 0 12px;color:#333333;font-size:16px;">Dear <strong>' . htmlspecialchars($name) . '</strong>,</p>
      <p style="margin:0 0 25px;color:#555555;font-size:15px;line-height:1.6;">
        Congratulations! ' . htmlspecialchars($purposeDesc) . '
      </p>

      <!-- SUCCESS BOX -->
      <div style="text-align:center;margin:0 0 25px;">
        <a href="https://vshop.zelofx.com/seller" style="display:inline-block;background:#28a745;color:#ffffff;text-decoration:none;font-weight:bold;border-radius:8px;padding:15px 35px;font-size:16px;box-shadow:0 4px 6px rgba(40,167,69,0.2);">
          Go to Seller Dashboard
        </a>
      </div>

      <p style="margin:0;color:#999999;font-size:13px;">
        If you have any questions or require assistance, please feel free to reach out to our support team.
      </p>
    </td>
  </tr>

  <!-- FOOTER -->
  <tr>
    <td style="background:#f8f9fa;padding:18px 40px;text-align:center;border-top:1px solid #eeeeee;">
      <p style="margin:0;color:#aaaaaa;font-size:12px;">
        &copy; 2025 VSHOP Seller Portal &mdash;
        <a href="https://vshop.zelofx.com/seller" style="color:#28a745;text-decoration:none;">vshop.zelofx.com/seller</a>
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
               . "Your permission has been approved. You can now login to your seller dashboard and start using the platform.\r\n\r\n"
               . "Seller Dashboard: https://vshop.zelofx.com/seller\r\n\r\n"
               . "Regards,\r\nVSHOP Seller Support\r\nhttps://vshop.zelofx.com/seller";

        // ── Build multipart MIME ─────────────────────────────────────────
        $boundary = '----SELLER_APPROVAL_' . md5(uniqid(rand(), true));

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
        $headers .= "X-Mailer: VSHOP-Seller/PHP-" . phpversion() . "\r\n";
        $headers .= "X-Priority: 1\r\n";

        // Ensure the PHP mail() function exists
        if (!function_exists('mail')) {
            \Illuminate\Support\Facades\Log::error('SellerApprovalMail: PHP mail() function not available.', [
                'to' => $to,
            ]);
            return false;
        }

        // ── Send via php mail() with proper envelope sender ──────────
        $sent = mail($to, $subject, $body, $headers, "-f {$fromEmail}");

        // ── Log result ───────────────────────────────────────────────────
        if ($sent) {
            \Illuminate\Support\Facades\Log::info('SellerApprovalMail: sent via mail()', [
                'to'      => $to,
            ]);
        } else {
            $err = error_get_last();
            \Illuminate\Support\Facades\Log::error('SellerApprovalMail: mail() failed', [
                'to'      => $to,
                'error'   => $err ? $err['message'] : 'Unknown',
            ]);
        }

        return $sent;
    }
}
