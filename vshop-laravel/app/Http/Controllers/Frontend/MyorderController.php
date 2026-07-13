<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Models\Category;
use App\Models\About;
use App\Models\Footer;
use App\Models\Order;
use App\Models\OrderReturn;
use App\Models\OrderCancel;
use App\Models\ProductAttribute;
use App\Models\Product;
use App\Models\ActivityLog;
use App\Traits\LogsActivity;
use Barryvdh\DomPDF\Facade\Pdf;

class MyorderController extends Controller
{
    use LogsActivity; // Add the trait
    
    public function index(Request $request)
    {
        // 🔐 Get logged-in customer ID from customer guard
        $customerId = auth()->guard('customer')->id();

        // Query orders only for this customer
        $query = Order::with('items')
            ->where('customer_id', $customerId);

        // ✅ Date filter
        if ($request->start_date && $request->end_date) {
            $query->whereDate('created_at', '>=', $request->start_date)
                  ->whereDate('created_at', '<=', $request->end_date);
        }

        // ✅ Status filter
        if ($request->status) {
            $query->where('status', $request->status);
        }

        // Pagination
        $orders = $query->orderBy('created_at', 'desc')
                        ->paginate(10)
                        ->withQueryString();

        // Other data
        $about = About::first();
        $categories = Category::all();
        $footer = Footer::first();

        return view('frontend.myorder', compact('orders', 'about', 'categories', 'footer'));
    }

    public function orderDetails($id)
    {
        $about = About::first();
        $categories = Category::all();
        $footer = Footer::first();

        $order = Order::with([
            'items.color',
            'items.size',
            'items.product.category'
        ])->findOrFail($id);

        // Calculate totals for order summary
        $totalUniqueItems = 0;
        $totalQuantity = 0;
        $totalSubtotal = 0;
        $totalGST = 0;
        $totalCourierCharge = 0;
        $grandTotal = 0;
        $grandTotalWithoutDiscount = 0;
        
        foreach($order->items as $item) {
            if($item->combo_id) {
                // Combo product: use new columns
                $mainProductTotal = ($item->main_product_price ?? 0) * ($item->main_product_quantity ?? 1);
                $comboProductTotal = ($item->combo_product_price ?? 0) * ($item->combo_product_quantity ?? 1);
                $itemSubtotal = $mainProductTotal + $comboProductTotal;
                $gstPercent = $item->gst_percent ?? 5;
                $itemGST = $itemSubtotal * ($gstPercent / 100);
                $itemCourierCharge = $item->courier_charge ?? 0;
                $itemTotal = $itemSubtotal + $itemGST + $itemCourierCharge;
                
                $grandTotal += $itemTotal;
                $totalUniqueItems++;
                $totalQuantity += ($item->main_product_quantity ?? 1) + ($item->combo_product_quantity ?? 1);
                $totalSubtotal += $itemSubtotal;
                $totalGST += $itemGST;
                $totalCourierCharge += $itemCourierCharge;
            } else {
                // Individual product: use legacy columns
                $itemSubtotal = ($item->price ?? 0) * ($item->quantity ?? 1);
                $gstPercent = $item->gst_percent ?? 5;
                $itemGST = $itemSubtotal * ($gstPercent / 100);
                $itemCourierCharge = $item->courier_charge ?? 0;
                $itemTotal = $itemSubtotal + $itemGST + $itemCourierCharge;
                
                $grandTotal += $itemTotal;
                $totalUniqueItems++;
                $totalQuantity += $item->quantity ?? 1;
                $totalSubtotal += $itemSubtotal;
                $totalGST += $itemGST;
                $totalCourierCharge += $itemCourierCharge;
            }
        }
        
        // Store the total before discount
        $grandTotalWithoutDiscount = $grandTotal;
        
        // Calculate coupon discount
        $couponDiscount = $order->coupon_discount ?? 0;
        $saveAmount = $order->save_amount ?? 0;
        
        // Apply coupon discount if exists
        if($order->coupon_code && $couponDiscount > 0) {
            $grandTotal = max(0, $grandTotal - $couponDiscount);
        }
        
        // Get activity logs for this order
        $activityLogs = ActivityLog::where('order_id', $id)
            ->orderBy('created_at', 'desc')
            ->get();
        
        return view('frontend.orderdetails', compact(
            'order', 
            'about', 
            'categories', 
            'footer',
            'totalUniqueItems',
            'totalQuantity',
            'totalSubtotal',
            'totalGST',
            'totalCourierCharge',
            'grandTotal',
            'grandTotalWithoutDiscount',
            'couponDiscount',
            'saveAmount',
            'activityLogs' // Add activity logs to view
        ));
    }

