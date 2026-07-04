<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\State;
use App\Models\TaxRule;

class PaymentFinanceController extends Controller
{
  


public function index()
{
    return view('admin.payment_finance.index', [

        // ✅ Only Refund Orders
        'refund_requests' => Order::where('status', 'Refund')
            ->orderBy('id', 'desc')
            ->get(),

        // ✅ Only Successful Transactions
        'transactions' => Order::whereIn('payment_status', ['paid', 'completed'])
            ->orderBy('id', 'desc')
            ->get(),

        // ✅ Unique Payment Methods
        'payment_methods' => Order::select('payment_method')
            ->whereNotNull('payment_method')
            ->distinct()
            ->pluck('payment_method'),
    ]);
}

public function filter(Request $request)
{
    $query = Order::query();

    // Filter: payment method
    if ($request->payment_method) {
        $query->where('payment_method', $request->payment_method);
    }

    // Filter: date range
    if ($request->from_date) {
        $query->whereDate('created_at', '>=', $request->from_date);
    }

    if ($request->to_date) {
        $query->whereDate('created_at', '<=', $request->to_date);
    }

    // Only successful transaction types
    $query->whereIn('payment_status', ['paid', 'completed']);

    $transactions = $query->orderBy('id', 'desc')->get();

    return view('admin.payment_finance.index', [
        'transactions'     => $transactions,
        'refund_requests'  => Order::where('status', 'Refund')->orderBy('id', 'desc')->get(),
        'payment_methods'  => Order::select('payment_method')->distinct()->pluck('payment_method'),
    ]);
}



public function transactions()
{
    return view('admin.payment_finance.transactions', [

        // Refund Orders Only
        'refund_requests' => Order::where('status', 'Refund')
            ->orderBy('id', 'desc')
            ->get(),

        // All successful/paid transactions
        'transactions' => Order::whereIn('payment_status', ['paid', 'completed'])
            ->orderBy('id', 'desc')
            ->get(),

        // Fetch unique payment methods for filter dropdown
        'payment_methods' => Order::select('payment_method')
            ->whereNotNull('payment_method')
            ->distinct()
            ->pluck('payment_method'),
    ]);
}

public function taxes()
{
    return view('admin.payment_finance.taxes', [
        'taxes'  => TaxRule::with('state')->get(),
        'states' => State::orderBy('name')->get(),
    ]);
}


public function storeTax(Request $request)
{
    $request->validate([
        'state_id' => 'required',
        'gst'      => 'required|numeric',
        'cgst'     => 'nullable|numeric',
        'sgst'     => 'nullable|numeric',
        'igst'     => 'nullable|numeric',
    ]);

    TaxRule::create($request->all());

    return back()->with('success', 'Tax rule added successfully!');
}






}
