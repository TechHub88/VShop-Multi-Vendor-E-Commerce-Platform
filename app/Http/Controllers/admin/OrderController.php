<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Seller;
use App\Models\Product;
use App\Models\City;
use App\Models\ActivityLog;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
class OrderController extends Controller
{
    // ORDER MONITORING
    // Display lifecycle control page with filters
    
        private function logActivity(array $data)
    {
        try {
            ActivityLog::create([
                'order_id' => $data['order_id'],
                'user_id' => $data['user_id'],
                'customer_id' => $data['customer_id'],
                'activity_type' => $data['activity_type'],
                'old_status' => $data['old_status'],
                'new_status' => $data['new_status'],
                'description' => $data['description'],
                'remarks' => $data['remarks'] ?? null,
                'metadata' => json_encode($data['metadata'] ?? []),
                'ip_address' => $data['ip_address'] ?? null,
                'user_agent' => $data['user_agent'] ?? null
            ]);
        } catch (\Exception $e) {
            // Log error but don't break the main functionality
            \Log::error('Failed to save activity log: ' . $e->getMessage());
        }
    }
   public function index(Request $request)
    {
        $query = Order::query()->with('seller');
        
        // Apply filters
        if ($request->filled('seller')) {
            $query->where('seller_id', $request->seller);
        }
        
        if ($request->filled('product')) {
            $query->where('product_id', $request->product);
        }
        
        if ($request->filled('location')) {
            $query->where('location', $request->location);
        }
        
        if ($request->filled('payment_type')) {
            $query->where('payment_type', $request->payment_type);
        }
        
        if ($request->filled('payment_status')) {
            $query->where('payment_status', $request->payment_status);
        }
        
        if ($request->filled('order_status')) {
            $query->where('status', $request->order_status);
        }
        
        $orders = $query->latest()->paginate(15);
        
        // Get data for filters from respective tables (optimized to only select needed columns)
        $sellers = Seller::select('id', 'name')->get(); 
        $products = Product::select('id', 'name')->get(); 
        $cities = City::select('name')->orderBy('name')->get();
        
        return view('admin.orders.index', compact('orders', 'sellers', 'products', 'cities'));
    }
 
 
 public function lifecycle(Request $request)
{
    // Fetch dropdown data
    $sellers = \App\Models\Seller::all();
    $products = \App\Models\Product::all();
    $cities   = \App\Models\City::all();

    // Build query with optional filters
    $orders = Order::with(['customer', 'items.product'])
        ->when($request->seller, function ($q) use ($request) {
            $q->whereHas('items.product', function ($q2) use ($request) {
                $q2->where('seller_id', $request->seller);
            });
        })
        ->when($request->product, function ($q) use ($request) {
            $q->whereHas('items', function ($q2) use ($request) {
                $q2->where('product_id', $request->product);
            });
        })
        ->when($request->location, function ($q) use ($request) {
            $q->whereHas('customer', function ($q2) use ($request) {
                $q2->where('city_id', $request->location);
            });
        })
        ->when($request->payment_type, function ($q) use ($request) {
            $q->where('payment_type', $request->payment_type);
        })
        ->orderBy('id', 'desc')
        ->paginate(6); // CHANGE FROM get() TO paginate()

    return view('admin.orders.lifecycle', compact('orders', 'sellers', 'products', 'cities'));
}


public function cancellation(Request $request)
{
    $sellers = \App\Models\Seller::all();
    $products = \App\Models\Product::all();
    $cities = \App\Models\City::all();
    
    $orders = Order::with(['customer', 'items.product'])
        ->where(function($q) {
            $q->where('cancellation_requested', true)
              ->orWhere('return_requested', true);
        })
        ->when($request->request_type, function($q) use ($request) {
            if($request->request_type == 'cancellation') {
                $q->where('cancellation_requested', true);
            } elseif($request->request_type == 'return') {
                $q->where('return_requested', true);
            }
        })
        ->when($request->status, function($q) use ($request) {
            $q->where(function($q2) use ($request) {
                $q2->where('cancellation_status', $request->status)
                   ->orWhere('return_status', $request->status);
            });
        })
        ->when($request->seller, function($q) use ($request) {
            $q->whereHas('items.product', function($q2) use ($request) {
                $q2->where('seller_id', $request->seller);
            });
        })
        ->when($request->product, function($q) use ($request) {
            $q->whereHas('items', function($q2) use ($request) {
                $q2->where('product_id', $request->product);
            });
        })
        ->orderBy('id', 'desc')
        ->paginate(6); // CHANGE FROM get() TO paginate()

    return view('admin.orders.cancellation', compact('orders', 'sellers', 'products', 'cities'));
}




