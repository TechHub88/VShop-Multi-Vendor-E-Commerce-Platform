<?php

namespace App\Services;

use App\Models\CustomerNotification;
use App\Models\Notification as SellerNotification;
use App\Models\AdminNotification;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class NotificationService
{
    /**
     * Dispatch notification to a target user and push to Firebase in real-time.
     *
     * @param int|string $userId Target user ID (or 'all' for admin/global)
     * @param string $userType 'customer', 'seller', or 'admin'
     * @param string $type E.g. 'order', 'product', 'enquiry', 'grievance', 'kyc'
     * @param string $message The notification text
     * @param string|null $link Optional redirect URL
     */
    public static function notify($userId, string $userType, string $type, string $message, string $link = null): void
    {
        try {
            // 1. Save to the SQL Database
            if ($userType === 'customer') {
                CustomerNotification::create([
                    'customer_id' => $userId,
                    'type' => $type,
                    'message' => $message,
                    'link' => $link,
                    'is_read' => 0
                ]);
            } elseif ($userType === 'seller') {
                SellerNotification::create([
                    'seller_id' => $userId,
                    'type' => $type,
                    'message' => $message,
                    'link' => $link,
                    'is_read' => 0
                ]);
            } elseif ($userType === 'admin') {
                AdminNotification::create([
                    'type' => $type,
                    'message' => $message,
                    'url' => $link,
                    'is_read' => 0
                ]);
            }

            // 2. Push to Firebase Realtime Database
            // For admin, we push to a global 'all' node so all logged-in admins receive it
            $targetId = $userType === 'admin' ? 'all' : $userId;
            $firebaseUrl = "https://mafatlalshop-90155-default-rtdb.asia-southeast1.firebasedatabase.app/notifications/{$userType}/{$targetId}.json";

            Http::post($firebaseUrl, [
                'type' => $type,
                'message' => $message,
                'link' => $link,
                'is_read' => false,
                'created_at' => now()->toDateTimeString()
            ]);

        } catch (\Exception $e) {
            Log::error("Notification dispatch error: " . $e->getMessage());
        }
    }

    /**
     * Legacy compatibility wrapper for notifyCustomer
     */
    public static function notifyCustomer(int $customerId, string $type, string $message, string $link = null): void
    {
        self::notify($customerId, 'customer', $type, $message, $link);
    }
}
