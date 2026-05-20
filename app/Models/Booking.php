<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;

class Booking extends Model
{
    protected $connection = 'mongodb';
    protected $collection = 'bookings';
    
    protected $fillable = [
        'trainee_id', 'trainer_id', 'session_date', 'session_time',
        'duration_minutes', 'status', 'amount', 'payment_id',
        'special_requests', 'cancelled_at', 'completed_at',
        'cancelled_by', 'cancellation_reason',
        'cancellation_policy', 'refund_status', 'refund_amount',
        'refund_reference', 'refund_requested_at', 'refund_processed_at', 'refund_error',
        'refund_upi_id', 'meeting_id', 'meeting_link', 'meeting_started', 'meeting_ended',
        'session_type', 'trainer_notes', 'trainee_joined', 'trainer_joined'
    ];
    
    protected $casts = [
        'session_date' => 'datetime',
        'amount' => 'float',
        'refund_amount' => 'float',
        'cancelled_at' => 'datetime',
        'completed_at' => 'datetime',
        'refund_requested_at' => 'datetime',
        'refund_processed_at' => 'datetime',
        'meeting_started' => 'boolean',
        'meeting_ended' => 'boolean',
        'trainee_joined' => 'boolean',
        'trainer_joined' => 'boolean',
    ];
    
    // Relationships (CO6 - Eloquent ORM Relationships)
    public function trainee()
    {
        return $this->belongsTo(User::class, 'trainee_id');
    }
    
    public function trainer()
    {
        return $this->belongsTo(User::class, 'trainer_id');
    }
    
    public function payment()
    {
        return $this->belongsTo(Payment::class);
    }
}
