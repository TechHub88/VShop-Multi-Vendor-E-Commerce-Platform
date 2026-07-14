<?php
namespace App\Http\Controllers\Shop;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use App\Models\SellerFinance;
use App\Models\SellerDispute;
use App\Models\SellerCommission;
use App\Models\SellerInvoice;
use Illuminate\Http\Request; 
use App\Models\SupportChat;
use App\Models\Grievance;
use App\Models\GrievanceMessage;
use App\Models\Transaction;
use App\Models\DisputeLog;
use App\Models\SupportLog;
use App\Models\Commission;
use App\Models\Order;
use App\Models\SellerOperationArea;


use App\Models\SellerBankAccount;

use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Carbon\Carbon;

use Barryvdh\DomPDF\Facade\Pdf;
class SellerFinanceController extends Controller
{

/* ================= DASHBOARD ================= */

public function dashboard()
{
    $seller = auth()->guard('seller')->user();

    /* ================= STATE ================= */
    $currency = request('currency', session('currency', 'INR'));
    $region   = request('region', session('region', 'IN'));
    $range    = request('range', 'monthly');
    $from     = request('from');
    $to       = request('to');

    session(['currency' => $currency]);
    session(['region' => $region]);

    /* ================= BASE QUERY ================= */
    $financeQuery = SellerFinance::where('seller_id', $seller->id);

    if ($range == 'daily') {
        $financeQuery->whereDate('created_at', today());
    }

    if ($range == 'weekly') {
        $financeQuery->whereBetween('created_at', [
            now()->startOfWeek(),
            now()->endOfWeek()
        ]);
    }

    if ($range == 'monthly') {
        $financeQuery->whereMonth('created_at', now()->month);
    }

    if ($range == 'custom' && $from && $to) {
        $financeQuery->whereBetween('created_at', [$from, $to]);
    }

    /* ================= FINANCIALS ================= */
    $totalCredit = (clone $financeQuery)->sum('credit');
    $totalDebit  = (clone $financeQuery)->sum('debit');

    $balance = $totalCredit - $totalDebit;

    $refund = (clone $financeQuery)
        ->where('transaction_type','refund')
        ->sum('debit');

    $wallet_hold = (clone $financeQuery)
        ->where('status','hold')
        ->sum('debit');

    $fees = SellerCommission::where('seller_id',$seller->id)
        ->selectRaw("SUM(commission_rate + logistic_fee + payment_gateway_fee) as total")
        ->value('total') ?? 0;

    /* ================= TREND ================= */
    $trend = (clone $financeQuery)
        ->selectRaw("DATE(created_at) as date, SUM(credit) as total")
        ->groupBy('date')
        ->orderBy('date')
        ->get();

    /* ================= PIE ================= */
    $pie = (clone $financeQuery)
        ->selectRaw("transaction_type as label, SUM(credit) as total")
        ->groupBy('transaction_type')
        ->get();

    /* ================= OTHERS ================= */
    $daily = SellerFinance::where('seller_id',$seller->id)
        ->whereDate('created_at',today())
        ->sum('credit');

    $weekly = SellerFinance::where('seller_id',$seller->id)
        ->whereBetween('created_at',[now()->startOfWeek(), now()->endOfWeek()])
        ->sum('credit');

    $monthly = SellerFinance::where('seller_id',$seller->id)
        ->whereMonth('created_at',now()->month)
        ->sum('credit');

    $disputes = SellerFinance::where('seller_id',$seller->id)
        ->whereIn('status',['hold','review'])
        ->latest()
        ->take(5)
        ->get();

    /* ================= COMMISSIONS TABLE DATA ================= */
    $commissionsData = SellerFinance::with(['order.items.product'])
        ->where('seller_id', $seller->id)
        ->where('transaction_type', 'earning')
        ->orderBy('created_at', 'desc')
        ->take(50)
        ->get()
        ->map(function($txn) {
            $revenue = $txn->credit;
            
            $productName = 'Txn ' . ($txn->order_id ?? 'N/A');
            $platform = 'Vshop';
            
            if ($txn->order && $txn->order->items->first() && $txn->order->items->first()->product) {
                $productName = $txn->order->items->first()->product->name;
                // If it's a travel product or digital service, set platform to Vtravel, else Vshop
                // Defaults to Vshop for now as an example
                $platform = 'Vshop'; 
            }

            return [
                'date' => $txn->created_at->format('d M Y'),
                'platform' => $platform,
                'product' => \Illuminate\Support\Str::limit($productName, 30),
                'order_id' => $txn->order_id ? '#' . $txn->order_id : ($txn->reference_no ? '#' . $txn->reference_no : 'N/A'),
                'revenue' => (float)$revenue,
                'referral' => 0,
                'lead_charges' => 0,
                'boost_spend' => 0.0,
                'refund' => 0.0,
                'net' => (float)($revenue - (float)$txn->commission_amount)
            ];
        });

    $taxes = \App\Models\Tax::all();

    return view('vshop.seller.finance.dashboard', compact(
        'balance','daily','weekly','monthly',
        'refund','wallet_hold','fees',
        'trend','pie','disputes',
        'range','from','to',
        'currency','region', 'commissionsData', 'taxes'
    ));
}
/* ================= LEDGER ================= */

public function transactions(Request $request)
{
    $seller = auth()->guard('seller')->user();

    $query = SellerFinance::where('seller_id', $seller->id);

    /* ================= TYPE TABS ================= */
    if ($request->filled('tab') && $request->tab != 'all') {
        $query->where('transaction_type', $request->tab);
    }

    /* ================= DATE ================= */
    if ($request->from) {
        $query->whereDate('created_at', '>=', $request->from);
    }

    if ($request->to) {
        $query->whereDate('created_at', '<=', $request->to);
    }

    /* ================= TYPE FILTER ================= */
    if ($request->type) {
        $query->where('transaction_type', $request->type);
    }

    /* ================= ORDER SEARCH ================= */
    if ($request->order_id) {
        $query->where(function ($q) use ($request) {
            $q->where('reference_no', 'like', "%{$request->order_id}%")
              ->orWhere('order_id', 'like', "%{$request->order_id}%");
        });
    }
    
    
    

    /* ================= AMOUNT ================= */
    if ($request->amount) {
        $query->where(function ($q) use ($request) {
            $q->where('credit', $request->amount)
              ->orWhere('debit', $request->amount);
        });
    }

    /* ================= DATA ================= */
    $transactions = $query->with(['order.items.product'])->latest()->paginate(15)->appends($request->all());

    /* ================= SUMMARY ================= */
    $summary = [
        'credit' => $query->sum('credit'),
        'debit'  => $query->sum('debit'),
        'count'  => $query->count(),
    ];

    /* ================= MONTHLY ================= */
    $monthly = SellerFinance::selectRaw("MONTH(created_at) as m, SUM(credit) as credit, SUM(debit) as debit")
        ->where('seller_id', $seller->id)
        ->groupBy('m')
        ->get();

    return view('vshop.seller.finance.transactions', compact(
        'transactions',
        'summary',
        'monthly'
    ));
}

/* ================= WITHDRAW ================= */

public function payouts()
{
    $seller = auth()->guard('seller')->user();

    if (!$seller) {
        abort(403, 'Unauthorized');
    }

    /* =========================================================
       COUNTRY FILTER
    ========================================================= */

    $selectedCountry = request('country');

    $sellerCountries = SellerOperationArea::where(
            'seller_id',
            $seller->id
        )
        ->pluck('country_id')
        ->toArray();

    // DEFAULT FIRST COUNTRY
    if (!$selectedCountry && count($sellerCountries) > 0) {

        $selectedCountry = $sellerCountries[0];

    }

    // SECURITY CHECK
    if (
        $selectedCountry &&
        !in_array($selectedCountry, $sellerCountries)
    ) {

        abort(403, 'Invalid country access');

    }


    /* =========================================================
       PAYOUT MODE
    ========================================================= */

    $payoutMode = request(
        'schedule',
        $seller->payout_mode ?? 'auto'
    );


    /* =========================================================
       CURRENCY
    ========================================================= */

    $selectedCurrency = request('currency', 'INR');


    /* =========================================================
       PAYOUT CYCLE
    ========================================================= */

    $cycleStartDate = Carbon::now()->startOfWeek();

    $cycleEndDate = Carbon::now()->endOfWeek();

    $cycleStart = $cycleStartDate->format('d M Y');

    $cycleEnd = $cycleEndDate->format('d M Y');


    /* =========================================================
       NEXT PAYOUT DATE
    ========================================================= */

    if($payoutMode == 'auto'){

        $nextPayoutDate = $cycleEndDate
            ->copy()
            ->addDay()
            ->format('d M Y');

    }else{

        $nextPayoutDate = 'Withdraw Anytime';

    }


    /* =========================================================
       CURRENT CYCLE EARNING
    ========================================================= */

    $currentCycleEarning = SellerFinance::where('seller_id', $seller->id)
        ->where('transaction_type', 'earning')
        ->whereBetween('created_at', [
            $cycleStartDate,
            $cycleEndDate
        ])
        ->sum('credit');


    /* =========================================================
       UPCOMING PAYOUTS
    ========================================================= */

    $pendingPayouts = SellerFinance::where('seller_id', $seller->id)
        ->where('transaction_type', 'payout_request')
        ->latest()
        ->get();


    /* =========================================================
       COMPLETED PAYOUTS
    ========================================================= */

    $completedPayouts = SellerFinance::where('seller_id', $seller->id)
        ->where('transaction_type', 'payout_success')
        ->latest()
        ->get();


    /* =========================================================
       FAILED PAYOUTS
    ========================================================= */

    $failedPayouts = SellerFinance::where('seller_id', $seller->id)
        ->where('transaction_type', 'payout_failed')
        ->latest()
        ->get();


    /* =========================================================
       BALANCE
    ========================================================= */

    $credit = SellerFinance::where('seller_id', $seller->id)
        ->sum('credit');

    $debit = SellerFinance::where('seller_id', $seller->id)
        ->sum('debit');

    $availableBalance = $credit - $debit;


    /* =========================================================
       PAYOUT HISTORY
    ========================================================= */

    $payouts = SellerFinance::where('seller_id', $seller->id)
        ->whereIn('transaction_type', [
            'payout_request',
            'payout_success',
            'payout_failed'
        ])
        ->latest()
        ->take(10)
        ->get();


    /* =========================================================
       SETTLEMENT TRACKER
    ========================================================= */

    $lastPayout = SellerFinance::where('seller_id', $seller->id)
        ->where('transaction_type', 'payout_success')
        ->latest()
        ->first();

    $lastPayoutDate = $lastPayout
        ? $lastPayout->created_at
        : now()->subDays(10);

    $cycleDays = $seller->settlement_cycle ?? 7;

    $daysPassed = now()->diffInDays($lastPayoutDate);

    $delayDays = max(0, $daysPassed - $cycleDays);

    $progress = min(
        100,
        ($daysPassed / $cycleDays) * 100
    );


    /* =========================================================
       SETTLEMENT %
    ========================================================= */

    $totalPayouts = $completedPayouts->count();

    $matchedPayouts = $completedPayouts->count();

    $settlementPercent = $totalPayouts > 0
        ? round(($matchedPayouts / $totalPayouts) * 100)
        : 0;


    /* =========================================================
       DISPUTES
    ========================================================= */

    $disputes = SellerFinance::where('seller_id', $seller->id)
        ->whereIn('transaction_type', [
            'dispute',
            'hold'
        ])
        ->latest()
        ->take(5)
        ->get();


    /* =========================================================
       WALLET HOLD
    ========================================================= */

    $wallet_hold = SellerFinance::where('seller_id', $seller->id)
        ->where('transaction_type', 'hold')
        ->sum('debit');


    /* =========================================================
       BANK ACCOUNTS
    ========================================================= */

    $bankAccounts = SellerBankAccount::where(
        'seller_id',
        $seller->id
    )->latest()->get();


    /* =========================================================
       RETURN VIEW
    ========================================================= */

    return view(
        'vshop.seller.finance.payouts',
        compact(

            'seller',

            'payoutMode',

            'selectedCountry',

            'selectedCurrency',

            'sellerCountries',

            'cycleStart',
            'cycleEnd',

            'nextPayoutDate',

            'currentCycleEarning',

            'pendingPayouts',
            'completedPayouts',
            'failedPayouts',

            'availableBalance',

            'payouts',

            'settlementPercent',

            'disputes',

            'wallet_hold',

            'delayDays',
            'cycleDays',
            'progress',

            'bankAccounts'
        )
    );
}
public function commission()
{
    $seller = auth()->guard('seller')->user();

    // 1. Fetch seller rules strictly from commission_management table seller_id wise
    $commissionRules = DB::table('commission_management')
        ->where('seller_id', $seller->id)
        ->get();

    $hasCommissionData = $commissionRules->isNotEmpty();
    $primaryRule = $commissionRules->first();

    $sellerCommissionPct   = $primaryRule && isset($primaryRule->seller_commission) ? (float) $primaryRule->seller_commission : null;
    $platformCommissionPct = $primaryRule && isset($primaryRule->platform_commission) ? (float) $primaryRule->platform_commission : null;
    $referrerCommissionPct = $primaryRule && isset($primaryRule->referrer_commission) ? (float) $primaryRule->referrer_commission : null;

    // 2. Dynamic KPI Metrics
    $totalRevenue = Order::where('seller_id', $seller->id)
        ->whereIn('status', ['completed', 'paid', 'delivered'])
        ->sum('total_amount');
    
    if ($totalRevenue <= 0 && $primaryRule && isset($primaryRule->total_sales) && $primaryRule->total_sales > 0) {
        $totalRevenue = (float) $primaryRule->total_sales;
    }

    $totalOrders = Order::where('seller_id', $seller->id)->count();

    $pendingSettlements = SellerFinance::where('seller_id', $seller->id)
        ->whereIn('status', ['pending', 'hold'])
        ->sum('credit');

    $disputesRaised = \App\Models\Grievance::where('seller_id', $seller->id)->count();
    if ($disputesRaised <= 0) {
        $disputesRaised = SellerFinance::where('seller_id', $seller->id)->where('status', 'disputed')->count();
    }

    // 3. Category-wise Commission Chart & Top Performing Categories (marketplace_categories table)
    $sellerCategories = DB::table('marketplace_categories')
        ->where('seller_id', $seller->id)
        ->get();

    if ($sellerCategories->isEmpty()) {
        $sellerCategoryIds = DB::table('products')
            ->where('created_by_id', $seller->id)
            ->pluck('category_id')
            ->filter()
            ->unique();

        if ($sellerCategoryIds->count() > 0) {
            $sellerCategories = DB::table('marketplace_categories')
                ->whereIn('id', $sellerCategoryIds)
                ->get();
        }
    }

    $categoryChartLabels = [];
    $categoryChartData = [];
    $topPerformingCategories = collect();
    $totalCatCount = $sellerCategories->count();
    $badgeColors = ['bg-primary', 'bg-success', 'bg-warning', 'bg-danger', 'bg-info'];

    foreach ($sellerCategories as $idx => $cat) {
        $categoryChartLabels[] = $cat->name;
        $catRule = $commissionRules->where('category_id', $cat->id)->first();
        $rate = $catRule && isset($catRule->seller_commission) ? (float)$catRule->seller_commission : ($sellerCommissionPct ?? 0);
        $categoryChartData[] = $rate;

        $pctShare = $totalCatCount > 0 ? round(100 / $totalCatCount) : 0;
        $topPerformingCategories->push((object)[
            'name' => $cat->name,
            'percentage' => $pctShare,
            'color_class' => $badgeColors[$idx % count($badgeColors)]
        ]);
    }

    // 4. Product vs Service & Dropship vs Inventory Chart Data
    $productVsService = [
        ['value' => 65, 'name' => 'Product'],
        ['value' => 35, 'name' => 'Service']
    ];
    $dropshipVsInventory = [
        ['value' => 55, 'name' => 'Dropship'],
        ['value' => 45, 'name' => 'Inventory']
    ];

    // 5. Promotional Waiver & Fee Breakdown
    $promoWaiverRate = $referrerCommissionPct !== null ? round($referrerCommissionPct, 1) : 0;
    $logisticsFee = round($totalRevenue * 0.02, 2);
    $gatewayFee   = round($totalRevenue * 0.015, 2);
    $platformFee  = $platformCommissionPct !== null ? round($totalRevenue * ($platformCommissionPct / 100), 2) : 0;
    $taxFee       = round(($logisticsFee + $gatewayFee + $platformFee) * 0.18, 2);

    // 6. Monthly Revenue Trend
    $trendLabels = [];
    $trendData = [];
    for ($i = 5; $i >= 0; $i--) {
        $m = Carbon::now()->subMonths($i);
        $trendLabels[] = $m->format('M');
        $mRev = Order::where('seller_id', $seller->id)
            ->whereYear('created_at', $m->year)
            ->whereMonth('created_at', $m->month)
            ->sum('total_amount');
        $trendData[] = (float)$mRev;
    }

    // 7. Fee Reconciliation Table Orders
    $reconciliationOrders = Order::where('seller_id', $seller->id)
        ->latest()
        ->take(10)
        ->get();

    return view(
        'vshop.seller.finance.commission',
        compact(
            'seller',
            'commissionRules',
            'hasCommissionData',
            'totalRevenue',
            'totalOrders',
            'pendingSettlements',
            'disputesRaised',
            'categoryChartLabels',
            'categoryChartData',
            'topPerformingCategories',
            'productVsService',
            'dropshipVsInventory',
            'promoWaiverRate',
            'logisticsFee',
            'gatewayFee',
            'platformFee',
            'taxFee',
            'trendLabels',
            'trendData',
            'reconciliationOrders',
            'sellerCommissionPct',
            'platformCommissionPct'
        )
    );
}





public function invoices($id)
{
    $seller = auth()->guard('seller')->user();

    $invoice = SellerInvoice::where('seller_id', $seller->id)
        ->where('id', $id)
        ->firstOrFail();

    $pdf = PDF::loadView('vshop.seller.finance.invoice_pdf', [
        'invoice' => $invoice,
        'seller'  => $seller
    ]);

    return $pdf->download('invoice_'.$invoice->id.'.pdf');
}


public function dispute(Request $request)
{
    $txn = SellerFinance::where('id',$request->transaction_id)
        ->where('seller_id',auth()->guard('seller')->id())
        ->first();

    if(!$txn){
        return back()->with('error','Transaction not found');
    }

    $txn->status = 'disputed';
    $txn->note = $request->message; // ðŸ‘ˆ SAVE NOTE
    $txn->save();

    return back()->with('success','Dispute Raised');
}

public function undispute(Request $request)
{
    $transaction = SellerFinance::findOrFail($request->id);

    // 🔥 reset dispute
    $transaction->dispute_status = null;

    // optional: status normal karo
    $transaction->status = 'success';

    $transaction->save();

    return back()->with('success','Dispute removed successfully');
}
public function raiseDispute(Request $request)
{
    $seller = auth()->guard('seller')->user();

    \App\Models\SellerDispute::create([
        'seller_id' => $seller->id,
        'order_id' => $request->order_id,
        'message' => $request->message,
        'status' => 'open'
    ]);

    return back()->with('success','Dispute Raised');
}
public function monthlyReport()
{
    $seller = auth()->guard('seller')->user();

    $data = SellerFinance::where('seller_id',$seller->id)
        ->selectRaw('MONTH(created_at) as month, SUM(credit) as total')
        ->groupBy('month')
        ->get();

    $pdf = Pdf::loadView('vshop.seller.finance.monthly_pdf', compact('data'));

    return $pdf->download('Monthly_Report.pdf');
}



/* CHAT PAGE */
public function supportChat(Request $request)
{
    $seller = auth()->guard('seller')->user();

    $orderId = $request->order_id;

    $messages = SupportChat::where('seller_id',$seller->id)
        ->where('order_id',$orderId)
        ->get();

    return view('vshop.seller.finance.support_chat', compact('messages','orderId'));
}

/* SEND MESSAGE */
public function sendMessage(Request $request)
{
    $seller = auth()->guard('seller')->user();

    SupportChat::create([
        'seller_id' => $seller->id,
        'order_id' => $request->order_id,
        'message' => $request->message,
        'sender' => 'seller'
    ]);

    return back();
}


public function generateInvoice()
{
    $seller = auth()->guard('seller')->user();

    /* GET SELLER TRANSACTIONS */
    $transactions = SellerFinance::where('seller_id',$seller->id)
        ->where('transaction_type','order')
        ->get();

    if($transactions->isEmpty()){
        return back()->with('error','No transactions found');
    }

    /* TOTAL */
    $total = $transactions->sum('credit');

    /* FEES */
    $commission = $total * 0.10; // 10%
    $logistics  = $total * 0.02; // 2%
    $gateway    = $total * 0.02; // 2%

    /* TAX */
    $taxable = $commission + $logistics + $gateway;
    $gst = $taxable * 0.18;

    /* NET */
    $net = $total - ($commission + $logistics + $gateway + $gst);

    /* SAVE INVOICE */
    SellerInvoice::create([
        'seller_id'       => $seller->id,
        'invoice_no'      => 'INV-'.time(),
        'total_amount'    => $total,
        'commission'      => $commission,
        'logistics_fee'   => $logistics,
        'gateway_fee'     => $gateway,
        'taxable_amount'  => $taxable,
        'gst_amount'      => $gst,
        'net_amount'      => $net,
        'invoice_date'    => now(),
    ]);

    return redirect()->route('finance.invoices')
        ->with('success','Invoice Generated Successfully');
}

public function analytics()
{
    $seller = auth()->guard('seller')->user();

    /* ================= SALES TREND ================= */
    $salesTrend = SellerFinance::selectRaw("
        DATE(created_at) as date,
        SUM(credit) as total
    ")
    ->where('seller_id',$seller->id)
    ->groupBy('date')
    ->orderBy('date')
    ->get();

    /* ================= CONVERSION FUNNEL ================= */
    // Assuming orders table exists
    $totalVisits = \DB::table('orders')
        ->where('seller_id',$seller->id)
        ->count();

    $totalOrders = \DB::table('orders')
        ->where('seller_id',$seller->id)
        ->where('status','completed')
        ->count();

    $conversionRate = $totalVisits > 0 
        ? ($totalOrders / $totalVisits) * 100 
        : 0;

    /* ================= CUSTOMER SEGMENTATION ================= */
    $customers = \DB::table('orders')
        ->selectRaw("
            customer_id,
            COUNT(*) as orders,
            SUM(total_amount) as spending
        ")
        ->where('seller_id',$seller->id)
        ->groupBy('customer_id')
        ->get();

    /* ================= CAMPAIGN ROI ================= */
    $rawCampaigns = \DB::table('promotion_campaigns')
        ->where(function($q) use ($seller) {
            $q->whereNull('seller_ids')
              ->orWhereJsonContains('seller_ids', (int)$seller->id)
              ->orWhereJsonContains('seller_ids', (string)$seller->id);
        })
        ->get();

    $campaigns = collect();
    foreach ($rawCampaigns as $c) {
        $rev = (float)($c->total_revenue ?? 0);
        $bud = (float)($c->total_discount_given ?? 1000);
        $campaigns->push((object)[
            'name' => $c->name,
            'budget' => $bud,
            'revenue' => $rev,
            'profit' => $rev - $bud
        ]);
    }

    return view('vshop.seller.analytics.dashboard', compact(
        'salesTrend',
        'totalVisits',
        'totalOrders',
        'conversionRate',
        'customers',
        'campaigns'
    ));
}



public function salesAnalytics()
{
    $seller = auth()->guard('seller')->user();

    /* ================= TREND ================= */
    $sales = SellerFinance::selectRaw("
            DATE(created_at) as date,
            COUNT(*) as orders,
            SUM(credit) as revenue
        ")
        ->where('seller_id', $seller->id)
        ->groupBy('date')
        ->orderBy('date','ASC')
        ->get();

    /* ================= CATEGORY ================= */
    $categoryData = DB::table('order_items as oi')
        ->join('products as p','p.id','=','oi.product_id')
        ->join('marketplace_categories as c','c.id','=','p.category_id')
        ->join('orders as o','o.id','=','oi.order_id')
        ->where('o.seller_id',$seller->id)
        ->select('c.name as label', DB::raw('SUM(oi.total) as total'))
        ->groupBy('c.name')
        ->get();

    /* ================= PRODUCT ================= */
    $productData = DB::table('order_items as oi')
        ->join('products as p','p.id','=','oi.product_id')
        ->join('orders as o','o.id','=','oi.order_id')
        ->where('o.seller_id',$seller->id)
        ->select('p.name as label', DB::raw('SUM(oi.total) as total'))
        ->groupBy('p.name')
        ->orderByDesc('total')
        ->limit(10)
        ->get();

    /* ================= PROFIT TREND ================= */
    $profitTrend = DB::table('seller_finances')
        ->selectRaw("DATE(created_at) as date, SUM(credit - debit) as profit")
        ->where('seller_id',$seller->id)
        ->groupBy('date')
        ->orderBy('date','ASC')
        ->get();

    /* ================= DEDUCTION ================= */
    $deduction = DB::table('seller_finances')
        ->selectRaw("
            SUM(debit) as total_deduction,
            SUM(credit) as total_credit
        ")
        ->where('seller_id',$seller->id)
        ->first();

    return view('vshop.seller.analytics.sales', compact(
        'sales',
        'categoryData',
        'productData',
        'profitTrend',
        'deduction'
    ));
}
public function funnelAnalytics()
{
    $seller = auth()->guard('seller')->user();

    // 1. Overview counts
    $visits = \DB::table('customer_recently_viewed')
        ->join('products', 'customer_recently_viewed.product_id', '=', 'products.id')
        ->where('products.seller_id', $seller->id)
        ->distinct('customer_recently_viewed.customer_id')
        ->count('customer_recently_viewed.customer_id');

    $cartAbandon = \DB::table('orders')->where('seller_id',$seller->id)->where('status','cart')->count();
    $orders = \DB::table('orders')->where('seller_id',$seller->id)->where('status','!=','cart')->count();
    
    // Ensure logical funnel progression
    if ($visits < $cartAbandon) { $visits = $cartAbandon + 15; }
    if ($visits < $orders) { $visits = $orders + 15; }

    // 2. Trend Data (Last 6 months)
    $trendMonths = [];
    $trendVisitors = [];
    $trendOrders = [];
    for ($i = 5; $i >= 0; $i--) {
        $monthStart = \Carbon\Carbon::now()->startOfMonth()->subMonths($i);
        $monthEnd = $monthStart->copy()->endOfMonth();
        $trendMonths[] = $monthStart->format('M');
        
        $v = \DB::table('customer_recently_viewed')
            ->join('products', 'customer_recently_viewed.product_id', '=', 'products.id')
            ->where('products.seller_id', $seller->id)
            ->whereBetween('customer_recently_viewed.created_at', [$monthStart, $monthEnd])
            ->distinct('customer_recently_viewed.customer_id')
            ->count('customer_recently_viewed.customer_id');
            
        $o = \DB::table('orders')
            ->where('seller_id', $seller->id)
            ->where('status', '!=', 'cart')
            ->whereBetween('created_at', [$monthStart, $monthEnd])
            ->count();
            
        $trendVisitors[] = $v;
        $trendOrders[] = $o;
    }

    // 3. Cart Recovery (Bar chart)
    $recoveryLabels = ['Potential','Abandoned','Recovered'];
    $recoveryData = [
        $cartAbandon + $orders,
        $cartAbandon,
        \DB::table('orders')->where('seller_id', $seller->id)->where('status', 'completed')->count() ?? 0
    ];

    // 4. Checkout Friction (Bar chart)
    $frictionLabels = ['Added to Cart', 'Address', 'Payment', 'Review'];
    $addedToCart = $cartAbandon + $orders;
    $addressStep = round($addedToCart * 0.8);
    $paymentStep = round($addedToCart * 0.5);
    $reviewStep = $orders;
    $frictionData = [$addedToCart, $addressStep, $paymentStep, $reviewStep];

    return view('vshop.seller.analytics.funnel', compact(
        'visits','orders','cartAbandon', 
        'trendMonths', 'trendVisitors', 'trendOrders',
        'recoveryLabels', 'recoveryData',
        'frictionLabels', 'frictionData'
    ));
}

public function customerAnalytics()
{
    $seller = auth()->guard('seller')->user();

    $customers = \DB::table('orders')
        ->leftJoin('customers', 'orders.customer_id', '=', 'customers.id')
        ->selectRaw("
            orders.customer_id,
            customers.name as customer_name,
            COUNT(orders.id) as orders,
            SUM(orders.total_amount) as total_spent,
            SUM(CASE WHEN orders.status = 'returned' THEN 1 ELSE 0 END) as returned_orders
        ")
        ->where('orders.seller_id', $seller->id)
        ->where('orders.status', '!=', 'cart')
        ->groupBy('orders.customer_id', 'customers.name')
        ->get();

    $totalCustomers = count($customers);
    $totalSpent = $customers->sum('total_spent');
    $totalOrders = $customers->sum('orders');
    $avgAOV = $totalOrders > 0 ? $totalSpent / $totalOrders : 0;
    
    $loyalCount = 0;
    $churnCount = 0;
    
    $segmentHigh = 0;
    $segmentMedium = 0;
    $segmentLow = 0;

    foreach ($customers as $c) {
        $c->aov = $c->orders > 0 ? $c->total_spent / $c->orders : 0;
        $c->return_rate = $c->orders > 0 ? ($c->returned_orders / $c->orders) * 100 : 0;
        
        if ($c->total_spent > 10000) {
            $c->segment = 'High';
            $segmentHigh++;
        } elseif ($c->total_spent > 5000) {
            $c->segment = 'Medium';
            $segmentMedium++;
        } else {
            $c->segment = 'Low';
            $segmentLow++;
        }

        if ($c->orders >= 5) {
            $c->loyalty = 'Loyal';
            $loyalCount++;
        } elseif ($c->orders >= 2) {
            $c->loyalty = 'Regular';
        } else {
            $c->loyalty = 'Churn';
            $churnCount++;
        }
    }

    return view('vshop.seller.analytics.customers', compact(
        'customers', 'totalCustomers', 'avgAOV', 
        'loyalCount', 'churnCount', 
        'segmentHigh', 'segmentMedium', 'segmentLow'
    ));
}
public function campaignAnalytics()
{
    $seller = auth()->guard('seller')->user();

    $rawCampaigns = DB::table('promotion_campaigns')
        ->where(function($q) use ($seller) {
            $q->whereNull('seller_ids')
              ->orWhereJsonContains('seller_ids', (int)$seller->id)
              ->orWhereJsonContains('seller_ids', (string)$seller->id);
        })
        ->get();

    $campaigns = collect();
    foreach ($rawCampaigns as $c) {
        $revenue = (float)($c->total_revenue ?? 0);
        $budget  = (float)($c->total_discount_given ?? 1000);
        $profit  = $revenue - $budget;
        $dsRevenue = round($revenue * 0.4, 2);
        $dsProfit = round($profit * 0.4, 2);
        $promoUsage = (int)($c->used_count ?? $c->total_orders ?? 0);

        $campaigns->push((object)[
            'name' => $c->name ?? 'Campaign #' . $c->id,
            'budget' => $budget,
            'revenue' => $revenue,
            'profit' => $profit,
            'ds_revenue' => $dsRevenue,
            'ds_profit' => $dsProfit,
            'promo_usage' => $promoUsage,
            'status' => $c->status ?? 'active'
        ]);
    }

    if ($campaigns->isEmpty()) {
        $campaigns = collect([
            (object)[
                'name' => 'Store Promotion Campaign',
                'budget' => 2000,
                'revenue' => 15000,
                'profit' => 13000,
                'ds_revenue' => 6000,
                'ds_profit' => 5200,
                'promo_usage' => 25,
                'status' => 'active'
            ]
        ]);
    }

    return view('vshop.seller.analytics.campaigns', compact('campaigns'));
}

public function leadAnalytics()
{
    $seller = auth()->guard('seller')->user();

    // 1. Total Enquiries & Leads count strictly for this seller
    $enquiryCount = DB::table('enquiries')->where('seller_id', $seller->id)->count();
    $leadCount = DB::table('leads')->where('seller_id', $seller->id)->count();
    $totalEnquiries = $enquiryCount + $leadCount;

    $convertedEnquiries = DB::table('enquiries')->where('seller_id', $seller->id)->where('status', 'Converted')->count();
    $convertedLeadsCount = DB::table('leads')->where('seller_id', $seller->id)->where('type', 'enquiry')->where('is_fraud', 0)->count();
    $convertedLeads = $convertedEnquiries + $convertedLeadsCount;

    $conversionRate = $totalEnquiries > 0 ? round(($convertedLeads / $totalEnquiries) * 100, 1) : 0.0;

    // 2. Top Locations from database (Strictly seller's enquiries)
    $locations = DB::table('enquiries')
        ->where('seller_id', $seller->id)
        ->whereNotNull('country')
        ->select('country as label', DB::raw('count(*) as value'))
        ->groupBy('country')
        ->orderByDesc('value')
        ->limit(5)
        ->get();

    // 3. Most Enquired Services (Strictly seller's enquiries or leads)
    $services = DB::table('enquiries')
        ->where('seller_id', $seller->id)
        ->whereNotNull('product_service')
        ->select('product_service as label', DB::raw('count(*) as value'))
        ->groupBy('product_service')
        ->orderByDesc('value')
        ->limit(6)
        ->get();

    if ($services->isEmpty()) {
        $services = DB::table('leads')
            ->join('products', 'leads.product_id', '=', 'products.id')
            ->where('leads.seller_id', $seller->id)
            ->select('products.name as label', DB::raw('count(*) as value'))
            ->groupBy('products.id', 'products.name')
            ->orderByDesc('value')
            ->limit(6)
            ->get();
    }

    // 4. Overtime Trend (Strictly seller data)
    $trend = DB::table('enquiries')
        ->where('seller_id', $seller->id)
        ->selectRaw("DATE_FORMAT(created_at, '%b %Y') as month, count(*) as enquiries, SUM(CASE WHEN status='Converted' THEN 1 ELSE 0 END) as converted")
        ->groupBy('month')
        ->get();

    if ($trend->isEmpty()) {
        $trend = DB::table('leads')
            ->where('seller_id', $seller->id)
            ->selectRaw("DATE_FORMAT(created_at, '%b %Y') as month, count(*) as enquiries, SUM(CASE WHEN type='enquiry' THEN 1 ELSE 0 END) as converted")
            ->groupBy('month')
            ->get();
    }

    // 5. Click & Lead Billing History
    $realBilling = DB::table('enquiries')
        ->where('seller_id', $seller->id)
        ->orderByDesc('created_at')
        ->limit(10)
        ->get();

    $billingHistory = collect();
    foreach ($realBilling as $e) {
        $interaction = 'Website Visit';
        if (isset($e->source)) {
            if ($e->source == 'In Call') $interaction = 'Call Click';
            elseif ($e->source == 'WhatsApp') $interaction = 'WhatsApp Click';
            elseif ($e->source == 'Email') $interaction = 'View Contact';
        }

        $buyerSource = 'Google';
        if (isset($e->source)) {
            if (in_array($e->source, ['Facebook', 'Instagram', 'LinkedIn'])) $buyerSource = 'Social';
            elseif ($e->source == 'Vridhee') $buyerSource = 'App';
            elseif ($e->source == 'Direct') $buyerSource = 'Direct';
        }

        $charge = isset($e->cost) && $e->cost > 0 ? (float)$e->cost : 10;
        $status = 'Deducted';
        if (isset($e->is_refunded) && $e->is_refunded) $status = 'Refunded';
        elseif (isset($e->status) && $e->status == 'Lost') $status = 'Pending Refund';

        $billingHistory->push((object)[
            'date' => Carbon::parse($e->created_at)->format('d-M'),
            'service' => $e->product_service ?? 'General Enquiry',
            'interaction' => $interaction,
            'buyer_source' => $buyerSource,
            'charge' => $charge,
            'status' => $status
        ]);
    }

    return view('vshop.seller.analytics.lead', compact(
        'totalEnquiries',
        'convertedLeads',
        'conversionRate',
        'locations',
        'services',
        'trend',
        'billingHistory'
    ));
}

public function referralAnalytics()
{
    $seller = auth()->guard('seller')->user();

    // Get currency and set rates dynamically
    $currency = request('currency', session('currency', 'INR'));
    session(['currency' => $currency]);

    $rate = 1.0;
    $symbol = '$';
    if ($currency === 'INR') {
        $rate = 83.0;
        $symbol = '₹';
    } elseif ($currency === 'EUR') {
        $rate = 0.92;
        $symbol = '€';
    }

    // 1. Query referral_histories strictly by seller_id
    $hasReferralHistories = \Illuminate\Support\Facades\Schema::hasTable('referral_histories');
    $referralHistories = collect();
    if ($hasReferralHistories) {
        $referralHistories = DB::table('referral_histories')
            ->where('seller_id', $seller->id)
            ->get();
    }

    // 2. Dynamic metrics calculation
    $referralConversions = $referralHistories->count();
    $totalRewardAmount = $referralHistories->sum('amount');

    $referralRevenueRaw = 0;
    if ($hasReferralHistories && $referralConversions > 0) {
        $referralRevenueRaw = DB::table('referral_histories')
            ->join('orders', 'referral_histories.order_id', '=', 'orders.id')
            ->where('referral_histories.seller_id', $seller->id)
            ->sum('orders.total_amount');
    }

    $referralRevenue = ($referralRevenueRaw > 0 ? $referralRevenueRaw : $totalRewardAmount) * $rate;
    $averageOrderValue = $referralConversions > 0 ? ($referralRevenue / $referralConversions) : 0.0;

    $totalClicks = DB::table('leads')->where('seller_id', $seller->id)->where('type', 'click')->count();
    if ($totalClicks < $referralConversions) {
        $totalClicks = $referralConversions > 0 ? $referralConversions * 3 : 0;
    }
    $conversionRate = $totalClicks > 0 ? round(($referralConversions / $totalClicks) * 100, 1) : 0.0;

    // 3. Trend Over Time from referral_histories
    $trend = collect();
    if ($hasReferralHistories && $referralConversions > 0) {
        $trendRaw = DB::table('referral_histories')
            ->select(
                DB::raw("DATE_FORMAT(created_at, '%b %d') as label"),
                DB::raw("COUNT(*) as conversions")
            )
            ->where('seller_id', $seller->id)
            ->groupBy('label')
            ->orderBy(DB::raw("MIN(created_at)"), 'asc')
            ->limit(10)
            ->get();

        $trend = $trendRaw->map(function($item) {
            $c = (int)$item->conversions;
            return (object)[
                'label' => $item->label,
                'conversions' => $c,
                'clicks' => $c * 3
            ];
        });
    }
    if ($trend->isEmpty()) {
        $trend = collect([
            (object)['label' => 'No Data', 'clicks' => 0, 'conversions' => 0]
        ]);
    }

    // 4. Top Referrers Leads & Service from referral_histories + products
    $topReferrersServices = collect();
    if ($hasReferralHistories && $referralConversions > 0) {
        $referredOrderItems = DB::table('referral_histories')
            ->join('order_items', 'referral_histories.order_id', '=', 'order_items.order_id')
            ->join('products', 'order_items.product_id', '=', 'products.id')
            ->select('products.name as service', DB::raw('COUNT(referral_histories.id) as conversions'))
            ->where('referral_histories.seller_id', $seller->id)
            ->groupBy('products.id', 'products.name')
            ->orderByDesc('conversions')
            ->limit(5)
            ->get();

        $topReferrersServices = $referredOrderItems->map(function($item) {
            return (object)[
                'service' => $item->service,
                'clicks' => $item->conversions * 3
            ];
        });
    }
    if ($topReferrersServices->isEmpty()) {
        $topReferrersServices = collect([
            (object)['service' => 'No referred products yet', 'clicks' => 0]
        ]);
    }

    // 5. Source Breakdown (Donut chart)
    $sources = ['Social Share', 'Email', 'Direct Link', 'QR Code'];
    $sourceCounts = [0, 0, 0, 0];
    foreach ($referralHistories as $rh) {
        $index = $rh->id % 4;
        $sourceCounts[$index]++;
    }
    $sourceBreakdown = collect();
    for ($i = 0; $i < 4; $i++) {
        if ($sourceCounts[$i] > 0 || $referralHistories->isEmpty()) {
            $sourceBreakdown->push((object)[
                'name' => $sources[$i],
                'value' => $referralHistories->isEmpty() ? [25, 25, 25, 25][$i] : $sourceCounts[$i]
            ]);
        }
    }

    // 6. Top Referred Products Table
    $topReferredProducts = collect();
    if ($hasReferralHistories && $referralConversions > 0) {
        $productsRaw = DB::table('referral_histories')
            ->join('order_items', 'referral_histories.order_id', '=', 'order_items.order_id')
            ->join('products', 'order_items.product_id', '=', 'products.id')
            ->leftJoin('marketplace_categories', 'products.category_id', '=', 'marketplace_categories.id')
            ->select(
                'products.id',
                'products.name',
                'marketplace_categories.name as category',
                DB::raw('COUNT(referral_histories.id) as conversions'),
                DB::raw('SUM(order_items.total) as sales_value')
            )
            ->where('referral_histories.seller_id', $seller->id)
            ->groupBy('products.id', 'products.name', 'category')
            ->orderByDesc('sales_value')
            ->get();

        $rank = 1;
        foreach ($productsRaw as $p) {
            $avgRating = 5;
            $topReferredProducts->push((object)[
                'rank' => $rank++,
                'name' => $p->name,
                'category' => $p->category ?? 'General',
                'views' => $p->conversions * 4,
                'conversions' => (int)$p->conversions,
                'value' => (float)$p->sales_value * $rate,
                'rating' => $avgRating
            ]);
        }
    }

    // 7. Top Referrers Leaderboard Table from referral_histories
    $leaderboard = collect();
    if ($hasReferralHistories && $referralConversions > 0) {
        $referrersRaw = DB::table('referral_histories')
            ->leftJoin('customers', 'referral_histories.referrer_id', '=', 'customers.id')
            ->select(
                'referral_histories.referrer_id',
                'customers.name',
                DB::raw('COUNT(referral_histories.id) as conversions'),
                DB::raw('SUM(referral_histories.amount) as total_rewards')
            )
            ->where('referral_histories.seller_id', $seller->id)
            ->groupBy('referral_histories.referrer_id', 'customers.name')
            ->orderByDesc('conversions')
            ->get();

        $lRank = 1;
        foreach ($referrersRaw as $r) {
            $leaderboard->push((object)[
                'rank' => $lRank++,
                'name' => $r->name ?? 'Referrer #' . $r->referrer_id,
                'clicks' => $r->conversions * 4,
                'conversions' => (int)$r->conversions,
                'total_rewards' => (float)$r->total_rewards * $rate,
                'status' => 'Active'
            ]);
        }
    }

    // 8. Referral Campaign Performance from referral_configurations table
    $campaignPerformance = collect();
    $hasRefConfigs = \Illuminate\Support\Facades\Schema::hasTable('referral_configurations');
    if ($hasRefConfigs) {
        $configQuery = DB::table('referral_configurations');
        if (\Illuminate\Support\Facades\Schema::hasColumn('referral_configurations', 'seller_id')) {
            $configQuery->where(function($q) use ($seller) {
                $q->whereNull('seller_id')->orWhere('seller_id', $seller->id);
            });
        }
        $configs = $configQuery->get();

        foreach ($configs as $cfg) {
            $cConversions = DB::table('referral_histories')
                ->where('seller_id', $seller->id)
                ->count();
            
            $rewardVal = (float)($cfg->reward_value ?? 0);
            $earn = $cConversions * $rewardVal;

            $campaignPerformance->push((object)[
                'campaign' => isset($cfg->type) ? ucwords(str_replace('_', ' ', $cfg->type)) : 'Standard Campaign',
                'clicks' => $cConversions * 3,
                'conversions' => $cConversions,
                'earn' => round($earn * $rate)
            ]);
        }
    }

    // Calculate average grievance resolution time
    $resolvedGrievances = DB::table('grievances')
        ->where('seller_id', $seller->id)
        ->where('status', 'resolved')
        ->get();
    
    $grievanceTime = '0.1s';
    if ($resolvedGrievances->count() > 0) {
        $totalMinutes = 0;
        foreach ($resolvedGrievances as $g) {
            $created = Carbon::parse($g->created_at);
            $updated = Carbon::parse($g->updated_at);
            $totalMinutes += $created->diffInMinutes($updated);
        }
        $avgMinutes = $totalMinutes / $resolvedGrievances->count();
        if ($avgMinutes < 1) {
            $grievanceTime = round($avgMinutes * 60, 1) . 's';
        } else if ($avgMinutes < 60) {
            $grievanceTime = round($avgMinutes, 1) . 'm';
        } else {
            $grievanceTime = round($avgMinutes / 60, 1) . 'h';
        }
    }

    return view('vshop.seller.analytics.referral', compact(
        'totalClicks',
        'referralConversions',
        'conversionRate',
        'referralRevenue',
        'averageOrderValue',
        'trend',
        'topReferrersServices',
        'sourceBreakdown',
        'topReferredProducts',
        'leaderboard',
        'campaignPerformance',
        'grievanceTime',
        'currency',
        'symbol'
    ));
}



public function grievanceList(Request $request)
{
    $seller = auth()->guard('seller')->user();

    $query = \App\Models\Grievance::where('seller_id', $seller->id);

    /* 🔍 SEARCH */
    if ($request->filled('search')) {
        $search = trim($request->search);

        $query->where(function($q) use ($search){
            $q->where('subject', 'LIKE', "%$search%");
        });
    }

    /* 📊 STATUS */
    if ($request->filled('status')) {
        $status = trim(strtolower($request->status));

        $query->whereRaw('LOWER(status) = ?', [$status]);
    }

    /* ⚡ PRIORITY */
    if ($request->filled('priority')) {
        $priority = trim(strtolower($request->priority));

        $query->whereRaw('LOWER(priority) = ?', [$priority]);
    }

    /* 📅 ORDER */
    $tickets = $query->orderByDesc('created_at')->get();

    return view('vshop.seller.grievance.list', compact('tickets'));
}

public function grievanceCreate()
{
    $ticket = null;
    return view('vshop.seller.grievance.create', compact('ticket'));
}

public function grievanceStore(Request $request)
{
    $seller = auth()->guard('seller')->user();

    // ✅ VALIDATION
    $request->validate([
        'type' => 'required',
        'subject' => 'required',
        'message' => 'required',
        'priority' => 'required',
        'assigned_to' => 'nullable|string'
    ]);

    // ✅ CREATE TICKET
    $ticket = \App\Models\Grievance::create([
        'seller_id'   => $seller->id,
        'type'        => $request->type,
        'subject'     => $request->subject,
        'message'     => $request->message,
        'priority'    => $request->priority,
        'status'      => 'open', // 🔥 important default
        'assigned_to' => $request->assigned_to ?? $this->autoAssign($request->type)
    ]);

    // ✅ CREATE MESSAGE
    \App\Models\GrievanceMessage::create([
        'grievance_id' => $ticket->id,
        'sender'       => 'seller',
        'message'      => $request->message
    ]);

    \App\Services\NotificationService::notify(
        'all',
        'admin',
        'grievance',
        "New grievance ticket raised by seller {$seller->name}.",
        route('admin.grievance.show', $ticket->id)
    );

    return redirect()->route('seller.grievance.list')
        ->with('success','Ticket Created Successfully');
}
public function grievanceView($id)
{
    $ticket = \App\Models\Grievance::findOrFail($id);

    $messages = \App\Models\GrievanceMessage::where('grievance_id',$id)->get();

    return view('vshop.seller.grievance.view', compact('ticket','messages'));
}


public function grievanceMessage(Request $request)
{
    $request->validate([
        'grievance_id' => 'required',
        'message'      => 'required',
        'status'       => 'required',
    ]);

    // FIND TICKET
    $ticket = Grievance::findOrFail($request->grievance_id);

    // SAVE MESSAGE
    GrievanceMessage::create([
        'grievance_id' => $request->grievance_id,
        'sender'       => 'seller',
        'message'      => $request->message,
    ]);

    // UPDATE STATUS
    $ticket->status = $request->status;
    $ticket->save();

    \App\Services\NotificationService::notify(
        'all',
        'admin',
        'grievance',
        "Seller " . ($ticket->seller->name ?? 'Unknown') . " replied to ticket #{$ticket->id}.",
        route('admin.grievance.show', $ticket->id)
    );

    // REDIRECT TO LIST PAGE
    return redirect()
            ->route('seller.grievance.list')
            ->with('success', 'Reply sent and status updated successfully.');
}




public function wallet()
{
    $seller = auth()->guard('seller')->user();

    /*
    |--------------------------------------------------------------------------
    | CURRENT SETTLEMENT CONFIG
    |--------------------------------------------------------------------------
    */
    $currentSettlementCycle = $seller->settlement_cycle ?? 15;

    /*
    |--------------------------------------------------------------------------
    | FETCH REAL SELLER WALLET FROM `wallets` TABLE
    |--------------------------------------------------------------------------
    */
    $wallet = \App\Models\Wallet::where('seller_id', $seller->id)
        ->orWhere(function($q) use ($seller) {
            $q->where('customer_id', $seller->id)->where('user_type', 'seller');
        })
        ->first();

    if (!$wallet) {
        $wallet = \App\Models\Wallet::create([
            'seller_id' => $seller->id,
            'user_type' => 'seller',
            'balance'   => 0.00,
            'frozen_balance' => 0.00,
            'is_frozen' => 0,
        ]);
    }

    $available = (float) $wallet->balance;
    $hold = (float) abs($wallet->frozen_balance);
    $total_balances = $available + $hold;

    /*
    |--------------------------------------------------------------------------
    | REAL TRANSACTIONS FROM `wallet_transactions` TABLE
    |--------------------------------------------------------------------------
    */
    $walletTransactionsQuery = \App\Models\WalletTransaction::where(function($q) use ($seller, $wallet) {
            $q->where('seller_id', $seller->id)
              ->orWhere('wallet_id', $wallet->id);
        });

    $walletTransactions = (clone $walletTransactionsQuery)->orderBy('created_at', 'desc')->paginate(15);

    $released = (float) (clone $walletTransactionsQuery)->where('credit', '>', 0)->sum('credit');
    if ($released <= 0) {
        $released = (float) SellerFinance::where('seller_id', $seller->id)->where('status', 'success')->sum('credit');
    }

    $lead_deduction_wallet = (float) (clone $walletTransactionsQuery)->where(function($q) {
        $q->where('description', 'like', '%lead%')
          ->orWhere('transaction_type', 'like', '%lead%');
    })->sum('credit');

    $boost_spend_wallet = (float) (clone $walletTransactionsQuery)->where(function($q) {
        $q->where('description', 'like', '%boost%')
          ->orWhere('transaction_type', 'like', '%boost%');
    })->sum('credit');

    /*
    |--------------------------------------------------------------------------
    | DEDUCTION SUMMARY (TODAY & MONTHLY)
    |--------------------------------------------------------------------------
    */
    $today_deduction = (float) (clone $walletTransactionsQuery)
        ->where('debit', '>', 0)
        ->whereDate('created_at', Carbon::today())
        ->sum('debit');

    $monthly_deduction = (float) (clone $walletTransactionsQuery)
        ->where('debit', '>', 0)
        ->whereMonth('created_at', Carbon::now()->month)
        ->whereYear('created_at', Carbon::now()->year)
        ->sum('debit');

    $refunds = (float) (clone $walletTransactionsQuery)
        ->where(function($q) {
            $q->where('description', 'like', '%refund%')
              ->orWhere('transaction_type', 'like', '%refund%');
        })->sum('debit');

    $dispute_hold = (float) (clone $walletTransactionsQuery)
        ->where('status', 'hold')
        ->sum('debit');

    /*
    |--------------------------------------------------------------------------
    | TRANSACTIONS & ORDERS (LEDGER)
    |--------------------------------------------------------------------------
    */
    $transactions = SellerFinance::where('seller_id', $seller->id)->latest()->paginate(15);
    $orders = Order::where('seller_id', $seller->id)->latest()->paginate(15);

    /*
    |--------------------------------------------------------------------------
    | CLICK & LEAD BILLING LOGS (FROM REAL ENQUIRIES)
    |--------------------------------------------------------------------------
    */
    $realBilling = DB::table('enquiries')
        ->where('seller_id', $seller->id)
        ->orderByDesc('created_at')
        ->limit(15)
        ->get();

    $clickLeadBilling = collect();
    foreach ($realBilling as $e) {
        $interaction = 'Website Visit';
        if (isset($e->source)) {
            if ($e->source == 'In Call') $interaction = 'Call Click';
            elseif ($e->source == 'WhatsApp') $interaction = 'WhatsApp Click';
            elseif ($e->source == 'Email') $interaction = 'View Contact';
        }

        $charge = isset($e->cost) && $e->cost > 0 ? (float)$e->cost : 10.00;
        $status = 'Deducted';
        if (isset($e->is_refunded) && $e->is_refunded) $status = 'Refunded';

        $clickLeadBilling->push((object)[
            'date' => Carbon::parse($e->created_at)->format('d/m/Y'),
            'listing_name' => $e->product_service ?? 'General Enquiry',
            'interaction_type' => $interaction,
            'buyer_source' => $e->source ?? 'Google',
            'charge' => $charge,
            'wallet_deducted' => 'Lead',
            'status' => $status
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | REFERRAL FINANCE DATA
    |--------------------------------------------------------------------------
    */
    $referralFinance = DB::table('orders')
        ->where('seller_id', $seller->id)
        ->latest()
        ->limit(10)
        ->get()
        ->map(function($o) {
            return (object)[
                'date' => Carbon::parse($o->created_at)->format('Y-m-d'),
                'name' => 'Order #' . $o->id,
                'deduction' => (float)($o->total_amount * 0.05),
                'sales' => (float)$o->total_amount,
                'budget' => (float)($o->total_amount * 0.1)
            ];
        });
    /*
    |--------------------------------------------------------------------------
    | INTERESTING SMALL ATTRACTIONS (DYNAMIC BASED ON LOGIC)
    |--------------------------------------------------------------------------
    */
    $interestingAttractions = DB::table('products')
        ->where('created_by_id', $seller->id)
        ->latest()
        ->limit(3)
        ->get();

    return view('vshop.seller.finance.wallet', compact(
        'seller',
        'wallet',
        'available',
        'hold',
        'released',
        'refunds',
        'dispute_hold',
        'transactions',
        'currentSettlementCycle',
        'orders',
        'referralFinance',
        'lead_deduction_wallet',
        'boost_spend_wallet',
        'today_deduction',
        'monthly_deduction',
        'total_balances',
        'clickLeadBilling',
        'walletTransactions',
        'interestingAttractions'
    ));
}

/**
 * Handle wallet recharge request
 */
public function rechargeWallet(Request $request)
{
    try {
        $seller = auth()->guard('seller')->user();

        $request->validate([
            'amount' => 'required|numeric|min:1|max:1000000',
            'payment_method' => 'required|string|in:bank_transfer,upi,card,cash,cheque',
            'transaction_id' => 'required|string|max:255',
            'attachment' => 'required|file|mimes:jpg,jpeg,png,pdf|max:5120',
            'remarks' => 'nullable|string|max:1000',
        ]);

        // Handle file upload
        $attachmentPath = null;
        if ($request->hasFile('attachment')) {
            $file = $request->file('attachment');
            $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $attachmentPath = $file->storeAs('wallet_recharges/' . $seller->id, $filename, 'public');
        }

        // Create wallet transaction record using your existing table
        $walletTransaction = \App\Models\WalletTransaction::create([
            'user_id' => $seller->id,
            'brand_id' => $seller->brand_id ?? null,
            'branch_id' => $seller->branch_id ?? null,
            'amount' => $request->amount,
            'type' => 'credit',
            'payment_method' => $request->payment_method,
            'transaction_id' => $request->transaction_id,
            'remarks' => $request->remarks ?? 'Wallet recharge request',
            'attachment' => $attachmentPath,
            'status' => 'pending',
            'updated_by' => $seller->id,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Recharge request submitted successfully. Please wait for verification.',
            'data' => $walletTransaction
        ], 200);

    } catch (\Illuminate\Validation\ValidationException $e) {
        return response()->json([
            'success' => false,
            'message' => 'Validation failed',
            'errors' => $e->errors()
        ], 422);
    } catch (\Exception $e) {
        \Log::error('Wallet recharge error: ' . $e->getMessage());
        return response()->json([
            'success' => false,
            'message' => 'Failed to submit recharge request. Please try again.'
        ], 500);
    }
}

  public function store(Request $request)
    {
        $request->validate([
            'reference_no' => 'nullable|string|max:100',
            'transaction_type' => 'required|string',
            'credit' => 'nullable|numeric',
            'debit' => 'nullable|numeric',
            'tax' => 'nullable|numeric',
        ]);

        Transaction::create([
            'user_id' => auth('seller')->id(),
            'reference_no' => $request->reference_no,
            'transaction_type' => $request->transaction_type,
            'credit' => $request->credit ?? 0,
            'debit' => $request->debit ?? 0,
            'tax' => $request->tax ?? 0,
            'status' => 'success'
        ]);

        return redirect()->back()->with('success', 'Transaction added successfully');
    }


public function downloadInvoice($id, Request $request)
{
    $txn = SellerFinance::findOrFail($id);

    $currency = $request->currency ?? 'INR';

    // conversion rates (same as frontend)
    $rates = [
        'INR' => 1,
        'USD' => 0.012,
        'EUR' => 0.011
    ];

    $symbols = [
        'INR' => '₹',
        'USD' => '$',
        'EUR' => '€'
    ];

    $rate = $rates[$currency];
    $symbol = $symbols[$currency];

    return Pdf::loadView('vshop.seller.finance.invoice_pdf', [
        'txn' => $txn,
        'rate' => $rate,
        'symbol' => $symbol,
        'currency' => $currency
    ])->stream('invoice_'.$txn->id.'.pdf');
}
public function viewDispute($id)
{
    $txn = SellerFinance::where('id',$id)
        ->where('seller_id',auth()->guard('seller')->id())
        ->first();

    if(!$txn){
        return back()->with('error','Transaction not found');
    }

    return view('vshop.seller.finance.dispute_view', compact('txn'));
}


public function exportMonthlyReport()
{
    $sellerId = Auth::guard('seller')->id();

    $data = Commission::where('seller_id', $sellerId)
                ->whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->get();

    if ($data->isEmpty()) {
        return back()->with('error', 'No data to export');
    }

    $fileName = "monthly_report_" . date('Y_m') . ".csv";

    $headers = [
        "Content-Type" => "text/csv",
        "Content-Disposition" => "attachment; filename=$fileName",
    ];

    $callback = function () use ($data) {

        $file = fopen('php://output', 'w');

        fputcsv($file, [
            'Order ID',
            'Product',
            'Amount',
            'Commission',
            'Fees',
            'Net',
            'Status',
            'Date'
        ]);

        foreach ($data as $row) {

            $totalFees = ($row->logistic_fee ?? 0)
                        + ($row->payment_gateway_fee ?? 0)
                        + ($row->service_fee ?? 0);

            fputcsv($file, [
                $row->order_id,
                $row->product_service,
                $row->order_amount,
                $row->commission_amount,
                $totalFees,
                $row->net_earning,
                $row->reconciliation_status,
                optional($row->created_at)->format('d-m-Y')
            ]);
        }

        fclose($file);
    };

    return response()->stream($callback, 200, $headers);
}


public function saveBankDetails(Request $request)
{
    $seller = auth()->guard('seller')->user();

    $method      = strtolower($request->method);
    $accountId   = $request->account_id;
    $isReverify  = $request->reverify_request;

    // Debug logging
    \Log::info('Bank Save Request', $request->all());

    if ($method == 'bank') {

        $request->validate([
            'bank_name'       => 'required|string|max:120',
            'account_name'    => 'required|regex:/^[A-Za-z ]{3,50}$/',
            'account_number'  => 'required|digits_between:8,18',
            'ifsc'            => 'required|regex:/^[A-Z]{4}0[A-Z0-9]{6}$/',
            'cancel_cheque'   => 'nullable|mimes:jpg,jpeg,png,pdf|max:2048'
        ]);

        $ifscCode = strtoupper($request->ifsc);
        $bankName = $request->bank_name;
        $accountName = $request->account_name;
        $accountNumber = $request->account_number;
        $upiId = null;

    }

    if ($method == 'upi') {

        $request->validate([
            'upi' => ['required', 'regex:/^[\w\.\-]+@[\w\.\-]+$/'],
            'account_name' => 'nullable|regex:/^[A-Za-z ]{3,50}$/',
            'cancel_cheque' => 'nullable|mimes:jpg,jpeg,png,pdf|max:2048'
        ]);

        $ifscCode = null;
        $bankName = null;
        $accountNumber = null;
        $accountName = $request->account_name;
        $upiId = $request->upi;
    }

    // File upload handling
    $cheque = null;
    
    // Check for both cancel_cheque field names (bank uses 'cancel_cheque', UPI uses 'cancel_cheque_upi')
    if ($request->hasFile('cancel_cheque')) {
        $cheque = $request->file('cancel_cheque')->store('cheques', 'public');
    } elseif ($request->hasFile('cancel_cheque_upi')) {
        $cheque = $request->file('cancel_cheque_upi')->store('cheques', 'public');
    }

    // UPDATE EXISTING ACCOUNT
    if ($accountId) {

        $bankAccount = SellerBankAccount::where('id', $accountId)
            ->where('seller_id', $seller->id)
            ->first();

        if (!$bankAccount) {
            return back()->with('error', 'Account not found');
        }

        $updateData = [
            'method' => $method,
            'bank_name' => $bankName,
            'account_name' => $accountName,
            'account_number' => $accountNumber,
            'ifsc_code' => $ifscCode,
            'upi_id' => $upiId,
        ];

        if ($cheque) {
            $updateData['cancel_cheque'] = $cheque;
        }

        if ($isReverify) {
            $updateData['status'] = 'pending';
            $message = 'Bank / UPI reverification requested successfully';
        } else {
            $updateData['status'] = 'pending';
            $message = 'Bank / UPI account updated successfully. Awaiting verification.';
        }

        $bankAccount->update($updateData);
        
        \Log::info('Account Updated', $updateData);

        return back()->with('success', $message);
    }

    // CREATE NEW ACCOUNT
    $isPrimary = SellerBankAccount::where('seller_id', $seller->id)->count() == 0 ? 1 : 0;

    $createData = [
        'seller_id' => $seller->id,
        'method' => $method,
        'bank_name' => $bankName,
        'account_name' => $accountName,
        'account_number' => $accountNumber,
        'ifsc_code' => $ifscCode,
        'upi_id' => $upiId,
        'cancel_cheque' => $cheque,
        'status' => 'pending',
        'is_primary' => $isPrimary
    ];

    \Log::info('Creating New Account', $createData);

    SellerBankAccount::create($createData);

    return back()->with('success', 'Bank / UPI account added successfully. Awaiting verification.');
}

public function deleteBankAccount($id)
{
    $seller = auth()->guard('seller')->user();
    
    $bankAccount = SellerBankAccount::where('id', $id)
        ->where('seller_id', $seller->id)
        ->first();
    
    if (!$bankAccount) {
        return response()->json([
            'success' => false,
            'message' => 'Account not found'
        ]);
    }
    
    // Check if this is the only account
    $accountCount = SellerBankAccount::where('seller_id', $seller->id)->count();
    
    if ($accountCount == 1) {
        return response()->json([
            'success' => false,
            'message' => 'Cannot delete the only bank account. Please add another account first.'
        ]);
    }
    
    // If deleting primary account, set another account as primary
    if ($bankAccount->is_primary == 1) {
        $anotherAccount = SellerBankAccount::where('seller_id', $seller->id)
            ->where('id', '!=', $id)
            ->first();
        
        if ($anotherAccount) {
            $anotherAccount->update(['is_primary' => 1]);
        }
    }
    
    // Delete the account
    $bankAccount->delete();
    
    return response()->json([
        'success' => true,
        'message' => 'Bank account deleted successfully'
    ]);
}
public function withdraw(Request $request)
{
    $seller = auth()->guard('seller')->user();

    $request->validate([
        'amount' => 'required|numeric|min:100'
    ]);

    $currentBalance =
        SellerFinance::where('seller_id',$seller->id)->sum('credit')
        -
        SellerFinance::where('seller_id',$seller->id)->sum('debit');

    if($request->amount > $currentBalance){
        return back()->with('error','Insufficient balance');
    }

    $gst = $request->amount * 0.18;

    SellerFinance::create([
        'seller_id' => $seller->id,
        'transaction_type' => 'payout_request',
        'debit' => $request->amount,
        'tax_amount' => $gst, // ✅ GST STORE
        'status' => 'pending',
        'payout_method' => $request->method,
        'note' => 'Withdrawal Request',
        'balance' => $currentBalance - $request->amount,
    ]);

    return back()->with('success','Withdrawal Requested');
}

public function exportGST()
{
    $seller = auth()->guard('seller')->user();

    $data = SellerFinance::where('seller_id', $seller->id)
        ->where('transaction_type', 'payout_success')
        ->get();

    $filename = "gst_report.csv";

    return response()->stream(function() use ($data){

        $file = fopen('php://output', 'w');

        fputcsv($file, ['Date','Amount','GST','Total']);

        foreach($data as $row){

            $gst = $row->tax_amount ?? ($row->debit * 0.18);

            fputcsv($file, [
                $row->created_at->format('d-m-Y'),
                $row->debit,
                round($gst,2),
                round($row->debit + $gst,2)
            ]);
        }

        fclose($file);

    }, 200, [
        "Content-Type" => "text/csv",
        "Content-Disposition" => "attachment; filename=$filename",
    ]);
}

public function createPayoutInvoice($txnId)
{
    $txn = SellerFinance::find($txnId);

    if(!$txn) return;

    $gst = $txn->debit * 0.18;
    $total = $txn->debit + $gst;

    SellerInvoice::create([
        'seller_id' => $txn->seller_id,
        'invoice_no' => 'INV-PAYOUT-'.time(),
        'total_amount' => $txn->debit,
        'taxable_amount' => $txn->debit,
        'gst_amount' => $gst,
        'net_amount' => $total,
        'invoice_date' => now(),
    ]);
}

public function monthlyGST()
{
    $seller = auth()->guard('seller')->user();

    $data = SellerFinance::selectRaw("
        MONTH(created_at) as month,
        SUM(debit) as total,
        SUM(tax_amount) as gst
    ")
    ->where('seller_id',$seller->id)
    ->where('transaction_type','payout_success')
    ->groupBy('month')
    ->get();

    return view('vshop.seller.finance.gst_monthly', compact('data'));
}

public function gstReport()
{
    $seller = auth()->guard('seller')->user();

    $data = SellerFinance::where('seller_id',$seller->id)
        ->where('transaction_type','payout_success')
        ->get();

    $pdf = Pdf::loadView('vshop.seller.finance.gst_pdf', compact('data'));

    return $pdf->download('GST_Report.pdf');
}

public function markPayoutSuccess($id)
{
    $txn = SellerFinance::findOrFail($id);

    $txn->update([
        'status' => 'success',
        'transaction_type' => 'payout_success',
        'utr_number' => 'UTR'.rand(10000,99999)
    ]);

    $this->createPayoutInvoice($txn->id);

    return back()->with('success','Payout marked as success');
}

public function export(Request $request)
{
    $seller = auth()->guard('seller')->user();

    $query = SellerFinance::where('seller_id', $seller->id);

    // APPLY SAME FILTERS
    if ($request->filled('type')) {
        $query->where('transaction_type', $request->type);
    }

    if ($request->filled('from')) {
        $query->whereDate('created_at', '>=', $request->from);
    }

    if ($request->filled('to')) {
        $query->whereDate('created_at', '<=', $request->to);
    }

    if ($request->filled('order_id')) {
        $query->where('reference_no', 'like', '%' . $request->order_id . '%');
    }

    if ($request->filled('amount')) {
        $query->where(function ($q) use ($request) {
            $q->where('credit', $request->amount)
              ->orWhere('debit', $request->amount);
        });
    }

    $data = $query->latest()->get();

    /*
    |--------------------------------------
    | CSV EXPORT
    |--------------------------------------
    */
    if ($request->type == 'csv') {

        $filename = "transactions.csv";

        $headers = ['Content-Type' => 'text/csv'];

        $callback = function() use ($data) {

            $file = fopen('php://output', 'w');

            fputcsv($file, ['Date','Ref','Type','Credit','Debit','Status']);

            foreach ($data as $row) {
                fputcsv($file, [
                    $row->created_at,
                    $row->reference_no,
                    $row->transaction_type,
                    $row->credit,
                    $row->debit,
                    $row->status
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /*
    |--------------------------------------
    | EXCEL EXPORT
    |--------------------------------------
    */
    if ($request->type == 'excel') {

        $filename = "transactions.xls";

        $headers = [
            "Content-Type" => "application/vnd.ms-excel",
            "Content-Disposition" => "attachment; filename=$filename",
        ];

        $callback = function() use ($data) {
            echo "Date\tRef\tType\tCredit\tDebit\tStatus\n";

            foreach ($data as $row) {
                echo "{$row->created_at}\t{$row->reference_no}\t{$row->transaction_type}\t{$row->credit}\t{$row->debit}\t{$row->status}\n";
            }
        };

        return response()->stream($callback, 200, $headers);
    }

    return back();
}



public function transactionexport(Request $request)
{
    $seller = auth()->guard('seller')->user();

    $query = SellerFinance::where('seller_id', $seller->id);

    // 🔍 APPLY SAME FILTERS
    if ($request->from) {
        $query->whereDate('created_at', '>=', $request->from);
    }

    if ($request->to) {
        $query->whereDate('created_at', '<=', $request->to);
    }

    if ($request->type) {
        $query->where('transaction_type', $request->type);
    }

    if ($request->order_id) {
        $query->where(function ($q) use ($request) {
            $q->where('reference_no', 'like', "%{$request->order_id}%")
              ->orWhere('id', 'like', "%{$request->order_id}%");
        });
    }

    if ($request->amount) {
        $query->where(function ($q) use ($request) {
            $q->where('credit', $request->amount)
              ->orWhere('debit', $request->amount);
        });
    }

    $transactions = $query->latest()->get();

    // 🔥 DOWNLOAD CSV
    $filename = "transactions_" . date('Ymd_His') . ".csv";

    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="' . $filename . '"');

    $output = fopen('php://output', 'w');

    // 🔥 CSV HEADER
    fputcsv($output, [
        'Date',
        'Transaction ID',
        'Type',
        'Reference',
        'Credit',
        'Debit',
        'Status'
    ]);

    // 🔥 DATA
    foreach ($transactions as $t) {

        // mapping
        $typeMap = [
            'earning' => 'Order',
            'refund' => 'Refund',
            'fee' => 'Fee',
            'payout_success' => 'Payout'
        ];

        fputcsv($output, [
            date('Y-m-d H:i:s', strtotime($t->created_at)),
            $t->id,
            $typeMap[$t->transaction_type] ?? $t->transaction_type,
            $t->reference_no,
            $t->credit,
            $t->debit,
            ucfirst($t->status)
        ]);
    }

    fclose($output);
    exit;
}



public function savePayoutSettings(Request $request)
{
    $seller = auth()->guard('seller')->user();

    if(!$seller){
        abort(403, 'Unauthorized');
    }

    $request->validate([
        'payout_mode' => 'required|in:auto,manual',
        'manual_next_date' => 'nullable|date'
    ]);

    $seller->payout_mode = $request->payout_mode;
    
    // Save next payout date with proper timezone
    if($request->filled('manual_next_date')) {
        // Store as is without timezone conversion
        $seller->manual_next_date = $request->manual_next_date;
    } else {
        $seller->manual_next_date = null;
    }
    
    $seller->save();

    return back()->with('success', 'Payout settings updated successfully');
}
public function payoutexport($type)
{
    $seller = auth()->guard('seller')->user();

    if (!$seller) {
        abort(403, 'Unauthorized');
    }

    /* ================= FETCH DATA ================= */
    $data = SellerFinance::where('seller_id', $seller->id)
        ->whereIn('transaction_type', [
            'payout_request',
            'payout_success',
            'payout_failed'
        ])
        ->latest()
        ->get();

    /* ================= FORMAT DATA ================= */
    $formatted = $data->map(function ($item) {
        return [
            'date'   => $item->created_at->format('d M Y'),
            'amount' => $item->debit ?? 0,
            'type'   => ucfirst(str_replace('_', ' ', $item->transaction_type)),
            'status' => match ($item->transaction_type) {
                'payout_success' => 'Success',
                'payout_failed'  => 'Failed',
                default          => 'Pending',
            },
        ];
    });

    /* ================= PDF EXPORT ================= */
    if ($type === 'pdf') {

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView(
            'vshop.seller.finance.payout_pdf',
            [
                'data'   => $formatted,
                'seller' => $seller
            ]
        );

        return $pdf->download('payout_report_' . date('Y_m_d_His') . '.pdf');
    }

    /* ================= CSV EXPORT ================= */
    if ($type === 'excel') {

        $filename = "payout_report_" . date('Y_m_d_His') . ".csv";

        return response()->stream(function () use ($formatted) {

            $file = fopen('php://output', 'w');

            // HEADER ROW
            fputcsv($file, ['Date', 'Amount', 'Type', 'Status']);

            // DATA ROWS
            foreach ($formatted as $row) {
                fputcsv($file, [
                    $row['date'],
                    $row['amount'],
                    $row['type'],
                    $row['status']
                ]);
            }

            fclose($file);

        }, 200, [
            "Content-Type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=$filename",
            "Cache-Control"       => "no-cache, must-revalidate",
            "Pragma"              => "no-cache",
            "Expires"             => "0"
        ]);
    }

    /* ================= INVALID TYPE ================= */
    abort(404, 'Invalid export type');
}


public function closeGrievance(Request $request)
{
    $ticket = \App\Models\Grievance::find($request->grievance_id);

    if(!$ticket){
        return back()->with('error','Ticket not found');
    }

    // ✅ CLOSE TICKET
    $ticket->status = 'close';
    $ticket->save();

    // ✅ OPTIONAL LOG MESSAGE
    \App\Models\GrievanceMessage::create([
        'grievance_id' => $ticket->id,
        'sender' => 'seller',
        'message' => 'Ticket closed by seller'
    ]);

    return redirect()
        ->route('seller.grievance.list')
        ->with('success','Ticket closed successfully');
}


public function updateSettlementCycle(Request $request)
{
    $request->validate([
        'settlement_cycle' => 'required|integer|min:0|max:30'
    ]);

    $seller = Auth::guard('seller')->user();

    // UPDATE CURRENT SELLER CONFIG
    $seller->settlement_cycle = $request->settlement_cycle;

    $seller->save();

    return response()->json([

        'status' => true,
        'message' => 'Settlement cycle updated successfully'

    ]);
}
}
