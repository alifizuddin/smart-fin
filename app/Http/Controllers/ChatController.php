<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ChatController extends Controller
{
    public function sendMessage(Request $request)
    {
        $request->validate(['message' => 'required|string']);
        $user = $request->user();

        // New n8n Webhook for Chat
        $n8nUrl = env('N8N_CHAT_WEBHOOK_URL');

        try {
            $response = Http::post($n8nUrl, [
                'message' => $request->message,
                'user_id' => $user->id,
                'user_name' => $user->name,
                'department_id' => $user->department_id
            ]);

            if ($response->successful()) {
                return response()->json([
                    'reply' => $response->json('output') ?? 'I processed that, but have no text response.'
                ]);
            }

            return response()->json(['reply' => 'My brain (n8n) is offline right now.'], 503);

        } catch (\Exception $e) {
            Log::error('Chatbot Error: ' . $e->getMessage());
            return response()->json(['reply' => 'An error occurred while connecting.'], 500);
        }
    }
}