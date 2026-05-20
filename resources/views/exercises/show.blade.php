@extends('layouts.app')

@section('title', $exercise->name)

@section('content')
<style>
    select {
        appearance: none !important;
        -webkit-appearance: none !important;
        -moz-appearance: none !important;
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='16' height='16' viewBox='0 0 24 24' fill='none' stroke='%23ffffff' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpath d='m6 9 6 6 6-6'/%3E%3C/svg%3E") !important;
        background-repeat: no-repeat !important;
        background-position: right 12px center !important;
        background-size: 16px !important;
        padding-right: 40px !important;
    }
    select option {
        background-color: #0a0a1a !important;
        color: #fff !important;
        padding: 10px !important;
    }
</style>
<div style="max-width:1450px;margin:0 auto;">
    {{-- Back Link & Actions --}}
    <div style="margin-bottom:2rem; display: flex; justify-content: space-between; align-items: center;" class="fade-in-up">
        <a href="{{ route('exercises.index') }}" style="color:#c4b5fd;text-decoration:none;font-size:.9rem;font-weight:700;display:inline-flex;align-items:center;gap:8px;transition:all .3s; background: rgba(139,92,246,0.1); padding: 8px 16px; border-radius: 12px; border: 1px solid rgba(139,92,246,0.2);" onmouseover="this.style.background='rgba(139,92,246,0.2)'" onmouseout="this.style.background='rgba(139,92,246,0.1)'">
            <i data-lucide="arrow-left" style="width: 18px; height: 18px;"></i> Back to Library
        </a>
        
        <div style="display: flex; gap: 12px; align-items: center;">
            <button onclick="toggleFavorite(this, '{{ (string)$exercise->id }}')" class="favorite-btn" style="border-radius: 12px; padding: 8px 16px; display: inline-flex; align-items: center; gap: 8px; font-size: 0.9rem; font-weight: 700; border: 1px solid rgba(255, 255, 255, 0.1); background: rgba(255, 255, 255, 0.05); color: #fff; cursor: pointer; transition: all 0.3s;" onmouseover="if(!this.classList.contains('is-favorite')) this.style.background='rgba(255,255,255,0.1)'" onmouseout="if(!this.classList.contains('is-favorite')) this.style.background='rgba(255, 255, 255, 0.05)'">
                <i data-lucide="bookmark" style="width: 18px; height: 18px;"></i> Save Exercise
            </button>
            <button onclick="openWorkoutModal('{{ (string)$exercise->id }}')" style="border-radius: 12px; padding: 8px 16px; display: inline-flex; align-items: center; gap: 8px; font-size: 0.9rem; font-weight: 700; border: 1px solid transparent; background: linear-gradient(135deg, #8b5cf6, #ec4899); color: #fff; cursor: pointer; transition: all 0.3s; box-shadow: 0 4px 15px rgba(139, 92, 246, 0.3);" onmouseover="this.style.transform='translateY(-1px)'; this.style.boxShadow='0 6px 20px rgba(139, 92, 246, 0.5)'" onmouseout="this.style.transform='none'; this.style.boxShadow='0 4px 15px rgba(139, 92, 246, 0.3)'">
                <i data-lucide="plus" style="width: 18px; height: 18px;"></i> Add to Workout
            </button>
        </div>
    </div>
    
    <div style="display: grid; grid-template-columns: 1fr 350px; gap: 2rem;" class="fade-in-up delay-1">
        {{-- Left Column: Main Content --}}
        <div>
            <div style="background:rgba(255,255,255,.03);border:1px solid rgba(139,92,246,.18);border-radius:32px;overflow:hidden; margin-bottom: 2rem;">
                {{-- Hero Header --}}
                @php
                    $muscleColors = [
                        'Chest' => ['bg' => 'rgba(239,68,68,.1)', 'text' => '#f87171', 'border' => 'rgba(239,68,68,.2)', 'icon' => 'layers', 'grad' => 'linear-gradient(135deg, #450a0a 0%, #000 100%)'],
                        'Back' => ['bg' => 'rgba(59,130,246,.1)', 'text' => '#60a5fa', 'border' => 'rgba(59,130,246,.2)', 'icon' => 'anchor', 'grad' => 'linear-gradient(135deg, #172554 0%, #000 100%)'],
                        'Legs' => ['bg' => 'rgba(16,185,129,.1)', 'text' => '#34d399', 'border' => 'rgba(16,185,129,.2)', 'icon' => 'box', 'grad' => 'linear-gradient(135deg, #064e3b 0%, #000 100%)'],
                        'Shoulders' => ['bg' => 'rgba(245,158,11,.1)', 'text' => '#fbbf24', 'border' => 'rgba(245,158,11,.2)', 'icon' => 'target', 'grad' => 'linear-gradient(135deg, #451a03 0%, #000 100%)'],
                        'Arms' => ['bg' => 'rgba(139,92,246,.1)', 'text' => '#a78bfa', 'border' => 'rgba(139,92,246,.2)', 'icon' => 'dumbbell', 'grad' => 'linear-gradient(135deg, #2e1065 0%, #000 100%)'],
                        'Core' => ['bg' => 'rgba(236,72,153,.1)', 'text' => '#f472b6', 'border' => 'rgba(236,72,153,.2)', 'icon' => 'activity', 'grad' => 'linear-gradient(135deg, #500724 0%, #000 100%)'],
                    ];
                    $style = $muscleColors[$exercise->muscle_group] ?? ['bg' => 'rgba(255,255,255,.05)', 'text' => '#ccc', 'border' => 'rgba(255,255,255,.1)', 'icon' => 'circle', 'grad' => 'linear-gradient(135deg, #1f2937 0%, #000 100%)'];
                @endphp
                
                <div style="height: 300px; background: {{ $style['grad'] }}; display: flex; align-items: center; justify-content: center; position: relative;">
                    <div style="position: absolute; inset: 0; opacity: 0.1; background-image: url('https://www.transparenttextures.com/patterns/carbon-fibre.png');"></div>
                    <i data-lucide="{{ $style['icon'] }}" style="width: 120px; height: 120px; color: {{ $style['text'] }}; opacity: 0.9; filter: drop-shadow(0 0 30px {{ $style['text'] }}55);"></i>
                    
                    <div style="position: absolute; bottom: 0; left: 0; right: 0; padding: 2rem; background: linear-gradient(to top, rgba(0,0,0,0.8), transparent);">
                        <h1 style="font-size: 2.5rem; font-weight: 950; color: #fff; margin: 0; letter-spacing: -0.02em;">{{ $exercise->name }}</h1>
                        <div style="display: flex; gap: 12px; margin-top: 12px;">
                            <span style="background: {{ $style['bg'] }}; color: {{ $style['text'] }}; border: 1px solid {{ $style['border'] }}; padding: 6px 14px; border-radius: 50px; font-size: 0.75rem; font-weight: 800; text-transform: uppercase; letter-spacing: 0.1em;">{{ $exercise->muscle_group }}</span>
                            <span style="background: rgba(255,255,255,0.1); color: #fff; padding: 6px 14px; border-radius: 50px; font-size: 0.75rem; font-weight: 800; text-transform: uppercase; letter-spacing: 0.1em;">{{ $exercise->equipment }}</span>
                        </div>
                    </div>
                </div>

                <div style="padding: 2.5rem;">
                    {{-- Exercise Video Tutorial --}}
                    @if($exercise->video_url)
                        <div style="margin-bottom: 3rem;">
                            <h2 style="font-size: 1.4rem; font-weight: 900; color: #fff; margin-bottom: 1.5rem; display: flex; align-items: center; gap: 12px;">
                                <span style="background: var(--vg-accent); color: #fff; width: 32px; height: 32px; border-radius: 10px; display: flex; align-items: center; justify-content: center; font-size: 1rem;">
                                    <i data-lucide="video" style="width: 16px; height: 16px;"></i>
                                </span>
                                Video Demonstration
                            </h2>
                            <div style="width:100%;border-radius:24px;overflow:hidden;border:1px solid rgba(255,255,255,0.08);box-shadow: 0 10px 30px rgba(0,0,0,0.45);background:#000;">
                                @if(Str::contains($exercise->video_url, ['youtube.com', 'youtu.be']))
                                    @php 
                                        $videoId = '';
                                        if (preg_match('%(?:youtube(?:-nocookie)?\.com/(?:[^/]+/.+/|(?:v|e(?:mbed)?)/|.*[?&]v=)|youtu\.be/)([^"&?/ ]{11})%i', $exercise->video_url, $match)) {
                                            $videoId = $match[1];
                                        }
                                    @endphp
                                    <div style="position:relative;padding-bottom:56.25%;height:0;overflow:hidden;width:100%;">
                                        <iframe style="position:absolute;top:0;left:0;width:100%;height:100%;" 
                                                src="https://www.youtube.com/embed/{{ $videoId }}" 
                                                frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen>
                                        </iframe>
                                    </div>
                                @else
                                    <video src="{{ $exercise->video_url }}" controls style="width:100%;display:block;"></video>
                                @endif
                            </div>
                        </div>
                    @endif

                    {{-- Step by Step --}}
                    <div style="margin-bottom: 3rem;">
                        <h2 style="font-size: 1.4rem; font-weight: 900; color: #fff; margin-bottom: 1.5rem; display: flex; align-items: center; gap: 12px;">
                            <span style="background: var(--vg-accent); color: #fff; width: 32px; height: 32px; border-radius: 10px; display: flex; align-items: center; justify-content: center; font-size: 1rem;">1</span>
                            Step-by-Step Instructions
                        </h2>
                        <div style="display: flex; flex-direction: column; gap: 1rem;">
                            @foreach(explode("\n", $exercise->instructions ?? "No specific instructions provided yet.") as $index => $step)
                                @if(trim($step))
                                    <div style="display: flex; gap: 16px; background: rgba(255,255,255,0.02); padding: 1.2rem; border-radius: 18px; border: 1px solid rgba(255,255,255,0.05);">
                                        <div style="color: var(--vg-accent); font-weight: 800; font-size: 1.1rem; opacity: 0.5;">{{ str_pad($index + 1, 2, '0', STR_PAD_LEFT) }}</div>
                                        <p style="color: rgba(255,255,255,0.75); line-height: 1.6; margin: 0; font-size: 1rem;">{{ $step }}</p>
                                    </div>
                                @endif
                            @endforeach
                        </div>
                    </div>

                    {{-- Common Mistakes --}}
                    <div>
                        <h2 style="font-size: 1.4rem; font-weight: 900; color: #fff; margin-bottom: 1.5rem; display: flex; align-items: center; gap: 12px;">
                            <span style="background: #ef4444; color: #fff; width: 32px; height: 32px; border-radius: 10px; display: flex; align-items: center; justify-content: center; font-size: 1rem;">!</span>
                            Common Mistakes to Avoid
                        </h2>
                        <div style="background: rgba(239, 68, 68, 0.03); border: 1px solid rgba(239, 68, 68, 0.15); border-radius: 20px; padding: 1.5rem;">
                            <ul style="list-style: none; padding: 0; margin: 0; display: flex; flex-direction: column; gap: 12px;">
                                <li style="display: flex; align-items: flex-start; gap: 12px; color: rgba(255,255,255,0.6); font-size: 0.95rem;">
                                    <i data-lucide="alert-circle" style="width: 18px; height: 18px; color: #ef4444; flex-shrink: 0; margin-top: 2px;"></i>
                                    Arching your back excessively during the movement.
                                </li>
                                <li style="display: flex; align-items: flex-start; gap: 12px; color: rgba(255,255,255,0.6); font-size: 0.95rem;">
                                    <i data-lucide="alert-circle" style="width: 18px; height: 18px; color: #ef4444; flex-shrink: 0; margin-top: 2px;"></i>
                                    Using momentum instead of controlled muscle activation.
                                </li>
                                <li style="display: flex; align-items: flex-start; gap: 12px; color: rgba(255,255,255,0.6); font-size: 0.95rem;">
                                    <i data-lucide="alert-circle" style="width: 18px; height: 18px; color: #ef4444; flex-shrink: 0; margin-top: 2px;"></i>
                                    Restricting the full range of motion.
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Related Exercises --}}
            <div>
                <h2 style="font-size: 1.3rem; font-weight: 900; color: #fff; margin-bottom: 1.5rem;">Alternative & Related</h2>
                <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(220px, 1fr)); gap: 1.5rem;">
                    @foreach($relatedExercises as $related)
                        <a href="{{ route('exercises.show', $related->id) }}" style="text-decoration: none; background: rgba(255,255,255,0.02); border: 1px solid rgba(255,255,255,0.05); border-radius: 20px; padding: 1.2rem; transition: all 0.3s;" onmouseover="this.style.borderColor='rgba(139,92,246,0.3)';this.style.transform='translateY(-4px)'" onmouseout="this.style.borderColor='rgba(255,255,255,0.05)';this.style.transform=''">
                            <div style="font-size: 0.7rem; font-weight: 800; color: var(--vg-accent); text-transform: uppercase; letter-spacing: 0.1em; margin-bottom: 8px;">{{ $related->muscle_group }}</div>
                            <h4 style="color: #fff; font-weight: 700; margin: 0; font-size: 1rem;">{{ $related->name }}</h4>
                        </a>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- Right Column: Stats & Diagrams --}}
        <div style="display: flex; flex-direction: column; gap: 2rem;">
            <div style="background:rgba(255,255,255,.03);border:1px solid rgba(139,92,246,.18);border-radius:24px;padding:1.8rem;">
                <h3 style="font-size: 1.1rem; font-weight: 900; color: #fff; margin-bottom: 1.5rem; text-transform: uppercase; letter-spacing: 0.05em; border-bottom: 1px solid rgba(255,255,255,0.1); padding-bottom: 1rem;">Training Focus</h3>
                
                <div style="display: flex; flex-direction: column; gap: 1.5rem;">
                    {{-- Muscle Stat --}}
                    <div>
                        <div style="display: flex; justify-content: space-between; margin-bottom: 8px;">
                            <span style="color: rgba(255,255,255,0.5); font-size: 0.85rem; font-weight: 600;">Primary Muscle</span>
                            <span style="color: {{ $style['text'] }}; font-size: 0.85rem; font-weight: 800;">{{ $exercise->muscle_group }}</span>
                        </div>
                        <div style="height: 6px; background: rgba(255,255,255,0.05); border-radius: 10px; overflow: hidden;">
                            <div style="width: 100%; height: 100%; background: {{ $style['text'] }};"></div>
                        </div>
                    </div>

                    {{-- Difficulty Stat --}}
                    <div>
                        <div style="display: flex; justify-content: space-between; margin-bottom: 8px;">
                            <span style="color: rgba(255,255,255,0.5); font-size: 0.85rem; font-weight: 600;">Difficulty Level</span>
                            <span style="color: #fff; font-size: 0.85rem; font-weight: 800;">{{ $exercise->difficulty }}</span>
                        </div>
                        @php
                            $diffWidth = match($exercise->difficulty) {
                                'Beginner' => '33%',
                                'Intermediate' => '66%',
                                'Advanced' => '100%',
                                default => '50%'
                            };
                        @endphp
                        <div style="height: 6px; background: rgba(255,255,255,0.05); border-radius: 10px; overflow: hidden;">
                            <div style="width: {{ $diffWidth }}; height: 100%; background: linear-gradient(to right, #8b5cf6, #ec4899);"></div>
                        </div>
                    </div>

                    <div style="background: rgba(139,92,246,0.05); padding: 1.2rem; border-radius: 18px; border: 1px solid rgba(139,92,246,0.1); margin-top: 1rem;">
                        <h4 style="color: #fff; font-size: 0.9rem; font-weight: 800; margin-bottom: 12px; display: flex; align-items: center; gap: 8px;">
                            <i data-lucide="flame" style="width: 16px; height: 16px; color: #f59e0b;"></i>
                            Burn Estimate
                        </h4>
                        <div style="display: flex; align-items: baseline; gap: 6px;">
                            <span style="font-size: 1.8rem; font-weight: 950; color: #fff;">~55</span>
                            <span style="color: rgba(255,255,255,0.4); font-size: 0.8rem; font-weight: 700;">KCAL / SET</span>
                        </div>
                        <p style="font-size: 0.75rem; color: rgba(255,255,255,0.3); margin-top: 10px; line-height: 1.4;">Based on a high-intensity standard effort session.</p>
                    </div>
                </div>
            </div>
            @if($exercise->tips)
            <div style="margin-bottom:2.5rem;">
                <h2 style="font-size:1.2rem;font-weight:800;color:#fcd34d;margin-bottom:.8rem;display:flex;align-items:center;gap:8px;">
                    💡 Pro Tips
                </h2>
                <div style="background:rgba(245,158,11,.05);border-radius:16px;padding:1.5rem;border:1px solid rgba(245,158,11,.2);">
                    <p style="font-size:.95rem;color:rgba(255,255,255,.7);line-height:1.7;">{{ $exercise->tips }}</p>
                </div>
            </div>
            @endif

            <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(300px,1fr));gap:1.5rem;margin-bottom:2.5rem;">
                @if($exercise->benefits)
                <div>
                    <h2 style="font-size:1.2rem;font-weight:800;color:#10b981;margin-bottom:.8rem;display:flex;align-items:center;gap:8px;">
                        🌟 Benefits
                    </h2>
                    <div style="background:rgba(16,185,129,.05);border-radius:16px;padding:1.5rem;border:1px solid rgba(16,185,129,.2);height:calc(100% - 2.5rem);">
                        <p style="font-size:.95rem;color:rgba(255,255,255,.7);line-height:1.7;">{{ $exercise->benefits }}</p>
                    </div>
                </div>
                @endif

                @if($exercise->precautions)
                <div>
                    <h2 style="font-size:1.2rem;font-weight:800;color:#f43f5e;margin-bottom:.8rem;display:flex;align-items:center;gap:8px;">
                        ⚠️ Precautions
                    </h2>
                    <div style="background:rgba(244,63,94,.05);border-radius:16px;padding:1.5rem;border:1px solid rgba(244,63,94,.2);height:calc(100% - 2.5rem);">
                        <p style="font-size:.95rem;color:rgba(255,255,255,.7);line-height:1.7;">{{ $exercise->precautions }}</p>
                    </div>
                </div>
                @endif
            </div>
            

        </div>
    </div>
