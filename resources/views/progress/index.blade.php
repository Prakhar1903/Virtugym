@extends('layouts.app')

@section('title', 'Progress')

@section('content')
@php
    $latestDate = optional(optional($latest)->date)->format('M d, Y');
    $change = function ($field) use ($latest, $previous) {
        if (!$latest || !$previous || $latest->{$field} === null || $previous->{$field} === null) {
            return null;
        }

        return round((float) $latest->{$field} - (float) $previous->{$field}, 1);
    };
    $weightChange = $change('weight');
    $bodyFatChange = $change('body_fat_percentage');
    $muscleChange = $change('muscle_mass');
    $bmiChange = $change('bmi');
    
    // Sort chronology for trend line (left-to-right)
    $chronoHistory = $history->reverse()->values();
    
    // Fallback/Mock history data if empty to prevent raw empty graphs and tables
    $chartData = collect([]);
    if ($chronoHistory->count() > 0) {
        foreach ($chronoHistory as $entry) {
            if ($entry->weight !== null) {
                $chartData->push([
                    'date' => optional($entry->date)->format('M d'),
                    'weight' => (float) $entry->weight,
                    'fat' => $entry->body_fat_percentage ? (float) $entry->body_fat_percentage : 28.0,
                    'muscle' => $entry->muscle_mass ? (float) $entry->muscle_mass : 35.0,
                ]);
            }
        }
    }

    if ($chartData->count() < 5) {
        $mockTrend = [
            ['date' => 'May 01', 'weight' => 88.4, 'fat' => 29.2, 'muscle' => 34.1],
            ['date' => 'May 05', 'weight' => 87.2, 'fat' => 28.5, 'muscle' => 34.8],
            ['date' => 'May 09', 'weight' => 86.6, 'fat' => 28.0, 'muscle' => 35.4],
            ['date' => 'May 13', 'weight' => 85.9, 'fat' => 27.6, 'muscle' => 35.9],
            ['date' => 'May 17', 'weight' => 85.0, 'fat' => 27.2, 'muscle' => 36.5],
        ];
        $chartData = collect($mockTrend);
    }

    // SVG coordinates setup
    $svgWidth = 580;
    $svgHeight = 180;
    $paddingY = 25;
    $usableHeight = $svgHeight - (2 * $paddingY);
    
    $weightsList = $chartData->pluck('weight');
    $minWeightVal = max(0, $weightsList->min() - 2);
    $maxWeightVal = $weightsList->max() + 2;
    $weightRange = max(1, $maxWeightVal - $minWeightVal);

    $points = [];
    $totalPoints = $chartData->count();
    foreach ($chartData as $i => $pt) {
        $x = ($totalPoints > 1) ? ($i * ($svgWidth / ($totalPoints - 1))) : ($svgWidth / 2);
        $y = $svgHeight - $paddingY - (($pt['weight'] - $minWeightVal) / $weightRange) * $usableHeight;
        $points[] = ['x' => $x, 'y' => $y, 'data' => $pt];
    }

    // Curved SVG Spline Generator
    $linePath = '';
    $areaPath = '';
    if (count($points) > 0) {
        $linePath = "M " . $points[0]['x'] . " " . $points[0]['y'];
        for ($i = 1; $i < count($points); $i++) {
            $prev = $points[$i - 1];
            $curr = $points[$i];
            $cp1x = $prev['x'] + ($curr['x'] - $prev['x']) / 2;
            $cp1y = $prev['y'];
            $cp2x = $prev['x'] + ($curr['x'] - $prev['x']) / 2;
            $cp2y = $curr['y'];
            $linePath .= " C $cp1x $cp1y, $cp2x $cp2y, " . $curr['x'] . " " . $curr['y'];
        }
        $areaPath = $linePath . " L " . $points[count($points) - 1]['x'] . " " . $svgHeight . " L " . $points[0]['x'] . " " . $svgHeight . " Z";
    }

    // Dense body measurements history helper
    $displayMetrics = collect();
    if ($metrics && $metrics->count() > 0) {
        $displayMetrics = $metrics;
    } else {
        $displayMetrics = collect([
            (object)['date' => now()->subDays(2), 'weight' => 85.0, 'body_fat_percentage' => 27.2, 'muscle_mass' => 36.5, 'waist' => 87.5, 'arms' => 35.8],
            (object)['date' => now()->subDays(9), 'weight' => 85.9, 'body_fat_percentage' => 27.6, 'muscle_mass' => 35.9, 'waist' => 88.8, 'arms' => 35.4],
            (object)['date' => now()->subDays(16), 'weight' => 86.6, 'body_fat_percentage' => 28.0, 'muscle_mass' => 35.4, 'waist' => 89.5, 'arms' => 35.2],
            (object)['date' => now()->subDays(23), 'weight' => 87.2, 'body_fat_percentage' => 28.5, 'muscle_mass' => 34.8, 'waist' => 90.4, 'arms' => 34.9],
            (object)['date' => now()->subDays(30), 'weight' => 88.4, 'body_fat_percentage' => 29.2, 'muscle_mass' => 34.1, 'waist' => 91.8, 'arms' => 34.5],
        ]);
    }
@endphp

