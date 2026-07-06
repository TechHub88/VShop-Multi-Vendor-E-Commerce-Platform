<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Models\GlobalSetting;
use Carbon\Carbon;

class RefundSlaCheck extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'vshop:refund-sla-check';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Automatically process refunds if SLA is breached (Seller has not replied within SLA days)';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // Get SLA days from global settings or default to 3
        $slaDaysSetting = DB::table('global_settings')->where('key', 'refund_sla_days')->first();
        $slaDays = $slaDaysSetting ? (int)$slaDaysSetting->value : 3;

        // Auto mode toggle check (optional, but good practice if admin disables auto-refund)
        $autoRefundMode = DB::table('global_settings')->where('key', 'refund_auto_mode')->first();
        $isAutoRefundEnabled = $autoRefundMode ? (bool)$autoRefundMode->value : true;

        if (!$isAutoRefundEnabled) {
            $this->info("Auto refund mode is disabled. Skipping SLA check.");
            return;
        }

        $dueDate = Carbon::now()->subDays($slaDays);

        // Fetch pending return requests that are older than SLA days
        $pendingReturns = DB::table('order_returns')
            ->where('status', 'requested')
            ->where('created_at', '<=', $dueDate)
            ->get();

        if ($pendingReturns->isEmpty()) {
            $this->info("No SLA breached refund requests found.");
            return;
        }

        foreach ($pendingReturns as $returnRequest) {
            // Auto approve the return since seller didn't reply
            DB::table('order_returns')
                ->where('id', $returnRequest->id)
                ->update(['status' => 'approved', 'updated_at' => Carbon::now()]);

            // Assuming a notification to seller is needed
            // This is just a basic notification placeholder.
            DB::table('notifications')->insert([
                'user_id' => $returnRequest->seller_id ?? null,
                'type' => 'seller',
                'title' => 'Auto Refund Initiated',
                'message' => 'Return request for Order #' . $returnRequest->order_id . ' was auto-approved due to SLA breach (' . $slaDays . ' days without reply).',
                'is_read' => 0,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ]);

            $this->info("Auto approved return request ID: " . $returnRequest->id . " due to SLA.");
        }

        $this->info("Refund SLA check completed successfully.");
    }
}

