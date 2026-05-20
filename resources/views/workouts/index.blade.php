@extends('layouts.app')

@section('title', 'My Workouts')

@section('content')
@php
    $templates = [
        [
            'name' => 'Push Pull Legs (PPL)', 
            'desc' => 'Classic 3-day split for mass', 
            'diff' => 'Intermediate', 
            'dur' => '65m', 
            'ex' => 4, 
            'icon' => 'layers',
            'exercises' => [
                ['name' => 'Barbell Bench Press', 'sets' => 4, 'reps' => 10],
                ['name' => 'Overhead Press', 'sets' => 3, 'reps' => 12],
                ['name' => 'Lateral Raises', 'sets' => 3, 'reps' => 15],
                ['name' => 'Tricep Pushdowns', 'sets' => 3, 'reps' => 12],
            ]
        ],
        [
            'name' => 'Full Body Ignition', 
            'desc' => 'High intensity total body blast', 
            'diff' => 'Beginner', 
            'dur' => '45m', 
            'ex' => 4, 
            'icon' => 'zap',
            'exercises' => [
                ['name' => 'Squats', 'sets' => 3, 'reps' => 12],
                ['name' => 'Push Ups', 'sets' => 3, 'reps' => 15],
                ['name' => 'Pull Ups', 'sets' => 3, 'reps' => 10],
                ['name' => 'Plank', 'sets' => 3, 'reps' => 60],
            ]
        ],
        [
            'name' => 'HIIT Cardio Burn', 
            'desc' => 'Max calorie burn in 20 mins', 
            'diff' => 'Advanced', 
            'dur' => '20m', 
            'ex' => 3, 
            'icon' => 'flame',
            'exercises' => [
                ['name' => 'Jump Rope', 'sets' => 4, 'reps' => 20],
                ['name' => 'Running', 'sets' => 4, 'reps' => 30],
                ['name' => 'Cycling', 'sets' => 4, 'reps' => 15],
            ]
        ],
        [
            'name' => 'Lower Body Focus', 
            'desc' => 'Strong legs and glutes', 
            'diff' => 'Intermediate', 
            'dur' => '55m', 
            'ex' => 3, 
            'icon' => 'chevron-down',
            'exercises' => [
                ['name' => 'Deadlift', 'sets' => 4, 'reps' => 8],
                ['name' => 'Leg Press', 'sets' => 3, 'reps' => 12],
                ['name' => 'Lunges', 'sets' => 3, 'reps' => 12],
            ]
        ]
    ];
