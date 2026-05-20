<?php

namespace App\Models;

use MongoDB\Laravel\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;
    
    protected $connection = 'mongodb';
    protected $collection = 'users';
    
    protected $fillable = [
        'name', 'email', 'password', 'role',  // Add role field
        'age', 'gender', 'weight', 'height', 'dob',
        'fitness_level', 'goal', 'equipment', 
        'workout_days', 'workout_duration', 'injuries',
        // Trainer specific fields
        'bio', 'experience_years', 'specialization', 'hourly_rate', 
        'certifications', 'is_verified', 'rating', 'total_clients',
        'profile_photo', 'languages', 'session_types', 'portfolio_link',
        // Payment fields
        'razorpay_id', 'stripe_id', 'upi_id',
        'activity_visit_dates',
        // Goal tracking
        'target_weight', 'target_body_fat', 'daily_water_goal'
    ];
    
    protected $hidden = [
        'password', 'remember_token',
    ];
    
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'equipment' => 'array',
        'session_types' => 'array',
        'age' => 'integer',
        'weight' => 'float',
        'height' => 'float',
        'workout_days' => 'integer',
        'workout_duration' => 'integer',
        'experience_years' => 'integer',
        'hourly_rate' => 'float',
        'is_verified' => 'boolean',
        'rating' => 'float',
        'total_clients' => 'integer',
        'activity_visit_dates' => 'array',
        'target_weight' => 'float',
        'target_body_fat' => 'float',
        'dob' => 'string',
        'daily_water_goal' => 'integer',
    ];
    
    public function workouts()
    {
        return $this->hasMany(Workout::class);
    }
    
    public function progressMetrics()
    {
        return $this->hasMany(ProgressMetric::class);
    }
    
    // Trainer relationships
    public function bookings()
    {
        return $this->hasMany(Booking::class, 'trainer_id');
    }
    
    public function clients()
    {
        return $this->hasMany(Booking::class, 'trainer_id')->where('status', 'active');
    }
    
    // Trainee relationships
    public function myTrainers()
    {
        return $this->hasMany(Booking::class, 'trainee_id')->where('status', 'active');
    }
    
    public function payments()
    {
        return $this->hasMany(Payment::class);
    }
}
