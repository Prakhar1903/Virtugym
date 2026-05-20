<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Booking;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BookingCompletionValidationTest extends TestCase
{
    use RefreshDatabase;

    private $trainer;
    private $trainee;

    protected function setUp(): void
    {
        parent::setUp();

        $this->trainer = User::factory()->create([
            'role' => 'trainer',
            'email' => 'trainer@example.com'
        ]);

        $this->trainee = User::factory()->create([
            'role' => 'trainee',
            'email' => 'trainee@example.com'
        ]);
    }

    public function test_trainer_cannot_mark_future_booking_completed(): void
    {
        // Future booking: starting tomorrow
        $booking = Booking::create([
            'trainer_id' => $this->trainer->id,
            'trainee_id' => $this->trainee->id,
            'session_date' => now()->addDay()->format('Y-m-d H:i:s'),
            'duration_minutes' => 60,
            'amount' => 500.0,
            'status' => 'confirmed'
        ]);

        $response = $this->actingAs($this->trainer)->put(route('bookings.update', $booking->id), [
            'status' => 'completed'
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('error', 'You cannot mark a future session as completed.');

        $booking->refresh();
        $this->assertEquals('confirmed', $booking->status);
    }

    public function test_trainer_can_mark_past_booking_completed(): void
    {
        // Past booking: starting yesterday
        $booking = Booking::create([
            'trainer_id' => $this->trainer->id,
            'trainee_id' => $this->trainee->id,
            'session_date' => now()->subDay()->format('Y-m-d H:i:s'),
            'duration_minutes' => 60,
            'amount' => 500.0,
            'status' => 'confirmed'
        ]);

        $response = $this->actingAs($this->trainer)->put(route('bookings.update', $booking->id), [
            'status' => 'completed'
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success', 'Booking status updated!');

        $booking->refresh();
        $this->assertEquals('completed', $booking->status);
        $this->assertNotNull($booking->completed_at);
    }

    public function test_trainer_bulk_complete_skips_future_bookings(): void
    {
        // Future booking
        $futureBooking = Booking::create([
            'trainer_id' => $this->trainer->id,
            'trainee_id' => $this->trainee->id,
            'session_date' => now()->addDay()->format('Y-m-d H:i:s'),
            'duration_minutes' => 60,
            'amount' => 500.0,
            'status' => 'confirmed'
        ]);

        // Past booking
        $pastBooking = Booking::create([
            'trainer_id' => $this->trainer->id,
            'trainee_id' => $this->trainee->id,
            'session_date' => now()->subDay()->format('Y-m-d H:i:s'),
            'duration_minutes' => 60,
            'amount' => 500.0,
            'status' => 'confirmed'
        ]);

        $response = $this->actingAs($this->trainer)->post(route('bookings.bulk-complete'), [
            'booking_ids' => [(string)$futureBooking->id, (string)$pastBooking->id]
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');
        $this->assertStringContainsString('Successfully marked 1 sessions as completed!', session('success'));
        $this->assertStringContainsString('1 future sessions were skipped.', session('success'));

        $futureBooking->refresh();
        $pastBooking->refresh();

        $this->assertEquals('confirmed', $futureBooking->status);
        $this->assertEquals('completed', $pastBooking->status);
    }
}
