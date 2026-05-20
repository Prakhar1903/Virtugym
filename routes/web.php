<?php

use App\Http\Controllers\BookingController;
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Trainer\TrainerDashboardController;
use App\Http\Controllers\Trainee\TraineeDashboardController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\AnalyticsController;
use App\Http\Controllers\TrainerAvailabilityController;
use App\Http\Controllers\VideoCallController;
use App\Http\Controllers\WorkoutController;
use App\Http\Controllers\ExerciseController;
use App\Http\Controllers\ProgressController;
use App\Http\Controllers\AIController;
use App\Http\Controllers\MusicController;
use App\Http\Controllers\WaterIntakeController;
use App\Http\Controllers\MindfulnessController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('home');
})->middleware('track.activity')->name('home');

require __DIR__.'/auth.php';

// ============ AUTHENTICATED ROUTES ============
Route::middleware(['auth', 'track.activity'])->group(function () {
    
    // Dashboard Redirect
    Route::get('/dashboard', function () {
        $role = auth()->user()->role ?? 'trainee';
        if ($role === 'admin') {
            return redirect()->route('admin.dashboard');
        } elseif ($role === 'trainer') {
            return redirect()->route('trainer.dashboard');
        } else {
            return redirect()->route('trainee.dashboard');
        }
    })->name('dashboard');
    
    // Bookings
    Route::resource('bookings', BookingController::class)->only(['index', 'update']);
    Route::post('/bookings/bulk-complete', [BookingController::class, 'bulkComplete'])->name('bookings.bulk-complete');
    Route::post('/bookings/{id}/notes', [BookingController::class, 'saveNotes'])->name('bookings.save-notes');
    Route::get('/book-trainer/{id}', [BookingController::class, 'create'])->name('book.trainer.create');
    Route::post('/initiate-payment/{trainer_id}', [BookingController::class, 'initiatePayment'])->name('initiate.payment');
    Route::post('/payment-success', [BookingController::class, 'paymentSuccess'])->name('payment.success');
    Route::get('/payment-failed', [BookingController::class, 'paymentFailed'])->name('payment.failed');
    
    // Search
    Route::get('/search', [TraineeDashboardController::class, 'search'])->name('search');
    
    // Chat
    Route::get('/chat/{trainer_id?}', [ChatController::class, 'index'])->name('chat.index');
    Route::get('/chat/messages/{trainer_id}', [ChatController::class, 'getMessages'])->name('chat.messages');
    Route::post('/chat/send', [ChatController::class, 'sendMessage'])->name('chat.send');
    Route::get('/chat/unread/count', [ChatController::class, 'getUnreadCount'])->name('chat.unread');
    
    // Analytics
    Route::get('/analytics', [AnalyticsController::class, 'index'])->name('analytics.index');

    // Workouts
    Route::resource('workouts', WorkoutController::class);
    Route::post('/workouts/{id}/complete', [WorkoutController::class, 'complete'])->name('workouts.complete');
    Route::post('/workouts/use-template', [WorkoutController::class, 'useTemplate'])->name('workouts.use-template');

    // Exercises
    Route::resource('exercises', ExerciseController::class);
    Route::post('/exercises/add-to-workout', [ExerciseController::class, 'addToWorkout'])->name('exercises.add-to-workout');
    Route::get('/exercises/user-workouts/{user_id}', [ExerciseController::class, 'getUserWorkouts'])->name('exercises.user-workouts');

    // Progress
    Route::get('/progress', [ProgressController::class, 'index'])->name('progress.index');
    Route::post('/progress', [ProgressController::class, 'store'])->name('progress.store');

    // Music
    Route::get('/music', [MusicController::class, 'index'])->name('music.index');
    Route::get('/music/search', [MusicController::class, 'search'])->name('music.search');
    Route::get('/music/default-track', [MusicController::class, 'defaultTrack'])->name('music.default');
    Route::get('/music/background-track', [MusicController::class, 'backgroundTrack'])->name('music.background');
    
    // Water Intake
    Route::get('/water', [WaterIntakeController::class, 'index'])->name('water.index');
    Route::post('/water', [WaterIntakeController::class, 'store'])->name('water.store');
    Route::post('/water/goal', [WaterIntakeController::class, 'updateGoal'])->name('water.goal');
    Route::delete('/water/{id}', [WaterIntakeController::class, 'destroy'])->name('water.destroy');

    // Mindfulness & Recovery
    Route::get('/mindfulness', [MindfulnessController::class, 'index'])->name('mindfulness.index');
    Route::get('/mindfulness/{id}', [MindfulnessController::class, 'show'])->name('mindfulness.show');

    // Profile
    Route::get('/profile', [\App\Http\Controllers\ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [\App\Http\Controllers\ProfileController::class, 'update'])->name('profile.update');
    Route::post('/profile/password', [\App\Http\Controllers\ProfileController::class, 'updatePassword'])->name('profile.password');
    Route::delete('/profile', [\App\Http\Controllers\ProfileController::class, 'destroy'])->name('profile.destroy');
    
    // Notifications
    Route::get('/notifications', [\App\Http\Controllers\NotificationController::class, 'index'])->name('notifications.index');
    
    // ============ AI ROUTES ============
    Route::prefix('ai')->name('ai.')->group(function () {
        Route::get('/dashboard', [AIController::class, 'index'])->name('dashboard');
        Route::get('/live-coach', [AIController::class, 'liveCoach'])->name('live-coach');
        Route::get('/recommend-workout', [AIController::class, 'recommendWorkout'])->name('recommend-workout');
        Route::post('/analyze-form', [AIController::class, 'analyzeForm'])->name('analyze-form');
        Route::post('/generate-plan', [AIController::class, 'generatePlan'])->name('generate-plan');
        Route::post('/chat', [AIController::class, 'chat'])->name('chat');
        Route::get('/nutrition-advice', [AIController::class, 'nutritionAdvice'])->name('nutrition-advice');
        Route::get('/predict-progress', [AIController::class, 'predictProgress'])->name('predict-progress');
        Route::get('/motivation', [AIController::class, 'getMotivation'])->name('motivation');
        Route::get('/workout-summary/{id}', [AIController::class, 'workoutSummary'])->name('workout-summary');
        // Debug route - remove later
Route::get('/debug', [AIController::class, 'debug'])->name('debug');
    });
    
    // ============ ADMIN ROUTES ============
    // Admin Routes
Route::middleware(['role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');
    Route::get('/users', [AdminDashboardController::class, 'users'])->name('users');
    Route::post('/users/{id}/block', [AdminDashboardController::class, 'blockUser'])->name('users.block');
    Route::delete('/users/{id}/delete', [AdminDashboardController::class, 'deleteUser'])->name('users.delete');
    
    Route::get('/trainers', [AdminDashboardController::class, 'trainers'])->name('trainers');
    Route::post('/trainers/{id}/verify', [AdminDashboardController::class, 'verifyTrainer'])->name('trainers.verify');
    Route::post('/trainers/{id}/unverify', [AdminDashboardController::class, 'unverifyTrainer'])->name('trainers.unverify');
    Route::delete('/trainers/{id}/delete', [AdminDashboardController::class, 'deleteTrainer'])->name('trainers.delete');
    
    Route::get('/bookings', [AdminDashboardController::class, 'bookings'])->name('bookings');
    Route::post('/bookings/{id}/refund', [AdminDashboardController::class, 'processBookingRefund'])->name('bookings.refund');
    Route::delete('/bookings/{id}/delete', [AdminDashboardController::class, 'deleteBooking'])->name('bookings.delete');
    
    Route::get('/withdrawals', [AdminDashboardController::class, 'withdrawals'])->name('withdrawals');
    Route::post('/withdrawals/{id}/approve', [AdminDashboardController::class, 'approveWithdrawal'])->name('withdrawals.approve');
    Route::post('/withdrawals/{id}/reject', [AdminDashboardController::class, 'rejectWithdrawal'])->name('withdrawals.reject');
});
    
    // ============ TRAINER ROUTES ============
    Route::prefix('trainer')->name('trainer.')->middleware(['role:trainer'])->group(function () {
        Route::get('/dashboard', [TrainerDashboardController::class, 'index'])->name('dashboard');
        Route::get('/clients', [TrainerDashboardController::class, 'clients'])->name('clients');
        Route::get('/clients-api', [TrainerDashboardController::class, 'clientsApi'])->name('clients-api');
        Route::get('/schedule', [TrainerDashboardController::class, 'schedule'])->name('schedule');
        Route::post('/profile/update', [TrainerDashboardController::class, 'updateProfile'])->name('profile.update');
        
        // TRAINER AVAILABILITY ROUTES
        Route::get('/availability', [TrainerAvailabilityController::class, 'index'])->name('availability.index');
        Route::post('/availability', [TrainerAvailabilityController::class, 'store'])->name('availability.store');
        Route::put('/availability/{id}', [TrainerAvailabilityController::class, 'update'])->name('availability.update');
        Route::delete('/availability/{id}', [TrainerAvailabilityController::class, 'destroy'])->name('availability.destroy');
        Route::get('/withdrawals', [TrainerDashboardController::class, 'withdrawalRequests'])->name('withdrawals');
Route::post('/withdrawal/request', [TrainerDashboardController::class, 'requestWithdrawal'])->name('withdrawal.request');
    });
    
    // ============ TRAINEE ROUTES ============
    Route::prefix('trainee')->name('trainee.')->middleware(['role:trainee'])->group(function () {
        Route::get('/dashboard', [TraineeDashboardController::class, 'index'])->name('dashboard');
        Route::get('/trainers', [TraineeDashboardController::class, 'trainers'])->name('trainers');
        Route::get('/trainers/{id}/book', [TraineeDashboardController::class, 'bookTrainer'])->name('book-trainer');
    });
    
    // ============ AVAILABLE SLOTS API ============
    Route::get('/trainer/available-slots/{trainer_id}/{date}', [TrainerAvailabilityController::class, 'getAvailableSlots'])->name('trainer.available-slots');
    
    // ============ VIDEO CALL ROUTES ============
    Route::prefix('video-call')->name('video-call.')->group(function () {
        Route::get('/join/{booking_id}', [VideoCallController::class, 'join'])->name('join');
        Route::post('/start/{booking_id}', [VideoCallController::class, 'startMeeting'])->name('start');
        Route::post('/end/{booking_id}', [VideoCallController::class, 'endMeeting'])->name('end');
        Route::get('/session-status', [VideoCallController::class, 'sessionStatus'])->name('session-status');
    });
});
Route::get('/health', function () {
    return response()->json([
        'status' => 'ok'
    ]);
});