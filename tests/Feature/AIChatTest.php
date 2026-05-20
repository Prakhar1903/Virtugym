<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AIChatTest extends TestCase
{
    use RefreshDatabase;

    public function test_unauthenticated_user_cannot_access_ai_dashboard(): void
    {
        $response = $this->get('/ai/dashboard');
        $response->assertStatus(302); // Redirects to login
    }

    public function test_authenticated_user_can_access_ai_dashboard(): void
    {
        $user = User::factory()->create(['role' => 'trainee']);
        $response = $this->actingAs($user)->get('/ai/dashboard');
        $response->assertStatus(200);
        $response->assertSee('VirtuCoach');
    }

    public function test_authenticated_user_can_send_chat_message(): void
    {
        $user = User::factory()->create(['role' => 'trainee']);
        
        $response = $this->actingAs($user)->postJson('/ai/chat', [
            'message' => 'Give me a workout recommendation'
        ]);

        $response->assertStatus(200);
        $response->assertJsonPath('success', true);
        $response->assertJsonStructure([
            'success',
            'response',
            'timestamp'
        ]);
    }
}
