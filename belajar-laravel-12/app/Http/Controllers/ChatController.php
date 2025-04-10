<?php

namespace App\Http\Controllers;

use App\Events\NewChatMessage;
use App\Models\Message;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ChatController extends Controller
{
    /**
     * Display the chat interface.
     */
    public function index()
    {
        $users = User::all();
        
        return view('chat.index', compact('users'));
    }
    
    /**
     * Get all messages between the authenticated user and another user.
     */
    public function getMessages($userId)
    {
        $authUser = Auth::user();
        $otherUser = User::findOrFail($userId);
        
        // Get messages between these two users
        $messages = Message::where(function($query) use ($authUser, $otherUser) {
                $query->where('user_id', $authUser->id)
                      ->where('recipient_id', $otherUser->id);
            })
            ->orWhere(function($query) use ($authUser, $otherUser) {
                $query->where('user_id', $otherUser->id)
                      ->where('recipient_id', $authUser->id);
            })
            ->orderBy('created_at', 'asc')
            ->get();
            
        // Count unread messages
        $unreadCount = Message::where('user_id', $otherUser->id)
            ->where('recipient_id', $authUser->id)
            ->whereNull('read_at')
            ->count();
        
        return response()->json([
            'messages' => $messages,
            'unread_count' => $unreadCount
        ]);
    }
    
    /**
     * Store a new message.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'recipient_id' => 'required|exists:users,id',
            'body' => 'required|string'
        ]);
        
        $message = new Message();
        $message->body = $validated['body'];
        $message->user_id = Auth::id();
        $message->recipient_id = $validated['recipient_id'];
        $message->save();
        
        // Broadcast the message
        broadcast(new NewChatMessage($message))->toOthers();
        
        return response()->json($message, 201);
    }
    
    /**
     * Mark a message as read.
     */
    public function markAsRead($id)
    {
        $message = Message::findOrFail($id);
        
        // Only the recipient can mark a message as read
        if ($message->recipient_id === Auth::id()) {
            $message->markAsRead();
            return response()->json(['success' => true]);
        }
        
        return response()->json(['success' => false], 403);
    }
    
    /**
     * Mark all messages from a specific user as read.
     */
    public function markAllAsRead($userId)
    {
        $authUser = Auth::user();
        
        // Get all unread messages from this user
        Message::where('user_id', $userId)
            ->where('recipient_id', $authUser->id)
            ->whereNull('read_at')
            ->update(['read_at' => now()]);
        
        return response()->json(['success' => true]);
    }
}