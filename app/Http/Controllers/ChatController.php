<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ChatConversation;
use App\Models\ChatMessage;
use App\Models\Seller;
use App\Models\BroadcastMessage;
use App\Models\InternalSupport;
use App\Services\BroadcastService;
use App\Services\GeminiChatService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;

class ChatController extends Controller
{
    protected $broadcastService;

    public function __construct(BroadcastService $broadcastService)
    {
        $this->broadcastService = $broadcastService;
    }

    // ==================== COMMUNICATION CENTER DASHBOARD ====================
    public function index()
    {
        try {
            $conversations = ChatConversation::with(['seller', 'admin', 'messages' => function($query) {
                $query->latest()->take(1);
            }])->latest('last_message_at')->get();

            $internalSupports = InternalSupport::with(['assignedTo', 'createdBy', 'conversation'])
                ->latest()
                ->get();

            $broadcasts = BroadcastMessage::with('sender')
                ->latest()
                ->take(10)
                ->get();

            $stats = [
                'total_conversations' => ChatConversation::count(),
                'open_conversations' => ChatConversation::where('status', 'open')->count(),
                'resolved_today' => InternalSupport::whereDate('resolved_at', today())->count(),
            ];

            return view('chat.communication-center', compact('conversations', 'internalSupports', 'broadcasts', 'stats'));
        } catch (\Exception $e) {
            return view('chat.communication-center', [
                'conversations' => collect(),
                'internalSupports' => collect(),
                'broadcasts' => collect(),
                'stats' => [
                    'total_conversations' => 0,
                    'open_conversations' => 0,
                    'resolved_today' => 0,
                ]
            ]);
        }
    }

    // ==================== SELLER-ADMIN CHAT ====================
    public function getSellers()
    {
        $sellers = Seller::select('id', 'name')->get();
        return response()->json($sellers);
    }

 public function getMessages($sellerId)
{
    try {
        $adminId = Auth::id();
        if (!$adminId) {
            return response()->json(['error' => 'Unauthenticated'], 401);
        }

        $conversation = ChatConversation::firstOrCreate(
            [
                'type' => 'seller_admin',
                'seller_id' => $sellerId,
                'admin_id' => $adminId,
            ],
            [
                'subject' => 'Chat between admin and seller',
                'status' => 'open',
                'last_message_at' => now(),
            ]
        );

        $messages = ChatMessage::where('conversation_id', $conversation->id)
            ->orderBy('created_at', 'asc')
            ->get();

        return response()->json(['messages' => $messages, 'conversation_id' => $conversation->id]);
    } catch (\Exception $e) {
        \Log::error('getMessages error: ' . $e->getMessage());
        return response()->json(['error' => $e->getMessage()], 500);
    }
}
    public function sendMessage(Request $request)
    {
        $request->validate([
            'seller_id' => 'required|exists:sellers,id',
            'message' => 'required|string',
        ]);

        $adminId = Auth::id();
        $conversation = ChatConversation::firstOrCreate(
            [
                'type' => 'seller_admin',
                'seller_id' => $request->seller_id,
                'admin_id' => $adminId,
            ],
            ['status' => 'open']
        );

        $message = ChatMessage::create([
            'conversation_id' => $conversation->id,
            'sender_id' => $adminId,
            'sender_type' => 'admin',
            'message' => $request->message,
            'is_read' => false,
        ]);

        $conversation->update(['last_message_at' => now()]);

        // Publish message to Socket.io server
        try {
            Http::post('http://localhost:3000/publish', [
                'room' => 'conversation_' . $conversation->id,
                'event' => 'message',
                'data' => [
                    'id' => $message->id,
                    'message' => $message->message,
                    'sender_id' => $adminId,
                    'sender_type' => 'admin',
                    'created_at' => $message->created_at->toDateTimeString()
                ]
            ]);
        } catch (\Exception $ex) {
            \Log::warning('Failed to publish admin message to Socket.io: ' . $ex->getMessage());
        }

        return response()->json(['success' => true, 'message' => $message]);
    }

    // ==================== CONVERSATION LIST (OPTIONAL) ====================
    public function conversations()
    {
        try {
            $conversations = ChatConversation::where('type', 'seller_admin')
                ->with(['seller', 'admin', 'messages' => function($query) {
                    $query->latest()->take(1);
                }])
                ->latest('last_message_at')
                ->get();

            return view('chat.conversations', compact('conversations'));
        } catch (\Exception $e) {
            return view('chat.conversations', ['conversations' => collect()]);
        }
    }

