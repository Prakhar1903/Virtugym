<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class VideoCallController extends Controller
{
    public function join($booking_id)
    {
        $booking = Booking::findOrFail($booking_id);
        
        // Check if user is part of this booking
        if (Auth::id() != $booking->trainee_id && Auth::id() != $booking->trainer_id) {
            abort(403);
        }

        if ($booking->status !== 'confirmed') {
            return redirect()->route('bookings.index')->with('error', 'Only confirmed sessions can be joined.');
        }

        // Trainee cannot enter the room until the trainer has started the session
        if (Auth::id() == $booking->trainee_id && !$booking->meeting_started) {
            return redirect()->route('bookings.index')->with('error', 'The session has not been started yet. Please wait for your trainer to start the session.');
        }
        
        // Check if session time has arrived (15 minutes before allowed)
        // We parse the stored session date naively in the local timezone (Asia/Kolkata)
        // since the database stores the local time naively with a UTC timezone cast.
        $sessionDateString = $booking->session_date instanceof \DateTime 
            ? $booking->session_date->format('Y-m-d H:i:s') 
            : \Carbon\Carbon::parse($booking->session_date)->format('Y-m-d H:i:s');

        $sessionTime = \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $sessionDateString, 'Asia/Kolkata');
        $now = \Carbon\Carbon::now('Asia/Kolkata');
        
        $joinOpensAt = $sessionTime->copy()->subMinutes(15);
        $canJoin = ($now->greaterThanOrEqualTo($joinOpensAt));
        
        if (!$canJoin) {
            $waitMinutes = ceil(($joinOpensAt->timestamp - $now->timestamp) / 60);
            return redirect()->back()->with('error', "Video session opens 15 minutes before the scheduled time. Please try again in {$waitMinutes} minutes.");
        }
        
        // Generate Jitsi meeting link
        $meetingId = $booking->meeting_id ?? 'virtugym_' . $booking->id . '_' . md5($booking->id);
        $meetingLink = "https://meet.jit.si/" . $meetingId;
        
        $updates = [];
        if (!$booking->meeting_id) {
            $updates['meeting_id'] = $meetingId;
            $updates['meeting_link'] = $meetingLink;
        }
        
        if (Auth::id() == $booking->trainee_id) {
            $updates['trainee_joined'] = true;
        } elseif (Auth::id() == $booking->trainer_id) {
            $updates['trainer_joined'] = true;
            $updates['meeting_started'] = true;
        }
        
        if (!empty($updates)) {
            $booking->update($updates);
        }
        
        return view('video-call.join', compact('booking', 'meetingLink', 'meetingId'));
    }
    
    public function startMeeting($booking_id)
    {
        $booking = Booking::findOrFail($booking_id);
        
        if (Auth::id() != $booking->trainer_id) {
            abort(403);
        }
        
        $booking->update(['meeting_started' => true]);
        
        return redirect()->route('video-call.join', $booking_id);
    }
    
    public function endMeeting($booking_id)
    {
        $booking = Booking::findOrFail($booking_id);
        
        if (Auth::id() != $booking->trainer_id) {
            abort(403);
        }
        
        $booking->update([
            'meeting_ended' => true,
            'status' => 'completed'
        ]);
        
        return redirect()->route('bookings.index')->with('success', 'Session completed!');
    }

    /**
     * Returns the meeting_started status of all active confirmed bookings for
     * the currently authenticated trainee. Used by the frontend polling loop
     * to detect when the trainer has started the session.
     */
    public function sessionStatus()
    {
        $user = Auth::user();

        // Only trainees need to poll
        if ($user->role !== 'trainee') {
            return response()->json([]);
        }

        $bookings = Booking::where('trainee_id', $user->id)
            ->where('status', 'confirmed')
            ->where('meeting_ended', '!=', true)
            ->get(['id', 'meeting_started', 'trainer_id'])
            ->map(fn($b) => [
                'id'              => (string) $b->id,
                'meeting_started' => (bool) $b->meeting_started,
            ]);

        return response()->json($bookings);
    }
}
