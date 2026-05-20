<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Workout;
use App\Models\Exercise;
use App\Models\ExerciseLog;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WorkoutCompletionTest extends TestCase
{
    use RefreshDatabase;

    public function test_completing_workout_generates_exercise_logs(): void
    {
        $user = User::factory()->create();

        // Create an exercise
        $exercise = Exercise::create([
            'name' => 'Barbell Bench Press',
            'category' => 'Strength',
            'muscle_group' => 'Chest',
            'equipment' => 'Barbell',
            'difficulty' => 'Intermediate',
            'instructions' => 'Lie on bench, lower bar to chest, press up',
            'tips' => 'Keep elbows at 45 degrees'
        ]);

        // Create a workout plan
        $workout = Workout::create([
            'user_id' => $user->id,
            'trainee_id' => $user->id,
            'title' => 'Chest and Arms',
            'type' => 'Strength',
            'difficulty' => 'Intermediate',
            'duration_minutes' => 60,
            'exercises' => [
                [
                    'exercise_id' => (string) $exercise->id,
                    'sets' => 4,
                    'reps' => 10,
                    'target_weight' => 80.0
                ]
            ],
            'scheduled_date' => now()
        ]);

        $this->assertNull($workout->completed_at);
        $this->assertEquals(0, ExerciseLog::where('user_id', $user->id)->count());

        // Perform the complete workout route POST request
        $response = $this->actingAs($user)->post("/workouts/{$workout->id}/complete", [
            'notes' => 'Felt super strong today!',
            'rating' => 8
        ]);

        $response->assertRedirect("/workouts/{$workout->id}");
        $response->assertSessionHas('success');

        // Assert workout is updated
        $workout->refresh();
        $this->assertNotNull($workout->completed_at);
        $this->assertEquals('Felt super strong today!', $workout->notes);
        $this->assertEquals(8, $workout->rating);
        $this->assertEquals(40, $workout->total_reps);

        // Assert ExerciseLog is generated automatically
        $logs = ExerciseLog::where('user_id', $user->id)->get();
        $this->assertCount(1, $logs);
        
        $log = $logs->first();
        $this->assertEquals((string) $exercise->id, $log->exercise_id);
        $this->assertEquals('Barbell Bench Press', $log->exercise_name);
        $this->assertEquals(4, $log->sets);
        
        // Assert array representations for reps and weights
        $this->assertEquals([10, 10, 10, 10], $log->reps);
        $this->assertEquals([80.0, 80.0, 80.0, 80.0], $log->weight);
        $this->assertEquals(8, $log->rpe);
        $this->assertEquals('Felt super strong today!', $log->notes);
    }
}
