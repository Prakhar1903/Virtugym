<?php
namespace App\Http\Controllers\Trainer;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\User;
use App\Models\Workout;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\WithdrawalRequest;
class TrainerDashboardController extends Controller
{
    public function index()
    {
        $trainer = Auth::user();
        $now = now();
        $startOfMonth = $now->copy()->startOfMonth();
        $endOfMonth = $now->copy()->endOfMonth();
        $todayStart = $now->copy()->startOfDay();
        $todayEnd = $now->copy()->endOfDay();
        
        // Upcoming bookings for the card
        $upcomingBookings = Booking::where('trainer_id', $trainer->id)
            ->where('status', 'confirmed')
            ->where('session_date', '>', $now)
            ->with('trainee')
            ->orderBy('session_date', 'asc')
            ->limit(5)
            ->get();
        
        // Assigned workouts (renamed from myWorkouts)
        $assignedWorkouts = Workout::where('trainer_id', $trainer->id)
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();
        
        // Financials
        $totalEarnings = $this->grossTrainerWalletAmount($trainer->id);
            
        $totalWithdrawn = WithdrawalRequest::where('trainer_id', $trainer->id)
            ->where('status', 'completed')
            ->sum('amount');
            
        $monthlyEarnings = Booking::where('trainer_id', $trainer->id)
            ->whereBetween('session_date', [$startOfMonth, $endOfMonth])
            ->whereIn('status', ['confirmed', 'completed'])
            ->sum('amount');

        // Client Stats
        $totalClients = Booking::where('trainer_id', $trainer->id)
            ->where('status', 'confirmed')
            ->distinct('trainee_id')
            ->count('trainee_id');

        $activeClientsThisMonth = Booking::where('trainer_id', $trainer->id)
            ->whereBetween('session_date', [$startOfMonth, $endOfMonth])
            ->where('status', 'confirmed')
            ->distinct('trainee_id')
            ->count('trainee_id');

        // Session Stats
        $totalSessions = Booking::where('trainer_id', $trainer->id)->count();
        $completedSessions = Booking::where('trainer_id', $trainer->id)
            ->where('status', 'completed')
            ->count();
        
        $todaysSessionsCount = Booking::where('trainer_id', $trainer->id)
            ->whereBetween('session_date', [$todayStart, $todayEnd])
            ->count();

        // Completion Rate
        $pastSessionsCount = Booking::where('trainer_id', $trainer->id)
            ->where('session_date', '<', $now)
            ->count();
        $completionRate = $pastSessionsCount > 0 ? round(($completedSessions / $pastSessionsCount) * 100) : 0;

        // Today's Schedule
        $todaysSchedule = Booking::where('trainer_id', $trainer->id)
            ->where('status', 'confirmed')
            ->whereBetween('session_date', [$todayStart, $todayEnd])
            ->with('trainee')
            ->orderBy('session_date', 'asc')
            ->get();

        // Recent Clients (last 4 unique clients)
        $recentClients = Booking::where('trainer_id', $trainer->id)
            ->with('trainee')
            ->orderBy('session_date', 'desc')
            ->get()
            ->unique('trainee_id')
            ->take(4);

        // Recent Earnings (last 5 completed sessions)
        $recentEarnings = Booking::where('trainer_id', $trainer->id)
            ->where('status', 'completed')
            ->with('trainee')
            ->orderBy('completed_at', 'desc')
            ->limit(5)
            ->get();

        // Performance Metrics (Merged from Analytics)
        $avgPerClient = $totalClients > 0 ? round($totalSessions / $totalClients, 1) : 0;
        $avgRevenuePerClient = $totalClients > 0 ? round($totalEarnings / $totalClients) : 0;

        $stats = [
            'total_clients' => $totalClients,
            'active_clients' => $activeClientsThisMonth,
            'total_sessions' => $totalSessions,
            'completed_sessions' => $completedSessions,
            'completion_rate' => $completionRate,
            'total_earned' => $totalEarnings - $totalWithdrawn,
            'monthly_earnings' => $monthlyEarnings,
            'upcoming_sessions' => $upcomingBookings->count(),
            'todays_sessions' => $todaysSessionsCount,
            'rating' => $trainer->rating ?? 0,
            'avg_sessions_per_client' => $avgPerClient,
            'avg_revenue_per_client' => $avgRevenuePerClient,
        ];
        
        return view('trainer.dashboard', compact(
            'stats', 
            'upcomingBookings', 
            'assignedWorkouts', 
            'todaysSchedule', 
            'recentClients', 
            'recentEarnings'
        ));
    }
    public function clients()
    {
        $clients = Booking::where('trainer_id', Auth::id())
            ->where('status', 'active')
            ->with('trainee')
            ->get();
            
        return view('trainer.clients', compact('clients'));
    }

