@extends('layouts.app')

@section('title', 'Mindfulness & Recovery')

@section('content')
<style>
    .mindfulness-categories-bar::-webkit-scrollbar {
        height: 4px;
    }
    .mindfulness-categories-bar::-webkit-scrollbar-thumb {
        background: rgba(255,255,255,0.05);
        border-radius: 10px;
    }
    
    .category-pill {
        color: #fff;
        padding: .6rem 1.2rem;
        border-radius: 50px;
        font-size: .85rem;
        font-weight: 700;
        text-decoration: none;
        border: 1px solid var(--vg-border);
        white-space: nowrap;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }
    .category-pill:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(139, 92, 246, 0.2);
        border-color: var(--vg-border-strong);
    }
    
    .mindfulness-card {
        background: var(--vg-panel);
        border: 1px solid var(--vg-border);
        border-radius: 24px;
        overflow: hidden;
        transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        display: flex;
        flex-direction: column;
        position: relative;
    }
    .mindfulness-card:hover {
        transform: translateY(-6px);
        border-color: var(--vg-border-strong) !important;
        box-shadow: 0 20px 40px rgba(139, 92, 246, 0.12);
    }
    
    .mindfulness-image-container {
        height: 190px;
        overflow: hidden;
        position: relative;
    }
    .mindfulness-image {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.6s cubic-bezier(0.4, 0, 0.2, 1);
    }
    .mindfulness-card:hover .mindfulness-image {
        transform: scale(1.04);
    }
    
    .begin-session-btn {
        display: block;
        text-align: center;
        background: rgba(255, 255, 255, 0.03);
        border: 1px solid rgba(255, 255, 255, 0.08);
        color: rgba(255, 255, 255, 0.9);
        padding: .8rem;
        border-radius: 14px;
        font-size: .85rem;
        font-weight: 700;
        text-decoration: none;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }
    .begin-session-btn:hover {
        background: var(--vg-gradient);
        color: #fff;
        border-color: transparent;
        transform: translateY(-2px) scale(1.02);
        box-shadow: 0 6px 20px rgba(139, 92, 246, 0.35);
    }
    
    .continue-session-btn {
        display: block;
        text-align: center;
        background: var(--vg-gradient);
        color: #fff;
        border: 1px solid transparent;
        padding: .8rem;
        border-radius: 14px;
        font-size: .85rem;
        font-weight: 800;
        text-decoration: none;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        box-shadow: 0 4px 15px rgba(139, 92, 246, 0.25);
    }
    .continue-session-btn:hover {
        transform: translateY(-2px) scale(1.02);
        box-shadow: 0 8px 25px rgba(139, 92, 246, 0.5);
    }
    
    @keyframes vg-pulse {
        0%, 100% { opacity: 0.6; transform: scale(1); }
        50% { opacity: 1; transform: scale(1.2); }
    }
    .pulse-dot {
        width: 6px;
        height: 6px;
        background: #10b981;
        border-radius: 50%;
        display: inline-block;
        animation: vg-pulse 2s infinite ease-in-out;
    }

    /* Recovery Stats Bar */
    .recovery-stats-bar {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 1rem;
        margin-bottom: 1.5rem;
    }
    .recovery-stat-card {
        background: var(--vg-panel);
        border: 1px solid var(--vg-border);
        border-radius: 16px;
        padding: 1.1rem;
        display: flex;
        align-items: center;
        gap: 12px;
        transition: all 0.3s ease;
    }
    .recovery-stat-card:hover {
        transform: translateY(-2px);
        border-color: rgba(139, 92, 246, 0.3);
        box-shadow: 0 4px 15px rgba(139, 92, 246, 0.08);
    }
    .stat-card-icon {
        font-size: 1.5rem;
        background: rgba(139, 92, 246, 0.1);
        width: 42px;
        height: 42px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    
    /* Featured Recommendation Banner */
    .featured-recommendation-card {
        background: linear-gradient(135deg, rgba(88, 28, 135, 0.25) 0%, rgba(15, 12, 41, 0.8) 100%);
        border: 1px solid rgba(139, 92, 246, 0.35);
        border-radius: 24px;
        padding: 1.75rem;
        margin-bottom: 2rem;
        display: flex;
        gap: 1.5rem;
        align-items: center;
        position: relative;
        overflow: hidden;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.25);
    }
    .featured-recommendation-card::before {
        content: '';
        position: absolute;
        top: -50px;
        right: -50px;
        width: 180px;
        height: 180px;
        background: radial-gradient(circle, rgba(139, 92, 246, 0.2) 0%, transparent 70%);
        pointer-events: none;
    }
    .featured-badge {
        background: rgba(167, 139, 250, 0.16);
        border: 1px solid rgba(167, 139, 250, 0.35);
        color: #c084fc;
        font-size: 0.68rem;
        font-weight: 800;
        padding: 4px 10px;
        border-radius: 8px;
        text-transform: uppercase;
        letter-spacing: 0.06em;
        display: inline-block;
        margin-bottom: 0.6rem;
    }
    .featured-img-wrap {
        width: 200px;
        height: 130px;
        border-radius: 16px;
        overflow: hidden;
        flex-shrink: 0;
        border: 1px solid rgba(255,255,255,0.06);
    }
    .featured-img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
    
    /* Pre-session Modal overlay */
    .mood-modal-overlay {
        position: fixed;
        inset: 0;
        background: rgba(8, 8, 26, 0.85);
        backdrop-filter: blur(12px);
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 1050;
        opacity: 0;
        pointer-events: none;
        transition: all 0.3s ease;
    }
    .mood-modal-overlay.active {
        opacity: 1;
        pointer-events: auto;
    }
    .mood-modal-card {
        background: #0d0c22;
        border: 1px solid rgba(139, 92, 246, 0.25);
        border-radius: 28px;
        width: 480px;
        max-width: 90%;
        padding: 2.2rem;
        box-shadow: 0 20px 50px rgba(0,0,0,0.5);
        transform: translateY(20px) scale(0.95);
        transition: all 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
        text-align: center;
    }
    .mood-modal-overlay.active .mood-modal-card {
        transform: translateY(0) scale(1);
    }
    .mood-btn {
        background: rgba(255,255,255,0.03);
        border: 1px solid rgba(255,255,255,0.08);
        border-radius: 18px;
        padding: 1rem;
        font-size: 0.9rem;
        font-weight: 700;
        color: #fff;
        cursor: pointer;
        transition: all 0.25s ease;
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 6px;
    }
    .mood-btn:hover {
        background: rgba(139,92,246,0.12);
        border-color: rgba(139,92,246,0.4);
        transform: translateY(-3px);
    }