  public function approveRequest(Request $request, $orderId)
    {
        try {
            $validated = $request->validate([
                'request_type' => 'required|in:cancellation,return',
                'approval_notes' => 'required|string|min:3',
                'refund_amount' => 'required|numeric|min:0',
                'quality_check' => 'sometimes|boolean'
            ]);

            $order = Order::findOrFail($orderId);
            
            DB::beginTransaction();
            
            $oldStatus = '';
            $newStatus = '';
            $activityType = '';
            
            if ($validated['request_type'] == 'cancellation') {
                if (!$order->cancellation_requested) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Cancellation not requested for this order'
                    ], 400);
                }
                
                $oldStatus = $order->cancellation_status ?? 'pending';
                $newStatus = 'approved';
                $activityType = 'cancellation_approved';
                
                // Update order table columns
                $order->cancellation_status = 'approved';
                $order->cancellation_approved_at = now();
                $order->cancellation_reason = $validated['approval_notes'];
                $order->status = 'cancelled';
                $order->refund_amount = $validated['refund_amount'];
                $order->refund_status = 'pending';
                $order->refund_initiated_at = now();
                
            } else { // Return request
                if (!$order->return_requested) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Return not requested for this order'
                    ], 400);
                }
                
                $oldStatus = $order->return_status ?? 'pending';
                $newStatus = 'approved';
                $activityType = 'return_approved';
                
                // Update order table columns
                $order->return_status = 'approved';
                $order->return_approved_at = now();
                $order->return_reason = $validated['approval_notes'];
                $order->refund_amount = $validated['refund_amount'];
                $order->refund_status = 'pending';
                $order->refund_initiated_at = now();
                
                if (isset($validated['quality_check']) && $validated['quality_check']) {
                    $order->quality_check_required = true;
                }
            }
            
            $order->save();
            
            // Log to activity_logs table
            $this->logActivity([
                'order_id' => $order->id,
                'user_id' => Auth::id(),
                'customer_id' => $order->customer_id,
                'activity_type' => $activityType,
                'old_status' => $oldStatus,
                'new_status' => $newStatus,
                'description' => ucfirst($validated['request_type']) . " request approved for order #{$order->id}",
                'remarks' => $validated['approval_notes'],
                'metadata' => [
                    'request_type' => $validated['request_type'],
                    'refund_amount' => $validated['refund_amount'],
                    'quality_check' => $validated['quality_check'] ?? false,
                    'approved_by' => Auth::user()->name ?? Auth::id()
                ],
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent()
            ]);
            
            DB::commit();
            
            return response()->json([
                'success' => true,
                'message' => ucfirst($validated['request_type']) . ' request approved successfully. Refund will be processed.'
            ]);
            
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Reject cancellation or return request
     */
    public function rejectRequest(Request $request, $orderId)
    {
        try {
            $validated = $request->validate([
                'rejection_reason' => 'required|string|min:3'
            ]);

            $order = Order::findOrFail($orderId);
            
            DB::beginTransaction();
            
            $oldStatus = '';
            $newStatus = 'rejected';
            $activityType = '';
            $message = '';
            
            if ($order->cancellation_requested && $order->cancellation_status == 'pending') {
                $oldStatus = $order->cancellation_status ?? 'pending';
                $order->cancellation_status = 'rejected';
                $order->cancellation_reason = $validated['rejection_reason'];
                $message = 'Cancellation request rejected';
                $activityType = 'cancellation_rejected';
                
            } elseif ($order->return_requested && $order->return_status == 'pending') {
                $oldStatus = $order->return_status ?? 'pending';
                $order->return_status = 'rejected';
                $order->return_reason = $validated['rejection_reason'];
                $message = 'Return request rejected';
                $activityType = 'return_rejected';
                
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'No pending request found for this order'
                ], 400);
            }
            
            $order->save();
            
            // Log to activity_logs table
            $this->logActivity([
                'order_id' => $order->id,
                'user_id' => Auth::id(),
                'customer_id' => $order->customer_id,
                'activity_type' => $activityType,
                'old_status' => $oldStatus,
                'new_status' => $newStatus,
                'description' => $message . " for order #{$order->id}",
                'remarks' => $validated['rejection_reason'],
                'metadata' => [
                    'rejection_reason' => $validated['rejection_reason'],
                    'rejected_by' => Auth::user()->name ?? Auth::id()
                ],
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent()
            ]);
            
            DB::commit();
            
            return response()->json([
                'success' => true,
                'message' => $message
            ]);
            
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Process refund for approved request
     */
    public function processRefund(Request $request, $orderId)
    {
        try {
            $order = Order::findOrFail($orderId);
            
            DB::beginTransaction();
            
            $oldStatus = $order->refund_status ?? 'pending';
            $newStatus = 'completed';
            
            // Update order table refund columns
            $order->refund_status = 'completed';
            $order->refund_completed_at = now();
            $order->refunded_at = now();
            $order->refund_method = $request->input('refund_method', 'original_payment');
            
            // Generate refund transaction ID if not exists
            if (empty($order->refund_transaction_id)) {
                $order->refund_transaction_id = 'RFD-' . time() . '-' . $order->id;
            }
            
            $order->save();
            
            // Log to activity_logs table
            $this->logActivity([
                'order_id' => $order->id,
                'user_id' => Auth::id(),
                'customer_id' => $order->customer_id,
                'activity_type' => 'refund_processed',
                'old_status' => $oldStatus,
                'new_status' => $newStatus,
                'description' => "Refund processed for order #{$order->id}",
                'remarks' => "Refund amount: ₹" . number_format($order->refund_amount ?? $order->total_amount, 2),
                'metadata' => [
                    'refund_amount' => $order->refund_amount ?? $order->total_amount,
                    'refund_method' => $order->refund_method,
                    'refund_transaction_id' => $order->refund_transaction_id,
                    'processed_by' => Auth::user()->name ?? Auth::id()
                ],
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent()
            ]);
            
            DB::commit();
            
            return response()->json([
                'success' => true,
                'message' => 'Refund processed successfully. Amount: ₹' . number_format($order->refund_amount ?? $order->total_amount, 2)
            ]);
            
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }
    
public function exportCancellation(Request $request)
{
    $query = Order::with(['customer', 'items.product']);
    
    if ($request->filled('request_type')) {
        if($request->request_type == 'cancellation') {
            $query->where('cancellation_requested', true);
        } elseif($request->request_type == 'return') {
            $query->where('return_requested', true);
        }
    }
    
    if ($request->filled('status')) {
        $query->where(function($q) use ($request) {
            $q->where('cancellation_status', $request->status)
              ->orWhere('return_status', $request->status);
        });
    }
    
    if ($request->filled('seller')) {
        $query->whereHas('items.product', function($q) use ($request) {
            $q->where('seller_id', $request->seller);
        });
    }
    
    if ($request->filled('product')) {
        $query->whereHas('items', function($q) use ($request) {
            $q->where('product_id', $request->product);
        });
    }
    
    $orders = $query->orderBy('id', 'desc')->get();
    
    // Generate CSV
    $filename = 'cancellation_returns_export_' . date('Y-m-d_His') . '.csv';
    $handle = fopen('php://temp', 'w');
    
    // Add headers
    fputcsv($handle, ['Order ID', 'Request Type', 'Customer Name', 'Customer Email', 'Product', 'Amount', 'Request Status', 'Request Date', 'Refund Status']);
    
    foreach ($orders as $order) {
        $requestType = $order->cancellation_requested ? 'Cancellation' : ($order->return_requested ? 'Return' : 'N/A');
        $requestStatus = $order->cancellation_requested ? ($order->cancellation_status ?? 'pending') : ($order->return_status ?? 'pending');
        
        fputcsv($handle, [
            '#' . $order->id,
            $requestType,
            $order->customer->name ?? 'N/A',
            $order->customer->email ?? 'N/A',
            $order->items->first()->product->name ?? 'N/A',
            $order->total_amount ?? $order->total ?? 0,
            ucfirst($requestStatus),
            $order->created_at ? $order->created_at->format('Y-m-d H:i:s') : '',
            ucfirst($order->refund_status ?? 'N/A')
        ]);
    }
    
    rewind($handle);
    $content = stream_get_contents($handle);
    fclose($handle);
    
    return response($content, 200, [
        'Content-Type' => 'text/csv',
        'Content-Disposition' => "attachment; filename=\"$filename\"",
    ]);
}

        public function payment(Request $request)
    {
        // Build query with eager loading
        $query = Order::with(['customer', 'items.product']);
        
        // ========== APPLY FILTERS ==========
        
        // Filter by Seller
        if ($request->filled('seller')) {
            $query->where('seller_id', $request->seller);
        }
        
        // Filter by Product
        if ($request->filled('product')) {
            $query->whereHas('items', function($q) use ($request) {
                $q->where('product_id', $request->product);
            });
        }
        
        // Filter by Location (City)
        if ($request->filled('location')) {
            $query->where('location', $request->location);
        }
        
        // Filter by Payment Type
        if ($request->filled('payment_type')) {
            $query->where('payment_type', $request->payment_type);
        }
        
        // Filter by Payment Status
        if ($request->filled('payment_status')) {
            $query->where('payment_status', $request->payment_status);
        }
        
        // Filter by Fraud Status
        if ($request->filled('fraud_status')) {
            $query->where('fraud_status', $request->fraud_status);
        }
        
        // Filter by Date Range
        if ($request->filled('date_range')) {
            switch($request->date_range) {
                case 'today':
                    $query->whereDate('created_at', today());
                    break;
                case 'yesterday':
                    $query->whereDate('created_at', today()->subDay());
                    break;
                case 'this_week':
                    $query->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()]);
                    break;
                case 'last_week':
                    $query->whereBetween('created_at', [now()->subWeek()->startOfWeek(), now()->subWeek()->endOfWeek()]);
                    break;
                case 'this_month':
                    $query->whereMonth('created_at', now()->month)
                          ->whereYear('created_at', now()->year);
                    break;
                case 'last_month':
                    $query->whereMonth('created_at', now()->subMonth()->month)
                          ->whereYear('created_at', now()->subMonth()->year);
                    break;
                case 'this_year':
                    $query->whereYear('created_at', now()->year);
                    break;
                case 'last_year':
                    $query->whereYear('created_at', now()->subYear()->year);
                    break;
            }
        }
        
        // Search by Order ID, Invoice No, or Transaction ID
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('id', 'LIKE', "%{$search}%")
                  ->orWhere('invoice_no', 'LIKE', "%{$search}%")
                  ->orWhere('transaction_id', 'LIKE', "%{$search}%")
                  ->orWhere('order_no', 'LIKE', "%{$search}%");
            });
        }
        
        // Get paginated results
        $orders = $query->orderBy('id', 'desc')->paginate(15);
        
        // ========== STATISTICS CARDS DATA ==========
        
        // Successful Payments
        $successfulPayments = Order::where('payment_status', 'success')->count();
        
        // Failed Payments
        $failedPayments = Order::where('payment_status', 'failed')->count();
        
        // Pending Verification
        $pendingVerification = Order::where('payment_status', 'pending')->count();
        
        // Fraud Alerts (Flagged payments)
        $fraudAlerts = Order::where('fraud_status', 'flagged')->count();
        
        // Additional statistics
        $totalAmount = Order::where('payment_status', 'success')->sum('total_amount');
        $refundedAmount = Order::where('payment_status', 'refunded')->sum('total_amount');
        
        // ========== FILTER DROPDOWN DATA ==========
        
        // Get all sellers for filter dropdown
        $sellers = Seller::orderBy('name')->get();
        
        // Get all products for filter dropdown
        $products = Product::orderBy('name')->get();
        
        // Get all cities for filter dropdown
        $cities = City::orderBy('name')->get();
        
        // Return view with all data
        return view('admin.orders.payment', compact(
            'orders',
            'sellers',
            'products',
            'cities',
            'successfulPayments',
            'failedPayments',
            'pendingVerification',
            'fraudAlerts',
            'totalAmount',
            'refundedAmount'
        ));
    }
    
  
  
    public function exportPayment(Request $request)
    {
        // Build query similar to payment method
        $query = Order::with(['customer', 'items.product']);
        
        // Apply same filters
        if ($request->filled('seller')) {
            $query->where('seller_id', $request->seller);
        }
        
        if ($request->filled('product')) {
            $query->whereHas('items', function($q) use ($request) {
                $q->where('product_id', $request->product);
            });
        }
        
        if ($request->filled('location')) {
            $query->where('location', $request->location);
        }
        
        if ($request->filled('payment_type')) {
            $query->where('payment_type', $request->payment_type);
        }
        
        if ($request->filled('payment_status')) {
            $query->where('payment_status', $request->payment_status);
        }
        
        if ($request->filled('fraud_status')) {
            $query->where('fraud_status', $request->fraud_status);
        }
        
        // Get all orders for export
        $orders = $query->orderBy('id', 'desc')->get();
        
        // Prepare CSV headers
        $headers = [
            'Order ID',
            'Invoice No',
            'Customer Name',
            'Customer Email',
            'Customer Phone',
            'Total Amount',
            'Payment Type',
            'Transaction ID',
            'Payment Status',
            'Fraud Score',
            'Fraud Status',
            'Payment Date',
            'Location',
            'Seller',
            'Product'
        ];
        
        // Prepare CSV data
        $csvData = [];
        $csvData[] = $headers;
        
        foreach ($orders as $order) {
            $products = $order->items->pluck('product.name')->implode(', ');
            $sellers = $order->items->pluck('product.seller.name')->implode(', ');
            
            $csvData[] = [
                '#' . $order->id,
                $order->invoice_no ?? 'INV-' . str_pad($order->id, 8, '0', STR_PAD_LEFT),
                $order->customer->name ?? $order->recipient_name ?? 'N/A',
                $order->customer->email ?? 'N/A',
                $order->customer->phone ?? $order->phone ?? 'N/A',
                $order->total_amount ?? 0,
                ucfirst(str_replace('_', ' ', $order->payment_type ?? 'N/A')),
                $order->transaction_id ?? 'N/A',
                ucfirst($order->payment_status ?? 'N/A'),
                $order->fraud_score ?? 0,
                ucfirst($order->fraud_status ?? 'Clean'),
                $order->created_at ? $order->created_at->format('Y-m-d H:i:s') : 'N/A',
                $order->location ?? $order->city ?? 'N/A',
                $sellers ?: $order->seller_name ?? 'N/A',
                $products ?: $order->product_name ?? 'N/A'
            ];
        }
        
        // Generate CSV file
        $filename = 'payment_report_' . date('Y-m-d_His') . '.csv';
        $handle = fopen('php://temp', 'w');
        
        foreach ($csvData as $row) {
            fputcsv($handle, $row);
        }
        
        rewind($handle);
        $content = stream_get_contents($handle);
        fclose($handle);
        
        return response($content, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
            'Cache-Control' => 'max-age=0',
        ]);
    }
    
    /**
     * Get payment details for a specific order
     */
  
    
    /**
     * Validate invoice for an order
     */
  /**
 * Validate invoice for an order
 */
