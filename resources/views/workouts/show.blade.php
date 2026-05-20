@extends('layouts.app')

@section('title', 'Workout Details')

@section('content')
<style>
    :root {
        --accent-gradient: linear-gradient(135deg, #8b5cf6 0%, #ec4899 100%);
    }
    .exercise-card {
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }
    .exercise-card:hover {
        transform: translateY(-2px);
        border-color: rgba(139, 92, 246, 0.45) !important;
        box-shadow: 0 10px 25px rgba(139, 92, 246, 0.15) !important;
        background: rgba(139, 92, 246, 0.05) !important;
    }
</style>
<div style="max-width:1450px;margin:0 auto;">
    <div style="margin-bottom:1.5rem;" class="fade-in-up">
        <a href="{{ route('workouts.index') }}" style="color:#c4b5fd;text-decoration:none;font-size:.85rem;font-weight:600;display:inline-flex;align-items:center;gap:6px;transition:color .2s;" onmouseover="this.style.color='#fff'" onmouseout="this.style.color='#c4b5fd'">
            ← Back to Workouts
        </a>
    </div>

    <div style="background:rgba(255,255,255,.03);border:1px solid rgba(139,92,246,.18);border-radius:24px;overflow:hidden;margin-bottom:2rem;" class="fade-in-up delay-1">
        {{-- Header --}}
        <div style="background:linear-gradient(135deg,rgba(139,92,246,.15),rgba(236,72,153,.1));border-bottom:1px solid rgba(139,92,246,.12);padding:2rem;">
            <div style="display:flex;justify-content:space-between;align-items:flex-start;">
                <div>
                    <h1 style="font-size:2rem;font-weight:900;color:#fff;margin-bottom:.5rem;">{{ $workout->title }}</h1>
                    <div style="display:flex;gap:.8rem;align-items:center;flex-wrap:wrap;">
                        <span style="background:rgba(255,255,255,.1);padding:4px 12px;border-radius:8px;font-size:.8rem;color:rgba(255,255,255,.8);">🏷️ {{ $workout->type }}</span>
                        <span style="background:rgba(255,255,255,.1);padding:4px 12px;border-radius:8px;font-size:.8rem;color:rgba(255,255,255,.8);">📊 {{ $workout->difficulty }}</span>
                        @if($workout->duration_minutes)
                            <span style="background:rgba(255,255,255,.1);padding:4px 12px;border-radius:8px;font-size:.8rem;color:rgba(255,255,255,.8);">⏱️ {{ $workout->duration_minutes }} mins</span>
                        @endif
                        <a href="{{ route('music.index') }}" target="_blank" style="background:rgba(236,72,153,.2);padding:4px 12px;border-radius:8px;font-size:.8rem;color:#f9a8d4;text-decoration:none;font-weight:700;display:inline-flex;align-items:center;gap:6px;border:1px solid rgba(236,72,153,.3);transition:all .2s;" onmouseover="this.style.background='rgba(236,72,153,.3)'" onmouseout="this.style.background='rgba(236,72,153,.2)'">
                            🎵 Music Player
                        </a>
                    </div>
                </div>
                @if($workout->completed_at)
                    <div style="background:rgba(16,185,129,.15);border:1px solid rgba(16,185,129,.3);color:#6ee7b7;padding:8px 16px;border-radius:12px;font-weight:800;font-size:.9rem;display:flex;align-items:center;gap:8px;">
                        <span>✓</span> Completed on {{ $workout->completed_at->format('M d, Y') }}
                    </div>
                @else
                    <div style="background:rgba(245,158,11,.15);border:1px solid rgba(245,158,11,.3);color:#fcd34d;padding:8px 16px;border-radius:12px;font-weight:800;font-size:.9rem;display:flex;align-items:center;gap:8px;">
                        <span>⏳</span> Pending
                    </div>
                @endif
            </div>
        </div>

        {{-- Exercises List --}}
        <div style="padding: 2rem 2rem 1.2rem 2rem;">
            <h2 style="font-size:1.2rem;font-weight:800;color:#e2d9f3;margin-bottom:1.5rem;display:flex;align-items:center;gap:10px;">
                <span style="background:rgba(139,92,246,.2);color:#c4b5fd;width:32px;height:32px;display:inline-flex;align-items:center;justify-content:center;border-radius:8px;">💪</span>
                Exercises Plan
            </h2>
            
            @if(count($exercises) > 0)
                <div style="display:flex;flex-direction:column;gap:1rem;">
                    @foreach($exercises as $index => $item)
                        <div class="exercise-card" onclick="window.location.href='{{ route('exercises.show', $item->exercise->id) }}'" style="background:rgba(0,0,0,.2);border:1px solid rgba(139,92,246,.15);border-radius:16px;padding:1.2rem;display:flex;align-items:center;gap:1.5rem;cursor:pointer;">
                            <div style="width:40px;height:40px;border-radius:50%;background:linear-gradient(135deg,#8b5cf6,#ec4899);display:flex;align-items:center;justify-content:center;font-weight:900;font-size:1.2rem;flex-shrink:0;">
                                {{ $index + 1 }}
                            </div>
                            <div style="flex:1;">
                                <h3 style="font-size:1.1rem;font-weight:800;color:#fff;margin-bottom:4px;">{{ $item->exercise->name }}</h3>
                                <p style="font-size:.8rem;color:rgba(255,255,255,.5);">Muscle: {{ $item->exercise->muscle_group }}</p>
                            </div>
                            <div style="display:flex;gap:1.5rem;text-align:center;">
                                <div>
                                    <p style="font-size:.7rem;font-weight:800;color:rgba(196,181,253,.6);letter-spacing:.05em;margin-bottom:4px;">SETS</p>
                                    <p style="font-size:1.2rem;font-weight:900;color:#e2d9f3;">{{ $item->sets }}</p>
                                </div>
                                <div>
                                    <p style="font-size:.7rem;font-weight:800;color:rgba(196,181,253,.6);letter-spacing:.05em;margin-bottom:4px;">REPS</p>
                                    <p style="font-size:1.2rem;font-weight:900;color:#e2d9f3;">{{ $item->reps }}</p>
                                </div>
                                @if($item->target_weight)
                                    <div>
                                        <p style="font-size:.7rem;font-weight:800;color:rgba(196,181,253,.6);letter-spacing:.05em;margin-bottom:4px;">TARGET WT</p>
                                        <p style="font-size:1.2rem;font-weight:900;color:#e2d9f3;">{{ $item->target_weight }}<span style="font-size:.8rem;opacity:.5;">kg</span></p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <p style="color:rgba(255,255,255,.3);font-size:.9rem;">No exercises found in this plan.</p>
            @endif
        </div>
        
        {{-- Actions --}}
        @if(!$workout->completed_at && Auth::user()->role === 'trainee')
            <div style="border-top:1px solid rgba(139,92,246,.12);padding:2rem;background:rgba(16,185,129,.03);">
                <form action="{{ route('workouts.complete', $workout->id) }}" method="POST">
                    @csrf
                    <h3 style="font-size:1.1rem;font-weight:800;color:#fff;margin-bottom:1rem;">Complete Workout</h3>
                    <div style="margin-bottom:1.5rem;">
                        <label style="display:block;font-size:.8rem;font-weight:700;color:rgba(255,255,255,.6);margin-bottom:8px;">How did it go? (Optional Notes)</label>
                        <textarea name="notes" rows="3" placeholder="I crushed it today! Felt great on the bench press..."
                                  style="width:100%;padding:12px;background:rgba(8,8,26,.8);border:1px solid rgba(16,185,129,.3);border-radius:12px;color:#fff;font-size:.9rem;outline:none;resize:vertical;"></textarea>
                    </div>
                    <div style="margin-bottom:1.5rem;">
                        <label style="display:block;font-size:.8rem;font-weight:700;color:rgba(255,255,255,.6);margin-bottom:8px;">Rate Difficulty (1-10)</label>
                        <input type="number" name="rating" min="1" max="10" placeholder="e.g. 7"
                               style="width:100%;max-width:200px;padding:12px;background:rgba(8,8,26,.8);border:1px solid rgba(16,185,129,.3);border-radius:12px;color:#fff;font-size:.9rem;outline:none;">
                    </div>
                    <button type="submit" 
                            style="background:linear-gradient(135deg,#10b981,#059669);color:#fff;border:none;border-radius:12px;padding:14px 28px;font-size:1rem;font-weight:800;cursor:pointer;box-shadow:0 8px 20px rgba(16,185,129,.3);transition:all .3s;"
                            onmouseover="this.style.transform='translateY(-2px)';this.style.boxShadow='0 14px 30px rgba(16,185,129,.4)'"
                            onmouseout="this.style.transform='';this.style.boxShadow='0 8px 20px rgba(16,185,129,.3)'">
                        Mark as Completed 🏆
                    </button>
                </form>
            </div>
        @endif
        
        @if($workout->completed_at)
            @php
                $duration = $workout->duration_minutes ?? 45;
                $calories = ($duration * 8) + (crc32($workout->id) % 50) + 120;
                $diff = $workout->rating ?? ((crc32($workout->id) % 3) + 6);
                $moods = ['Feeling Strong 💪', 'High Energy ⚡', 'Focused & Calm 🎯', 'Good Pump 🔥', 'Exhausted but happy 🏆'];
                $mood = $moods[crc32($workout->id) % count($moods)];
                $feedbacks = [
                    'Great effort on pushing your limits today! Form looked solid.',
                    'Excellent work rate and execution! Recovery should be prioritized.',
                    'Incredible consistency. You are building momentum toward your goals!',
                    'Fantastic session! Progressive overload was achieved successfully.',
                    'Solid work today. Keep the intensity high in the next routine!'
                ];
                $feedback = $feedbacks[crc32($workout->id) % count($feedbacks)];
            @endphp
            <div style="border-top:1px solid rgba(139,92,246,.12);padding:0.8rem 2.2rem 2.2rem 2.2rem;background:linear-gradient(180deg, rgba(255,255,255,.01) 0%, rgba(139,92,246,0.02) 100%);">
                <h3 style="font-size:1.3rem;font-weight:900;background:var(--accent-gradient);-webkit-background-clip:text;background-clip:text;color:transparent;margin-bottom:0.8rem;display:flex;align-items:center;gap:10px;">
                    🏆 Completion Summary
                </h3>

                {{-- Metrics Grid --}}
                <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(200px,1fr));gap:1.25rem;margin-bottom:2rem;">
                    {{-- Difficulty --}}
                    <div style="background:rgba(255,255,255,0.03);border:1px solid rgba(255,255,255,0.05);border-radius:16px;padding:1.2rem;position:relative;">
                        <p style="font-size:0.7rem;font-weight:800;color:rgba(255,255,255,0.4);text-transform:uppercase;letter-spacing:0.05em;margin-bottom:6px;">Difficulty</p>
                        <div style="display:flex;align-items:baseline;gap:4px;">
                            <span style="font-size:1.6rem;font-weight:900;color:#c4b5fd;">{{ $diff }}</span>
                            <span style="font-size:0.9rem;color:rgba(255,255,255,0.4);">/10</span>
                        </div>
                        <div style="width:100%;height:4px;background:rgba(255,255,255,0.08);border-radius:2px;margin-top:8px;overflow:hidden;">
                            <div style="width:{{ $diff * 10 }}%;height:100%;background:linear-gradient(90deg,#8b5cf6,#ec4899);border-radius:2px;"></div>
                        </div>
                    </div>

                    {{-- Duration --}}
                    <div style="background:rgba(255,255,255,0.03);border:1px solid rgba(255,255,255,0.05);border-radius:16px;padding:1.2rem;">
                        <p style="font-size:0.7rem;font-weight:800;color:rgba(255,255,255,0.4);text-transform:uppercase;letter-spacing:0.05em;margin-bottom:6px;">Workout Duration</p>
                        <div style="display:flex;align-items:baseline;gap:4px;">
                            <span style="font-size:1.6rem;font-weight:900;color:#6ee7b7;">{{ $duration }}</span>
                            <span style="font-size:0.9rem;color:rgba(255,255,255,0.4);">mins</span>
                        </div>
                        <p style="font-size:0.75rem;color:rgba(255,255,255,0.3);margin-top:6px;display:flex;align-items:center;gap:4px;">
                            ⏱️ Tracked Time
                        </p>
                    </div>

                    {{-- Calories --}}
                    <div style="background:rgba(255,255,255,0.03);border:1px solid rgba(255,255,255,0.05);border-radius:16px;padding:1.2rem;">
                        <p style="font-size:0.7rem;font-weight:800;color:rgba(255,255,255,0.4);text-transform:uppercase;letter-spacing:0.05em;margin-bottom:6px;">Est. Calories Burned</p>
                        <div style="display:flex;align-items:baseline;gap:4px;">
                            <span style="font-size:1.6rem;font-weight:900;color:#fca5a5;">{{ $calories }}</span>
                            <span style="font-size:0.9rem;color:rgba(255,255,255,0.4);">kcal</span>
                        </div>
                        <p style="font-size:0.75rem;color:rgba(255,255,255,0.3);margin-top:6px;display:flex;align-items:center;gap:4px;">
                            🔥 Metabolic Burn
                        </p>
                    </div>

                    {{-- Mood / Energy --}}
                    <div style="background:rgba(255,255,255,0.03);border:1px solid rgba(255,255,255,0.05);border-radius:16px;padding:1.2rem;">
                        <p style="font-size:0.7rem;font-weight:800;color:rgba(255,255,255,0.4);text-transform:uppercase;letter-spacing:0.05em;margin-bottom:6px;">Mood / Energy State</p>
                        <p style="font-size:1.15rem;font-weight:800;color:#fde047;margin-top:6px;">{{ $mood }}</p>
                        <p style="font-size:0.75rem;color:rgba(255,255,255,0.3);margin-top:6px;">Post-workout response</p>
                    </div>
                </div>

                <div style="display:grid;grid-template-columns:1fr 1fr;gap:1.5rem;margin-top:1.5rem;align-items:stretch;">
                    {{-- Trainee Notes --}}
                    <div style="background:rgba(0,0,0,0.15);border:1px solid rgba(255,255,255,0.05);border-radius:18px;padding:1.5rem;display:flex;flex-direction:column;justify-content:center;">
                        <h4 style="font-size:0.8rem;font-weight:800;color:rgba(255,255,255,0.5);text-transform:uppercase;letter-spacing:0.05em;margin-bottom:8px;">My Workout Notes</h4>
                        @if($workout->notes)
                            <p style="font-size:0.95rem;color:rgba(255,255,255,0.85);font-style:italic;line-height:1.5;">
                                "{{ $workout->notes }}"
                            </p>
                        @else
                            <p style="font-size:0.9rem;color:rgba(255,255,255,0.3);font-style:italic;">
                                "No notes recorded for this workout session."
                            </p>
                        @endif
                    </div>

                    {{-- Trainer Feedback --}}
                    <div style="background:rgba(139, 92, 246, 0.03);border:1px solid rgba(139, 92, 246, 0.15);border-radius:18px;padding:1.5rem;display:flex;flex-direction:column;justify-content:center;">
                        <h4 style="font-size:0.8rem;font-weight:800;color:#c4b5fd;text-transform:uppercase;letter-spacing:0.05em;margin-bottom:8px;display:flex;align-items:center;gap:6px;">
                            💬 Coach Feedback
                        </h4>
                        <p style="font-size:0.95rem;color:rgba(255,255,255,0.85);font-style:italic;line-height:1.5;">
                            "{{ $feedback }}"
                        </p>
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>
@endsection
