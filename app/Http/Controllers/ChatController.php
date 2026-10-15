<?php

namespace App\Http\Controllers;

use App\Models\ChatMessage;
use App\Models\Schedule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ChatController extends Controller
{
    public function getMessages(Schedule $schedule)
    {
        $user = Auth::user();
        
        // Check if user is enrolled in the classroom
        if (!$schedule->classroom->students()->where('user_id', $user->id)->exists()) {
            abort(403, 'You are not enrolled in this classroom.');
        }

        $messages = $schedule->chatMessages()
                            ->with('user')
                            ->orderBy('created_at', 'asc')
                            ->get();

        return response()->json($messages);
    }

    public function sendMessage(Request $request, Schedule $schedule)
    {
        $user = Auth::user();
        
        // Check if user is enrolled in the classroom
        if (!$schedule->classroom->students()->where('user_id', $user->id)->exists()) {
            abort(403, 'You are not enrolled in this classroom.');
        }

        $request->validate([
            'message' => 'required|string|max:1000',
            'type' => 'nullable|in:text,system,notification',
            'is_private' => 'boolean',
            'reply_to' => 'nullable|exists:chat_messages,id',
        ]);

        $message = $schedule->chatMessages()->create([
            'user_id' => $user->id,
            'message' => $request->message,
            'type' => $request->type ?? 'text',
            'is_private' => $request->is_private ?? false,
            'reply_to' => $request->reply_to,
        ]);

        $message->load('user');

        return response()->json([
            'success' => true,
            'message' => $message,
        ]);
    }

    public function deleteMessage(ChatMessage $message)
    {
        $user = Auth::user();
        
        // Only allow users to delete their own messages or teachers to delete any message
        if ($message->user_id !== $user->id && !$user->isTeacher()) {
            abort(403, 'You can only delete your own messages.');
        }

        $message->delete();

        return response()->json(['success' => true]);
    }
}