public function validateInvoice($id)
{
    $order = Order::findOrFail($id);
    
    $isValid = true;
    $message = [];
    
    // Check if invoice exists - Auto generate if missing
    if (!$order->invoice_no) {
        $order->invoice_no = 'INV-' . str_pad($order->id, 8, '0', STR_PAD_LEFT);
        $order->save();
        $message[] = 'Invoice number was missing and has been auto-generated';
    }
    
    // Check if payment is successful - Consider 'paid' as success too
    if (!in_array($order->payment_status, ['success', 'paid'])) {
        $isValid = false;
        $message[] = 'Payment is not completed';
    }
    
    // Verify total amount - Skip if no items
    if ($order->items && $order->items->count() > 0) {
        $calculatedTotal = $order->items->sum('total_price') ?? $order->items->sum('price') ?? 0;
        if ($order->total_amount != $calculatedTotal && $calculatedTotal > 0) {
            $isValid = false;
            $message[] = 'Total amount mismatch';
        }
    }
    
    // Check for fraud flags
    if ($order->fraud_status == 'flagged') {
        $isValid = false;
        $message[] = 'Transaction flagged for fraud';
    }
    
    // If valid, update status
    if ($isValid) {
        $order->payment_status = 'success';
        $order->fraud_status = 'clean';
        $order->fraud_score = 0;
        $order->save();
        $message = ['Invoice is valid and verified'];
    }
    
    return response()->json([
        'valid' => $isValid,
        'message' => implode('<br>', $message)
    ]);
}
    /**
     * Mark payment as suspicious
     */
    public function markSuspicious($id, Request $request)
    {
        $order = Order::findOrFail($id);
        $order->fraud_status = 'suspicious';
        $order->fraud_score = ($order->fraud_score ?? 0) + 30;
        $order->fraud_reason = $request->reason ?? 'Marked suspicious by admin';
        $order->save();
        
        return response()->json(['success' => true, 'message' => 'Payment marked as suspicious']);
    }
    
    /**
     * Flag payment as fraud
     */
    public function flagFraud($id, Request $request)
    {
        $order = Order::findOrFail($id);
        $order->fraud_status = 'flagged';
        $order->fraud_score = 100;
        $order->fraud_reason = $request->reason ?? 'Flagged as fraudulent';
        $order->payment_status = 'failed';
        $order->save();
        
        return response()->json(['success' => true, 'message' => 'Payment flagged as fraud']);
    }
    
    /**
     * Clear fraud flag
     */
    public function clearFraud($id)
    {
        $order = Order::findOrFail($id);
        $order->fraud_status = 'clean';
        $order->fraud_score = 0;
        $order->fraud_reason = null;
        $order->save();
        
        return response()->json(['success' => true, 'message' => 'Fraud flag cleared']);
    }
    
    /**
     * Process refund for payment
     */
    public function refundPayment($id, Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric|min:0',
            'reason' => 'required|string'
        ]);
        
        $order = Order::findOrFail($id);
        
        if ($order->payment_status != 'success') {
            return response()->json(['error' => 'Only successful payments can be refunded'], 400);
        }
        
        if ($request->amount > $order->total_amount) {
            return response()->json(['error' => 'Refund amount cannot exceed order total'], 400);
        }
        
        $order->payment_status = 'refunded';
        $order->refund_amount = $request->amount;
        $order->refund_reason = $request->reason;
        $order->refunded_at = now();
        $order->save();
        
        // Create refund log
        \App\Models\PaymentLog::create([
            'order_id' => $order->id,
            'type' => 'refund',
            'amount' => $request->amount,
            'status' => 'completed',
            'remarks' => $request->reason
        ]);
        
        return response()->json([
            'success' => true, 
            'message' => 'Refund processed successfully. Amount: ₹' . number_format($request->amount, 2)
        ]);
    }
    
    
    
    
    
    /**
 * View invoice for an order
 */
