<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;

class ProgressPhoto extends Model
{
    protected $connection = 'mongodb';
    protected $collection = 'progress_photos';

    protected $fillable = [
        'user_id',
        'photo',
        'weight',
        'week_number',
    ];

    protected $casts = [
        'weight' => 'float',
        'week_number' => 'integer',
        'created_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
