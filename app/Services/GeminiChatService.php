<?php

namespace App\Services;

use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GeminiChatService
{
    protected $apiKey;

    public function __construct()
    {
        $this->apiKey = env('OPENROUTER_API_KEY');
    }

    /**
     * Get chatbot response from Gemini using real-time database context.
     *
     * @param string $userMessage
     * @param int|null $userId
     * @return string
     */
    public function getChatbotResponse(string $userMessage, ?int $userId = null): string
    {
        // 1. Fetch real-time database context
        $context = $this->buildDatabaseContext($userMessage, $userId);

        // 2. Fallback if API key is not configured
        if (empty($this->apiKey)) {
            return $this->getMockResponse($userMessage, $context);
        }

        try {
            $systemInstruction = "You are Ashmita, the helpful and professional real-time AI customer support assistant for Vridhee Shop.\n";
            $systemInstruction .= "Answer the buyer's query as accurately as possible using the provided store context.\n";
            $systemInstruction .= "If you cannot find the information in the context, politely state that you are looking into it and a human support admin will connect shortly.\n";
            $systemInstruction .= "Keep answers concise, direct, helpful, and friendly (max 2-3 sentences where possible).";

            $payload = [
                'model' => 'google/gemini-2.5-flash',
                'messages' => [
                    [
                        'role' => 'system',
                        'content' => $systemInstruction
                    ],
                    [
                        'role' => 'user',
                        'content' => "STORE DATABASE CONTEXT:\n" . json_encode($context, JSON_PRETTY_PRINT) . "\n\nBUYER MESSAGE: " . $userMessage
                    ]
                ]
            ];

            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->apiKey,
                'Content-Type' => 'application/json',
                'HTTP-Referer' => 'https://vshop.zelofx.com',
                'X-Title' => 'Vridhee Shop'
            ])->post("https://openrouter.ai/api/v1/chat/completions", $payload);

            if ($response->successful()) {
                $data = $response->json();
                if (isset($data['choices'][0]['message']['content'])) {
                    return trim($data['choices'][0]['message']['content']);
                }
            }

            Log::error("OpenRouter API Error response: " . $response->body());
            return "Thank you for reaching out! I'm having trouble processing that right now, but a human support admin will connect with you shortly.";

        } catch (\Exception $e) {
            Log::error("OpenRouter service exception: " . $e->getMessage());
            return "Thank you! I am reviewing your query. A support admin is being notified to connect with you.";
        }
    }

    /**
     * Gather database context for orders, products, and user profiles.
     */
    protected function buildDatabaseContext(string $userMessage, ?int $userId): array
    {
        $context = [
            'store_info' => [
                'name' => 'Vridhee Shop (VShop)',
                'website' => 'vshop.zelofx.com',
                'return_policy' => 'Returns are accepted within 7 days of delivery for unused products in original packaging.',
                'shipping_policy' => 'Free shipping on orders above ₹999. Standard delivery takes 3-5 business days.',
                'support_email' => 'support@vridhee.com'
            ],
            'buyer_info' => null,
            'orders' => [],
            'matching_products' => []
        ];

        // Fetch User and Order History if logged in
        if ($userId) {
            $user = User::find($userId);
            if ($user) {
                $context['buyer_info'] = [
                    'name' => $user->name,
                    'email' => $user->email,
                    'phone' => $user->phone ?? 'Not provided'
                ];

                // Fetch recent 3 orders
                $orders = Order::where('user_id', $userId)
                    ->with('items.product')
                    ->latest()
                    ->take(3)
                    ->get();

                foreach ($orders as $order) {
                    $items = [];
                    foreach ($order->items as $item) {
                        $items[] = [
                            'product_name' => $item->product->name ?? 'Unknown Product',
                            'quantity' => $item->qty,
                            'price' => $item->price
                        ];
                    }

                    $context['orders'][] = [
                        'order_id' => $order->id,
                        'order_number' => $order->order_number ?? '#' . $order->id,
                        'status' => $order->status ?? 'pending',
                        'total' => $order->total ?? $order->grand_total,
                        'created_at' => $order->created_at ? $order->created_at->toDateTimeString() : 'N/A',
                        'items' => $items
                    ];
                }
            }
        }

        // Search products if the message mentions product/stock/price queries
        $keywords = array_filter(explode(' ', strtolower(preg_replace('/[^a-z0-9 ]/i', '', $userMessage))), function($val) {
            return strlen($val) > 3; // only keywords longer than 3 chars
        });

        if (!empty($keywords)) {
            $productQuery = Product::query();
            foreach ($keywords as $word) {
                $productQuery->orWhere('name', 'like', "%{$word}%");
            }

            $products = $productQuery->where('status', 'active')
                ->take(3)
                ->get(['id', 'name', 'price', 'quantity', 'description']);

            foreach ($products as $p) {
                $context['matching_products'][] = [
                    'name' => $p->name,
                    'price' => $p->price,
                    'stock_status' => $p->quantity > 0 ? 'In Stock (' . $p->quantity . ' available)' : 'Out of Stock',
                    'description' => substr(strip_tags($p->description), 0, 100) . '...'
                ];
            }
        }

        return $context;
    }

    /**
     * Fallback mock handler when Gemini API key is missing.
     */
    protected function getMockResponse(string $userMessage, array $context): string
    {
        $msg = strtolower($userMessage);

        // Try order check
        if (str_contains($msg, 'order') || str_contains($msg, 'delivery') || str_contains($msg, 'track')) {
            if (!empty($context['orders'])) {
                $lastOrder = $context['orders'][0];
                return "Hi " . ($context['buyer_info']['name'] ?? 'there') . "! I found your recent order " . $lastOrder['order_number'] . " placed on " . explode(' ', $lastOrder['created_at'])[0] . ". Its status is currently '" . strtoupper($lastOrder['status']) . "' with a total of ₹" . number_of_format_or_raw($lastOrder['total']) . ". Let me know if you need more details!";
            }
            return "I couldn't find any recent orders associated with your account. Please share your Order Number, and a support agent will help you track it shortly!";
        }

        // Try product check
        if (!empty($context['matching_products'])) {
            $p = $context['matching_products'][0];
            return "Yes! We have '" . $p['name'] . "' available for ₹" . $p['price'] . ". It is currently " . $p['stock_status'] . ". Would you like help adding it to your cart?";
        }

        // Default replies
        if (str_contains($msg, 'hi') || str_contains($msg, 'hello') || str_contains($msg, 'hey')) {
            return "Hello! I am Ashmita, your Vridhee support assistant. How can I help you today? You can ask me about your orders, products, or store policies!";
        }

        if (str_contains($msg, 'return') || str_contains($msg, 'refund')) {
            return "Our return policy allows returns within 7 days of delivery for unused products in their original packaging. You can raise a return request directly from your dashboard under the My Orders section.";
        }

        return "Thank you for your message! Ashmita is reviewing your request. Since my AI services are in setup mode, an admin will connect with you here shortly.";
    }
}

// Helper function to safely output total/price
function number_of_format_or_raw($val) {
    return is_numeric($val) ? number_format($val, 2) : $val;
}
