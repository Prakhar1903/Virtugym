<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Booking;

$bookings = Booking::with('trainer', 'trainee')->orderBy('updated_at', 'desc')->get();
echo "TOTAL BOOKINGS: " . $bookings->count() . "\n";
foreach ($bookings as $b) {
    echo sprintf(
        "ID: %s | Date: %s | Status: %s | Updated At: %s | Completed At: %s | Trainee: %s\n",
        $b->id,
        $b->session_date->toIso8601String(),
        $b->status,
        $b->updated_at ? $b->updated_at->toIso8601String() : 'N/A',
        $b->completed_at ? $b->completed_at->toIso8601String() : 'N/A',
        $b->trainee->name ?? 'None'
    );
}
