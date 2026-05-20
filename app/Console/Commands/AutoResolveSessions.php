<?php

namespace App\Console\Commands;

use App\Models\Booking;
use App\Models\User;
use Illuminate\Console\Command;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class AutoResolveSessions extends Command
{
    protected $signature = 'sessions:auto-resolve';
    protected $description = 'Automatically complete overdue sessions and mark no-shows based on participation';

    public function handle()
    {
        $now = Carbon::now('Asia/Kolkata');
        
        // Fetch all confirmed bookings to check if they are overdue
        $bookings = Booking::where('status', 'confirmed')->get();
        
        $resolvedCount = 0;
        
        foreach ($bookings as $booking) {
            $sessionDateString = $booking->session_date instanceof \DateTime 
                ? $booking->session_date->format('Y-m-d H:i:s') 
                : Carbon::parse($booking->session_date)->format('Y-m-d H:i:s');
                
            $sessionStart = Carbon::createFromFormat('Y-m-d H:i:s', $sessionDateString, 'Asia/Kolkata');
            $duration = (int) ($booking->duration_minutes ?: 60);
            $sessionEnd = $sessionStart->copy()->addMinutes($duration);
            
            // Check if the scheduled session duration has fully elapsed
            if ($now->greaterThanOrEqualTo($sessionEnd)) {
                $traineeJoined = (bool) ($booking->trainee_joined ?? false);
                $trainerJoined = (bool) ($booking->trainer_joined ?? false);
                
                if ($traineeJoined && $trainerJoined) {
                    // Both parties joined: Auto-complete the session
                    $booking->update([
                        'status' => 'completed',
                        'completed_at' => $now,
                        'meeting_ended' => true,
                        'auto_resolved_reason' => 'Both parties joined. Session marked complete.'
                    ]);
                    $this->info("Booking {$booking->id} auto-completed (both joined).");
                } elseif ($trainerJoined && !$traineeJoined) {
                    // Trainer showed up, Trainee did not: Trainee No-show
                    // Trainer keeps the payment
                    $booking->update([
                        'status' => 'no_show',
                        'no_show_party' => 'trainee',
                        'auto_resolved_reason' => 'Trainee was a no-show. Trainer joined.',
                        'completed_at' => $now
                    ]);
                    $this->info("Booking {$booking->id} marked Trainee No-Show (trainer joined).");
                } elseif (!$trainerJoined && $traineeJoined) {
                    // Trainee showed up, Trainer did not: Trainer No-show
                    // Mark as no-show and refund the trainee
                    $booking->update([
                        'status' => 'no_show',
                        'no_show_party' => 'trainer',
                        'refund_status' => 'pending_admin',
                        'refund_amount' => (float) $booking->amount,
                        'auto_resolved_reason' => 'Trainer was a no-show. Trainee joined.'
                    ]);
                    $this->info("Booking {$booking->id} marked Trainer No-Show (trainee joined). Refund pending admin approval.");
                } else {
                    // Neither joined: Both No-show
                    // Mark as no-show and refund the trainee
                    $booking->update([
                        'status' => 'no_show',
                        'no_show_party' => 'both',
                        'refund_status' => 'pending_admin',
                        'refund_amount' => (float) $booking->amount,
                        'auto_resolved_reason' => 'Neither party joined the session.'
                    ]);
                    $this->info("Booking {$booking->id} marked Both No-Show. Refund pending admin approval.");
                }
                
                $resolvedCount++;
            }
        }
        
        $this->info("Auto-resolved {$resolvedCount} overdue bookings.");
        return Command::SUCCESS;
    }
}