</div>

@push('modals')
{{-- Workout Selector Modal --}}
<div id="workoutSelectorModal" class="modal-overlay">
    <div class="modal-content" style="max-width: 450px;">
        <div class="modal-header">
            <div>
                <h2 id="modalTitle" style="font-size: 1.25rem; font-weight: 900; color: #fff;">Assign Exercise</h2>
                <p id="modalSubtitle" style="color: rgba(255,255,255,0.4); font-size: 0.85rem;">Select a target for this exercise</p>
            </div>
            <button type="button" onclick="closeWorkoutModal()" class="modal-close-btn">
                <i data-lucide="x" style="width: 20px; height: 20px;"></i>
            </button>
        </div>
        <div class="modal-body">
            <input type="hidden" id="selectedExerciseId">
            <input type="hidden" id="selectedUserId">
            
            {{-- Step 1: Client List (Trainer Only) --}}
            <div id="clientListStep" style="display: none; flex-direction: column; gap: 10px;">
                <label style="font-size: 0.7rem; font-weight: 800; color: rgba(196,181,253,0.5); text-transform: uppercase; letter-spacing: 0.1em; margin-bottom: 5px;">Select Client</label>
                {{-- Note: In 'show' view, we might not have $clients passed. We'll handle this in the JS or by passing it from controller. --}}
                <div id="clientListItems" style="display: flex; flex-direction: column; gap: 10px;">
                    <p style="color: rgba(255,255,255,0.4); text-align: center; font-size: 0.85rem; padding: 1rem;">Loading clients...</p>
                </div>
            </div>

            {{-- Step 2: Workout List --}}
            <div id="workoutListStep" style="display: none; flex-direction: column; gap: 10px;">
                <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 10px;">
                    <label id="workoutListLabel" style="font-size: 0.7rem; font-weight: 800; color: rgba(196,181,253,0.5); text-transform: uppercase; letter-spacing: 0.1em;">Select Workout Plan</label>
                    <button id="backToClientsBtn" onclick="showClientStep()" style="background: none; border: none; color: var(--vg-accent); font-size: 0.75rem; font-weight: 700; cursor: pointer; display: none;">← Back</button>
                </div>
                <div id="workoutItems" style="display: flex; flex-direction: column; gap: 8px; max-height: 300px; overflow-y: auto; padding-right: 5px;">
                    {{-- Dynamically populated --}}
                </div>
                <div id="noWorkoutsMsg" style="display: none; text-align: center; padding: 2rem 1rem; background: rgba(255,255,255,0.02); border: 1px dashed rgba(255,255,255,0.1); border-radius: 16px;">
                    <p style="color: rgba(255,255,255,0.4); font-size: 0.85rem; margin: 0;">No active workout plans found.</p>
                </div>
            </div>
            
            <div id="modalLoading" style="display: none; text-align: center; padding: 2rem;">
                <div class="loading-spinner" style="margin: 0 auto 15px;"></div>
                <p style="color: rgba(255,255,255,0.4); font-size: 0.85rem;">Processing...</p>
            </div>
        </div>
    </div>
</div>
@endpush

@push('scripts')
<script>
    const userRole = "{{ Auth::user()->role }}";
    const currentUserId = "{{ Auth::id() }}";

    function openWorkoutModal(exerciseId) {
        document.getElementById('selectedExerciseId').value = exerciseId;
        document.getElementById('workoutSelectorModal').style.display = 'flex';
        document.body.style.overflow = 'hidden';
        
        if (userRole === 'trainer') {
            fetchClients(); // Fetch clients dynamically for the 'show' page
            showClientStep();
        } else {
            selectClient(currentUserId, 'My Plans');
        }
    }

    function fetchClients() {
        // Simple mock or small fetch to get clients if not available
        // In a real app, this should be consistent
        const container = document.getElementById('clientListItems');
        container.innerHTML = '<p style="color: rgba(255,255,255,0.4); text-align: center; padding: 1rem;">Loading clients...</p>';
        
        fetch('/trainer/clients-api') // I'll add this route or similar
            .then(res => res.json())
            .then(clients => {
                container.innerHTML = '';
                if (clients.length === 0) {
                    container.innerHTML = '<p style="color: rgba(255,255,255,0.4); text-align: center; padding: 1rem;">No confirmed clients found.</p>';
                }
                clients.forEach(client => {
                    const btn = document.createElement('button');
                    btn.className = 'favorite-btn';
                    btn.style = 'display: flex; align-items: center; justify-content: space-between; padding: 14px 18px; border-radius: 16px; width: 100%; margin-bottom: 8px; border: 1px solid rgba(139, 92, 246, 0.2); background: rgba(139, 92, 246, 0.05); color: #fff; cursor: pointer; transition: all 0.3s;';
                    btn.onmouseover = () => { btn.style.background = 'rgba(139, 92, 246, 0.15)'; btn.style.borderColor = 'rgba(139, 92, 246, 0.4)'; };
                    btn.onmouseout = () => { btn.style.background = 'rgba(139, 92, 246, 0.05)'; btn.style.borderColor = 'rgba(139, 92, 246, 0.2)'; };
                    btn.innerHTML = `
                        <div style="display: flex; align-items: center; gap: 12px;">
                            <div style="width: 32px; height: 32px; background: rgba(139,92,246,0.2); border-radius: 10px; display: flex; align-items: center; justify-content: center; font-weight: 800; color: #c4b5fd;">${client.name.charAt(0)}</div>
                            <span style="font-weight: 700;">${client.name}</span>
                        </div>
                        <i data-lucide="chevron-right" style="width: 18px; height: 18px; opacity: 0.3;"></i>
                    `;
                    btn.onclick = () => selectClient(client.id, client.name);
                    container.appendChild(btn);
                });
                lucide.createIcons();
            });
    }

    function closeWorkoutModal() {
        document.getElementById('workoutSelectorModal').style.display = 'none';
        document.body.style.overflow = 'auto';
    }

    function showClientStep() {
        document.getElementById('clientListStep').style.display = 'flex';
        document.getElementById('workoutListStep').style.display = 'none';
        document.getElementById('modalTitle').innerText = 'Assign Exercise';
        document.getElementById('modalSubtitle').innerText = 'Select a client to assign this exercise';
        document.getElementById('backToClientsBtn').style.display = 'none';
    }

    function selectClient(userId, userName) {
        document.getElementById('selectedUserId').value = userId;
        document.getElementById('clientListStep').style.display = 'none';
        document.getElementById('modalLoading').style.display = 'block';
        document.getElementById('workoutListStep').style.display = 'none';
        
        if (userRole === 'trainer') {
            document.getElementById('modalTitle').innerText = 'Assign to ' + userName;
            document.getElementById('backToClientsBtn').style.display = 'block';
        } else {
            document.getElementById('modalTitle').innerText = 'Add to Workout';
        }

        fetch(`/exercises/user-workouts/${userId}`)
            .then(response => response.json())
            .then(workouts => {
                document.getElementById('modalLoading').style.display = 'none';
                
                if (workouts.error) {
                    alert('❌ Error: ' + workouts.error);
                    if (userRole === 'trainer') {
                        showClientStep();
                    } else {
                        closeWorkoutModal();
                    }
                    return;
                }

                document.getElementById('workoutListStep').style.display = 'flex';
                
                const container = document.getElementById('workoutItems');
                container.innerHTML = '';
                
                if (workouts.length === 0) {
                    document.getElementById('noWorkoutsMsg').style.display = 'block';
                } else {
                    document.getElementById('noWorkoutsMsg').style.display = 'none';
                    workouts.forEach(workout => {
                        const btn = document.createElement('button');
                        btn.className = 'favorite-btn';
                        btn.style = 'display: flex; align-items: center; justify-content: space-between; padding: 12px 16px; border-radius: 12px; margin-bottom: 8px; width: 100%; border: 1px solid rgba(255, 255, 255, 0.1); background: rgba(255, 255, 255, 0.03); color: #fff; cursor: pointer; transition: all 0.3s;';
                        btn.onmouseover = () => { btn.style.background = 'rgba(255, 255, 255, 0.08)'; btn.style.borderColor = 'rgba(255, 255, 255, 0.2)'; };
                        btn.onmouseout = () => { btn.style.background = 'rgba(255, 255, 255, 0.03)'; btn.style.borderColor = 'rgba(255, 255, 255, 0.1)'; };
                        btn.innerHTML = `
                            <div style="text-align: left;">
                                <div style="font-weight: 700; color: #fff;">${workout.title}</div>
                                <div style="font-size: 0.7rem; color: rgba(255,255,255,0.3);">${new Date(workout.scheduled_date).toLocaleDateString()}</div>
                            </div>
                            <i data-lucide="plus" style="width: 16px; height: 16px; opacity: 0.5;"></i>
                        `;
                        btn.onclick = () => addToWorkout(workout.id);
                        container.appendChild(btn);
                    });
                    lucide.createIcons();
                }
            });
    }

    function addToWorkout(workoutId) {
        const exerciseId = document.getElementById('selectedExerciseId').value;
        
        fetch('/exercises/add-to-workout', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({ workout_id: workoutId, exercise_id: exerciseId })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('✨ ' + data.message);
                closeWorkoutModal();
            } else {
                alert('❌ Error: ' + (data.error || 'Failed to add exercise'));
            }
        });
    }

    function toggleFavorite(btn, exerciseId) {
        let favorites = JSON.parse(localStorage.getItem('vg_favorites') || '[]');
        if (favorites.includes(exerciseId)) {
            favorites = favorites.filter(id => id !== exerciseId);
            btn.style.color = '#fff';
            btn.style.background = 'rgba(255, 255, 255, 0.05)';
            btn.style.borderColor = 'rgba(255, 255, 255, 0.1)';
            btn.classList.remove('is-favorite');
        } else {
            favorites.push(exerciseId);
            btn.style.color = '#fbbf24';
            btn.style.background = 'rgba(251, 191, 36, 0.1)';
            btn.style.borderColor = 'rgba(251, 191, 36, 0.2)';
            btn.classList.add('is-favorite');
        }
        localStorage.setItem('vg_favorites', JSON.stringify(favorites));
        lucide.createIcons();
    }

    document.addEventListener('DOMContentLoaded', function() {
        const favorites = JSON.parse(localStorage.getItem('vg_favorites') || '[]');
        if (favorites.includes('{{ (string)$exercise->id }}')) {
            const btn = document.querySelector('.favorite-btn');
            if (btn) {
                btn.style.color = '#fbbf24';
                btn.style.background = 'rgba(251, 191, 36, 0.1)';
                btn.style.borderColor = 'rgba(251, 191, 36, 0.2)';
                btn.classList.add('is-favorite');
            }
        }
    });
