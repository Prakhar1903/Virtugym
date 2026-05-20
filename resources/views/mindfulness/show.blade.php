@extends('layouts.app')

@section('title', $content->title)

@section('content')
<style>
    .player-container {
        display: grid;
        grid-template-columns: 1.2fr 1fr;
        gap: 2rem;
        max-width: 1200px;
        margin: 0 auto;
    }
    @media (max-width: 900px) {
        .player-container {
            grid-template-columns: 1fr;
        }
    }
    
    .player-visuals-card {
        background: var(--vg-panel);
        border: 1px solid var(--vg-border);
        border-radius: 28px;
        padding: 2.5rem;
        display: flex;
        flex-direction: column;
        align-items: center;
        text-align: center;
        position: relative;
        box-shadow: 0 15px 35px rgba(0,0,0,0.2);
    }
    
    .player-details-card {
        background: var(--vg-panel);
        border: 1px solid var(--vg-border);
        border-radius: 28px;
        padding: 2.5rem;
        display: flex;
        flex-direction: column;
        gap: 1.8rem;
    }

    /* Circular progress indicator */
    .progress-ring-wrap {
        position: relative;
        width: 220px;
        height: 220px;
        margin-bottom: 1.5rem;
    }
    .progress-ring-circle-bg {
        fill: transparent;
        stroke: rgba(255,255,255,0.04);
        stroke-width: 8;
    }
    .progress-ring-circle {
        fill: transparent;
        stroke: url(#progressGradient);
        stroke-width: 8;
        stroke-linecap: round;
        transition: stroke-dashoffset 0.35s;
        transform: rotate(-90deg);
        transform-origin: 50% 50%;
    }
    .timer-text {
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        font-size: 2.2rem;
        font-weight: 900;
        color: #fff;
        font-family: monospace;
    }

    /* Breathing Guide widget */
    .breathing-guide-box {
        margin: 2rem 0;
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 12px;
    }
    .breathing-outer-ring {
        width: 100px;
        height: 100px;
        border-radius: 50%;
        background: rgba(139, 92, 246, 0.08);
        border: 2px solid rgba(139, 92, 246, 0.25);
        display: flex;
        align-items: center;
        justify-content: center;
        transition: transform 4s cubic-bezier(0.4, 0, 0.2, 1);
        box-shadow: 0 0 20px rgba(139, 92, 246, 0.15);
    }
    .breathing-inner-circle {
        width: 60px;
        height: 60px;
        border-radius: 50%;
        background: var(--vg-gradient);
        opacity: 0.8;
        box-shadow: 0 0 20px rgba(139, 92, 246, 0.4);
        transition: transform 4s cubic-bezier(0.4, 0, 0.2, 1);
    }
    
    /* Breathing animation states */
    .breath-inhale .breathing-outer-ring {
        transform: scale(1.6);
        box-shadow: 0 0 40px rgba(139, 92, 246, 0.4);
    }
    .breath-inhale .breathing-inner-circle {
        transform: scale(1.4);
        opacity: 1;
    }
    .breath-exhale .breathing-outer-ring {
        transform: scale(1.0);
        box-shadow: 0 0 20px rgba(139, 92, 246, 0.15);
    }
    .breath-exhale .breathing-inner-circle {
        transform: scale(1.0);
        opacity: 0.8;
    }

    /* Ambient Audio Chips */
    .ambient-sounds-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 0.75rem;
    }
    .ambient-sound-card {
        background: rgba(255,255,255,0.02);
        border: 1px solid rgba(255,255,255,0.06);
        border-radius: 16px;
        padding: 1rem;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: space-between;
        transition: all 0.3s ease;
    }
    .ambient-sound-card:hover {
        background: rgba(255,255,255,0.04);
        border-color: rgba(139,92,246,0.3);
    }
    .ambient-sound-card.active {
        background: rgba(139,92,246,0.12);
        border-color: rgba(139,92,246,0.5);
        box-shadow: 0 4px 15px rgba(139, 92, 246, 0.15);
    }
    .sound-pulse-dot {
        width: 8px;
        height: 8px;
        border-radius: 50%;
        background: #10b981;
        opacity: 0;
        transition: opacity 0.3s ease;
    }
    .ambient-sound-card.active .sound-pulse-dot {
        opacity: 1;
        animation: pulse-online 1.5s infinite alternate;
    }

    /* Post-Session Modal */
    .post-modal-overlay {
        position: fixed;
        inset: 0;
        background: rgba(8, 8, 26, 0.85);
        backdrop-filter: blur(12px);
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 1060;
        opacity: 0;
        pointer-events: none;
        transition: all 0.4s ease;
    }
    .post-modal-overlay.active {
        opacity: 1;
        pointer-events: auto;
    }
    .post-modal-card {
        background: #0d0c22;
        border: 1px solid rgba(139, 92, 246, 0.25);
        border-radius: 28px;
        width: 480px;
        max-width: 90%;
        padding: 2.5rem;
        box-shadow: 0 20px 50px rgba(0,0,0,0.5);
        transform: translateY(20px) scale(0.95);
        transition: all 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
        text-align: center;
    }
    .post-modal-overlay.active .post-modal-card {
        transform: translateY(0) scale(1);
    }
    .post-mood-btn {
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
    .post-mood-btn:hover {
        background: rgba(139,92,246,0.12);
        border-color: rgba(139,92,246,0.4);
        transform: translateY(-3px);
    }
</style>

<!-- Hidden Ambient Audio Elements -->
<audio id="audio_rain" src="https://archive.org/download/various-sound-effects/rain.mp3" loop preload="auto"></audio>
<audio id="audio_ocean" src="https://archive.org/download/various-sound-effects/oside1.mp3" loop preload="auto"></audio>
<audio id="audio_fire" src="https://archive.org/download/various-sound-effects/fire.mp3" loop preload="auto"></audio>
<audio id="audio_forest" src="https://archive.org/download/various-sound-effects/forest.mp3" loop preload="auto"></audio>

<div style="max-width:1200px;margin:0 auto;padding-bottom:3rem;">
    <!-- Navigation Back Link -->
    <div style="margin-bottom:1.5rem;" class="fade-in-up">
        <a href="{{ route('mindfulness.index') }}" style="color:var(--vg-text-muted);text-decoration:none;font-size:.85rem;display:flex;align-items:center;gap:6px;margin-bottom:1rem;" onclick="stopAllAudio()">
            <i data-lucide="arrow-left" style="width:16px;"></i> Back to Library
        </a>
    </div>

    <div class="player-container">
        <!-- Left Side: Active Session Player (Ring, Breathing Animation, Controls) -->
        <div class="player-visuals-card fade-in-up">
            <!-- Immersive Video Player (Request: Plays video on click) -->
            @if($content->media_url && request('show_video') === 'true')
                <div style="width:100%;border-radius:18px;overflow:hidden;border:1px solid rgba(255,255,255,0.08);margin-bottom:1.5rem;box-shadow: 0 10px 30px rgba(0,0,0,0.45);background:#000;">
                    @if(Str::contains($content->media_url, ['youtube.com', 'youtu.be']))
                        @php 
                            $videoId = '';
                            if (preg_match('%(?:youtube(?:-nocookie)?\.com/(?:[^/]+/.+/|(?:v|e(?:mbed)?)/|.*[?&]v=)|youtu\.be/)([^"&?/ ]{11})%i', $content->media_url, $match)) {
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
                        <video src="{{ $content->media_url }}" controls style="width:100%;display:block;"></video>
                    @endif
                </div>
            @endif

            <span style="font-size:0.7rem;font-weight:800;color:var(--vg-accent);text-transform:uppercase;letter-spacing:.05em;margin-bottom:0.3rem;" id="player_current_category">
                {{ $content->category }}
            </span>
            <h1 style="font-size:1.4rem;font-weight:900;color:#fff;margin:0 0 1rem 0;line-height:1.25;">
                {{ $content->title }}
            </h1>

            <!-- Pre-session Check-in Mood badge -->
            <div style="margin-bottom:1.2rem;" id="pre_mood_badge_container">
                <span style="font-size:0.72rem;background:rgba(139,92,246,0.12);border:1px solid rgba(139,92,246,0.25);color:#c4b5fd;padding:6px 12px;border-radius:12px;font-weight:700;display:inline-flex;align-items:center;gap:6px;">
                    🧠 Intended Mood: <span id="intended_mood_span">Calm 😊</span>
                </span>
            </div>

            <!-- Sub-grid: Progress ring & Breathing side-by-side -->
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:1.5rem;align-items:center;width:100%;margin-bottom:1.5rem;border-top:1px solid var(--vg-border);border-bottom:1px solid var(--vg-border);padding:1.5rem 0;">
                <!-- Session Progress Ring -->
                <div style="display:flex;flex-direction:column;align-items:center;">
                    <div class="progress-ring-wrap" style="width:160px;height:160px;margin-bottom:0;">
                        <svg width="160" height="160">
                            <circle class="progress-ring-circle-bg" cx="80" cy="80" r="70" stroke-width="6" />
                            <circle class="progress-ring-circle" id="playerProgressCircle" cx="80" cy="80" r="70" stroke-width="6" stroke-dasharray="439.82" stroke-dashoffset="0" />
                        </svg>
                        <div class="timer-text" id="playerTimerDisplay" style="font-size:1.6rem;">00:00</div>
                    </div>
                </div>

                <!-- Breathing Guide Widget -->
                <div class="breathing-guide-box" id="breathingGuideBox" style="margin:0;">
                    <div class="breathing-outer-ring" style="width:80px;height:80px;">
                        <div class="breathing-inner-circle" style="width:48px;height:48px;"></div>
                    </div>
                    <p style="font-size:0.8rem;color:var(--vg-text-strong);font-weight:800;margin:6px 0 0 0;" id="breathingGuideText">Inhale deeply...</p>
                    <p style="font-size:0.65rem;color:var(--vg-text-muted);margin:2px 0 0 0;" id="breathingGuideSubtext">Follow the rhythm of the circle</p>
                </div>
            </div>

            <!-- Timer controls -->
            <div style="display:flex;align-items:center;gap:1rem;width:100%;">
                <button id="btnPlayPause" class="continue-session-btn" style="flex:1;cursor:pointer;border:none;display:flex;align-items:center;justify-content:center;gap:8px;" onclick="togglePlayPause()">
                    <i data-lucide="play" id="btnPlayIcon" style="width:18px;height:18px;"></i>
                    <span id="btnPlayText">Start Session</span>
                </button>
                <button class="begin-session-btn" style="padding:.8rem 1.2rem;cursor:pointer;" onclick="completeSessionManual()">
                    Complete Now
                </button>
            </div>
        </div>

        <!-- Right Side: Details & Ambient Sound controls -->
        <div class="player-details-card fade-in-up delay-1">
            <!-- Ambient Audio / Nature Sounds (Request 1) -->
            <div>
                <h3 style="font-size:0.95rem;font-weight:800;color:#fff;margin:0 0 0.8rem 0;display:flex;align-items:center;gap:8px;">
                    <span>🎵 Ambient Nature Sounds</span>
                    <span style="font-size:0.65rem;background:rgba(167,139,250,0.15);color:#c084fc;padding:2px 6px;border-radius:4px;font-weight:700;">Lo-Fi layer</span>
                </h3>
                <p style="font-size:0.75rem;color:var(--vg-text-muted);margin:0 0 1rem 0;">Mix and match ambient loops to personalize your meditation atmosphere.</p>
                
                <div class="ambient-sounds-grid">
                    <div class="ambient-sound-card" id="card_rain" onclick="toggleAmbient('rain')">
                        <span style="font-size:0.85rem;font-weight:700;color:#fff;display:flex;align-items:center;gap:6px;">
                            <span>🌧️</span> Rain Sound
                        </span>
                        <span class="sound-pulse-dot"></span>
                    </div>
                    <div class="ambient-sound-card" id="card_ocean" onclick="toggleAmbient('ocean')">
                        <span style="font-size:0.85rem;font-weight:700;color:#fff;display:flex;align-items:center;gap:6px;">
                            <span>🌊</span> Ocean Waves
                        </span>
                        <span class="sound-pulse-dot"></span>
                    </div>
                    <div class="ambient-sound-card" id="card_fire" onclick="toggleAmbient('fire')">
                        <span style="font-size:0.85rem;font-weight:700;color:#fff;display:flex;align-items:center;gap:6px;">
                            <span>🔥</span> Fire Crackle
                        </span>
                        <span class="sound-pulse-dot"></span>
                    </div>
                    <div class="ambient-sound-card" id="card_forest" onclick="toggleAmbient('forest')">
                        <span style="font-size:0.85rem;font-weight:700;color:#fff;display:flex;align-items:center;gap:6px;">
                            <span>🌲</span> Forest River
                        </span>
                        <span class="sound-pulse-dot"></span>
                    </div>
                </div>
            </div>

            <!-- Session Guidelines / Text -->
            <div style="border-top:1px solid var(--vg-border);padding-top:1.5rem;">
                <h3 style="font-size:0.95rem;font-weight:800;color:#fff;margin:0 0 0.8rem 0;">📖 Instructions</h3>
                <div style="color:rgba(255,255,255,0.7);line-height:1.6;font-size:0.85rem;">
                    {!! nl2br(e($content->content)) !!}
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Post-Session Mood Check-In Modal (Request 4) -->
<div id="postSessionModalOverlay" class="post-modal-overlay" onclick="preventClose(event)">
    <div class="post-modal-card">
        <span style="font-size:3rem;display:block;margin-bottom:0.5rem;">🎉</span>
        <h3 style="font-size:1.4rem;font-weight:900;color:#fff;margin:0 0 0.5rem 0;">Session Completed!</h3>
        <p style="font-size:0.82rem;color:var(--vg-text-muted);margin-bottom:1.8rem;">Fantastic work taking time to recover. How do you feel now?</p>
        
        <div style="display:grid;grid-template-columns:repeat(4,1fr);gap:0.75rem;margin-bottom:1.8rem;">
            <button class="post-mood-btn" onclick="savePostMood('Refreshed 🌸')">
                <span style="font-size:1.8rem;">🌸</span>
                <span style="font-size:0.68rem;text-transform:uppercase;font-weight:800;letter-spacing:0.05em;color:rgba(255,255,255,0.75)">Refreshed</span>
            </button>
            <button class="post-mood-btn" onclick="savePostMood('Energized ⚡')">
                <span style="font-size:1.8rem;">⚡</span>
                <span style="font-size:0.68rem;text-transform:uppercase;font-weight:800;letter-spacing:0.05em;color:rgba(255,255,255,0.75)">Energized</span>
            </button>
            <button class="post-mood-btn" onclick="savePostMood('Peaceful 🍃')">
                <span style="font-size:1.8rem;">🍃</span>
                <span style="font-size:0.68rem;text-transform:uppercase;font-weight:800;letter-spacing:0.05em;color:rgba(255,255,255,0.75)">Peaceful</span>
            </button>
            <button class="post-mood-btn" onclick="savePostMood('Tired 💤')">
                <span style="font-size:1.8rem;">💤</span>
                <span style="font-size:0.68rem;text-transform:uppercase;font-weight:800;letter-spacing:0.05em;color:rgba(255,255,255,0.75)">Relaxed</span>
            </button>
        </div>
        
        <button class="continue-session-btn" onclick="finishSessionComplete()" style="width:100%;border:none;cursor:pointer;">
            Complete & Exit
        </button>
    </div>
</div>

<script>
    // Config details
    const totalDurationSeconds = {{ $content->duration_minutes }} * 60;
    let timeRemaining = totalDurationSeconds;
    let timerInterval = null;
    let isPlaying = false;
    
    // Circle progress properties
    const circle = document.getElementById('playerProgressCircle');
    const radius = circle.r.baseVal.value;
    const circumference = radius * 2 * Math.PI;

    // Breathing parameters
    let breathingCycleInterval = null;
    let breathingPhase = 0; // 0 = Inhale, 1 = Hold, 2 = Exhale

    document.addEventListener('DOMContentLoaded', function() {
        // Parse current intended mood from url param or storage
        const urlParams = new URLSearchParams(window.location.search);
        const intendedMood = urlParams.get('mood') || localStorage.getItem('user_mood') || 'Not Set';
        
        if (intendedMood && intendedMood !== 'Not Set') {
            document.getElementById('intended_mood_span').innerText = decodeURIComponent(intendedMood);
            document.getElementById('pre_mood_badge_container').style.display = 'block';
        } else {
            document.getElementById('pre_mood_badge_container').style.display = 'none';
        }

        // Initialize progress bar
        circle.style.strokeDasharray = `${circumference} ${circumference}`;
        updateTimerProgress();
    });

    function updateTimerProgress() {
        // Format display text
        const minutes = Math.floor(timeRemaining / 60);
        const seconds = timeRemaining % 60;
        document.getElementById('playerTimerDisplay').innerText = 
            `${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`;
        
        // Progress ring offset
        const progressFraction = timeRemaining / totalDurationSeconds;
        const offset = circumference * (1 - progressFraction);
        circle.style.strokeDashoffset = offset;
    }

    function togglePlayPause() {
        if (isPlaying) {
            pauseTimer();
        } else {
            playTimer();
        }
    }

    function playTimer() {
        isPlaying = true;
        document.getElementById('btnPlayText').innerText = "Pause Session";
        
        const playIcon = document.getElementById('btnPlayIcon');
        playIcon.setAttribute('data-lucide', 'pause');
        if (window.lucide) window.lucide.createIcons();

        // Start countdown
        timerInterval = setInterval(() => {
            if (timeRemaining > 0) {
                timeRemaining--;
                updateTimerProgress();
            } else {
                completeSession();
            }
        }, 1000);

        // Start breathing cycle loops
        startBreathingCycle();
    }

    function pauseTimer() {
        isPlaying = false;
        document.getElementById('btnPlayText').innerText = "Resume Session";
        
        const playIcon = document.getElementById('btnPlayIcon');
        playIcon.setAttribute('data-lucide', 'play');
        if (window.lucide) window.lucide.createIcons();

        clearInterval(timerInterval);
        stopBreathingCycle();
    }

    // Breathing rhythm controller
    function startBreathingCycle() {
        const guideBox = document.getElementById('breathingGuideBox');
        const textEl = document.getElementById('breathingGuideText');
        const subtextEl = document.getElementById('breathingGuideSubtext');

        function triggerPhase() {
            if (!isPlaying) return;
            
            // Loop pattern: 4s inhale -> 4s hold -> 4s exhale
            if (breathingPhase === 0) {
                // Inhale
                guideBox.className = "breathing-guide-box breath-inhale";
                textEl.innerText = "Inhale slowly...";
                subtextEl.innerText = "Expand your lungs";
                breathingPhase = 1;
            } else if (breathingPhase === 1) {
                // Hold
                guideBox.className = "breathing-guide-box breath-inhale";
                textEl.innerText = "Hold your breath...";
                subtextEl.innerText = "Find calm in stillness";
                breathingPhase = 2;
            } else {
                // Exhale
                guideBox.className = "breathing-guide-box breath-exhale";
                textEl.innerText = "Exhale gently...";
                subtextEl.innerText = "Release all tension";
                breathingPhase = 0;
            }
        }
        
        triggerPhase();
        breathingCycleInterval = setInterval(triggerPhase, 4000);
    }

    function stopBreathingCycle() {
        clearInterval(breathingCycleInterval);
        const guideBox = document.getElementById('breathingGuideBox');
        guideBox.className = "breathing-guide-box breath-exhale";
        document.getElementById('breathingGuideText').innerText = "Session Paused";
        document.getElementById('breathingGuideSubtext').innerText = "Ready to resume breathing";
    }

    // Ambient audio handlers
    function toggleAmbient(sound) {
        const audio = document.getElementById(`audio_${sound}`);
        const card = document.getElementById(`card_${sound}`);
        if (!audio || !card) return;

        if (card.classList.contains('active')) {
            audio.pause();
            card.classList.remove('active');
        } else {
            card.classList.add('active');
            audio.play().catch(e => {
                console.log("Audio play error, requires user interaction first:", e);
            });
        }
    }

    function stopAllAudio() {
        ['rain', 'ocean', 'fire', 'forest'].forEach(sound => {
            const audio = document.getElementById(`audio_${sound}`);
            if (audio) {
                audio.pause();
                audio.currentTime = 0;
            }
        });
    }

    // Completion routines
    function completeSessionManual() {
        timeRemaining = 0;
        updateTimerProgress();
        completeSession();
    }

    function completeSession() {
        pauseTimer();
        stopAllAudio();
        
        // Show success / post-session check-in modal
        document.getElementById('postSessionModalOverlay').classList.add('active');
    }

    function preventClose(event) {
        event.stopPropagation();
    }

    function savePostMood(mood) {
        // Save post-mood selection
        localStorage.setItem('post_session_mood', mood);
        
        // Update dashboard values in local storage
        let currentCompleted = parseInt(localStorage.getItem('sessions_completed') || '12');
        localStorage.setItem('sessions_completed', (currentCompleted + 1).toString());

        let currentMinutes = parseInt(localStorage.getItem('mindfulness_minutes') || '85');
        localStorage.setItem('mindfulness_minutes', (currentMinutes + {{ $content->duration_minutes }}).toString());
        
        let currentStreak = parseInt(localStorage.getItem('recovery_streak') || '6');
        localStorage.setItem('recovery_streak', (currentStreak + 1).toString());

        finishSessionComplete();
    }

    function finishSessionComplete() {
        window.location.href = "{{ route('mindfulness.index') }}";
    }
</script>
@endsection
