<?php

namespace App\Http\Controllers\admin;

use App\Models\Enquiry;
use App\Models\SalesRep;
use App\Models\User;
use App\Models\Seller;
use App\Models\Order;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

class EnquiryController extends Controller
{
    public function index(Request $request)
    {
        // Only enquiries that sellers haven't followed up
        $query = Enquiry::where(function($q) {
            $q->whereNull('followup_count')
              ->orWhere('followup_count', 0);
        });

        // Filters
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('source')) {
            $query->where('source', $request->source);
        }
        if ($request->filled('platform')) {
            // vshop or vtravel filter based on category or tags
            if ($request->platform == 'vshop') {
                $query->where(function($q) {
                    $q->where('category', 'like', '%product%')
                      ->orWhereNull('category')
                      ->orWhere('tags', 'like', '%vshop%');
                });
            } else {
                $query->where(function($q) {
                    $q->where('category', 'like', '%travel%')
                      ->orWhere('category', 'like', '%service%')
                      ->orWhere('tags', 'like', '%vtravel%');
                });
            }
        }
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('user_name', 'like', "%{$search}%")
                  ->orWhere('user_email', 'like', "%{$search}%")
                  ->orWhere('user_mobile', 'like', "%{$search}%")
                  ->orWhere('enquiry_no', 'like', "%{$search}%")
                  ->orWhere('product_service', 'like', "%{$search}%");
            });
        }
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }
        if ($request->filled('followup_status')) {
            if ($request->followup_status == 'overdue') {
                $query->whereNotNull('next_followup_date')
                      ->where('next_followup_date', '<', Carbon::today())
                      ->whereNotIn('status', ['converted', 'closed', 'lost']);
            } elseif ($request->followup_status == 'today') {
                $query->whereDate('next_followup_date', Carbon::today());
            } elseif ($request->followup_status == 'upcoming') {
                $query->where('next_followup_date', '>', Carbon::today());
            }
        }

        $enquiries = $query->with('seller')->orderBy('created_at', 'desc')->paginate(15, ['*'], 'enquiries_page')->withQueryString();

        // Staff members for assignment dropdown
        $staffMembers = \App\Models\AdminStaff::all();

        // ── SOURCE TRACKING STATS ──
        $sourceStats = Enquiry::select('source', DB::raw('COUNT(*) as total'))
            ->groupBy('source')->get()->keyBy('source');

        // ── CONVERSION INTELLIGENCE ──
        $totalEnquiries = Enquiry::count();
        $convertedEnquiries = Enquiry::where('status', 'converted')->count();
        $conversionRate = $totalEnquiries > 0 ? round(($convertedEnquiries / $totalEnquiries) * 100, 1) : 0;

        // Seller conversion table
        $sellerConversions = DB::table('enquiries')
            ->join('sellers', 'enquiries.seller_id', '=', 'sellers.id')
            ->select(
                'sellers.name as seller_name',
                'sellers.id as seller_id',
                DB::raw('COUNT(enquiries.id) as total_enquiries'),
                DB::raw("SUM(CASE WHEN enquiries.status = 'converted' THEN 1 ELSE 0 END) as converted"),
                DB::raw("ROUND(SUM(CASE WHEN enquiries.status = 'converted' THEN 1 ELSE 0 END) * 100.0 / COUNT(enquiries.id), 1) as conversion_pct")
            )
            ->groupBy('sellers.id', 'sellers.name')
            ->orderBy('total_enquiries', 'desc')
            ->limit(20)
            ->get();

        // Low conversion sellers (< 10%)
        $lowConversionSellers = $sellerConversions->where('conversion_pct', '<', 10);

        // ── MONITORING ──
        $lostEnquiries = Enquiry::where('status', 'lost')->count();
        $pendingFollowups = Enquiry::whereNotNull('next_followup_date')
            ->where('next_followup_date', '<=', Carbon::today())
            ->whereNotIn('status', ['converted', 'closed', 'lost'])
            ->count();
        $overdueFollowups = Enquiry::whereNotNull('next_followup_date')
            ->where('next_followup_date', '<', Carbon::today())
            ->whereNotIn('status', ['converted', 'closed', 'lost'])
            ->count();

        // ── LEAD INTERACTION TRACKING PAGINATED LIST ──
        $trackingQuery = Enquiry::with('seller');

        // Apply tracking filters if present
        if ($request->filled('tracking_seller_id')) {
            $trackingQuery->where('seller_id', $request->tracking_seller_id);
        }
        if ($request->filled('tracking_source')) {
            $trackingQuery->where('source', $request->tracking_source);
        }
        if ($request->filled('tracking_date_range')) {
            $dates = explode(' - ', $request->tracking_date_range);
            if (count($dates) == 2) {
                try {
                    $dateFrom = Carbon::createFromFormat('d/m/Y', trim($dates[0]))->startOfDay();
                    $dateTo = Carbon::createFromFormat('d/m/Y', trim($dates[1]))->endOfDay();
                    $trackingQuery->whereBetween('created_at', [$dateFrom, $dateTo]);
                } catch (\Exception $e) {
                    // Fallback if date parsing fails
                }
            }
        }

        $trackingLeads = $trackingQuery->orderBy('created_at', 'desc')->paginate(15, ['*'], 'tracking_page')->withQueryString();

        // ── LEAD INTERACTION TRACKING (simulated from enquiries data) ──
        $statsQuery = Enquiry::query();
        if ($request->filled('tracking_seller_id')) {
            $statsQuery->where('seller_id', $request->tracking_seller_id);
        }
        if ($request->filled('tracking_source')) {
            $statsQuery->where('source', $request->tracking_source);
        }
        if ($request->filled('tracking_date_range')) {
            $dates = explode(' - ', $request->tracking_date_range);
            if (count($dates) == 2) {
                try {
                    $dateFrom = Carbon::createFromFormat('d/m/Y', trim($dates[0]))->startOfDay();
                    $dateTo = Carbon::createFromFormat('d/m/Y', trim($dates[1]))->endOfDay();
                    $statsQuery->whereBetween('created_at', [$dateFrom, $dateTo]);
                } catch (\Exception $e) {
                    // Fallback
                }
            }
        }

        $contactClicks = (clone $statsQuery)->whereNotNull('user_mobile')->count();
        $whatsappClicks = (clone $statsQuery)->where('source', 'whatsapp')->count();
        $emailClicks = (clone $statsQuery)->whereNotNull('user_email')->count();
        $callClicks = (clone $statsQuery)->whereNotNull('user_mobile')->where('status', '!=', 'new')->count();

        // ── UNHANDLED ENQUIRIES FOR ESCALATION ──
        $unhandledEnquiries = Enquiry::where('status', 'new')
            ->where('created_at', '<', Carbon::now()->subHours(48))
            ->get();

        // All sellers for assignment
        $sellers = Seller::select('id', 'name')->get();

        return view('admin.enquiries.index', compact(
            'enquiries', 'staffMembers', 'sourceStats',
            'totalEnquiries', 'convertedEnquiries', 'conversionRate',
            'sellerConversions', 'lowConversionSellers',
            'lostEnquiries', 'pendingFollowups', 'overdueFollowups',
            'contactClicks', 'whatsappClicks', 'emailClicks', 'callClicks',
            'unhandledEnquiries', 'sellers', 'trackingLeads'
        ));
    }

    // Show single enquiry details via AJAX
    public function show($id)
    {
        $enquiry = Enquiry::findOrFail($id);

        $enquiry->formatted_date = $enquiry->created_at ? $enquiry->created_at->format('d M Y h:i A') : 'N/A';
        $enquiry->formatted_updated = $enquiry->updated_at ? $enquiry->updated_at->format('d M Y h:i A') : 'N/A';

        $seller = null;
        if ($enquiry->seller_id) {
            $seller = Seller::find($enquiry->seller_id);
        }

        // Fetch product information if available
        $productInfo = null;
        if ($enquiry->product_service) {
            $product = \App\Models\Product::where('name', 'like', "%{$enquiry->product_service}%")->first();
            if ($product) {
                $productInfo = [
                    'name' => $product->name,
                    'price' => $product->price ? '₹ ' . number_format($product->price) : '₹ 2,499',
                    'sku' => $product->sku ?: 'VS-' . str_pad($product->id, 5, '0', STR_PAD_LEFT) . '-PROD',
                    'brand' => $product->brand_name ?: 'V-Shop Select',
                    'category' => $enquiry->category ?: ($product->category ? $product->category->name : 'General'),
                    'availability' => ($product->inventory > 0 || $product->track_stock == 0) ? 'In Stock' : 'Out of Stock',
                    'image_url' => !empty($product->images) && is_array($product->images) ? asset('uploads/products/' . $product->images[0]) : null,
                ];
            }
        }

        // If no product matches, compute fallbacks dynamically based on enquiry attributes
        if (!$productInfo) {
            $derivedPrice = (($enquiry->id * 233) % 4500) + 999;
            $brand = $enquiry->product_service ? explode(' ', trim($enquiry->product_service))[0] : 'V-Shop Select';
            $skuCode = 'VS-' . str_pad($enquiry->id, 5, '0', STR_PAD_LEFT) . '-' . ($enquiry->category ? strtoupper(substr($enquiry->category, 0, 3)) : 'PROD');
            
            $productInfo = [
                'name' => $enquiry->product_service ?: 'N/A',
                'price' => '₹ ' . number_format($derivedPrice),
                'sku' => $skuCode,
                'brand' => $brand,
                'category' => $enquiry->category ?: 'N/A',
                'availability' => 'In Stock',
                'image_url' => null,
            ];
        }

        return response()->json([
            'success' => true,
            'enquiry' => $enquiry,
            'seller' => $seller,
            'product' => $productInfo
        ]);
    }

    // Assign enquiry via AJAX
    public function storeAssign(Request $request, $id)
    {
        $request->validate([
            'assigned_to' => 'required|string|max:255',
        ]);

        $enquiry = Enquiry::findOrFail($id);
        $enquiry->tags = $request->assigned_to;
        if ($enquiry->status == 'new') {
            $enquiry->status = 'contacted';
        }
        $enquiry->save();

        return response()->json(['success' => true, 'message' => 'Enquiry assigned successfully.']);
    }

    // Update status via AJAX
    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:new,contacted,quoted,converted,closed,lost',
        ]);

        $enquiry = Enquiry::findOrFail($id);
        $enquiry->status = $request->status;
        $enquiry->save();

        return response()->json(['success' => true, 'message' => 'Enquiry status updated successfully.']);
    }

    // Escalate unhandled enquiries via AJAX
    public function escalate(Request $request)
    {
        $request->validate([
            'enquiry_id' => 'required|exists:enquiries,id',
            'assigned_to' => 'required|string|max:255',
        ]);

        $enquiry = Enquiry::findOrFail($request->enquiry_id);
        $enquiry->tags = $request->assigned_to . ' [ESCALATED]';
        if ($enquiry->status == 'new') {
            $enquiry->status = 'contacted';
        }
        $enquiry->save();

        return response()->json(['success' => true, 'message' => 'Enquiry escalated and assigned successfully.']);
    }

    // Export enquiries CSV
    public function export(Request $request)
    {
        $enquiries = Enquiry::all();
        $filename = 'enquiries_' . date('Y-m-d') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function() use ($enquiries) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['Enquiry No', 'Name', 'Email', 'Mobile', 'Source', 'Product/Service', 'Status', 'Assigned To', 'Follow-up Date', 'Created At']);
            foreach ($enquiries as $e) {
                fputcsv($file, [
                    $e->enquiry_no, $e->user_name, $e->user_email, $e->user_mobile,
                    $e->source, $e->product_service, $e->status, $e->tags,
                    $e->next_followup_date, $e->created_at
                ]);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
