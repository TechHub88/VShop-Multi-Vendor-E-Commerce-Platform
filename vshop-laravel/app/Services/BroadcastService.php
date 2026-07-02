<?php

namespace App\Services;

use App\Models\BroadcastMessage;
use App\Models\User;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class BroadcastService
{
    /**
     * Send broadcast message based on channel
     */
    public function sendBroadcast(BroadcastMessage $broadcast): bool
    {
        try {
            $recipients = $this->getRecipients($broadcast->audience);
            
            if ($recipients->isEmpty()) {
                Log::warning("No recipients found for broadcast: {$broadcast->id}");
                return false;
            }

            $broadcast->update([
                'total_recipients' => $recipients->count(),
                'recipient_ids' => $recipients->pluck('id')->toArray(),
            ]);

            $deliveredCount = 0;

            foreach ($recipients as $recipient) {
                $success = false;

                switch ($broadcast->channel) {
                    case 'email':
                        $success = $this->sendEmail($recipient, $broadcast);
                        break;
                    case 'sms':
                        $success = $this->sendSMS($recipient, $broadcast);
                        break;
                    case 'whatsapp':
                        $success = $this->sendWhatsApp($recipient, $broadcast);
                        break;
                }

                if ($success) {
                    $deliveredCount++;
                }
            }

            $broadcast->update([
                'delivered_count' => $deliveredCount,
                'status' => $deliveredCount > 0 ? 'sent' : 'failed',
                'sent_at' => now(),
            ]);

            return $deliveredCount > 0;

        } catch (\Exception $e) {
            Log::error("Broadcast failed: " . $e->getMessage());
            $broadcast->update(['status' => 'failed']);
            return false;
        }
    }

    /**
     * Get recipients based on audience type
     */
    private function getRecipients(string $audience)
    {
        $query = User::query();

        switch ($audience) {
            case 'all_sellers':
                $query->whereHas('roles', function ($q) {
                    $q->where('name', 'seller');
                });
                break;
            case 'active_sellers':
                $query->whereHas('roles', function ($q) {
                    $q->where('name', 'seller');
                })->where('status', 'active');
                break;
            case 'all_admins':
                $query->whereHas('roles', function ($q) {
                    $q->whereIn('name', ['admin', 'admin_staff']);
                });
                break;
            case 'specific_users':
                // This would need to be handled differently with specific user IDs
                return collect();
        }

        return $query->get();
    }

    /**
     * Send email broadcast
     */
    private function sendEmail(User $recipient, BroadcastMessage $broadcast): bool
    {
        try {
            Mail::raw($broadcast->message, function ($message) use ($recipient, $broadcast) {
                $message->to($recipient->email)
                    ->subject('Broadcast Message');
            });

            return true;
        } catch (\Exception $e) {
            Log::error("Email send failed to {$recipient->email}: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Send SMS broadcast
     */
    private function sendSMS(User $recipient, BroadcastMessage $broadcast): bool
    {
        try {
            // Integration with SMS service like Twilio, Nexmo, etc.
            // This is a placeholder - actual implementation depends on your SMS provider
            
            // Example with Twilio (requires twilio/sdk package):
            // $twilio = new Client(config('services.twilio.sid'), config('services.twilio.token'));
            // $twilio->messages->create(
            //     $recipient->phone,
            //     ['from' => config('services.twilio.from'), 'body' => $broadcast->message]
            // );

            Log::info("SMS would be sent to {$recipient->phone}: {$broadcast->message}");
            return true; // Placeholder return
        } catch (\Exception $e) {
            Log::error("SMS send failed to {$recipient->phone}: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Send WhatsApp broadcast
     */
    private function sendWhatsApp(User $recipient, BroadcastMessage $broadcast): bool
    {
        try {
            // Integration with WhatsApp Business API
            // This is a placeholder - actual implementation depends on your WhatsApp provider
            
            // Example with Twilio WhatsApp:
            // $twilio = new Client(config('services.twilio.sid'), config('services.twilio.token'));
            // $twilio->messages->create(
            //     "whatsapp:{$recipient->phone}",
            //     ['from' => config('services.twilio.whatsapp_from'), 'body' => $broadcast->message]
            // );

            Log::info("WhatsApp would be sent to {$recipient->phone}: {$broadcast->message}");
            return true; // Placeholder return
        } catch (\Exception $e) {
            Log::error("WhatsApp send failed to {$recipient->phone}: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Get recipient count for audience
     */
    public function getRecipientCount(string $audience): int
    {
        return $this->getRecipients($audience)->count();
    }
}
