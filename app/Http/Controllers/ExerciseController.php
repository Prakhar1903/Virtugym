<?php

namespace App\Http\Controllers;

use App\Models\Exercise;
use App\Models\Workout;
use App\Models\Booking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ExerciseController extends Controller
{
    public function index(Request $request)
    {
        $query = Exercise::query();
        
        // Apply filters
        if ($request->has('search') && $request->search) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }
        
        if ($request->has('muscle_group') && $request->muscle_group) {
            $query->where('muscle_group', $request->muscle_group);
        }
        
        if ($request->has('equipment') && $request->equipment) {
            $query->where('equipment', $request->equipment);
        }
        
        if ($request->has('difficulty') && $request->difficulty) {
            $query->where('difficulty', $request->difficulty);
        }
        
        $exercises = $query->orderBy('name')->paginate(12);
        $totalCount = $query->count();
        
        // Get filter options (Native MongoDB distinct fix)
        $muscleGroups = collect(Exercise::raw()->distinct('muscle_group'))->filter()->values();
        $equipmentList = collect(Exercise::raw()->distinct('equipment'))->filter()->values();
        $difficulties = collect(Exercise::raw()->distinct('difficulty'))->filter()->values();
        
        $clients = collect();
        if (Auth::user()->role === 'trainer') {
            $clients = \App\Models\Booking::where('trainer_id', Auth::id())
                ->whereIn('status', ['confirmed', 'completed'])
                ->with('trainee')
                ->get()
                ->pluck('trainee')
                ->filter()
                ->unique(function($u) { return (string)$u->_id; })
                ->map(function($u) {
                    $u->id = (string)$u->_id;
                    return $u;
                });
        }
        
        $muscleCounts = Exercise::get(['muscle_group'])->groupBy('muscle_group')->map->count();
        
        return view('exercises.index', compact('exercises', 'muscleGroups', 'equipmentList', 'difficulties', 'totalCount', 'clients', 'muscleCounts'));
    }
    
    public function show($id)
    {
        $exercise = Exercise::findOrFail($id);
        $relatedExercises = Exercise::where('muscle_group', $exercise->muscle_group)
            ->where('id', '!=', $exercise->id)
            ->limit(3)
            ->get();
            
        return view('exercises.show', compact('exercise', 'relatedExercises'));
    }

    public function store(Request $request)
    {
        if (Auth::user()->role !== 'trainer') {
            return redirect()->back()->with('error', 'Only trainers can add exercises.');
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'muscle_group' => 'required|string',
            'equipment' => 'nullable|string',
            'description' => 'nullable|string',
        ]);

        Exercise::create([
            'name' => $request->name,
            'muscle_group' => $request->muscle_group,
            'equipment' => $request->equipment ?? 'None',
            'description' => $request->description,
            'difficulty' => 'Intermediate', // Default
            'user_id' => Auth::id(), // To track who created it if needed
        ]);

        return redirect()->route('exercises.index')->with('success', 'Custom exercise added to library! ✨');
    }

    public function addToWorkout(Request $request)
    {
        $request->validate([
            'workout_id' => 'required|string',
            'exercise_id' => 'required|string',
        ]);

        $workout = Workout::findOrFail($request->workout_id);
        $exercise = Exercise::findOrFail($request->exercise_id);

        // Security check: Ensure trainer owns the workout or trainee owns the workout
        if (Auth::user()->role === 'trainer' && $workout->trainer_id !== Auth::id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }
        if (Auth::user()->role === 'trainee' && $workout->trainee_id !== Auth::id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $exercises = $workout->exercises ?? [];
        $exercises[] = [
            'exercise_id' => (string)$exercise->id,
            'name' => $exercise->name,
            'muscle_group' => $exercise->muscle_group,
            'sets' => 3,
            'reps' => 10,
            'weight_kg' => 0,
            'rest_seconds' => 60,
            'added_at' => now()->toDateTimeString()
        ];

        $workout->update(['exercises' => $exercises]);

        return response()->json([
            'success' => true,
            'message' => 'Exercise added to ' . $workout->title,
            'workout_title' => $workout->title
        ]);
    }
    public function getUserWorkouts($userId)
    {
        try {
            if (Auth::user()->role === 'trainer') {
                // Verify trainer has a booking relationship with this client
                $isClient = Booking::where('trainer_id', Auth::id())
                    ->where('trainee_id', (string)$userId)
                    ->whereIn('status', ['confirmed', 'completed', 'active'])
                    ->exists();

                if (!$isClient) {
                    return response()->json(['error' => 'Unauthorized'], 403);
                }

                $workouts = Workout::where('trainee_id', (string)$userId)
                    ->get(['_id', 'title', 'scheduled_date']);
            } else {
                // Trainee can only see their own workouts
                if ((string)$userId !== (string)Auth::id()) {
                    return response()->json(['error' => 'Unauthorized'], 403);
                }
                $workouts = Workout::where('trainee_id', (string)Auth::id())
                    ->get(['_id', 'title', 'scheduled_date']);
            }

            // Map MongoDB _id to id for the frontend
            $formattedWorkouts = $workouts->map(function($w) {
                return [
                    'id' => (string)$w->_id,
                    'title' => $w->title,
                    'scheduled_date' => $w->scheduled_date
                ];
            });

            return response()->json($formattedWorkouts);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}