<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Message;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ChatMessageTest extends TestCase
{
    use RefreshDatabase;

    public function test_unauthenticated_user_cannot_send_messages(): void
    {
        $response = $this->postJson('/chat/send', [
            'receiver_id' => 'trainer123',
            'message' => 'Hello trainer'
        ]);

        $response->assertStatus(401);
    }

    public function test_authenticated_user_can_send_text_message(): void
    {
        $user = User::factory()->create();
        $trainer = User::factory()->create();

        $response = $this->actingAs($user)->postJson('/chat/send', [
            'receiver_id' => $trainer->id,
            'message' => 'Hello there!'
        ]);

        $response->assertStatus(200);
        $response->assertJsonPath('success', true);
        
        $this->assertDatabaseHas('messages', [
            'sender_id' => $user->id,
            'receiver_id' => $trainer->id,
            'message' => 'Hello there!'
        ], 'mongodb');
    }

    public function test_authenticated_user_can_send_message_with_attachment(): void
    {
        Storage::fake('public');

        $user = User::factory()->create();
        $trainer = User::factory()->create();
        
        $file = UploadedFile::fake()->create('workout_plan.pdf', 500, 'application/pdf');

        $response = $this->actingAs($user)->post('/chat/send', [
            'receiver_id' => $trainer->id,
            'message' => 'Please review this',
            'attachment' => $file
        ]);

        $response->assertStatus(200);
        $response->assertJsonPath('success', true);

        $message = Message::where('sender_id', $user->id)->first();
        $this->assertNotNull($message->attachment_path);
        $this->assertEquals('workout_plan.pdf', $message->attachment_name);
        $this->assertEquals('application/pdf', $message->attachment_type);

        Storage::disk('public')->assertExists($message->attachment_path);
    }
}
