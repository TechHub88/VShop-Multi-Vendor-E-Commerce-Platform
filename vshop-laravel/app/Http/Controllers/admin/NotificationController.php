<?php
namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\AdminNotification;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
   // app/Http/Controllers/AdminNotificationController.php

public function fetch()
{
    $notifications = \App\Models\Order::where('status', 'Completed')
        ->latest()
        ->take(10)
        ->get();

    return response()->json([
        'count' => $notifications->count(),
        'items' => $notifications
    ]);
}

}
