@extends('layouts.app')

@section('title', 'Trainer Dashboard')

@section('content')
<div style="max-width:1450px;margin:0 auto;padding-bottom: 3rem;">

    {{-- Welcome Banner --}}
    <div style="background:linear-gradient(135deg,rgba(139,92,246,.18),rgba(236,72,153,.12));border:1px solid rgba(139,92,246,.28);border-radius:28px;padding:2.5rem;margin-bottom:2.5rem;position:relative;overflow:hidden;display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:2rem;" class="fade-in-up">
        <div style="position:absolute;inset:0;background:conic-gradient(from 0deg at 100% 0%,rgba(139,92,246,.08) 0deg,transparent 80deg);pointer-events:none;"></div>
        
        <div style="position:relative;z-index:1;display:flex;align-items:center;gap:1.5rem;">
            <div style="width:80px;height:80px;border-radius:24px;background:var(--vg-gradient);display:flex;align-items:center;justify-content:center;font-size:2rem;font-weight:900;color:#fff;box-shadow:0 10px 25px var(--vg-accent-glow);">
                {{ substr(auth()->user()->name, 0, 1) }}
            </div>
            <div>
                <h1 style="font-size:clamp(1.5rem,3vw,2.2rem);font-weight:900;background:linear-gradient(135deg,#fff 20%,#c4b5fd 60%,#f9a8d4 90%);-webkit-background-clip:text;background-clip:text;color:transparent;margin-bottom:.35rem;">
                    Welcome back, {{ auth()->user()->name }}! 🏋️
                </h1>
                <div style="display:flex;gap:1.5rem;margin-top:.5rem;">
                    <div style="display:flex;align-items:center;gap:6px;color:rgba(255,255,255,.5);font-size:.85rem;">
                        <i data-lucide="calendar" style="width:16px;height:16px;color:var(--vg-accent);"></i>
                        <span>{{ $stats['todays_sessions'] }} sessions today</span>
                    </div>
                    <div style="display:flex;align-items:center;gap:6px;color:rgba(255,255,255,.5);font-size:.85rem;">
                        <i data-lucide="trending-up" style="width:16px;height:16px;color:#10b981;"></i>
                        <span>₹{{ number_format($stats['monthly_earnings']) }} this month</span>
                    </div>
                </div>
            </div>
        </div>

        <div style="position:relative;z-index:1;background:rgba(255,255,255,0.05);padding:1rem 1.5rem;border-radius:20px;border:1px solid rgba(255,255,255,0.1);backdrop-filter:blur(10px);">
            <p style="font-size:.7rem;color:rgba(255,255,255,.4);font-weight:700;letter-spacing:.05em;margin-bottom:4px;text-transform:uppercase;">Current Rating</p>
            <div style="display:flex;align-items:baseline;gap:8px;">
                @if($stats['rating'] > 0)
                    <span style="font-size:1.8rem;font-weight:900;color:#fff;">{{ $stats['rating'] }}</span>
                    <div style="display:flex;color:#fbbf24;gap:2px;">
                        @for($i=0; $i<5; $i++)
                            <i data-lucide="star" style="width:12px;height:12px;{{ $i < floor($stats['rating']) ? 'fill:#fbbf24;' : '' }}"></i>
                        @endfor
                    </div>
                @else
                    <span style="font-size:.85rem;color:rgba(255,255,255,0.3);font-weight:600;">No ratings yet</span>
                @endif
            </div>
        </div>
    </div>

    {{-- Stats Grid --}}
    <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(240px,1fr));gap:1.5rem;margin-bottom:2.5rem;">
        {{-- Total Clients --}}
        <div class="fade-in-up delay-1" style="background:rgba(255,255,255,.03);border:1px solid rgba(139,92,246,.15);border-radius:24px;padding:1.8rem;position:relative;overflow:hidden;">
            <div style="position:absolute;top:1rem;right:1rem;width:40px;height:40px;border-radius:12px;background:rgba(139,92,246,.1);display:flex;align-items:center;justify-content:center;color:var(--vg-accent);">
                <i data-lucide="users"></i>
            </div>
            <p style="color:rgba(255,255,255,.4);font-size:.75rem;font-weight:700;letter-spacing:.05em;margin-bottom:1rem;text-transform:uppercase;">Clients</p>
            <div style="display:flex;align-items:baseline;gap:10px;">
                <h3 style="font-size:2.2rem;font-weight:900;color:#fff;">{{ $stats['total_clients'] }}</h3>
                <span style="color:#10b981;font-size:.8rem;font-weight:600;">{{ $stats['active_clients'] }} active</span>
            </div>
        </div>

        {{-- Completion Rate --}}
        <div class="fade-in-up delay-2" style="background:rgba(255,255,255,.03);border:1px solid rgba(139,92,246,.15);border-radius:24px;padding:1.8rem;position:relative;overflow:hidden;">
            <div style="position:absolute;top:1rem;right:1rem;width:40px;height:40px;border-radius:12px;background:rgba(16,185,129,.1);display:flex;align-items:center;justify-content:center;color:#10b981;">
                <i data-lucide="check-circle"></i>
            </div>
            <p style="color:rgba(255,255,255,.4);font-size:.75rem;font-weight:700;letter-spacing:.05em;margin-bottom:1rem;text-transform:uppercase;">Completion Rate</p>
            <div style="display:flex;align-items:baseline;gap:10px;">
                <h3 style="font-size:2.2rem;font-weight:900;color:#fff;">{{ $stats['completion_rate'] }}%</h3>
                <div style="width:60px;height:4px;background:rgba(255,255,255,0.1);border-radius:10px;overflow:hidden;">
                    <div style="width:{{ $stats['completion_rate'] }}%;height:100%;background:#10b981;"></div>
                </div>
            </div>
        </div>

        {{-- Total Earned --}}
        <div class="fade-in-up delay-3" style="background:rgba(255,255,255,.03);border:1px solid rgba(139,92,246,.15);border-radius:24px;padding:1.8rem;position:relative;overflow:hidden;">
            <div style="position:absolute;top:1rem;right:1rem;width:40px;height:40px;border-radius:12px;background:rgba(251,191,36,.1);display:flex;align-items:center;justify-content:center;color:#fbbf24;">
                <i data-lucide="wallet"></i>
            </div>
            <p style="color:rgba(255,255,255,.4);font-size:.75rem;font-weight:700;letter-spacing:.05em;margin-bottom:1rem;text-transform:uppercase;">Total Earned</p>
            <h3 style="font-size:2.2rem;font-weight:900;color:#fff;">₹{{ number_format($stats['total_earned']) }}</h3>
        </div>
    </div>

    {{-- Main Layout: 2 Columns --}}
    <div style="display:grid;grid-template-columns:1.8fr 1fr;gap:2rem;">
        
        {{-- Left Column --}}
        <div style="display:flex;flex-direction:column;gap:2rem;">
            
            {{-- Upcoming Sessions --}}
            <div class="fade-in-up delay-1" style="background:rgba(255,255,255,.02);border:1px solid rgba(255,255,255,0.06);border-radius:28px;padding:2rem;">
                <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:1.5rem;">
                    <h2 style="font-size:1.25rem;font-weight:800;color:#fff;display:flex;align-items:center;gap:10px;">
                        <i data-lucide="calendar-check" style="color:var(--vg-accent);"></i> Upcoming Sessions
                    </h2>
                    <a href="{{ route('trainer.schedule') }}" style="color:var(--vg-accent);font-size:.85rem;font-weight:600;text-decoration:none;">View All Schedule</a>
                </div>

                @if(count($upcomingBookings) > 0)
                    <div style="display:flex;flex-direction:column;gap:1rem;">
                        @foreach($upcomingBookings as $booking)
                            <div style="background:rgba(255,255,255,.03);border:1px solid rgba(255,255,255,0.05);border-radius:20px;padding:1.2rem;display:flex;justify-content:space-between;align-items:center;">
                                <div style="display:flex;align-items:center;gap:1rem;">
                                    <div style="width:48px;height:48px;border-radius:14px;background:rgba(139,92,246,0.1);display:flex;align-items:center;justify-content:center;color:var(--vg-accent);font-weight:800;">
                                        {{ substr($booking->trainee->name ?? 'T', 0, 1) }}
                                    </div>
                                    <div>
                                        <p style="font-weight:700;color:#fff;margin-bottom:2px;">{{ $booking->trainee->name ?? 'Trainee' }}</p>
                                        <p style="font-size:.75rem;color:rgba(255,255,255,.4);">
                                            <i data-lucide="clock" style="width:12px;height:12px;display:inline;vertical-align:middle;margin-right:4px;"></i>
                                            {{ \Carbon\Carbon::parse($booking->session_date)->format('h:i A') }} • {{ $booking->special_requests ?? 'General Session' }}
                                        </p>
                                    </div>
                                </div>
                                <div style="display:flex;gap:10px;">
                                    <a href="{{ route('video-call.join', $booking->id) }}" style="background:var(--vg-gradient);color:#fff;padding:8px 16px;border-radius:10px;font-size:.8rem;font-weight:700;text-decoration:none;box-shadow:0 4px 12px var(--vg-accent-glow);">
                                        Join
                                    </a>
                                    <a href="{{ route('chat.index', ['trainer_id' => $booking->trainee_id]) }}" style="background:rgba(255,255,255,0.05);color:#fff;padding:8px 16px;border-radius:10px;font-size:.8rem;font-weight:700;text-decoration:none;border:1px solid rgba(255,255,255,0.1);">
                                        Chat
                                    </a>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div style="text-align:center;padding:3rem 1rem;background:rgba(255,255,255,0.02);border-radius:20px;border:1px dashed rgba(255,255,255,0.1);">
                        <p style="color:rgba(255,255,255,.3);">No sessions scheduled for today</p>
                    </div>
                @endif
            </div>

            {{-- Today's Schedule Timeline --}}
            <div class="fade-in-up delay-2" style="background:rgba(255,255,255,.02);border:1px solid rgba(255,255,255,0.06);border-radius:28px;padding:2rem;">
                <h2 style="font-size:1.25rem;font-weight:800;color:#fff;margin-bottom:1.5rem;display:flex;align-items:center;gap:10px;">
                    <i data-lucide="clock" style="color:#fbbf24;"></i> Today's Schedule
                </h2>
                
                @if(count($todaysSchedule) > 0)
                    <div style="position:relative;padding-left:1.5rem;border-left:2px solid rgba(139,92,246,0.2);">
                        @foreach($todaysSchedule as $item)
                            <div style="position:relative;margin-bottom:1.5rem;">
                                <div style="position:absolute;left:-1.9rem;top:0;width:10px;height:10px;border-radius:50%;background:var(--vg-accent);box-shadow:0 0 10px var(--vg-accent-glow);"></div>
                                <div style="display:flex;justify-content:space-between;align-items:center;">
                                    <div>
                                        <p style="font-size:.8rem;font-weight:800;color:var(--vg-accent);text-transform:uppercase;letter-spacing:.05em;">{{ \Carbon\Carbon::parse($item->session_date)->format('h:i A') }}</p>
                                        <p style="font-weight:600;color:#fff;">{{ $item->trainee->name ?? 'Trainee' }}</p>
                                    </div>
                                    <span style="font-size:.7rem;padding:4px 10px;border-radius:50px;background:rgba(255,255,255,0.05);color:rgba(255,255,255,0.5);border:1px solid rgba(255,255,255,0.1);">
                                        {{ $item->duration_minutes ?? 60 }} min
                                    </span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p style="color:rgba(255,255,255,0.3);text-align:center;padding:1rem;">Clear schedule for today</p>
                @endif
            </div>

            {{-- Recent Clients --}}
            <div class="fade-in-up delay-3" style="background:rgba(255,255,255,.02);border:1px solid rgba(255,255,255,0.06);border-radius:28px;padding:2rem;">
                <h2 style="font-size:1.1rem;font-weight:800;color:#fff;margin-bottom:1.2rem;">Recent Clients</h2>
                @if(count($recentClients) > 0)
                    <div style="display:flex;flex-direction:column;gap:1rem;">
                        @foreach($recentClients as $booking)
                            <div style="display:flex;align-items:center;gap:12px;">
                                <div style="width:40px;height:40px;border-radius:50%;background:rgba(255,255,255,0.05);display:flex;align-items:center;justify-content:center;font-size:.9rem;font-weight:700;color:#fff;border:1px solid rgba(255,255,255,0.1);">
                                    {{ substr($booking->trainee->name ?? 'T', 0, 1) }}
                                </div>
                                <div>
                                    <p style="font-size:.9rem;font-weight:600;color:#fff;">{{ $booking->trainee->name ?? 'Trainee' }}</p>
                                    <p style="font-size:.7rem;color:rgba(255,255,255,0.3);">Last session: {{ \Carbon\Carbon::parse($booking->session_date)->format('M d') }}</p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p style="color:rgba(255,255,255,0.3);font-size:.85rem;">No clients yet</p>
                @endif
            </div>

        </div>

        {{-- Right Column --}}
        <div style="display:flex;flex-direction:column;gap:2rem;">
            
            {{-- Business Performance (Merged from Insights) --}}
            <div class="fade-in-up delay-2" style="background:linear-gradient(135deg,rgba(16,185,129,.1),rgba(139,92,246,.05));border:1px solid rgba(16,185,129,.2);border-radius:28px;padding:2rem;">
                <h2 style="font-size:1.1rem;font-weight:800;color:#fff;margin-bottom:1.5rem;display:flex;align-items:center;gap:10px;">
                    <i data-lucide="line-chart" style="color:#10b981;"></i> Business Insights
                </h2>
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:1rem;">
                    <div style="background:rgba(255,255,255,0.03);padding:1.2rem;border-radius:20px;border:1px solid rgba(255,255,255,0.05);">
                        <p style="font-size:.65rem;color:rgba(255,255,255,.4);font-weight:700;text-transform:uppercase;margin-bottom:8px;">Avg Sessions</p>
                        <p style="font-size:1.4rem;font-weight:800;color:#fff;">{{ $stats['avg_sessions_per_client'] }}</p>
                        <p style="font-size:.65rem;color:rgba(255,255,255,0.3);margin-top:4px;">per client</p>
                    </div>
                    <div style="background:rgba(255,255,255,0.03);padding:1.2rem;border-radius:20px;border:1px solid rgba(255,255,255,0.05);">
                        <p style="font-size:.65rem;color:rgba(255,255,255,.4);font-weight:700;text-transform:uppercase;margin-bottom:8px;">Avg Revenue</p>
                        <p style="font-size:1.4rem;font-weight:800;color:#10b981;">₹{{ number_format($stats['avg_revenue_per_client']) }}</p>
                        <p style="font-size:.65rem;color:rgba(255,255,255,0.3);margin-top:4px;">per client</p>
                    </div>
                </div>
               {{-- Recent Earnings --}}
            <div class="fade-in-up delay-4" style="background:rgba(255,255,255,.02);border:1px solid rgba(255,255,255,0.06);border-radius:28px;padding:2rem;">
                <h2 style="font-size:1.1rem;font-weight:800;color:#fff;margin-bottom:1.2rem;">Recent Earnings</h2>
                @if(count($recentEarnings) > 0)
                    <div style="display:flex;flex-direction:column;gap:.8rem;">
                        @foreach($recentEarnings as $earning)
                            <div style="display:flex;justify-content:space-between;align-items:center;">
                                <div>
                                    <p style="font-size:.85rem;font-weight:600;color:#fff;">{{ $earning->trainee->name ?? 'Session' }}</p>
                                    <p style="font-size:.65rem;color:rgba(255,255,255,0.3);">{{ \Carbon\Carbon::parse($earning->completed_at)->format('M d, Y') }}</p>
                                </div>
                                <span style="font-weight:700;color:#10b981;">+₹{{ number_format($earning->amount) }}</span>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p style="color:rgba(255,255,255,0.3);font-size:.85rem;">No earnings recorded</p>
                @endif
            </div>

            {{-- Assigned Workouts --}}
            <div class="fade-in-up delay-4" style="background:rgba(255,255,255,.02);border:1px solid rgba(255,255,255,0.06);border-radius:28px;padding:2rem;">
                <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:1.2rem;">
                    <h2 style="font-size:1.1rem;font-weight:800;color:#fff;">Assigned Workouts</h2>
                    <div style="display:flex;gap:12px;align-items:center;">
                        <a href="{{ route('workouts.index') }}" style="color:rgba(255,255,255,0.4);font-size:.8rem;font-weight:600;text-decoration:none;">View All</a>
                        <a href="{{ route('workouts.index', ['create' => 1]) }}" style="color:var(--vg-accent);font-size:.8rem;font-weight:700;text-decoration:none;display:flex;align-items:center;gap:4px;">
                            <i data-lucide="plus" style="width:14px;height:14px;"></i> Create
                        </a>
                    </div>
                </div>
                @if(count($assignedWorkouts) > 0)
                    <div style="display:flex;flex-direction:column;gap:.8rem;">
                        @foreach($assignedWorkouts as $workout)
                            <div style="background:rgba(255,255,255,0.03);border:1px solid rgba(255,255,255,0.05);border-radius:16px;padding:1rem;">
                                <p style="font-size:.9rem;font-weight:600;color:#fff;margin-bottom:2px;">{{ $workout->title }}</p>
                                <p style="font-size:.7rem;color:rgba(255,255,255,0.3);">{{ $workout->type }} • {{ $workout->difficulty }}</p>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p style="color:rgba(255,255,255,0.3);font-size:.85rem;">No workouts assigned</p>
                @endif
            </div>
        </div>


        </div>
    </div>

</div>
@endsection