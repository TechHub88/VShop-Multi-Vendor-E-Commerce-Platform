<?php

namespace App\Mail;

/**
 * BookingMsg — Professional booking confirmation email using php mail()
 * Sends thank-you email with order details, product image, booking slots,
 * meeting link, provider info, duration, session details
 */
class BookingMsg
{
    /**
     * Send booking confirmation email
     *
     * @param  string  $to        Customer email
     * @param  string  $name      Customer name
     * @param  object  $order     Order model
     * @param  array   $items     Order items (Eloquent models with product relation)
     * @return bool
     */
    public static function send(string $to, string $name, $order, array $items): bool
    {
        $fromEmail = env('MAIL_FROM_ADDRESS', 'support@vridhee.com');
        $fromName  = env('MAIL_FROM_NAME', 'VSHOP');

        ini_set('sendmail_from', $fromEmail);

        $subject = 'VSHOP - Booking Confirmation | Order #' . $order->id;

        $siteUrl = rtrim(env('APP_URL', 'https://vshop.zelofx.com'), '/');

        // Build items HTML
        $itemsHtml = '';
        foreach ($items as $item) {
            $product = $item->product ?? null;
            $productName = $product->name ?? $item->product_name ?? 'Product';

            // Fix image URL — use asset helper for full absolute URL
            $thumbnail = $product->thumbnail ?? 'images/default-product.png';
            $imgUrl = asset($thumbnail);

            $price = number_format($item->price ?? $item->main_product_price ?? 0, 2);
            $qty   = $item->quantity ?? $item->main_product_quantity ?? 1;

            // Parse booking slots from attributes
            $attrs = [];
            $rawAttrs = $item->main_product_attributes ?? null;
            if ($rawAttrs) {
                $attrs = is_string($rawAttrs) ? json_decode($rawAttrs, true) : $rawAttrs;
            }

            // --- Service details from product ---
            $providerName    = $product->provider_name ?? '';
            $sessionMode     = $product->session_mode ?? '';
            $sessionType     = $product->session_type ?? '';
            $serviceDuration = $product->service_duration ?? '';
            $sessionDuration = $product->session_duration ?? '';
            $languages       = $product->languages ?? '';
            $maxBooking      = $product->max_booking ?? '';
            $serviceValidity = $product->service_validity ?? '';
            $meetingLink     = $product->meeting_link ?? '';
            $startTime       = $product->start_time ?? '';
            $endTime         = $product->end_time ?? '';

            // Build service info rows
            $serviceRows = '';
            $serviceData = [];
            if ($providerName)    $serviceData['Provider']         = $providerName;
            if ($sessionMode)     $serviceData['Session Mode']     = ucfirst($sessionMode);
            if ($sessionType)     $serviceData['Session Type']     = ucfirst($sessionType);
            if ($serviceDuration) $serviceData['Service Duration'] = $serviceDuration;
            if ($sessionDuration) $serviceData['Session Duration'] = $sessionDuration . ' Mins';
            if ($startTime && $endTime) $serviceData['Timing'] = $startTime . ' - ' . $endTime;
            if ($languages)       $serviceData['Languages']        = $languages;
            if ($maxBooking)      $serviceData['Max Booking']      = $maxBooking;
            if ($serviceValidity) $serviceData['Validity']         = $serviceValidity;

            if (!empty($serviceData)) {
                $serviceRows .= '<table width="100%" cellpadding="0" cellspacing="0" style="margin:8px 0;">';
                foreach ($serviceData as $label => $val) {
                    $serviceRows .= '
                    <tr>
                        <td style="padding:3px 0;font-size:12px;color:#888;width:40%;">' . htmlspecialchars($label) . '</td>
                        <td style="padding:3px 0;font-size:12px;color:#333;font-weight:600;">' . htmlspecialchars($val) . '</td>
                    </tr>';
                }
                $serviceRows .= '</table>';
            }

            // Booking slots badges
            $slotsHtml = '';
            if (isset($attrs['Booking Slots'])) {
                $slotParts = explode(', ', $attrs['Booking Slots']['name'] ?? '');
                foreach ($slotParts as $sl) {
                    $slotsHtml .= '<span style="display:inline-block;background:#e8f4fd;color:#0d6efd;padding:5px 12px;border-radius:6px;font-size:12px;font-weight:600;margin:3px 4px 3px 0;border:1px solid #b6d4fe;">' . htmlspecialchars(trim($sl)) . '</span>';
                }
            }

            // Meeting link section
            $meetingHtml = '';
            if (!empty($meetingLink)) {
                $meetingHtml = '
                <table width="100%" cellpadding="0" cellspacing="0" style="background:#f0fdf4;border:1px solid #bbf7d0;border-radius:8px;margin:10px 0;">
                    <tr>
                        <td style="padding:12px 16px;">
                            <p style="margin:0 0 6px;font-size:13px;color:#166534;font-weight:700;">🎥 Meeting / Session Link</p>
                            <a href="' . htmlspecialchars($meetingLink) . '" style="display:inline-block;background:#0d6efd;color:#ffffff;padding:8px 20px;border-radius:6px;font-size:13px;font-weight:600;text-decoration:none;" target="_blank">
                                Join Session
                            </a>
                            <p style="margin:8px 0 0;font-size:11px;color:#6b7280;word-break:break-all;">' . htmlspecialchars($meetingLink) . '</p>
                            <p style="margin:4px 0 0;font-size:11px;color:#dc2626;font-weight:600;">⏰ Please join at your booked slot time</p>
                        </td>
                    </tr>
                </table>';
            }

            $itemsHtml .= '
            <table width="100%" cellpadding="0" cellspacing="0" style="border:1px solid #e5e7eb;border-radius:10px;margin-bottom:16px;overflow:hidden;">
                <!-- Product Header -->
                <tr style="background:#f9fafb;">
                    <td width="90" style="padding:14px;vertical-align:top;">
                        <img src="' . htmlspecialchars($imgUrl) . '" width="80" height="80" style="border-radius:8px;object-fit:cover;border:1px solid #e5e7eb;display:block;" alt="' . htmlspecialchars($productName) . '">
                    </td>
                    <td style="padding:14px 14px 14px 0;vertical-align:top;">
                        <h4 style="margin:0 0 6px;font-size:15px;color:#1a1a2e;font-weight:700;">' . htmlspecialchars($productName) . '</h4>
                        <p style="margin:0;font-size:13px;color:#6b7280;">₹' . $price . ' × ' . $qty . '</p>
                        <p style="margin:4px 0 0;font-size:14px;color:#0d6efd;font-weight:700;">Total: ₹' . number_format(($item->price ?? 0) * $qty, 2) . '</p>
                    </td>
                </tr>

                ' . ($serviceRows ? '
                <!-- Service Details -->
                <tr>
                    <td colspan="2" style="padding:0 16px 10px;">
                        <p style="margin:0 0 4px;font-size:12px;color:#0d6efd;font-weight:700;text-transform:uppercase;letter-spacing:0.5px;">Service Details</p>
                        ' . $serviceRows . '
                    </td>
                </tr>' : '') . '

                ' . ($slotsHtml ? '
                <!-- Booking Slots -->
                <tr>
                    <td colspan="2" style="padding:0 16px 12px;">
                        <p style="margin:0 0 6px;font-size:12px;color:#0d6efd;font-weight:700;text-transform:uppercase;letter-spacing:0.5px;">📅 Your Booked Slots</p>
                        ' . $slotsHtml . '
                    </td>
                </tr>' : '') . '

                ' . ($meetingHtml ? '
                <!-- Meeting Link -->
                <tr>
                    <td colspan="2" style="padding:0 16px 14px;">
                        ' . $meetingHtml . '
                    </td>
                </tr>' : '') . '
            </table>';
        }

        $orderDate   = $order->created_at ? $order->created_at->format('d M Y, h:i A') : date('d M Y, h:i A');
        $totalAmount = number_format($order->total_amount ?? 0, 2);
        $payMethod   = ucfirst($order->payment_method ?? 'COD');

        $html = '<!DOCTYPE html>
<html>
<head><meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1.0"></head>
<body style="margin:0;padding:0;background:#f0f2f5;font-family:\'Segoe UI\',Arial,sans-serif;">
<table width="100%" cellpadding="0" cellspacing="0" style="background:#f0f2f5;padding:30px 0;">
<tr><td align="center">
<table width="600" cellpadding="0" cellspacing="0" style="background:#ffffff;border-radius:14px;overflow:hidden;box-shadow:0 4px 20px rgba(0,0,0,0.08);">

  <!-- HEADER -->
  <tr>
    <td style="background:linear-gradient(135deg,#0d6efd 0%,#0a47a9 100%);padding:30px;text-align:center;">
      <h1 style="margin:0;color:#fff;font-size:24px;font-weight:800;letter-spacing:-0.5px;">🎉 Booking Confirmed!</h1>
      <p style="margin:8px 0 0;color:rgba(255,255,255,0.9);font-size:14px;">Thank you for choosing our service</p>
    </td>
  </tr>

  <!-- GREETING -->
  <tr>
    <td style="padding:28px 30px 12px;">
      <p style="margin:0;font-size:16px;color:#1a1a2e;">Hello <strong>' . htmlspecialchars($name) . '</strong>,</p>
      <p style="margin:10px 0 0;font-size:14px;color:#6b7280;line-height:1.6;">
        Your booking has been successfully placed. Below are the complete details of your order.
      </p>
    </td>
  </tr>

  <!-- ORDER INFO BOX -->
  <tr>
    <td style="padding:8px 30px 16px;">
      <table width="100%" style="background:#f8fafc;border-radius:10px;border:1px solid #e2e8f0;" cellpadding="0" cellspacing="0">
        <tr>
          <td style="padding:14px 20px;border-right:1px solid #e2e8f0;" width="50%">
            <p style="margin:0;font-size:11px;color:#94a3b8;text-transform:uppercase;letter-spacing:1px;">Order No</p>
            <p style="margin:4px 0 0;font-size:20px;color:#1e293b;font-weight:800;">#' . $order->id . '</p>
          </td>
          <td style="padding:14px 20px;" width="50%">
            <p style="margin:0;font-size:11px;color:#94a3b8;text-transform:uppercase;letter-spacing:1px;">Order Date</p>
            <p style="margin:4px 0 0;font-size:14px;color:#1e293b;font-weight:600;">' . $orderDate . '</p>
          </td>
        </tr>
        <tr>
          <td colspan="2" style="padding:0 20px 14px;">
            <p style="margin:0;font-size:11px;color:#94a3b8;text-transform:uppercase;letter-spacing:1px;">Payment Method</p>
            <p style="margin:4px 0 0;font-size:13px;color:#1e293b;font-weight:600;">' . $payMethod . '</p>
          </td>
        </tr>
      </table>
    </td>
  </tr>

  <!-- ITEMS HEADING -->
  <tr>
    <td style="padding:8px 30px 4px;">
      <h3 style="margin:0;font-size:15px;color:#1e293b;font-weight:700;border-bottom:2px solid #0d6efd;padding-bottom:8px;display:inline-block;">Order Items</h3>
    </td>
  </tr>

  <!-- ITEMS -->
  <tr>
    <td style="padding:12px 30px;">
      ' . $itemsHtml . '
    </td>
  </tr>

  <!-- GRAND TOTAL -->
  <tr>
    <td style="padding:0 30px 20px;">
      <table width="100%" style="background:linear-gradient(135deg,#0d6efd 0%,#0a47a9 100%);border-radius:10px;" cellpadding="0" cellspacing="0">
        <tr>
          <td style="padding:16px 24px;">
            <table width="100%" cellpadding="0" cellspacing="0">
              <tr>
                <td><p style="margin:0;font-size:14px;color:rgba(255,255,255,0.85);">Grand Total</p></td>
                <td style="text-align:right;"><p style="margin:0;font-size:22px;color:#fff;font-weight:800;">₹' . $totalAmount . '</p></td>
              </tr>
            </table>
          </td>
        </tr>
      </table>
    </td>
  </tr>

  <!-- FOOTER -->
  <tr>
    <td style="padding:20px 30px;background:#f8fafc;text-align:center;border-top:1px solid #e2e8f0;">
      <p style="margin:0;font-size:11px;color:#94a3b8;">This is an automated email from VSHOP. Please do not reply.</p>
      <p style="margin:6px 0 0;font-size:11px;color:#94a3b8;">© ' . date('Y') . ' VSHOP by Vridhee Infotech Pvt Ltd. All rights reserved.</p>
    </td>
  </tr>

</table>
</td></tr>
</table>
</body>
</html>';

        $headers  = "MIME-Version: 1.0\r\n";
        try {
            \Illuminate\Support\Facades\Mail::html($html, function ($message) use ($to, $subject, $fromEmail, $fromName) {
                $message->to($to)
                        ->subject($subject)
                        ->from($fromEmail, $fromName);
            });
            
            \Illuminate\Support\Facades\Log::info('BookingMsg: sent via SMTP', ['to' => $to, 'order_id' => $order->id]);
            return true;
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('BookingMsg: SMTP failed', [
                'to' => $to,
                'order_id' => $order->id,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }
}
