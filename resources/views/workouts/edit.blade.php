@extends('layouts.app')

@section('title', 'Edit Workout')

@section('content')
<div style="max-width:960px;margin:0 auto;">
    <div style="margin-bottom:1.5rem;" class="fade-in-up">
        <a href="{{ route('workouts.show', $workout->id) }}" style="color:#c4b5fd;text-decoration:none;font-size:.85rem;font-weight:600;display:inline-flex;align-items:center;gap:6px;transition:color .2s;" onmouseover="this.style.color='#fff'" onmouseout="this.style.color='#c4b5fd'">
            ← Back to Workout
        </a>
    </div>

    <div style="background:rgba(255,255,255,.03);border:1px solid rgba(139,92,246,.18);border-radius:24px;overflow:hidden;" class="fade-in-up delay-1">
        <div style="background:linear-gradient(135deg,rgba(139,92,246,.15),rgba(236,72,153,.1));border-bottom:1px solid rgba(139,92,246,.12);padding:1.8rem 2rem;">
            <h1 style="font-size:1.6rem;font-weight:900;color:#fff;margin-bottom:.3rem;">Edit Workout</h1>
            <p style="color:rgba(255,255,255,.4);font-size:.9rem;">Update the plan details and exercise targets</p>
        </div>

        <form action="{{ route('workouts.update', $workout->id) }}" method="POST" style="padding:2rem;">
            @csrf
            @method('PUT')

            @if ($errors->any())
                <div style="background:rgba(239,68,68,0.15);border:1px solid rgba(239,68,68,0.3);border-radius:12px;padding:1.5rem;margin-bottom:2rem;">
                    <h4 style="color:#fca5a5;font-size:1rem;margin-top:0;margin-bottom:0.8rem;font-weight:700;">Please fix the following errors:</h4>
                    <ul style="color:#fecaca;margin-bottom:0;font-size:0.85rem;padding-left:1.2rem;">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(260px,1fr));gap:1.5rem;margin-bottom:2rem;">
                <div>
                    <label style="display:block;font-size:.73rem;font-weight:700;color:rgba(196,181,253,.65);letter-spacing:.04em;margin-bottom:6px;">WORKOUT TITLE *</label>
                    <input type="text" name="title" required value="{{ old('title', $workout->title) }}" style="width:100%;padding:11px 14px;background:rgba(255,255,255,.05);border:1px solid rgba(139,92,246,.25);border-radius:12px;color:#fff;font-size:.88rem;outline:none;">
                </div>

                <div>
                    <label style="display:block;font-size:.73rem;font-weight:700;color:rgba(196,181,253,.65);letter-spacing:.04em;margin-bottom:6px;">WORKOUT TYPE *</label>
                    <select name="type" required style="width:100%;padding:11px 14px;background:rgba(8,8,26,.8);border:1px solid rgba(139,92,246,.25);border-radius:12px;color:#fff;font-size:.88rem;outline:none;">
                        @foreach(['Strength', 'Cardio', 'HIIT', 'Flexibility', 'Full Body'] as $type)
                            <option value="{{ $type }}" {{ old('type', $workout->type) === $type ? 'selected' : '' }}>{{ $type }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label style="display:block;font-size:.73rem;font-weight:700;color:rgba(196,181,253,.65);letter-spacing:.04em;margin-bottom:6px;">DIFFICULTY *</label>
                    <select name="difficulty" required style="width:100%;padding:11px 14px;background:rgba(8,8,26,.8);border:1px solid rgba(139,92,246,.25);border-radius:12px;color:#fff;font-size:.88rem;outline:none;">
                        @foreach(['Beginner', 'Intermediate', 'Advanced'] as $difficulty)
                            <option value="{{ $difficulty }}" {{ old('difficulty', $workout->difficulty) === $difficulty ? 'selected' : '' }}>{{ $difficulty }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label style="display:block;font-size:.73rem;font-weight:700;color:rgba(196,181,253,.65);letter-spacing:.04em;margin-bottom:6px;">DURATION (MINUTES)</label>
                    <input type="number" name="duration_minutes" value="{{ old('duration_minutes', $workout->duration_minutes) }}" style="width:100%;padding:11px 14px;background:rgba(255,255,255,.05);border:1px solid rgba(139,92,246,.25);border-radius:12px;color:#fff;font-size:.88rem;outline:none;">
                </div>
            </div>

            <div style="border-top:1px solid rgba(139,92,246,.12);padding-top:2rem;margin-bottom:2rem;">
                <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:1.5rem;">
                    <h2 style="font-size:1.1rem;font-weight:800;color:#e2d9f3;">Exercises</h2>
                    <button type="button" id="addExerciseBtn" style="background:rgba(16,185,129,.15);border:1px solid rgba(16,185,129,.3);color:#6ee7b7;padding:8px 16px;border-radius:10px;font-size:.8rem;font-weight:700;cursor:pointer;">+ Add Exercise</button>
                </div>

                <div id="exercisesContainer" style="display:flex;flex-direction:column;gap:1.2rem;"></div>
            </div>

            <div style="display:flex;justify-content:flex-end;gap:1rem;border-top:1px solid rgba(139,92,246,.12);padding-top:1.5rem;">
                <a href="{{ route('workouts.show', $workout->id) }}" style="background:rgba(255,255,255,.05);color:rgba(255,255,255,.7);border:1px solid rgba(255,255,255,.15);padding:12px 24px;border-radius:12px;font-size:.9rem;font-weight:600;text-decoration:none;transition:all .3s ease;" onmouseover="this.style.background='rgba(255,255,255,.1)';this.style.color='#fff'" onmouseout="this.style.background='rgba(255,255,255,.05)';this.style.color='rgba(255,255,255,.7)'">Cancel</a>
                <button type="submit" style="background:linear-gradient(135deg,#8b5cf6,#ec4899);color:#fff;border:none;border-radius:12px;padding:12px 28px;font-size:.9rem;font-weight:700;cursor:pointer;box-shadow:0 8px 20px rgba(139,92,246,.35);transition:all .3s ease;" onmouseover="this.style.transform='translateY(-1px)';this.style.boxShadow='0 12px 25px rgba(139,92,246,.55)'" onmouseout="this.style.transform='none';this.style.boxShadow='0 8px 20px rgba(139,92,246,.35)'">Save Changes</button>
            </div>
        </form>
    </div>
</div>

<script>
    let exerciseCount = 0;
    const exercises = @json($exercises);
    const existingExercises = @json(old('exercises', $workout->exercises ?? []));

    document.querySelector('form').addEventListener('submit', function(event) {
        if (document.querySelectorAll('[id^="exercise-"]').length === 0) {
            event.preventDefault();
            alert('Please add at least one exercise to the workout plan.');
        }
    });

    function optionList(selectedId) {
        return exercises.map(exercise => {
            const selected = String(exercise.id) === String(selectedId) ? 'selected' : '';
            return `<option value="${exercise.id}" ${selected}>${exercise.name} (${exercise.muscle_group})</option>`;
        }).join('');
    }

    function addExerciseRow(data = {}) {
        const container = document.getElementById('exercisesContainer');
        const rowId = exerciseCount++;
        const exerciseHtml = `
            <div id="exercise-${rowId}" style="background:rgba(0,0,0,.2);border:1px solid rgba(139,92,246,.12);border-radius:16px;padding:1.5rem;position:relative;">
                <button type="button" onclick="removeExercise(${rowId})" style="position:absolute;top:1rem;right:1rem;background:none;border:none;color:#f87171;font-size:.75rem;font-weight:700;cursor:pointer;">Remove</button>
                <h4 style="font-size:.85rem;font-weight:700;color:#c4b5fd;margin-bottom:1.2rem;">EXERCISE ${rowId + 1}</h4>
                <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(180px,1fr));gap:1rem;">
                    <div style="grid-column:1/-1;">
                        <label style="display:block;font-size:.7rem;font-weight:700;color:rgba(196,181,253,.65);margin-bottom:6px;">EXERCISE *</label>
                        <select name="exercises[${rowId}][exercise_id]" required style="width:100%;padding:10px 14px;background:rgba(8,8,26,.8);border:1px solid rgba(139,92,246,.25);border-radius:10px;color:#fff;font-size:.85rem;outline:none;">
                            <option value="">Select exercise</option>
                            ${optionList(data.exercise_id)}
                        </select>
                    </div>
                    <div>
                        <label style="display:block;font-size:.7rem;font-weight:700;color:rgba(196,181,253,.65);margin-bottom:6px;">SETS *</label>
                        <input type="number" name="exercises[${rowId}][sets]" required min="1" value="${data.sets || ''}" style="width:100%;padding:10px 14px;background:rgba(255,255,255,.05);border:1px solid rgba(139,92,246,.25);border-radius:10px;color:#fff;font-size:.85rem;outline:none;">
                    </div>
                    <div>
                        <label style="display:block;font-size:.7rem;font-weight:700;color:rgba(196,181,253,.65);margin-bottom:6px;">REPS *</label>
                        <input type="number" name="exercises[${rowId}][reps]" required min="1" value="${data.reps || ''}" style="width:100%;padding:10px 14px;background:rgba(255,255,255,.05);border:1px solid rgba(139,92,246,.25);border-radius:10px;color:#fff;font-size:.85rem;outline:none;">
                    </div>
                    <div>
                        <label style="display:block;font-size:.7rem;font-weight:700;color:rgba(196,181,253,.65);margin-bottom:6px;">TARGET WEIGHT (KG)</label>
                        <input type="number" step="0.5" name="exercises[${rowId}][target_weight]" value="${data.target_weight || ''}" style="width:100%;padding:10px 14px;background:rgba(255,255,255,.05);border:1px solid rgba(139,92,246,.25);border-radius:10px;color:#fff;font-size:.85rem;outline:none;">
                    </div>
                </div>
            </div>
        `;
        container.insertAdjacentHTML('beforeend', exerciseHtml);
    }

    function removeExercise(id) {
        const element = document.getElementById(`exercise-${id}`);
        if (element) element.remove();
    }

    document.getElementById('addExerciseBtn').addEventListener('click', () => addExerciseRow());
    (existingExercises.length ? existingExercises : [{}]).forEach(addExerciseRow);
</script>
@endsection
