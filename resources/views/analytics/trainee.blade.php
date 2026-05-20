@extends('layouts.app')

@section('title', 'My Analytics')

@section('content')
<style>
    /* Time Filter Buttons */
    .filter-btn {
        padding: 6px 14px;
        font-size: 0.78rem;
        font-weight: 700;
        color: rgba(255, 255, 255, 0.52);
        border-radius: 8px;
        text-decoration: none;
        transition: all 0.25s ease;
    }
    .filter-btn.active {
        background: linear-gradient(135deg, #8b5cf6, #ec4899);
        color: #fff;
        box-shadow: 0 4px 12px rgba(139, 92, 246, 0.3);
    }
    .filter-btn:hover:not(.active) {
        background: rgba(255, 255, 255, 0.06);
        color: rgba(255, 255, 255, 0.9);
    }

    /* Analytics Panel Cards */
    .analytics-card {
        background: rgba(255, 255, 255, 0.03);
        border: 1px solid rgba(139, 92, 246, 0.18);
        border-radius: 20px;
        padding: 1.25rem 1.4rem;
        position: relative;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        overflow: hidden;
    }
    .analytics-card:hover {
        transform: translateY(-4px);
        border-color: rgba(139, 92, 246, 0.45) !important;
        box-shadow: 0 12px 25px -5px rgba(139, 92, 246, 0.15), 0 0 15px rgba(139, 92, 246, 0.08);
    }

    /* Grids & Responsiveness */
    .analytics-grid-3 {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 1.5rem;
    }
    .analytics-grid-2 {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 1.5rem;
    }
    @media (max-width: 1024px) {
        .analytics-grid-3 {
            grid-template-columns: repeat(2, 1fr);
        }
    }
    @media (max-width: 768px) {
        .analytics-grid-3, .analytics-grid-2 {
            grid-template-columns: 1fr;
        }
    }

    /* Workout Frequency Chart Bars */
    .frequency-bar-container {
        flex: 1;
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 6px;
        height: 100%;
    }
    .frequency-bar-wrapper {
        flex: 1;
        width: 100%;
        display: flex;
        align-items: flex-end;
        justify-content: center;
    }
    .frequency-bar {
        width: 50%;
        background: linear-gradient(to top, rgba(139, 92, 246, 0.25), rgba(139, 92, 246, 0.65));
        border-radius: 6px 6px 0 0;
        position: relative;
        cursor: pointer;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        height: 0;
        animation: growBar 0.8s cubic-bezier(0.25, 1, 0.5, 1) forwards;
    }
    .frequency-bar.active {
        background: linear-gradient(to top, #f9a8d4, #8b5cf6);
        box-shadow: 0 0 12px rgba(139, 92, 246, 0.35);
    }
    .frequency-bar:hover {
        filter: brightness(1.2);
        box-shadow: 0 0 18px rgba(139, 92, 246, 0.6);
        transform: translateY(-2px);
    }
    .frequency-bar::after {
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
    .frequency-bar:hover::after {
        opacity: 1;
        transform: translateX(-50%) scale(1);
    }

    /* Volume Over Time Dots & Tooltips */
    .volume-dot {
        position: absolute;
        width: 12px;
        height: 12px;
        background: #08081a;
        border: 3px solid #8b5cf6;
        border-radius: 50%;
        cursor: pointer;
        transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
        z-index: 2;
    }
    .volume-dot:hover {
        transform: translate(-50%, 50%) scale(1.35) !important;
        box-shadow: 0 0 12px #8b5cf6;
        background: #fff;
    }
    .volume-dot::after {
        content: attr(data-tooltip);
        position: absolute;
        top: -40px;
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
    .volume-dot:hover::after {
        opacity: 1;
        transform: translateX(-50%) scale(1);
    }

    /* Achievement Cards */
    .badge-card {
        background: rgba(255, 255, 255, 0.02);
        border: 1px solid rgba(255, 255, 255, 0.06);
        border-radius: 16px;
        padding: 0.9rem;
        display: flex;
        align-items: center;
        gap: 12px;
        transition: all 0.3s ease;
    }
    .badge-card.unlocked {
        background: rgba(139, 92, 246, 0.05);
        border-color: rgba(139, 92, 246, 0.25);
    }
    .badge-card.unlocked:hover {
        transform: translateY(-3px);
        border-color: rgba(139, 92, 246, 0.45);
        box-shadow: 0 8px 18px -4px rgba(139, 92, 246, 0.22);
    }

    /* Heatmap highlights */
    .heatmap-day {
        flex: 1;
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 6px;
    }
    .heatmap-box {
        width: 100%;
        height: 48px;
        border-radius: 8px;
        background: rgba(255, 255, 255, 0.04);
        border: 1px solid rgba(255, 255, 255, 0.06);
        position: relative;
        transition: all 0.3s ease;
    }
    .heatmap-box.strongest {
        border: 2px solid #8b5cf6;
        box-shadow: 0 0 10px rgba(139, 92, 246, 0.4);
        transform: scale(1.05);
    }
    .heatmap-box.strongest::after {
        content: "⭐";
        position: absolute;
        top: -6px;
        right: -6px;
        font-size: 0.7rem;
    }

    @keyframes growBar {
        to { height: var(--target-height); }
    }
</style>

<div style="max-width:1400px;margin:0 auto;padding-bottom:3rem;">

    {{-- Title and Time Filter Actions --}}
    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:2rem;flex-wrap:wrap;gap:1rem;">
        <div>
            <h1 style="font-size:1.8rem;font-weight:900;background:var(--vg-title-gradient);-webkit-background-clip:text;background-clip:text;color:transparent;margin-bottom:.3rem;letter-spacing:-.02em;">Fitness Analytics 📊</h1>
            <p style="color:var(--vg-text-muted);font-size:.85rem;">Track your progress and consistency over time</p>
        </div>
        <div style="display:flex;align-items:center;gap:1.5rem;flex-wrap:wrap;">
            {{-- Time Filter Toggle --}}
            <div style="display:flex;gap:0.4rem;background:rgba(255,255,255,0.03);border:1px solid rgba(139,92,246,0.18);padding:4px;border-radius:12px;">
                <a href="?filter=weekly" class="filter-btn {{ $filter === 'weekly' ? 'active' : '' }}">Weekly</a>
                <a href="?filter=monthly" class="filter-btn {{ $filter === 'monthly' ? 'active' : '' }}">Monthly</a>
                <a href="?filter=yearly" class="filter-btn {{ $filter === 'yearly' ? 'active' : '' }}">Yearly</a>
            </div>
            <div style="text-align:right;">
                <p style="font-size:.72rem;color:var(--vg-text-muted);text-transform:uppercase;letter-spacing:.06em;font-weight:700;">Live Analytics</p>
                <p id="analyticsLastUpdated" style="font-size:.78rem;color:var(--vg-text-faint);margin-top:3px;">Updated {{ now()->format('h:i A') }}</p>
            </div>
        </div>
    </div>

    {{-- Top Stat Cards --}}
    <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(200px,1fr));gap:1.2rem;margin-bottom:2rem;">
        <div class="analytics-card">
            <p style="color:var(--vg-text-muted);font-size:.73rem;font-weight:600;letter-spacing:.04em;margin-bottom:.4rem;text-transform:uppercase;">Total Workouts</p>
            <p style="font-size:2.4rem;font-weight:900;background:linear-gradient(135deg,#c4b5fd,#f9a8d4);-webkit-background-clip:text;background-clip:text;color:transparent;line-height:1;margin:0;">
                {{ $totalWorkouts }}
            </p>
        </div>
        
        <div class="analytics-card">
            <p style="color:var(--vg-text-muted);font-size:.73rem;font-weight:600;letter-spacing:.04em;margin-bottom:.4rem;text-transform:uppercase;">Completion Rate</p>
            <p style="font-size:2.4rem;font-weight:900;background:linear-gradient(135deg,#6ee7b7,#34d399);-webkit-background-clip:text;background-clip:text;color:transparent;line-height:1;margin:0;">
                {{ $completionRate }}%
            </p>
        </div>
        
        <div class="analytics-card">
            <p style="color:var(--vg-text-muted);font-size:.73rem;font-weight:600;letter-spacing:.04em;margin-bottom:.4rem;text-transform:uppercase;">Total Planned Reps</p>
            <p style="font-size:2.4rem;font-weight:900;background:linear-gradient(135deg,#fb923c,#f97316);-webkit-background-clip:text;background-clip:text;color:transparent;line-height:1;margin:0;">
                {{ number_format($totalReps) }}
            </p>
        </div>

        <div class="analytics-card">
            <p style="color:var(--vg-text-muted);font-size:.73rem;font-weight:600;letter-spacing:.04em;margin-bottom:.4rem;text-transform:uppercase;">Avg Duration</p>
            <p style="font-size:2.4rem;font-weight:900;background:linear-gradient(135deg,#38bdf8,#0ea5e9);-webkit-background-clip:text;background-clip:text;color:transparent;line-height:1;margin:0;">
                {{ $avgDuration }} min
            </p>
        </div>
    </div>

    {{-- Workout Frequency & Reps Side-by-Side --}}
    <div class="analytics-grid-2" style="margin-bottom:2rem;">
        
        {{-- Workout Frequency Bar Chart --}}
        <div class="analytics-card">
            <h2 style="font-size:1rem;font-weight:700;color:var(--vg-text-strong);margin-bottom:1.2rem;">📈 Workout Frequency</h2>
            <div style="display:flex;align-items:flex-end;gap:10px;height:140px;padding-top:20px;">
                @if(isset($workoutFrequency) && count($workoutFrequency) > 0)
                    @php $maxFreq = max(max($workoutFrequency), 1); @endphp
                    @foreach($workoutFrequency as $idx => $val)
                        @php
                            $height = ($val / $maxFreq) * 100;
                            $isCurrent = $idx === count($workoutFrequency) - 1;
                            $dayName = $dayLabels[$idx] ?? ('W' . ($idx + 1));
                        @endphp
                        <div class="frequency-bar-container">
                            <div class="frequency-bar-wrapper">
                                <div class="frequency-bar {{ $isCurrent ? 'active' : '' }}" 
                                     style="--target-height: {{ $height }}%;"
                                     data-tooltip="{{ $dayName }}: {{ $val }} workouts">
                                </div>
                            </div>
                            <span style="font-size:.65rem;color:var(--vg-text-muted);margin-top:2px;">{{ $dayName }}</span>
                        </div>
                    @endforeach
                @else
                    <div style="width:100%;text-align:center;color:var(--vg-text-muted);font-size:.85rem;padding-top:2rem;">No data available</div>
                @endif
            </div>
        </div>

        {{-- Reps Over Time Line Chart --}}
        <div class="analytics-card">
            <h2 style="font-size:1rem;font-weight:700;color:var(--vg-text-strong);margin-bottom:1.2rem;">🔄 Reps Over Time</h2>
            <div style="display:flex;align-items:flex-end;gap:4px;height:140px;padding-top:20px;position:relative;">
                @if(isset($repsOverTime) && count($repsOverTime) > 0)
                    @php 
                        $maxVol = max(max($repsOverTime), 1); 
                        $minVol = min($repsOverTime);
                        $range = max($maxVol - $minVol, 1);
                    @endphp
                    
                    {{-- SVG Line implementation with Gradient Area Fill and Glow --}}
                    <svg style="position:absolute;top:20px;left:0;width:100%;height:calc(100% - 20px);z-index:0;overflow:visible;" preserveAspectRatio="none">
                        <defs>
                            <linearGradient id="volumeAreaGrad" x1="0" y1="0" x2="0" y2="1">
                                <stop offset="0%" stop-color="#8b5cf6" stop-opacity="0.35"/>
                                <stop offset="100%" stop-color="#8b5cf6" stop-opacity="0.0"/>
                            </linearGradient>
                            <filter id="volumeGlow" x="-20%" y="-20%" width="140%" height="140%">
                                <feGaussianBlur stdDeviation="5" result="blur" />
                                <feComposite in="SourceGraphic" in2="blur" operator="over" />
                            </filter>
                        </defs>
                        @php
                            $points = [];
                            $areaPoints = [];
                            $step = 100 / (count($repsOverTime) - 1);
                            
                            // Start area path at bottom left
                            $areaPoints[] = "0%,100%";
                            
                            foreach($repsOverTime as $idx => $val) {
                                $x = $idx * $step;
                                $y = 100 - ((($val - $minVol) / $range) * 80 + 10); // leave 10% padding top/bottom
                                $points[] = "$x%,$y%";
                                $areaPoints[] = "$x%,$y%";
                            }
                            
                            // Close area path at bottom right
                            $areaPoints[] = "100%,100%";
                            
                            $linePointsStr = implode(' ', $points);
                            $areaPointsStr = implode(' ', $areaPoints);
                        @endphp
                        {{-- Glow Layer --}}
                        <polyline points="{{ str_replace('%', '', $linePointsStr) }}" style="fill:none;stroke:#8b5cf6;stroke-width:6;stroke-linejoin:round;stroke-linecap:round;filter:blur(4px);opacity:0.6;" vector-effect="non-scaling-stroke"></polyline>
                        {{-- Main Area Fill --}}
                        <polygon points="{{ str_replace('%', '', $areaPointsStr) }}" style="fill:url(#volumeAreaGrad);" vector-effect="non-scaling-stroke"></polygon>
                        {{-- Main Stroke Line --}}
                        <polyline points="{{ str_replace('%', '', $linePointsStr) }}" style="fill:none;stroke:#8b5cf6;stroke-width:3.5;stroke-linejoin:round;stroke-linecap:round;" vector-effect="non-scaling-stroke"></polyline>
                    </svg>

                    @foreach($repsOverTime as $idx => $val)
                        @php
                            $y = ((($val - $minVol) / $range) * 80 + 10);
                            $dayName = $dayLabels[$idx] ?? ('W' . ($idx + 1));
                        @endphp
                        <div style="flex:1;display:flex;flex-direction:column;align-items:center;height:100%;position:relative;z-index:1;">
                            <div style="flex:1;width:100%;position:relative;">
                                <div class="volume-dot" 
                                     style="bottom:{{ $y }}%;left:50%;transform:translate(-50%, 50%);" 
                                     data-tooltip="{{ $dayName }}: {{ number_format($val) }} reps">
                                </div>
                            </div>
                            <span style="font-size:.65rem;color:var(--vg-text-muted);position:absolute;bottom:-20px;">{{ $dayName }}</span>
                        </div>
                    @endforeach
                @else
                    <div style="width:100%;text-align:center;color:var(--vg-text-muted);font-size:.85rem;padding-top:2rem;">No data available</div>
                @endif
            </div>
        </div>

    </div>

    {{-- Workout Insights Section --}}
    <div class="analytics-card" style="margin-bottom:2rem;">
        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:1.1rem;">
            <h2 style="font-size:1rem;font-weight:700;color:var(--vg-text-strong);margin:0;">✨ Workout Insights</h2>
            <span style="font-size:.78rem;color:var(--vg-text-muted);font-weight:600;">Real-time analysis</span>
        </div>
        
        <div style="display:grid;grid-template-columns:repeat(auto-fit, minmax(180px, 1fr));gap:1rem;">
            {{-- Favorite Workout Type --}}
            <div style="background:rgba(255,255,255,0.02);border:1px solid rgba(139,92,246,0.12);border-radius:14px;padding:0.75rem 1rem;display:flex;align-items:center;justify-content:space-between;transition:all 0.3s;" onmouseover="this.style.borderColor='rgba(139,92,246,0.35)';this.style.background='rgba(139,92,246,0.05)';" onmouseout="this.style.borderColor='rgba(139,92,246,0.12)';this.style.background='rgba(255,255,255,0.02)';">
                <div>
                    <p style="font-size:0.7rem;color:var(--vg-text-muted);font-weight:600;text-transform:uppercase;margin:0;">Favorite Type</p>
                    <p style="font-size:1.15rem;font-weight:900;color:#fff;margin:2px 0 0 0;">{{ $favType }}</p>
                </div>
                <div style="font-size:1.5rem;opacity:0.85;">🏷️</div>
            </div>

            {{-- Most Trained Muscle --}}
            <div style="background:rgba(255,255,255,0.02);border:1px solid rgba(16,185,129,0.12);border-radius:14px;padding:0.75rem 1rem;display:flex;align-items:center;justify-content:space-between;transition:all 0.3s;" onmouseover="this.style.borderColor='rgba(16,185,129,0.35)';this.style.background='rgba(16,185,129,0.05)';" onmouseout="this.style.borderColor='rgba(16,185,129,0.12)';this.style.background='rgba(255,255,255,0.02)';">
                <div>
                    <p style="font-size:0.7rem;color:var(--vg-text-muted);font-weight:600;text-transform:uppercase;margin:0;">Most Trained Muscle</p>
                    <p style="font-size:1.15rem;font-weight:900;color:#fff;margin:2px 0 0 0;">{{ $mostTrainedMuscle }}</p>
                </div>
                <div style="font-size:1.5rem;opacity:0.85;">💪</div>
            </div>

            {{-- Average Duration --}}
            <div style="background:rgba(255,255,255,0.02);border:1px solid rgba(59,130,246,0.12);border-radius:14px;padding:0.75rem 1rem;display:flex;align-items:center;justify-content:space-between;transition:all 0.3s;" onmouseover="this.style.borderColor='rgba(59,130,246,0.35)';this.style.background='rgba(59,130,246,0.05)';" onmouseout="this.style.borderColor='rgba(59,130,246,0.12)';this.style.background='rgba(255,255,255,0.02)';">
                <div>
                    <p style="font-size:0.7rem;color:var(--vg-text-muted);font-weight:600;text-transform:uppercase;margin:0;">Avg Duration</p>
                    <p style="font-size:1.15rem;font-weight:900;color:#fff;margin:2px 0 0 0;">{{ $avgDuration }} min</p>
                </div>
                <div style="font-size:1.5rem;opacity:0.85;">⏱️</div>
            </div>

            {{-- Average Rating --}}
            <div style="background:rgba(255,255,255,0.02);border:1px solid rgba(245,158,11,0.12);border-radius:14px;padding:0.75rem 1rem;display:flex;align-items:center;justify-content:space-between;transition:all 0.3s;" onmouseover="this.style.borderColor='rgba(245,158,11,0.35)';this.style.background='rgba(245,158,11,0.05)';" onmouseout="this.style.borderColor='rgba(245,158,11,0.12)';this.style.background='rgba(255,255,255,0.02)';">
                <div>
                    <p style="font-size:0.7rem;color:var(--vg-text-muted);font-weight:600;text-transform:uppercase;margin:0;">Avg Rating</p>
                    <p style="font-size:1.15rem;font-weight:900;color:#fff;margin:2px 0 0 0;">{{ $avgRating > 0 ? $avgRating . ' / 10' : 'N/A' }}</p>
                </div>
                <div style="font-size:1.5rem;opacity:0.85;">⭐</div>
            </div>
        </div>
    </div>

    {{-- Muscle Group Breakdown & Workout Duration --}}
    <div class="analytics-grid-2" style="margin-bottom:2rem;">
        
        {{-- Muscle Group Pie/Donut Chart with Detailed Legend --}}
        <div class="analytics-card" style="display:flex;flex-direction:column;">
            <h2 style="font-size:1rem;font-weight:700;color:var(--vg-text-strong);margin-bottom:1.1rem;">💪 Muscle Group Breakdown</h2>
            <div style="display:flex;gap:1.5rem;align-items:center;flex:1;flex-wrap:wrap;">
                @if(isset($muscleBreakdown) && count($muscleBreakdown) > 0)
                    @php
                        $conicStops = [];
                        $currentPercentage = 0;
                        foreach($muscleBreakdown as $muscle) {
                            $nextPercentage = $currentPercentage + $muscle['value'];
                            $conicStops[] = $muscle['color'] . " $currentPercentage% $nextPercentage%";
                            $currentPercentage = $nextPercentage;
                        }
                        $conicGradient = implode(', ', $conicStops);
                    @endphp
                    <div style="width:120px;height:120px;border-radius:50%;background:conic-gradient({{ $conicGradient }});position:relative;flex-shrink:0;box-shadow: 0 0 15px rgba(139,92,246,0.15);">
                        <div style="position:absolute;top:50%;left:50%;transform:translate(-50%,-50%);width:82px;height:82px;background:var(--vg-panel-strong);border-radius:50%;display:flex;align-items:center;justify-content:center;">
                            <span style="font-size:1.1rem;">📊</span>
                        </div>
                    </div>
                    <div style="flex:1;display:flex;flex-direction:column;gap:0.45rem;min-width:180px;">
                        @foreach($muscleBreakdown as $muscle)
                            <div style="display:flex;align-items:center;justify-content:space-between;font-size:0.78rem;">
                                <div style="display:flex;align-items:center;gap:8px;">
                                    <div style="width:10px;height:10px;border-radius:3px;background:{{ $muscle['color'] }};"></div>
                                    <span style="color:var(--vg-text-strong);font-weight:500;">{{ $muscle['name'] }}</span>
                                </div>
                                <div style="display:flex;gap:10px;color:var(--vg-text-muted);">
                                    <span>{{ $muscle['count'] }} sets</span>
                                    <span style="color:#fff;font-weight:700;">{{ $muscle['value'] }}%</span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div style="width:100%;text-align:center;color:var(--vg-text-muted);font-size:.85rem;">No data available</div>
                @endif
            </div>
        </div>

        {{-- Workout Duration Trend --}}
        <div class="analytics-card">
            <h2 style="font-size:1rem;font-weight:700;color:var(--vg-text-strong);margin-bottom:1.2rem;">⏱️ Workout Duration Trend</h2>
            <div style="display:flex;align-items:flex-end;gap:8px;height:140px;padding-top:20px;">
                @if(isset($durationTrend) && count($durationTrend) > 0)
                    @php $maxDur = max(max($durationTrend), 1); @endphp
                    @foreach($durationTrend as $idx => $val)
                        @php
                            $height = ($val / $maxDur) * 100;
                            $isCurrent = $idx === count($durationTrend) - 1;
                            $dayName = $dayLabels[$idx] ?? ('S' . ($idx + 1));
                        @endphp
                        <div class="frequency-bar-container">
                            <div class="frequency-bar-wrapper">
                                <div class="frequency-bar {{ $isCurrent ? 'active' : '' }}" 
                                     style="--target-height: {{ $height }}%;"
                                     data-tooltip="{{ $dayName }}: {{ $val }}m">
                                </div>
                            </div>
                            <span style="font-size:.65rem;color:var(--vg-text-muted);margin-top:2px;">{{ $dayName }}</span>
                        </div>
                    @endforeach
                @else
                    <div style="width:100%;text-align:center;color:var(--vg-text-muted);font-size:.85rem;padding-top:2rem;">No data available</div>
                @endif
            </div>
        </div>

    </div>

    {{-- Bottom Section: Best Day Heatmap, Comparison, Consistency Score --}}
    <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(300px,1fr));gap:1.5rem;margin-bottom:2rem;">
        
        {{-- Best Performing Day Heatmap --}}
        <div class="analytics-card">
            <h2 style="font-size:1rem;font-weight:700;color:var(--vg-text-strong);margin-bottom:1.2rem;">📅 Best Performing Day</h2>
            @if(isset($bestDays) && count($bestDays) > 0)
                @php
                    $maxDay = max(max($bestDays), 1);
                    $strongestDayName = 'Friday';
                    $strongestCount = 0;
                    foreach($bestDays as $day => $count) {
                        if ($count > $strongestCount) {
                            $strongestCount = $count;
                            $strongestDayName = $day;
                        }
                    }
                @endphp
                <div style="display:flex;gap:6px;height:55px;margin-bottom:1.1rem;">
                    @foreach($bestDays as $day => $count)
                        @php 
                            $opacity = 0.15 + (($count / $maxDay) * 0.85);
                            $isStrongest = $day === $strongestDayName;
                        @endphp
                        <div class="heatmap-day">
                            <div class="heatmap-box {{ $isStrongest ? 'strongest' : '' }}" 
                                 style="background:rgba(139,92,246,{{ $opacity }});"
                                 title="{{ $day }}: {{ $count }} workouts">
                            </div>
                            <span style="font-size:.65rem;color:{{ $isStrongest ? '#c4b5fd' : 'var(--vg-text-muted)' }};font-weight:{{ $isStrongest ? '700' : '500' }};">{{ substr($day, 0, 1) }}</span>
                        </div>
                    @endforeach
                </div>
                <div style="background:rgba(139,92,246,0.06);border:1px solid rgba(139,92,246,0.18);border-radius:12px;padding:0.5rem 0.8rem;font-size:0.75rem;color:#e2d9f3;display:flex;align-items:center;justify-content:space-between;">
                    <span>Strongest Day: <strong style="color:#c4b5fd;">{{ $strongestDayName }}</strong></span>
                    <span><strong>{{ $strongestCount }}</strong> Workouts logged</span>
                </div>
            @else
                <div style="width:100%;text-align:center;color:var(--vg-text-muted);font-size:.85rem;padding-top:1rem;">No data available</div>
            @endif
        </div>

        {{-- Progress Comparison --}}
        <div class="analytics-card">
            <h2 style="font-size:1rem;font-weight:700;color:var(--vg-text-strong);margin-bottom:1.2rem;">⚖️ This vs Last Month</h2>
            @if(isset($comparison))
                <div style="display:flex;flex-direction:column;gap:0.9rem;">
                    @foreach($comparison as $key => $data)
                        @php
                            $isPositive = strpos($data['trend'], '+') !== false;
                            $trendColor = $isPositive ? '#10b981' : '#f43f5e';
                            $icon = $key == 'workouts' ? '🏋️' : ($key == 'reps' ? '🔄' : '⏱️');
                        @endphp
                        <div style="display:flex;align-items:center;justify-content:space-between;">
                            <div style="display:flex;align-items:center;gap:10px;">
                                <div style="width:36px;height:36px;border-radius:10px;background:var(--vg-sidebar);display:flex;align-items:center;justify-content:center;font-size:1.2rem;">{{ $icon }}</div>
                                <div>
                                    <p style="font-size:.8rem;color:var(--vg-text-strong);text-transform:capitalize;font-weight:600;margin:0 0 2px 0;">{{ $key }}</p>
                                    <p style="font-size:.7rem;color:var(--vg-text-muted);margin:0;">{{ is_numeric($data['current']) ? number_format($data['current']) : $data['current'] }} vs {{ is_numeric($data['previous']) ? number_format($data['previous']) : $data['previous'] }}</p>
                                </div>
                            </div>
                            <div style="font-size:.78rem;font-weight:700;color:{{ $trendColor }};background:{{ $isPositive ? 'rgba(16,185,129,.15)' : 'rgba(244,63,94,.15)' }};padding:4px 8px;border-radius:6px;">
                                {{ $data['trend'] }}
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div style="width:100%;text-align:center;color:var(--vg-text-muted);font-size:.85rem;padding-top:1rem;">No data available</div>
            @endif
        </div>

        {{-- Consistency Score with Ring Animation --}}
        <div class="analytics-card" style="display:flex;flex-direction:column;align-items:center;justify-content:center;text-align:center;background:linear-gradient(135deg, rgba(139,92,246,0.06), rgba(236,72,153,0.02));border-color:rgba(139,92,246,0.25);">
            <h2 style="font-size:1rem;font-weight:700;color:var(--vg-text-strong);margin-bottom:1rem;width:100%;text-align:left;">🎯 Consistency Score</h2>
            <div style="position:relative;width:110px;height:110px;margin-bottom:0.75rem;">
                <svg viewBox="0 0 36 36" style="width:100%;height:100%;transform:rotate(-90deg);filter:drop-shadow(0 0 8px rgba(139,92,246,0.35));">
                    <path d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" fill="none" stroke="rgba(255,255,255,.05)" stroke-width="3"/>
                    <path id="consistencyProgressRing" d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" fill="none" stroke="url(#pinkPurpleGrad)" stroke-width="3.2" stroke-linecap="round" stroke-dasharray="0, 100" style="transition: stroke-dasharray 1.2s cubic-bezier(0.4, 0, 0.2, 1);"/>
                    <defs>
                        <linearGradient id="pinkPurpleGrad" x1="0%" y1="0%" x2="100%" y2="100%">
                            <stop offset="0%" stop-color="#8b5cf6"/>
                            <stop offset="100%" stop-color="#ec4899"/>
                        </linearGradient>
                    </defs>
                </svg>
                <div style="position:absolute;top:50%;left:50%;transform:translate(-50%, -50%);text-align:center;">
                    <div style="font-size:1.8rem;font-weight:900;background:var(--vg-title-gradient);-webkit-background-clip:text;background-clip:text;color:transparent;line-height:1;"><span id="consistencyPercentage">0</span><span style="font-size:1rem;">%</span></div>
                </div>
            </div>
            <p style="font-size:.78rem;color:var(--vg-text-muted);line-height:1.4;margin:0;">You've completed {{ $consistencyScore }}% of your planned workouts. Keep it up!</p>
        </div>

    </div>

    {{-- Achievement Badges and AI Insights Grid --}}
    <div class="analytics-grid-2" style="margin-bottom:2rem;">
        
        {{-- Achievements --}}
        <div class="analytics-card">
            <h2 style="font-size:1rem;font-weight:700;color:var(--vg-text-strong);margin-bottom:1.1rem;">🏆 Achievement Badges</h2>
            <div style="display:grid;grid-template-columns:repeat(2, 1fr);gap:0.75rem;">
                @foreach($achievements as $ach)
                    <div class="badge-card unlocked">
                        <div style="font-size:1.6rem;background:rgba(255,255,255,0.05);width:38px;height:38px;border-radius:50%;display:flex;align-items:center;justify-content:center;box-shadow: 0 0 10px rgba(139,92,246,0.1);">{{ $ach['icon'] }}</div>
                        <div>
                            <p style="font-size:0.78rem;font-weight:700;color:#fff;margin:0;">{{ $ach['title'] }}</p>
                            <p style="font-size:0.65rem;color:var(--vg-text-muted);margin:0;">{{ $ach['desc'] }}</p>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- AI Insights --}}
        <div class="analytics-card" style="background:linear-gradient(135deg,rgba(139,92,246,0.08),rgba(236,72,153,0.03));border-color:rgba(139,92,246,0.28);">
            <h2 style="font-size:1rem;font-weight:700;color:var(--vg-text-strong);margin-bottom:1.1rem;display:flex;align-items:center;gap:8px;">
                <span>🤖</span> AI Performance Insights
            </h2>
            <div style="display:flex;flex-direction:column;gap:0.65rem;">
                @foreach($aiInsights as $insight)
                    <div style="display:flex;align-items:flex-start;gap:8px;font-size:0.78rem;line-height:1.45;color:rgba(255,255,255,0.85);">
                        <span style="color:#c4b5fd;font-weight:bold;">•</span>
                        <span>{{ $insight }}</span>
                    </div>
                @endforeach
            </div>
        </div>

    </div>

</div>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        // Consistency progress ring animation setup
        const targetPercent = {{ $consistencyScore }};
        const ring = document.getElementById('consistencyProgressRing');
        const text = document.getElementById('consistencyPercentage');
        
        setTimeout(() => {
            if (ring) {
                ring.setAttribute('stroke-dasharray', `${targetPercent}, 100`);
            }
        }, 200);

        // Animate counter text
        let current = 0;
        const duration = 1200; 
        const intervalTime = 20; 
        const step = targetPercent / (duration / intervalTime);
        
        const counterInterval = setInterval(() => {
            current += step;
            if (current >= targetPercent) {
                current = targetPercent;
                clearInterval(counterInterval);
            }
            if (text) {
                text.textContent = Math.round(current);
            }
        }, intervalTime);
    });
</script>
@endsection
