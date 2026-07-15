<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\OrderReturn;
use App\Models\RefundReason;
use App\Models\Brand;
use App\Models\Customer;
use App\Models\RefundProcess;
use App\Models\RefundSetting;


class RefundController extends Controller
{
    // Refund Requests
  public function requests(Request $request)
{
    $refundRequests = OrderReturn::with('customer','order')
        ->latest()
        ->get();

    $brands = Brand::all();

    $query = Customer::query();

    // Search filter
    if ($request->filled('search')) {
        $search = $request->search;
        $query->where(function ($q) use ($search) {
            $q->where('name', 'like', "%{$search}%")
              ->orWhere('email', 'like', "%{$search}%");
        });
    }

    // Brand filter
    if ($request->filled('brand_id')) {
        $query->where('brand_id', $request->brand_id);
    }

    // Status filter
    if ($request->filled('customer_status')) {
        $query->where('status', $request->customer_status);
    } else {
        // Default list is customers with status = 'Refund' if no status filter is selected, or we can keep it as is
        $query->where('status', 'Refund');
    }

    // Sorting
    $sort = $request->sort ?? 'newest';
    if ($sort == 'name_asc') {
        $query->orderBy('name', 'asc');
    } elseif ($sort == 'name_desc') {
        $query->orderBy('name', 'desc');
    } elseif ($sort == 'oldest') {
        $query->oldest();
    } else {
        $query->latest();
    }

    $customers = $query->paginate(10)->withQueryString();

    return view('admin.refund.requests', compact('refundRequests', 'brands', 'customers'));
}

    // Refund Reasons
    public function reasons()
    {
        $reasons = RefundReason::latest()->get();
        return view('admin.refund.reasons', compact('reasons'));
    }
    
      public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'is_active' => 'required|boolean',
        ]);

        RefundReason::create($request->only('title', 'is_active'));

        return redirect()->route('admin.refund.reasons')
                         ->with('success', 'Refund Reason created successfully.');
    }

    // Show edit form
    public function edit($id)
    {
        $reason = RefundReason::findOrFail($id);
        return view('admin.refund.reasons.edit', compact('reason'));
    }

    // Update reason
    public function update(Request $request, $id)
    {
        $reason = RefundReason::findOrFail($id);

        $request->validate([
            'title' => 'required|string|max:255',
            'is_active' => 'required|boolean',
        ]);

        $reason->update($request->only('title', 'is_active'));

        return redirect()->route('refund.reasons.create')
                         ->with('success', 'Refund Reason updated successfully.');
    }

    // Delete reason
    public function destroy($id)
    {
        $reason = RefundReason::findOrFail($id);
        $reason->delete();

        return redirect()->route('admin.refund.reasons')
                         ->with('success', 'Refund Reason deleted successfully.');
    }

    // Refund Process
    public function process()
    {
        $processes = RefundProcess::latest()->get();
        return view('admin.refund.process', compact('processes'));
    }

    // Refund Settings
    public function settings()
    {
        $settings = RefundSetting::first(); // single settings row
        return view('admin.refund.settings', compact('settings'));
    }
    
    // Store new refund process
public function storeProcess(Request $request)
{
    $request->validate([
        'step_name' => 'required|string|max:255',
        'description' => 'nullable|string',
        'is_active' => 'required|boolean',
    ]);

    RefundProcess::create($request->only('step_name', 'description', 'is_active'));

    return redirect()->route('admin.refund.process')
                     ->with('success', 'Refund Process created successfully.');
}

// Show edit form for refund process
public function editProcess($id)
{
    $process = RefundProcess::findOrFail($id);
    return view('admin.refund.process_edit', compact('process'));
}

// Update refund process
public function updateProcess(Request $request, $id)
{
    $process = RefundProcess::findOrFail($id);

    $request->validate([
        'step_name' => 'required|string|max:255',
        'description' => 'nullable|string',
        'is_active' => 'required|boolean',
    ]);

    $process->update($request->only('step_name', 'description', 'is_active'));

    return redirect()->route('admin.refund.process')
                     ->with('success', 'Refund Process updated successfully.');
}

// Delete refund process
public function destroyProcess($id)
{
    $process = RefundProcess::findOrFail($id);
    $process->delete();

    return redirect()->route('admin.refund.process')
                     ->with('success', 'Refund Process deleted successfully.');
}

public function search(Request $request)
{
    try {
        $query = RefundRequest::with(['order', 'customer']);
        
        // Keyword search
        if ($request->keyword) {
            $keyword = $request->keyword;
            $query->where(function($q) use ($keyword) {
                $q->whereHas('order', function($q2) use ($keyword) {
                    $q2->where('id', 'like', "%{$keyword}%");
                })->orWhereHas('customer', function($q2) use ($keyword) {
                    $q2->where('name', 'like', "%{$keyword}%")
                       ->orWhere('email', 'like', "%{$keyword}%");
                });
            });
        }
        
        // Status filter
        if ($request->status) {
            $query->where('status', $request->status);
        }
        
        // Date range filter
        if ($request->date_from) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->date_to) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }
        
        // For export, return all data
        if ($request->export) {
            $refunds = $query->get()->map(function($refund) {
                return [
                    'order_id' => $refund->order->id ?? null,
                    'customer_name' => $refund->customer->name ?? null,
                    'amount' => $refund->order->total_amount ?? 0,
                    'status' => $refund->status,
                    'created_at_formatted' => $refund->created_at->format('d M Y')
                ];
            });
        } else {
            // For table display, paginate or limit
            $refunds = $query->latest()->paginate(50);
        }
        
        return response()->json([
            'success' => true,
            'data' => $refunds
        ]);
        
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => $e->getMessage()
        ], 500);
    }
}
}
