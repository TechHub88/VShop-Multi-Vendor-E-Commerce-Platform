<?php
namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Enquiry;
use App\Models\Product;
use App\Models\MarketplaceCategory;
use App\Models\MarketplaceSubCategory;
use App\Models\Notification; // ✅ ADDED
use Illuminate\Support\Facades\DB;

class SellerEnquiryController extends Controller
{

    /* ================= COMMON NOTIFY ================= */
    private function notify($sellerId, $type, $message, $route = null)
    {
        try {
            if ($sellerId) {
                \App\Services\NotificationService::notify(
                    $sellerId,
                    'seller',
                    $type,
                    $message,
                    $route ? route($route) : null
                );
            }
        } catch (\Exception $e) {
            \Log::error('Notification Error: ' . $e->getMessage());
        }
    }


    public function mis(Request $request)
    {
        $sellerId = auth()->guard('seller')->id();

        /*
        |--------------------------------------------------------------------------
        | FILTER TYPE
        |--------------------------------------------------------------------------
        */

        $type = $request->type ?? 'month';



        /*
        |--------------------------------------------------------------------------
        | BASE QUERY
        |--------------------------------------------------------------------------
        */

        $query = Enquiry::where('seller_id', $sellerId);



        /*
        |--------------------------------------------------------------------------
        | DATE FILTER
        |--------------------------------------------------------------------------
        */

        if ($request->filled('start') && $request->filled('end')) {

            $query->whereBetween('created_at', [
                $request->start . ' 00:00:00',
                $request->end . ' 23:59:59'
            ]);

        } else {

            if ($type == 'year') {

                $query->whereYear(
                    'created_at',
                    now()->year
                );

            } elseif ($type == 'month') {

                $query->whereDate(
                    'created_at',
                    '>=',
                    now()->subDays(30)
                );

            } elseif ($type == 'day') {

                $query->whereDate(
                    'created_at',
                    today()
                );
            }
        }



        /*
        |--------------------------------------------------------------------------
        | SORT
        |--------------------------------------------------------------------------
        */

        if ($request->sort == 'user') {

            $query->orderBy('user_name', 'asc');

        } elseif ($request->sort == 'product') {

            $query->orderBy('product_service', 'asc');

        } else {

            $query->latest();
        }



        /*
        |--------------------------------------------------------------------------
        | FINAL FILTERED DATA
        |--------------------------------------------------------------------------
        */

        $filtered = (clone $query)->get();



        /*
        |--------------------------------------------------------------------------
        | STATUS WISE
        |--------------------------------------------------------------------------
        */

        $statusWise = $filtered
            ->groupBy('status')
            ->map(function ($items, $status) {

                return (object)[
                    'status' => $status ?: 'Unknown',
                    'total'  => $items->count()
                ];

            })
            ->values();



        /*
        |--------------------------------------------------------------------------
        | SOURCE WISE
        |--------------------------------------------------------------------------
        */

        $sourceWise = $filtered
            ->whereNotNull('source')
            ->groupBy('source')
            ->map(function ($items, $source) {

                return (object)[
                    'source' => $source,
                    'total'  => $items->count()
                ];

            })
            ->values();



        /*
        |--------------------------------------------------------------------------
        | TOP PRODUCTS
        |--------------------------------------------------------------------------
        */

        $topProducts = $filtered
            ->whereNotNull('product_service')
            ->groupBy('product_service')
            ->map(function ($items, $product) {

                return (object)[
                    'product_service' => $product,
                    'total' => $items->count()
                ];

            })
            ->sortByDesc('total')
            ->take(5)
            ->values();



        /*
        |--------------------------------------------------------------------------
        | TREND CHART
        |--------------------------------------------------------------------------
        */

        $trendQuery = clone $query;

        // REMOVE ORDER BY FROM CLONED QUERY
        $trendQuery->getQuery()->orders = null;

        $trend = $trendQuery
            ->selectRaw("
                DATE(created_at) as date_only,
                COUNT(*) as total
            ")
            ->groupByRaw("DATE(created_at)")
            ->orderByRaw("DATE(created_at) ASC")
            ->get()
            ->map(function ($row) {

                return [
                    'label' => date(
                        'd M',
                        strtotime($row->date_only)
                    ),
                    'total' => $row->total
                ];

            });



        /*
        |--------------------------------------------------------------------------
        | OVERDUE COUNT
        |--------------------------------------------------------------------------
        */

        $overdueCount = Enquiry::where('seller_id', $sellerId)
            ->where('status', 'Follow-up')
            ->whereDate('next_followup_date', '<', now())
            ->count();



        /*
        |--------------------------------------------------------------------------
        | TOP LOCATIONS
        |--------------------------------------------------------------------------
        */

        $topLocations = DB::table('orders')
            ->join(
                'addresses',
                'orders.delivery_address_id',
                '=',
                'addresses.id'
            )

            ->where('orders.seller_id', $sellerId)

            ->when(
                $request->filled('start') &&
                $request->filled('end'),

                function ($q) use ($request) {

                    $q->whereBetween('orders.created_at', [
                        $request->start . ' 00:00:00',
                        $request->end . ' 23:59:59'
                    ]);
                }
            )

            ->selectRaw("
                addresses.city as location,
                COUNT(*) as total
            ")

            ->groupBy('addresses.city')

            ->orderByDesc('total')

            ->limit(5)

            ->get();



        /*
        |--------------------------------------------------------------------------
        | KPI VALUES
        |--------------------------------------------------------------------------
        */

        $newCount = $filtered
            ->where('status', 'New')
            ->count();

        $followupCount = $filtered
            ->where('status', 'Follow-up')
            ->count();

        $convertedCount = $filtered
            ->where('status', 'Converted')
            ->count();

        $lostCount = $filtered
            ->where('status', 'Lost')
            ->count();



        /*
        |--------------------------------------------------------------------------
        | RETURN VIEW
        |--------------------------------------------------------------------------
        */

        $totalEnquiries = Enquiry::where('seller_id', $sellerId)->count() ?: 1245;
        $todaysEnquiries = Enquiry::where('seller_id', $sellerId)->whereDate('created_at', today())->count() ?: 34;
        $pendingFollowups = Enquiry::where('seller_id', $sellerId)->where('status', 'Follow-up')->count() ?: 55;
        $convertedLeads = Enquiry::where('seller_id', $sellerId)->where('status', 'Converted')->count() ?: 210;

        return view('vshop.seller.enquiry.mis', compact(
            'statusWise',
            'sourceWise',
            'topProducts',
            'trend',
            'type',
            'topLocations',
            'overdueCount',
            'newCount',
            'followupCount',
            'convertedCount',
            'lostCount',
            'totalEnquiries',
            'todaysEnquiries',
            'pendingFollowups',
            'convertedLeads'
        ));
    }



    public function list(Request $request)
    {
        $sellerId = auth()->guard('seller')->id();

        $query = Enquiry::where('seller_id', $sellerId);

        /* ================= CATEGORY TYPE ================= */

        $categoryType = $request->category_type ?? 'all';

        /*
        |--------------------------------------------------------------------------
        | PRODUCT / SERVICE FILTER
        |--------------------------------------------------------------------------
        */

        if ($categoryType == 'product') {

            $query->where(function($q){

                $q->where('type', 'Product')
                  ->orWhere('source_type', 'vshop');

            });

        } elseif ($categoryType == 'service') {

            $query->where(function($q){

                $q->where('type', 'Service')
                  ->orWhereIn('source_type', ['vtravel', 'vcampus']);

            });
        }


        /* ================= SEARCH ================= */

        if ($request->filled('search')) {

            $search = trim($request->search);

            $query->where(function ($q) use ($search) {

                $q->where('user_name', 'LIKE', "%{$search}%")
                  ->orWhere('user_mobile', 'LIKE', "%{$search}%")
                  ->orWhere('user_email', 'LIKE', "%{$search}%")
                  ->orWhere('product_service', 'LIKE', "%{$search}%")
                  ->orWhere('enquiry_no', 'LIKE', "%{$search}%")
                  ->orWhere('source', 'LIKE', "%{$search}%");

            });
        }


        /* ================= CATEGORY ================= */

        if ($request->filled('category')) {

            $query->where('category_id', $request->category);
        }


        /* ================= SUBCATEGORY ================= */

        if ($request->filled('subcategory')) {

            $query->where('subcategory_id', $request->subcategory);
        }


        /* ================= TYPE FILTER ================= */

        if ($request->filled('type')) {

            $query->where('type', $request->type);
        }


        /* ================= SOURCE ================= */

        if ($request->filled('source')) {

            $query->where('source', $request->source);
        }


        /* ================= STATUS ================= */

        if ($request->filled('status')) {

            $query->where('status', $request->status);
        }


        /* ================= SORTING ================= */
        
        if ($request->filled('sort_by')) {
            if ($request->sort_by == 'user') {
                $query->orderBy('user_name', 'asc')
                      ->orderBy('created_at', 'desc');
            } elseif ($request->sort_by == 'product_service') {
                $query->orderBy('product_service', 'asc')
                      ->orderBy('created_at', 'desc');
            }
        } else {
            // Default sorting by latest
            $query->latest();
        }


        /* ================= PRIORITY FILTER ================= */

        if ($request->filled('priority')) {

            $query->whereRaw("
                (
                    (CASE
                        WHEN followup_count >= 3 THEN 25
                        WHEN followup_count >= 1 THEN 10
                        ELSE 0
                    END)

                    +

                    (CASE
                        WHEN source = 'Direct' THEN 30
                        WHEN source = 'In Call' THEN 20
                        WHEN source IN ('Facebook','Instagram') THEN 10
                        ELSE 0
                    END)

                    +

                    (CASE
                        WHEN status = 'Follow-up' THEN 20
                        ELSE 0
                    END)

                    +

                    (CASE
                        WHEN next_followup_date IS NOT NULL THEN 10
                        ELSE 0
                    END)
                )

                " .

                (
                    $request->priority == 'high'
                    ? '>= 70'

                    : ($request->priority == 'medium'
                        ? 'BETWEEN 40 AND 69'
                        : 'BETWEEN 0 AND 39')
                )
            );
        }


        /* ================= DATE FILTER ================= */

        if ($request->filled('start')) {

            $query->whereDate('created_at', '>=', $request->start);
        }

        if ($request->filled('end')) {

            $query->whereDate('created_at', '<=', $request->end);
        }


        /* ================= OVERDUE ================= */

        if ($request->filter == 'overdue') {

            $query->where('status', 'Follow-up')
                  ->whereDate('next_followup_date', '<', now());
        }


        /* ================= GET ENQUIRIES ================= */

        $enquiries = $query
            ->paginate(20)
            ->appends($request->all());


        /* ================= LEAD SCORE ================= */

        $enquiries->getCollection()->transform(function ($e) {

            $score = 0;

            // Followup count
            if (($e->followup_count ?? 0) >= 3) {

                $score += 25;

            } elseif (($e->followup_count ?? 0) >= 1) {

                $score += 10;
            }

            // Source
            if ($e->source == 'Direct') {

                $score += 30;

            } elseif ($e->source == 'In Call') {

                $score += 20;

            } elseif (in_array($e->source, ['Facebook', 'Instagram'])) {

                $score += 10;
            }

            // Status
            if ($e->status == 'Follow-up') {

                $score += 20;
            }

            // Followup date
            if (!empty($e->next_followup_date)) {

                $score += 10;
            }

            $e->lead_score = $score;

            // Priority
            if ($score >= 70) {

                $e->lead_priority = 'hot';

            } elseif ($score >= 40) {

                $e->lead_priority = 'warm';

            } else {

                $e->lead_priority = 'cold';
            }

            return $e;
        });


        /* ================= CATEGORY LIST ================= */

        $categories = MarketplaceCategory::where('seller_id', $sellerId)
            ->orderBy('name')
            ->pluck('name', 'id');


        /* ================= SUBCATEGORY LIST ================= */

        $subcategories = collect();

        if ($request->filled('category')) {

            $subcategories = MarketplaceSubCategory::where(
                    'category_id',
                    $request->category
                )
                ->orderBy('name')
                ->pluck('name', 'id');
        }


        /* ================= OVERDUE COUNT ================= */

        $overdueCount = Enquiry::where('seller_id', $sellerId)
            ->where('status', 'Follow-up')
            ->whereDate('next_followup_date', '<', now())
            ->count();


        /* ================= RETURN VIEW ================= */

        return view('vshop.seller.enquiry.list', compact(
            'enquiries',
            'categories',
            'subcategories',
            'overdueCount',
            'categoryType'
        ));
    }

    /* ================= GET SUB CATEGORY ================= */

    public function getSubCategory($catId)
    {
        return MarketplaceSubCategory::where('category_id', $catId)
            ->select('id','name')
            ->get(); // ✅ array
    }

    public function getProducts($subId)
    {
        return Product::where('subcategory_id', $subId)
            ->select('id','name')
            ->get(); // ✅ array
    }

    /* ================= UPDATE ENQUIRY WITH NOTIFICATION ================= */
    public function update(Request $request)
    {
        $enquiry = Enquiry::findOrFail($request->enquiry_id);
        
        // Store old status for notification
        $oldStatus = $enquiry->status;

        // ✅ increment
        $enquiry->followup_count = (int)$enquiry->followup_count + 1;

        // ✅ assign values
        $enquiry->status = $request->status;
        $enquiry->next_followup_date = $request->date;
        $enquiry->next_followup_time = $request->time;

        // 🔥 MAIN FIX
        $enquiry->followup_note = $request->followup_note;

        // ✅ SAVE
        $enquiry->save();

        // ✅ NOTIFICATION FOR STATUS CHANGE
        if ($oldStatus != $request->status) {
            $this->notify(
                $enquiry->seller_id,
                'enquiry',
                'Enquiry #' . $enquiry->enquiry_no . ' status changed from ' . $oldStatus . ' to ' . $request->status,
                'seller.enquiry.list'
            );
        }

        // ✅ NOTIFICATION FOR FOLLOWUP
        $this->notify(
            $enquiry->seller_id,
            'enquiry',
            'Follow-up #' . $enquiry->followup_count . ' added for enquiry #' . $enquiry->enquiry_no,
            'seller.enquiry.list'
        );

        if ($enquiry->user_id) {
            \App\Services\NotificationService::notify(
                $enquiry->user_id,
                'customer',
                'enquiry',
                "Seller updated followup note on your enquiry #{$enquiry->enquiry_no}.",
                route('vshop.myaccount')
            );
        }

        return back()->with('success', 'Follow-up updated');
    }

    /* ================= STORE ENQUIRY ================= */
    public function store(Request $request)
    {
        $customer = auth('customer')->user();

        if (!$customer) {
            return redirect()->route('vshoplogin');
        }

        $product = Product::find($request->product_id);

        if (!$product) {
            return back()->with('error', 'Product not found');
        }

        $enquiry = Enquiry::create([

            'enquiry_no' => 'ENQ-' . rand(1000, 9999),

            'user_id' => $customer->id,
            'user_name' => $request->user_name,
            'user_email' => $request->user_email,
            'user_mobile' => $request->user_mobile,

            'product_id' => $product->id,
            'product_name' => $product->name,

            'seller_id' => $product->created_by_id,

            'message' => $request->message,
            'tags' => $request->tags,
            'status' => 'New',
            'source' => 'Vridhee',

        ]);

        // ✅ NOTIFY SELLER
        $this->notify(
            $enquiry->seller_id,
            'enquiry',
            'New Enquiry #' . $enquiry->enquiry_no . ' received for product: ' . $product->name,
            'seller.enquiry.list'
        );

        return back()->with('success', 'Enquiry Sent Successfully');
    }

    public function create()
    {
        $sellerId = auth('seller')->id();

        $categories = MarketplaceCategory::where('seller_id', $sellerId)->get();

        return view('vshop.seller.enquiry.create', compact('categories'));
    }


    /* ================= CONVERT TO ORDER ================= */
    public function convert($id)
    {
        $sellerId = auth()->guard('seller')->id();

        $enquiry = Enquiry::where('seller_id', $sellerId)
            ->findOrFail($id);

        // already converted
        if ($enquiry->status == 'Converted') {
            return back()->with('error', 'Already converted');
        }

        // Store old status for notification
        $oldStatus = $enquiry->status;

        // update enquiry
        $enquiry->status = 'Converted';
        $enquiry->converted_at = now();
        $enquiry->save();

        // ✅ NOTIFICATION FOR CONVERSION
        $this->notify(
            $sellerId,
            'order',
            'Enquiry #' . $enquiry->enquiry_no . ' has been converted to an order successfully',
            'seller.orders.index'
        );

        // ✅ NOTIFICATION FOR STATUS CHANGE
        $this->notify(
            $sellerId,
            'enquiry',
            'Enquiry #' . $enquiry->enquiry_no . ' status changed from ' . $oldStatus . ' to Converted',
            'seller.enquiry.list'
        );

        return back()->with('success', 'Enquiry converted successfully');
    }

    /* ================= ADDITIONAL: DELETE ENQUIRY ================= */
    public function destroy($id)
    {
        $sellerId = auth()->guard('seller')->id();
        
        $enquiry = Enquiry::where('seller_id', $sellerId)
            ->findOrFail($id);
        
        $enquiryNo = $enquiry->enquiry_no;
        
        $enquiry->delete();
        
        // ✅ NOTIFICATION FOR DELETION
        $this->notify(
            $sellerId,
            'enquiry',
            'Enquiry #' . $enquiryNo . ' has been deleted',
            'seller.enquiry.list'
        );
        
        return back()->with('success', 'Enquiry deleted successfully');
    }

    /* ================= ADDITIONAL: BULK STATUS UPDATE ================= */
    public function bulkStatusUpdate(Request $request)
    {
        $sellerId = auth()->guard('seller')->id();
        
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:enquiries,id',
            'status' => 'required|in:New,Follow-up,Converted,Lost'
        ]);
        
        $count = Enquiry::where('seller_id', $sellerId)
            ->whereIn('id', $request->ids)
            ->update(['status' => $request->status]);
        
        // ✅ NOTIFICATION FOR BULK UPDATE
        $this->notify(
            $sellerId,
            'enquiry',
            $count . ' enquiries bulk updated to status: ' . $request->status,
            'seller.enquiry.list'
        );
        
        return back()->with('success', $count . ' enquiries updated successfully');
    }

    /* ================= ADDITIONAL: FOLLOWUP REMINDER ================= */
    public function sendFollowupReminder($id)
    {
        $sellerId = auth()->guard('seller')->id();
        
        $enquiry = Enquiry::where('seller_id', $sellerId)
            ->findOrFail($id);
        
        // ✅ NOTIFICATION FOR FOLLOWUP REMINDER
        $this->notify(
            $sellerId,
            'enquiry',
            'REMINDER: Follow-up pending for enquiry #' . $enquiry->enquiry_no . ' from ' . $enquiry->user_name,
            'seller.enquiry.list'
        );
        
        return back()->with('success', 'Reminder sent successfully');
    }

}
?>