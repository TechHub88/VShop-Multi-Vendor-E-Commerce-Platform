<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Wallet;
use App\Models\WalletTransaction;
use App\Models\WithdrawalRequest;
use App\Models\User;
use App\Models\Seller;
use App\Models\Customer;
use App\Models\Refund;
use App\Models\Team;
use App\Models\AdminStaff;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Pagination\LengthAwarePaginator;

class WalletController extends Controller
{
    /**
     * Display the wallet dashboard
     */
    public function index()
    {
        // Get dashboard statistics
        // Admin's "Total Balance" is sum of all placed orders minus refunds.
        $totalOrdersValue = DB::table('orders')->whereNotIn('status', ['cancelled'])->sum('total_amount');
        $totalRefunds = DB::table('wallet_transactions')
            ->where('user_type', 'customer')
            ->where('transaction_type', 'refund')
            ->sum('credit');
        $totalBalance = $totalOrdersValue - $totalRefunds;
        $sellerBalance = Wallet::where('user_type', 'seller')->sum('balance');
        $buyerBalance = Wallet::where('user_type', 'customer')->sum('balance');
        $frozenAmount = Wallet::sum('frozen_balance');
        $pendingWithdrawals = WithdrawalRequest::where('status', 'pending')->sum('amount');
        $pendingWithdrawalsCount = WithdrawalRequest::where('status', 'pending')->count();
        
        // Donut chart data
        $donutData = [
            'seller' => (float) $sellerBalance,
            'buyer' => (float) $buyerBalance,
            'frozen' => (float) $frozenAmount,
            'pending' => (float) $pendingWithdrawals
        ];
        
        // Trend data for last 7 days
        $trendData = $this->getTrendData();
        
        // Recent transactions (first page)
        $transactions = WalletTransaction::orderBy('created_at', 'desc')->paginate(10);
        
        // Add user_name and badge_color to each transaction
        foreach ($transactions as $transaction) {
            $transaction->user_name = $this->getUserName($transaction);
            $transaction->badge_color = $this->getTransactionBadgeColor($transaction->transaction_type);
        }
        
        // Pending withdrawals list
        $pendingWithdrawalsList = WithdrawalRequest::where('status', 'pending')
            ->orderBy('created_at', 'desc')
            ->paginate(10);
        
        foreach ($pendingWithdrawalsList as $withdrawal) {
            $withdrawal->user_name = $this->getWithdrawalUserName($withdrawal);
        }
        $buyers = Customer::select('id', 'name')->orderBy('name')->get();
        
        // Platform wallet balance is the sum of platform earnings from all order items
        $platformBalance = DB::table('order_items')->sum('platform_earning');
        
        // Refunds data from order_returns table (all buyers' requests)
        $refundsData = DB::table('order_returns')
            ->join('orders', 'order_returns.order_id', '=', 'orders.id')
            ->leftJoin('customers', 'order_returns.customer_id', '=', 'customers.id')
            ->select(
                'order_returns.*',
                'orders.total_amount',
                'orders.seller_id',
                'customers.name as buyer_name'
            )
            ->orderBy('order_returns.created_at', 'desc')
            ->paginate(10);

        return view('admin.wallet.index', compact(
            'totalBalance', 'sellerBalance', 'buyerBalance', 'frozenAmount',
            'pendingWithdrawals', 'pendingWithdrawalsCount', 'donutData',
            'trendData', 'transactions', 'pendingWithdrawalsList', 'buyers',
            'platformBalance', 'refundsData'
        ));
    }
    
    /**
     * Get transactions with filters (AJAX)
     */
    public function getTransactions(Request $request)
    {
        $query = WalletTransaction::query();
        
        if ($request->user_type && $request->user_type != '') {
            $query->where('user_type', $request->user_type);
        }
        
        if ($request->user_id && $request->user_id != '') {
            if ($request->user_type == 'seller') {
                $query->where('seller_id', $request->user_id);
            } else if ($request->user_type == 'customer') {
                $query->where('customer_id', $request->user_id);
            }
        }
        
        if ($request->transaction_type && $request->transaction_type != '') {
            $query->where('transaction_type', $request->transaction_type);
        }
        
        if ($request->start_date && $request->end_date) {
            $query->whereBetween('created_at', [
                $request->start_date . ' 00:00:00',
                $request->end_date . ' 23:59:59'
            ]);
        }
        
        $transactions = $query->orderBy('created_at', 'desc')->paginate(10);
        
        foreach ($transactions as $transaction) {
            $transaction->user_name = $this->getUserName($transaction);
            $transaction->badge_color = $this->getTransactionBadgeColor($transaction->transaction_type);
        }
        
        return response()->json([
            'success' => true,
            'data' => $transactions
        ]);
    }
    
    /**
     * Get users by type for dropdown (AJAX)
     */
    public function getUsers(Request $request)
    {
        $userType = $request->user_type;
        $users = [];
        
        if ($userType == 'seller') {
            $sellers = Seller::select('id', 'name')->get();
            foreach ($sellers as $seller) {
                $users[] = ['id' => $seller->id, 'text' => $seller->name];
            }
        } else if ($userType == 'customer') {
            // Fetch from customers table instead of users
            $customers = Customer::select('id', 'name')->get();
            foreach ($customers as $customer) {
                $users[] = ['id' => $customer->id, 'text' => $customer->name];
            }
        }
        
        return response()->json([
            'success' => true,
            'data' => $users
        ]);
    }
    