    public function getConversation($id)
    {
        $conversation = ChatConversation::with(['seller', 'admin', 'messages.sender'])
            ->findOrFail($id);

        // Mark unread messages as read
        $conversation->messages()
            ->where('sender_id', '!=', Auth::id())
            ->where('is_read', false)
            ->update(['is_read' => true, 'read_at' => now()]);

        return response()->json($conversation);
    }

    public function createConversation(Request $request)
    {
        $request->validate([
            'seller_id' => 'required|exists:sellers,id',
            'subject' => 'nullable|string',
        ]);

        $conversation = ChatConversation::create([
            'type' => 'seller_admin',
            'seller_id' => $request->seller_id,
            'admin_id' => Auth::id(),
            'subject' => $request->subject,
            'status' => 'open',
        ]);

        return response()->json($conversation);
    }

    // ==================== INTERNAL SUPPORT ====================
    public function internalSupport()
    {
        try {
            $supports = InternalSupport::with(['assignedTo', 'createdBy', 'conversation'])
                ->latest()
                ->get();

            return view('chat.internal-support', compact('supports'));
        } catch (\Exception $e) {
            return view('chat.internal-support', ['supports' => collect()]);
        }
    }

    public function createInternalSupport(Request $request)
    {
        $request->validate([
            'department' => 'required|in:support,finance,technical',
            'priority' => 'required|in:low,medium,high,urgent',
            'subject' => 'required|string',
            'description' => 'required|string',
        ]);

        $conversation = ChatConversation::create([
            'type' => 'internal',
            'subject' => $request->subject,
            'status' => 'open',
        ]);

        $support = InternalSupport::create([
            'conversation_id' => $conversation->id,
            'ticket_number' => InternalSupport::generateTicketNumber(),
            'department' => $request->department,
            'priority' => $request->priority,
            'subject' => $request->subject,
            'description' => $request->description,
            'created_by' => Auth::id(),
            'status' => 'open',
        ]);

        return response()->json($support->load('conversation'));
    }

    public function updateInternalSupport(Request $request, $id)
    {
        $support = InternalSupport::findOrFail($id);

        $request->validate([
            'status' => 'required|in:open,in_progress,resolved,closed',
            'assigned_to' => 'nullable|exists:users,id',
        ]);

        $support->update([
            'status' => $request->status,
            'assigned_to' => $request->assigned_to,
        ]);

        if ($request->status === 'resolved') {
            $support->markAsResolved();
        }

        return response()->json($support);
    }

    public function getInternalSupport($id)
    {
        $support = InternalSupport::with(['assignedTo', 'createdBy', 'conversation'])
            ->findOrFail($id);

        return response()->json($support);
    }

    // ==================== BROADCAST MESSAGING ====================
    public function broadcastMessaging()
    {
        try {
            $broadcasts = BroadcastMessage::with('sender')
                ->latest()
                ->paginate(20);

            return view('chat.broadcast-messaging', compact('broadcasts'));
        } catch (\Exception $e) {
            return view('chat.broadcast-messaging', ['broadcasts' => collect()]);
        }
    }

    public function sendBroadcast(Request $request)
    {
        $request->validate([
            'channel' => 'required|in:email,sms,whatsapp',
            'audience' => 'required|string',
            'message' => 'required|string',
        ]);

        $broadcast = BroadcastMessage::create([
            'sender_id' => Auth::id(),
            'channel' => $request->channel,
            'audience' => $request->audience,
            'message' => $request->message,
            'status' => 'pending',
            'total_recipients' => $this->broadcastService->getRecipientCount($request->audience),
        ]);

        $this->broadcastService->sendBroadcast($broadcast);

        return response()->json($broadcast);
    }

    // ==================== STATS API ====================
    public function getStats()
    {
        $stats = [
            'total_conversations' => ChatConversation::count(),
            'open_conversations' => ChatConversation::where('status', 'open')->count(),
            'resolved_today' => InternalSupport::whereDate('resolved_at', today())->count(),
        ];

        return response()->json($stats);
    }

