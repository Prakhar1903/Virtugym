<?php

namespace App\Http\Controllers;

use App\Models\Message;
use App\Models\User;
use App\Models\Booking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ChatController extends Controller
{
    public function index($trainer_id = null)
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }
        
        $user = Auth::user();
        
        // Get all conversations
        $conversations = Message::where('sender_id', $user->id)
            ->orWhere('receiver_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->get()
            ->unique(function ($item) use ($user) {
                return $item->sender_id == $user->id ? $item->receiver_id : $item->sender_id;
            });
        
        $selectedTrainer = null;
        $nextSession = null;
        if ($trainer_id) {
            $selectedTrainer = User::find($trainer_id);
            
            // Get next session info
            $nextSession = Booking::where(function($q) use ($user, $trainer_id) {
                    $q->where('trainee_id', $user->id)->where('trainer_id', $trainer_id);
                })
                ->where('session_date', '>=', now()->toDateString())
                ->where('status', 'confirmed')
                ->orderBy('session_date', 'asc')
                ->orderBy('session_time', 'asc')
                ->first();
        }
        
        return view('chat.index', compact('conversations', 'selectedTrainer', 'nextSession'));
    }
    
    public function getMessages($trainer_id)
    {
        if (!Auth::check()) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
        
        $userId = Auth::id();
        
        $messages = Message::where(function($query) use ($userId, $trainer_id) {
            $query->where('sender_id', $userId)->where('receiver_id', $trainer_id);
        })->orWhere(function($query) use ($userId, $trainer_id) {
            $query->where('sender_id', $trainer_id)->where('receiver_id', $userId);
        })->orderBy('created_at', 'asc')->get();
        
        // Mark messages as read
        Message::where('sender_id', $trainer_id)
            ->where('receiver_id', $userId)
            ->where('is_read', false)
            ->update(['is_read' => true, 'read_at' => now()]);
        
        return response()->json($messages);
    }
    
    public function sendMessage(Request $request)
    {
        if (!Auth::check()) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
        
        $request->validate([
            'receiver_id' => 'required',
            'message' => 'nullable|string|max:1000',
            'attachment' => 'nullable|file|max:10240'
        ]);
        
        if (!$request->message && !$request->hasFile('attachment')) {
            return response()->json(['error' => 'Message or attachment is required'], 422);
        }

        $attachmentPath = null;
        $attachmentName = null;
        $attachmentType = null;

        if ($request->hasFile('attachment')) {
            $file = $request->file('attachment');
            $attachmentPath = $file->store('attachments', 'public');
            $attachmentName = $file->getClientOriginalName();
            $attachmentType = $file->getClientMimeType();
        }
        
        $message = Message::create([
            'sender_id' => Auth::id(),
            'receiver_id' => $request->receiver_id,
            'message' => $request->message ?? '',
            'is_read' => false,
            'attachment_path' => $attachmentPath,
            'attachment_name' => $attachmentName,
            'attachment_type' => $attachmentType,
        ]);
        
        return response()->json([
            'success' => true,
            'message' => $message
        ]);
    }
    
    public function getUnreadCount()
    {
        if (!Auth::check()) {
            return response()->json(['count' => 0]);
        }
        
        $count = Message::where('receiver_id', Auth::id())
            ->where('is_read', false)
            ->count();
            
        return response()->json(['count' => $count]);
    }
}