</script>
<style>
    .loading-spinner {
        width: 30px;
        height: 30px;
        border: 3px solid rgba(139, 92, 246, 0.1);
        border-top: 3px solid #8b5cf6;
        border-radius: 50%;
        animation: spin 1s linear infinite;
    }
    @keyframes spin { 0% { transform: rotate(0deg); } 100% { transform: rotate(360deg); } }
    .favorite-btn.is-favorite i { fill: #fbbf24; }
    .modal-overlay {
        position: fixed; inset: 0; background: rgba(0, 0, 0, 0.8);
        backdrop-filter: blur(8px); display: none; align-items: center;
        justify-content: center; z-index: 1000; padding: 20px;
    }
    .modal-content {
        background: #0a0a1a; border: 1px solid rgba(139, 92, 246, 0.3);
        border-radius: 28px; width: 100%; max-width: 600px;
        box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
    }
    .modal-header {
        padding: 1.5rem; border-bottom: 1px solid rgba(139, 92, 246, 0.1);
        display: flex; justify-content: space-between; align-items: center;
    }
    .modal-body { padding: 1.5rem; }
    .modal-close-btn {
        background: rgba(255, 255, 255, 0.05); border: 1px solid rgba(255, 255, 255, 0.1);
        color: #fff; width: 40px; height: 40px; border-radius: 50%;
        cursor: pointer; display: flex; align-items: center; justify-content: center;
    }
    #workoutItems::-webkit-scrollbar { width: 5px; }
    #workoutItems::-webkit-scrollbar-track { background: transparent; }
    #workoutItems::-webkit-scrollbar-thumb { background: rgba(139,92,246,0.3); border-radius: 10px; }
</style>
@endpush
</div>
@endsection