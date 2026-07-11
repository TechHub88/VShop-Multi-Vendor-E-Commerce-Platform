<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AdminChatController extends Controller
{
    public function handleChat(Request $request)
    {
        $message = $request->input('message');
        $context = $request->input('context'); // Up to ~5000 chars of page text
        
        $apiKey = env('OPENROUTER_API_KEY', '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');

        if (empty($apiKey)) {
            return response()->json([
                'status' => 'error',
                'message' => 'OpenRouter API Key not configured.'
            ], 500);
        }

        $systemPrompt = "You are Ashmita, a helpful AI assistant built directly into the V-Shop Admin Panel. 
        You exist to assist the admin or staff member who is currently logged in. 
        Read the following context from the page they are currently viewing, and answer their query based on it. 
        Keep your answer concise and helpful. Don't mention that you read the page context explicitly, just use the information.
        
        PAGE CONTEXT: 
        " . strip_tags($context);

        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $apiKey,
                'HTTP-Referer' => url('/'),
                'X-Title' => 'V-Shop Admin',
                'Content-Type' => 'application/json'
            ])->post('https://openrouter.ai/api/v1/chat/completions', [
                'model' => 'openrouter/auto',
                'messages' => [
                    ['role' => 'system', 'content' => $systemPrompt],
                    ['role' => 'user', 'content' => $message]
                ],
                'temperature' => 0.7,
                'max_tokens' => 500
            ]);

            if ($response->successful()) {
                $data = $response->json();
                $reply = $data['choices'][0]['message']['content'] ?? 'Sorry, I am unable to formulate a response right now.';
                return response()->json([
                    'status' => 'success',
                    'reply' => $reply
                ]);
            } else {
                Log::error("OpenRouter API Error: " . $response->body());
                return response()->json([
                    'status' => 'error',
                    'message' => 'Failed to connect to the AI service.'
                ], 500);
            }
        } catch (\Exception $e) {
            Log::error("AdminChatController exception: " . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'An error occurred while communicating with the AI service.'
            ], 500);
        }
    }
}
