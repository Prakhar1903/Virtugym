@extends('layouts.app')

@section('title', 'My Bookings')

@section('content')
<style>
    .tab-btn {
        padding: 8px 16px;
        border-radius: 8px;
        font-size: .85rem;
        font-weight: 600;
        color: var(--vg-text-muted);
        background: transparent;
        border: none;
        cursor: pointer;
        transition: all 0.2s;
    }
    .tab-btn.active {
        background: var(--vg-accent-soft);
        color: var(--vg-text-strong);
    }
    .tab-btn:hover:not(.active) {
        color: var(--vg-text-strong);
    }
    .booking-card {
        background: rgba(255, 255, 255, 0.015);
        border: 1px solid rgba(255, 255, 255, 0.05);
        border-radius: 24px;
        padding: 1.8rem;
        margin-bottom: 1.5rem;
        transition: all 0.4s cubic-bezier(0.16, 1, 0.3, 1);
        position: relative;
    }
    .booking-card:hover {
        border-color: rgba(139, 92, 246, 0.25) !important;
        transform: translateY(-3px);
        box-shadow: 0 16px 40px rgba(139, 92, 246, 0.08);
        background: rgba(255, 255, 255, 0.025);
    }
    .booking-card-active {
        border-color: rgba(139, 92, 246, 0.55) !important;
        background: linear-gradient(135deg, rgba(8, 8, 26, 0.98) 0%, rgba(139, 92, 246, 0.03) 100%) !important;
        animation: active-card-pulse 2s infinite alternate ease-in-out;
    }
    @keyframes active-card-pulse {
        0% {
            box-shadow: 0 8px 24px rgba(139, 92, 246, 0.12);
        }
        100% {
            box-shadow: 0 16px 35px rgba(139, 92, 246, 0.25);
            border-color: rgba(139, 92, 246, 0.75) !important;
        }
    }
    .past-session-item {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 1.2rem;
        background: rgba(255, 255, 255, 0.01);
        border: 1px solid rgba(255, 255, 255, 0.04);
        border-radius: 16px;
        margin-bottom: .8rem;
        transition: all 0.2s;
    }
    .past-session-item:hover {
        border-color: rgba(255, 255, 255, 0.1);
        background: rgba(255, 255, 255, 0.03);
    }
    .stat-badge {
        background: rgba(255, 255, 255, 0.03);
        border: 1px solid rgba(255, 255, 255, 0.06);
        border-radius: 20px;
        padding: 1.5rem;
        text-align: center;
    }
    .timeline-item {
        position: relative;
        padding-left: 24px;
        border-left: 2px solid rgba(139, 92, 246, 0.2);
        padding-bottom: 1.5rem;
    }
    .timeline-item::before {
        content: '';
        position: absolute;
        left: -6px;
        top: 4px;
        width: 10px;
        height: 10px;
        border-radius: 50%;
        background: var(--vg-accent);
        box-shadow: 0 0 8px var(--vg-accent-glow);
    }
    .countdown-badge {
        background: linear-gradient(135deg, rgba(139,92,246,0.1) 0%, rgba(249,168,212,0.05) 100%);
        border: 1px solid rgba(139, 92, 246, 0.2);
        color: #c4b5fd;
        padding: 6px 12px;
        border-radius: 20px;
        font-size: .8rem;
        font-weight: 700;
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }
    .action-btn {
        background: rgba(255,255,255,0.03);
        border: 1px solid rgba(255,255,255,0.08);
        color: rgba(255,255,255,0.85);
        padding: 8px 16px;
        border-radius: 12px;
        font-size: .8rem;
        font-weight: 700;
        cursor: pointer;
        transition: all 0.2s;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        text-decoration: none;
    }
    .action-btn:hover {
        background: rgba(255,255,255,0.08);
        border-color: rgba(255,255,255,0.18);
        color: #fff;
    }
    .action-btn.primary {
        background: var(--vg-gradient);
        border: none;
        box-shadow: 0 4px 15px var(--vg-accent-glow);
        color: white;
    }
    .action-btn.primary:hover {
        transform: translateY(-1px);
        box-shadow: 0 6px 20px var(--vg-accent-glow);
    }
    .action-btn.danger {
        background: rgba(244,63,94,0.08);
        border-color: rgba(244,63,94,0.18);
        color: #f43f5e;
    }
    .action-btn.danger:hover {
        background: rgba(244,63,94,0.18);
        color: #fff;
    }
    
    /* Horizontal Progress timeline styling */
    .progress-timeline {
        display: flex;
        justify-content: space-between;
        position: relative;
        margin: 1.2rem 0;
        padding: 0 10px;
    }
    .progress-timeline::before {
        content: '';
        position: absolute;
        top: 10px;
        left: 20px;
        right: 20px;
        height: 2px;
        background: rgba(255, 255, 255, 0.06);
        z-index: 1;
    }
    .progress-line-fill {
        position: absolute;
        top: 10px;
        left: 20px;
        height: 2px;
        background: linear-gradient(90deg, var(--vg-accent), #10b981);
        z-index: 1;
        transition: width 0.4s ease;
    }
    .progress-step {
        display: flex;
        flex-direction: column;
        align-items: center;
        position: relative;
        z-index: 2;
        font-size: 0.7rem;
        color: var(--vg-text-muted);
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.02em;
    }
    .progress-step-dot {
        width: 22px;
        height: 22px;
        border-radius: 50%;
        background: #121226;
        border: 2px solid rgba(255, 255, 255, 0.1);
        margin-bottom: 6px;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.3s ease;
    }
    .progress-step.active .progress-step-dot {
        border-color: var(--vg-accent);
        background: var(--vg-accent);
        box-shadow: 0 0 10px rgba(139, 92, 246, 0.45);
    }
    .progress-step.active {
        color: var(--vg-text-strong);
    }
    .progress-step.completed .progress-step-dot {
        border-color: #10b981;
        background: #10b981;
        box-shadow: 0 0 10px rgba(16, 185, 129, 0.4);
    }
    .progress-step.completed {
        color: #10b981;
    }
    
    /* Beautiful Empty State designs */
    .empty-state-card {
        background: rgba(255, 255, 255, 0.015);
        border: 1px solid rgba(255, 255, 255, 0.05);
        border-radius: 24px;
        padding: 3rem 2rem;
        text-align: center;
        box-shadow: 0 8px 30px rgba(0, 0, 0, 0.15);
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 10px;
        transition: all 0.3s ease;
    }
    .empty-state-icon-wrapper {
        width: 58px;
        height: 58px;
        border-radius: 18px;
        background: rgba(139, 92, 246, 0.08);
        border: 1px solid rgba(139, 92, 246, 0.15);
        display: flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 6px;
        color: var(--vg-accent);
        filter: drop-shadow(0 0 12px rgba(139, 92, 246, 0.15));
    }

    /* Mini Calendar */
    .mini-calendar-card {
        background: rgba(255, 255, 255, 0.015);
        border: 1px solid rgba(255, 255, 255, 0.05);
        border-radius: 20px;
        padding: 1.2rem;
    }
    .calendar-grid {
        display: grid;
        grid-template-columns: repeat(7, 1fr);
        gap: 4px;
        text-align: center;
        font-size: 0.65rem;
        font-weight: 700;
        color: var(--vg-text-muted);
    }
    .calendar-day-header {
        font-weight: 800;
        color: rgba(255, 255, 255, 0.25);
        padding-bottom: 6px;
    }
    .calendar-day {
        aspect-ratio: 1;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 6px;
        color: rgba(255, 255, 255, 0.6);
        position: relative;
        cursor: default;
    }
    .calendar-day.today {
        background: rgba(139, 92, 246, 0.15);
        border: 1px solid rgba(139, 92, 246, 0.35);
        color: #fff;
        font-weight: 900;
    }
    .calendar-day.booked {
        background: rgba(16, 185, 129, 0.12);
        border: 1px solid rgba(16, 185, 129, 0.3);
        color: #10b981;
        font-weight: 900;
    }
    .calendar-day.booked::after {
        content: '';
        position: absolute;
        bottom: 2px;
        width: 3px;
        height: 3px;
        border-radius: 50%;
        background: #10b981;
    }

    @media(max-width: 992px) {
        .layout-container { flex-direction: column; }
        .summary-panel { width: 100% !important; margin-top: 2rem; }
    }
</style>

{{-- Top Row: Stats (Trainer Only) --}}
@if($isTrainer && isset($trainerStats))
    <div style="display:grid;grid-template-columns:repeat(auto-fit, minmax(240px, 1fr));gap:1.5rem;margin-bottom:2.5rem;" class="fade-in-up">
        <div class="stat-badge">
            <p style="font-size:.75rem;color:rgba(255,255,255,.4);font-weight:700;text-transform:uppercase;letter-spacing:.05em;margin-bottom:6px;">Today's Sessions</p>
            <p style="font-size:2rem;font-weight:900;color:#fff;margin-bottom:0;">{{ $trainerStats['todays_sessions'] }}</p>
            @if($trainerStats['todays_sessions'] == 0 && isset($upcomingBookings) && $upcomingBookings->count() > 0)
                @php
                    $nextSession = \Carbon\Carbon::parse($upcomingBookings->first()->session_date);
                    $daysToNext = now()->diffInDays($nextSession, false);
                    $daysToNext = $daysToNext < 0 ? 0 : floor($daysToNext);
                    $nextText = $daysToNext == 0 ? 'later today' : ($daysToNext == 1 ? 'tomorrow' : "in $daysToNext days");
                @endphp
                <p style="font-size:.7rem;color:var(--vg-accent);margin-top:4px;font-weight:600;">next session {{ $nextText }}</p>
            @endif
        </div>
        <div class="stat-badge">
            <p style="font-size:.75rem;color:rgba(255,255,255,.4);font-weight:700;text-transform:uppercase;letter-spacing:.05em;margin-bottom:6px;">This Week's Earnings</p>
            <p style="font-size:2rem;font-weight:900;color:#10b981;margin-bottom:0;">₹{{ number_format($trainerStats['weekly_earnings']) }}</p>
        </div>
        <div class="stat-badge">
            <p style="font-size:.75rem;color:rgba(255,255,255,.4);font-weight:700;text-transform:uppercase;letter-spacing:.05em;margin-bottom:6px;">Total Upcoming</p>
            <p style="font-size:2rem;font-weight:900;color:var(--vg-accent);margin-bottom:0;">{{ $trainerStats['total_upcoming'] }}</p>
        </div>
    </div>
@endif

<div class="layout-container" style="max-width:1400px;margin:0 auto;display:flex;gap:2rem;align-items:flex-start;">
    
    {{-- Main Content Area --}}
    <div style="flex:1;min-width:0;">
        <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:2rem;flex-wrap:wrap;gap:1.5rem;">
            <div>
                <h1 style="font-size:1.8rem;font-weight:900;background:var(--vg-title-gradient);-webkit-background-clip:text;background-clip:text;color:transparent;margin-bottom:.3rem;">My Bookings 📅</h1>
                <p style="color:var(--vg-text-muted);font-size:.85rem;">Manage your training sessions</p>
            </div>
            
            <div style="display:flex;align-items:center;gap:1rem;flex-wrap:wrap;">
                {{-- Trainee Filter (Trainer Only) --}}
                @if($isTrainer && isset($uniqueTrainees) && $uniqueTrainees->count() > 0)
                    <form method="GET" action="{{ route('bookings.index') }}" id="filterForm">
                        <select name="trainee_id" onchange="document.getElementById('filterForm').submit()" style="background:rgba(255,255,255,0.02);border:1px solid rgba(255,255,255,0.1);border-radius:12px;padding:8px 16px;color:#fff;font-size:.85rem;outline:none;cursor:pointer;">
                            <option value="">All Trainees</option>
                            @foreach($uniqueTrainees as $trainee)
                                <option value="{{ $trainee->id }}" {{ request('trainee_id') == $trainee->id ? 'selected' : '' }}>
                                    {{ $trainee->name }}
                                </option>
                            @endforeach
                        </select>
                    </form>
                @endif

                {{-- Filter Tabs --}}
                <div style="display:flex;gap:4px;background:var(--vg-sidebar);padding:6px;border-radius:12px;border:1px solid var(--vg-border);">
                    <button onclick="switchTab('upcoming')" id="tab-upcoming" class="tab-btn active">Upcoming</button>
                    <button onclick="switchTab('past')" id="tab-past" class="tab-btn">Past</button>
                    <button onclick="switchTab('cancelled')" id="tab-cancelled" class="tab-btn">Cancelled</button>
                </div>
            </div>
        </div>

        @if(session('success'))
            <div style="background:rgba(16,185,129,.1);border-left:4px solid #10b981;color:#10b981;padding:1rem;border-radius:8px;margin-bottom:1.5rem;font-size:.85rem;font-weight:600;">
                {{ session('success') }}
            </div>
        @endif
        @if(session('error'))
            <div style="background:rgba(244,63,94,.1);border-left:4px solid #f43f5e;color:#f43f5e;padding:1rem;border-radius:8px;margin-bottom:1.5rem;font-size:.85rem;font-weight:600;">
                {{ session('error') }}
            </div>
        @endif

        {{-- UPCOMING SESSIONS TAB --}}
        <div id="content-upcoming" style="display:block;" class="fade-in-up">
            @if(isset($upcomingBookings) && $upcomingBookings->count() > 0)
                @if($isTrainer && $upcomingBookings->count() >= 3)
                    {{-- Bulk Actions Bar --}}
                    <div style="display:flex;justify-content:space-between;align-items:center;background:rgba(255,255,255,0.01);border:1px solid rgba(255,255,255,0.04);padding:1rem 1.5rem;border-radius:16px;margin-bottom:1.5rem;">
                        <div style="display:flex;align-items:center;gap:10px;">
                            <input type="checkbox" id="selectAllBookings" style="width:18px;height:18px;cursor:pointer;accent-color:var(--vg-accent);">
                            <span style="font-size:.85rem;color:rgba(255,255,255,0.6);font-weight:600;">Select All</span>
                        </div>
                        <form method="POST" action="{{ route('bookings.bulk-complete') }}" id="bulkCompleteForm">
                            @csrf
                            <input type="hidden" name="booking_ids[]" id="bulkBookingIds">
                            <button type="submit" onclick="return prepareBulkSubmit()" class="action-btn primary" style="font-size:.75rem;padding:6px 14px;">
                                <i data-lucide="check-square" style="width:14px;height:14px;"></i> Mark Completed
                            </button>
                        </form>
                    </div>
                @endif

                @foreach($upcomingBookings as $booking)
                    @php
                        $sessionTime = strtotime($booking->session_date);
                        $duration = (int) ($booking->duration_minutes ?? 60);
                        $now = time();
                        $activeStart = $sessionTime - 900; // 15 mins before
                        $activeEnd = $sessionTime + ($duration * 60); // end of session
                        
                        $canJoin = ($now >= $activeStart && $now <= $activeEnd);
                        $isOverdue = ($now > $activeEnd);
                        $isActive = $canJoin;
                        
                        $partner = $isTrainer ? ($booking->trainee ?? null) : ($booking->trainer ?? null);
                        $partnerName = $partner ? $partner->name : ($isTrainer ? 'Trainee' : 'Trainer');
                        $initial = substr($partnerName, 0, 1);
                        $avatarColor = (crc32($booking->id ?? '1') % 2 == 0) ? '#8b5cf6' : '#10b981';
                    @endphp
                    <div class="booking-card {{ $isActive ? 'booking-card-active' : '' }}" id="booking-{{ $booking->id }}" data-duration="{{ $booking->duration_minutes ?? 60 }}">
                        <div style="display:flex;gap:1.5rem;align-items:flex-start;">
                            
                            @if($isTrainer && $upcomingBookings->count() > 1)
                                <div style="padding-top:14px;">
                                    <input type="checkbox" class="booking-selector" value="{{ $booking->id }}" style="width:18px;height:18px;cursor:pointer;accent-color:var(--vg-accent);">
                                </div>
                            @endif

                            <div style="flex:1;display:flex;flex-direction:column;gap:1.5rem;">
                                
                                {{-- Top Section: Trainee Details & Badges --}}
                                <div style="display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:1rem;border-bottom:1px solid rgba(255,255,255,0.03);padding-bottom:1rem;">
                                    <div style="display:flex;gap:1rem;align-items:center;">
                                        <div style="width:48px;height:48px;border-radius:50%;background:{{ $avatarColor }};display:flex;align-items:center;justify-content:center;font-size:1.25rem;font-weight:700;color:#fff;flex-shrink:0;">
                                            {{ $initial }}
                                        </div>
                                        <div>
                                            <div style="display:flex;align-items:center;gap:10px;flex-wrap:wrap;">
                                                <h3 style="font-size:1.15rem;font-weight:700;color:var(--vg-text-strong);margin:0;">{{ $partnerName }}</h3>
                                                
                                                {{-- Confirmed Badge --}}
                                                <span style="font-size:.65rem;background:rgba(16,185,129,.15);color:#10b981;border:1px solid rgba(16,185,129,.3);padding:2px 10px;border-radius:50px;font-weight:700;text-transform:uppercase;letter-spacing:.02em;">
                                                    <i data-lucide="check-circle" style="width:10px;height:10px;display:inline-block;margin-bottom:-1px;"></i> Confirmed
                                                </span>
                                                
                                                {{-- Session Type Tag --}}
                                                <span style="font-size:.65rem;background:rgba(139,92,246,.15);color:#c4b5fd;border:1px solid rgba(139,92,246,.3);padding:2px 10px;border-radius:50px;font-weight:700;text-transform:uppercase;letter-spacing:.02em;">
                                                    {{ $booking->session_type ?? 'Strength' }}
                                                </span>
                                            </div>

                                            @if(!$isTrainer)
                                                <div style="display:flex;align-items:center;gap:6px;font-size:0.75rem;color:rgba(255,255,255,0.45);margin-top:2px;">
                                                    <span style="color:#fbbf24;display:flex;align-items:center;gap:2px;font-weight:700;"><i data-lucide="star" style="width:12px;height:12px;fill:#fbbf24;stroke:none;"></i> 4.9</span>
                                                    <span style="opacity:0.3;">•</span>
                                                    <span style="font-weight:600;">{{ $booking->session_type ?? 'Strength' }} Specialist</span>
                                                    <span style="opacity:0.3;">•</span>
                                                    <span>5 yrs exp</span>
                                                </div>
                                            @endif
                                            
                                            <div style="display:flex;gap:12px;margin-top:6px;align-items:center;">
                                                <button onclick="viewTraineeProfile('{{ $partnerName }}', '{{ $partner->email ?? '' }}', '{{ $booking->session_type ?? 'Strength' }}')" style="background:none;border:none;color:var(--vg-accent);font-size:.75rem;font-weight:600;padding:0;cursor:pointer;text-decoration:underline;">View Profile</button>
                                                <span style="color:rgba(255,255,255,0.15);">|</span>
                                                <a href="{{ route('chat.index', $partner->id ?? '') }}" style="color:rgba(255,255,255,0.4);font-size:.75rem;font-weight:600;text-decoration:none;display:inline-flex;align-items:center;gap:4px;" onmouseover="this.style.color='#fff'" onmouseout="this.style.color='rgba(255,255,255,0.4)'">
                                                    <i data-lucide="message-square" style="width:12px;height:12px;"></i> Chat Shortcut
                                                </a>
                                                
                                                @if($isTrainer && isset($pastBookings))
                                                    @php
                                                        $lastSession = $pastBookings->where('trainee_id', $booking->trainee_id)->where('status', 'completed')->first();
                                                    @endphp
                                                    @if($lastSession)
                                                        <span style="color:rgba(255,255,255,0.15);">|</span>
                                                        <span style="font-size:.7rem;color:rgba(255,255,255,0.5);font-style:italic;">
                                                            Last session: {{ \Carbon\Carbon::parse($lastSession->session_date)->format('M d') }}
                                                        </span>
                                                    @endif
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                    
                                    {{-- Countdown Badge --}}
                                    @php
                                        $duration = (int) ($booking->duration_minutes ?? 60);
                                        $sessionIso = \Carbon\Carbon::parse($booking->session_date)->toIso8601String();
                                        $d = \Carbon\Carbon::parse($booking->session_date);
                                        $nowDate = now();
                                        
                                        $activeStart = $d->copy()->subMinutes(15);
                                        $activeEnd = $d->copy()->addMinutes($duration);
                                        
                                        $isActive = $nowDate->greaterThanOrEqualTo($activeStart) && $nowDate->lessThanOrEqualTo($activeEnd);
                                        $isPast = $nowDate->greaterThan($activeEnd);
                                        
                                        if ($isActive) {
                                            $countdown = 'Active Now';
                                        } elseif ($isPast) {
                                            $countdown = 'Session Overdue';
                                        } else {
                                            $totalMinutes = $nowDate->diffInMinutes($d);
                                            $days = intdiv($totalMinutes, 1440);
                                            $remainingMinutes = $totalMinutes % 1440;
                                            $hours = intdiv($remainingMinutes, 60);
                                            $minutes = $remainingMinutes % 60;
                                            
                                            $countdown = 'Starts in ';
                                            if ($days > 0) {
                                                $countdown .= $days . 'd';
                                                if ($hours > 0) {
                                                    $countdown .= ' ' . $hours . 'h';
                                                }
                                            } else {
                                                $countdown .= $hours . 'h ' . $minutes . 'm';
                                            }
                                        }
                                    @endphp
                                    <div class="countdown-badge" data-datetime="{{ $sessionIso }}" style="{{ $isActive ? 'background:rgba(16, 185, 129, 0.1); border-color:rgba(16, 185, 129, 0.3); color:#10b981;' : ($isPast ? 'background:rgba(239, 68, 68, 0.1); border-color:rgba(239, 68, 68, 0.3); color:#ef4444;' : '') }}">
                                        @if($isActive)
                                            <span class="pulse-indicator" style="display:inline-block;width:6px;height:6px;border-radius:50%;background:#10b981;margin-right:4px;"></span>
                                        @else
                                            <i data-lucide="bell" style="width:12px;height:12px;"></i>
                                        @endif
                                        <span class="countdown-text">{{ $countdown }}</span>
                                    </div>
                                </div>

                                {{-- Session Status Progress Timeline --}}
                                @php
                                    $progressWidth = '33%';
                                    $step1 = 'completed'; // Booked
                                    $step2 = 'completed'; // Confirmed
                                    $step3 = '';          // Upcoming
                                    $step4 = '';          // Completed
                                    
                                    if ($isActive) {
                                        $progressWidth = '66%';
                                        $step3 = 'active';
                                    } elseif ($isPast) {
                                        $progressWidth = '100%';
                                        $step3 = 'completed';
                                        $step4 = 'completed';
                                    } else {
                                        $step3 = 'active';
                                        $progressWidth = '66%';
                                    }
                                @endphp
                                <div class="progress-timeline" style="margin: 0.8rem 0 1.2rem 0;">
                                    <div class="progress-line-fill" style="width: {{ $progressWidth }};"></div>
                                    <div class="progress-step completed">
                                        <div class="progress-step-dot"><i data-lucide="calendar" style="width:11px;height:11px;color:white;"></i></div>
                                        <span>Booked</span>
                                    </div>
                                    <div class="progress-step completed">
                                        <div class="progress-step-dot"><i data-lucide="check" style="width:11px;height:11px;color:white;"></i></div>
                                        <span>Confirmed</span>
                                    </div>
                                    <div class="progress-step {{ $step3 }}">
                                        <div class="progress-step-dot"><i data-lucide="clock" style="width:11px;height:11px;color:white;"></i></div>
                                        <span>Upcoming</span>
                                    </div>
                                    <div class="progress-step {{ $step4 }}">
                                        <div class="progress-step-dot"><i data-lucide="award" style="width:11px;height:11px;color:white;"></i></div>
                                        <span>Completed</span>
                                    </div>
                                </div>

                                {{-- Middle Section: Booking Details --}}
                                <div style="display:grid;grid-template-columns:repeat(auto-fit, minmax(180px, 1fr));gap:1.5rem;">
                                    <div>
                                        <p style="font-size:.7rem;color:var(--vg-text-muted);text-transform:uppercase;letter-spacing:.05em;margin-bottom:4px;">Date & Time</p>
                                        <p style="font-size:.9rem;color:var(--vg-text-strong);font-weight:700;">{{ \Carbon\Carbon::parse($booking->session_date)->format('M d, Y • h:i A') }}</p>
                                    </div>
                                    <div>
                                        <p style="font-size:.7rem;color:var(--vg-text-muted);text-transform:uppercase;letter-spacing:.05em;margin-bottom:4px;">Duration</p>
                                        <p style="font-size:.9rem;color:var(--vg-text-strong);font-weight:700;">{{ $booking->duration_minutes }} min</p>
                                    </div>
                                    <div>
                                        <p style="font-size:.7rem;color:var(--vg-text-muted);text-transform:uppercase;letter-spacing:.05em;margin-bottom:4px;">Session Cost</p>
                                        <p style="font-size:.9rem;color:#10b981;font-weight:700;">₹{{ number_format($booking->amount) }}</p>
                                    </div>
                                </div>

                                {{-- Divider line --}}
                                @if($isTrainer)
                                    <hr style="border:none;border-top:1px solid rgba(255,255,255,0.08);margin:0.5rem 0 0 0;">
                                @endif

                                {{-- Notes Area (Trainer Only) --}}
                                @if($isTrainer)
                                    <div style="background:rgba(255,255,255,0.01);border:1px solid rgba(255,255,255,0.04);border-radius:16px;padding:1.2rem;">
                                        <form method="POST" action="{{ route('bookings.save-notes', $booking->id) }}">
                                            @csrf
                                            
                                            {{-- Session Type selection row --}}
                                            <div style="margin-bottom:1.2rem;display:flex;flex-direction:column;gap:6px;">
                                                <label style="font-size:.75rem;color:rgba(255,255,255,0.4);font-weight:700;text-transform:uppercase;">Session Type</label>
                                                <select name="session_type" style="width:100%;background:rgba(0,0,0,0.3);border:1px solid rgba(255,255,255,0.08);border-radius:10px;padding:10px 12px;color:#fff;font-size:.85rem;outline:none;cursor:pointer;">
                                                    <option value="Strength" {{ ($booking->session_type ?? '') == 'Strength' ? 'selected' : '' }}>Strength & Conditioning</option>
                                                    <option value="Cardio" {{ ($booking->session_type ?? '') == 'Cardio' ? 'selected' : '' }}>Cardio / HIIT</option>
                                                    <option value="Flexibility" {{ ($booking->session_type ?? '') == 'Flexibility' ? 'selected' : '' }}>Yoga / Flexibility</option>
                                                    <option value="General" {{ ($booking->session_type ?? '') == 'General' ? 'selected' : '' }}>General Fitness</option>
                                                </select>
                                            </div>

                                            {{-- Notes row --}}
                                            <div style="margin-bottom:1rem;display:flex;flex-direction:column;gap:6px;">
                                                <label style="font-size:.75rem;color:rgba(255,255,255,0.4);font-weight:700;text-transform:uppercase;display:flex;align-items:center;gap:6px;">
                                                    <i data-lucide="clipboard-list" style="width:14px;height:14px;color:var(--vg-accent);"></i> Trainer's Private Notes
                                                </label>
                                                <textarea name="notes" placeholder="Write session focus, client progress, injuries or goals here..." rows="3" style="width:100%;background:rgba(0,0,0,0.2);border:1px solid rgba(255,255,255,0.08);border-radius:10px;padding:8px 12px;color:#fff;font-family:inherit;font-size:.8rem;outline:none;resize:vertical;">{{ $booking->trainer_notes }}</textarea>
                                            </div>

                                            <div style="display:flex;justify-content:flex-start;">
                                                <button type="submit" class="action-btn primary" style="font-size:.75rem;padding:6px 14px;">Save Session Details</button>
                                            </div>
                                        </form>
                                    </div>
                                @endif

                                {{-- Action Section --}}
                                <div style="display:flex;justify-content:flex-end;align-items:center;flex-wrap:wrap;gap:8px;margin-top:0.5rem;padding-top:1.2rem;border-top:1px solid rgba(255,255,255,0.05);">
                                    @if($canJoin)
                                        @if($isTrainer)
                                            {{-- Trainer: always show Start/Join button to initiate the session --}}
                                            <a href="{{ route('video-call.join', $booking->id) }}" class="action-btn primary" style="display:inline-flex;align-items:center;gap:6px;">
                                                🎥 {{ $booking->meeting_started ? 'Join Video Session' : 'Start Session' }}
                                            </a>
                                        @elseif($booking->meeting_started)
                                            {{-- Trainee: only show Join button after trainer has started --}}
                                            <a href="{{ route('video-call.join', $booking->id) }}" class="action-btn primary" style="display:inline-flex;align-items:center;gap:6px;">
                                                🎥 Join Video Session
                                            </a>
                                        @else
                                            {{-- Trainee: trainer hasn't started yet, show waiting state --}}
                                            <button disabled class="action-btn" style="opacity:0.85;cursor:not-allowed;background:rgba(251,191,36,0.08);border:1px solid rgba(251,191,36,0.25);color:#fbbf24;display:inline-flex;align-items:center;gap:8px;animation:none;">
                                                <span style="display:inline-block;width:8px;height:8px;border-radius:50%;background:#fbbf24;animation:pulse 1.5s infinite;flex-shrink:0;"></span>
                                                Waiting for Trainer to Start...
                                            </button>
                                        @endif
                                    @elseif($isOverdue)
                                        <button disabled class="action-btn" style="opacity:0.5;cursor:not-allowed;background:rgba(255,255,255,0.02);color:var(--vg-text-muted);display:inline-flex;align-items:center;gap:6px;">
                                            🎥 Video Call Ended
                                        </button>
                                    @else
                                        <button disabled class="action-btn" style="opacity:0.6;cursor:not-allowed;background:rgba(255,255,255,0.02);display:inline-flex;align-items:center;gap:6px;">
                                            🎥 Video Call (opens 15 min before)
                                        </button>
                                    @endif

                                    @if(!$isTrainer)
                                        <button onclick="openRescheduleModal('{{ $booking->id }}')" class="action-btn" style="background:transparent;border:1px solid rgba(255,255,255,0.08);color:rgba(255,255,255,0.6);" onmouseover="this.style.borderColor='var(--vg-accent)';this.style.color='#fff';" onmouseout="this.style.borderColor='rgba(255,255,255,0.08)';this.style.color='rgba(255,255,255,0.6)';">
                                            <i data-lucide="calendar-range" style="width:13px;height:13px;"></i> Reschedule
                                        </button>
                                        <form method="POST" action="{{ route('bookings.update', $booking->id) }}" onsubmit="return confirm('Are you sure you want to cancel this booking?')" style="margin:0;">
                                            @csrf
                                            @method('PUT')
                                            <input type="hidden" name="status" value="cancelled">
                                            <input type="hidden" name="cancellation_reason" value="Cancelled by Trainee">
                                            <button type="submit" class="action-btn danger" style="background:transparent;border:1px solid rgba(244,63,94,0.15);color:#f43f5e;" onmouseover="this.style.background='rgba(244,63,94,0.1)';" onmouseout="this.style.background='transparent';">
                                                <i data-lucide="trash-2" style="width:13px;height:13px;"></i> Cancel
                                            </button>
                                        </form>
                                        <button onclick="alert('Session added to calendar reminder!')" class="action-btn" style="background:transparent;border:1px solid rgba(255,255,255,0.08);color:rgba(255,255,255,0.6);" onmouseover="this.style.borderColor='rgba(255,255,255,0.2)';this.style.color='#fff';" onmouseout="this.style.borderColor='rgba(255,255,255,0.08)';this.style.color='rgba(255,255,255,0.6)';">
                                            <i data-lucide="calendar-plus" style="width:13px;height:13px;"></i> Add Reminder
                                        </button>
                                    @endif

                                    @if($isTrainer)
                                        @if(\Carbon\Carbon::parse($booking->session_date)->isFuture())
                                            <button disabled class="action-btn" style="opacity:0.5;cursor:not-allowed;border-color:rgba(255,255,255,0.08);color:var(--vg-text-muted);" title="You cannot mark a future session as completed.">Mark Complete</button>
                                        @else
                                            <form method="POST" action="{{ route('bookings.update', $booking->id) }}" style="margin:0;">
                                                @csrf
                                                @method('PUT')
                                                <input type="hidden" name="status" value="completed">
                                                <button type="submit" class="action-btn" style="border-color:#10b981;color:#10b981;">Mark Complete</button>
                                            </form>
                                        @endif
                                    @endif

                                    @if($isTrainer)
                                        <button onclick="openRescheduleModal('{{ $booking->id }}')" class="action-btn">Reschedule</button>
                                        <form method="POST" action="{{ route('bookings.update', $booking->id) }}" onsubmit="return confirmTrainerCancellation(this)" style="margin:0;">
                                            @csrf
                                            @method('PUT')
                                            <input type="hidden" name="status" value="cancelled">
                                            <input type="hidden" name="cancellation_reason" value="">
                                            <button type="submit" class="action-btn danger">Cancel</button>
                                        </form>
                                    @endif
                                </div>

                            </div>
                        </div>
                    </div>
                @endforeach

                {{-- Session Preparation Guidelines (Trainee only, when there are upcoming sessions) --}}
                @if(!$isTrainer)
                    <div style="background:linear-gradient(135deg, rgba(139,92,246,0.02) 0%, rgba(255,255,255,0.01) 100%);border:1px solid rgba(255,255,255,0.04);border-radius:24px;padding:2rem;margin-top:2.5rem;box-shadow:0 8px 30px rgba(0,0,0,0.12);" class="fade-in-up">
                        <div style="display:flex;align-items:center;gap:10px;margin-bottom:1.5rem;border-bottom:1px solid rgba(255,255,255,0.04);padding-bottom:0.8rem;">
                            <div style="width:36px;height:36px;border-radius:10px;background:rgba(139,92,246,0.1);display:flex;align-items:center;justify-content:center;color:var(--vg-accent);">
                                <i data-lucide="clipboard-list" style="width:20px;height:20px;"></i>
                            </div>
                            <div>
                                <h3 style="font-size:1.1rem;font-weight:800;color:var(--vg-text-strong);margin:0;">📋 Upcoming Session Preparation Guide</h3>
                                <p style="font-size:0.75rem;color:var(--vg-text-muted);margin:2px 0 0 0;">Follow these steps to make the most out of your live AI training session</p>
                            </div>
                        </div>
                        
                        <div style="display:grid;grid-template-columns:repeat(auto-fit, minmax(220px, 1fr));gap:1.5rem;">
                            <div style="background:rgba(255,255,255,0.01);border:1px solid rgba(255,255,255,0.03);border-radius:16px;padding:1.2rem;transition:all 0.3s ease;" onmouseover="this.style.borderColor='rgba(139, 92, 246, 0.2)';this.style.background='rgba(255,255,255,0.02)';" onmouseout="this.style.borderColor='rgba(255,255,255,0.03)';this.style.background='rgba(255,255,255,0.01)';">
                                <div style="display:flex;align-items:center;gap:8px;margin-bottom:8px;">
                                    <i data-lucide="droplet" style="width:16px;height:16px;color:#3b82f6;"></i>
                                    <h4 style="font-size:0.88rem;font-weight:700;color:var(--vg-text-strong);margin:0;">1. Hydrate Properly</h4>
                                </div>
                                <p style="font-size:0.78rem;color:var(--vg-text-muted);line-height:1.45;margin:0;">Drink 300-500ml of water 30-45 minutes before starting. Avoid heavy meals 2 hours prior to your training.</p>
                            </div>
                            
                            <div style="background:rgba(255,255,255,0.01);border:1px solid rgba(255,255,255,0.03);border-radius:16px;padding:1.2rem;transition:all 0.3s ease;" onmouseover="this.style.borderColor='rgba(139, 92, 246, 0.2)';this.style.background='rgba(255,255,255,0.02)';" onmouseout="this.style.borderColor='rgba(255,255,255,0.03)';this.style.background='rgba(255,255,255,0.01)';">
                                <div style="display:flex;align-items:center;gap:8px;margin-bottom:8px;">
                                    <i data-lucide="dumbbell" style="width:16px;height:16px;color:var(--vg-accent);"></i>
                                    <h4 style="font-size:0.88rem;font-weight:700;color:var(--vg-text-strong);margin:0;">2. Prepare Equipment</h4>
                                </div>
                                <p style="font-size:0.78rem;color:var(--vg-text-muted);line-height:1.45;margin:0;">Lay out your exercise mat. Keep your resistance bands, water bottle, and a sweat towel within arm's reach.</p>
                            </div>
                            
                            <div style="background:rgba(255,255,255,0.01);border:1px solid rgba(255,255,255,0.03);border-radius:16px;padding:1.2rem;transition:all 0.3s ease;" onmouseover="this.style.borderColor='rgba(139, 92, 246, 0.2)';this.style.background='rgba(255,255,255,0.02)';" onmouseout="this.style.borderColor='rgba(255,255,255,0.03)';this.style.background='rgba(255,255,255,0.01)';">
                                <div style="display:flex;align-items:center;gap:8px;margin-bottom:8px;">
                                    <i data-lucide="heart" style="width:16px;height:16px;color:#ef4444;"></i>
                                    <h4 style="font-size:0.88rem;font-weight:700;color:var(--vg-text-strong);margin:0;">3. 5-Min Warm-up</h4>
                                </div>
                                <p style="font-size:0.78rem;color:var(--vg-text-muted);line-height:1.45;margin:0;">Perform dynamic stretches like arm circles, body squats, and high knees to raise heart rate and prep joints.</p>
                            </div>
                            
                            <div style="background:rgba(255,255,255,0.01);border:1px solid rgba(255,255,255,0.03);border-radius:16px;padding:1.2rem;transition:all 0.3s ease;" onmouseover="this.style.borderColor='rgba(139, 92, 246, 0.2)';this.style.background='rgba(255,255,255,0.02)';" onmouseout="this.style.borderColor='rgba(255,255,255,0.03)';this.style.background='rgba(255,255,255,0.01)';">
                                <div style="display:flex;align-items:center;gap:8px;margin-bottom:8px;">
                                    <i data-lucide="monitor-play" style="width:16px;height:16px;color:#10b981;"></i>
                                    <h4 style="font-size:0.88rem;font-weight:700;color:var(--vg-text-strong);margin:0;">4. Connect 5 Mins Early</h4>
                                </div>
                                <p style="font-size:0.78rem;color:var(--vg-text-muted);line-height:1.45;margin:0;">Join the call early to test your camera placement. Ensure your full body is visible to optimize AI posture tracking.</p>
                            </div>
                        </div>
                    </div>
                @endif
            @else
                @if($isTrainer)
                    <div class="empty-state-container">
                        <div style="font-size:2.5rem;margin-bottom:.8rem;opacity:.5;">📅</div>
                        <h3 style="color:var(--vg-text-strong);font-size:1.1rem;font-weight:700;margin-bottom:.4rem;">No upcoming sessions</h3>
                        <p style="color:var(--vg-text-muted);font-size:.85rem;margin-bottom:1.2rem;">You have no confirmed bookings right now.</p>
                    </div>
                @else
                    <div class="empty-state-container" style="background:var(--vg-panel);border:1px solid var(--vg-border);border-radius:24px;padding:2.5rem 2rem;text-align:center;margin-bottom:2rem;">
                        <div style="font-size:3rem;margin-bottom:1rem;opacity:.8;">📅</div>
                        <h3 style="color:var(--vg-text-strong);font-size:1.2rem;font-weight:800;margin-bottom:.5rem;">No upcoming sessions</h3>
                        <p style="color:var(--vg-text-muted);font-size:.9rem;margin-bottom:2rem;max-width:400px;margin-left:auto;margin-right:auto;">
                            Ready to crush your goals? Find a trainer and schedule your next workout.
                        </p>
                        
                        <div style="display:flex;gap:1rem;justify-content:center;margin-bottom:2.5rem;flex-wrap:wrap;">
                            <a href="{{ route('trainee.trainers') }}" style="display:inline-flex;align-items:center;gap:8px;background:linear-gradient(135deg,#8b5cf6,#ec4899);color:#fff;padding:10px 24px;border-radius:12px;font-size:.85rem;font-weight:700;text-decoration:none;box-shadow:0 4px 15px rgba(139,92,246,.3);transition:all .2s;" onmouseover="this.style.boxShadow='0 6px 20px rgba(139,92,246,.5)';this.style.transform='translateY(-1px)'" onmouseout="this.style.boxShadow='0 4px 15px rgba(139,92,246,.3)';this.style.transform=''">
                                <i data-lucide="search" style="width:16px;height:16px;"></i> Browse Trainers
                            </a>
                            
                            @if(isset($pastBookings) && $pastBookings->count() > 0)
                                @php
                                    $lastBooking = $pastBookings->first();
                                    $lastTrainer = $lastBooking ? ($lastBooking->trainer ?? null) : null;
                                @endphp
                                @if($lastTrainer)
                                    <a href="{{ route('book.trainer.create', $lastTrainer->id) }}" style="display:inline-flex;align-items:center;gap:8px;background:rgba(255,255,255,.05);border:1px solid rgba(255,255,255,.1);color:var(--vg-text-strong);padding:10px 24px;border-radius:12px;font-size:.85rem;font-weight:700;text-decoration:none;transition:all .2s;" onmouseover="this.style.background='rgba(255,255,255,.1)';this.style.transform='translateY(-1px)'" onmouseout="this.style.background='rgba(255,255,255,.05)';this.style.transform=''">
                                        <i data-lucide="refresh-cw" style="width:16px;height:16px;color:#c4b5fd;"></i> Book again with {{ explode(' ', $lastTrainer->name)[0] }}
                                    </a>
                                @endif
                            @endif
                        </div>

                        @if(isset($pastBookings) && $pastBookings->count() > 0)
                            <div style="border-top:1px solid var(--vg-border);padding-top:2rem;text-align:left;">
                                <h4 style="font-size:.85rem;font-weight:800;color:var(--vg-text-muted);text-transform:uppercase;letter-spacing:.05em;margin-bottom:1rem;display:flex;align-items:center;gap:6px;">
                                    <i data-lucide="history" style="width:14px;height:14px;color:var(--vg-accent);"></i> Recent Session Preview
                                </h4>
                                
                                <div style="display:flex;flex-direction:column;gap:10px;">
                                    @foreach($pastBookings->take(2) as $recent)
                                        @php
                                            $trainer = $recent->trainer ?? null;
                                            $trainerName = $trainer ? $trainer->name : 'Trainer';
                                        @endphp
                                        <div style="background:rgba(255,255,255,.02);border:1px solid var(--vg-border);border-radius:14px;padding:1rem 1.2rem;display:flex;justify-content:space-between;align-items:center;transition:all .2s;" onmouseover="this.style.background='rgba(255,255,255,.04)'" onmouseout="this.style.background='rgba(255,255,255,.02)'">
                                            <div>
                                                <div style="display:flex;align-items:center;gap:8px;margin-bottom:3px;">
                                                    <span style="font-size:.9rem;font-weight:700;color:var(--vg-text-strong);">{{ $trainerName }}</span>
                                                    <span style="font-size:.65rem;background:rgba(139,92,246,.15);color:#c4b5fd;padding:2px 6px;border-radius:4px;font-weight:700;">{{ $recent->session_type ?? 'Strength' }}</span>
                                                </div>
                                                <p style="font-size:.78rem;color:var(--vg-text-muted);margin:0;">
                                                    {{ \Carbon\Carbon::parse($recent->session_date)->format('M d, Y') }} • {{ $recent->duration_minutes }} min • Completed
                                                </p>
                                            </div>
                                            @if($trainer)
                                                <a href="{{ route('book.trainer.create', $trainer->id) }}" style="background:var(--vg-sidebar);border:1px solid var(--vg-border-strong);color:var(--vg-text-strong);padding:6px 14px;border-radius:8px;font-size:.78rem;font-weight:700;text-decoration:none;transition:all .2s;" onmouseover="this.style.background='var(--vg-panel-strong)';this.style.borderColor='rgba(139,92,246,.4)'" onmouseout="this.style.background='var(--vg-sidebar)';this.style.borderColor='var(--vg-border-strong)'">
                                                    Book Again
                                                </a>
                                            @endif
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    </div>
                @endif
            @endif
        </div>

        {{-- PAST SESSIONS TAB --}}
        <div id="content-past" style="display:none;" class="fade-in-up">
            @if(isset($pastBookings) && $pastBookings->count() > 0)
                <div style="display:flex;flex-direction:column;">
                    @foreach($pastBookings as $booking)
                        @php
                            $partner = $isTrainer ? ($booking->trainee ?? null) : ($booking->trainer ?? null);
                            $partnerName = $partner ? $partner->name : ($isTrainer ? 'Trainee' : 'Trainer');
                        @endphp
                        <div class="past-session-item">
                            <div>
                                <div style="display:flex;align-items:center;gap:8px;margin-bottom:2px;">
                                    <p style="font-size:.95rem;font-weight:700;color:var(--vg-text-strong);">{{ $partnerName }}</p>
                                    <span style="font-size:.6rem;background:rgba(255,255,255,0.05);color:rgba(255,255,255,0.6);padding:2px 8px;border-radius:50px;font-weight:600;text-transform:uppercase;">
                                        {{ $booking->session_type ?? 'Strength' }}
                                    </span>
                                </div>
                                <p style="font-size:.75rem;color:var(--vg-text-muted);">{{ \Carbon\Carbon::parse($booking->session_date)->format('M d, Y') }} • {{ $booking->duration_minutes }} min</p>
                            </div>
                            
                            <div style="display:flex;gap:1.5rem;align-items:center;">
                                <div style="text-align:right;">
                                    <p style="font-size:.85rem;color:var(--vg-text-strong);font-weight:600;">₹{{ number_format($booking->amount) }}</p>
                                    <p style="font-size:.7rem;color:{{ $booking->status === 'no_show' ? '#ef4444' : '#10b981' }};font-weight:600;">
                                        {{ $booking->status === 'no_show' ? 'No Show' : ucfirst($booking->status) }}
                                    </p>
                                </div>
                                @if(!$isTrainer && $partner)
                                    <a href="{{ route('book.trainer.create', $partner->id) }}" class="past-action-btn" style="background:var(--vg-sidebar);border:1px solid var(--vg-border-strong);color:var(--vg-text-strong);padding:6px 12px;border-radius:6px;font-size:.75rem;font-weight:600;text-decoration:none;transition:all .2s;" onmouseover="this.style.background='var(--vg-panel-strong)'" onmouseout="this.style.background='var(--vg-sidebar)'">Book Again</a>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="empty-state-card">
                    <div class="empty-state-icon-wrapper">
                        <i data-lucide="history" style="width:28px;height:28px;"></i>
                    </div>
                    <h3 style="color:var(--vg-text-strong);font-size:1.1rem;font-weight:800;margin:0;">No past sessions found</h3>
                    <p style="color:var(--vg-text-muted);font-size:.85rem;margin:0;max-width:320px;">You haven't completed any training sessions yet. Complete your first session to view your history!</p>
                </div>
            @endif
        </div>

        {{-- CANCELLED SESSIONS TAB --}}
        <div id="content-cancelled" style="display:none;" class="fade-in-up">
            @if(isset($cancelledBookings) && $cancelledBookings->count() > 0)
                <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(300px,1fr));gap:1rem;">
                    @foreach($cancelledBookings as $booking)
                        @php
                            $partner = $isTrainer ? ($booking->trainee ?? null) : ($booking->trainer ?? null);
                            $partnerName = $partner ? $partner->name : ($isTrainer ? 'Trainee' : 'Trainer');
                        @endphp
                        <div style="background:var(--vg-panel);border:1px solid rgba(244,63,94,.2);border-radius:16px;padding:1.2rem;position:relative;">
                            <span style="position:absolute;top:1rem;right:1rem;font-size:.7rem;color:#f43f5e;background:rgba(244,63,94,.1);padding:2px 8px;border-radius:4px;font-weight:700;">Cancelled</span>
                            <h3 style="font-size:1rem;font-weight:700;color:var(--vg-text-strong);margin-bottom:4px;">{{ $partnerName }}</h3>
                            <p style="font-size:.8rem;color:var(--vg-text-muted);margin-bottom:12px;">{{ \Carbon\Carbon::parse($booking->session_date)->format('M d, Y • h:i A') }}</p>
                            @if($booking->cancellation_reason)
                                <div style="background:rgba(255,255,255,.035);border:1px solid rgba(255,255,255,.07);border-radius:12px;padding:.8rem;margin-bottom:12px;">
                                    <p style="font-size:.68rem;color:var(--vg-text-muted);text-transform:uppercase;letter-spacing:.05em;font-weight:800;margin-bottom:4px;">Cancellation Reason</p>
                                    <p style="font-size:.82rem;color:var(--vg-text-strong);line-height:1.45;">{{ $booking->cancellation_reason }}</p>
                                </div>
                            @endif
                            @if(!$isTrainer && ($booking->refund_status ?? null))
                                <p style="font-size:.75rem;color:var(--vg-text-muted);margin-bottom:12px;">
                                    Refund status:
                                    <span style="color:{{ ($booking->refund_status ?? '') === 'processed' ? '#10b981' : '#fbbf24' }};font-weight:700;">
                                        {{ str_replace('_', ' ', ucfirst($booking->refund_status)) }}
                                    </span>
                                </p>
                            @endif
                            @if(!$isTrainer && $partner)
                                <a href="{{ route('book.trainer.create', $partner->id) }}" style="display:inline-block;font-size:.75rem;color:var(--vg-accent);font-weight:600;text-decoration:none;">Rebook Session →</a>
                            @endif
                        </div>
                    @endforeach
                </div>
            @else
                <div class="empty-state-card">
                    <div class="empty-state-icon-wrapper" style="background:rgba(244,63,94,0.08); border-color:rgba(244,63,94,0.15); color:#f43f5e;">
                        <i data-lucide="calendar-x" style="width:28px;height:28px;"></i>
                    </div>
                    <h3 style="color:var(--vg-text-strong);font-size:1.1rem;font-weight:800;margin:0;">No cancelled sessions</h3>
                    <p style="color:var(--vg-text-muted);font-size:.85rem;margin:0;max-width:320px;">Great! You have no cancelled bookings. All scheduled sessions are on track.</p>
                </div>
            @endif
        </div>

    </div>

    {{-- Right Sidebar --}}
    <div class="summary-panel" style="width:340px;flex-shrink:0;">
        @if(!$isTrainer)
            <div style="background:var(--vg-panel);border:1px solid var(--vg-border);border-radius:24px;padding:1.5rem;position:sticky;top:2rem;box-shadow:0 12px 40px rgba(0,0,0,0.15);">
                <h2 style="font-size:1rem;font-weight:800;color:var(--vg-text-strong);margin-bottom:1.2rem;display:flex;align-items:center;gap:8px;">
                    <i data-lucide="line-chart" style="width:16px;height:16px;color:var(--vg-accent);"></i> Dashboard Analytics
                </h2>
                
                {{-- Spent Money Metric: Primary Aesthetic Anchor --}}
                <div style="background:linear-gradient(135deg, rgba(16,185,129,0.08) 0%, rgba(139,92,246,0.02) 100%); border:1px solid rgba(16,185,129,0.18); border-radius:18px; padding:1.2rem; margin-bottom:1rem; position:relative; overflow:hidden; box-shadow:0 8px 24px rgba(16,185,129,0.04);">
                    <div style="position:absolute;top:-10px;right:-10px;width:60px;height:60px;background:rgba(16,185,129,0.1);filter:blur(20px);border-radius:50%;"></div>
                    <div style="display:flex;align-items:center;gap:6px;margin-bottom:4px;">
                        <i data-lucide="wallet" style="width:14px;height:14px;color:#10b981;"></i>
                        <span style="font-size:.65rem;color:var(--vg-text-muted);text-transform:uppercase;letter-spacing:.05em;font-weight:700;">Total Spent (This Month)</span>
                    </div>
                    <p style="font-size:2.2rem;font-weight:900;color:#10b981;line-height:1;margin:4px 0;">₹{{ number_format($totalSpentThisMonth) }}</p>
                    <p style="font-size:.68rem;color:rgba(255,255,255,0.4);margin:0;display:flex;align-items:center;gap:4px;">
                        <span style="display:inline-block;width:5px;height:5px;background:#10b981;border-radius:50%;"></span> Current Month Focus
                    </p>
                </div>

                {{-- Compact Side-by-Side Statistics --}}
                @php
                    $lifetimeSessions = \App\Models\Booking::where('trainee_id', Auth::id())->where('status', 'completed')->count();
                @endphp
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:0.75rem;margin-bottom:1.2rem;">
                    <div style="background:rgba(255,255,255,0.015); border:1px solid rgba(255,255,255,0.04); border-radius:14px; padding:0.8rem; text-align:left;">
                        <p style="font-size:.62rem;color:var(--vg-text-muted);text-transform:uppercase;letter-spacing:.02em;margin:0 0 4px 0;line-height:1.2;">Month Completed</p>
                        <p style="font-size:1.4rem;font-weight:800;color:var(--vg-text-strong);margin:0;line-height:1;">{{ $totalSessionsCompleted > 0 ? $totalSessionsCompleted : '0' }}</p>
                    </div>
                    <div style="background:rgba(255,255,255,0.015); border:1px solid rgba(255,255,255,0.04); border-radius:14px; padding:0.8rem; text-align:left;">
                        <p style="font-size:.62rem;color:var(--vg-text-muted);text-transform:uppercase;letter-spacing:.02em;margin:0 0 4px 0;line-height:1.2;">Total Lifetime</p>
                        <p style="font-size:1.4rem;font-weight:800;color:var(--vg-accent);margin:0;line-height:1;">{{ $lifetimeSessions > 0 ? $lifetimeSessions : '0' }}</p>
                    </div>
                </div>

                {{-- Dynamic Mini Calendar Preview --}}
                @php
                    $today = \Carbon\Carbon::today();
                    $startOfMonth = $today->copy()->startOfMonth();
                    $endOfMonth = $today->copy()->endOfMonth();
                    $daysInMonth = $today->daysInMonth;
                    $startOfWeek = $startOfMonth->dayOfWeek; // 0 = Sunday, 1 = Monday, etc.
                    
                    $bookedDays = [];
                    if (isset($upcomingBookings)) {
                        foreach ($upcomingBookings as $booking) {
                            $date = \Carbon\Carbon::parse($booking->session_date);
                            if ($date->month === $today->month && $date->year === $today->year) {
                                $bookedDays[] = $date->day;
                            }
                        }
                    }
                    if (isset($pastBookings)) {
                        foreach ($pastBookings as $booking) {
                            $date = \Carbon\Carbon::parse($booking->session_date);
                            if ($date->month === $today->month && $date->year === $today->year) {
                                $bookedDays[] = $date->day;
                            }
                        }
                    }
                    $bookedDays = array_unique($bookedDays);
                @endphp
                <div class="mini-calendar-card" style="margin-bottom:1.2rem;">
                    <div class="calendar-header" style="display:flex;justify-content:space-between;align-items:center;margin-bottom:0.75rem;">
                        <span style="font-size:0.72rem;font-weight:800;color:var(--vg-text-strong);text-transform:uppercase;letter-spacing:0.05em;">{{ $today->format('F Y') }}</span>
                        <div style="display:flex;gap:4px;align-items:center;">
                            <span class="calendar-indicator" style="background:#10b981;width:6px;height:6px;border-radius:50%;display:inline-block;"></span>
                            <span style="font-size:0.65rem;color:var(--vg-text-muted);font-weight:600;">Booked</span>
                        </div>
                    </div>
                    
                    <div class="calendar-grid" style="display:grid;grid-template-columns:repeat(7, 1fr);gap:4px;text-align:center;">
                        <div class="calendar-day-name" style="font-size:0.65rem;color:var(--vg-text-faint);font-weight:700;">Su</div>
                        <div class="calendar-day-name" style="font-size:0.65rem;color:var(--vg-text-faint);font-weight:700;">Mo</div>
                        <div class="calendar-day-name" style="font-size:0.65rem;color:var(--vg-text-faint);font-weight:700;">Tu</div>
                        <div class="calendar-day-name" style="font-size:0.65rem;color:var(--vg-text-faint);font-weight:700;">We</div>
                        <div class="calendar-day-name" style="font-size:0.65rem;color:var(--vg-text-faint);font-weight:700;">Th</div>
                        <div class="calendar-day-name" style="font-size:0.65rem;color:var(--vg-text-faint);font-weight:700;">Fr</div>
                        <div class="calendar-day-name" style="font-size:0.65rem;color:var(--vg-text-faint);font-weight:700;">Sa</div>
                        
                        @for($i = 0; $i < $startOfWeek; $i++)
                            <div class="calendar-day empty" style="font-size:0.7rem;padding:4px 0;opacity:0;"></div>
                        @endfor
                        
                        @for($day = 1; $day <= $daysInMonth; $day++)
                            @php
                                $isCurrentDay = ($day === $today->day);
                                $isBooked = in_array($day, $bookedDays);
                                
                                $class = '';
                                if ($isCurrentDay) $class = 'today';
                                elseif ($isBooked) $class = 'booked';
                            @endphp
                            <div class="calendar-day {{ $class }}" style="font-size:0.7rem;padding:4px 0;border-radius:6px;font-weight:600;display:flex;align-items:center;justify-content:center;aspect-ratio:1;">
                                {{ $day }}
                            </div>
                        @endfor
                    </div>
                </div>

                <hr style="border:0;border-top:1px solid rgba(255,255,255,0.05);margin:1rem 0;">

                <div style="margin-bottom:0.8rem;display:flex;justify-content:space-between;align-items:center;">
                    <span style="font-size:.75rem;color:var(--vg-text-muted);">Favourite Trainer</span>
                    <span style="font-size:.8rem;font-weight:700;color:var(--vg-text-strong);">{{ $favouriteTrainerName }}</span>
                </div>
                
                <div style="margin-bottom:0.8rem;display:flex;justify-content:space-between;align-items:center;">
                    <span style="font-size:.75rem;color:var(--vg-text-muted);">Avg. Session Duration</span>
                    <span style="font-size:.8rem;font-weight:700;color:var(--vg-text-strong);">{{ $averageSessionDuration }}</span>
                </div>
                
                <div style="display:flex;justify-content:space-between;align-items:center;">
                    <span style="font-size:.75rem;color:var(--vg-text-muted);">Next Session Date</span>
                    <span style="font-size:.8rem;font-weight:700;color:var(--vg-accent);">{{ $nextSessionDate !== '—' ? $nextSessionDate : '—' }}</span>
                </div>
            </div>
        @else
            {{-- Trainer Right Sidebar (Today's Timeline & Earnings Summary) --}}
            <div style="background:rgba(255, 255, 255, 0.02);border:1px solid rgba(255, 255, 255, 0.06);border-radius:24px;padding:1.8rem;position:sticky;top:2rem;display:flex;flex-direction:column;gap:2rem;">
                
                {{-- Today's Schedule Timeline --}}
                <div>
                    <h2 style="font-size:1rem;font-weight:800;color:#fff;margin-bottom:1.5rem;display:flex;align-items:center;gap:8px;">
                        <i data-lucide="activity" style="color:var(--vg-accent);"></i> Today's Schedule
                    </h2>
                    
                    @if(isset($todaysSchedule) && $todaysSchedule->count() > 0)
                        <div style="display:flex;flex-direction:column;">
                            @foreach($todaysSchedule as $session)
                                <div class="timeline-item">
                                    <p style="font-size:.85rem;font-weight:700;color:#fff;margin-bottom:2px;">{{ $session->trainee->name ?? 'Trainee' }}</p>
                                    <p style="font-size:.75rem;color:rgba(255,255,255,0.4);font-weight:600;margin-bottom:4px;">
                                        {{ \Carbon\Carbon::parse($session->session_date)->format('h:i A') }} • {{ $session->duration_minutes }}m
                                    </p>
                                    <span style="font-size:.6rem;background:rgba(139,92,246,0.1);color:#c4b5fd;padding:2px 8px;border-radius:4px;font-weight:700;text-transform:uppercase;">
                                        {{ $session->session_type ?? 'Strength' }}
                                    </span>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p style="font-size:.8rem;color:rgba(255,255,255,0.25);font-style:italic;">No training sessions scheduled for today.</p>
                    @endif
                </div>

                {{-- Quick Earnings Card --}}
                <div style="border-top:1px solid rgba(255,255,255,0.04);padding-top:1.5rem;">
                    <h2 style="font-size:1rem;font-weight:800;color:#fff;margin-bottom:1.2rem;display:flex;align-items:center;gap:8px;">
                        <i data-lucide="wallet" style="color:#10b981;"></i> Weekly Earnings
                    </h2>
                    <div style="background:rgba(0,0,0,0.2);padding:1.2rem;border-radius:16px;border:1px solid rgba(255,255,255,0.04);text-align:center;">
                        <p style="font-size:.65rem;color:rgba(255,255,255,0.4);font-weight:700;text-transform:uppercase;margin-bottom:4px;">Weekly Revenue Goal (₹20,000)</p>
                        <p style="font-size:1.8rem;font-weight:900;color:#10b981;margin-bottom:10px;">
                            ₹{{ number_format($trainerStats['weekly_earnings'] ?? 0) }}
                        </p>
                        
                        @php
                            $target = 20000;
                            $pct = min(100, round((($trainerStats['weekly_earnings'] ?? 0) / $target) * 100));
                        @endphp
                        <div style="width:100%;height:10px;background:rgba(255,255,255,0.05);border-radius:5px;overflow:hidden;margin-bottom:6px;">
                            <div style="width:{{ $pct }}%;height:100%;background:linear-gradient(90deg, #10b981, var(--vg-accent));border-radius:5px;"></div>
                        </div>
                        <span style="font-size:.7rem;color:rgba(255,255,255,0.3);font-weight:600;">{{ $pct }}% of weekly goal achieved</span>
                    </div>
                </div>

            </div>
        @endif
    </div>
</div>

{{-- Trainee Profile Modal --}}
<div id="profileModal" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,.8);backdrop-filter:blur(10px);z-index:1000;align-items:center;justify-content:center;">
    <div style="background:rgba(20,20,20,.95);border:1px solid rgba(255,255,255,.1);border-radius:28px;padding:2.5rem;width:100%;max-width:440px;position:relative;text-align:center;">
        <button onclick="closeProfileModal()" style="position:absolute;top:1.5rem;right:1.5rem;background:none;border:none;color:rgba(255,255,255,.4);cursor:pointer;">
            <i data-lucide="x"></i>
        </button>
        <div id="modalAvatar" style="width:70px;height:70px;border-radius:50%;background:var(--vg-accent);margin:0 auto 1rem;display:flex;align-items:center;justify-content:center;font-size:2rem;font-weight:900;color:#fff;"></div>
        <h2 id="modalName" style="font-size:1.4rem;font-weight:900;color:#fff;margin-bottom:4px;">Trainee Name</h2>
        <p id="modalEmail" style="font-size:.85rem;color:rgba(255,255,255,0.4);margin-bottom:1.5rem;">email@example.com</p>
        
        <div style="background:rgba(255,255,255,0.02);border:1px solid rgba(255,255,255,0.04);border-radius:16px;padding:1rem;display:grid;grid-template-columns:1fr 1fr;gap:1rem;text-align:left;margin-bottom:1.5rem;">
            <div>
                <p style="font-size:.65rem;color:rgba(255,255,255,0.4);font-weight:700;text-transform:uppercase;">Primary Focus</p>
                <p id="modalFocus" style="font-size:.85rem;color:#fff;font-weight:600;">Cardio</p>
            </div>
            <div>
                <p style="font-size:.65rem;color:rgba(255,255,255,0.4);font-weight:700;text-transform:uppercase;">Goal Metrics</p>
                <p style="font-size:.85rem;color:#10b981;font-weight:600;">Active Tracker</p>
            </div>
        </div>
        
        <button onclick="closeProfileModal()" class="action-btn primary" style="width:100%;justify-content:center;">Close Profile</button>
    </div>
</div>

{{-- Reschedule Modal --}}
<div id="rescheduleModal" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,.8);backdrop-filter:blur(10px);z-index:1000;align-items:center;justify-content:center;">
    <div style="background:rgba(20,20,20,.95);border:1px solid rgba(255,255,255,.1);border-radius:28px;padding:2.5rem;width:100%;max-width:440px;position:relative;">
        <button onclick="closeRescheduleModal()" style="position:absolute;top:1.5rem;right:1.5rem;background:none;border:none;color:rgba(255,255,255,.4);cursor:pointer;">
            <i data-lucide="x"></i>
        </button>
        <h2 style="font-size:1.4rem;font-weight:900;color:#fff;margin-bottom:8px;">Reschedule Session</h2>
        <p style="font-size:.85rem;color:rgba(255,255,255,0.4);margin-bottom:1.5rem;">Please contact your trainee via chat to agree on a new time, or select a slot below to propose a change.</p>
        
        <div style="background:rgba(255,255,255,0.02);border:1px solid rgba(255,255,255,0.04);border-radius:16px;padding:1.5rem;text-align:center;margin-bottom:1.5rem;">
            <i data-lucide="calendar-clock" style="width:40px;height:40px;color:var(--vg-accent);margin-bottom:10px;"></i>
            <p style="font-size:.9rem;color:#fff;font-weight:600;margin-bottom:6px;">Automated Rescheduling</p>
            <p style="font-size:.75rem;color:rgba(255,255,255,0.5);">This feature is currently in beta. Please use direct messaging to coordinate timing changes for now.</p>
        </div>
        
        <div style="display:flex;gap:12px;">
            <button onclick="closeRescheduleModal()" class="action-btn" style="flex:1;justify-content:center;">Cancel</button>
            <a href="{{ route('chat.index') }}" class="action-btn primary" style="flex:1;justify-content:center;text-decoration:none;">Open Chat</a>
        </div>
    </div>
</div>

<script>
    // Tab switching
    function switchTab(tabName) {
        document.querySelectorAll('.tab-btn').forEach(btn => btn.classList.remove('active'));
        document.getElementById('tab-' + tabName).classList.add('active');
        
        document.getElementById('content-upcoming').style.display = 'none';
        document.getElementById('content-past').style.display = 'none';
        document.getElementById('content-cancelled').style.display = 'none';
        
        document.getElementById('content-' + tabName).style.display = 'block';
    }

    // View Trainee Profile Modal
    function viewTraineeProfile(name, email, focus) {
        document.getElementById('modalAvatar').innerText = name.charAt(0);
        document.getElementById('modalName').innerText = name;
        document.getElementById('modalEmail').innerText = email || 'No email provided';
        document.getElementById('modalFocus').innerText = focus || 'General Fitness';
        document.getElementById('profileModal').style.display = 'flex';
    }

    function closeProfileModal() {
        document.getElementById('profileModal').style.display = 'none';
    }

    // Reschedule Modal
    function openRescheduleModal(bookingId) {
        document.getElementById('rescheduleModal').style.display = 'flex';
    }

    function closeRescheduleModal() {
        document.getElementById('rescheduleModal').style.display = 'none';
    }

    // Bulk action select all
    const selectAll = document.getElementById('selectAllBookings');
    if (selectAll) {
        selectAll.addEventListener('change', function() {
            const selectors = document.querySelectorAll('.booking-selector');
            selectors.forEach(sel => sel.checked = selectAll.checked);
        });
    }

    function prepareBulkSubmit() {
        const checkedSelectors = document.querySelectorAll('.booking-selector:checked');
        if (checkedSelectors.length === 0) {
            alert('Please select at least one booking to complete.');
            return false;
        }

        const idsInput = document.getElementById('bulkBookingIds');
        // Clear previous entries
        const form = document.getElementById('bulkCompleteForm');
        // Remove existing dynamic inputs
        form.querySelectorAll('.dynamic-id-input').forEach(input => input.remove());

        // Append inputs dynamically
        checkedSelectors.forEach(selector => {
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'booking_ids[]';
            input.value = selector.value;
            input.className = 'dynamic-id-input';
            form.appendChild(input);
        });

        return confirm(`Are you sure you want to mark ${checkedSelectors.length} sessions as completed?`);
    }

    function confirmTrainerCancellation(form) {
        const reason = prompt('Please enter the cancellation reason. Admin will review the trainee refund request.');

        if (!reason || reason.trim().length < 5) {
            alert('Cancellation reason must be at least 5 characters.');
            return false;
        }

        form.querySelector('input[name="cancellation_reason"]').value = reason.trim();
        return confirm('Cancel this session and send a refund request to admin?');
    }

    // Countdown Timer logic for upcoming bookings
    function updateCountdowns() {
        const badges = document.querySelectorAll('.countdown-badge[data-datetime]');
        badges.forEach(badge => {
            const targetStr = badge.getAttribute('data-datetime');
            const targetDate = new Date(targetStr);
            const now = new Date();
            
            const card = badge.closest('.booking-card');
            const durationMin = card ? parseInt(card.getAttribute('data-duration')) || 60 : 60;
            const durationMs = durationMin * 60 * 1000;
            
            const activeStart = new Date(targetDate.getTime() - 15 * 60 * 1000); // 15 mins before
            const activeEnd = new Date(targetDate.getTime() + durationMs); // session end

            const textEl = badge.querySelector('.countdown-text');
            if (!textEl) return;

            if (now >= activeStart && now <= activeEnd) {
                // Active Now
                badge.style.background = 'rgba(16, 185, 129, 0.1)';
                badge.style.borderColor = 'rgba(16, 185, 129, 0.3)';
                badge.style.color = '#10b981';
                textEl.textContent = 'Active Now';
                
                // Enable join video call button if disabled
                if (card) {
                    const disabledBtn = card.querySelector('button[disabled]');
                    if (disabledBtn && disabledBtn.textContent.includes('Video Call')) {
                        const bookingId = card.id.replace('booking-', '');
                        const joinLink = document.createElement('a');
                        joinLink.href = `/video-call/join/${bookingId}`;
                        joinLink.className = 'action-btn primary';
                        joinLink.style.display = 'inline-flex';
                        joinLink.style.alignItems = 'center';
                        joinLink.style.gap = '6px';
                        joinLink.style.textDecoration = 'none';
                        joinLink.innerHTML = '🎥 Join Video Session';
                        disabledBtn.replaceWith(joinLink);
                    }
                }
            } else if (now > activeEnd) {
                // Overdue / Past
                badge.style.background = 'rgba(239, 68, 68, 0.1)';
                badge.style.borderColor = 'rgba(239, 68, 68, 0.3)';
                badge.style.color = '#ef4444';
                textEl.textContent = 'Session Overdue';
                
                // Replace active video link with disabled state if present
                if (card) {
                    const activeLink = card.querySelector('a[href^="/video-call/join/"]');
                    if (activeLink) {
                        const disabledBtn = document.createElement('button');
                        disabledBtn.className = 'action-btn';
                        disabledBtn.disabled = true;
                        disabledBtn.style.background = 'rgba(255,255,255,0.02)';
                        disabledBtn.style.border = '1px solid var(--vg-border)';
                        disabledBtn.style.color = 'var(--vg-text-muted)';
                        disabledBtn.innerHTML = '🎥 Video Call Ended';
                        activeLink.replaceWith(disabledBtn);
                    }
                }
            } else {
                // Upcoming countdown
                const diffMs = targetDate - now;
                const totalSeconds = Math.floor(diffMs / 1000);
                const days = Math.floor(totalSeconds / 86400);
                const hours = Math.floor((totalSeconds % 86400) / 3600);
                const minutes = Math.floor((totalSeconds % 3600) / 60);
                const seconds = totalSeconds % 60;

                badge.style.background = '';
                badge.style.borderColor = '';
                badge.style.color = '';

                let countdownStr = 'Starts in ';
                if (days > 0) {
                    countdownStr += `${days}d ${hours}h`;
                } else if (hours > 0) {
                    countdownStr += `${hours}h ${minutes}m`;
                } else {
                    countdownStr += `${minutes}m ${seconds}s`;
                }
                textEl.textContent = countdownStr;
            }
        });
    }
    
    updateCountdowns();
    setInterval(updateCountdowns, 1000);

    // Close modal on click outside
    window.onclick = function(event) {
        const profileModal = document.getElementById('profileModal');
        const rescheduleModal = document.getElementById('rescheduleModal');
        if (event.target == profileModal) {
            closeProfileModal();
        }
        if (event.target == rescheduleModal) {
            closeRescheduleModal();
        }
    }
</script>
@endsection