</style>

<div style="max-width:1200px;margin:0 auto;">
    <div style="margin-bottom:1.5rem;" class="fade-in-up">
        <h1 style="font-size:1.8rem;font-weight:900;background:var(--vg-title-gradient);-webkit-background-clip:text;background-clip:text;color:transparent;margin-bottom:.35rem;">
            Mindfulness & Recovery 🧘‍♂️
        </h1>
        <p style="color:var(--vg-text-muted);font-size:.9rem;">Heal your body and mind for sustainable progress</p>
    </div>

    @php
        $lastCompletedWorkout = \App\Models\Workout::where('user_id', auth()->id())
            ->where('status', 'completed')
            ->orderBy('updated_at', 'desc')
            ->first();
        
        $recommendationText = "Morning Zen Meditation";
        $recommendationReason = "Start your day centered with guided breathing and focus.";
        $recommendationCategory = "Meditation";
        $recommendationSessionId = null;

        if ($lastCompletedWorkout) {
            $name = strtolower($lastCompletedWorkout->name);
            if (Str::contains($name, ['push', 'chest', 'shoulder', 'arm'])) {
                $recommendationText = "Deep Tissue Recovery";
                $recommendationReason = "Heavy upper-body strain detected from your recent workout '{$lastCompletedWorkout->name}'. Recommended recovery stretch to prevent muscle tightness.";
                $recommendationCategory = "Recovery";
            } elseif (Str::contains($name, ['pull', 'back', 'leg', 'squat'])) {
                $recommendationText = "Deep Tissue Recovery";
                $recommendationReason = "Heavy lower-body or posterior chain load detected from '{$lastCompletedWorkout->name}'. Relieve spinal pressure and hamstring tightness.";
                $recommendationCategory = "Recovery";
            } else {
                $recommendationText = "Box Breathing for Stress";
                $recommendationReason = "Post-workout nervous system stabilization recommended after your '{$lastCompletedWorkout->name}' session.";
                $recommendationCategory = "Breathing";
            }
        } else {
            $recommendationReason = "No workouts logged recently. Start with a foundational mindfulness session to cultivate daily mental recovery.";
        }

        $recommendedContent = $contents->where('title', $recommendationText)->first() ?? $contents->first();
        if ($recommendedContent) {
            $recommendationSessionId = $recommendedContent->id;
        }
    @endphp

    <!-- Recovery Stats Bar (Request 5 & 6) -->
    <div class="recovery-stats-bar fade-in-up" style="margin-bottom: 2rem;">
        <div class="recovery-stat-card">
            <span class="stat-card-icon">🔥</span>
            <div>
                <p style="font-size: 0.68rem; color: var(--vg-text-faint); margin: 0; text-transform: uppercase; font-weight: 800; letter-spacing: 0.05em;">Streak</p>
                <p style="font-size: 0.95rem; font-weight: 800; color: #fff; margin: 2px 0 0 0;" id="stat_streak">6 Days</p>
            </div>
        </div>
        <div class="recovery-stat-card">
            <span class="stat-card-icon">🧘</span>
            <div>
                <p style="font-size: 0.68rem; color: var(--vg-text-faint); margin: 0; text-transform: uppercase; font-weight: 800; letter-spacing: 0.05em;">Completed</p>
                <p style="font-size: 0.95rem; font-weight: 800; color: #fff; margin: 2px 0 0 0;" id="stat_sessions">12 Sessions</p>
            </div>
        </div>
        <div class="recovery-stat-card">
            <span class="stat-card-icon">⏱️</span>
            <div>
                <p style="font-size: 0.68rem; color: var(--vg-text-faint); margin: 0; text-transform: uppercase; font-weight: 800; letter-spacing: 0.05em;">Mindful Mins</p>
                <p style="font-size: 0.95rem; font-weight: 800; color: #fff; margin: 2px 0 0 0;" id="stat_minutes">85 mins</p>
            </div>
        </div>
        <div class="recovery-stat-card" style="position: relative; cursor: pointer;" onclick="openMoodModal()">
            <span class="stat-card-icon">🧠</span>
            <div style="flex-grow: 1;">
                <p style="font-size: 0.68rem; color: var(--vg-text-faint); margin: 0; text-transform: uppercase; font-weight: 800; letter-spacing: 0.05em;">Current Mood</p>
                <p style="font-size: 0.95rem; font-weight: 800; color: #a78bfa; margin: 2px 0 0 0; display: flex; align-items: center; gap: 4px;" id="stat_mood">
                    <span>Not Set</span> <span style="font-size: 0.75rem; color: var(--vg-text-faint);">✏️</span>
                </p>
            </div>
        </div>
    </div>

    <!-- Featured Recommendation Card (Request 8 & 10) -->
    @if(isset($recommendedContent) && $recommendedContent)
    <div class="featured-recommendation-card fade-in-up" onclick="triggerBeginSession('{{ $recommendedContent->id }}', '{{ $recommendedContent->title }}', false)" style="margin-bottom: 2rem; cursor: pointer;">
        <div class="featured-img-wrap">
            <img class="featured-img" src="{{ $recommendedContent->image_url ?? 'https://images.unsplash.com/photo-1506126613408-eca07ce68773?auto=format&fit=crop&q=80&w=800' }}" alt="{{ $recommendedContent->title }}">
        </div>
        <div style="flex: 1;">
            <span class="featured-badge">✨ Recommended For You</span>
            <h2 style="font-size: 1.3rem; font-weight: 900; color: #fff; margin: 0 0 0.5rem 0; line-height: 1.2;">
                {{ $recommendedContent->title }}
            </h2>
            <p style="font-size: 0.85rem; color: rgba(255,255,255,0.7); margin: 0 0 1rem 0; line-height: 1.45;">
                {{ $recommendationReason }}
            </p>
            <div style="display: flex; align-items: center; gap: 12px;">
                <button class="continue-session-btn" style="padding: 0.6rem 1.5rem; font-size: 0.8rem; box-shadow: 0 4px 14px rgba(139, 92, 246, 0.4); border: none; cursor: pointer;">
                    Start Session
                </button>
                <span style="font-size: 0.75rem; color: var(--vg-text-faint); font-weight: 700; display: flex; align-items: center; gap: 4px;">
                    <i data-lucide="clock" style="width: 14px; height: 14px; color: var(--vg-accent);"></i>
                    {{ $recommendedContent->duration_minutes }} Mins • {{ $recommendedContent->category }}
                </span>
            </div>
        </div>
    </div>
    @endif
    
    <!-- Categories -->
    @php
        $catIcons = [
            'Meditation' => 'flower-2',
            'Recovery' => 'heart-pulse',
            'Breathing' => 'wind',
        ];
    @endphp
    <div style="display:flex;gap:1rem;margin-bottom:1.5rem;overflow-x:auto;padding-bottom:.5rem;" class="mindfulness-categories-bar fade-in-up delay-1">
        <a href="{{ route('mindfulness.index') }}" 
           class="category-pill"
           style="background:{{ !request('category') ? 'var(--vg-gradient)' : 'var(--vg-panel)' }};">
           <i data-lucide="compass" style="width:16px;height:16px;"></i>
           <span>All</span>
        </a>
        @foreach($categories as $cat)
            @php
                $icon = $catIcons[$cat] ?? 'circle';
            @endphp
            <a href="{{ route('mindfulness.index', ['category' => $cat]) }}" 
               class="category-pill"
               style="background:{{ request('category') == $cat ? 'var(--vg-gradient)' : 'var(--vg-panel)' }};">
               <i data-lucide="{{ $icon }}" style="width:16px;height:16px;"></i>
               <span>{{ $cat }}</span>
            </a>
        @endforeach
    </div>

    <!-- Content Grid -->
    <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(300px,1fr));gap:1.5rem;" class="fade-in-up delay-2">
        @forelse($contents as $index => $item)
            @php
                $metadata = match($item->category) {
                    'Meditation' => ['icon' => 'moon', 'text' => 'Beginner • Stress Relief'],
                    'Recovery' => ['icon' => 'shield', 'text' => 'Guided • Muscle Recovery'],
                    'Breathing' => ['icon' => 'wind', 'text' => 'Calm • Deep Breathing'],
                    default => ['icon' => 'sparkles', 'text' => 'Guided Session']
                };
            @endphp
            <div class="mindfulness-card" onclick="triggerBeginSession('{{ $item->id }}', '{{ $item->title }}', true)" style="cursor: pointer;">
                <div class="mindfulness-image-container">
                    <img class="mindfulness-image" src="{{ $item->image_url ?? 'https://images.unsplash.com/photo-1506126613408-eca07ce68773?auto=format&fit=crop&q=80&w=800' }}" alt="{{ $item->title }}">
                    <div style="position:absolute;inset:0;background:linear-gradient(to top, rgba(8,8,26,0.85) 0%, transparent 100%);"></div>
                    
                    {{-- Duration badge with high visibility --}}
                    <div style="position:absolute;top:12px;right:12px;background:rgba(8, 8, 26, 0.85);border:1px solid rgba(255,255,255,0.15);backdrop-filter:blur(12px);padding:5px 12px;border-radius:10px;font-size:.7rem;font-weight:800;color:#fff;display:inline-flex;align-items:center;gap:6px;box-shadow:0 4px 12px rgba(0,0,0,0.35);">
                        <i data-lucide="clock" style="width:12px;height:12px;color:var(--vg-accent);"></i>
                        <span>{{ $item->duration_minutes }} min</span>
                    </div>
                </div>
                
                <div style="padding:1.5rem;display:flex;flex-direction:column;flex-grow:1;">
                    <div style="display:flex;align-items:center;gap:8px;margin-bottom:0.4rem;">
                        <span style="font-size:.7rem;font-weight:800;color:var(--vg-accent);text-transform:uppercase;letter-spacing:.05em;">{{ $item->category }}</span>
                    </div>
                    
                    {{-- Metadata row --}}
                    <div style="display:flex;align-items:center;gap:6px;margin-bottom:0.75rem;color:rgba(255,255,255,0.55);font-size:0.75rem;font-weight:600;">
                        <i data-lucide="{{ $metadata['icon'] }}" style="width:14px;height:14px;color:rgba(255,255,255,0.4);"></i>
                        <span>{{ $metadata['text'] }}</span>
                    </div>
                    
                    <h3 style="font-size:1.1rem;font-weight:800;color:var(--vg-text-strong);margin:0 0 .8rem;line-height:1.35;">{{ $item->title }}</h3>
                    
                    <p style="color:rgba(255,255,255,0.65);font-size:.85rem;margin-bottom:1.5rem;line-height:1.5;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden;flex-grow:1;">
                        {{ $item->description }}
                    </p>
                    
                    @if($index === 0 && !request('category'))
                        <div style="margin-top:auto;display:flex;flex-direction:column;gap:8px;">
                            <button class="continue-session-btn" style="width: 100%; border: none;">
                                Continue Session
                            </button>
                            <span style="font-size:0.68rem;font-weight:700;color:#10b981;text-align:center;display:flex;align-items:center;justify-content:center;gap:5px;">
                                <span class="pulse-dot"></span>
                                Last played yesterday
                            </span>
                        </div>
                    @else
                        <div style="margin-top:auto;">
                            <button class="begin-session-btn" style="width: 100%; border: 1px solid rgba(255,255,255,0.08); background: rgba(255,255,255,0.03);">
                                Begin Session
                            </button>
                        </div>
                    @endif
                </div>
            </div>
        @empty
            <div style="grid-column:1/-1;text-align:center;padding:4rem;background:var(--vg-panel);border:1px solid var(--vg-border);border-radius:24px;">
                <p style="color:var(--vg-text-muted);">No mindfulness content available yet.</p>
            </div>
        @endforelse
    </div>
