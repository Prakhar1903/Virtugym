<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Workout;
use App\Models\ProgressMetric;
use App\Models\WaterIntake;
use App\Models\User;
use App\Models\WithdrawalRequest;
use Illuminate\Http\Request;
use Carbon\Carbon;

class NotificationController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        if (!$user) {
            return response()->json([]);
        }

        $notifications = [];

        if ($user->role === 'admin') {
            // 1. Pending trainer verifications
            $pendingTrainers = User::where('role', 'trainer')
                ->where(function($q) {
                    $q->whereNull('is_verified')->orWhere('is_verified', false);
                })
                ->count();
            if ($pendingTrainers > 0) {
                $notifications[] = [
                    'id' => 'admin_trainers',
                    'title' => 'Pending Trainer Verifications',
                    'message' => "There are {$pendingTrainers} trainer(s) waiting for verification.",
                    'category' => 'admin',
                    'type' => 'warning',
                    'time_ago' => 'Action required',
                    'url' => route('admin.trainers')
                ];
            }

            // 2. Pending withdrawal requests
            $pendingWithdrawals = WithdrawalRequest::where('status', 'pending')->count();
            if ($pendingWithdrawals > 0) {
                $notifications[] = [
                    'id' => 'admin_withdrawals',
                    'title' => 'Pending Withdrawals',
                    'message' => "There are {$pendingWithdrawals} withdrawal request(s) pending approval.",
                    'category' => 'admin',
                    'type' => 'warning',
                    'time_ago' => 'Action required',
                    'url' => route('admin.withdrawals')
                ];
            }
        } elseif ($user->role === 'trainer') {
            // 1. Pending booking requests
            $pendingBookings = Booking::where('trainer_id', $user->id)
                ->where('status', 'pending')
                ->count();
            if ($pendingBookings > 0) {
                $notifications[] = [
                    'id' => 'trainer_pending_bookings',
                    'title' => 'Pending Bookings',
                    'message' => "You have {$pendingBookings} pending booking request(s).",
                    'category' => 'booking',
                    'type' => 'warning',
                    'time_ago' => 'Review now',
                    'url' => route('trainer.schedule')
                ];
            }

            // 2. Today's confirmed sessions
            $todayConfirmed = Booking::where('trainer_id', $user->id)
                ->where('status', 'confirmed')
                ->whereDate('session_date', Carbon::today())
                ->with('trainee')
                ->get();
            foreach ($todayConfirmed as $b) {
                $time = Carbon::parse($b->session_date)->format('g:i A');
                $traineeName = $b->trainee ? $b->trainee->name : 'Client';
                $notifications[] = [
                    'id' => 'trainer_today_session_' . $b->id,
                    'title' => 'Session Today',
                    'message' => "Confirmed session with {$traineeName} at {$time} today.",
                    'category' => 'booking',
                    'type' => 'success',
                    'time_ago' => 'Today',
                    'url' => route('trainer.dashboard')
                ];
            }

            // 3. Profile verification status
            if (empty($user->is_verified)) {
                $notifications[] = [
                    'id' => 'trainer_unverified',
                    'title' => 'Profile Pending Verification',
                    'message' => 'Your profile is pending admin approval. You will receive an email once verified.',
                    'category' => 'profile',
                    'type' => 'info',
                    'time_ago' => 'Info',
                    'url' => '#'
                ];
            }
        } else {
            // Trainee
            // 1. Confirmed upcoming bookings (Trainer confirmed notification)
            $upcomingBookings = Booking::where('trainee_id', $user->id)
                ->where('status', 'confirmed')
                ->where('session_date', '>=', now())
                ->with('trainer')
                ->get();
            foreach ($upcomingBookings as $b) {
                $trainerName = $b->trainer ? $b->trainer->name : 'Trainer';
                $formattedDate = Carbon::parse($b->session_date)->format('M d, g:i A');
                $notifications[] = [
                    'id' => 'trainee_booking_' . $b->id,
                    'title' => 'Trainer Confirmed',
                    'message' => "Trainer {$trainerName} confirmed your session for {$formattedDate}.",
                    'category' => 'booking',
                    'type' => 'success',
                    'time_ago' => Carbon::parse($b->session_date)->diffForHumans(),
                    'url' => route('bookings.index')
                ];
            }

            // 2. Newly completed workouts
            $completedWorkouts = Workout::where('user_id', $user->id)
                ->whereNotNull('completed_at')
                ->where('completed_at', '>=', now()->subDays(3))
                ->orderBy('completed_at', 'desc')
                ->get();
            foreach ($completedWorkouts as $w) {
                $notifications[] = [
                    'id' => 'trainee_workout_completed_' . $w->id,
                    'title' => 'Workout Completed',
                    'message' => "Workout \"{$w->title}\" completed successfully. Keep it up! 💪",
                    'category' => 'workout',
                    'type' => 'success',
                    'time_ago' => Carbon::parse($w->completed_at)->diffForHumans(),
                    'url' => route('workouts.index')
                ];
            }

            // 3. Newly assigned workouts by trainer
            $assignedWorkouts = Workout::where('trainee_id', $user->id)
                ->whereNotNull('assigned_by')
                ->where('created_at', '>=', now()->subDays(3))
                ->with('trainer')
                ->get();
            foreach ($assignedWorkouts as $w) {
                $trainerName = $w->trainer ? $w->trainer->name : 'your trainer';
                $notifications[] = [
                    'id' => 'trainee_workout_' . $w->id,
                    'title' => 'New Workout Assigned',
                    'message' => "Workout \"{$w->title}\" has been assigned to you by {$trainerName}.",
                    'category' => 'workout',
                    'type' => 'info',
                    'time_ago' => Carbon::parse($w->created_at)->diffForHumans(),
                    'url' => route('workouts.index')
                ];
            }

            // 4. Hydration Check (Dynamic goal reached vs reminder)
            $waterIntake = WaterIntake::where('user_id', $user->id)
                ->where('date', Carbon::today())
                ->sum('amount_ml');
            $waterGoal = $user->daily_water_goal ?? 3000;
            if ($waterIntake >= $waterGoal) {
                $notifications[] = [
                    'id' => 'trainee_water_goal_reached',
                    'title' => 'Hydration Goal Reached',
                    'message' => "Hydration goal reached! You've successfully logged " . number_format($waterIntake) . "ml of water today. 💧",
                    'category' => 'water',
                    'type' => 'success',
                    'time_ago' => 'Today',
                    'url' => route('water.index')
                ];
            } else {
                $notifications[] = [
                    'id' => 'trainee_water_reminder',
                    'title' => 'Hydration Check',
                    'message' => "Remember to stay hydrated! Log your water intake to hit your " . number_format($waterGoal) . "ml goal.",
                    'category' => 'water',
                    'type' => 'warning',
                    'time_ago' => 'Today',
                    'url' => route('water.index')
                ];
            }

            // 5. Weight logging check (if no updates in 7 days)
            $lastProgress = ProgressMetric::where('user_id', $user->id)
                ->orderBy('date', 'desc')
                ->first();
            if (!$lastProgress || Carbon::parse($lastProgress->date)->diffInDays(now()) >= 7) {
                $notifications[] = [
                    'id' => 'trainee_progress_reminder',
                    'title' => 'Log Your Progress',
                    'message' => 'It has been over a week since your last progress update. Log your weight to stay on track!',
                    'category' => 'progress',
                    'type' => 'info',
                    'time_ago' => 'Weekly check',
                    'url' => route('progress.index')
                ];
            }
        }

        return response()->json($notifications);
    }
}