@endphp
<style>
    :root {
        --glass-bg: rgba(255, 255, 255, 0.03);
        --glass-border: rgba(255, 255, 255, 0.08);
        --glass-border-strong: rgba(255, 255, 255, 0.15);
        --accent-gradient: linear-gradient(135deg, #8b5cf6 0%, #ec4899 100%);
        --accent-glow: rgba(139, 92, 246, 0.4);
    }

    select {
        appearance: none !important;
        -webkit-appearance: none !important;
        -moz-appearance: none !important;
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='16' height='16' viewBox='0 0 24 24' fill='none' stroke='%23ffffff' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpath d='m6 9 6 6 6-6'/%3E%3C/svg%3E") !important;
        background-repeat: no-repeat !important;
        background-position: right 12px center !important;
        background-size: 16px !important;
        padding-right: 40px !important;
        color: #ffffff !important;
        background-color: #0a0a1a !important;
    }
    select option {
        background-color: #0a0a1a !important;
        color: #ffffff !important;
        padding: 10px !important;
    }
    select * {
        color: #ffffff !important;
    }

    /* Custom Dropdown Styling */
    .custom-select-container.active .custom-select-trigger {
        border-color: #8b5cf6 !important;
        box-shadow: 0 0 10px rgba(139, 92, 246, 0.3) !important;
    }
    
    .custom-select-container.active .chevron-icon {
        transform: rotate(180deg) !important;
        opacity: 1 !important;
        color: #8b5cf6 !important;
    }
    
    .custom-select-options.show {
        opacity: 1 !important;
        pointer-events: auto !important;
        transform: translateY(0) scale(1) !important;
    }

    .workout-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 2.5rem;
        flex-wrap: wrap;
        gap: 1.5rem;
    }

    .btn-premium {
        background: var(--accent-gradient);
        color: #fff;
        padding: 10px 20px;
        border-radius: 12px;
        font-weight: 700;
        font-size: 0.9rem;
        border: none;
        cursor: pointer;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        display: inline-flex;
        align-items: center;
        gap: 8px;
        box-shadow: 0 8px 20px var(--accent-glow);
        text-decoration: none;
    }

    .btn-premium:hover {
        transform: translateY(-2px);
        box-shadow: 0 12px 25px var(--accent-glow);
        filter: brightness(1.1);
    }

    .btn-outline {
        background: var(--glass-bg);
        color: #e2e8f0;
        padding: 10px 20px;
        border-radius: 12px;
        font-weight: 600;
        font-size: 0.9rem;
        border: 1px solid var(--glass-border-strong);
        cursor: pointer;
        transition: all 0.2s;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        text-decoration: none;
    }

    .btn-outline:hover {
        background: rgba(255, 255, 255, 0.08);
        border-color: rgba(255, 255, 255, 0.2);
    }

    /* Filter Tabs */
    .filter-tabs {
        display: flex;
        gap: 8px;
        background: var(--glass-bg);
        padding: 6px;
        border-radius: 14px;
        border: 1px solid var(--glass-border);
        margin-bottom: 2.5rem;
        width: fit-content;
    }

    .filter-tab {
        padding: 8px 18px;
        border-radius: 10px;
        font-size: 0.85rem;
        font-weight: 600;
        color: rgba(255, 255, 255, 0.5);
        cursor: pointer;
        transition: all 0.2s;
        border: none;
        background: transparent;
    }

    .filter-tab.active {
        background: rgba(139, 92, 246, 0.15);
        color: #fff;
        box-shadow: inset 0 0 10px rgba(139, 92, 246, 0.1);
    }

    /* Active Plan Card */
    .active-plan-card {
        background: linear-gradient(135deg, rgba(139, 92, 246, 0.1) 0%, rgba(236, 72, 153, 0.05) 100%);
        border: 1px solid rgba(139, 92, 246, 0.2);
        border-radius: 24px;
        padding: 1.25rem 2rem; /* Reduced padding by ~25% */
        margin-bottom: 2rem; /* Reduced bottom margin */
        display: flex;
        justify-content: space-between;
        align-items: center;
        backdrop-filter: blur(10px);
        position: relative;
        overflow: hidden;
    }

    .active-plan-card::before {
        content: '';
        position: absolute;
        top: -50%;
        right: -10%;
        width: 300px;
        height: 300px;
        background: radial-gradient(circle, var(--accent-glow) 0%, transparent 70%);
        opacity: 0.2;
        pointer-events: none;
    }

    .progress-bar-container {
        width: 100%;
        height: 6px;
        background: rgba(255, 255, 255, 0.05);
        border-radius: 10px;
        margin: 0.6rem 0 0.4rem 0; /* Reduced margins */
        overflow: hidden;
    }

    .progress-bar-fill {
        height: 100%;
        background: var(--accent-gradient);
        border-radius: 10px;
        animation: fillProgress 1.4s cubic-bezier(0.4, 0, 0.2, 1) forwards;
    }
    @keyframes fillProgress {
        from { width: 0%; }
    }

    /* Active Plan Glowing CTA */
    .btn-active-glowing {
        box-shadow: 0 0 15px rgba(139, 92, 246, 0.5);
        animation: activeBtnGlow 2s infinite alternate;
    }
    @keyframes activeBtnGlow {
        from { box-shadow: 0 0 12px rgba(139, 92, 246, 0.4); }
        to { box-shadow: 0 0 25px rgba(139, 92, 246, 0.85), 0 0 10px rgba(236, 72, 153, 0.5); }
    }

    /* Workout Grid */
    .workout-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
        gap: 1.5rem;
        margin-bottom: 4rem;
    }

    .workout-card {
        background: var(--glass-bg);
        border: 1px solid var(--glass-border);
        border-radius: 20px;
        padding: 1.25rem;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        display: flex;
        flex-direction: column;
        justify-content: space-between;
    }

    /* Custom Workout Personalization */
    .workout-card.custom-card {
        border-color: rgba(236, 72, 153, 0.18);
        background: linear-gradient(135deg, rgba(255, 255, 255, 0.03) 0%, rgba(236, 72, 153, 0.02) 100%);
    }
    .workout-card.custom-card:hover {
        border-color: rgba(236, 72, 153, 0.45);
        box-shadow: 0 15px 35px rgba(236, 72, 153, 0.12);
    }

    /* Status Badges */
    .status-badge {
        font-size: 0.65rem;
        font-weight: 800;
        text-transform: uppercase;
        padding: 4px 10px;
        border-radius: 50px;
        letter-spacing: 0.05em;
        display: inline-flex;
        align-items: center;
        gap: 4px;
    }
    .status-completed { background: rgba(16, 185, 129, 0.15); color: #10b981; border: 1px solid rgba(16, 185, 129, 0.25); }
    .status-in-progress { background: rgba(139, 92, 246, 0.15); color: #a78bfa; border: 1px solid rgba(139, 92, 246, 0.25); animation: pulseStatus 2s infinite alternate; }
    .status-scheduled { background: rgba(56, 189, 248, 0.15); color: #38bdf8; border: 1px solid rgba(56, 189, 248, 0.25); }
    .status-missed { background: rgba(244, 63, 94, 0.15); color: #f43f5e; border: 1px solid rgba(244, 63, 94, 0.25); }
    @keyframes pulseStatus {
        from { box-shadow: 0 0 5px rgba(139, 92, 246, 0.2); }
        to { box-shadow: 0 0 10px rgba(139, 92, 246, 0.45); }
    }

    /* Action Buttons Hover Feedback & Tooltips */
    .workout-action-btn {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 32px;
        height: 32px;
        border-radius: 50%;
        background: rgba(255, 255, 255, 0.04);
        border: 1px solid rgba(255, 255, 255, 0.1);
        color: rgba(255, 255, 255, 0.6);
        text-decoration: none;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        position: relative;
    }
    .workout-action-btn:hover {
        transform: scale(1.15) translateY(-2px);
        color: #fff;
    }
    .workout-action-btn.edit-btn:hover {
        background: rgba(139, 92, 246, 0.2);
        border-color: rgba(139, 92, 246, 0.5);
        box-shadow: 0 0 15px rgba(139, 92, 246, 0.5);
        color: #c4b5fd;
    }
    .workout-action-btn.delete-btn:hover {
        background: rgba(244, 63, 94, 0.2);
        border-color: rgba(244, 63, 94, 0.5);
        box-shadow: 0 0 15px rgba(244, 63, 94, 0.5);
        color: #fda4af;
    }
    .workout-action-btn[data-tooltip]::after {
        content: attr(data-tooltip);
        position: absolute;
        bottom: 125%;
        left: 50%;
        transform: translateX(-50%) translateY(4px);
        background: rgba(15, 23, 42, 0.95);
        border: 1px solid rgba(255, 255, 255, 0.1);
        color: #fff;
        padding: 4px 8px;
        border-radius: 6px;
        font-size: 0.65rem;
        font-weight: 600;
        white-space: nowrap;
        opacity: 0;
        pointer-events: none;
        transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.5);
        z-index: 10;
    }
    .workout-action-btn[data-tooltip]:hover::after {
        opacity: 1;
        transform: translateX(-50%) translateY(0);
    }

    .workout-card:hover {
        transform: translateY(-5px);
        border-color: rgba(139, 92, 246, 0.3);
        background: rgba(255, 255, 255, 0.05);
        box-shadow: 0 15px 35px rgba(0, 0, 0, 0.2);
    }

    .difficulty-badge {
        font-size: 0.65rem;
        font-weight: 800;
        text-transform: uppercase;
        padding: 4px 10px;
        border-radius: 50px;
        letter-spacing: 0.05em;
    }

    .diff-beginner { background: rgba(16, 185, 129, 0.15); color: #10b981; border: 1px solid rgba(16, 185, 129, 0.2); }
    .diff-intermediate { background: rgba(245, 158, 11, 0.15); color: #fbbf24; border: 1px solid rgba(245, 158, 11, 0.2); }
    .diff-advanced { background: rgba(239, 68, 68, 0.15); color: #ef4444; border: 1px solid rgba(239, 68, 68, 0.2); }

    /* Templates */
    .template-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
        gap: 1.2rem;
    }

    .template-card {
        background: rgba(255, 255, 255, 0.02);
        border: 1px dashed var(--glass-border-strong);
        border-radius: 18px;
        padding: 1.2rem;
        transition: all 0.2s;
    }

    .template-card:hover {
        background: rgba(255, 255, 255, 0.04);
        border-style: solid;
        border-color: var(--accent-glow);
    }

    /* Modal */
    .modal-overlay {
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        width: 100%;
        height: 100vh;
        background: rgba(0, 0, 0, 0.85);
        backdrop-filter: blur(12px);
        display: none;
        justify-content: center;
        align-items: center; /* Back to center for safety */
        z-index: 9999;
        padding: 20px;
    }

    .modal-content {
        background: #0f172a;
        border: 1px solid var(--glass-border-strong);
        border-radius: 28px;
        width: 100%;
        max-width: 800px;
        max-height: calc(100vh - 100px); /* Strictly limited to fit screen */
        display: flex;
        flex-direction: column;
        box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
        overflow: hidden;
        margin-top: 0; /* Centered by flexbox */
    }

    .modal-body {
        overflow-y: auto;
        padding: 2rem;
        flex: 1; /* Grow to fill available space */
    }

    .modal-header {
        padding: 1.5rem 2rem;
        border-bottom: 1px solid var(--glass-border);
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-shrink: 0; /* Don't shrink header */
    }

    .modal-footer {
        padding: 1.5rem 2rem;
        border-top: 1px solid var(--glass-border);
        display: flex;
        gap: 12px;
        flex-shrink: 0; /* Don't shrink footer */
        background: rgba(15, 23, 42, 0.8); /* Slight transparency */
        backdrop-filter: blur(8px);
    }

    .modal-content form {
        flex: 1;
        display: flex;
        flex-direction: column;
        min-height: 0;
    }

    .form-group { margin-bottom: 1.5rem; }
    .form-label { display: block; font-size: 0.85rem; font-weight: 600; color: rgba(255,255,255,0.6); margin-bottom: 8px; }
    .form-input {
        width: 100%;
        background: rgba(255,255,255,0.05);
        border: 1px solid var(--glass-border);
        border-radius: 12px;
        padding: 12px 16px;
        color: #fff;
        font-size: 0.95rem;
        transition: all 0.2s;
    }
    .form-input:focus { outline: none; border-color: #8b5cf6; background: rgba(255,255,255,0.08); }
    select.form-input {
        appearance: none !important;
        -webkit-appearance: none !important;
        -moz-appearance: none !important;
        background: rgba(255, 255, 255, 0.05) url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='16' height='16' viewBox='0 0 24 24' fill='none' stroke='%23a78bfa' stroke-width='2.5' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpath d='m6 9 6 6 6-6'/%3E%3C/svg%3E") no-repeat right 16px center !important;
        background-size: 16px !important;
        padding-right: 40px !important;
        cursor: pointer;
    }
    select.form-input:focus {
        background: rgba(255, 255, 255, 0.08) url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='16' height='16' viewBox='0 0 24 24' fill='none' stroke='%23a78bfa' stroke-width='2.5' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpath d='m6 9 6 6 6-6'/%3E%3C/svg%3E") no-repeat right 16px center !important;
        background-size: 16px !important;
    }

    .exercise-item {
        background: rgba(255,255,255,0.03);
        border: 1px solid var(--glass-border);
        border-radius: 14px;
        padding: 1rem;
        margin-bottom: 1rem;
        display: flex;
        align-items: center;
        gap: 1rem;
    }

    @keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
    .fade-in { animation: fadeIn 0.4s ease forwards; }

    /* Custom Dropdown Options Styling */
    .custom-select-container.active .custom-select-trigger {
        border-color: rgba(139, 92, 246, 0.6) !important;
        box-shadow: 0 0 12px rgba(139, 92, 246, 0.3) !important;
    }
    .custom-select-container.active .chevron-icon {
        transform: rotate(180deg) !important;
        opacity: 1 !important;
        color: #a78bfa !important;
    }
    .custom-select-options {
        position: absolute;
        top: calc(100% + 6px);
        left: 0;
        right: 0;
        background: rgba(10, 10, 26, 0.98);
        backdrop-filter: blur(16px);
        -webkit-backdrop-filter: blur(16px);
        border: 1px solid rgba(139, 92, 246, 0.2);
        border-radius: 12px;
        z-index: 1000;
        opacity: 0;
        pointer-events: none;
        transform: translateY(10px) scale(0.95);
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.6);
        max-height: 220px;
        overflow-y: auto;
    }
    .custom-select-options.show {
        opacity: 1 !important;
        pointer-events: auto !important;
        transform: translateY(0) scale(1) !important;
    }
    .custom-select-option {
        padding: 12px 16px;
        color: rgba(255, 255, 255, 0.7);
        cursor: pointer;
        transition: all 0.2s ease;
        font-size: 0.9rem;
        text-align: left;
    }
    .custom-select-option:hover {
        background: rgba(255, 255, 255, 0.05);
        color: #fff;
    }
    .custom-select-option.selected {
        background: rgba(139, 92, 246, 0.15);
        color: #c4b5fd;
        font-weight: 700;
    }

    .client-assign-btn {
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1) !important;
    }
    .client-assign-btn:hover {
        background: rgba(139, 92, 246, 0.15) !important;
        border-color: rgba(139, 92, 246, 0.4) !important;
        transform: translateY(-2px);
        box-shadow: 0 8px 24px rgba(139, 92, 246, 0.2) !important;
    }
</style>

<div class="mx-auto px-4 py-6" style="max-width: 1450px;">
    
    {{-- Top Section --}}
    <div class="workout-header fade-in">
        <div>
            <h1 style="font-size: 2.2rem; font-weight: 900; background: var(--accent-gradient); -webkit-background-clip: text; background-clip: text; color: transparent;">
                My Workouts 💪
            </h1>
            <p style="color: rgba(255, 255, 255, 0.4); font-size: 0.95rem; margin-top: 4px;">
                @if(Auth::user()->role === 'trainer')
                    Track, create and excel in your fitness journey
                @else
                    Track and excel in your fitness journey
                @endif
            </p>
            
            @if(Auth::user()->role === 'trainee')
            <div style="display: flex; gap: 16px; align-items: center; margin-top: 12px; background: rgba(255,255,255,0.02); border: 1px solid rgba(255,255,255,0.05); padding: 8px 16px; border-radius: 12px; width: fit-content; flex-wrap: wrap;">
                <div style="display: flex; align-items: center; gap: 6px;">
                    <span style="font-size: 0.9rem; font-weight: 800; color: #fff;">{{ $totalWorkouts }}</span>
                    <span style="font-size: 0.75rem; color: rgba(255,255,255,0.4);">Workouts</span>
                </div>
                <div style="color: rgba(255,255,255,0.1);">|</div>
                <div style="display: flex; align-items: center; gap: 6px;">
                    <span style="font-size: 0.9rem; font-weight: 800; color: #10b981;">{{ $completedCount }}</span>
                    <span style="font-size: 0.75rem; color: rgba(255,255,255,0.4);">Completed</span>
                </div>
                <div style="color: rgba(255,255,255,0.1);">|</div>
                <div style="display: flex; align-items: center; gap: 6px;">
                    <span style="font-size: 0.9rem; font-weight: 800; color: #38bdf8;">{{ $avgDuration }}m</span>
                    <span style="font-size: 0.75rem; color: rgba(255,255,255,0.4);">Avg Duration</span>
                </div>
                <div style="color: rgba(255,255,255,0.1);">|</div>
                <div style="display: flex; align-items: center; gap: 6px;">
                    <span style="font-size: 0.9rem; font-weight: 800; color: #fb923c;">{{ $activePlansCount }}</span>
                    <span style="font-size: 0.75rem; color: rgba(255,255,255,0.4);">Active Plans</span>
                </div>
            </div>
            @endif
        </div>
        
        <div style="display: flex; gap: 12px; align-items: flex-start;">
            @if(Auth::user()->role === 'trainer')
                <button onclick="openModal()" class="btn-premium">
                    <i data-lucide="plus-circle" style="width: 18px; height: 18px;"></i> Create Workout
                </button>
                <a href="#templates-section" class="btn-outline">
                    <i data-lucide="layout-grid" style="width: 18px; height: 18px;"></i> Browse Templates
                </a>
            @endif
        </div>
    </div>

    {{-- Filter Tabs (Trainee Only) --}}
    @if(Auth::user()->role === 'trainee')
    <div class="filter-tabs fade-in" style="animation-delay: 0.1s;">
        <button class="filter-tab active" onclick="filterWorkouts('all')">All</button>
        <button class="filter-tab" onclick="filterWorkouts('strength')">Strength</button>
        <button class="filter-tab" onclick="filterWorkouts('cardio')">Cardio</button>
        <button class="filter-tab" onclick="filterWorkouts('hiit')">HIIT</button>
        <button class="filter-tab" onclick="filterWorkouts('flexibility')">Flexibility</button>
    </div>
    @endif

    {{-- 1. Active Workout Plan Card (Trainee Only) --}}
    @if(Auth::user()->role === 'trainee' && ($nextWorkout || true)) {{-- Keeping it visible for now with dummy data as requested --}}
    <div class="active-plan-card fade-in" style="animation-delay: 0.2s;">
        <div style="flex: 1; max-width: 60%;">
            <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 6px;">
                <span style="background: rgba(139, 92, 246, 0.2); color: #a78bfa; font-size: 0.65rem; font-weight: 800; padding: 4px 10px; border-radius: 50px; text-transform: uppercase;">Active Plan</span>
                <span style="color: rgba(255, 255, 255, 0.4); font-size: 0.8rem;">• Last active 2 days ago</span>
            </div>
            <h2 style="font-size: 1.6rem; font-weight: 800; color: #fff; margin-bottom: 2px;">{{ $nextWorkout->title ?? 'Hypertrophy: 5 Day Split 🔥' }}</h2>
            <div style="display: flex; gap: 15px; align-items: center; margin-bottom: 0.4rem;">
                <p style="color: rgba(255, 255, 255, 0.5); font-size: 0.85rem; margin: 0;">Week 2 of 4 • Next session: Upper Body Power</p>
                <div style="display: flex; align-items: center; gap: 4px; color: #10b981; font-size: 0.75rem; font-weight: 700;">
                    <i data-lucide="clock" style="width: 12px; height: 12px;"></i>
                    <span>Est. {{ $nextWorkout->duration_minutes ?? '45' }} min</span>
                </div>
            </div>
            
            <div class="progress-bar-container">
                <div class="progress-bar-fill" style="width: 35%;"></div>
            </div>
            <div style="display: flex; justify-content: space-between; font-size: 0.7rem; color: rgba(255, 255, 255, 0.4);">
                <span>Progress: 35%</span>
                <span>7 of 20 sessions</span>
            </div>
        </div>
        
        <div style="text-align: right;">
            @if($nextWorkout)
                <a href="{{ route('workouts.show', $nextWorkout->id) }}" class="btn-premium btn-active-glowing" style="padding: 12px 24px; font-size: 0.95rem;">
                    @if($nextWorkout->completed_at)
                        View Summary <i data-lucide="eye" style="width: 16px; height: 16px; margin-left: 4px;"></i>
                    @else
                        Start Workout <i data-lucide="play" style="width: 16px; height: 16px; margin-left: 4px;"></i>
                    @endif
                </a>
            @else
                <button onclick="openModal()" class="btn-premium btn-active-glowing" style="padding: 12px 24px; font-size: 0.95rem;">
                    Start Session <i data-lucide="arrow-right" style="width: 16px; height: 16px; margin-left: 4px;"></i>
                </button>
            @endif
        </div>
    </div>
    @endif

    {{-- 2. Assigned By Trainer Section (Trainee Only) --}}
    @if(Auth::user()->role === 'trainee')
    <div style="margin-bottom: 1.5rem; display: flex; justify-content: space-between; align-items: center;" class="fade-in">
        <h3 style="font-size: 1.4rem; font-weight: 800; color: #fff;">Assigned By Trainer</h3>
        <span style="color: rgba(255, 255, 255, 0.3); font-size: 0.85rem;">{{ count($assignedWorkouts) }} workouts</span>
    </div>

    @if(count($assignedWorkouts) > 0)
        <div class="workout-grid fade-in" style="animation-delay: 0.3s;">
            @foreach($assignedWorkouts as $workout)
                @include('workouts._card', ['workout' => $workout, 'showActions' => false])
            @endforeach
            @if(count($assignedWorkouts) === 1)
                <div style="background: rgba(255, 255, 255, 0.01); border: 1px dashed rgba(255, 255, 255, 0.05); border-radius: 20px; padding: 1.5rem; display: flex; flex-direction: column; justify-content: center; align-items: center; color: rgba(255, 255, 255, 0.15); min-height: 250px;">
                    <i data-lucide="plus-circle" style="width: 24px; height: 24px; margin-bottom: 8px; color: rgba(255, 255, 255, 0.15);"></i>
                    <span style="font-size: 0.8rem; font-weight: 600;">Future Assigned Workout Slot</span>
                </div>
            @endif
        </div>
    @else
        <div style="background: var(--glass-bg); border: 1px dashed var(--glass-border-strong); border-radius: 24px; text-align: center; padding: 4rem 2rem; margin-bottom: 4rem;" class="fade-in">
            <div style="font-size: 3.5rem; opacity: 0.2; margin-bottom: 1rem;">📋</div>
            <h3 style="font-size: 1.4rem; font-weight: 800; color: #fff; margin-bottom: 0.5rem;">No assigned workouts</h3>
            <p style="color: rgba(255, 255, 255, 0.4); font-size: 0.95rem; margin-bottom: 2rem; max-width: 400px; margin-left: auto; margin-right: auto;">
                Ask your trainer to assign a workout to get started on your guided journey!
            </p>
            <a href="{{ route('chat.index') }}" class="btn-outline" style="border-color: var(--vg-accent); color: var(--vg-accent);">
                <i data-lucide="message-square" style="width: 18px; height: 18px;"></i> Message Trainer
            </a>
        </div>
    @endif

    {{-- 3. My Custom Workouts Section (Trainee Only) --}}
    <div style="margin-bottom: 1.5rem; display: flex; justify-content: space-between; align-items: center; padding-top: 1.5rem;" class="fade-in">
        <h3 style="font-size: 1.4rem; font-weight: 800; color: #fff;">My Custom Workouts</h3>
        <div style="display: flex; gap: 12px; align-items: center;">
            <span style="color: rgba(255, 255, 255, 0.3); font-size: 0.85rem; margin-right: 12px;">{{ count($customWorkouts) }} workouts</span>
            <button onclick="openModal()" class="btn-premium" style="padding: 8px 16px; font-size: 0.8rem;">
                <i data-lucide="plus" style="width: 14px; height: 14px;"></i> Create Own
            </button>
        </div>
    </div>

    @if(count($customWorkouts) > 0)
        <div class="workout-grid fade-in" style="animation-delay: 0.3s;">
            @foreach($customWorkouts as $workout)
                @include('workouts._card', ['workout' => $workout, 'showActions' => true])
            @endforeach
            @if(count($customWorkouts) === 1)
                <div onclick="openModal()" style="background: rgba(255, 255, 255, 0.01); border: 1px dashed rgba(236, 72, 153, 0.15); border-radius: 20px; padding: 1.5rem; display: flex; flex-direction: column; justify-content: center; align-items: center; color: rgba(236, 72, 153, 0.4); min-height: 250px; cursor: pointer; transition: all 0.3s;" onmouseover="this.style.background='rgba(236, 72, 153, 0.02)'; this.style.borderColor='rgba(236, 72, 153, 0.35)';" onmouseout="this.style.background='rgba(255, 255, 255, 0.01)'; this.style.borderColor='rgba(236, 72, 153, 0.15)';">
                    <i data-lucide="plus" style="width: 24px; height: 24px; margin-bottom: 8px; color: rgba(236, 72, 153, 0.4);"></i>
                    <span style="font-size: 0.85rem; font-weight: 700;">Create Custom Routine</span>
                </div>
            @endif
        </div>
    @else
        <div style="background: var(--glass-bg); border: 1px dashed var(--glass-border-strong); border-radius: 24px; text-align: center; padding: 3rem 2rem; margin-bottom: 4rem;" class="fade-in">
            <h3 style="font-size: 1.1rem; font-weight: 700; color: rgba(255,255,255,0.6); margin-bottom: 0.5rem;">Create your own routines</h3>
            <p style="color: rgba(255, 255, 255, 0.3); font-size: 0.85rem; margin-bottom: 1.5rem;">Want to do something extra? Build your own custom workouts.</p>
            <button onclick="openModal()" class="btn-outline">
                Start Building
            </button>
        </div>
    @endif
    @endif

    {{-- 4. Client Workouts Section (Trainer Only) --}}
    @if(Auth::user()->role === 'trainer')
    <div style="margin-bottom: 2rem; display: flex; justify-content: space-between; align-items: center;" class="fade-in">
        <h3 style="font-size: 1.4rem; font-weight: 800; color: #fff;">Client Workouts</h3>
        <span style="color: rgba(255, 255, 255, 0.3); font-size: 0.85rem;">Showing {{ $workouts->total() }} total</span>
    </div>

    @if($workouts->count() > 0)
        <div class="workout-grid fade-in" style="animation-delay: 0.3s;">
            @foreach($workouts as $workout)
                @include('workouts._card', ['workout' => $workout, 'showActions' => true, 'showClient' => true])
            @endforeach
        </div>
        <div class="mt-8">
            {{ $workouts->links() }}
        </div>
    @else
        <div style="background: var(--glass-bg); border: 1px dashed var(--glass-border-strong); border-radius: 24px; text-align: center; padding: 4rem 2rem; margin-bottom: 4rem;" class="fade-in">
            <div style="font-size: 3.5rem; opacity: 0.2; margin-bottom: 1rem;">🏋️</div>
            <h3 style="font-size: 1.4rem; font-weight: 800; color: #fff; margin-bottom: 0.5rem;">No workouts found</h3>
            <p style="color: rgba(255, 255, 255, 0.4); font-size: 0.95rem; margin-bottom: 2rem; max-width: 400px; margin-left: auto; margin-right: auto;">Start building your perfect routine for your clients!</p>
            <button onclick="openModal()" class="btn-premium">
                Create First Client Workout
            </button>
        </div>
    @endif
    @endif

    {{-- 3. Workout Templates Section --}}
    @if(Auth::user()->role === 'trainer')
    <div id="templates-section" style="margin-bottom: 2rem; display: flex; justify-content: space-between; align-items: center; padding-top: 2rem;" class="fade-in">
        <div>
            <h3 style="font-size: 1.4rem; font-weight: 800; color: #fff;">Workout Templates</h3>
            <p style="color: rgba(255, 255, 255, 0.3); font-size: 0.85rem;">Professional pre-built routines</p>
        </div>
    </div>

    <div class="template-grid fade-in" style="animation-delay: 0.4s;">

        @foreach($templates as $tmp)
            <div class="template-card">
                <div style="width: 40px; height: 40px; border-radius: 12px; background: rgba(139, 92, 246, 0.1); border: 1px solid rgba(139, 92, 246, 0.2); display: flex; align-items: center; justify-content: center; margin-bottom: 1.2rem; color: #a78bfa;">
                    <i data-lucide="{{ $tmp['icon'] }}" style="width: 20px; height: 20px;"></i>
                </div>
                <h4 style="font-size: 1.05rem; font-weight: 700; color: #fff; margin-bottom: 4px;">{{ $tmp['name'] }}</h4>
                <p style="color: rgba(255, 255, 255, 0.4); font-size: 0.75rem; margin-bottom: 1.2rem;">{{ $tmp['desc'] }}</p>
                
                <div style="display: flex; gap: 12px; margin-bottom: 1.5rem;">
                    <div style="font-size: 0.7rem; color: rgba(255,255,255,0.3); text-transform: uppercase; letter-spacing: 0.05em;">
                        <span style="color: #fff; font-weight: 700;">{{ $tmp['diff'] }}</span>
                    </div>
                    <div style="font-size: 0.7rem; color: rgba(255,255,255,0.3); text-transform: uppercase; letter-spacing: 0.05em;">
                        <span style="color: #fff; font-weight: 700;">{{ $tmp['dur'] }}</span>
                    </div>
                </div>
                
                <div style="display: flex; gap: 8px; margin-top: 8px; width: 100%;">
                    <button type="button" onclick="showTemplateDetails('{{ $tmp['name'] }}')" class="btn-outline" style="flex: 1; font-size: 0.75rem; padding: 10px; justify-content: center; border-radius: 12px;">
                        <i data-lucide="eye" style="width: 14px; height: 14px; margin-right: 4px;"></i> Details
                    </button>
                    <button type="button" onclick="triggerAssignTemplateModal('{{ $tmp['name'] }}')" class="btn-premium" style="flex: 1.2; font-size: 0.75rem; padding: 10px; justify-content: center; border-radius: 12px;">
                        <i data-lucide="user-plus" style="width: 14px; height: 14px; margin-right: 4px;"></i> Assign
                    </button>
                </div>
            </div>
        @endforeach
    </div>
    @endif
@endsection

@push('modals')
{{-- Template Details Modal --}}
<div id="templateDetailsModal" class="modal-overlay">
    <div class="modal-content" style="max-width: 500px;">
        <div class="modal-header">
            <div>
                <h2 id="detailTemplateName" style="font-size: 1.4rem; font-weight: 900; color: #fff;">Template Details</h2>
                <p id="detailTemplateDesc" style="color: rgba(255,255,255,0.4); font-size: 0.85rem; margin-top: 4px;">Description</p>
            </div>
            <button type="button" onclick="closeTemplateDetailsModal()" class="modal-close-btn" style="background: rgba(255,255,255,0.05); border: 1px solid var(--glass-border); color: #fff; width: 36px; height: 36px; border-radius: 50%; cursor: pointer; display: flex; align-items: center; justify-content: center;">
                <i data-lucide="x" style="width: 20px; height: 20px;"></i>
            </button>
        </div>
        <div class="modal-body" style="padding: 2rem; display: flex; flex-direction: column; gap: 20px;">
            {{-- Quick Stats --}}
            <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 12px; background: rgba(255,255,255,0.02); padding: 1rem; border-radius: 18px; border: 1px solid rgba(255,255,255,0.05); text-align: center;">
                <div>
                    <span style="display: block; font-size: 0.65rem; color: rgba(255,255,255,0.3); text-transform: uppercase; font-weight: 800; letter-spacing: 0.05em; margin-bottom: 4px;">Difficulty</span>
                    <span id="detailTemplateDiff" style="font-weight: 700; color: #a78bfa; font-size: 0.85rem;">Intermediate</span>
                </div>
                <div>
                    <span style="display: block; font-size: 0.65rem; color: rgba(255,255,255,0.3); text-transform: uppercase; font-weight: 800; letter-spacing: 0.05em; margin-bottom: 4px;">Duration</span>
                    <span id="detailTemplateDur" style="font-weight: 700; color: #f472b6; font-size: 0.85rem;">65m</span>
                </div>
                <div>
                    <span style="display: block; font-size: 0.65rem; color: rgba(255,255,255,0.3); text-transform: uppercase; font-weight: 800; letter-spacing: 0.05em; margin-bottom: 4px;">Exercises</span>
                    <span id="detailTemplateExCount" style="font-weight: 700; color: #34d399; font-size: 0.85rem;">4</span>
                </div>
            </div>

            {{-- Exercise List --}}
            <div>
                <label style="display: block; font-size: 0.75rem; font-weight: 800; color: rgba(196,181,253,0.6); text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 12px;">Included Exercises</label>
                <div id="detailExerciseList" style="display: flex; flex-direction: column; gap: 10px;">
                    {{-- Dynamically populated --}}
                </div>
            </div>
        </div>
        <div class="modal-footer" style="padding: 1.5rem; display: flex; gap: 12px;">
            <button type="button" id="detailAssignBtn" class="btn-premium" style="flex: 1; justify-content: center; padding: 14px;">
                <i data-lucide="user-plus" style="width: 18px; height: 18px; margin-right: 6px;"></i> Assign Template to Client
            </button>
        </div>
    </div>
</div>

{{-- Custom Confirm Delete Modal --}}
<div id="customConfirmModal" class="modal-overlay">
    <div class="modal-content" style="max-width: 400px; text-align: center; border: 1px solid rgba(244, 63, 94, 0.3); box-shadow: 0 20px 40px rgba(244, 63, 94, 0.15); background: #0f172a;">
        <div class="modal-body" style="padding: 2.5rem 2rem 2rem; display: flex; flex-direction: column; align-items: center; gap: 1rem;">
            <div style="width: 64px; height: 64px; border-radius: 50%; background: rgba(244, 63, 94, 0.1); border: 2px solid rgba(244, 63, 94, 0.25); display: flex; align-items: center; justify-content: center; font-size: 1.8rem; margin-bottom: 0.5rem; animation: pulseRed 2s infinite;">
                ⚠️
            </div>
            <h3 style="font-size: 1.3rem; font-weight: 800; color: #fff; margin: 0;">Delete Workout</h3>
            <p style="font-size: 0.9rem; color: rgba(255,255,255,0.6); line-height: 1.6; margin: 0;">
                Are you sure you want to delete this workout? This action is permanent and cannot be undone.
            </p>
        </div>
        <div class="modal-footer" style="padding: 1.5rem; display: flex; justify-content: center; gap: 12px; border-top: none; background: transparent;">
            <button type="button" onclick="closeConfirmDelete()" style="flex: 1; padding: 11px 20px; font-weight: 700; font-size: 0.85rem; border-radius: 12px; background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.1); color: #fff; cursor: pointer; transition: all 0.2s;" onmouseover="this.style.background='rgba(255,255,255,0.1)'" onmouseout="this.style.background='rgba(255,255,255,0.05)'">
                Cancel
            </button>
            <button type="button" id="confirmDeleteBtn" style="flex: 1; padding: 11px 20px; font-weight: 700; font-size: 0.85rem; border-radius: 12px; background: linear-gradient(135deg, #f43f5e, #be123c); border: none; color: #fff; cursor: pointer; transition: all 0.2s; box-shadow: 0 4px 15px rgba(244, 63, 94, 0.35);" onmouseover="this.style.transform='translateY(-1px)';this.style.boxShadow='0 6px 20px rgba(244, 63, 94, 0.55)'" onmouseout="this.style.transform='';this.style.boxShadow='0 4px 15px rgba(244, 63, 94, 0.35)'">
                Delete
            </button>
        </div>
    </div>
</div>

<style>
@keyframes pulseRed {
    0% { transform: scale(1); box-shadow: 0 0 0 0 rgba(244, 63, 94, 0.4); }
    70% { transform: scale(1.05); box-shadow: 0 0 0 10px rgba(244, 63, 94, 0); }
    100% { transform: scale(1); box-shadow: 0 0 0 0 rgba(244, 63, 94, 0); }
}
</style>

{{-- Assign Template Modal --}}
<div id="assignTemplateModal" class="modal-overlay">
    <div class="modal-content" style="max-width: 450px;">
        <div class="modal-header">
            <div>
                <h2 style="font-size: 1.25rem; font-weight: 900; color: #fff;">Assign Template</h2>
                <p style="color: rgba(255,255,255,0.4); font-size: 0.85rem;" id="assignTemplateSubtitle">Select client for template</p>
            </div>
            <button type="button" onclick="closeAssignTemplateModal()" class="modal-close-btn" style="background: rgba(255,255,255,0.05); border: 1px solid var(--glass-border); color: #fff; width: 36px; height: 36px; border-radius: 50%; cursor: pointer; display: flex; align-items: center; justify-content: center;">
                <i data-lucide="x" style="width: 20px; height: 20px;"></i>
            </button>
        </div>
        <div class="modal-body" style="padding: 1.5rem;">
            <form id="assignTemplateForm" action="{{ route('workouts.use-template') }}" method="POST">
                @csrf
                <input type="hidden" name="template_name" id="assignTemplateName">
                <input type="hidden" name="trainee_id" id="assignTraineeId">
            </form>
            
            <div style="display: flex; flex-direction: column; gap: 10px;">
                <label style="font-size: 0.7rem; font-weight: 800; color: rgba(196,181,253,0.5); text-transform: uppercase; letter-spacing: 0.1em; margin-bottom: 5px;">Select Client</label>
                <div style="display: flex; flex-direction: column; gap: 10px; max-height: 320px; overflow-y: auto; padding-right: 5px;">
                    @foreach($clients ?? [] as $client)
                        <button type="button" onclick="selectTemplateClient('{{ $client->id }}')" class="client-assign-btn" 
                                style="display: flex; align-items: center; justify-content: space-between; padding: 14px 18px; border-radius: 16px; width: 100%; border: 1px solid rgba(139, 92, 246, 0.2); background: rgba(139, 92, 246, 0.05); color: #fff; cursor: pointer; transition: all 0.3s; text-align: left;">
                            <div style="display: flex; align-items: center; gap: 12px;">
                                <div style="width: 32px; height: 32px; background: rgba(139,92,246,0.2); border-radius: 10px; display: flex; align-items: center; justify-content: center; font-weight: 800; color: #c4b5fd;">
                                    {{ substr($client->name, 0, 1) }}
                                </div>
                                <span style="font-weight: 700;">{{ $client->name }}</span>
                            </div>
                            <i data-lucide="chevron-right" style="width: 18px; height: 18px; opacity: 0.3; color: #fff;"></i>
                        </button>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Create Workout Modal --}}
<div id="workoutModal" class="modal-overlay">
    <div class="modal-content">
        <form action="{{ route('workouts.store') }}" method="POST">
            @csrf
            
            <div class="modal-header">
                <div>
                    <h2 style="font-size: 1.5rem; font-weight: 800; color: #fff;">
                        {{ Auth::user()->role === 'trainer' ? 'Create Client Workout' : 'Create Custom Workout' }} ✨
                    </h2>
                    <p style="color: rgba(255,255,255,0.4); font-size: 0.85rem;">
                        {{ Auth::user()->role === 'trainer' ? 'Build a routine for your client' : 'Build your custom training routine' }}
                    </p>
                </div>
                <button type="button" onclick="closeModal()" style="background: rgba(255,255,255,0.05); border: 1px solid var(--glass-border); color: #fff; width: 36px; height: 36px; border-radius: 50%; cursor: pointer; display: flex; align-items: center; justify-content: center;">
                    <i data-lucide="x" style="width: 20px; height: 20px;"></i>
                </button>
            </div>

            <div class="modal-body">
                @if(Auth::user()->role === 'trainer')
                    <div class="form-group" style="margin-bottom: 2rem; background: rgba(139, 92, 246, 0.05); padding: 1.5rem; border-radius: 20px; border: 1px solid rgba(139, 92, 246, 0.2);">
                        <label class="form-label" style="color: #a78bfa;">1. Select Client to Assign</label>
                        <select name="trainee_id" class="form-input smooth-select" style="border-color: rgba(139, 92, 246, 0.4); font-size: 1.1rem; padding: 15px;" required>
                            <option value="">-- Choose a Client --</option>
                            @foreach($clients as $client)
                                <option value="{{ $client->id }}">{{ $client->name }}</option>
                            @endforeach
                        </select>
                    </div>
                @endif

                <div class="grid md:grid-cols-2 gap-6">
                    <div class="form-group">
                        <label class="form-label">Workout Title</label>
                        <input type="text" name="title" class="form-input" placeholder="e.g. Morning Push Day" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Difficulty</label>
                        <select name="difficulty" class="form-input smooth-select">
                            <option value="Beginner">Beginner</option>
                            <option value="Intermediate">Intermediate</option>
                            <option value="Advanced">Advanced</option>
                        </select>
                    </div>
                </div>

                <div class="grid md:grid-cols-2 gap-6">
                    <div class="form-group">
                        <label class="form-label">Type</label>
                        <select name="type" class="form-input smooth-select">
                            <option value="Strength">Strength</option>
                            <option value="Cardio">Cardio</option>
                            <option value="HIIT">HIIT</option>
                            <option value="Flexibility">Flexibility</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Duration (mins)</label>
                        <input type="number" name="duration_minutes" class="form-input" placeholder="45">
                    </div>
                </div>

                <div style="margin-top: 2rem;">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
                        <h3 style="font-size: 1rem; font-weight: 700; color: #fff;">Exercises</h3>
                        <button type="button" onclick="addExerciseRow()" style="color: #a78bfa; background: none; border: none; font-size: 0.85rem; font-weight: 700; cursor: pointer; display: flex; align-items: center; gap: 4px;">
                            <i data-lucide="plus" style="width: 14px; height: 14px;"></i> Add Exercise
                        </button>
                    </div>

                    <div id="exerciseList">
                        {{-- Initial Exercise Row --}}
                        <div class="exercise-item">
                            <div style="flex: 1;">
                                <label class="form-label">Exercise</label>
                                <select name="exercises[0][exercise_id]" class="form-input smooth-select" required>
                                    <option value="">Search exercise...</option>
                                    @foreach($exercises as $exercise)
                                        <option value="{{ $exercise->id }}">{{ $exercise->name }} ({{ $exercise->muscle_group }})</option>
                                    @endforeach
                                </select>
                            </div>
                            <div style="width: 80px;">
                                <label class="form-label">Sets</label>
                                <input type="number" name="exercises[0][sets]" class="form-input" value="3" required>
                            </div>
                            <div style="width: 80px;">
                                <label class="form-label">Reps</label>
                                <input type="number" name="exercises[0][reps]" class="form-input" value="10" required>
                            </div>
                            <button type="button" onclick="this.parentElement.remove()" style="margin-top: 24px; background: none; border: none; color: rgba(255,255,255,0.2); cursor: pointer;">
                                <i data-lucide="trash-2" style="width: 18px; height: 18px;"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal-footer">
                <button type="submit" class="btn-premium" style="flex: 1; justify-content: center;">Save & Create Workout</button>
                <button type="button" onclick="closeModal()" class="btn-outline" style="flex: 0.3; justify-content: center;">Cancel</button>
            </div>
        </form>
    </div>
</div>

@endpush

@push('scripts')
<script>
    function openModal() {
        document.getElementById('workoutModal').style.display = 'flex';
        document.body.style.overflow = 'hidden';
        initAllSmoothSelects();
    }

    function closeModal() {
        document.getElementById('workoutModal').style.display = 'none';
        document.body.style.overflow = 'auto';
    }

    let exerciseCount = 1;
    function addExerciseRow() {
        const list = document.getElementById('exerciseList');
        const row = document.createElement('div');
        row.className = 'exercise-item fade-in';
        row.innerHTML = `
            <div style="flex: 1;">
                <label class="form-label">Exercise</label>
                <select name="exercises[${exerciseCount}][exercise_id]" class="form-input smooth-select" required>
                    <option value="">Search exercise...</option>
                    @foreach($exercises as $exercise)
                        <option value="{{ $exercise->id }}">{{ $exercise->name }} ({{ $exercise->muscle_group }})</option>
                    @endforeach
                </select>
            </div>
            <div style="width: 80px;">
                <label class="form-label">Sets</label>
                <input type="number" name="exercises[${exerciseCount}][sets]" class="form-input" value="3" required>
            </div>
            <div style="width: 80px;">
                <label class="form-label">Reps</label>
                <input type="number" name="exercises[${exerciseCount}][reps]" class="form-input" value="10" required>
            </div>
            <button type="button" onclick="this.parentElement.remove()" style="margin-top: 24px; background: none; border: none; color: rgba(255,255,255,0.2); cursor: pointer;">
                <i data-lucide="trash-2" style="width: 18px; height: 18px;"></i>
            </button>
        `;
        list.appendChild(row);
        exerciseCount++;
        lucide.createIcons();
        initAllSmoothSelects();
    }

    function filterWorkouts(type) {
        // Update tabs
        const tabs = document.querySelectorAll('.filter-tab');
        tabs.forEach(tab => tab.classList.remove('active'));
        if (event && event.target) {
            event.target.classList.add('active');
        }

        // Filter cards
        const cards = document.querySelectorAll('.workout-card');
        cards.forEach(card => {
            if (type === 'all' || card.getAttribute('data-type') === type) {
                card.style.display = 'flex';
                card.classList.add('fade-in');
            } else {
                card.style.display = 'none';
            }
        });
    }

    let workoutIdToDelete = null;

    function deleteWorkout(id) {
        workoutIdToDelete = id;
        const modal = document.getElementById('customConfirmModal');
        modal.style.display = 'flex';
        modal.classList.add('fade-in');
    }

    function closeConfirmDelete() {
        const modal = document.getElementById('customConfirmModal');
        modal.style.display = 'none';
        workoutIdToDelete = null;
    }

    document.getElementById('confirmDeleteBtn').addEventListener('click', function() {
        if (workoutIdToDelete) {
            document.getElementById('delete-form-' + workoutIdToDelete).submit();
        }
    });

    // Make cards clickable
    document.querySelectorAll('.workout-card').forEach(card => {
        card.addEventListener('click', (e) => {
            // Don't trigger if clicking buttons, links, or their children
            if (e.target.closest('button') || e.target.closest('a') || e.target.closest('form')) return;
            
            const workoutId = card.getAttribute('data-id');
            if (workoutId) {
                window.location.href = `/workouts/${workoutId}`;
            }
        });
    });


    
    function makeDropdownSmooth(select) {
        if (select.dataset.customInitialized) return;
        select.dataset.customInitialized = "true";
        
        // Hide native select
        select.style.position = 'absolute';
        select.style.opacity = '0';
        select.style.width = '0';
        select.style.height = '0';
        select.style.pointerEvents = 'none';
        
        // Wrap select in a container
        const container = document.createElement('div');
        container.className = 'custom-select-container';
        container.style.position = 'relative';
        container.style.width = '100%';
        select.parentNode.insertBefore(container, select);
        container.appendChild(select);
        
        // Create custom trigger element
        const trigger = document.createElement('div');
        // Add form-input class to perfectly keep the default input/select layout!
        trigger.className = 'form-input custom-select-trigger';
        
        // Inline styles to ensure it behaves like a flexbox wrapper for label + custom chevron
        trigger.style.display = 'flex';
        trigger.style.alignItems = 'center';
        trigger.style.justifyContent = 'space-between';
        trigger.style.cursor = 'pointer';
        trigger.style.userSelect = 'none';
        
        // We set background-image manually to keep the default custom chevron from the select!
        trigger.style.backgroundImage = `url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='16' height='16' viewBox='0 0 24 24' fill='none' stroke='%23a78bfa' stroke-width='2.5' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpath d='m6 9 6 6 6-6'/%3E%3C/svg%3E")`;
        trigger.style.backgroundRepeat = 'no-repeat';
        trigger.style.backgroundPosition = 'right 16px center';
        trigger.style.backgroundSize = '16px';
        trigger.style.paddingRight = '40px'; // Give room for the chevron
        
        const triggerLabel = document.createElement('span');
        triggerLabel.className = 'trigger-label';
        
        const activeOption = select.options[select.selectedIndex] || select.options[0];
        triggerLabel.innerText = activeOption ? activeOption.text : '';
        if (select.value === "") {
            triggerLabel.style.color = 'rgba(255, 255, 255, 0.4)';
        } else {
            triggerLabel.style.color = '#fff';
            triggerLabel.style.fontWeight = '600';
        }
        
        trigger.appendChild(triggerLabel);
        container.appendChild(trigger);
        
        // Create options list container
        const optionsContainer = document.createElement('div');
        optionsContainer.className = 'custom-select-options';
        
        // Populate custom options from native options
        Array.from(select.options).forEach(opt => {
            if (opt.value === "") return;
            
            const customOpt = document.createElement('div');
            customOpt.className = 'custom-select-option';
            customOpt.innerText = opt.text;
            
            if (opt.selected) {
                customOpt.classList.add('selected');
            }
            
            customOpt.addEventListener('click', (e) => {
                e.stopPropagation();
                select.value = opt.value;
                select.dispatchEvent(new Event('change'));
                
                triggerLabel.innerText = opt.text;
                if (opt.value === "") {
                    triggerLabel.style.color = 'rgba(255, 255, 255, 0.4)';
                    triggerLabel.style.fontWeight = '400';
                } else {
                    triggerLabel.style.color = '#fff';
                    triggerLabel.style.fontWeight = '600';
                }
                
                // Highlight selected option
                Array.from(optionsContainer.children).forEach(child => {
                    child.classList.remove('selected');
                });
                customOpt.classList.add('selected');
                
                // Close dropdown
                optionsContainer.classList.remove('show');
                container.classList.remove('active');
            });
            
            optionsContainer.appendChild(customOpt);
        });
        
        container.appendChild(optionsContainer);
        
        // Toggle on trigger click
        trigger.addEventListener('click', (e) => {
            e.stopPropagation();
            
            // Collapse all other dropdowns
            document.querySelectorAll('.custom-select-options').forEach(opt => {
                if (opt !== optionsContainer) {
                    opt.classList.remove('show');
                    opt.parentElement.classList.remove('active');
                }
            });
            
            optionsContainer.classList.toggle('show');
            container.classList.toggle('active');
        });
    }

    function initAllSmoothSelects() {
        document.querySelectorAll('select.smooth-select').forEach(select => {
            makeDropdownSmooth(select);
        });
    }

    // Global click listener to close dropdowns when clicking outside
    document.addEventListener('click', function() {
        document.querySelectorAll('.custom-select-options').forEach(opt => {
            opt.classList.remove('show');
            opt.parentElement.classList.remove('active');
        });
    });

    function triggerAssignTemplateModal(templateName) {
        document.getElementById('assignTemplateName').value = templateName;
        document.getElementById('assignTemplateSubtitle').innerText = `Select client for "${templateName}"`;
        document.getElementById('assignTemplateModal').style.display = 'flex';
        document.body.style.overflow = 'hidden';
    }

    function closeAssignTemplateModal() {
        document.getElementById('assignTemplateModal').style.display = 'none';
        document.body.style.overflow = 'auto';
    }

    function selectTemplateClient(clientId) {
        document.getElementById('assignTraineeId').value = clientId;
        document.getElementById('assignTemplateForm').submit();
    }

    const workoutTemplatesData = @json($templates);

    function showTemplateDetails(templateName) {
        const template = workoutTemplatesData.find(t => t.name === templateName);
        if (!template) return;

        document.getElementById('detailTemplateName').innerText = template.name;
        document.getElementById('detailTemplateDesc').innerText = template.desc;
        document.getElementById('detailTemplateDiff').innerText = template.diff;
        document.getElementById('detailTemplateDur').innerText = template.dur;
        document.getElementById('detailTemplateExCount').innerText = template.ex;

        const listContainer = document.getElementById('detailExerciseList');
        listContainer.innerHTML = '';

        template.exercises.forEach(ex => {
            const item = document.createElement('div');
            item.style = 'display: flex; align-items: center; justify-content: space-between; padding: 14px 18px; background: rgba(255, 255, 255, 0.03); border: 1px solid rgba(255, 255, 255, 0.05); border-radius: 16px;';
            item.innerHTML = `
                <div style="display: flex; align-items: center; gap: 12px;">
                    <div style="width: 8px; height: 8px; background: var(--vg-accent); border-radius: 50%; box-shadow: 0 0 8px var(--vg-accent);"></div>
                    <span style="font-weight: 700; color: #fff; font-size: 0.9rem;">${ex.name}</span>
                </div>
                <span style="font-size: 0.75rem; font-weight: 800; color: #c4b5fd; background: rgba(139, 92, 246, 0.15); padding: 4px 10px; border-radius: 8px; border: 1px solid rgba(139, 92, 246, 0.2);">
                    ${ex.sets} Sets × ${ex.reps} ${ex.name.toLowerCase().includes('plank') ? 'Secs' : 'Reps'}
                </span>
            `;
            listContainer.appendChild(item);
        });

        // Set up assign button click action
        const assignBtn = document.getElementById('detailAssignBtn');
        assignBtn.onclick = () => {
            closeTemplateDetailsModal();
            triggerAssignTemplateModal(template.name);
        };

        document.getElementById('templateDetailsModal').style.display = 'flex';
        document.body.style.overflow = 'hidden';
        
        if (window.lucide) {
            lucide.createIcons();
        }
    }

    function closeTemplateDetailsModal() {
        document.getElementById('templateDetailsModal').style.display = 'none';
        document.body.style.overflow = 'auto';
    }

    // Initialize Lucide icons for dynamic elements
    window.addEventListener('load', () => {
        initAllSmoothSelects();
        
        if (window.lucide) {
            lucide.createIcons();
        }
        
        // Auto-open modal if create=1 query param is present
        const urlParams = new URLSearchParams(window.location.search);
        if (urlParams.get('create') === '1') {
            openModal();
        }
    });
</script>
@endpush