</div>

<!-- Pre-Session Mood Check-In Modal (Request 4) -->
<div id="moodModalOverlay" class="mood-modal-overlay" onclick="closeMoodModalOnOuter(event)">
    <div class="mood-modal-card">
        <h3 style="font-size: 1.3rem; font-weight: 800; color: #fff; margin: 0 0 0.5rem 0;">🧘 How are you feeling today?</h3>
        <p style="font-size: 0.82rem; color: var(--vg-text-muted); margin-bottom: 1.5rem;" id="modal_prompt_desc">Check in with your current state before starting <span id="modal_session_title" style="color: #c084fc; font-weight: 700;">your session</span></p>
        
        <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 0.75rem; margin-bottom: 1.5rem;">
            <button class="mood-btn" onclick="selectMood('Stressed 😓')">
                <span style="font-size: 1.8rem;">😓</span>
                <span style="font-size: 0.72rem; text-transform: uppercase; font-weight: 800; letter-spacing: 0.05em; color: rgba(255,255,255,0.75)">Stressed</span>
            </button>
            <button class="mood-btn" onclick="selectMood('Tired 😴')">
                <span style="font-size: 1.8rem;">😴</span>
                <span style="font-size: 0.72rem; text-transform: uppercase; font-weight: 800; letter-spacing: 0.05em; color: rgba(255,255,255,0.75)">Tired</span>
            </button>
            <button class="mood-btn" onclick="selectMood('Calm 😊')">
                <span style="font-size: 1.8rem;">😊</span>
                <span style="font-size: 0.72rem; text-transform: uppercase; font-weight: 800; letter-spacing: 0.05em; color: rgba(255,255,255,0.75)">Calm</span>
            </button>
            <button class="mood-btn" onclick="selectMood('Motivated 🔥')">
                <span style="font-size: 1.8rem;">🔥</span>
                <span style="font-size: 0.72rem; text-transform: uppercase; font-weight: 800; letter-spacing: 0.05em; color: rgba(255,255,255,0.75)">Motivated</span>
            </button>
        </div>
        
        <button class="begin-session-btn" onclick="closeMoodModal()" style="width: 100%; cursor: pointer; border: 1px solid rgba(255,255,255,0.1); background: rgba(255,255,255,0.02);">
            Skip Check-in
        </button>
    </div>
