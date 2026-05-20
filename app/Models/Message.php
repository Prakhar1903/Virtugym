<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;

class Message extends Model
{
    protected $connection = 'mongodb';
    protected $collection = 'messages';
    
    protected $fillable = [
        'sender_id', 'receiver_id', 'message', 'is_read',
        'booking_id', 'read_at', 'created_at', 'updated_at',
        'attachment_path', 'attachment_name', 'attachment_type'
    ];
    
    protected $casts = [
        'is_read' => 'boolean',
        'read_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];
    
    public function sender()
    {
        return $this->belongsTo(User::class, 'sender_id');
    }
    
    public function receiver()
    {
        return $this->belongsTo(User::class, 'receiver_id');
    }
    
    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }
}
