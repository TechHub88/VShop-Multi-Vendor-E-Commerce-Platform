<?php

namespace App\Traits;

use App\Models\ActivityLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

trait LogsActivity
{
    /**
     * Log activity for order status changes
     */
    protected function logOrderActivity($order, $activityType, $oldStatus = null, $newStatus = null, $description = null, $remarks = null, $metadata = [])
    {
        try {
            $user = Auth::guard('admin')->user() ?? Auth::guard('web')->user();
            $customer = Auth::guard('customer')->user();

            ActivityLog::create([
                'order_id' => $order->id,
                'user_id' => $user ? $user->id : null,
                'customer_id' => $customer ? $customer->id : $order->customer_id,
                'activity_type' => $activityType,
                'old_status' => $oldStatus,
                'new_status' => $newStatus,
                'description' => $description,
                'remarks' => $remarks,
                'metadata' => $metadata,
                'ip_address' => Request::ip(),
                'user_agent' => Request::userAgent(),
            ]);
        } catch (\Exception $e) {
            \Log::error('Failed to log activity: ' . $e->getMessage());
        }
    }
}