public function viewInvoice($id)
{
    $order = Order::with(['customer', 'items.product', 'seller'])->findOrFail($id);
    
    // Return HTML for invoice modal
    $html = '
    <div class="invoice-wrapper p-4">
        <div class="text-center mb-4">
            <h2>INVOICE</h2>
            <p>Order #' . $order->id . '</p>
        </div>
        
        <div class="row mb-4">
            <div class="col-6">
                <strong>Bill To:</strong><br>
                ' . ($order->customer->name ?? $order->recipient_name ?? 'N/A') . '<br>
                ' . ($order->customer->email ?? 'N/A') . '<br>
                ' . ($order->customer->phone ?? $order->phone ?? 'N/A') . '<br>
                ' . ($order->location ?? $order->city ?? 'N/A') . '
            </div>
            <div class="col-6 text-end">
                <strong>Invoice Details:</strong><br>
                Invoice No: ' . ($order->invoice_no ?? 'INV-' . str_pad($order->id, 8, '0', STR_PAD_LEFT)) . '<br>
                Order Date: ' . ($order->created_at ? $order->created_at->format('d M Y') : 'N/A') . '<br>
                Payment Date: ' . ($order->payment_date ? date('d M Y', strtotime($order->payment_date)) : 'N/A') . '<br>
                Payment Method: ' . ucfirst(str_replace('_', ' ', $order->payment_type ?? 'N/A')) . '
            </div>
        </div>
        
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Product</th>
                    <th>Quantity</th>
                    <th>Unit Price</th>
                    <th>Total</th>
                </tr>
            </thead>
            <tbody>';
    
    $total = 0;
    if($order->items && $order->items->count() > 0) {
        foreach($order->items as $key => $item) {
            $subtotal = $item->quantity * $item->price;
            $total += $subtotal;
            $html .= '
                <tr>
                    <td>' . ($key + 1) . '</td>
                    <td>' . ($item->product->name ?? 'Product ' . $item->product_id) . '</td>
                    <td>' . $item->quantity . '</td>
                    <td>₹' . number_format($item->price, 2) . '</td>
                    <td>₹' . number_format($subtotal, 2) . '</td>
                </tr>';
        }
    } else {
        $html .= '
                <tr>
                    <td colspan="5" class="text-center">No items found</td>
                </tr>';
    }
    
    $html .= '
            </tbody>
            <tfoot>
                <tr>
                    <th colspan="4" class="text-end">Subtotal:</th>
                    <th>₹' . number_format($total, 2) . '</th>
                </tr>
                <tr>
                    <th colspan="4" class="text-end">Shipping:</th>
                    <th>₹0.00</th>
                </tr>
                <tr>
                    <th colspan="4" class="text-end">Tax:</th>
                    <th>₹0.00</th>
                </tr>
                <tr class="table-active">
                    <th colspan="4" class="text-end">Total Amount:</th>
                    <th>₹' . number_format($order->total_amount ?? $total, 2) . '</th>
                </tr>
            </tfoot>
        </table>
        
        <div class="row mt-4">
            <div class="col-12">
                <div class="alert alert-info">
                    <strong>Payment Status:</strong> 
                    <span class="badge bg-' . ($order->payment_status == 'success' ? 'success' : ($order->payment_status == 'failed' ? 'danger' : 'warning')) . '">
                        ' . ucfirst($order->payment_status ?? 'Pending') . '
                    </span>
                    <br>
                    <strong>Transaction ID:</strong> ' . ($order->transaction_id ?? 'N/A') . '
                </div>
            </div>
        </div>
        
        <div class="text-center mt-4">
            <p>Thank you for your business!</p>
        </div>
    </div>';
    
    return response()->json(['html' => $html]);
}

