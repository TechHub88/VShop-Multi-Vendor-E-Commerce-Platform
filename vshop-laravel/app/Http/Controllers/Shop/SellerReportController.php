<?php

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;

class SellerReportController extends Controller
{
    /* ============================================================
       BASE QUERY (DO NOT TOUCH MODELS)
    ============================================================ */
    private function sellerOrderItems()
    {
        $sellerId = auth()->guard('seller')->id();

        return OrderItem::whereHas('product', function ($q) use ($sellerId) {
            $q->where('created_by', 'seller')
              ->where('created_by_id', $sellerId);
        });
    }

    /* ============================================================
       DAILY SALES REPORT
    ============================================================ */
    public function index(Request $request)
{
    $sellerId = auth()->guard('seller')->id();

    /*
    |----------------------------------------------------------
    | BASE QUERY → ONLY SELLER PRODUCTS ORDERS
    |----------------------------------------------------------
    */
    $orderQuery = Order::whereHas('items.product', function ($q) use ($sellerId) {
        $q->where('created_by', 'seller')
          ->where('created_by_id', $sellerId);
    });

    /*
    |----------------------------------------------------------
    | DATE FILTER
    |----------------------------------------------------------
    */
    if ($request->date_from) {
        $orderQuery->whereDate('created_at', '>=', $request->date_from);
    }

    if ($request->date_to) {
        $orderQuery->whereDate('created_at', '<=', $request->date_to);
    }

    /*
    |----------------------------------------------------------
    | DAILY SALES GROUPING (FOR LINE CHART + TABLE)
    |----------------------------------------------------------
    */
    $sales = (clone $orderQuery)
        ->select(
            DB::raw('DATE(created_at) as date'),
            DB::raw('COUNT(id) as total_orders'),
            DB::raw('COUNT(DISTINCT customer_id) as customers'),
            DB::raw('SUM(total_amount) as amount')
        )
        ->groupBy('date')
        ->orderByDesc('date')
        ->get();   // ⚠️ pagination nahi → chart ke liye full data

    /*
    |----------------------------------------------------------
    | GRAND TOTAL SALES
    |----------------------------------------------------------
    */
    $grandTotal = (clone $orderQuery)->sum('total_amount');

    /*
    |----------------------------------------------------------
    | ALL ORDERS LIST (FOR TABLE)
    |----------------------------------------------------------
    */
    $orders = (clone $orderQuery)
        ->with('customer')
        ->latest()
        ->get();

    /*
    |----------------------------------------------------------
    | RETURN SELLER VIEW
    |----------------------------------------------------------
    */
    return view('vshop.seller.reports.index', compact(
        'sales',
        'grandTotal',
        'orders'
    ));
}


    /* ============================================================
       DASHBOARD SUMMARY
    ============================================================ */
    
    /* ============================================================
       EXPORT CSV
    ============================================================ */
    public function exportCSV()
    {
        $items = $this->sellerOrderItems()
            ->with('order.customer', 'product')
            ->get();

        $fileName = "seller_sales_" . date('Ymd') . ".csv";

        $headers = [
            "Content-type" => "text/csv",
            "Content-Disposition" => "attachment; filename=$fileName",
        ];

        $callback = function () use ($items) {
            $file = fopen('php://output', 'w');

            fputcsv($file, ['Order ID', 'Product', 'Customer', 'Amount', 'Date']);

            foreach ($items as $item) {
                fputcsv($file, [
                    $item->order_id,
                    $item->product->name ?? '',
                    $item->order->customer->name ?? '',
                    $item->total,
                    $item->created_at,
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /* ============================================================
       EXPORT PDF
    ============================================================ */
    public function exportPDF()
    {
        $items = $this->sellerOrderItems()
            ->with('order.customer', 'product')
            ->get();

        $pdf = Pdf::loadView('vshop.seller.reports.pdf', compact('items'))
            ->setPaper('a4', 'landscape');

        return $pdf->download('seller_sales.pdf');
    }

    /* ============================================================
       AJAX SALES SEARCH
    ============================================================ */
    public function searchSales(Request $request)
    {
        $query = $this->sellerOrderItems();

        if ($request->keyword) {
            $query->where('order_id', 'LIKE', "%{$request->keyword}%");
        }

        if ($request->date_from) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->date_to) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $items = $query->with('order.customer', 'product')->latest()->get();

        return response()->json([
            'success' => true,
            'total_sales' => $items->sum('total'),
            'total_orders' => $items->pluck('order_id')->unique()->count(),
            'data' => $items
        ]);
    }
}
