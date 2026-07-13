<?php

namespace App\Http\Controllers\admin;

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
        $query = Order::query()->with(['seller', 'customer']);
        
        // Apply filters
        if ($request->filled('seller')) {
            $query->where('seller_id', $request->seller);
        }
        
        if ($request->filled('product')) {
            $query->whereHas('items', function($q) use ($request) {
                $q->where('product_id', $request->product);
            });
        }
        
        if ($request->filled('location')) {
            $query->where(function($q) use ($request) {
                $q->where('location', 'like', '%' . $request->location . '%')
                  ->orWhere('city', 'like', '%' . $request->location . '%');
            });
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

        if ($request->filled('date_range')) {
            switch ($request->date_range) {
                case 'today':
                    $query->whereDate('created_at', now()->today());
                    break;
                case 'yesterday':
                    $query->whereDate('created_at', now()->yesterday());
                    break;
                case 'this_week':
                    $query->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()]);
                    break;
                case 'last_week':
                    $query->whereBetween('created_at', [now()->subWeek()->startOfWeek(), now()->subWeek()->endOfWeek()]);
                    break;
                case 'this_month':
                    $query->whereMonth('created_at', now()->month)->whereYear('created_at', now()->year);
                    break;
                case 'last_month':
                    $query->whereMonth('created_at', now()->subMonth()->month)->whereYear('created_at', now()->subMonth()->year);
                    break;
                case 'this_year':
                    $query->whereYear('created_at', now()->year);
                    break;
                case 'last_year':
                    $query->whereYear('created_at', now()->subYear()->year);
                    break;
            }
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('id', 'like', "%{$search}%")
                  ->orWhere('order_no', 'like', "%{$search}%")
                  ->orWhereHas('customer', function($q2) use ($search) {
                      $q2->where('name', 'like', "%{$search}%")
                         ->orWhere('email', 'like', "%{$search}%");
                  });
            });
        }
        
        $orders = $query->latest()->paginate(15);
        
        // Compute KPI Counts accurately
        $directOrdersCount = Order::whereNull('referrer_id')->count();
        $completedOrdersCount = Order::whereIn('status', ['Completed', 'completed', 'delivered', 'Delivered'])->count();
        $cancelledOrdersCount = Order::whereIn('status', ['Cancelled', 'cancelled'])->count();
        $pendingOrdersCount = Order::whereIn('status', ['Pending', 'pending'])->count();

        // Get data for filters optimized to prevent memory limit issues
        $sellers = Seller::select('id', 'name')->limit(300)->get(); 
        $products = Product::select('id', 'name')->limit(300)->get(); 
        $cities = City::select('name')->orderBy('name')->limit(300)->get();
        
        return view('admin.orders.index', compact(
            'orders', 'sellers', 'products', 'cities',
            'directOrdersCount', 'completedOrdersCount', 'cancelledOrdersCount', 'pendingOrdersCount'
        ));
    }

    public function invoice(Request $request, $id)
    {
        $order = Order::with([
            'customer.firstAddress',
            'seller',
            'items.product',
            'items.color',
            'items.size'
        ])->findOrFail($id);

        if ($request->has('download') || $request->query('export') === 'pdf') {
            if (class_exists(\Barryvdh\DomPDF\Facade\Pdf::class)) {
                $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('admin.orders.invoice', compact('order'));
                return $pdf->download('Invoice_' . ($order->order_no ?? $order->id) . '.pdf');
            }
        }

        return view('admin.orders.invoice', compact('order'));
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
    
    $returns = \App\Models\OrderReturn::with(['customer', 'order.items.product'])
        ->when($request->request_type, function($q) use ($request) {
            $q->where('return_type', $request->request_type);
        })
        ->when($request->status, function($q) use ($request) {
            $q->where('status', $request->status);
        })
        ->when($request->seller, function($q) use ($request) {
            $q->whereHas('order.items.product', function($q2) use ($request) {
                $q2->where('seller_id', $request->seller);
            });
        })
        ->when($request->product, function($q) use ($request) {
            $q->whereHas('order.items', function($q2) use ($request) {
                $q2->where('product_id', $request->product);
            });
        })
        ->orderBy('id', 'desc')
        ->paginate(10);

    return view('admin.orders.cancellation', compact('returns', 'sellers', 'products', 'cities'));
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
        
        // ========== FILTER DROPDOWN DATA (Optimized) ==========
        
        $sellers = Seller::select('id', 'name')->orderBy('name')->limit(300)->get();
        $products = Product::select('id', 'name')->orderBy('name')->limit(300)->get();
        $cities = City::select('name')->orderBy('name')->limit(300)->get();
        
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

    /* =====================================================
     *  PAYMENT MONITORING ACTIONS
     * ===================================================== */
    public function getPaymentDetails($id)
    {
        try {
            $order = Order::with(['customer', 'seller', 'items.product'])->findOrFail($id);
            $html = view('admin.orders.partials.payment_details', compact('order'))->render();
            return response()->json(['success' => true, 'html' => $html]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function paymentDetails($id)
    {
        return $this->getPaymentDetails($id);
    }

    public function validateInvoice($id)
    {
        $order = Order::with('items')->findOrFail($id);
        
        $errors = [];
        if (in_array(strtolower($order->payment_type), ['credit_card', 'debit_card', 'upi', 'paypal', 'net_banking']) && empty($order->transaction_id)) {
            $errors[] = 'Missing transaction ID for online payment.';
        }
        
        if (strtolower($order->payment_status) !== 'success' && strtolower($order->payment_status) !== 'paid' && strtolower($order->payment_status) !== 'completed') {
            $errors[] = 'Payment status is ' . ucfirst($order->payment_status) . ' (not successful).';
        }
        
        $calculatedTotal = 0;
        if($order->items) {
            foreach ($order->items as $item) {
                $calculatedTotal += ($item->price * $item->quantity);
            }
        }
        
        if (empty($order->total_amount) || $order->total_amount <= 0) {
            $errors[] = 'Order total is zero or missing.';
        } else if ($calculatedTotal > 0 && abs($calculatedTotal - $order->total_amount) > 100) {
            $errors[] = 'Order total (₹' . number_format($order->total_amount, 2) . ') mismatches with items total (₹' . number_format($calculatedTotal, 2) . ').';
        }
        
        $isValid = count($errors) === 0;
        
        return response()->json([
            'valid' => $isValid,
            'message' => $isValid 
                ? '<strong>Invoice Validated & Verified.</strong><br>Order #' . $order->id . ' has matching payment records and total (₹' . number_format($order->total_amount, 2) . ').' 
                : '<strong>Invoice Validation Failed!</strong><br>' . implode('<br>', $errors)
        ]);
    }

    public function runFraudDetection($id)
    {
        $order = Order::findOrFail($id);
        
        $fraudScore = 0;
        $reasons = [];

        if (strtolower($order->payment_status) === 'failed') {
            $fraudScore += 10;
            $reasons[] = 'Payment failed previously.';
        }

        if ($order->total_amount > 10000) {
            $fraudScore += 20;
            $reasons[] = 'Unusually high transaction amount (>' . number_format(10000, 2) . ').';
        }

        if (in_array(strtolower($order->payment_type), ['credit_card', 'debit_card', 'upi', 'paypal']) && empty($order->transaction_id)) {
            $fraudScore += 40;
            $reasons[] = 'Missing transaction ID for online payment.';
        }

        if (empty($order->customer_id)) {
            $fraudScore += 15;
            $reasons[] = 'Guest checkout.';
        }

        if ($fraudScore >= 70) {
            $order->fraud_status = 'flagged';
        } elseif ($fraudScore >= 30) {
            $order->fraud_status = 'suspicious';
        } else {
            $order->fraud_status = 'clean';
        }

        $order->fraud_score = $fraudScore;
        $order->fraud_reason = empty($reasons) ? null : implode(' | ', $reasons);
        $order->save();

        return response()->json([
            'success' => true,
            'score' => $fraudScore,
            'status' => ucfirst($order->fraud_status),
            'reasons' => $reasons,
            'message' => 'Fraud detection completed. Score: ' . $fraudScore . '%'
        ]);
    }

    public function markSuspicious($id)
    {
        $order = Order::findOrFail($id);
        $order->fraud_status = 'suspicious';
        $order->fraud_score = max(50, $order->fraud_score ?? 50);
        $order->save();
        return response()->json(['success' => true, 'message' => 'Payment marked as suspicious']);
    }

    public function flagFraud(Request $request, $id)
    {
        $order = Order::findOrFail($id);
        $order->fraud_status = 'flagged';
        $order->fraud_score = 95;
        $order->fraud_reason = $request->reason ?? 'Flagged by admin';
        $order->save();
        return response()->json(['success' => true, 'message' => 'Payment flagged as fraud']);
    }

    public function clearFraudFlag($id)
    {
        $order = Order::findOrFail($id);
        $order->fraud_status = 'clean';
        $order->fraud_score = 0;
        $order->fraud_reason = null;
        $order->save();
        return response()->json(['success' => true, 'message' => 'Fraud flag cleared']);
    }

    public function refundPayment(Request $request, $id)
    {
        $order = Order::findOrFail($id);
        $order->payment_status = 'refunded';
        $order->refund_status = 'completed';
        $order->refund_amount = $request->amount ?? $order->total_amount;
        $order->refund_reason = $request->reason ?? 'Refunded by admin';
        $order->refunded_at = now();
        $order->save();
        return response()->json(['success' => true, 'message' => 'Payment refunded successfully.']);
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


    /* =====================================================
     *  UPDATE ORDER STATUS (from Admin Order Table)
     * ===================================================== */
    public function updateStatus(Request $request, $id)
    {
        try {
            $order = Order::findOrFail($id);

            $oldStatus = $order->status;
            $newStatus = $request->status ?: $request->prev_status;

            $order->status     = $newStatus;
            $order->start_date = $request->start_date ?: $order->start_date;
            $order->end_date   = $request->end_date ?: $order->end_date;
            $order->remarks    = $request->remarks ?? $order->remarks;
            $order->privacy    = $request->privacy ?? $order->privacy;

            if (strtolower($newStatus) === 'cancelled') {
                Order::reverseOrderFunds($order);
            }

            // ── Referral commission on delivery ──
            if (
                strtolower($newStatus) === 'delivered' ||
                strtolower($newStatus) === 'completed'
            ) {
                if ($order->referrer_id && !$order->referral_paid) {
                    $commissionPercent = 3.0;
                    $commissionAmount  = ($order->total_amount * $commissionPercent) / 100;

                    // Deduct from Seller Wallet
                    $sellerWallet = \App\Models\Wallet::firstOrCreate(
                        ['seller_id' => $order->seller_id, 'user_type' => 'seller'],
                        ['balance' => 0]
                    );
                    $sellerWallet->decrement('balance', $commissionAmount);

                    // Credit to Referrer (buyer) Wallet
                    $referrerWallet = \App\Models\Wallet::firstOrCreate(
                        ['customer_id' => $order->referrer_id, 'user_type' => 'customer'],
                        ['balance' => 0]
                    );
                    $referrerWallet->increment('balance', $commissionAmount);

                    // Log referral history
                    \App\Models\ReferralHistory::create([
                        'referrer_id'        => $order->referrer_id,
                        'buyer_id'           => $order->customer_id,
                        'seller_id'          => $order->seller_id,
                        'order_id'           => $order->id,
                        'amount'             => $commissionAmount,
                        'commission_percent' => $commissionPercent,
                        'status'             => 'paid',
                    ]);

                    $order->referral_commission_percent = $commissionPercent;
                    $order->referral_commission_amount  = $commissionAmount;
                    $order->referral_paid               = true;
                }
            }

            $order->save();

            // Activity log
            $this->logActivity([
                'order_id'      => $order->id,
                'user_id'       => Auth::id(),
                'customer_id'   => $order->customer_id,
                'activity_type' => 'status_update',
                'old_status'    => $oldStatus,
                'new_status'    => $newStatus,
                'description'   => "Admin updated order #{$order->id} status from {$oldStatus} to {$newStatus}",
                'remarks'       => $request->remarks,
                'ip_address'    => $request->ip(),
                'user_agent'    => $request->userAgent(),
            ]);

            // Notify customer
            if (class_exists(\App\Services\NotificationService::class)) {
                \App\Services\NotificationService::notify(
                    $order->customer_id,
                    'customer',
                    'order',
                    "Your order #{$order->id} status has been updated to {$newStatus}.",
                    url('/order/' . $order->id)
                );
            }

            return back()->with('success', 'Order status updated successfully.');

        } catch (\Exception $e) {
            return back()->with('error', 'Failed to update: ' . $e->getMessage());
        }
    }
}

