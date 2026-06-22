<?php

namespace App\Http\Controllers\Admin;

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
        $totalBalance = Wallet::sum('balance');
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
        return view('admin.wallet.index', compact(
            'totalBalance', 'sellerBalance', 'buyerBalance', 'frozenAmount',
            'pendingWithdrawals', 'pendingWithdrawalsCount', 'donutData',
            'trendData', 'transactions', 'pendingWithdrawalsList','buyers'
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
                'total_balance' => Wallet::sum('balance'),
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
        // Build query for refunds
        $query = DB::table('refunds')
            ->leftJoin('customers', 'refunds.customer_id', '=', 'customers.id')
            ->select(
                'refunds.*',
                'customers.name as buyer_name',
                'customers.id as customer_id',
                'customers.email as customer_email'
            );
        
        // Apply filters
        if ($request->status && $request->status != '') {
            $query->where('refunds.status', $request->status);
        }
        
        if ($request->buyer_id && $request->buyer_id != '') {
            $query->where('refunds.customer_id', $request->buyer_id);
        }
        
        if ($request->order_id && $request->order_id != '') {
            $query->where('refunds.order_id', 'LIKE', '%' . $request->order_id . '%');
        }
        
        if ($request->start_date && $request->end_date) {
            $query->whereBetween('refunds.created_at', [
                $request->start_date . ' 00:00:00',
                $request->end_date . ' 23:59:59'
            ]);
        }
        
        // Get paginated results
        $refunds = $query->orderBy('refunds.created_at', 'desc')
            ->paginate(10);
        
        // Transform data for frontend
        $refunds->getCollection()->transform(function ($refund) {
            return [
                'id' => $refund->id,
                'order_id' => $refund->order_id ?? '#ORD-' . str_pad($refund->id, 4, '0', STR_PAD_LEFT),
                'buyer_id' => '#CUST-' . str_pad($refund->customer_id, 3, '0', STR_PAD_LEFT),
                'buyer_name' => $refund->buyer_name ?? 'Unknown Customer',
                'amount' => (float) $refund->amount,
                'reason' => $refund->reason ?? 'Not specified',
                'reason_color' => $this->getRefundReasonColor($refund->reason),
                'status' => $refund->status,
                'created_at' => $refund->created_at
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
            // Get refund details
            $refund = DB::table('refunds')->where('id', $id)->first();
            
            if (!$refund) {
                return response()->json(['success' => false, 'message' => 'Refund not found'], 404);
            }
            
            if ($refund->status !== 'pending') {
                return response()->json(['success' => false, 'message' => 'Refund is not pending'], 400);
            }
            
            // Find buyer's wallet
            $wallet = Wallet::where('user_type', 'customer')
                ->where('customer_id', $refund->customer_id)
                ->first();
            
            if (!$wallet) {
                return response()->json(['success' => false, 'message' => 'Buyer wallet not found'], 404);
            }
            
            // Credit amount to buyer's wallet
            $oldBalance = $wallet->balance;
            $wallet->balance += $refund->amount;
            $wallet->save();
            
            // Create transaction record
            WalletTransaction::create([
                'wallet_id' => $wallet->id,
                'seller_id' => null,
                'customer_id' => $refund->customer_id,
                'user_type' => 'customer',
                'transaction_type' => 'refund',
                'credit' => $refund->amount,
                'debit' => 0,
                'balance_after' => $wallet->balance,
                'description' => 'Refund for order - ' . ($refund->order_id ?? 'Order #' . $refund->id),
                'reference_id' => 'REF_' . $refund->id . '_' . time(),
                'status' => 'completed'
            ]);
            
            // Update refund status
            DB::table('refunds')->where('id', $id)->update([
                'status' => 'refunded',
                'processed_at' => now(),
                'processed_by' => auth()->id(),
                'updated_at' => now()
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
    
    // Fetch order from your orders table
    $order = \App\Models\Order::where('order_id', $orderId)->orWhere('id', $orderId)->first();
    
    if (!$order) {
        return response()->json(['success' => false, 'message' => 'Order not found']);
    }
    
    // Fetch refund details
    $refund = \App\Models\Refund::where('id', $refundId)->orWhere('order_id', $orderId)->first();
    
    // Fetch order items with product images
    $items = [];
    foreach ($order->items as $item) {
        $items[] = [
            'name' => $item->product_name,
            'sku' => $item->sku ?? 'N/A',
            'quantity' => $item->quantity,
            'price' => $item->price,
            'image' => $item->product->image ?? 'https://placehold.co/60x60'
        ];
    }
    
    return response()->json([
        'success' => true,
        'data' => [
            'order_id' => $order->order_id ?? '#ORD-'.$order->id,
            'order_date' => $order->created_at,
            'total_amount' => $order->total_amount,
            'payment_method' => $order->payment_method,
            'order_status' => $order->status,
            'shipping_charges' => $order->shipping_charges ?? 0,
            'customer' => [
                'name' => $order->customer->name ?? 'N/A',
                'email' => $order->customer->email ?? 'N/A',
                'phone' => $order->customer->phone ?? 'N/A',
                'address' => $order->shipping_address ?? $order->customer->address ?? 'N/A'
            ],
            'items' => $items,
            'refund_id' => $refund->id ?? $refundId,
            'refund_request_date' => $refund->created_at ?? null,
            'refund_reason' => $refund->reason ?? 'N/A',
            'refund_amount' => $refund->amount ?? null,
            'refund_status' => $refund->status ?? 'pending'
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