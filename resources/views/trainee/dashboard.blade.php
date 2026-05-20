@extends('layouts.app')

@section('title', 'Trainee Dashboard')

@section('content')
<style>
    /* Card Height & Padding Adjustments (~20% reduction) */
    .dashboard-card {
        background: rgba(255, 255, 255, 0.03);
        border: 1px solid rgba(139, 92, 246, 0.18);
        border-radius: 20px;
        padding: 1.15rem 1.3rem;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        position: relative;
    }
    
    /* Hover Lift and Glow Interactions */
    .hover-lift {
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }
    .hover-lift:hover {
        transform: translateY(-4px);
        border-color: rgba(139, 92, 246, 0.45) !important;
        box-shadow: 0 10px 25px -5px rgba(139, 92, 246, 0.15), 0 0 15px rgba(139, 92, 246, 0.08);
    }
    
    /* Button Hover Scale */
    .btn-hover-scale {
        transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
    }
    .btn-hover-scale:hover {
        transform: scale(1.03);
        filter: brightness(1.1);
    }
    
    /* Quick Action Button Styles */
    .quick-action-btn {
        display: flex;
        align-items: center;
        gap: 12px;
        background: rgba(255, 255, 255, 0.03);
        border: 1px solid rgba(139, 92, 246, 0.15);
        border-radius: 16px;
        padding: 0.8rem 1.1rem;
        text-decoration: none;
        color: #fff;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }
    .quick-action-btn:hover {
        background: rgba(139, 92, 246, 0.08);
        border-color: rgba(139, 92, 246, 0.45);
        transform: translateY(-3px);
        box-shadow: 0 8px 20px -6px rgba(139, 92, 246, 0.25);
    }
    .action-icon {
        font-size: 1.5rem;
        transition: transform 0.3s ease;
    }
    .quick-action-btn:hover .action-icon {
        transform: scale(1.2) rotate(6deg);
    }
    .action-text {
        display: flex;
        flex-direction: column;
    }
    .action-title {
        font-size: 0.88rem;
        font-weight: 700;
        color: #e2d9f3;
    }
    .action-sub {
        font-size: 0.7rem;
        color: rgba(255, 255, 255, 0.4);
    }

    /* Grids & Responsiveness (Desktop 3cols, Tablet 2cols, Mobile stacked) */
    .dashboard-grid-3 {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 1.5rem;
    }
    .dashboard-grid-2 {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 1.5rem;
    }
    @media (max-width: 1024px) {
        .dashboard-grid-3 {
            grid-template-columns: repeat(2, 1fr);
        }
    }
    @media (max-width: 768px) {
        .dashboard-grid-3, .dashboard-grid-2 {
            grid-template-columns: 1fr;
        }
    }

    /* Trainer Grid - Limit horizontal stretching and density */
    .trainer-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(240px, 260px));
        gap: 1.5rem;
        justify-content: flex-start;
    }
    .trainer-card {
        background: linear-gradient(145deg, rgba(255, 255, 255, 0.03), rgba(255, 255, 255, 0.01));
        border: 1px solid rgba(139, 92, 246, 0.15);
        border-radius: 24px;
        padding: 1.5rem 1.25rem 1.25rem;
        text-align: center;
        transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        position: relative;
        overflow: hidden;
        display: flex;
        flex-direction: column;
        min-height: 380px;
    }
    .trainer-card:hover {
        border-color: rgba(236, 72, 153, 0.4);
        transform: translateY(-8px) scale(1.02);
        box-shadow: 0 15px 35px rgba(139, 92, 246, 0.25);
    }
    .trainer-card-overlay {
        position: absolute;
        inset: 0 0 72px 0;
        background: rgba(8, 8, 26, 0.88);
        backdrop-filter: blur(8px);
        -webkit-backdrop-filter: blur(8px);
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        opacity: 0;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        z-index: 5;
        pointer-events: none;
    }
    .trainer-card:hover .trainer-card-overlay {
        opacity: 1;
        pointer-events: auto;
    }
    .view-profile-btn {
        background: linear-gradient(135deg, #8b5cf6, #ec4899);
        color: #fff;
        padding: 10px 20px;
        border-radius: 50px;
        font-size: 0.8rem;
        font-weight: 800;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        box-shadow: 0 8px 24px rgba(139, 92, 246, 0.4);
        transform: translateY(15px);
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }
    .trainer-card:hover .view-profile-btn {
        transform: translateY(0);
    }

    /* Weekly Calorie Chart Enhancements */
    .chart-bar-container {
        flex: 1;
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 6px;
        height: 100%;
    }
    .chart-bar-wrapper {
        flex: 1;
        width: 100%;
        display: flex;
        align-items: flex-end;
        justify-content: center;
    }
    .chart-bar {
        width: 60%;
        background: rgba(139, 92, 246, 0.35);
        border-radius: 6px 6px 0 0;
        position: relative;
        cursor: pointer;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        height: 0;
        animation: growBar 0.8s cubic-bezier(0.25, 1, 0.5, 1) forwards;
    }
    .chart-bar.current-week {
        background: linear-gradient(to top, #f9a8d4, #8b5cf6);
        box-shadow: 0 0 10px rgba(139, 92, 246, 0.25);
    }
    .chart-bar:hover {
        filter: brightness(1.2);
        box-shadow: 0 0 15px rgba(139, 92, 246, 0.5);
    }
    .chart-bar::after {
        content: attr(data-tooltip);
        position: absolute;
        top: -36px;
        left: 50%;
        transform: translateX(-50%) scale(0.85);
        background: rgba(15, 23, 42, 0.95);
        border: 1px solid rgba(139, 92, 246, 0.3);
        color: #fff;
        padding: 5px 10px;
        font-size: 0.72rem;
        font-weight: 700;
        border-radius: 8px;
        opacity: 0;
        pointer-events: none;
        transition: all 0.2s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        white-space: nowrap;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.6);
        z-index: 10;
    }
    .chart-bar:hover::after {
        opacity: 1;
        transform: translateX(-50%) scale(1);
    }
    @keyframes growBar {
        to { height: var(--target-height); }
    }

    /* Skeleton Loading Shimmer effect */
    .shimmer {
        background: linear-gradient(90deg, rgba(255, 255, 255, 0.03) 25%, rgba(255, 255, 255, 0.08) 50%, rgba(255, 255, 255, 0.03) 75%);
        background-size: 200% 100%;
        animation: loading-shimmer 1.5s infinite;
    }
    @keyframes loading-shimmer {
        0% { background-position: 200% 0; }
        100% { background-position: -200% 0; }
    }
    .skeleton-box {
        border-radius: 12px;
    }
</style>

{{-- Skeleton Loading Screen (Hidden after simulated load) --}}
<div id="dashboard-skeleton" style="max-width:1450px;margin:0 auto;padding-bottom:2rem;">
    <div style="height:140px;border-radius:24px;margin-bottom:1.5rem;" class="shimmer"></div>
    <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(200px,1fr));gap:1rem;margin-bottom:1.5rem;">
        <div style="height:62px;" class="shimmer skeleton-box"></div>
        <div style="height:62px;" class="shimmer skeleton-box"></div>
        <div style="height:62px;" class="shimmer skeleton-box"></div>
        <div style="height:62px;" class="shimmer skeleton-box"></div>
    </div>
    <div style="height:160px;border-radius:20px;margin-bottom:1.5rem;" class="shimmer"></div>
    <div style="height:120px;border-radius:20px;margin-bottom:1.5rem;" class="shimmer"></div>
    <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(300px,1fr));gap:1.5rem;margin-bottom:2rem;">
        <div style="height:190px;border-radius:20px;" class="shimmer"></div>
        <div style="height:190px;border-radius:20px;" class="shimmer"></div>
        <div style="height:190px;border-radius:20px;" class="shimmer"></div>
    </div>