</div>

<script>
    // Initialize & update stats from LocalStorage
    document.addEventListener('DOMContentLoaded', function() {
        // Streak: default to 6
        if (!localStorage.getItem('recovery_streak')) {
            localStorage.setItem('recovery_streak', '6');
        }
        document.getElementById('stat_streak').innerText = localStorage.getItem('recovery_streak') + ' Days';

        // Completed: default to 12
        if (!localStorage.getItem('sessions_completed')) {
            localStorage.setItem('sessions_completed', '12');
        }
        document.getElementById('stat_sessions').innerText = localStorage.getItem('sessions_completed') + ' Sessions';

        // Minutes: default to 85
        if (!localStorage.getItem('mindfulness_minutes')) {
            localStorage.setItem('mindfulness_minutes', '85');
        }
        document.getElementById('stat_minutes').innerText = localStorage.getItem('mindfulness_minutes') + ' mins';

        // Mood
        const userMood = localStorage.getItem('user_mood') || 'Not Set';
        updateMoodDisplay(userMood);
    });

    function updateMoodDisplay(mood) {
        const moodEl = document.getElementById('stat_mood');
        if (moodEl) {
            if (mood === 'Not Set') {
                moodEl.innerHTML = `<span>Not Set</span> <span style="font-size: 0.75rem; color: var(--vg-text-faint);">✏️</span>`;
            } else {
                moodEl.innerHTML = `<span style="color: #6ee7b7; font-weight: 800;">${mood}</span> <span style="font-size: 0.75rem; color: var(--vg-text-faint);">✏️</span>`;
            }
        }
    }

    let pendingSessionId = null;
    let pendingShowVideo = true;

    function triggerBeginSession(sessionId, sessionTitle, showVideo = true) {
        pendingSessionId = sessionId;
        pendingShowVideo = showVideo;
        document.getElementById('modal_session_title').innerText = '"' + sessionTitle + '"';
        document.getElementById('moodModalOverlay').classList.add('active');
    }

    function openMoodModal() {
        pendingSessionId = null;
        pendingShowVideo = false;
        document.getElementById('modal_session_title').innerText = 'your recovery';
        document.getElementById('moodModalOverlay').classList.add('active');
    }

    function closeMoodModal() {
        document.getElementById('moodModalOverlay').classList.remove('active');
        if (pendingSessionId) {
            // If they click skip, proceed directly
            window.location.href = `/mindfulness/${pendingSessionId}?show_video=${pendingShowVideo}`;
        }
    }

    function closeMoodModalOnOuter(event) {
        if (event.target.id === 'moodModalOverlay') {
            document.getElementById('moodModalOverlay').classList.remove('active');
            pendingSessionId = null;
        }
    }

    function selectMood(mood) {
        localStorage.setItem('user_mood', mood);
        updateMoodDisplay(mood);
        document.getElementById('moodModalOverlay').classList.remove('active');
        if (pendingSessionId) {
            window.location.href = `/mindfulness/${pendingSessionId}?mood=${encodeURIComponent(mood)}&show_video=${pendingShowVideo}`;
        }
    }
</script>
@endsection
