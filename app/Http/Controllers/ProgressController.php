<?php

namespace App\Http\Controllers;

use App\Models\ProgressMetric;
use App\Models\ProgressPhoto;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;

class ProgressController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $metrics = ProgressMetric::where('user_id', $user->id)
            ->orderBy('date', 'desc')
            ->limit(12)
            ->get();

        $latest = $metrics->first();
        $previous = $metrics->skip(1)->first();
        $history = $metrics->reverse()->values();

        // Retrieve database-backed progress photos
        $photos = ProgressPhoto::where('user_id', $user->id)
            ->orderBy('week_number', 'asc')
            ->get();

        $firstPhoto = $photos->first();
        $latestPhoto = $photos->last();

        return view('progress.index', compact('user', 'metrics', 'latest', 'previous', 'history', 'photos', 'firstPhoto', 'latestPhoto'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'date' => ['required', 'date', 'before_or_equal:today'],
            'weight' => ['required', 'numeric', 'min:20', 'max:300'],
            'height' => ['nullable', 'numeric', 'min:50', 'max:250'],
            'body_fat_percentage' => ['nullable', 'numeric', 'min:3', 'max:80'],
            'muscle_mass' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'chest' => ['nullable', 'numeric', 'min:20', 'max:250'],
            'waist' => ['nullable', 'numeric', 'min:20', 'max:250'],
            'hips' => ['nullable', 'numeric', 'min:20', 'max:250'],
            'biceps' => ['nullable', 'numeric', 'min:10', 'max:100'],
            'arms' => ['nullable', 'numeric', 'min:10', 'max:100'],
            'thighs' => ['nullable', 'numeric', 'min:20', 'max:150'],
            'notes' => ['nullable', 'string', 'max:1000'],
            'progress_photo' => ['nullable', 'image', 'max:2048'],
            'target_weight' => ['nullable', 'numeric', 'min:20', 'max:300'],
            'target_body_fat' => ['nullable', 'numeric', 'min:3', 'max:80'],
        ]);

        $user = Auth::user();
        
        if ($request->has('height')) {
            $user->height = $validated['height'];
        }
        if ($request->has('target_weight')) {
            $user->target_weight = $validated['target_weight'];
        }
        if ($request->has('target_body_fat')) {
            $user->target_body_fat = $validated['target_body_fat'];
        }

        if ($user->isDirty()) {
            $user->save();
        }

        $heightMeters = $user->height ? ((float) $user->height / 100) : null;
        $weight = (float) $validated['weight'];

        if ($heightMeters && $weight) {
            $validated['bmi'] = round($weight / ($heightMeters * $heightMeters), 1);
        }

        if ($request->hasFile('progress_photo')) {
            $path = $request->file('progress_photo')->store('progress_photos', 'public');
            $validated['progress_photo'] = $path;

            // Auto-calculate week number and save database-backed progress photo
            $week = ProgressPhoto::where('user_id', $user->id)->count() + 1;
            ProgressPhoto::create([
                'user_id' => $user->id,
                'photo' => $path,
                'weight' => $weight,
                'week_number' => $week,
            ]);
        }

        $metricDate = Carbon::parse($validated['date'])->startOfDay();
        $entry = ProgressMetric::where('user_id', $user->id)
            ->whereDate('date', $metricDate->toDateString())
            ->first();

        if ($entry) {
            $entry->update($validated + ['date' => $metricDate]);
        } else {
            ProgressMetric::create($validated + [
                'user_id' => $user->id,
                'date' => $metricDate,
            ]);
        }

        if ($weight) {
            $user->weight = $weight;
            $user->save();
        }

        return Redirect::route('progress.index')->with('success', 'Progress entry saved.');
    }
}