    public function clientsApi()
    {
        $clientIds = Booking::where('trainer_id', Auth::id())
            ->whereIn('status', ['confirmed', 'completed', 'active'])
            ->pluck('trainee_id')
            ->filter()
            ->unique();

        $clients = User::whereIn('_id', $clientIds)
            ->get(['_id', 'name'])
            ->map(function($user) {
                return [
                    'id' => (string)$user->_id,
                    'name' => $user->name
                ];
            });

        return response()->json($clients);
    }
    
    public function schedule()
    {
        $bookings = Booking::where('trainer_id', Auth::id())
            ->where('status', 'confirmed')
            ->where('session_date', '>', now())
            ->with('trainee')
            ->orderBy('session_date', 'asc')
            ->paginate(20);
            
        return view('trainer.schedule', compact('bookings'));
    }
    
    public function updateProfile(Request $request)
    {
        $trainer = Auth::user();
        
        $request->validate([
            'bio' => 'nullable|string',
            'experience_years' => 'nullable|integer',
            'specialization' => 'nullable|string',
            'hourly_rate' => 'nullable|numeric',
            'certifications' => 'nullable|string',
        ]);
        
        $trainer->update($request->only(['bio', 'experience_years', 'specialization', 'hourly_rate', 'certifications']));
        
        return redirect()->back()->with('success', 'Profile updated successfully!');
    }
    public function withdrawalRequests()
    {
        $requests = WithdrawalRequest::where('trainer_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->get();
        
        $totalEarnings = $this->grossTrainerWalletAmount(Auth::id());
            
        $monthlyEarnings = Booking::where('trainer_id', Auth::id())
            ->whereBetween('session_date', [now()->startOfMonth(), now()->endOfMonth()])
            ->whereIn('status', ['confirmed', 'completed'])
            ->sum('amount');
        
        $totalWithdrawn = WithdrawalRequest::where('trainer_id', Auth::id())
            ->where('status', 'completed')
            ->get()
            ->sum('amount');
            
        $pendingAmount = WithdrawalRequest::where('trainer_id', Auth::id())
            ->where('status', 'pending')
            ->sum('amount');
            
        $hasPending = WithdrawalRequest::where('trainer_id', Auth::id())
            ->where('status', 'pending')
            ->exists();
            
        $savedUpis = WithdrawalRequest::where('trainer_id', Auth::id())
            ->whereNotNull('upi_id')
            ->distinct('upi_id')
            ->pluck('upi_id');
        
        $availableBalance = $totalEarnings - $totalWithdrawn;
        
        return view('trainer.withdrawals', compact('requests', 'totalEarnings', 'monthlyEarnings', 'totalWithdrawn', 'pendingAmount', 'availableBalance', 'hasPending', 'savedUpis'));
    }

public function requestWithdrawal(Request $request)
{
    $request->validate([
        'amount' => 'required|numeric|min:100|max:50000',
        'upi_id' => 'required|string'
    ]);
    
    $hasPending = WithdrawalRequest::where('trainer_id', Auth::id())
        ->where('status', 'pending')
        ->exists();
        
    if ($hasPending) {
        return redirect()->back()->with('error', 'You already have a pending withdrawal request. Please wait for the admin to process it.');
    }
    
    $totalEarnings = $this->grossTrainerWalletAmount(Auth::id());
    
    $totalWithdrawn = WithdrawalRequest::where('trainer_id', Auth::id())
        ->where('status', 'completed')
        ->get()
        ->sum('amount');
    
    $availableBalance = $totalEarnings - $totalWithdrawn;
    
    if ($request->amount > $availableBalance) {
        return redirect()->back()->with('error', 'Insufficient balance.');
    }
    
    WithdrawalRequest::create([
        'trainer_id' => Auth::id(),
        'amount' => (float) $request->amount,
        'upi_id' => $request->upi_id,
        'status' => 'pending'
    ]);
    
    return redirect()->back()->with('success', 'Withdrawal request submitted successfully. Admin will review it.');
}

private function grossTrainerWalletAmount(string $trainerId): float
{
    // Include all bookings: confirmed, completed, and cancelled
    $activeEarnings = Booking::where('trainer_id', $trainerId)
        ->whereIn('status', ['confirmed', 'completed', 'cancelled'])
        ->sum('amount');

    // Only deduct processed refunds (not pending)
    $processedRefunds = Booking::where('trainer_id', $trainerId)
        ->where('status', 'cancelled')
        ->where('cancellation_policy', 'trainer_refund')
        ->where('refund_status', 'processed')
        ->sum('refund_amount');

    return max(0, (float) $activeEarnings - (float) $processedRefunds);
}
}
