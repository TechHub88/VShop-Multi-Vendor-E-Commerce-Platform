<?php


namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ActivityLog;
use App\Models\Customer;



class ActivityLogController extends Controller
{
   
public function index(Request $request)
{
    // Start query with relationships
    $query = ActivityLog::with(['customer', 'order', 'user'])
        ->orderBy('created_at', 'desc');

    // Apply filters
    if ($request->has('type') && $request->type) {
        $query->where('activity_type', $request->type);
    }

    if ($request->has('customer') && $request->customer) {
        $query->where('customer_id', $request->customer);
    }

    if ($request->has('date') && $request->date) {
        $query->whereDate('created_at', $request->date);
    }

    if ($request->has('search') && $request->search) {
        $search = $request->search;
        $query->where(function($q) use ($search) {
            // Search in description and remarks
            $q->where('description', 'like', "%{$search}%")
              ->orWhere('remarks', 'like', "%{$search}%")
              ->orWhere('activity_type', 'like', "%{$search}%");
            
            // Search in customer name/email
            $q->orWhereHas('customer', function($q2) use ($search) {
                $q2->where('name', 'like', "%{$search}%")
                   ->orWhere('email', 'like', "%{$search}%");
            });
            
            // Handle order number search (remove # and leading zeros)
            $orderSearch = preg_replace('/[^0-9]/', '', $search);
            if (is_numeric($orderSearch)) {
                $q->orWhereHas('order', function($q2) use ($orderSearch) {
                    $q2->where('id', $orderSearch);
                });
                
                // Also search by order_id directly
                $q->orWhere('order_id', $orderSearch);
            }
        });
    }

    // Get all customers for filter dropdown
    $customers = Customer::orderBy('name')->get(['id', 'name']);

    // Paginate results
    $logs = $query->paginate(25)->withQueryString();

    return view('admin.activitylog.index', compact('logs', 'customers'));
}

public function destroy($id)
{
    try {
        $log = ActivityLog::findOrFail($id);
        $log->delete();
        
        // For AJAX requests
        if (request()->ajax() || request()->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Activity log deleted successfully.'
            ]);
        }
        
        // For regular form submissions
        return redirect()->route('admin.activity-logs.index')
            ->with('success', 'Activity log deleted successfully.');
            
    } catch (\Exception $e) {
        if (request()->ajax() || request()->wantsJson()) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete activity log: ' . $e->getMessage()
            ], 500);
        }
        
        return redirect()->route('admin.activity-logs.index')
            ->with('error', 'Failed to delete activity log: ' . $e->getMessage());
    }
}
}