/**
 * Get payment details HTML for modal
 */
public function paymentDetails($id)
{
    $order = Order::with(['customer', 'items.product'])->findOrFail($id);
    
    $html = '
    <div class="payment-details">
        <div class="row">
            <div class="col-md-6">
                <div class="card mb-3">
                    <div class="card-header bg-light">
                        <strong>Order Information</strong>
                    </div>
                    <div class="card-body">
                        <table class="table table-sm">
                            <tr><th>Order ID:</th><td>#' . $order->id . '</td></tr>
                            <tr><th>Order Date:</th><td>' . ($order->created_at ? $order->created_at->format('Y-m-d H:i:s') : 'N/A') . '</td></tr>
                            <tr><th>Customer:</th><td>' . ($order->customer->name ?? $order->recipient_name ?? 'N/A') . '</td></tr>
                            <tr><th>Email:</th><td>' . ($order->customer->email ?? 'N/A') . '</td></tr>
                            <tr><th>Phone:</th><td>' . ($order->customer->phone ?? $order->phone ?? 'N/A') . '</td></tr>
                            <tr><th>Location:</th><td>' . ($order->location ?? $order->city ?? 'N/A') . '</td></tr>
                        </table>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card mb-3">
                    <div class="card-header bg-light">
                        <strong>Payment Information</strong>
                    </div>
                    <div class="card-body">
                        <table class="table table-sm">
                            <tr><th>Total Amount:</th><td class="fw-bold">₹' . number_format($order->total_amount ?? 0, 2) . '</td></tr>
                            <tr><th>Payment Type:</th><td>' . ucfirst(str_replace('_', ' ', $order->payment_type ?? 'N/A')) . '</td></tr>
                            <tr><th>Transaction ID:</th><td><code>' . ($order->transaction_id ?? 'N/A') . '</code></td></tr>
                            <tr><th>Payment Status:</th>
                                <td>
                                    <span class="badge bg-' . ($order->payment_status == 'success' ? 'success' : ($order->payment_status == 'failed' ? 'danger' : 'warning')) . '">
                                        ' . ucfirst($order->payment_status ?? 'Pending') . '
                                    </span>
                                </td>
                            </tr>
                            <tr><th>Payment Date:</th><td>' . ($order->payment_date ? date('Y-m-d H:i:s', strtotime($order->payment_date)) : 'N/A') . '</td></tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header bg-light">
                        <strong>Fraud Detection Details</strong>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4">
                                <strong>Fraud Score:</strong>
                                <div class="progress mt-2" style="height: 10px;">
                                    <div class="progress-bar bg-' . ($order->fraud_score < 30 ? 'success' : ($order->fraud_score < 70 ? 'warning' : 'danger')) . '" 
                                         style="width: ' . ($order->fraud_score ?? 0) . '%">
                                    </div>
                                </div>
                                <p class="mt-2">' . ($order->fraud_score ?? 0) . '%</p>
                            </div>
                            <div class="col-md-4">
                                <strong>Fraud Status:</strong><br>
                                <span class="badge bg-' . ($order->fraud_status == 'clean' ? 'success' : ($order->fraud_status == 'suspicious' ? 'warning' : 'danger')) . ' mt-2">
                                    ' . ucfirst($order->fraud_status ?? 'Clean') . '
                                </span>
                            </div>
                            <div class="col-md-4">
                                <strong>Fraud Reason:</strong><br>
                                <p class="mt-2">' . ($order->fraud_reason ?? 'No fraud detected') . '</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="row mt-3">
            <div class="col-12">
                <div class="card">
                    <div class="card-header bg-light">
                        <strong>Products</strong>
                    </div>
                    <div class="card-body">
                        <table class="table table-sm">
                            <thead>
                                <tr><th>Product</th><th>Quantity</th><th>Price</th><th>Total</th></tr>
                            </thead>
                            <tbody>';
    
    if($order->items && $order->items->count() > 0) {
        foreach($order->items as $item) {
            $html .= '<tr>
                        <td>' . ($item->product->name ?? 'Product ' . $item->product_id) . '</td>
                        <td>' . $item->quantity . '</td>
                        <td>₹' . number_format($item->price, 2) . '</td>
                        <td>₹' . number_format($item->quantity * $item->price, 2) . '</td>
                      </tr>';
        }
    } else {
        $html .= '<tr><td colspan="4" class="text-center">No items found</td></tr>';
    }
    
    $html .= '
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>';
    
    return response()->json(['html' => $html]);
}


