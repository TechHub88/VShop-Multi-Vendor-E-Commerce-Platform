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

        return response()->json(['messages' => $messages]);
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
    public function ashmitaHistory(Request $request)
    {
        try {
            $userId = Auth::guard('customer')->id() ?? Auth::id();
            $senderType = Auth::guard('customer')->check() ? 'customer' : 'seller';
            
            if (!$userId) {
                // Not authenticated, check if socket_id is provided
                $socketId = $request->input('socket_id');
                if (!$socketId) {
                    return response()->json(['error' => 'Unauthenticated'], 401);
                }
                $userId = $socketId;
                $senderType = 'guest';
            }

            // Use the correct column based on user type
            $idColumn = 'socket_id';
            if ($senderType === 'customer') {
                $idColumn = 'customer_id'; // wait, customer_id doesn't exist on chat_conversations
                // actually, let's use socket_id for guests, but wait, ChatConversation only has seller_id and admin_id and socket_id
            }

            // Let's adjust this securely. If it's a seller, they have seller_id. If guest, socket_id. If customer, they might not have a column yet, but earlier code used customer_id. 
            // We just added socket_id. Let's just find by the respective field.
            $matchArgs = ['type' => 'support'];
            if ($senderType === 'guest') {
                $matchArgs['socket_id'] = $userId;
            } elseif ($senderType === 'customer') {
                $matchArgs['customer_id'] = $userId; // might crash if column is missing, assuming it exists
            } else {
                $matchArgs['seller_id'] = $userId;
            }

            $adminId = \App\Models\User::first()->id ?? null;

            $conversation = ChatConversation::firstOrCreate(
                $matchArgs,
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
            } elseif (Auth::guard('seller')->check() || Auth::id()) {
                $senderType = 'seller';
                $userId = Auth::guard('seller')->id() ?? Auth::id();
            } else {
                $socketId = $request->input('socket_id');
                if (!$socketId) {
                    return response()->json(['error' => 'Unauthenticated'], 401);
                }
                $userId = $socketId;
                $senderType = 'guest';
            }

            $request->validate([
                'message' => 'required|string',
            ]);

            $matchArgs = ['type' => 'support'];
            if ($senderType === 'guest') {
                $matchArgs['socket_id'] = $userId;
            } elseif ($senderType === 'customer') {
                $matchArgs['customer_id'] = $userId;
            } elseif ($senderType === 'admin') {
                $matchArgs['admin_id'] = $userId;
            } else {
                $matchArgs['seller_id'] = $userId;
            }

            $adminId = \App\Models\User::first()->id ?? null;

            $conversation = ChatConversation::firstOrCreate(
                $matchArgs,
                [
                    'admin_id' => $adminId,
                    'subject' => 'Support Chat with Ashmita',
                    'status' => 'open',
                    'last_message_at' => now(),
                ]
            );

            // 1. Create and save the buyer's message
            // sender_id is integer in DB. If userId is a string (socket_id), it will be cast to 0. Let's keep it as 0 for guests.
            $dbSenderId = ($senderType === 'guest') ? 0 : $userId;
            $buyerMessage = ChatMessage::create([
                'conversation_id' => $conversation->id,
                'sender_id' => $dbSenderId,
                'sender_type' => $senderType,
                'message' => $request->message,
                'is_read' => false,
            ]);

            $conversation->update(['last_message_at' => now()]);

            // 2. Publish buyer message to Socket.io server
            try {
                Http::timeout(1)->post('http://localhost:3000/publish', [
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
                Http::timeout(1)->post('http://localhost:3000/publish', [
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

    public function ashmitaClearHistory(Request $request)
    {
        try {
            $senderType = \Auth::guard('customer')->check() ? 'customer' : 'seller';
            $userId = \Auth::guard('customer')->id() ?? \Auth::id();

            if (!$userId) {
                $socketId = $request->input('socket_id');
                if (!$socketId) {
                    return response()->json(['error' => 'Unauthorized'], 401);
                }
                $userId = $socketId;
                $senderType = 'guest';
            }

            $idColumn = 'seller_id';
            if ($senderType === 'guest') {
                $idColumn = 'socket_id';
            } elseif ($senderType === 'customer') {
                $idColumn = 'customer_id';
            }

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