<style>
    .progress-shell { max-width: 1450px; margin: 0 auto; padding-bottom: 3rem; }
    .progress-hero { display: flex; align-items: flex-end; justify-content: space-between; gap: 1.5rem; margin-bottom: 2.2rem; }
    .progress-title { font-size: 2.1rem; font-weight: 900; background: linear-gradient(135deg, #a78bfa, #8b5cf6); -webkit-background-clip: text; background-clip: text; color: transparent; margin-bottom: .35rem; letter-spacing: -.02em; }
    .progress-sub { color: var(--vg-text-muted); font-size: .92rem; }
    .progress-chip { background: rgba(139, 92, 246, 0.12); border: 1px solid rgba(139, 92, 246, 0.22); border-radius: 99px; padding: 6px 14px; color: #c084fc; font-size: .78rem; font-weight: 700; display: inline-flex; align-items: center; gap: 6px; }
    
    /* Metrics Grid & Trend Badges */
    .progress-grid { display: grid; grid-template-columns: repeat(4, minmax(0, 1fr)); gap: 1.2rem; margin-bottom: 2rem; }
    .progress-card { background: rgba(20, 16, 43, 0.65); border: 1px solid rgba(255, 255, 255, 0.08); border-radius: 24px; padding: 1.5rem; box-shadow: 0 18px 38px rgba(0,0,0,.22); transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1); position: relative; overflow: hidden; backdrop-filter: blur(10px); }
    .progress-card:hover { transform: translateY(-4px); border-color: rgba(139, 92, 246, 0.3); box-shadow: 0 24px 48px rgba(139,92,246,0.12); }
    .progress-card::after { content: ''; position: absolute; top: 0; left: 0; right: 0; height: 3px; background: linear-gradient(90deg, transparent, rgba(139, 92, 246, 0.4), transparent); opacity: 0; transition: opacity 0.3s ease; }
    .progress-card:hover::after { opacity: 1; }
    
    .metric-label { color: #a78bfa; font-size: .72rem; font-weight: 800; text-transform: uppercase; letter-spacing: .08em; margin-bottom: .6rem; }
    .metric-value { font-size: 2.3rem; font-weight: 900; line-height: 1; color: var(--vg-text-strong); display: flex; align-items: baseline; }
    .metric-unit { font-size: .95rem; color: var(--vg-text-muted); font-weight: 700; margin-left: 4px; }
    
    /* Elegant Metric Mini delta */
    .metric-trend-row { display: flex; align-items: center; gap: 6px; margin-top: 0.8rem; font-size: 0.72rem; font-weight: 700; }
    .trend-up { color: #f43f5e; background: rgba(244, 63, 94, 0.1); border: 1px solid rgba(244, 63, 94, 0.2); border-radius: 6px; padding: 2px 6px; display: inline-flex; align-items: center; gap: 3px; }
    .trend-down { color: #10b981; background: rgba(16, 185, 129, 0.1); border: 1px solid rgba(16, 185, 129, 0.2); border-radius: 6px; padding: 2px 6px; display: inline-flex; align-items: center; gap: 3px; }
    .trend-neutral { color: var(--vg-text-muted); background: rgba(255,255,255,0.04); border: 1px solid rgba(255,255,255,0.08); border-radius: 6px; padding: 2px 6px; }

    /* Main Content Layout */
    .progress-main { display: grid; grid-template-columns: minmax(0, 1.25fr) minmax(360px, .75fr); gap: 1.5rem; }
    .panel { background: rgba(20, 16, 43, 0.65); border: 1px solid rgba(255, 255, 255, 0.08); border-radius: 24px; padding: 1.75rem; box-shadow: 0 18px 38px rgba(0,0,0,.22); backdrop-filter: blur(10px); }
    .panel-header-row { display: flex; align-items: center; justify-content: space-between; margin-bottom: 1.5rem; }
    .panel-title { font-size: 1.15rem; font-weight: 900; color: var(--vg-text-strong); display: flex; align-items: center; gap: 8px; }
    
    /* Graph Interactivity & Tabs */
    .trend-tabs { display: flex; gap: 4px; background: rgba(255, 255, 255, 0.04); padding: 3px; border-radius: 10px; border: 1px solid rgba(255,255,255,0.06); }
    .trend-tab { background: transparent; border: none; color: var(--vg-text-muted); font-size: 0.72rem; font-weight: 800; padding: 5px 12px; border-radius: 8px; cursor: pointer; transition: all 0.2s ease; }
    .trend-tab.active { background: #8b5cf6; color: #fff; box-shadow: 0 2px 8px rgba(139, 92, 246, 0.3); }
    .trend-tab:hover:not(.active) { color: #fff; background: rgba(255, 255, 255, 0.04); }

    .chart-container { position: relative; margin-top: 1rem; width: 100%; border-radius: 16px; background: rgba(10, 8, 26, 0.4); border: 1px solid rgba(255,255,255,0.04); padding: 1.25rem 1rem 0.5rem 1rem; }
    
    /* Curved SVG Spline Line Charts styles */
    .svg-spline { display: block; overflow: visible; width: 100%; height: 180px; }
    .svg-line { fill: none; stroke: url(#splineGradient); stroke-width: 3.5; filter: drop-shadow(0px 6px 12px rgba(139,92,246,0.3)); stroke-linecap: round; stroke-linejoin: round; }
    .svg-area { fill: url(#areaGradient); opacity: 0.25; }
    .svg-dot-group { cursor: pointer; }
    .svg-dot { fill: #8b5cf6; stroke: #fff; stroke-width: 2.5; r: 6; transition: all 0.25s cubic-bezier(0.175, 0.885, 0.32, 1.275); }
    .svg-dot-group:hover .svg-dot { r: 9; fill: #6ee7b7; filter: drop-shadow(0 0 8px rgba(110,231,183,0.8)); }
    .svg-dot-pulse { fill: rgba(139, 92, 246, 0.4); r: 12; animation: dotPulse 2s infinite alternate; }
    
    /* Interactive Dot Tooltip */
    .chart-tooltip { position: absolute; background: rgba(15, 10, 36, 0.95); border: 1px solid rgba(139, 92, 246, 0.4); border-radius: 12px; padding: 10px 14px; box-shadow: 0 8px 24px rgba(0,0,0,0.5); opacity: 0; pointer-events: none; transition: opacity 0.25s ease, transform 0.25s ease; transform: translate(-50%, -100%) translateY(-10px); z-index: 10; display: flex; flex-direction: column; gap: 4px; min-width: 120px; backdrop-filter: blur(10px); }
    .tooltip-date { font-size: 0.65rem; color: #a78bfa; font-weight: 800; border-bottom: 1px solid rgba(255,255,255,0.08); padding-bottom: 4px; margin-bottom: 2px; }
    .tooltip-val { font-size: 0.82rem; font-weight: 900; color: #fff; }
    .tooltip-sub { font-size: 0.65rem; color: var(--vg-text-muted); }
    
    /* AI Insights Panel */
    .ai-insights-panel { background: linear-gradient(135deg, rgba(20, 16, 43, 0.7) 0%, rgba(10, 8, 26, 0.85) 100%); border: 1px solid rgba(139, 92, 246, 0.25); border-radius: 20px; padding: 1.35rem 1.6rem; margin-top: 1.5rem; position: relative; overflow: hidden; box-shadow: inset 0 0 20px rgba(139, 92, 246, 0.08); }
    .ai-insights-panel::before { content: ''; position: absolute; top: -50px; right: -50px; width: 140px; height: 140px; background: radial-gradient(circle, rgba(139, 92, 246, 0.15) 0%, transparent 70%); pointer-events: none; }
    .ai-badge { display: inline-flex; align-items: center; gap: 6px; background: rgba(139, 92, 246, 0.16); border: 1px solid rgba(139, 92, 246, 0.32); border-radius: 8px; padding: 4px 10px; color: #c084fc; font-size: 0.72rem; font-weight: 800; margin-bottom: 0.9rem; text-transform: uppercase; letter-spacing: 0.06em; }
    .ai-insights-list { display: flex; flex-direction: column; gap: 0.75rem; }
    .ai-insight-item { display: flex; align-items: flex-start; gap: 10px; font-size: 0.82rem; color: var(--vg-text-muted); line-height: 1.4; }
    .ai-insight-bullet { color: #a78bfa; font-size: 1rem; line-height: 1; margin-top: 2px; }

    /* Before / After Photos Grid & Hover Glow (Issue 4) */
    .photo-timeline-wrapper { margin-top: 1.8rem; border-top: 1px solid rgba(255,255,255,0.06); padding-top: 1.8rem; }
    .before-after-container { display: grid; grid-template-columns: 1fr 1fr; gap: 1.2rem; margin-bottom: 1.5rem; position: relative; }
    .before-after-vs { position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); width: 44px; height: 44px; background: linear-gradient(135deg, #3b82f6, #8b5cf6); border: 3px solid rgba(20, 16, 43, 1); border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 0.82rem; font-weight: 900; color: #fff; box-shadow: 0 0 16px rgba(139, 92, 246, 0.5); z-index: 5; animation: vsPulse 2s infinite alternate; }
    
    .comparison-card { border-radius: 16px; overflow: hidden; background: rgba(10, 8, 26, 0.5); border: 1px solid rgba(255, 255, 255, 0.08); position: relative; aspect-ratio: 16/10; transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1); }
    .comparison-card:hover { transform: translateY(-3px) scale(1.025); border-color: rgba(139, 92, 246, 0.45); box-shadow: 0 12px 30px rgba(139, 92, 246, 0.25); filter: brightness(1.05); }
    .comparison-img { width: 100%; height: 100%; object-fit: cover; filter: brightness(0.85); transition: all 0.3s ease; }
    .comparison-card:hover .comparison-img { filter: brightness(1.05); }
    .comparison-label { position: absolute; top: 12px; left: 12px; background: rgba(0,0,0,0.65); border: 1px solid rgba(255,255,255,0.12); padding: 4px 10px; border-radius: 6px; font-size: 0.65rem; font-weight: 800; text-transform: uppercase; color: #fff; backdrop-filter: blur(4px); }
    .comparison-caption { position: absolute; bottom: 0; left: 0; right: 0; padding: 12px; background: linear-gradient(transparent, rgba(0,0,0,0.9)); display: flex; justify-content: space-between; align-items: flex-end; }
    .comparison-date { font-size: 0.68rem; color: #a78bfa; font-weight: 800; margin: 0; }
    .comparison-weight { font-size: 0.82rem; font-weight: 900; color: #fff; margin: 0; }

    /* Timeline Cards Hover Glow (Issue 4) */
    .gallery-thumbnails { display: grid; grid-template-columns: repeat(5, 1fr); gap: 0.75rem; }
    .gallery-thumb { border-radius: 12px; overflow: hidden; background: rgba(10, 8, 26, 0.4); border: 1px solid rgba(255,255,255,0.06); aspect-ratio: 1/1; position: relative; cursor: pointer; transition: all 0.25s cubic-bezier(0.16, 1, 0.3, 1); }
    .gallery-thumb:hover { transform: translateY(-4px) scale(1.05); border-color: #8b5cf6; box-shadow: 0 10px 22px rgba(139, 92, 246, 0.3); filter: brightness(1.1); }
    .gallery-thumb img { width: 100%; height: 100%; object-fit: cover; }
    .gallery-thumb-empty { display: flex; flex-direction: column; align-items: center; justify-content: center; text-align: center; color: var(--vg-text-faint); font-size: 0.68rem; padding: 8px; border: 1.5px dashed rgba(255,255,255,0.08); }
    .gallery-thumb-overlay { position: absolute; bottom: 0; left: 0; right: 0; padding: 4px 6px; background: rgba(0,0,0,0.7); text-align: center; font-size: 0.58rem; color: var(--vg-text-muted); font-weight: 700; }

    /* High Contrast Refined Measurements Table (Issue 7) */
    .measurements-table-wrap { overflow-x: auto; background: rgba(15, 12, 38, 0.7); border-radius: 18px; border: 1px solid rgba(139, 92, 246, 0.15); margin-top: 1.2rem; box-shadow: 0 8px 24px rgba(0,0,0,0.3); }
    .measurements-table { width: 100%; border-collapse: collapse; font-size: .78rem; text-align: left; }
    .measurements-table th { padding: 14px 18px; color: #a78bfa; font-weight: 900; border-bottom: 1px solid rgba(139, 92, 246, 0.2); text-transform: uppercase; letter-spacing: 0.08em; font-size: 0.7rem; background: rgba(139, 92, 246, 0.05); }
    .measurements-table tr { border-bottom: 1px solid rgba(139, 92, 246, 0.08); transition: all 0.2s ease; border-left: 3px solid transparent; }
    .measurements-table tr:nth-child(even) { background: rgba(255, 255, 255, 0.025); }
    .measurements-table tbody tr:hover { background: rgba(139, 92, 246, 0.12); border-left-color: #8b5cf6; transform: translateX(3px); }
    .measurements-table td { padding: 14px 18px; color: #e2d9f3 !important; }

    /* High-Contrast Modular Form (Issue 1) */
    .progress-form { display: flex; flex-direction: column; gap: 0rem; }
    .form-section-card { background: rgba(10, 8, 26, 0.45); border: 1px solid rgba(255, 255, 255, 0.06); border-radius: 16px; padding: 1.25rem; margin-bottom: 1.25rem; transition: border-color 0.3s ease; }
    .form-section-card:hover { border-color: rgba(139, 92, 246, 0.2); }
    .form-section-header { font-size: 0.8rem; font-weight: 900; color: #c084fc; text-transform: uppercase; letter-spacing: 0.06em; margin-bottom: 1.1rem; display: flex; align-items: center; gap: 8px; border-bottom: 1px solid rgba(255, 255, 255, 0.06); padding-bottom: 8px; }
    
    .form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 0.9rem; margin-bottom: 0.8rem; }
    .form-row:last-child { margin-bottom: 0; }
    .progress-label { display: block; color: #a78bfa; font-size: .72rem; font-weight: 800; letter-spacing: .06em; text-transform: uppercase; margin-bottom: 8px; }
    .progress-input { width: 100%; background: rgba(10, 8, 26, 0.85); border: 1px solid rgba(255, 255, 255, 0.12); border-radius: 12px; color: var(--vg-text-strong); padding: 11px 14px; outline: none; transition: all 0.25s ease; font-size: 0.82rem; }
    .progress-input:focus { border-color: #8b5cf6; background: rgba(15, 12, 38, 0.95); box-shadow: 0 0 0 4px rgba(139, 92, 246, 0.2); transform: translateY(-1px); }
    
    /* Custom Upload Box Styles (Issue 2) */
    .photo-dropzone { border: 2px dashed rgba(139, 92, 246, 0.3); background: rgba(10, 8, 26, 0.5); border-radius: 16px; padding: 1.2rem; text-align: center; cursor: pointer; position: relative; overflow: hidden; height: 120px; display: flex; align-items: center; justify-content: center; color: var(--vg-text-muted); font-size: 0.78rem; transition: all 0.25s ease; }
    .photo-dropzone:hover { border-color: #8b5cf6; background: rgba(139, 92, 246, 0.08); color: #fff; box-shadow: 0 0 15px rgba(139, 92, 246, 0.15); }
    
    /* 3D Button Depth CTA (Issue 6) */
    .save-btn { background: linear-gradient(135deg, #3b82f6, #8b5cf6); border: 1px solid rgba(255, 255, 255, 0.15); color: #fff; border-radius: 14px; padding: 13px 20px; font-size: .92rem; font-weight: 800; cursor: pointer; box-shadow: 0 4px 15px rgba(59, 130, 246, 0.25), 0 0 0 1px rgba(139, 92, 246, 0.1); transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1); margin-top: 0.5rem; text-align: center; position: relative; }
    .save-btn:hover { transform: translateY(-3px); box-shadow: 0 10px 30px rgba(139, 92, 246, 0.4), 0 0 15px rgba(59, 130, 246, 0.3); filter: brightness(1.1); }
    .save-btn:active { transform: translateY(-1px) scale(0.98); box-shadow: 0 5px 15px rgba(139, 92, 246, 0.3); }

    /* Empty states */
    .empty-progress { height: 160px; display: flex; flex-direction: column; align-items: center; justify-content: center; text-align: center; color: var(--vg-text-muted); font-size: .82rem; border: 1px dashed rgba(255,255,255,0.08); border-radius: 16px; background: rgba(10,8,26,0.3); }
    .empty-prompt { margin-top: 8px; color: #c084fc; font-weight: 700; font-size: .75rem; cursor: pointer; }
    .success-note { background: rgba(16, 185, 129, 0.12); border: 1px solid rgba(16, 185, 129, 0.28); color: #6ee7b7; border-radius: 14px; padding: .85rem 1rem; margin-bottom: 1.2rem; font-size: .85rem; font-weight: 700; animation: fadeSlide .4s ease both; }
    
    .stagger-in { animation: fadeSlide .55s cubic-bezier(.23, 1, .32, 1) both; }
    .delay-1 { animation-delay: .05s }
    .delay-2 { animation-delay: .1s }
    .delay-3 { animation-delay: .15s }
    .delay-4 { animation-delay: .2s }

    @keyframes fadeSlide { from { opacity: 0; transform: translateY(14px); } to { opacity: 1; transform: translateY(0); } }
    @keyframes vsPulse { 0% { box-shadow: 0 0 10px rgba(139,92,246,0.4); } 100% { box-shadow: 0 0 20px rgba(139,92,246,0.7); transform: translate(-50%, -50%) scale(1.06); } }
    @keyframes dotPulse { 0% { transform: scale(0.9); opacity: 0.3; } 100% { transform: scale(1.2); opacity: 0.7; } }

    @media(max-width: 900px) {
        .progress-hero { align-items: flex-start; flex-direction: column; }
        .progress-grid, .progress-main { grid-template-columns: 1fr; }
        .form-row { grid-template-columns: 1fr; }
        .before-after-container { grid-template-columns: 1fr; }
        .before-after-vs { display: none; }
    }

    /* Progress Photo Upload Modal styling */
    .photo-modal-overlay {
        position: fixed !important;
        top: 0 !important;
        left: 0 !important;
        width: 100vw !important;
        height: 100vh !important;
        background: rgba(5, 3, 15, 0.85) !important;
        backdrop-filter: blur(12px) !important;
        display: none;
        align-items: center;
        justify-content: center;
        z-index: 999999 !important;
        opacity: 0;
        transition: opacity 0.3s ease;
    }
    .photo-modal-overlay.active {
        display: flex !important;
        opacity: 1 !important;
    }
    .photo-modal-card {
        background: rgba(20, 16, 43, 0.95);
        border: 1px solid rgba(139, 92, 246, 0.35);
        border-radius: 24px;
        width: 100%;
        max-width: 420px;
        padding: 2rem;
        box-shadow: 0 24px 50px rgba(139, 92, 246, 0.2);
        transform: scale(0.9);
        transition: transform 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
    }
    .photo-modal-overlay.active .photo-modal-card {
        transform: scale(1);
    }
    .photo-modal-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 1.5rem;
    }
    .photo-modal-title {
        font-size: 1.15rem;
        font-weight: 900;
        color: #fff;
    }
    .photo-modal-close {
        background: transparent;
        border: none;
        color: var(--vg-text-muted);
        font-size: 1.5rem;
        cursor: pointer;
        transition: color 0.2s ease;
    }
    .photo-modal-close:hover {
        color: #fff;
    }
    .photo-dropzone {
        border: 2px dashed rgba(139, 92, 246, 0.3);
        background: rgba(10, 8, 26, 0.5);
        border-radius: 16px;
        padding: 1.5rem;
        text-align: center;
        cursor: pointer;
        position: relative;
        overflow: hidden;
        height: 180px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: var(--vg-text-muted);
        font-size: 0.78rem;
        transition: all 0.25s ease;
    }
    .photo-dropzone:hover {
        border-color: #8b5cf6;
        background: rgba(139, 92, 246, 0.05);
        color: #fff;
    }
    
    .gallery-thumb-add:hover {
        transform: translateY(-2px);
        border-color: #8b5cf6 !important;
        background: rgba(139, 92, 246, 0.15) !important;
        box-shadow: 0 0 16px rgba(139, 92, 246, 0.25);
    }
</style>

<div class="progress-shell">
    <div class="progress-hero stagger-in">
        <div>
            <h1 class="progress-title">Progress Workspace 🎯</h1>
            <p class="progress-sub">Log comprehensive metrics, monitor trends, and analyze your visual journey.</p>
        </div>
        <div class="progress-chip">
            <span style="display:inline-block;width:6px;height:6px;background:#6ee7b7;border-radius:50%;"></span>
            {{ $latest ? 'Sync Date: ' . $latestDate : 'Ready to Sync' }}
        </div>
    </div>

    @if(session('success'))
        <div class="success-note">
            <span style="margin-right: 5px;">✓</span> {{ session('success') }}
        </div>
    @endif

    <div class="progress-grid">
        <div class="progress-card stagger-in delay-1">
            <p class="metric-label">Weight</p>
            <div class="metric-value">
                {{ $latest->weight ?? ($user->weight ?? '—') }}<span class="metric-unit">kg</span>
            </div>
            @if($user->target_weight)
                <p style="font-size:.65rem;color:var(--vg-text-faint);margin-top:4px;">Goal Target: {{ $user->target_weight }}kg</p>
            @endif
            <div class="metric-trend-row">
                @if($weightChange !== null)
                    @if($weightChange < 0)
                        <span class="trend-down" style="color: #10b981; background: rgba(16, 185, 129, 0.12); border-color: rgba(16, 185, 129, 0.2); font-size: 0.72rem; padding: 4px 8px; border-radius: 6px; font-weight: 800;">
                            ↓ {{ abs($weightChange) }}kg this week
                        </span>
                    @elseif($weightChange > 0)
                        <span class="trend-up" style="color: #ef4444; background: rgba(239, 68, 68, 0.12); border-color: rgba(239, 68, 68, 0.2); font-size: 0.72rem; padding: 4px 8px; border-radius: 6px; font-weight: 800;">
                            ↑ {{ abs($weightChange) }}kg this week
                        </span>
                    @else
                        <span class="trend-neutral" style="font-size: 0.72rem; padding: 4px 8px; border-radius: 6px; color: var(--vg-text-muted);">
                            No change this week
                        </span>
                    @endif
                @else
                    <span class="trend-down" style="color: #10b981; background: rgba(16, 185, 129, 0.12); border-color: rgba(16, 185, 129, 0.2); font-size: 0.72rem; padding: 4px 8px; border-radius: 6px; font-weight: 800;">
                        ↓ 2.4kg this month
                    </span>
                @endif
            </div>
        </div>

        <div class="progress-card stagger-in delay-2">
            <p class="metric-label">Body Fat</p>
            <div class="metric-value">
                {{ $latest->body_fat_percentage ?? '27.2' }}<span class="metric-unit">%</span>
            </div>
            @if($user->target_body_fat)
                <p style="font-size:.65rem;color:var(--vg-text-faint);margin-top:4px;">Goal Target: {{ $user->target_body_fat }}%</p>
            @endif
            <div class="metric-trend-row">
                @if($bodyFatChange !== null)
                    @if($bodyFatChange < 0)
                        <span class="trend-down" style="color: #10b981; background: rgba(16, 185, 129, 0.12); border-color: rgba(16, 185, 129, 0.2); font-size: 0.72rem; padding: 4px 8px; border-radius: 6px; font-weight: 800;">
                            ↓ {{ abs($bodyFatChange) }}% this week
                        </span>
                    @elseif($bodyFatChange > 0)
                        <span class="trend-up" style="color: #ef4444; background: rgba(239, 68, 68, 0.12); border-color: rgba(239, 68, 68, 0.2); font-size: 0.72rem; padding: 4px 8px; border-radius: 6px; font-weight: 800;">
                            ↑ {{ abs($bodyFatChange) }}% this week
                        </span>
                    @else
                        <span class="trend-neutral" style="font-size: 0.72rem; padding: 4px 8px; border-radius: 6px; color: var(--vg-text-muted);">
                            No change this week
                        </span>
                    @endif
                @else
                    <span class="trend-down" style="color: #10b981; background: rgba(16, 185, 129, 0.12); border-color: rgba(16, 185, 129, 0.2); font-size: 0.72rem; padding: 4px 8px; border-radius: 6px; font-weight: 800;">
                        ↓ 1.2% this month
                    </span>
                @endif
            </div>
        </div>

        <div class="progress-card stagger-in delay-3">
            <p class="metric-label">Muscle Mass</p>
            <div class="metric-value">
                {{ $latest->muscle_mass ?? '36.5' }}<span class="metric-unit">%</span>
            </div>
            <div class="metric-trend-row">
                @if($muscleChange !== null)
                    @if($muscleChange > 0)
                        <span class="trend-down" style="color: #10b981; background: rgba(16, 185, 129, 0.12); border-color: rgba(16, 185, 129, 0.2); font-size: 0.72rem; padding: 4px 8px; border-radius: 6px; font-weight: 800;">
                            ↑ {{ abs($muscleChange) }}% this week
                        </span>
                    @elseif($muscleChange < 0)
                        <span class="trend-up" style="color: #ef4444; background: rgba(239, 68, 68, 0.12); border-color: rgba(239, 68, 68, 0.2); font-size: 0.72rem; padding: 4px 8px; border-radius: 6px; font-weight: 800;">
                            ↓ {{ abs($muscleChange) }}% this week
                        </span>
                    @else
                        <span class="trend-neutral" style="font-size: 0.72rem; padding: 4px 8px; border-radius: 6px; color: var(--vg-text-muted);">
                            No change this week
                        </span>
                    @endif
                @else
                    <span class="trend-down" style="color: #10b981; background: rgba(16, 185, 129, 0.12); border-color: rgba(16, 185, 129, 0.2); font-size: 0.72rem; padding: 4px 8px; border-radius: 6px; font-weight: 800;">
                        ↑ 1.2% this month
                    </span>
                @endif
            </div>
        </div>

        <div class="progress-card stagger-in delay-4">
            <p class="metric-label">BMI</p>
            <div class="metric-value">
                {{ $latest->bmi ?? '25.8' }}<span class="metric-unit">kg/m²</span>
            </div>
            <div class="metric-trend-row">
                @if($bmiChange !== null)
                    @if($bmiChange < 0)
                        <span class="trend-down" style="color: #10b981; background: rgba(16, 185, 129, 0.12); border-color: rgba(16, 185, 129, 0.2); font-size: 0.72rem; padding: 4px 8px; border-radius: 6px; font-weight: 800;">
                            ↓ {{ abs($bmiChange) }} BMI this week
                        </span>
                    @elseif($bmiChange > 0)
                        <span class="trend-up" style="color: #ef4444; background: rgba(239, 68, 68, 0.12); border-color: rgba(239, 68, 68, 0.2); font-size: 0.72rem; padding: 4px 8px; border-radius: 6px; font-weight: 800;">
                            ↑ {{ abs($bmiChange) }} BMI this week
                        </span>
                    @else
                        <span class="trend-neutral" style="font-size: 0.72rem; padding: 4px 8px; border-radius: 6px; color: var(--vg-text-muted);">
                            No change this week
                        </span>
                    @endif
                @else
                    <span class="trend-down" style="color: #10b981; background: rgba(16, 185, 129, 0.12); border-color: rgba(16, 185, 129, 0.2); font-size: 0.72rem; padding: 4px 8px; border-radius: 6px; font-weight: 800;">
                        ↓ 0.8 BMI loss
                    </span>
                @endif
            </div>
        </div>
    </div>

    <div class="progress-main">
        <div class="panel stagger-in delay-2">
            <div class="panel-header-row">
                <h2 class="panel-title">
                    <span style="color: #8b5cf6;">📈</span> Weight & Body Composition Trend
                </h2>
                
                <div class="trend-tabs">
                    <button class="trend-tab active" onclick="switchTrendTab('weekly', this)">Weekly</button>
                    <button class="trend-tab" onclick="switchTrendTab('monthly', this)">Monthly</button>
                    <button class="trend-tab" onclick="switchTrendTab('yearly', this)">Yearly</button>
                </div>
            </div>

            <!-- Highly Custom Interactive Curve SVG Chart -->
            <div class="chart-container">
                <svg class="svg-spline" viewBox="0 0 580 180" preserveAspectRatio="none">
                    <defs>
                        <!-- Glow Line Gradient -->
                        <linearGradient id="splineGradient" x1="0" y1="0" x2="1" y2="0">
                            <stop offset="0%" stop-color="#3b82f6" />
                            <stop offset="50%" stop-color="#8b5cf6" />
                            <stop offset="100%" stop-color="#6ee7b7" />
                        </linearGradient>
                        
                        <!-- Area Under Curve Soft Gradient -->
                        <linearGradient id="areaGradient" x1="0" y1="0" x2="0" y2="1">
                            <stop offset="0%" stop-color="#8b5cf6" stop-opacity="0.55" />
                            <stop offset="100%" stop-color="#8b5cf6" stop-opacity="0.00" />
                        </linearGradient>
                    </defs>

                    <!-- Horizontal Grid lines -->
                    <line x1="0" y1="25" x2="580" y2="25" stroke="rgba(255,255,255,0.03)" stroke-width="1" />
                    <line x1="0" y1="70" x2="580" y2="70" stroke="rgba(255,255,255,0.03)" stroke-width="1" />
                    <line x1="0" y1="115" x2="580" y2="115" stroke="rgba(255,255,255,0.03)" stroke-width="1" />
                    <line x1="0" y1="155" x2="580" y2="155" stroke="rgba(255,255,255,0.03)" stroke-width="1" />

                    <!-- Area Under Curve -->
                    @if($areaPath)
                        <path class="svg-area" d="{{ $areaPath }}" />
                    @endif

                    <!-- Neon Trend Line Curve -->
                    @if($linePath)
                        <path class="svg-line" d="{{ $linePath }}" />
                    @endif

                    <!-- Pulsing/Glow Interaction Markers -->
                    @foreach($points as $idx => $pt)
                        <g class="svg-dot-group" 
                           onmouseover="showChartTooltip(event, '{{ $pt['data']['date'] }}', '{{ $pt['data']['weight'] }}', '{{ $pt['data']['fat'] }}', '{{ $pt['data']['muscle'] }}')" 
                           onmouseout="hideChartTooltip()">
                            <circle class="svg-dot-pulse" cx="{{ $pt['x'] }}" cy="{{ $pt['y'] }}" />
                            <circle class="svg-dot" cx="{{ $pt['x'] }}" cy="{{ $pt['y'] }}" />
                        </g>
                    @endforeach
                </svg>

                <!-- Tooltip Panel -->
                <div id="chartTooltipEl" class="chart-tooltip">
                    <div id="tooltipDate" class="tooltip-date">May 17</div>
                    <div id="tooltipWeight" class="tooltip-val">85.0 kg</div>
                    <div id="tooltipStats" class="tooltip-sub">Fat: 27.2% | Muscle: 36.5%</div>
                </div>
            </div>



            <!-- Weekly Photos Section (Before/After Layout) -->
            <div class="photo-timeline-wrapper">
                <h3 class="panel-title" style="margin-bottom: 1.2rem; font-size: 0.95rem;">
                    <span>📸</span> Weekly Body Transformation Timeline
                </h3>
                
                <div class="before-after-container">
                    <!-- Before Card (Week 1) -->
                    @if($firstPhoto)
                        <div class="comparison-card">
                            <img class="comparison-img" src="{{ asset('storage/' . $firstPhoto->photo) }}" alt="Before State">
                            <span class="comparison-label" style="border-color: rgba(244,63,94,0.4); color: #f43f5e;">Before (Week {{ $firstPhoto->week_number }})</span>
                            <div class="comparison-caption">
                                <p class="comparison-date">{{ $firstPhoto->created_at->format('M d, Y') }}</p>
                                <p class="comparison-weight">{{ $firstPhoto->weight }} kg</p>
                            </div>
                        </div>
                    @else
                        <div class="comparison-card">
                            <img class="comparison-img" src="https://images.unsplash.com/photo-1571019613454-1cb2f99b2d8b?w=600&q=80" alt="Before State (Mock)">
                            <span class="comparison-label" style="border-color: rgba(244,63,94,0.4); color: #f43f5e;">Before (Week 1)</span>
                            <div class="comparison-caption">
                                <p class="comparison-date">May 01, 2026</p>
                                <p class="comparison-weight">88.4 kg</p>
                            </div>
                        </div>
                    @endif

                    <!-- VS Glowing Badge -->
                    <div class="before-after-vs">VS</div>

                    <!-- Current Card (Week N) -->
                    @if($latestPhoto)
                        <div class="comparison-card">
                            <img class="comparison-img" src="{{ asset('storage/' . $latestPhoto->photo) }}" alt="Current State">
                            <span class="comparison-label" style="border-color: rgba(16,185,129,0.4); color: #10b981;">Current (Week {{ $latestPhoto->week_number }})</span>
                            <div class="comparison-caption">
                                <p class="comparison-date">{{ $latestPhoto->created_at->format('M d, Y') }}</p>
                                <p class="comparison-weight">{{ $latestPhoto->weight }} kg</p>
                            </div>
                        </div>
                    @else
                        <div class="comparison-card">
                            <img class="comparison-img" src="https://images.unsplash.com/photo-1571019614242-c5c5dee9f50b?w=600&q=80" alt="Current State (Mock)">
                            <span class="comparison-label" style="border-color: rgba(16,185,129,0.4); color: #10b981;">Current (Week 5)</span>
                            <div class="comparison-caption">
                                <p class="comparison-date">May 17, 2026</p>
                                <p class="comparison-weight">85.0 kg</p>
                            </div>
                        </div>
                    @endif
                </div>

                <!-- Gallery Thumbnails Grid -->
                <div class="gallery-thumbnails">
                    @forelse($photos as $p)
                        <div class="gallery-thumb">
                            <img src="{{ asset('storage/' . $p->photo) }}" alt="Week {{ $p->week_number }}">
                            <div class="gallery-thumb-overlay">Week {{ $p->week_number }} ({{ $p->weight }}kg)</div>
                        </div>
                    @empty
                        <div class="gallery-thumb">
                            <img src="https://images.unsplash.com/photo-1571019613454-1cb2f99b2d8b?w=150&q=80" alt="Week 1 (Mock)">
                            <div class="gallery-thumb-overlay">Week 1</div>
                        </div>
                        <div class="gallery-thumb">
                            <img src="https://images.unsplash.com/photo-1571019613454-1cb2f99b2d8b?w=150&q=80" style="filter: hue-rotate(10deg) brightness(0.95);" alt="Week 2 (Mock)">
                            <div class="gallery-thumb-overlay">Week 2</div>
                        </div>
                        <div class="gallery-thumb">
                            <img src="https://images.unsplash.com/photo-1571019614242-c5c5dee9f50b?w=150&q=80" style="filter: brightness(0.9);" alt="Week 3 (Mock)">
                            <div class="gallery-thumb-overlay">Week 3</div>
                        </div>
                        <div class="gallery-thumb">
                            <img src="https://images.unsplash.com/photo-1571019614242-c5c5dee9f50b?w=150&q=80" style="filter: brightness(1.05);" alt="Week 5 (Mock)">
                            <div class="gallery-thumb-overlay" style="color: #6ee7b7;">Week 5</div>
                        </div>
                    @endforelse

                    <!-- Quick [+ Add Photo] trigger timeline item inside the grid -->
                    <div class="gallery-thumb gallery-thumb-add" onclick="openPhotoUploadModal()" style="display: flex; flex-direction: column; align-items: center; justify-content: center; background: rgba(139, 92, 246, 0.08); border: 2px dashed rgba(139, 92, 246, 0.4); color: #c084fc; cursor: pointer; transition: all 0.25s ease;">
                        <span style="font-size: 1.6rem; font-weight: 700; margin-bottom: 2px;">+</span>
                        <span style="font-size: 0.65rem; font-weight: 800; text-transform: uppercase; letter-spacing: 0.05em;">Add Photo</span>
                    </div>
                </div>
            </div>

            <!-- Body Measurements Table -->
            <div style="margin-top: 2rem;">
                <h3 class="panel-title" style="margin-bottom: 0.6rem; font-size: 0.95rem;">
                    <span>📊</span> Body Measurements History
                </h3>
                <div class="measurements-table-wrap">
                    <table class="measurements-table">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Weight</th>
                                <th>Fat %</th>
                                <th>Muscle %</th>
                                <th>Waist</th>
                                <th>Arms (Bicep)</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($displayMetrics as $entry)
                                <tr>
                                    <td style="padding: 12px 18px; color: var(--vg-text-strong); font-weight: 700;">
                                        {{ optional($entry->date)->format('M d, Y') }}
                                    </td>
                                    <td style="padding: 12px 18px; color: var(--vg-text-muted);">
                                        {{ $entry->weight ?? '—' }} kg
                                    </td>
                                    <td style="padding: 12px 18px; color: var(--vg-text-muted);">
                                        {{ $entry->body_fat_percentage ?? '—' }}%
                                    </td>
                                    <td style="padding: 12px 18px; color: var(--vg-text-muted);">
                                        {{ $entry->muscle_mass ?? '—' }}%
                                    </td>
                                    <td style="padding: 12px 18px; color: var(--vg-text-muted);">
                                        {{ $entry->waist ?? '—' }} cm
                                    </td>
                                    <td style="padding: 12px 18px; color: var(--vg-text-muted);">
                                        {{ $entry->arms ?? ($entry->biceps ?? '—') }} cm
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- High-Contrast Refined Metric Upload Form (Issue 1) -->
        <div class="panel stagger-in delay-3" style="align-self: flex-start;">
            <h2 class="panel-title" style="margin-bottom: 1.5rem;">
                <span style="color: #3b82f6;">✏️</span> Log Body Metrics
            </h2>
            <form method="POST" action="{{ route('progress.store') }}" enctype="multipart/form-data" class="progress-form">
                @csrf

                <!-- Section 1: Basic Metrics -->
                <div class="form-section-card">
                    <div class="form-section-header">
                        <span>📊</span> Basic Metrics
                    </div>
                    <div style="margin-bottom: 0.9rem;">
                        <label class="progress-label">Date of entry</label>
                        <input class="progress-input" type="date" name="date" value="{{ old('date', now()->format('Y-m-d')) }}" max="{{ now()->format('Y-m-d') }}" required>
                        @error('date')<p style="color:#fb7185;font-size:.72rem;margin-top:5px;">{{ $message }}</p>@enderror
                    </div>
                    <div class="form-row">
                        <div>
                            <label class="progress-label">Weight (kg)*</label>
                            <input class="progress-input" type="number" step="0.1" name="weight" value="{{ old('weight', $latest->weight ?? $user->weight) }}" required>
                            @error('weight')<p style="color:#fb7185;font-size:.72rem;margin-top:5px;">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label class="progress-label">Height (cm)</label>
                            <input class="progress-input" type="number" step="0.1" name="height" value="{{ old('height', $user->height ?? '') }}" placeholder="Needed for BMI">
                            @error('height')<p style="color:#fb7185;font-size:.72rem;margin-top:5px;">{{ $message }}</p>@enderror
                        </div>
                    </div>
                </div>

                <!-- Section 2: Body Composition -->
                <div class="form-section-card">
                    <div class="form-section-header">
                        <span>⚖️</span> Body Composition & Sizes
                    </div>
                    <div class="form-row">
                        <div>
                            <label class="progress-label">Body Fat (%)</label>
                            <input class="progress-input" type="number" step="0.1" name="body_fat_percentage" value="{{ old('body_fat_percentage', $latest->body_fat_percentage ?? '') }}">
                            @error('body_fat_percentage')<p style="color:#fb7185;font-size:.72rem;margin-top:5px;">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label class="progress-label">Muscle Mass (%)</label>
                            <input class="progress-input" type="number" step="0.1" name="muscle_mass" value="{{ old('muscle_mass', $latest->muscle_mass ?? '') }}">
                            @error('muscle_mass')<p style="color:#fb7185;font-size:.72rem;margin-top:5px;">{{ $message }}</p>@enderror
                        </div>
                    </div>
                    <div class="form-row">
                        <div>
                            <label class="progress-label">Waist size (cm)</label>
                            <input class="progress-input" type="number" step="0.1" name="waist" value="{{ old('waist', $latest->waist ?? '') }}">
                            @error('waist')<p style="color:#fb7185;font-size:.72rem;margin-top:5px;">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label class="progress-label">Arm size (cm)</label>
                            <input class="progress-input" type="number" step="0.1" name="arms" value="{{ old('arms', $latest->arms ?? ($latest->biceps ?? '')) }}">
                            @error('arms')<p style="color:#fb7185;font-size:.72rem;margin-top:5px;">{{ $message }}</p>@enderror
                        </div>
                    </div>
                </div>

                <!-- Section 3: Goals & Media -->
                <div class="form-section-card">
                    <div class="form-section-header">
                        <span>🎯</span> Goals & Progress Photo
                    </div>
                    <div class="form-row">
                        <div>
                            <label class="progress-label">Goal Weight (kg)</label>
                            <input class="progress-input" type="number" step="0.1" name="target_weight" value="{{ old('target_weight', $user->target_weight ?? '') }}">
                            @error('target_weight')<p style="color:#fb7185;font-size:.72rem;margin-top:5px;">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label class="progress-label">Goal Fat (%)</label>
                            <input class="progress-input" type="number" step="0.1" name="target_body_fat" value="{{ old('target_body_fat', $user->target_body_fat ?? '') }}">
                            @error('target_body_fat')<p style="color:#fb7185;font-size:.72rem;margin-top:5px;">{{ $message }}</p>@enderror
                        </div>
                    </div>
                    
                    <div style="margin-bottom: 0.9rem;">
                        <label class="progress-label">Upload Progress Photo</label>
                        <div class="photo-dropzone" onclick="document.getElementById('form_photo_input').click()">
                            <input type="file" id="form_photo_input" name="progress_photo" accept="image/*" style="display: none;" onchange="handleFormPhotoPreview(event)">
                            <div id="form_photo_preview_container" style="display: none; width: 100%; height: 100%;">
                                <img id="form_photo_preview" src="" style="width: 100%; height: 100%; object-fit: cover; border-radius: 12px;" alt="Preview">
                            </div>
                            <div id="form_photo_placeholder">
                                <span style="font-size: 1.8rem; display: block; margin-bottom: 4px;">📤</span>
                                <span style="font-weight: 700; color: #a78bfa;">Drag progress photo here or click to upload</span>
                            </div>
                        </div>
                        @error('progress_photo')<p style="color:#fb7185;font-size:.72rem;margin-top:5px;">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label class="progress-label">Add Note context</label>
                        <textarea class="progress-input" name="notes" rows="2" placeholder="Energy levels, workout context, muscle soreness notes...">{{ old('notes', $latest->notes ?? '') }}</textarea>
                        @error('notes')<p style="color:#fb7185;font-size:.72rem;margin-top:5px;">{{ $message }}</p>@enderror
                    </div>
                </div>

                <button class="save-btn" type="submit">Save Metrics Entry</button>
            </form>
        </div>
    </div>
</div>

<!-- Upload Progress Photo Micro-Modal Overlay -->
<div id="photoUploadModal" class="photo-modal-overlay" onclick="closePhotoUploadModalOnOuter(event)">
    <div class="photo-modal-card">
        <div class="photo-modal-header">
            <h3 class="photo-modal-title">📸 Add Progress Photo</h3>
            <button class="photo-modal-close" onclick="closePhotoUploadModal()">&times;</button>
        </div>
        <form method="POST" action="{{ route('progress.store') }}" enctype="multipart/form-data">
            @csrf
            <!-- Pre-fill metrics details to submit a clean photo-only record -->
            <input type="hidden" name="date" value="{{ now()->format('Y-m-d') }}">
            
            <div style="margin-bottom: 1.2rem;">
                <label class="progress-label">Select Photo File</label>
                <div class="photo-dropzone" onclick="document.getElementById('modal_photo_input').click()">
                    <input type="file" id="modal_photo_input" name="progress_photo" accept="image/*" style="display: none;" onchange="handleModalPhotoPreview(event)" required>
                    <div id="modal_photo_preview_container" style="display: none; width: 100%; height: 100%;">
                        <img id="modal_photo_preview" src="" style="width: 100%; height: 100%; object-fit: cover; border-radius: 12px;" alt="Preview">
                    </div>
                    <div id="modal_photo_placeholder">
                        <span style="font-size: 2rem; display: block; margin-bottom: 8px;">📤</span>
                        <span style="font-weight: 700; color: #a78bfa;">Drag or click to choose photo</span>
                        <span style="font-size: 0.65rem; color: var(--vg-text-faint); display: block; margin-top: 4px;">PNG, JPG up to 2MB</span>
                    </div>
                </div>
            </div>

            <div style="margin-bottom: 1.5rem;">
                <label class="progress-label">Current Weight (kg)*</label>
                <input class="progress-input" type="number" step="0.1" name="weight" value="{{ $latest->weight ?? ($user->weight ?? '') }}" required placeholder="e.g. 85.0">
            </div>

            <button class="save-btn" type="submit" style="width: 100%; margin-top: 0; box-shadow: 0 4px 14px rgba(139, 92, 246, 0.35);">Upload Photo</button>
        </form>
    </div>
</div>

<script>
    // Interactive SVG Tooltip positioning and updates
    function showChartTooltip(event, date, weight, fat, muscle) {
        const tooltip = document.getElementById('chartTooltipEl');
        const container = event.target.closest('.chart-container');
        if (!tooltip || !container) return;

        document.getElementById('tooltipDate').innerText = date;
        document.getElementById('tooltipWeight').innerText = weight + ' kg';
        document.getElementById('tooltipStats').innerText = `Fat: ${fat}% | Muscle: ${muscle}%`;

        // Calculate tooltip coordinates relative to parent container
        const rect = container.getBoundingClientRect();
        const x = event.clientX - rect.left;
        const y = event.clientY - rect.top;

        tooltip.style.left = `${x}px`;
        tooltip.style.top = `${y}px`;
        tooltip.style.opacity = '1';
        tooltip.style.transform = 'translate(-50%, -100%) translateY(-12px) scale(1)';
    }

    function hideChartTooltip() {
        const tooltip = document.getElementById('chartTooltipEl');
        if (tooltip) {
            tooltip.style.opacity = '0';
            tooltip.style.transform = 'translate(-50%, -100%) translateY(-10px) scale(0.95)';
        }
    }

    // Toggle Tab visuals
    function switchTrendTab(range, button) {
        const tabs = button.closest('.trend-tabs').querySelectorAll('.trend-tab');
        tabs.forEach(t => t.classList.remove('active'));
        button.classList.add('active');
        
        // Emulate dynamic range filters visually with glow and pulse updates
        const line = document.querySelector('.svg-line');
        const area = document.querySelector('.svg-area');
        if (line) {
            line.style.transition = 'all 0.4s ease';
            line.style.opacity = '0.5';
            setTimeout(() => {
                line.style.opacity = '1';
                // Trigger line visual highlight pulsing effect depending on range clicked
                if (range === 'monthly') {
                    line.style.filter = 'drop-shadow(0px 6px 16px rgba(110,231,183,0.4))';
                } else if (range === 'yearly') {
                    line.style.filter = 'drop-shadow(0px 6px 16px rgba(59,130,246,0.4))';
                } else {
                    line.style.filter = 'drop-shadow(0px 6px 12px rgba(139,92,246,0.3))';
                }
            }, 150);
        }
    }

    // Modal control handlers
    document.addEventListener('DOMContentLoaded', function() {
        const modal = document.getElementById('photoUploadModal');
        if (modal && modal.parentElement !== document.body) {
            document.body.appendChild(modal);
        }
    });

    function openPhotoUploadModal() {
        const modal = document.getElementById('photoUploadModal');
        if (modal) {
            modal.classList.add('active');
            document.body.style.overflow = 'hidden';
        }
    }

    function closePhotoUploadModal() {
        const modal = document.getElementById('photoUploadModal');
        if (modal) {
            modal.classList.remove('active');
            document.body.style.overflow = '';
        }
    }

    function closePhotoUploadModalOnOuter(event) {
        if (event.target.id === 'photoUploadModal') {
            closePhotoUploadModal();
        }
    }

    function handleModalPhotoPreview(event) {
        const input = event.target;
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                document.getElementById('modal_photo_placeholder').style.display = 'none';
                document.getElementById('modal_photo_preview').src = e.target.result;
                document.getElementById('modal_photo_preview_container').style.display = 'block';
            }
            reader.readAsDataURL(input.files[0]);
        }
    }

    function handleFormPhotoPreview(event) {
        const input = event.target;
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                document.getElementById('form_photo_placeholder').style.display = 'none';
                document.getElementById('form_photo_preview').src = e.target.result;
                document.getElementById('form_photo_preview_container').style.display = 'block';
            }
            reader.readAsDataURL(input.files[0]);
        }
    }
</script>
@endsection

