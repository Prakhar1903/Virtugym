@php
    $statusText = 'Scheduled';
    $statusClass = 'scheduled';
    $statusIcon = '📅';
    
    if ($workout->completed_at) {
        $statusText = 'Completed';
        $statusClass = 'completed';
        $statusIcon = '✓';
    } else {
        $scheduledDate = $workout->scheduled_date ? \Carbon\Carbon::parse($workout->scheduled_date) : null;
        if ($scheduledDate) {
            if ($scheduledDate->isPast() && !$scheduledDate->isToday()) {
                $statusText = 'Missed';
                $statusClass = 'missed';
                $statusIcon = '⚠️';
            } elseif ($scheduledDate->isToday()) {
                $statusText = 'In Progress';
                $statusClass = 'in-progress';
                $statusIcon = '🔥';
            }
        }
    }
@endphp

<div class="workout-card {{ !$workout->assigned_by ? 'custom-card' : '' }}" data-type="{{ strtolower($workout->type) }}" data-id="{{ $workout->id }}">
    <div>
        <div style="display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:0.75rem;">
            <div style="display:flex;gap:8px;align-items:center;flex-wrap:wrap;">
                <span class="difficulty-badge diff-{{ strtolower($workout->difficulty) }}">{{ $workout->difficulty }}</span>
                
                {{-- Dynamic Status Badge --}}
                <span class="status-badge status-{{ $statusClass }}">
                    {{ $statusIcon }} {{ $statusText }}
                </span>
                
                {{-- Custom Routine Badge (Trainee only) --}}
                @if(!$workout->assigned_by && Auth::user()->role === 'trainee')
                    <span style="font-size:0.65rem;font-weight:800;color:#f472b6;background:rgba(236,72,153,0.15);padding:4px 10px;border-radius:50px;border:1px solid rgba(236,72,153,0.25);display:inline-flex;align-items:center;gap:4px;">
                        👤 Custom Routine
                    </span>
                @endif
                
                {{-- Assigned Status Badge (Trainer only) --}}
                @if(Auth::user()->role === 'trainer')
                    @if($workout->trainee_id)
                        <span style="font-size:0.65rem;font-weight:700;color:#10b981;background:rgba(16,185,129,0.1);padding:4px 10px;border-radius:50px;border:1px solid rgba(16,185,129,0.2);">
                            Assigned to {{ $workout->trainee->name ?? 'Client' }}
                        </span>
                    @else
                        <span style="font-size:0.65rem;font-weight:700;color:rgba(255,255,255,0.4);background:rgba(255,255,255,0.05);padding:4px 10px;border-radius:50px;border:1px solid rgba(255,255,255,0.1);">
                            Unassigned
                        </span>
                    @endif
                @endif
            </div>

            @if($showActions ?? true)
            <div style="display: flex; gap: 8px;">
                @if(!(Auth::user()->role === 'trainer' && $workout->completed_at))
                <a href="{{ route('workouts.edit', $workout->id) }}" 
                   class="workout-action-btn edit-btn"
                   data-tooltip="Edit Workout">
                    <i data-lucide="edit-3" style="width: 14px; height: 14px;"></i>
                </a>
                @endif
                <form id="delete-form-{{ $workout->id }}" action="{{ route('workouts.destroy', $workout->id) }}" method="POST" style="display: none;">
                    @csrf @method('DELETE')
                </form>
                <button type="button" 
                        class="workout-action-btn delete-btn"
                        data-tooltip="Delete Workout"
                        onclick="deleteWorkout('{{ $workout->id }}')">
                    <i data-lucide="trash-2" style="width: 14px; height: 14px;"></i>
                </button>
            </div>
            @endif
        </div>
        
        <h4 style="font-size: 1.15rem; font-weight: 800; color: #fff; margin-bottom: 6px;">{{ $workout->title }}</h4>
        <p style="color: rgba(255,255,255,0.4); font-size: 0.8rem; margin-bottom: 0.75rem; display: flex; align-items: center; gap: 6px;">
            <i data-lucide="target" style="width: 14px; height: 14px;"></i>
            @php
                $muscles = [];
                if(is_array($workout->exercises)) {
                    foreach($workout->exercises as $ex) {
                        $exercise = \App\Models\Exercise::find($ex['exercise_id']);
                        if($exercise && !in_array($exercise->muscle_group, $muscles)) $muscles[] = $exercise->muscle_group;
                    }
                }
                echo !empty($muscles) ? implode(', ', array_slice($muscles, 0, 3)) . (count($muscles) > 3 ? '...' : '') : 'General Fitness';
            @endphp
        </p>
    </div>

    <div>
        <div style="display: flex; gap: 15px; margin-bottom: 0.8rem; padding-top: 0.8rem; border-top: 1px solid var(--glass-border);">
            <div style="display: flex; align-items: center; gap: 5px;">
                <i data-lucide="clock" style="width: 14px; height: 14px; color: rgba(255,255,255,0.4);"></i>
                <span style="font-size: 0.8rem; color: #fff; font-weight: 600;">{{ $workout->duration_minutes ?? '45' }}m</span>
            </div>
            <div style="display: flex; align-items: center; gap: 5px;">
                <i data-lucide="dumbbell" style="width: 14px; height: 14px; color: rgba(255,255,255,0.4);"></i>
                <span style="font-size: 0.8rem; color: #fff; font-weight: 600;">{{ count($workout->exercises ?? []) }} exercises</span>
            </div>
        </div>
        
        <a href="{{ route('workouts.show', $workout->id) }}" class="btn-outline" style="width: 100%; justify-content: center; background: rgba(255, 255, 255, 0.02); border-color: rgba(255, 255, 255, 0.08); color: rgba(255, 255, 255, 0.7); font-size: 0.8rem; padding: 8px 16px; transition: all 0.2s;" onmouseover="this.style.background='rgba(139, 92, 246, 0.08)'; this.style.borderColor='rgba(139, 92, 246, 0.3)'; this.style.color='#a78bfa';" onmouseout="this.style.background='rgba(255, 255, 255, 0.02)'; this.style.borderColor='rgba(255, 255, 255, 0.08)'; this.style.color='rgba(255, 255, 255, 0.7)';">
            @if(Auth::user()->role === 'trainer')
                View Details <i data-lucide="eye" style="width: 14px; height: 14px; margin-left: 4px;"></i>
            @elseif($workout->completed_at)
                View Summary <i data-lucide="eye" style="width: 14px; height: 14px; margin-left: 4px;"></i>
            @else
                Start Workout <i data-lucide="arrow-right" style="width: 14px; height: 14px; margin-left: 4px;"></i>
            @endif
        </a>
    </div>
</div>