    // ==================== ASHMITA CHATBOT API ====================
    public function ashmitaHistory()
    {
        try {
            $userId = Auth::guard('customer')->id() ?? Auth::id();
            $senderType = Auth::guard('customer')->check() ? 'customer' : 'seller';
            if (!$userId) {
                return response()->json(['error' => 'Unauthenticated'], 401);
            }

            // Use the correct column based on user type
            $idColumn = $senderType === 'customer' ? 'customer_id' : 'seller_id';
            $adminId = \App\Models\User::first()->id ?? null;

            $conversation = ChatConversation::firstOrCreate(
                [
                    'type' => 'support',
                    $idColumn => $userId,
                ],
                [
                    'admin_id' => $adminId,
                    'subject' => 'Support Chat with Ashmita',
                    'status' => 'open',
                    'last_message_at' => now(),
                ]
            );

            $messages = ChatMessage::where('conversation_id', $conversation->id)
                ->orderBy('created_at', 'asc')
                ->get();

            return response()->json([
                'success' => true,
                'conversation_id' => $conversation->id,
                'messages' => $messages
            ]);
        } catch (\Exception $e) {
            \Log::error('ashmitaHistory error: ' . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function ashmitaMessage(Request $request, GeminiChatService $geminiService)
    {
        try {
            $userId = null;
            $senderType = 'seller';
            if (Auth::guard('customer')->check()) {
                $senderType = 'customer';
                $userId = Auth::guard('customer')->id();
            } elseif (Auth::guard('web')->check()) {
                $senderType = 'admin';
                $userId = Auth::guard('web')->id();
            } else {
                $senderType = 'seller';
                $userId = Auth::guard('seller')->id() ?? Auth::id();
            }

            if (!$userId) {
                return response()->json(['error' => 'Unauthenticated'], 401);
            }

            $request->validate([
                'message' => 'required|string',
            ]);

            // Use the correct column based on user type
            $idColumn = $senderType === 'customer' ? 'customer_id' : ($senderType === 'admin' ? 'admin_id' : 'seller_id');
            $adminId = \App\Models\User::first()->id ?? null;

            $conversation = ChatConversation::firstOrCreate(
                [
                    'type' => 'support',
                    $idColumn => $userId,
                ],
                [
                    'admin_id' => $adminId,
                    'subject' => 'Support Chat with Ashmita',
                    'status' => 'open',
                    'last_message_at' => now(),
                ]
            );

            // 1. Create and save the buyer's message
            $buyerMessage = ChatMessage::create([
                'conversation_id' => $conversation->id,
                'sender_id' => $userId,
                'sender_type' => $senderType,
                'message' => $request->message,
                'is_read' => false,
            ]);

            $conversation->update(['last_message_at' => now()]);

            // 2. Publish buyer message to Socket.io server
            try {
                Http::post('http://localhost:3000/publish', [
                    'room' => 'conversation_' . $conversation->id,
                    'event' => 'message',
                    'data' => [
                        'id' => $buyerMessage->id,
                        'message' => $buyerMessage->message,
                        'sender_id' => $userId,
                        'sender_type' => $senderType,
                        'created_at' => $buyerMessage->created_at->toDateTimeString()
                    ]
                ]);
            } catch (\Exception $ex) {
                \Log::warning('Failed to publish buyer message to Socket.io: ' . $ex->getMessage());
            }

            // 3. Generate AI response from Ashmita
            $botReplyText = $geminiService->getChatbotResponse($request->message, $userId, $senderType);

            // 4. Save Ashmita's message to database
            $botMessage = ChatMessage::create([
                'conversation_id' => $conversation->id,
                'sender_id' => 0, // 0 represents Ashmita Bot
                'sender_type' => 'bot',
                'message' => $botReplyText,
                'is_read' => false,
            ]);

            // 5. Publish Ashmita message to Socket.io server
            try {
                Http::post('http://localhost:3000/publish', [
                    'room' => 'conversation_' . $conversation->id,
                    'event' => 'message',
                    'data' => [
                        'id' => $botMessage->id,
                        'message' => $botMessage->message,
                        'sender_id' => 0,
                        'sender_type' => 'bot',
                        'created_at' => $botMessage->created_at->toDateTimeString()
                    ]
                ]);
            } catch (\Exception $ex) {
                \Log::warning('Failed to publish bot message to Socket.io: ' . $ex->getMessage());
            }

            return response()->json([
                'success' => true,
                'buyer_message' => $buyerMessage,
                'bot_message' => $botMessage
            ]);

        } catch (\Exception $e) {
            \Log::error('ashmitaMessage error: ' . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function ashmitaClearHistory()
    {
        try {
            $senderType = \Auth::guard('customer')->check() ? 'customer' : 'seller';
            $userId = \Auth::guard('customer')->id() ?? \Auth::id();

            if (!$userId) {
                return response()->json(['error' => 'Unauthorized'], 401);
            }

            $idColumn = $senderType === 'customer' ? 'customer_id' : 'seller_id';

            $conversation = ChatConversation::where('type', 'support')
                ->where($idColumn, $userId)
                ->first();

            if ($conversation) {
                ChatMessage::where('conversation_id', $conversation->id)->delete();
            }

            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            \Log::error('ashmitaClearHistory error: ' . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}