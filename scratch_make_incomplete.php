<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Booking;
use Carbon\Carbon;

$now = Carbon::now('Asia/Kolkata');

// Find any bookings in the future (or starting from tomorrow) that might be marked completed
$bookings = Booking::where('session_date', '>', $now)
    ->where('status', 'completed')
    ->get();

if ($bookings->isEmpty()) {
    echo "No future completed bookings found in the database (they might have already been reset or remained confirmed).\n";
    // Let's also check if there is any booking starting tomorrow specifically, and make sure it is confirmed
    $tomorrowBookings = Booking::where('session_date', '>=', $now->copy()->startOfDay()->addDay())
        ->where('session_date', '<=', $now->copy()->endOfDay()->addDay())
        ->get();
    foreach ($tomorrowBookings as $tb) {
        if ($tb->status !== 'confirmed') {
            $tb->update([
                'status' => 'confirmed',
                'completed_at' => null
            ]);
            echo "Reverted booking {$tb->id} (starts tomorrow) back to confirmed.\n";
        } else {
            echo "Booking {$tb->id} (starts tomorrow) is already confirmed.\n";
        }
    }
} else {
    foreach ($bookings as $b) {
        $b->update([
            'status' => 'confirmed',
            'completed_at' => null
        ]);
        echo "Successfully reverted future booking {$b->id} (session date: {$b->session_date->toIso8601String()}) back to confirmed.\n";
    }
}