public function exportOrders(Request $request)
{
    $query = Order::with(['customer', 'items.product']);
    
    if ($request->filled('status')) {
        $query->where('status', $request->status);
    }
    if ($request->filled('seller')) {
        $query->whereHas('items.product', function($q) use ($request) {
            $q->where('seller_id', $request->seller);
        });
    }
    if ($request->filled('product')) {
        $query->whereHas('items', function($q) use ($request) {
            $q->where('product_id', $request->product);
        });
    }
    
    $orders = $query->orderBy('id', 'desc')->get();
    
    // Generate CSV
    $filename = 'orders_export_' . date('Y-m-d_His') . '.csv';
    $handle = fopen('php://temp', 'w');
    
    // Add headers
    fputcsv($handle, ['Order ID', 'Customer Name', 'Customer Email', 'Product', 'Quantity', 'Amount', 'Status', 'Order Date']);
    
    foreach ($orders as $order) {
        fputcsv($handle, [
            '#' . $order->id,
            $order->customer->name ?? 'N/A',
            $order->customer->email ?? 'N/A',
            $order->items->first()->product->name ?? 'N/A',
            $order->items->first()->quantity ?? 1,
            $order->total_amount ?? $order->total ?? 0,
            $order->status ?? 'pending',
            $order->created_at ? $order->created_at->format('Y-m-d H:i:s') : ''
        ]);
    }
    
    rewind($handle);
    $content = stream_get_contents($handle);
    fclose($handle);
    
    return response($content, 200, [
        'Content-Type' => 'text/csv',
        'Content-Disposition' => "attachment; filename=\"$filename\"",
    ]);
}


public function showManageOrder(Request $request, $orderId){
    
    
    
    $order = Order::findOrFail($orderId);
    
    return view ('admin.orders.manage',compact('order'));
}
}

