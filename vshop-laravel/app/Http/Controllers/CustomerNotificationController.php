<?php

namespace App\Http\Controllers;

use App\Models\CustomerNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CustomerNotificationController extends Controller
{
    /**
     * Return a list of recent notifications for the authenticated customer.
     */
    public function index()
    {
        $customer = Auth::guard('customer')->user();
        if (!$customer) {
            return response()->json(['success' => false, 'message' => 'Unauthenticated'], 401);
        }

        $notifications = CustomerNotification::where('customer_id', $customer->id)
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        $unreadCount = CustomerNotification::where('customer_id', $customer->id)
            ->where('is_read', false)
            ->count();

        return response()->json([
            'success' => true,
            'notifications' => $notifications,
            'unread_count' => $unreadCount,
        ]);
    }

    /**
     * Mark all notifications for the authenticated customer as read.
     */
    public function clear()
    {
        $customer = Auth::guard('customer')->user();
        if (!$customer) {
            return response()->json(['success' => false, 'message' => 'Unauthenticated'], 401);
        }

        CustomerNotification::where('customer_id', $customer->id)
            ->where('is_read', false)
            ->update(['is_read' => true]);

        return response()->json(['success' => true]);
    }
}
?>
