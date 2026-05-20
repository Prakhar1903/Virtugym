<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TraineeAnalyticsTest extends TestCase
{
    use RefreshDatabase;

    public function test_unauthenticated_user_cannot_access_analytics(): void
    {
        $response = $this->get('/analytics');
        $response->assertRedirect('/login');
    }

    public function test_authenticated_trainee_can_access_analytics_page(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/analytics');

        $response->assertStatus(200);
        $response->assertViewIs('analytics.trainee');
        $response->assertViewHasAll([
            'totalWorkouts', 'completedWorkouts', 'completionRate',
            'totalReps', 'avgDuration', 'workoutFrequency', 'repsOverTime',
            'durationTrend', 'muscleBreakdown', 'bestDays', 'comparison',
            'consistencyScore', 'dayLabels', 'filter', 'achievements',
            'aiInsights', 'favType', 'mostTrainedMuscle', 'avgRating'
        ]);
    }

    public function test_analytics_handles_filter_query_parameters(): void
    {
        $user = User::factory()->create();

        // Test Monthly filter
        $responseMonthly = $this->actingAs($user)->get('/analytics?filter=monthly');
        $responseMonthly->assertStatus(200);
        $this->assertEquals('monthly', $responseMonthly->viewData('filter'));

        // Test Yearly filter
        $responseYearly = $this->actingAs($user)->get('/analytics?filter=yearly');
        $responseYearly->assertStatus(200);
        $this->assertEquals('yearly', $responseYearly->viewData('filter'));
    }
}
