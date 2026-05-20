<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Workout;
use App\Models\ExerciseLog;

$workoutId = '6a0beeb0d5f8d00cd5023f22';
$workout = Workout::find($workoutId);

if (!$workout) {
    echo "Workout not found!\n";
    exit(1);
}

// 1. Delete associated ExerciseLog records
$deletedLogsCount = ExerciseLog::where('workout_id', $workoutId)->delete();
echo "Deleted {$deletedLogsCount} exercise log(s) associated with this workout.\n";

// 2. Reset the workout status fields
$workout->update([
    'completed_at' => null,
    'notes' => null,
    'rating' => null,
    'total_reps' => null
]);

echo "Successfully reverted workout '{$workout->title}' (ID: {$workoutId}) back to incomplete!\n";
