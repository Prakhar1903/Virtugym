<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Workout;

$workouts = Workout::orderBy('updated_at', 'desc')->get();
echo "TOTAL WORKOUTS: " . $workouts->count() . "\n";
foreach ($workouts as $w) {
    echo sprintf(
        "ID: %s | Title: %s | Scheduled: %s | Completed At: %s | Updated At: %s\n",
        $w->id,
        $w->title,
        $w->scheduled_date ? $w->scheduled_date->toIso8601String() : 'N/A',
        $w->completed_at ? $w->completed_at->toIso8601String() : 'N/A',
        $w->updated_at ? $w->updated_at->toIso8601String() : 'N/A'
    );
}