</div>

{{-- Real Dashboard Content --}}
<div id="dashboard-real-content" style="max-width:1450px;margin:0 auto;display:none;opacity:0;transition:opacity 0.4s ease;padding-bottom:2rem;">

    {{-- Welcome Banner --}}
    <div style="background:linear-gradient(135deg,rgba(139,92,246,.18),rgba(236,72,153,.12));border:1px solid rgba(139,92,246,.28);border-radius:24px;padding:1.8rem 2.2rem;margin-bottom:1.5rem;position:relative;overflow:hidden;display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:1.5rem;" class="fade-in-up">
        <div style="position:absolute;inset:0;background:conic-gradient(from 0deg at 100% 0%,rgba(139,92,246,.08) 0deg,transparent 80deg);pointer-events:none;"></div>
        <div style="position:relative;z-index:1;display:flex;align-items:center;gap:1.2rem;">
            <div style="width:58px;height:58px;border-radius:50%;background:linear-gradient(135deg,#c4b5fd,#f9a8d4);display:flex;align-items:center;justify-content:center;font-size:1.6rem;font-weight:bold;color:#fff;">
                {{ substr(auth()->user()->name, 0, 1) }}
            </div>
            <div>
                <h1 style="font-size:clamp(1.4rem,2.8vw,2rem);font-weight:900;background:linear-gradient(135deg,#fff 20%,#c4b5fd 60%,#f9a8d4 90%);-webkit-background-clip:text;background-clip:text;color:transparent;margin-bottom:.2rem;">
                    Welcome back, {{ explode(' ', auth()->user()->name)[0] }}! 💪
                </h1>
                <p style="color:rgba(255,255,255,.38);font-size:0.88rem;">Track your fitness journey — you're doing great this week. Keep the momentum going!</p>
            </div>
        </div>
        <div style="position:relative;z-index:1;text-align:right;">
            <div style="display:inline-flex;align-items:center;gap:8px;background:rgba(255,255,255,.05);border:1px solid rgba(255,255,255,.1);padding:8px 16px;border-radius:16px;">
                <span style="font-size:1.6rem;">🔥</span>
                <div style="text-align:left;">
                    <div style="font-size:1.4rem;font-weight:900;color:#fff;line-height:1;">{{ $streak ?? 0 }}</div>
                    <div style="font-size:.7rem;color:rgba(255,255,255,.5);font-weight:600;text-transform:uppercase;">Day Streak</div>
                </div>
            </div>
        </div>
    </div>

    {{-- Mini Quick Actions (Improvements) --}}
    <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(200px,1fr));gap:1rem;margin-bottom:1.5rem;" class="fade-in-up">
        <a href="{{ route('workouts.index') }}" class="quick-action-btn hover-lift btn-hover-scale">
            <span class="action-icon">💪</span>
            <div class="action-text">
                <span class="action-title">Start Workout</span>
                <span class="action-sub">View programs & logs</span>
            </div>
        </a>
        <a href="{{ route('water.index') }}" class="quick-action-btn hover-lift btn-hover-scale">
            <span class="action-icon">💧</span>
            <div class="action-text">
                <span class="action-title">Log Water</span>
                <span class="action-sub">Track daily hydration</span>
            </div>
        </a>
        <a href="{{ route('progress.index') }}" class="quick-action-btn hover-lift btn-hover-scale">
            <span class="action-icon">📈</span>
            <div class="action-text">
                <span class="action-title">Track Progress</span>
                <span class="action-sub">Log weight & stats</span>
            </div>
        </a>
        <a href="{{ route('trainee.trainers') }}" class="quick-action-btn hover-lift btn-hover-scale">
            <span class="action-icon">🤝</span>
            <div class="action-text">
                <span class="action-title">Book Trainer</span>
                <span class="action-sub">Find a personal coach</span>
            </div>
        </a>
    </div>

    {{-- Available Trainers --}}
    <div class="dashboard-card hover-lift" style="margin-bottom:1.5rem;">
        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:1.1rem;">
            <h2 style="font-size:0.95rem;font-weight:700;color:#e2d9f3;">🏆 Available Trainers</h2>
            <a href="{{ route('trainee.trainers') }}" style="font-size:.78rem;color:#a78bfa;font-weight:600;text-decoration:none;transition:color .2s;" onmouseover="this.style.color='#c4b5fd'" onmouseout="this.style.color='#a78bfa'">View All →</a>
        </div>
        <div class="trainer-grid">
            @if(isset($availableTrainers) && $availableTrainers->count() > 0)
                @foreach($availableTrainers as $trainer)
                    <div class="trainer-card">
                        <!-- HOVER QUICK ACTION OVERLAY -->
                        <div class="trainer-card-overlay">
                            <a href="{{ route('trainee.trainers', ['search' => $trainer->name]) }}" class="view-profile-btn">
                                View Profile <i data-lucide="arrow-right" style="width: 14px; height: 14px;"></i>
                            </a>
                        </div>

                        <!-- TOP: Avatar + online -->
                        <div style="position: relative; width: 80px; height: 80px; margin: 0 auto 15px;">
                            @if($trainer->profile_photo)
                                <img src="{{ asset('storage/' . $trainer->profile_photo) }}" alt="{{ $trainer->name }}" style="width: 100%; height: 100%; border-radius: 50%; object-fit: cover; border: 2px solid rgba(139,92,246,0.3);" />
                            @else
                                @php
                                    $words = explode(' ', $trainer->name);
                                    $initials = '';
                                    foreach ($words as $w) {
                                        $initials .= strtoupper(substr($w, 0, 1));
                                    }
                                    $initials = substr($initials, 0, 2);
                                @endphp
                                <div style="width: 100%; height: 100%; border-radius: 50%; background: linear-gradient(135deg, #8b5cf6, #ec4899); display: flex; align-items: center; justify-content: center; font-size: 1.5rem; font-weight: 800; color: #fff; box-shadow: 0 8px 16px rgba(139, 92, 246, 0.25);">
                                    {{ $initials }}
                                </div>
                            @endif
                            @php $isAvailable = crc32($trainer->id ?? '1') % 2 == 0; @endphp
                            <span style="position: absolute; bottom: 2px; right: 2px; width: 14px; height: 14px; background: {{ $isAvailable ? '#10b981' : '#f59e0b' }}; border: 3px solid #0f172a; border-radius: 50%; box-shadow: 0 0 10px {{ $isAvailable ? '#10b981' : '#f59e0b' }};" title="{{ $isAvailable ? 'Online' : 'Busy' }}"></span>
                        </div>

                        <!-- MIDDLE: Name, Specialization tags, Rating, Experience, Sessions -->
                        <h3 style="font-size: 0.95rem; font-weight: 800; color: #fff; margin: 0 0 4px 0; display: inline-flex; align-items: center; justify-content: center; gap: 6px; width: 100%;">
                            {{ $trainer->name }}
                            @if($trainer->is_verified)
                                <span title="Verified Trainer" style="background: #10b981; color: #fff; width: 15px; height: 15px; border-radius: 50%; display: inline-flex; align-items: center; justify-content: center; font-size: 8px; font-weight: 900; box-shadow: 0 0 8px rgba(16,185,129,0.5);">✓</span>
                            @endif
                        </h3>
                        
                        <p style="font-size: 0.8rem; font-weight: 800; margin: 0 0 10px 0;">
                            <span style="background: linear-gradient(135deg, #c4b5fd, #f9a8d4); -webkit-background-clip: text; background-clip: text; color: transparent;">₹{{ number_format($trainer->hourly_rate ?? 500) }}/hr</span>
                        </p>

                        <div style="display: flex; gap: 6px; justify-content: center; flex-wrap: wrap; margin-bottom: 12px; min-height: 24px; width: 100%;">
                            @php
                                $specs = array_map('trim', explode(',', $trainer->specialization ?? 'Strength, Fat Loss, HIIT'));
                            @endphp
                            @foreach(array_slice($specs, 0, 3) as $spec)
                                <span style="font-size: 0.62rem; font-weight: 700; background: rgba(139, 92, 246, 0.1); color: #c4b5fd; border: 1px solid rgba(139, 92, 246, 0.15); padding: 3px 8px; border-radius: 50px;">{{ $spec }}</span>
                            @endforeach
                        </div>

                        <div style="display: flex; justify-content: space-around; align-items: center; background: rgba(255, 255, 255, 0.02); border: 1px solid rgba(255, 255, 255, 0.04); border-radius: 16px; padding: 8px 4px; margin-bottom: 15px; width: 100%;">
                            <div style="text-align: center; flex: 1;">
                                <span style="font-size: 0.8rem; font-weight: 800; color: #fbbf24; display: block;">⭐ {{ $trainer->rating ?? '4.8' }}</span>
                                <span style="font-size: 0.58rem; color: rgba(255, 255, 255, 0.4); text-transform: uppercase; font-weight: 700; letter-spacing: 0.03em;">Rating</span>
                            </div>
                            <div style="width: 1px; height: 20px; background: rgba(255, 255, 255, 0.08);"></div>
                            <div style="text-align: center; flex: 1;">
                                <span style="font-size: 0.8rem; font-weight: 800; color: #e2d9f3; display: block;">{{ $trainer->experience_years ?? 5 }} yrs</span>
                                <span style="font-size: 0.58rem; color: rgba(255, 255, 255, 0.4); text-transform: uppercase; font-weight: 700; letter-spacing: 0.03em;">Exp</span>
                            </div>
                            <div style="width: 1px; height: 20px; background: rgba(255, 255, 255, 0.08);"></div>
                            <div style="text-align: center; flex: 1;">
                                @php
                                    $completedSessionsCount = (crc32($trainer->id ?? '1') % 150) + 120;
                                @endphp
                                <span style="font-size: 0.8rem; font-weight: 800; color: #a78bfa; display: block;">{{ $completedSessionsCount }}+</span>
                                <span style="font-size: 0.58rem; color: rgba(255, 255, 255, 0.4); text-transform: uppercase; font-weight: 700; letter-spacing: 0.03em;">Sessions</span>
                            </div>
                        </div>

                        <!-- BOTTOM: Buttons -->
                        <div style="display: flex; gap: 8px; margin-top: auto; position: relative; z-index: 10; width: 100%;">
                            <a href="{{ route('chat.index', $trainer->id) }}" style="flex: 1; display: inline-flex; align-items: center; justify-content: center; height: 36px; background: transparent; border: 1px solid rgba(139, 92, 246, 0.4); color: #c4b5fd; border-radius: 12px; font-size: 0.72rem; font-weight: 700; text-decoration: none; transition: all 0.2s;" onmouseover="this.style.background='rgba(139, 92, 246, 0.1)'; this.style.borderColor='rgba(139, 92, 246, 0.6)';" onmouseout="this.style.background='transparent'; this.style.borderColor='rgba(139, 92, 246, 0.4)';">
                                💬 Chat
                            </a>
                            <a href="{{ route('book.trainer.create', $trainer->id) }}" style="flex: 1.2; display: inline-flex; align-items: center; justify-content: center; height: 36px; background: linear-gradient(135deg, #8b5cf6, #ec4899); color: #fff; border-radius: 12px; font-size: 0.72rem; font-weight: 700; text-decoration: none; box-shadow: 0 4px 12px rgba(139, 92, 246, 0.3); transition: all 0.2s;" onmouseover="this.style.transform='translateY(-1px)'; this.style.boxShadow='0 6px 16px rgba(139, 92, 246, 0.5)';" onmouseout="this.style.transform='none'; this.style.boxShadow='0 4px 12px rgba(139, 92, 246, 0.3)';">
                                Book Now
                            </a>
                        </div>
                    </div>
                @endforeach
            @else
                <div style="grid-column:1/-1;text-align:center;padding:2rem;">
                    <div style="font-size:1.8rem;margin-bottom:.4rem;opacity:.4;">🏋️</div>
                    <p style="color:rgba(255,255,255,.3);font-size:.82rem;">No trainers available at the moment</p>
                </div>
            @endif
        </div>
    </div>

    {{-- Activity Calendar --}}
    <x-activity-calendar :calendar="$activityCalendar ?? collect()" :total="$activityTotal ?? 0" :streak="$streak ?? 0" />

    {{-- Stats Cards Row --}}
    <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(190px,1fr));gap:1.2rem;margin-bottom:1.5rem;margin-top:1.5rem;">
        @if(($stats['total_workouts'] ?? 0) > 0)
        <div class="dashboard-card hover-lift">
            <p style="color:rgba(255,255,255,.35);font-size:.7rem;font-weight:600;letter-spacing:.04em;margin-bottom:.3rem;">TOTAL WORKOUTS</p>
            <p style="font-size:2.2rem;font-weight:900;background:linear-gradient(135deg,#c4b5fd,#f9a8d4);-webkit-background-clip:text;background-clip:text;color:transparent;line-height:1;margin:0;">{{ $stats['total_workouts'] }}</p>
        </div>
        @endif
        
        @if(($stats['completed_workouts'] ?? 0) > 0)
        <div class="dashboard-card hover-lift">
            <p style="color:rgba(255,255,255,.35);font-size:.7rem;font-weight:600;letter-spacing:.04em;margin-bottom:.3rem;">COMPLETED</p>
            <p style="font-size:2.2rem;font-weight:900;background:linear-gradient(135deg,#6ee7b7,#34d399);-webkit-background-clip:text;background-clip:text;color:transparent;line-height:1;margin:0;">{{ $stats['completed_workouts'] }}</p>
        </div>
        @endif
    </div>

    {{-- Goal, BMI, AI Coach Grid (Desktop 3cols, Tablet 2cols, Mobile stacked) --}}
    <div class="dashboard-grid-3" style="margin-bottom:1.5rem;">
        
        {{-- Goal Progress Bars --}}
        <div class="dashboard-card hover-lift">
            <h2 style="font-size:0.95rem;font-weight:700;color:#e2d9f3;margin-bottom:1rem;">🎯 Goal Progress</h2>
            <div style="display:flex;flex-direction:column;gap:0.75rem;">
                <div>
                    <div style="display:flex;justify-content:space-between;font-size:.75rem;color:rgba(255,255,255,.6);margin-bottom:.25rem;"><span>Workouts This Week</span> <span style="font-weight:700;color:#e2d9f3;">{{ $completedWorkoutsThisWeek }}/{{ $workoutGoal }}</span></div>
                    <div style="width:100%;height:5px;background:rgba(255,255,255,.1);border-radius:3px;overflow:hidden;"><div style="width:{{ $workoutPercentage }}%;height:100%;background:#8b5cf6;"></div></div>
                </div>
                <div>
                    <div style="display:flex;justify-content:space-between;font-size:.75rem;color:rgba(255,255,255,.6);margin-bottom:.25rem;"><span>Weekly Calories Burned</span> <span style="font-weight:700;color:#e2d9f3;">{{ $caloriesPercentage }}%</span></div>
                    <div style="width:100%;height:5px;background:rgba(255,255,255,.1);border-radius:3px;overflow:hidden;"><div style="width:{{ $caloriesPercentage }}%;height:100%;background:#fb923c;"></div></div>
                </div>
                <div>
                    <div style="display:flex;justify-content:space-between;font-size:.75rem;color:rgba(255,255,255,.6);margin-bottom:.25rem;"><span>Today's Hydration</span> <span style="font-weight:700;color:#e2d9f3;">{{ $hydrationPercentage }}%</span></div>
                    <div style="width:100%;height:5px;background:rgba(255,255,255,.1);border-radius:3px;overflow:hidden;"><div style="width:{{ $hydrationPercentage }}%;height:100%;background:#60a5fa;"></div></div>
                </div>
                <div>
                    <div style="display:flex;justify-content:space-between;font-size:.75rem;color:rgba(255,255,255,.6);margin-bottom:.25rem;"><span>Sleep Quality</span> <span style="font-weight:700;color:#e2d9f3;">{{ $sleepPercentage }}%</span></div>
                    <div style="width:100%;height:5px;background:rgba(255,255,255,.1);border-radius:3px;overflow:hidden;"><div style="width:{{ $sleepPercentage }}%;height:100%;background:#818cf8;"></div></div>
                </div>
                <div>
                    <div style="display:flex;justify-content:space-between;font-size:.75rem;color:rgba(255,255,255,.6);margin-bottom:.25rem;"><span>Strength Index</span> <span style="font-weight:700;color:#e2d9f3;">{{ $strengthPercentage }}%</span></div>
                    <div style="width:100%;height:5px;background:rgba(255,255,255,.1);border-radius:3px;overflow:hidden;"><div style="width:{{ $strengthPercentage }}%;height:100%;background:#f43f5e;"></div></div>
                </div>
            </div>
        </div>

        {{-- BMI / Body Stats --}}
        <div class="dashboard-card hover-lift">
            <h2 style="font-size:0.95rem;font-weight:700;color:#e2d9f3;margin-bottom:1rem;">⚖️ Body Stats</h2>
            @if(isset($latestProgress) && ($latestProgress->weight || $latestProgress->body_fat_percentage || $latestProgress->muscle_mass))
                <div style="display:flex;justify-content:space-between;align-items:center;margin-top:0.5rem;margin-bottom:1rem;">
                    <div style="text-align:center;flex:1;">
                        <div style="font-size:1.6rem;font-weight:900;color:#c4b5fd;line-height:1;margin-bottom:.2rem;">{{ $latestProgress->weight ?? '--' }}</div>
                        <div style="font-size:.7rem;color:rgba(255,255,255,.4);text-transform:uppercase;font-weight:600;">Weight (kg)</div>
                    </div>
                    <div style="width:1px;height:35px;background:rgba(255,255,255,.1);"></div>
                    <div style="text-align:center;flex:1;">
                        <div style="font-size:1.6rem;font-weight:900;color:#6ee7b7;line-height:1;margin-bottom:.2rem;">{{ $latestProgress->body_fat_percentage ?? '--' }}<span style="font-size:0.8rem">%</span></div>
                        <div style="font-size:.7rem;color:rgba(255,255,255,.4);text-transform:uppercase;font-weight:600;">Body Fat</div>
                    </div>
                    <div style="width:1px;height:35px;background:rgba(255,255,255,.1);"></div>
                    <div style="text-align:center;flex:1;">
                        <div style="font-size:1.6rem;font-weight:900;color:#f9a8d4;line-height:1;margin-bottom:.2rem;">{{ $latestProgress->muscle_mass ?? '--' }}<span style="font-size:0.8rem">%</span></div>
                        <div style="font-size:.7rem;color:rgba(255,255,255,.4);text-transform:uppercase;font-weight:600;">Muscle</div>
                    </div>
                </div>
                <div style="text-align:center;margin-top:0.8rem;">
                    <a href="{{ route('progress.index') }}" style="font-size:.75rem;color:#a78bfa;font-weight:600;text-decoration:none;" class="btn-hover-scale">Update Stats →</a>
                </div>
            @else
                <div style="text-align:center;padding:1.2rem 0;">
                    <p style="color:rgba(255,255,255,.4);font-size:.8rem;margin-bottom:0.8rem;">No body stats recorded yet.</p>
                    <a href="{{ route('progress.index') }}" style="display:inline-block;background:rgba(139,92,246,.2);color:#c4b5fd;padding:6px 12px;border-radius:8px;font-size:.75rem;font-weight:600;text-decoration:none;transition:background .2s;" class="btn-hover-scale">+ Add Stats</a>
                </div>
            @endif
        </div>

        {{-- AI Coach Tip --}}
        <div class="dashboard-card hover-lift" style="background:linear-gradient(135deg,rgba(139,92,246,.12),rgba(139,92,246,.02));border:1px solid rgba(139,92,246,.3);display:flex;flex-direction:column;">
            <div style="display:flex;align-items:center;gap:8px;margin-bottom:0.75rem;">
                <span style="font-size:1.1rem;">🤖</span>
                <h2 style="font-size:0.95rem;font-weight:700;color:#e2d9f3;">AI Coach Tip</h2>
            </div>
            <p style="font-size:.8rem;color:rgba(255,255,255,.75);line-height:1.5;flex:1;margin:0;overflow-y:auto;">
                {{ $aiTip ?? "Stay hydrated and make sure to stretch after your workouts to improve recovery." }}
            </p>
        </div>

    </div>

    {{-- Recent Workouts & Upcoming Sessions Grid --}}
    <div class="dashboard-grid-2" style="margin-bottom:1.5rem;">

        {{-- Recent Workouts --}}
        <div class="dashboard-card hover-lift">
            <h2 style="font-size:0.95rem;font-weight:700;color:#e2d9f3;margin-bottom:1rem;">🏋️ Recent Workouts</h2>
            @if(isset($recentWorkouts) && $recentWorkouts->count() > 0)
                <div style="display:flex;flex-direction:column;gap:.75rem;">
                    @foreach($recentWorkouts as $workout)
                        <div style="border-bottom:1px solid rgba(139,92,246,.1);padding-bottom:.65rem;display:flex;justify-content:space-between;align-items:center;">
                            <div>
                                <p style="font-size:.82rem;font-weight:600;color:#e2d9f3;margin:0 0 2px 0;">{{ $workout->title }}</p>
                                <p style="font-size:.72rem;color:rgba(255,255,255,.3);margin:0;">{{ $workout->type }} • {{ $workout->difficulty }}</p>
                            </div>
                            <div style="text-align:right;">
                                <p style="font-size:.78rem;color:#c4b5fd;font-weight:600;margin:0;">{{ $workout->duration_minutes ?? 45 }} min</p>
                                @php
                                    $factor = 7.5;
                                    $wType = strtolower($workout->type ?? '');
                                    if (str_contains($wType, 'strength')) $factor = 6.0;
                                    elseif (str_contains($wType, 'cardio') || str_contains($wType, 'hiit')) $factor = 10.0;
                                    elseif (str_contains($wType, 'yoga')) $factor = 4.0;
                                    $estCalories = ($workout->duration_minutes ?? 45) * $factor;
                                @endphp
                                <p style="font-size:.68rem;color:rgba(255,255,255,.4);margin:0;">{{ $estCalories }} kcal</p>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div style="text-align:center;padding:1.5rem 1rem;">
                    <div style="font-size:1.8rem;margin-bottom:.4rem;opacity:.4;">🏋️</div>
                    <p style="color:rgba(255,255,255,.3);font-size:.8rem;margin-bottom:0.8rem;">No workouts completed yet.</p>
                    <a href="{{ route('workouts.index') }}" style="display:inline-block;background:rgba(139,92,246,.2);color:#c4b5fd;padding:6px 12px;border-radius:8px;font-size:.75rem;font-weight:600;text-decoration:none;transition:background .2s;" class="btn-hover-scale">Log First Workout</a>
                </div>
            @endif
        </div>

        {{-- Upcoming Sessions --}}
        <div class="dashboard-card hover-lift">
            <h2 style="font-size:0.95rem;font-weight:700;color:#e2d9f3;margin-bottom:1rem;">📅 Upcoming Sessions</h2>
            @if(isset($upcomingSessions) && $upcomingSessions->count() > 0)
                <div style="display:flex;flex-direction:column;gap:.75rem;">
                    @foreach($upcomingSessions as $session)
                        @php
                            $sessionDate = \Carbon\Carbon::parse($session->session_date);
                            $joinAt = $sessionDate->copy()->subMinutes(15);
                            $canJoin = now()->greaterThanOrEqualTo($joinAt);
                        @endphp
                        <div style="border-bottom:1px solid rgba(139,92,246,.1);padding-bottom:.65rem;position:relative;">
                            <p style="font-size:.82rem;font-weight:600;color:#e2d9f3;margin:0 0 2px 0;">{{ $session->trainer->specialization ?? 'Training' }} with {{ $session->trainer->name ?? 'Trainer' }}</p>
                            <p style="font-size:.72rem;color:rgba(255,255,255,.3);margin:0;">{{ $sessionDate->format('M d, Y h:i A') }}</p>
                            <span style="font-size:.65rem;background:rgba(139,92,246,.15);color:#a78bfa;border:1px solid rgba(139,92,246,.25);padding:1px 6px;border-radius:50px;font-weight:600;display:inline-block;margin-top:4px;">{{ ucfirst($session->status) }}</span>
                            @if($canJoin)
                                @if($session->meeting_started)
                                    <a href="{{ route('video-call.join', $session->id) }}" style="font-size:.72rem;color:#6ee7b7;font-weight:700;position:absolute;bottom:0.65rem;right:0;text-decoration:none;" class="btn-hover-scale">Join Session</a>
                                @else
                                    <span data-booking-id="{{ $session->id }}" style="font-size:.68rem;color:#fbbf24;font-weight:700;position:absolute;bottom:0.65rem;right:0;display:inline-flex;align-items:center;gap:4px;">
                                        <span style="display:inline-block;width:6px;height:6px;border-radius:50%;background:#fbbf24;animation:pulse 1.5s infinite;"></span>
                                        Waiting for Trainer...
                                    </span>
                                @endif
                            @else
                                <span style="font-size:.68rem;color:#fca5a5;font-weight:600;position:absolute;bottom:0.65rem;right:0;">Opens {{ $joinAt->format('h:i A') }}</span>
                            @endif
                        </div>
                    @endforeach
                </div>
                <div style="margin-top:0.8rem;">
                    <a href="{{ route('bookings.index') }}" style="font-size:.75rem;color:#a78bfa;font-weight:600;text-decoration:none;" class="btn-hover-scale">View All Bookings →</a>
                </div>
            @else
                <div style="text-align:center;padding:1.5rem 1rem;">
                    <div style="font-size:1.8rem;margin-bottom:.4rem;opacity:.4;">📅</div>
                    <p style="color:rgba(255,255,255,.3);font-size:.8rem;margin-bottom:0.4rem;">No upcoming sessions.</p>
                    <a href="{{ route('trainee.trainers') }}" style="font-size:.75rem;color:#a78bfa;font-weight:600;text-decoration:none;" class="btn-hover-scale">+ Book a Trainer</a>
                </div>
            @endif
        </div>
    </div>

    {{-- Weekly Calorie Burn Chart --}}
    <div class="dashboard-card hover-lift" style="margin-bottom:1.5rem;">
        <h2 style="font-size:0.95rem;font-weight:700;color:#e2d9f3;margin-bottom:1rem;">🔥 Weekly Calorie Burn</h2>
        <div style="display:flex;align-items:flex-end;gap:12px;height:130px;padding-top:10px;">
            @php
                $maxCal = max($weeklyCalories ?? [1]);
            @endphp
            @foreach($weeklyCalories as $idx => $cal)
                @php
                    $height = ($cal / $maxCal) * 100;
                    $isCurrent = $idx === count($weeklyCalories) - 1;
                    $dayName = $dayLabels[$idx] ?? ('W' . ($idx + 1));
                @endphp
                <div class="chart-bar-container">
                    <div class="chart-bar-wrapper">
                        <div class="chart-bar {{ $isCurrent ? 'current-week' : '' }}" 
                             style="--target-height: {{ $height }}%;" 
                             data-tooltip="{{ $dayName }}: {{ number_format($cal) }} kcal">
                        </div>
                    </div>
                    <span style="font-size:.65rem;color:rgba(255,255,255,.45);font-weight:{{ $isCurrent ? '700' : 'normal' }};">{{ $dayName }}</span>
                </div>
            @endforeach
        </div>
    </div>

</div>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        // Shimmer simulation of loading skeleton
        setTimeout(function() {
            const skeleton = document.getElementById('dashboard-skeleton');
            const realContent = document.getElementById('dashboard-real-content');
            if (skeleton && realContent) {
                skeleton.style.transition = 'opacity 0.25s ease';
                skeleton.style.opacity = '0';
                setTimeout(() => {
                    skeleton.style.display = 'none';
                    realContent.style.display = 'block';
                    setTimeout(() => {
                        realContent.style.opacity = '1';
                    }, 50);
                }, 250);
            }
        }, 700); // simulated quick load
    });
</script>
@endsection