    public function returnOrder(Request $request, $id)
    {
        $request->validate([
            'reason' => 'required|string|max:255',
            'note'   => 'nullable|string|max:500',
        ]);

        $order = Order::find($id);

        if (!$order) {
            return redirect()->back()->with('error', 'Order not found.');
        }

        if (!in_array($order->status, ['completed', 'out for delivery'])) {
            return redirect()->back()->with('error', 'This order cannot be returned.');
        }

        try {
            // 1️⃣ Create a new return record
            OrderReturn::create([
                'order_id'    => $order->id,
                'customer_id' => $order->customer_id,
                'reason'      => $request->reason,
                'note'        => $request->note,
                'status'      => 'requested',
            ]);

            // 2️⃣ Update order status
            $oldStatus = $order->status;
            $order->update(['status' => 'refund']);

            // 3️⃣ Log the activity
            $this->logOrderActivity(
                order: $order,
                activityType: 'return_request',
                oldStatus: $oldStatus,
                newStatus: 'refund',
                description: 'Return/Refund request submitted by customer',
                remarks: 'Reason: ' . $request->reason . ($request->note ? ' | Note: ' . $request->note : ''),
                metadata: [
                    'return_reason' => $request->reason,
                    'return_note' => $request->note,
                ]
            );

            return redirect()->back()->with('success', 'Return/Refund request submitted successfully.');

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Something went wrong: ' . $e->getMessage());
        }
    }

    public function cancelOrder(Request $request, $id)
    {
        $request->validate([
            'reason' => 'required|string|max:255',
            'note'   => 'nullable|string|max:500',
        ]);

        $order = Order::find($id);

        if (!$order) {
            return redirect()->back()->with('error', 'Order not found.');
        }

        // Only allow cancellation before delivery
        if (!in_array($order->status, ['confirmed', 'processing', 'pending', 'shipped'])) {
            return redirect()->back()->with('error', 'This order cannot be cancelled.');
        }

        try {
            // ✅ Create cancel record
            OrderCancel::create([
                'order_id'    => $order->id,
                'customer_id' => $order->customer_id,
                'reason'      => $request->reason,
                'note'        => $request->note,
                'status'      => 'requested',
            ]);

            // ✅ Update main order status
            $oldStatus = $order->status;
            $order->update([
                'status' => 'cancelled',
                'remarks' => 'Cancel Requested: ' . $request->reason .
                             ($request->note ? ' | Note: ' . $request->note : ''),
            ]);

            // ✅ If payment was made online and is paid, create a refund request
            if (strtolower($order->payment_method) !== 'cod' && strtolower($order->payment_status) === 'paid') {
                \App\Models\OrderReturn::create([
                    'order_id'    => $order->id,
                    'customer_id' => $order->customer_id,
                    'reason'      => 'Order Cancelled: ' . $request->reason,
                    'note'        => $request->note,
                    'status'      => 'approved', // 'approved' maps to 'pending' admin refund in WalletController
                ]);
            } else {
                \App\Models\Order::reverseOrderFunds($order);
            }

            // ✅ Log the activity
            $this->logOrderActivity(
                order: $order,
                activityType: 'cancellation_request',
                oldStatus: $oldStatus,
                newStatus: 'cancelled',
                description: 'Cancellation request submitted by customer',
                remarks: 'Cancel Requested: ' . $request->reason . ($request->note ? ' | Note: ' . $request->note : ''),
                metadata: [
                    'cancel_reason' => $request->reason,
                    'cancel_note' => $request->note,
                ]
            );

            return redirect()->back()->with('success', 'Cancellation request submitted successfully.');

        } catch (\Exception $e) {
            Log::error('Cancel order failed: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Something went wrong: ' . $e->getMessage());
        }
    }

    public function downloadInvoice($id)
    {
        $order = Order::with([
            'items.product.category',
            'items.color',
            'items.size',
            'customer',
            'customer.firstAddress'
        ])->findOrFail($id);

        $pdf = Pdf::loadView('frontend.invoice', compact('order'))
                  ->setPaper('A4');

        $fileName = 'Invoice_'.$order->id.'.pdf';

        return $pdf->download($fileName);
    }

    /**
     * Admin function to update order status (if needed in frontend)
     */
    public function updateOrderStatus(Request $request, $id)
    {
        // If you want customers to update status from frontend (optional)
        $request->validate([
            'status' => 'required|string|in:pending,confirmed,processing,shipped,out for delivery,delivered,cancelled,refund',
            'remarks' => 'nullable|string|max:500',
        ]);

        $order = Order::findOrFail($id);
        
        // Check if customer owns this order
        if (auth()->guard('customer')->id() != $order->customer_id) {
            return redirect()->back()->with('error', 'Unauthorized access.');
        }

        $oldStatus = $order->status;
        $newStatus = $request->status;

        // Update order status
        $order->update([
            'status' => $newStatus,
            'remarks' => $request->remarks ?? $order->remarks,
        ]);

        // Log the activity
        $this->logOrderActivity(
            order: $order,
            activityType: 'status_update',
            oldStatus: $oldStatus,
            newStatus: $newStatus,
            description: 'Order status updated by customer',
            remarks: $request->remarks,
            metadata: [
                'updated_via' => 'frontend',
                'ip_address' => request()->ip(),
            ]
        );

        return redirect()->back()->with('success', 'Order status updated successfully.');
    }
}