    /**
     * Get withdrawal requests (AJAX)
     */
    public function getWithdrawals(Request $request)
    {
        $status = $request->status ?? 'pending';
        
        $withdrawals = WithdrawalRequest::where('status', $status)
            ->orderBy('created_at', 'desc')
            ->paginate(10);
        
        foreach ($withdrawals as $withdrawal) {
            $withdrawal->user_name = $this->getWithdrawalUserName($withdrawal);
        }
        
        return response()->json([
            'success' => true,
            'data' => $withdrawals
        ]);
    }
    
    /**
     * Freeze a user's wallet
     */
    public function freezeWallet(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_type' => 'required|in:seller,customer',
            'user_id' => 'required|integer',
            'reason' => 'required|string|max:500'
        ]);
        
        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }
        
        $wallet = Wallet::where('user_type', $request->user_type)
            ->where($request->user_type == 'seller' ? 'seller_id' : 'customer_id', $request->user_id)
            ->first();
        
        if (!$wallet) {
            return response()->json(['success' => false, 'message' => 'Wallet not found'], 404);
        }
        
        if ($wallet->is_frozen) {
            return response()->json(['success' => false, 'message' => 'Wallet is already frozen'], 400);
        }
        
        $wallet->is_frozen = true;
        $wallet->frozen_at = now();
        $wallet->freeze_reason = $request->reason;
        $wallet->save();
        
        // Create log entry
        \App\Models\WalletFrozenLog::create([
            'wallet_id' => $wallet->id,
            'action' => 'freeze',
            'reason' => $request->reason,
            'performed_by' => auth()->id()
        ]);
        
        return response()->json([
            'success' => true,
            'message' => 'Wallet frozen successfully'
        ]);
    }
    
    /**
     * Unfreeze a user's wallet
     */
    public function unfreezeWallet(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_type' => 'required|in:seller,customer',
            'user_id' => 'required|integer',
            'reason' => 'required|string|max:500'
        ]);
        
        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }
        
        $wallet = Wallet::where('user_type', $request->user_type)
            ->where($request->user_type == 'seller' ? 'seller_id' : 'customer_id', $request->user_id)
            ->first();
        
        if (!$wallet) {
            return response()->json(['success' => false, 'message' => 'Wallet not found'], 404);
        }
        
        if (!$wallet->is_frozen) {
            return response()->json(['success' => false, 'message' => 'Wallet is not frozen'], 400);
        }
        
        $wallet->is_frozen = false;
        $wallet->frozen_at = null;
        $wallet->freeze_reason = null;
        $wallet->save();
        
        // Create log entry
        \App\Models\WalletFrozenLog::create([
            'wallet_id' => $wallet->id,
            'action' => 'unfreeze',
            'reason' => $request->reason,
            'performed_by' => auth()->id()
        ]);
        
        return response()->json([
            'success' => true,
            'message' => 'Wallet unfrozen successfully'
        ]);
    }
    
    /**
     * Manually credit amount to wallet
     */
    public function manualCredit(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_type' => 'required|in:seller,customer',
            'user_id' => 'required|integer',
            'amount' => 'required|numeric|min:0.01',
            'remark' => 'required|string|max:500'
        ]);
        
        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }
        
        $wallet = Wallet::where('user_type', $request->user_type)
            ->where($request->user_type == 'seller' ? 'seller_id' : 'customer_id', $request->user_id)
            ->first();
        
        if (!$wallet) {
            return response()->json(['success' => false, 'message' => 'Wallet not found'], 404);
        }
        
        DB::beginTransaction();
        
        try {
            $oldBalance = $wallet->balance;
            $wallet->balance += $request->amount;
            $wallet->save();
            
            // Create transaction record
            WalletTransaction::create([
                'wallet_id' => $wallet->id,
                'seller_id' => $request->user_type == 'seller' ? $request->user_id : null,
                'customer_id' => $request->user_type == 'customer' ? $request->user_id : null,
                'user_type' => $request->user_type,
                'transaction_type' => 'manual_credit',
                'credit' => $request->amount,
                'debit' => 0,
                'balance_after' => $wallet->balance,
                'description' => $request->remark,
                'reference_id' => 'MANUAL_' . time(),
                'status' => 'completed'
            ]);
            
            DB::commit();
            
            return response()->json([
                'success' => true,
                'message' => 'Amount credited successfully',
                'new_balance' => $wallet->balance
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Failed to credit amount: ' . $e->getMessage()], 500);
        }
    }
    
    /**
     * Manually debit amount from wallet
     */
    public function manualDebit(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_type' => 'required|in:seller,customer',
            'user_id' => 'required|integer',
            'amount' => 'required|numeric|min:0.01',
            'remark' => 'required|string|max:500'
        ]);
        
        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }
        
        $wallet = Wallet::where('user_type', $request->user_type)
            ->where($request->user_type == 'seller' ? 'seller_id' : 'customer_id', $request->user_id)
            ->first();
        
        if (!$wallet) {
            return response()->json(['success' => false, 'message' => 'Wallet not found'], 404);
        }
        
        $availableBalance = $wallet->balance - $wallet->frozen_balance;
        if ($availableBalance < $request->amount) {
            return response()->json(['success' => false, 'message' => 'Insufficient balance'], 400);
        }
        
        DB::beginTransaction();
        
        try {
            $wallet->balance -= $request->amount;
            $wallet->save();
            
            // Create transaction record
            WalletTransaction::create([
                'wallet_id' => $wallet->id,
                'seller_id' => $request->user_type == 'seller' ? $request->user_id : null,
                'customer_id' => $request->user_type == 'customer' ? $request->user_id : null,
                'user_type' => $request->user_type,
                'transaction_type' => 'manual_debit',
                'credit' => 0,
                'debit' => $request->amount,
                'balance_after' => $wallet->balance,
                'description' => $request->remark,
                'reference_id' => 'MANUAL_' . time(),
                'status' => 'completed'
            ]);
            
            DB::commit();
            
            return response()->json([
                'success' => true,
                'message' => 'Amount debited successfully',
                'new_balance' => $wallet->balance
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Failed to debit amount'], 500);
        }
    }
    
    /**
     * Approve a withdrawal request
     */
    public function approveWithdrawal(Request $request, $id)
    {
        $withdrawal = WithdrawalRequest::findOrFail($id);
        
        if ($withdrawal->status !== 'pending') {
            return response()->json(['success' => false, 'message' => 'Withdrawal is not pending'], 400);
        }

        // ROI Tracking: Freeze payout on dispute
        if ($withdrawal->user_type === 'seller') {
            $hasActiveDispute = \App\Models\Grievance::where('seller_id', $withdrawal->seller_id)
                ->where('status', 'open')
                ->exists();
                
            if ($hasActiveDispute) {
                return response()->json(['success' => false, 'message' => 'Payout frozen: Seller has active disputes.'], 400);
            }
        }

        
        DB::beginTransaction();
        
        try {
            $wallet = $withdrawal->wallet;
            if ($wallet) {
                $wallet->frozen_balance -= $withdrawal->amount;
                $wallet->save();
            }
            
            $withdrawal->status = 'approved';
            $withdrawal->processed_by = auth()->id();
            $withdrawal->processed_at = now();
            $withdrawal->admin_remarks = $request->remarks;
            $withdrawal->save();
            
            // Create transaction record for the withdrawal
            if ($wallet) {
                WalletTransaction::create([
                    'wallet_id' => $wallet->id,
                    'seller_id' => $withdrawal->user_type == 'seller' ? $withdrawal->seller_id : null,
                    'customer_id' => $withdrawal->user_type == 'customer' ? $withdrawal->customer_id : null,
                    'user_type' => $withdrawal->user_type,
                    'transaction_type' => 'payout',
                    'credit' => 0,
                    'debit' => $withdrawal->amount,
                    'balance_after' => $wallet->balance,
                    'description' => 'Withdrawal approved - ' . $withdrawal->request_id,
                    'reference_id' => $withdrawal->request_id,
                    'status' => 'completed'
                ]);
            }
            
            DB::commit();
            
            return response()->json([
                'success' => true,
                'message' => 'Withdrawal approved successfully'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Failed to approve withdrawal: ' . $e->getMessage()], 500);
        }
    }
    
    /**
     * Reject a withdrawal request
     */
    public function rejectWithdrawal(Request $request, $id)
    {
        $withdrawal = WithdrawalRequest::findOrFail($id);
        
        if ($withdrawal->status !== 'pending') {
            return response()->json(['success' => false, 'message' => 'Withdrawal is not pending'], 400);
        }
        
        DB::beginTransaction();
        
        try {
            $wallet = $withdrawal->wallet;
            if ($wallet) {
                $wallet->frozen_balance -= $withdrawal->amount;
                $wallet->save();
            }
            
            $withdrawal->status = 'rejected';
            $withdrawal->processed_by = auth()->id();
            $withdrawal->processed_at = now();
            $withdrawal->admin_remarks = $request->remarks;
            $withdrawal->save();
            
            DB::commit();
            
            return response()->json([
                'success' => true,
                'message' => 'Withdrawal rejected successfully'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Failed to reject withdrawal'], 500);
        }
    }
    
    /**
     * Put a withdrawal request on hold
     */
    public function holdWithdrawal(Request $request, $id)
    {
        $withdrawal = WithdrawalRequest::findOrFail($id);
        
        if ($withdrawal->status !== 'pending') {
            return response()->json(['success' => false, 'message' => 'Withdrawal is not pending'], 400);
        }
        
        $withdrawal->status = 'hold';
        $withdrawal->admin_remarks = $request->remarks;
        $withdrawal->save();
        
        return response()->json([
            'success' => true,
            'message' => 'Withdrawal put on hold successfully'
        ]);
    }
    
    /**
     * Get dashboard summary for refresh (AJAX)
     */
    public function getDashboardSummary()
    {
        $donutData = $this->getDonutData();
        $trendData = $this->getTrendData();
        
        return response()->json([
            'success' => true,
            'data' => [
                'total_balance' => DB::table('orders')->whereNotIn('status', ['cancelled'])->sum('total_amount') - DB::table('wallet_transactions')->where('user_type', 'customer')->where('transaction_type', 'refund')->sum('credit'),
                'seller_balance' => Wallet::where('user_type', 'seller')->sum('balance'),
                'buyer_balance' => Wallet::where('user_type', 'customer')->sum('balance'),
                'frozen_amount' => Wallet::sum('frozen_balance'),
                'pending_withdrawals' => WithdrawalRequest::where('status', 'pending')->sum('amount'),
                'pending_withdrawals_count' => WithdrawalRequest::where('status', 'pending')->count(),
                'donut_data' => $donutData,
                'trend_data' => $trendData
            ]
        ]);
    }
    
    /**
     * Get donut chart data
     */
    private function getDonutData()
    {
        return [
            'seller' => (float) Wallet::where('user_type', 'seller')->sum('balance'),
            'buyer' => (float) Wallet::where('user_type', 'customer')->sum('balance'),
            'frozen' => (float) Wallet::sum('frozen_balance'),
            'pending' => (float) WithdrawalRequest::where('status', 'pending')->sum('amount')
        ];
    }
    
    /**
     * Get trend data for last 7 days
     */
    private function getTrendData()
    {
        $trendData = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $endOfDay = $date->copy()->endOfDay();
            
            $lastTransaction = WalletTransaction::where('created_at', '<=', $endOfDay)
                ->orderBy('created_at', 'desc')
                ->first();
            
            $balance = $lastTransaction ? (float) $lastTransaction->balance_after : 0;
            
            $trendData[] = [
                'date' => $date->format('d M'),
                'balance' => $balance
            ];
        }
        
        return $trendData;
    }
    
    /**
     * Get user name from transaction
     */
    private function getUserName($transaction)
    {
        if ($transaction->user_type == 'seller' && $transaction->seller_id) {
            $seller = Seller::find($transaction->seller_id);
            return $seller ? $seller->name : 'Unknown Seller';
        } else if ($transaction->customer_id) {
            $customer = Customer::find($transaction->customer_id);
            return $customer ? $customer->name : 'Unknown Customer';
        }
        return 'N/A';
    }
    
    /**
     * Get user name from withdrawal
     */
    private function getWithdrawalUserName($withdrawal)
    {
        if ($withdrawal->user_type == 'seller' && $withdrawal->seller_id) {
            $seller = Seller::find($withdrawal->seller_id);
            return $seller ? $seller->name : 'Unknown Seller';
        } else if ($withdrawal->customer_id) {
            $customer = Customer::find($withdrawal->customer_id);
            return $customer ? $customer->name : 'Unknown Customer';
        }
        return 'N/A';
    }
    
    /**
     * Get badge color for transaction type
     */
    private function getTransactionBadgeColor($type)
    {
        $colors = [
            'payout' => 'info',
            'refund' => 'warning',
            'commission' => 'purple',
            'add_money' => 'teal',
            'hold' => 'danger',
            'payment' => 'secondary',
            'manual_credit' => 'success',
            'manual_debit' => 'danger'
        ];
        
        return $colors[$type] ?? 'secondary';
    }
    
    /**
     * Get refund requests (AJAX) - DYNAMIC FROM DATABASE
     */
    public function getRefunds(Request $request)
    {
        // Build query for all return/refund requests across all buyers
        $query = DB::table('order_returns')
            ->join('orders', 'order_returns.order_id', '=', 'orders.id')
            ->leftJoin('customers', 'order_returns.customer_id', '=', 'customers.id')
            ->select(
                'order_returns.*',
                'orders.total_amount as amount',
                'customers.name as buyer_name',
                'customers.id as customer_id',
                'customers.email as customer_email'
            );
        
        // Apply filters
        if ($request->status && $request->status != '') {
            $query->where('order_returns.status', $request->status);
        }
        
        if ($request->buyer_id && $request->buyer_id != '') {
            $query->where('order_returns.customer_id', $request->buyer_id);
        }
        
        if ($request->order_id && $request->order_id != '') {
            $query->where('order_returns.order_id', 'LIKE', '%' . $request->order_id . '%');
        }
        
        if ($request->start_date && $request->end_date) {
            $query->whereBetween('order_returns.created_at', [
                $request->start_date . ' 00:00:00',
                $request->end_date . ' 23:59:59'
            ]);
        }
        
        // Get paginated results
        $refunds = $query->orderBy('order_returns.created_at', 'desc')
            ->paginate(10);
        
        // Transform data for frontend
        $refunds->getCollection()->transform(function ($refund) {
            
            // Map the order_returns status to admin refund status terminology
            $displayStatus = $refund->status;
            if ($refund->status == 'requested') $displayStatus = 'requested';
            else if ($refund->status == 'approved') $displayStatus = 'pending'; // Approved by seller, pending admin refund
            else if ($refund->status == 'rejected') $displayStatus = 'rejected';
            else if ($refund->status == 'refunded') $displayStatus = 'refunded';

            return [
                'id' => $refund->id, // This is order_returns.id
                'order_id' => $refund->order_id ?? '#ORD-' . str_pad($refund->id, 4, '0', STR_PAD_LEFT),
                'buyer_id' => '#CUST-' . str_pad($refund->customer_id, 3, '0', STR_PAD_LEFT),
                'buyer_name' => $refund->buyer_name ?? 'Unknown Customer',
                'amount' => (float) $refund->amount,
                'reason' => $refund->reason ?? 'Not specified',
                'reason_color' => $this->getRefundReasonColor($refund->reason),
                'status' => $displayStatus,
                'created_at' => $refund->created_at,
                'seller_pickup_required' => (bool)$refund->seller_pickup_required,
                'pickup_status' => $refund->pickup_status ?? 'pending',
                'hold_date' => $refund->hold_date
            ];
        });
        
        return response()->json([
            'success' => true,
            'data' => $refunds
        ]);
    }
    
    /**
     * Approve a refund request and credit back to buyer's wallet
     */
    public function approveRefund(Request $request, $id)
    {
        DB::beginTransaction();
        
        try {
            // Get return details from order_returns (which acts as our refund requests table)
            $orderReturn = DB::table('order_returns')->where('id', $id)->first();
            
            if (!$orderReturn) {
                return response()->json(['success' => false, 'message' => 'Refund request not found'], 404);
            }
            
            // In our system, the seller must approve it first. If status is 'approved', it's ready for admin refund.
            if ($orderReturn->status !== 'approved') {
                return response()->json(['success' => false, 'message' => 'Refund cannot be processed. Ensure the seller has approved the return first.'], 400);
            }
            
            $order = DB::table('orders')->where('id', $orderReturn->order_id)->first();
            if (!$order) {
                return response()->json(['success' => false, 'message' => 'Associated order not found'], 404);
            }

            // Get platform commission from commission_management table
            $commissionRules = DB::table('commission_management')->get();
            $sellerId = $order->seller_id;
            $appType = 'vshop';
            $platformComm = 30; // default fallback

            // Priority 1: Exact seller + app match
            $specificRule = $commissionRules->first(function ($rule) use ($sellerId, $appType) {
                return $rule->seller_id == $sellerId && strtolower($rule->app_type) == $appType;
            });

            if ($specificRule) {
                $platformComm = $specificRule->platform_commission;
            } else {
                $appRule = $commissionRules->filter(function ($rule) use ($appType) {
                    return is_null($rule->seller_id) && strtolower($rule->app_type) == $appType;
                })->first();
                if ($appRule) {
                    $platformComm = $appRule->platform_commission;
                } else {
                    $generalRule = $commissionRules->whereNull('seller_id')->first();
                    if ($generalRule) {
                        $platformComm = $generalRule->platform_commission;
                    }
                }
            }

            $platformCut = ($order->total_amount * $platformComm) / 100;
            
            // ROI Tracking: Partial Refund Check
            $isPartial = $request->input('is_partial', false) == 'true' || $request->input('is_partial') == 1;
            $partialAmount = (float) $request->input('partial_amount', 0);
            
            $refundAmount = $isPartial && $partialAmount > 0 ? $partialAmount : $order->total_amount;
            if ($refundAmount < 0) $refundAmount = 0;

            // 1. Update Customer's wallet_balance column directly
            $customer = \App\Models\Customer::find($orderReturn->customer_id);
            if ($customer) {
                $customer->wallet_balance = ($customer->wallet_balance ?? 0) + $refundAmount;
                $customer->save();
            }

            // Also maintain the Wallet & WalletTransaction for history
            $wallet = \App\Models\Wallet::firstOrCreate(
                ['user_type' => 'customer', 'customer_id' => $orderReturn->customer_id],
                ['balance' => 0]
            );
            
            $wallet->balance += $refundAmount;
            $wallet->save();
            
            // Create transaction record for buyer
            \App\Models\WalletTransaction::create([
                'wallet_id' => $wallet->id,
                'seller_id' => null,
                'customer_id' => $orderReturn->customer_id,
                'user_type' => 'customer',
                'transaction_type' => 'refund',
                'credit' => $refundAmount,
                'debit' => 0,
                'balance_after' => $customer ? $customer->wallet_balance : $wallet->balance,
                'description' => ($isPartial ? "Partial " : "") . "Refund for Order #{$orderReturn->order_id}",
                'reference_id' => 'REF_' . $orderReturn->id . '_' . time(),
                'status' => 'completed'
            ]);
            
            // ROI Tracking: Deduct from Admin/Platform Wallet instead of Seller
            $platformWallet = \App\Models\Wallet::firstOrCreate(
                ['user_type' => 'platform', 'seller_id' => null, 'customer_id' => null],
                ['balance' => 0]
            );
            
            $platformWallet->balance -= $refundAmount;
            $platformWallet->save();
            
            \App\Models\WalletTransaction::create([
                'wallet_id' => $platformWallet->id,
                'seller_id' => null,
                'customer_id' => null,
                'user_type' => 'platform',
                'transaction_type' => 'refund_deduction',
                'credit' => 0,
                'debit' => $refundAmount,
                'balance_after' => $platformWallet->balance,
                'description' => "Refund deduction for order #{$orderReturn->order_id}",
                'reference_id' => 'REV_' . $orderReturn->id . '_' . time(),
                'status' => 'completed'
            ]);
            
            // ROI Tracking: Reverse Referral Rewards
            $referralHistory = DB::table('referral_histories')->where('order_id', $order->id)->where('status', 'completed')->first();
            if ($referralHistory) {
                $referrerWallet = \App\Models\Wallet::where('user_type', 'seller')->where('seller_id', $referralHistory->referrer_id)->first();
                if ($referrerWallet) {
                    $referrerWallet->balance -= $referralHistory->amount;
                    $referrerWallet->save();
                    
                    \App\Models\WalletTransaction::create([
                        'wallet_id' => $referrerWallet->id,
                        'seller_id' => $referralHistory->referrer_id,
                        'user_type' => 'seller',
                        'transaction_type' => 'referral_reversal',
                        'credit' => 0,
                        'debit' => $referralHistory->amount,
                        'balance_after' => $referrerWallet->balance,
                        'description' => "Referral reward reversed for refunded order #{$orderReturn->order_id}",
                        'reference_id' => 'REF_REV_' . $orderReturn->id . '_' . time(),
                        'status' => 'completed'
                    ]);
                    
                    DB::table('referral_histories')->where('id', $referralHistory->id)->update(['status' => 'reversed']);
                }
            }
            
            // ROI Tracking: Reverse Fake Lead Charge
            $isFakeLead = $request->input('is_fake_lead', false) == 'true' || $request->input('is_fake_lead') == 1;
            if ($isFakeLead) {
                // Assuming lead fee was deducted from SellerFinance or WalletTransactions
                $leadTransaction = \App\Models\WalletTransaction::where('seller_id', $sellerId)
                    ->where('transaction_type', 'lead_fee')
                    ->orderBy('id', 'desc')->first();
                    
                if ($leadTransaction) {
                    $sellerWallet->balance += $leadTransaction->debit;
                    $sellerWallet->save();
                    
                    \App\Models\WalletTransaction::create([
                        'wallet_id' => $sellerWallet->id,
                        'seller_id' => $sellerId,
                        'user_type' => 'seller',
                        'transaction_type' => 'lead_fee_reversal',
                        'credit' => $leadTransaction->debit,
                        'debit' => 0,
                        'balance_after' => $sellerWallet->balance,
                        'description' => "Fake lead fee reversed for refunded order #{$orderReturn->order_id}",
                        'reference_id' => 'LEAD_REV_' . $orderReturn->id . '_' . time(),
                        'status' => 'completed'
                    ]);
                }
            }
            
            // 3. Update the order return status to 'refunded'/ Update order and order return status
            \App\Models\Order::where('id', $order->id)->update([
                'refund_status' => 'completed',
                'status' => 'returned',
                'refund_completed_at' => now()
            ]);
            
            DB::table('order_returns')->where('id', $id)->update([
                'status' => 'refunded'
            ]);
            
            DB::commit();
            
            return response()->json([
                'success' => true,
                'message' => 'Refund approved and amount credited to buyer\'s wallet'
            ]);
            
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to approve refund: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Get refund reason color for badge
     */
    private function getRefundReasonColor($reason)
    {
        $colors = [
            'Defective' => 'danger',
            'Defective Product' => 'danger',
            'Damaged' => 'danger',
            'Wrong Item' => 'warning',
            'Order Cancelled' => 'info',
            'Cancelled' => 'info',
            'Shipping Delay' => 'info',
            'Not Delivered' => 'warning',
            'Quality Issue' => 'danger'
        ];
        
        foreach ($colors as $key => $color) {
            if (stripos($reason, $key) !== false) {
                return $color;
            }
        }
        
        return 'secondary';
    }
    
    /**
     * Get freeze/unfreeze history logs (AJAX)
     */
    public function getHistoryLogs(Request $request)
    {
        $logs = \App\Models\WalletFrozenLog::with(['wallet', 'performer'])
            ->orderBy('created_at', 'desc')
            ->paginate(15);
        
        foreach ($logs as $log) {
            // Get user name
            if ($log->wallet) {
                if ($log->wallet->user_type == 'seller' && $log->wallet->seller_id) {
                    $seller = Seller::find($log->wallet->seller_id);
                    $log->user_name = $seller ? $seller->name : 'Unknown Seller';
                } else if ($log->wallet->customer_id) {
                    $customer = Customer::find($log->wallet->customer_id);
                    $log->user_name = $customer ? $customer->name : 'Unknown Customer';
                }
                $log->user_type = $log->wallet->user_type;
            } else {
                $log->user_name = 'N/A';
                $log->user_type = 'N/A';
            }
            
            $log->performed_by_name = $log->performer ? $log->performer->name : 'System';
        }
        
        return response()->json([
            'success' => true,
            'data' => $logs
        ]);
    }
    
    
    /**
 * Get team members for salary disbursement
 */
public function getTeamMembers()
{
    // Fetch from your teams table
    // Adjust the model and fields according to your database structure
    $teams = \App\Models\Team::select('id', 'name', 'role', 'email')
        ->where('status', 'active')
        ->orderBy('name')
        ->get();
    
    if ($teams->isEmpty()) {
        // Return demo data if no teams exist
        return response()->json([
            'success' => true,
            'data' => [
                ['id' => 1, 'name' => 'John Doe', 'role' => 'Admin', 'email' => 'john@example.com'],
                ['id' => 2, 'name' => 'Jane Smith', 'role' => 'Manager', 'email' => 'jane@example.com'],
                ['id' => 3, 'name' => 'Mike Johnson', 'role' => 'Accountant', 'email' => 'mike@example.com'],
                ['id' => 4, 'name' => 'Sarah Wilson', 'role' => 'Support', 'email' => 'sarah@example.com'],
                ['id' => 5, 'name' => 'David Brown', 'role' => 'Marketing', 'email' => 'david@example.com'],
            ]
        ]);
    }
    
    return response()->json([
        'success' => true,
        'data' => $teams
    ]);
}


public function getOrderDetails(Request $request)
{
    $orderId = $request->input('order_id');
    $refundId = $request->input('refund_id');
    
    // Fetch order from orders table
    $order = \App\Models\Order::with(['items.product', 'customer'])->where('id', $orderId)->first();
    
    if (!$order) {
        return response()->json(['success' => false, 'message' => 'Order not found']);
    }
    
    // Fetch refund/return details from order_returns
    $refund = \App\Models\OrderReturn::where('id', $refundId)->orWhere('order_id', $orderId)->first();
    
    // Fetch order items with product details
    $items = [];
    foreach ($order->items as $item) {
        $productName = $item->product ? $item->product->name : 'Unknown Product';
        $productImage = '';
        if ($item->product && $item->product->thumbnail) {
            $thumb = $item->product->thumbnail;
            if (strpos($thumb, 'http') === 0) {
                $productImage = $thumb;
            } else {
                $productImage = url('/public/' . ltrim($thumb, '/'));
            }
        } else {
            $productImage = 'https://placehold.co/60x60?text=No+Image';
        }
        $sku = $item->product ? ($item->product->sku ?? 'N/A') : 'N/A';
        
        $items[] = [
            'name' => $productName,
            'sku' => $sku,
            'quantity' => $item->quantity,
            'price' => $item->price,
            'image' => $productImage
        ];
    }
    
    // Get platform commission from commission_management table
    $commissionRules = DB::table('commission_management')->get();
    $sellerId = $order->seller_id;
    $appType = 'vshop';
    $platformComm = 70; // default fallback
    $sellerComm = 20;
    $referrerComm = 10;

    // Priority 1: Exact seller + app match
    $specificRule = $commissionRules->first(function ($rule) use ($sellerId, $appType) {
        return $rule->seller_id == $sellerId && strtolower($rule->app_type) == $appType;
    });

    if ($specificRule) {
        $sellerComm = $specificRule->seller_commission;
        $referrerComm = $specificRule->referrer_commission;
        $platformComm = $specificRule->platform_commission;
    } else {
        // Priority 2: App type match (no specific seller)
        $appRule = $commissionRules->filter(function ($rule) use ($appType) {
            return is_null($rule->seller_id) && strtolower($rule->app_type) == $appType;
        })->first();
        if ($appRule) {
            $sellerComm = $appRule->seller_commission;
            $referrerComm = $appRule->referrer_commission;
            $platformComm = $appRule->platform_commission;
        } else {
            // Priority 3: General fallback
            $generalRule = $commissionRules->whereNull('seller_id')->first();
            if ($generalRule) {
                $sellerComm = $generalRule->seller_commission;
                $referrerComm = $generalRule->referrer_commission;
                $platformComm = $generalRule->platform_commission;
            }
        }
    }

    $platformCut = ($order->total_amount * $platformComm) / 100;
    $refundAmount = $order->total_amount - $platformCut;
    if ($refundAmount < 0) $refundAmount = 0;

    // Customer details from the customers table
    $customer = $order->customer;
    
    // Delivery address
    $address = 'N/A';
    if ($order->delivery_address_id) {
        $addr = DB::table('addresses')->where('id', $order->delivery_address_id)->first();
        if ($addr) {
            $address = trim(($addr->address_line_1 ?? '') . ', ' . ($addr->city ?? '') . ', ' . ($addr->state ?? '') . ' - ' . ($addr->pincode ?? ''));
        }
    }
    if ($address === 'N/A' && $customer) {
        $address = $customer->address ?? 'N/A';
    }

    return response()->json([
        'success' => true,
        'data' => [
            'order_id' => $order->id,
            'order_date' => $order->created_at,
            'total_amount' => $order->total_amount,
            'sub_total' => $order->sub_total,
            'gst_amount' => $order->gst_amount ?? 0,
            'courier_charge' => $order->courier_charge ?? 0,
            'discount_amount' => $order->discount_amount ?? 0,
            'payment_method' => $order->payment_method,
            'payment_status' => $order->payment_status,
            'order_status' => $order->status,
            'customer' => [
                'name' => $customer->name ?? 'N/A',
                'email' => $customer->email ?? 'N/A',
                'phone' => $customer->phone ?? 'N/A',
                'address' => $address
            ],
            'items' => $items,
            'commission' => [
                'platform_pct' => $platformComm,
                'seller_pct' => $sellerComm,
                'referrer_pct' => $referrerComm,
                'platform_cut' => $platformCut,
            ],
            'refund_id' => $refund->id ?? $refundId,
            'refund_request_date' => $refund->created_at ?? null,
            'refund_reason' => $refund->reason ?? 'N/A',
            'refund_amount' => $refundAmount,
            'refund_status' => $refund->status ?? 'pending',
            'refund_attachment' => $refund->attachment ?? null
        ]
    ]);
}

public function getStaffMembers(Request $request)
{
    try {
        $staffMembers = DB::table('admin_staffs')
            ->select('id', 'first_name', 'last_name', 'email', 'phone', 'employee_type', 'status', 'basic_salary')
            ->where('status', 'Active')
            ->orderBy('first_name', 'asc')
            ->get();
        
        return response()->json([
            'success' => true,
            'data' => $staffMembers
        ]);
        
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => $e->getMessage()
        ]);
    }
}

public function disburseSalary(Request $request)
{
    try {
        $selectedMembers = $request->selected_members;
        $perMemberAmount = $request->per_member_amount;
        $totalAmount = $request->total_amount;
        $month = $request->month;
        $paymentType = $request->payment_type;
        $remarks = $request->remarks;
        
        if (empty($selectedMembers) || count($selectedMembers) == 0) {
            return response()->json(['success' => false, 'message' => 'Please select at least one team member']);
        }
        
        $disbursedCount = 0;
        $errors = [];
        
        foreach ($selectedMembers as $memberId) {
            try {
                // Get current staff data
                $staff = DB::table('admin_staffs')->where('id', $memberId)->first();
                
                // Update the admin_staffs table with salary information
                DB::table('admin_staffs')
                    ->where('id', $memberId)
                    ->update([
                        'last_salary_date' => now()->format('Y-m-d'),
                        'next_salary_date' => now()->addMonth()->format('Y-m-d'),
                        'last_salary_amount' => $perMemberAmount,
                        'salary_disbursed_month' => $month,
                        'payment_status' => 'Paid',
                        'updated_at' => now()
                    ]);
                
                $disbursedCount++;
            } catch (\Exception $e) {
                $errors[] = "Member ID $memberId: " . $e->getMessage();
            }
        }
        
        return response()->json([
            'success' => true,
            'message' => "Salary disbursed to $disbursedCount member(s) successfully",
            'disbursed_count' => $disbursedCount,
            'total_amount' => $totalAmount,
            'errors' => $errors
        ]);
        
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Error: ' . $e->getMessage()
        ]);
    }
}




public function selfWithdraw(Request $request)
{
    try {
        $amount = $request->amount;
        $remarks = $request->remarks;
        $adminId = auth()->id();
        
        if (!$amount || $amount < 500) {
            return response()->json(['success' => false, 'message' => 'Amount must be at least ₹500']);
        }
        
        // Get admin wallet
        $adminWallet = Wallet::where('user_id', $adminId)->where('user_type', 'admin')->first();
        
        if (!$adminWallet) {
            $adminWallet = Wallet::create([
                'user_id' => $adminId,
                'user_type' => 'admin',
                'balance' => 0,
                'frozen_balance' => 0,
                'is_frozen' => 0
            ]);
        }
        
        // Check if wallet is frozen
        if ($adminWallet->is_frozen == 1) {
            return response()->json(['success' => false, 'message' => 'Your wallet is frozen. Cannot withdraw.']);
        }
        
        // Check if bank details exist
        if (!$adminWallet->bank_account_number) {
            return response()->json(['success' => false, 'message' => 'Please add bank details first.']);
        }
        
        // Check sufficient balance
        if ($adminWallet->balance < $amount) {
            return response()->json(['success' => false, 'message' => 'Insufficient balance. Available: ₹' . number_format($adminWallet->balance, 2)]);
        }
        
        // Deduct from wallet
        $adminWallet->balance -= $amount;
        $adminWallet->save();
        
        // Create withdrawal request in withdrawals table
        $withdrawalId = DB::table('withdrawals')->insertGetId([
            'user_id' => $adminId,
            'user_type' => 'admin',
            'amount' => $amount,
            'bank_name' => $adminWallet->bank_name,
            'bank_account_number' => $adminWallet->bank_account_number,
            'ifsc_code' => $adminWallet->bank_ifsc_code,
            'account_holder_name' => $adminWallet->bank_account_holder,
            'status' => 'pending',
            'remarks' => $remarks,
            'request_id' => 'WD-' . strtoupper(uniqid()),
            'created_at' => now(),
            'updated_at' => now()
        ]);
        
        // Create transaction record
        DB::table('wallet_transactions')->insert([
            'user_id' => $adminId,
            'user_type' => 'admin',
            'transaction_type' => 'withdrawal',
            'debit' => $amount,
            'balance_after' => $adminWallet->balance,
            'description' => 'Self withdrawal request - ' . ($remarks ?: 'Amount withdrawn to bank account'),
            'reference_id' => 'WD-' . strtoupper(uniqid()),
            'created_at' => now(),
            'updated_at' => now()
        ]);
        
        return response()->json([
            'success' => true,
            'message' => 'Withdrawal request submitted successfully',
            'new_balance' => $adminWallet->balance
        ]);
        
    } catch (\Exception $e) {
        return response()->json(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
    }
}

public function updateBankDetails(Request $request)
{
    try {
        $adminId = auth()->id();
        
        $adminWallet = Wallet::where('user_id', $adminId)->where('user_type', 'admin')->first();
        
        if (!$adminWallet) {
            $adminWallet = Wallet::create([
                'user_id' => $adminId,
                'user_type' => 'admin',
                'balance' => 0,
                'frozen_balance' => 0,
                'is_frozen' => 0
            ]);
        }
        
        $adminWallet->bank_account_holder = $request->account_holder_name;
        $adminWallet->bank_name = $request->bank_name;
        $adminWallet->bank_account_number = $request->account_number;
        $adminWallet->bank_ifsc_code = $request->ifsc_code;
        $adminWallet->save();
        
        return response()->json([
            'success' => true,
            'message' => 'Bank details updated successfully',
            'data' => [
                'bank_name' => $adminWallet->bank_name,
                'account_number' => '****' . substr($adminWallet->bank_account_number, -4),
                'account_holder' => $adminWallet->bank_account_holder
            ]
        ]);
        
    } catch (\Exception $e) {
        return response()->json(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
    }
}

public function getBankDetails(Request $request)
{
    try {
        $adminId = auth()->id();
        $adminWallet = Wallet::where('user_id', $adminId)->where('user_type', 'admin')->first();
        
        if ($adminWallet && $adminWallet->bank_account_number) {
            return response()->json([
                'success' => true,
                'has_bank_details' => true,
                'data' => [
                    'bank_name' => $adminWallet->bank_name,
                    'account_number' => '****' . substr($adminWallet->bank_account_number, -4),
                    'account_holder' => $adminWallet->bank_account_holder,
                    'ifsc_code' => $adminWallet->bank_ifsc_code
                ]
            ]);
        } else {
            return response()->json([
                'success' => true,
                'has_bank_details' => false,
                'message' => 'No bank details found'
            ]);
        }
        
    } catch (\Exception $e) {
        return response()->json(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
    }}
}
