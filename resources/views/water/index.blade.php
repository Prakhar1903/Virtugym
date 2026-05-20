@extends('layouts.app')

@section('title', 'Water Intake')

@section('content')
<style>
    .water-circle-container {
        position: relative;
        width: 200px;
        height: 200px;
        margin: 0 auto 1.5rem;
        border-radius: 50%;
        background: rgba(255, 255, 255, 0.02);
        border: 3px solid rgba(255, 255, 255, 0.05);
        box-shadow: inset 0 0 20px rgba(0, 0, 0, 0.6), 0 0 15px rgba(59, 130, 246, 0.1);
        overflow: hidden;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.5s cubic-bezier(0.4, 0, 0.2, 1);
    }
    
    .water-circle-container::before {
        content: '';
        position: absolute;
        inset: -3px;
        border-radius: 50%;
        background: linear-gradient(135deg, #3b82f6, #8b5cf6);
        -webkit-mask: linear-gradient(#fff 0 0) content-box, linear-gradient(#fff 0 0);
        -webkit-mask-composite: xor;
        mask-composite: exclude;
        opacity: {{ max(0.2, $percentage / 100) }};
        z-index: 2;
        transition: all 0.5s ease;
    }
    
    .milestone-glow {
        border-color: transparent !important;
        box-shadow: 0 0 35px rgba(16, 185, 129, 0.45), inset 0 0 25px rgba(16, 185, 129, 0.3) !important;
        animation: gold-shimmer 3s infinite alternate;
    }
    
    .milestone-glow::before {
        background: linear-gradient(135deg, #10b981, #34d399) !important;
        opacity: 1 !important;
    }
    
    @keyframes gold-shimmer {
        0% { transform: scale(1); }
        100% { transform: scale(1.025); }
    }
    
    .water-bg {
        position: absolute;
        bottom: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: linear-gradient(to top, rgba(59, 130, 246, 0.7), rgba(139, 92, 246, 0.45));
        transition: transform 1.2s cubic-bezier(0.4, 0, 0.2, 1);
        z-index: 1;
    }
    
    .water-wave {
        position: absolute;
        width: 200%;
        height: 200%;
        top: -188%;
        left: -50%;
        border-radius: 38%;
        background: rgba(8, 8, 26, 0.95);
        animation: spin-wave 12s infinite linear;
    }
    
    .wave-1 {
        opacity: 0.85;
        animation-duration: 10s;
    }
    
    .wave-2 {
        border-radius: 35%;
        background: rgba(8, 8, 26, 0.85);
        animation-duration: 14s;
        animation-delay: -2s;
    }
    
    @keyframes spin-wave {
        from { transform: rotate(0deg); }
        to { transform: rotate(360deg); }
    }
    
    .water-content {
        position: relative;
        z-index: 3;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        color: #fff;
        pointer-events: none;
    }
    
    .water-percentage {
        font-size: 2.6rem;
        font-weight: 900;
        line-height: 1;
        margin-bottom: 2px;
        background: linear-gradient(to bottom, #fff 40%, rgba(255,255,255,0.7));
        -webkit-background-clip: text;
        background-clip: text;
        color: transparent;
        text-shadow: 0 4px 12px rgba(0,0,0,0.5);
    }
    
    .water-label {
        font-size: 0.65rem;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 0.12em;
        opacity: 0.8;
    }

    .quick-log-btn {
        background: rgba(255,255,255,.03);
        border: 1px solid var(--vg-border);
        color: var(--vg-text-strong);
        padding: .65rem 1.25rem;
        border-radius: 12px;
        font-size: .85rem;
        font-weight: 700;
        cursor: pointer;
        transition: all .2s cubic-bezier(0.4, 0, 0.2, 1);
        outline: none;
    }
    
    .quick-log-btn:hover {
        background: rgba(59, 130, 246, 0.1) !important;
        border-color: rgba(59, 130, 246, 0.5) !important;
        box-shadow: 0 4px 12px rgba(59, 130, 246, 0.2);
        transform: translateY(-2px);
    }
    
    .quick-log-btn:active {
        transform: scale(0.94);
    }

    .graph-column-group {
        position: relative;
        cursor: pointer;
    }
    
    .graph-bar {
        width: 26px;
        background: linear-gradient(to top, rgba(59, 130, 246, 0.2), rgba(139, 92, 246, 0.4));
        border-radius: 8px;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        box-shadow: 0 0 10px rgba(139, 92, 246, 0.05);
    }
    
    .graph-column-group:hover .graph-bar {
        background: linear-gradient(to top, #3b82f6, #8b5cf6);
        box-shadow: 0 0 15px rgba(59, 130, 246, 0.4);
        transform: scaleX(1.08);
    }
    
    .bar-complete {
        background: linear-gradient(to top, rgba(16, 185, 129, 0.3), rgba(52, 211, 153, 0.5));
    }
    
    .graph-column-group:hover .bar-complete {
        background: linear-gradient(to top, #10b981, #34d399) !important;
        box-shadow: 0 0 15px rgba(16, 185, 129, 0.4) !important;
    }
    
    .graph-tooltip {
        position: absolute;
        bottom: calc(100% - 10px);
        background: rgba(8, 8, 26, 0.95);
        border: 1px solid rgba(255, 255, 255, 0.15);
        padding: 4px 8px;
        border-radius: 6px;
        font-size: 0.65rem;
        font-weight: 800;
        color: #fff;
        opacity: 0;
        transform: translateY(4px);
        transition: all 0.2s ease;
        white-space: nowrap;
        pointer-events: none;
        z-index: 10;
    }
    
    .graph-column-group:hover .graph-tooltip {
        opacity: 1;
        transform: translateY(0);
    }
    
    .graph-day-label {
        font-size: .8rem;
        color: rgba(255, 255, 255, 0.7);
        font-weight: 800;
        transition: color 0.2s;
    }
    
    .graph-column-group:hover .graph-day-label {
        color: var(--vg-accent);
    }
</style>

<div style="max-width:1000px;margin:0 auto;">
    <!-- Header without floating detached badge -->
    <div style="margin-bottom:1.5rem; display: flex; justify-content: space-between; align-items: center;" class="fade-in-up">
        <div>
            <h1 style="font-size:1.8rem;font-weight:900;background:var(--vg-title-gradient);-webkit-background-clip:text;background-clip:text;color:transparent;margin-bottom:.35rem;">
                Hydration Tracker 💧
            </h1>
            <p style="color:var(--vg-text-muted);font-size:.9rem;">Stay hydrated to perform at your best</p>
        </div>
    </div>

    <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(320px,1fr));gap:1.5rem;">
        <!-- Daily Goal Panel -->
        <div style="background:var(--vg-panel);border:1px solid var(--vg-border);border-radius:24px;padding:2rem;text-align:center;position:relative;" class="fade-in-up delay-1">
            <!-- Goal Settings Gear Button -->
            <button onclick="toggleGoalEdit()" style="background:rgba(255,255,255,.03);border:1px solid var(--vg-border);color:var(--vg-text-muted);width:36px;height:36px;border-radius:50%;display:flex;align-items:center;justify-content:center;cursor:pointer;transition:all .2s;position:absolute;top:1.2rem;right:1.2rem;" title="Adjust Daily Goal" onmouseover="this.style.color='#c084fc';this.style.background='rgba(192,132,252,0.1)'" onmouseout="this.style.color='var(--vg-text-muted)';this.style.background='rgba(255,255,255,.03)'">
                <i data-lucide="settings" style="width:16px;height:16px;"></i>
            </button>

            <!-- Goal View State -->
            <div id="goalViewState">
                <!-- Premium horizontal Streak Card replacing top-right floating badge -->
                @if($streak > 0)
                    <div style="background:linear-gradient(135deg, rgba(251,191,36,.06), rgba(217,119,6,.06));border:1px solid rgba(251,191,36,.25);border-radius:16px;padding:.6rem 1rem;margin-bottom:1.5rem;display:flex;align-items:center;justify-content:center;gap:8px;box-shadow:0 4px 15px rgba(251,191,36,0.05);">
                        <span style="font-size:1.2rem;">🔥</span>
                        <span style="font-size:.85rem;font-weight:800;color:#fbbf24;letter-spacing:0.02em;">{{ $streak }} Day Hydration Streak Active!</span>
                    </div>
                @else
                    <div style="background:rgba(255,255,255,.02);border:1px solid var(--vg-border);border-radius:16px;padding:.6rem 1rem;margin-bottom:1.5rem;display:flex;align-items:center;justify-content:center;gap:8px;">
                        <span style="font-size:1rem;">💧</span>
                        <span style="font-size:.8rem;font-weight:700;color:var(--vg-text-muted);">Start a streak today! Reach your {{ number_format($goal) }}ml goal.</span>
                    </div>
                @endif

                <!-- Beautiful wave container -->
                <div class="water-circle-container {{ $percentage >= 100 ? 'milestone-glow' : '' }}">
                    <div class="water-bg" style="transform: translateY({{ 100 - $percentage }}%);">
                        <div class="water-wave wave-1"></div>
                        <div class="water-wave wave-2"></div>
                    </div>
                    <div class="water-content">
                        <span class="water-percentage">{{ $percentage }}%</span>
                        <span class="water-label">{{ $percentage >= 100 ? 'Goal Met! 🏆' : 'of daily goal' }}</span>
                    </div>
                </div>
                
                <h3 style="font-size:1.4rem;font-weight:800;color:var(--vg-text-strong);margin-bottom:.5rem;">{{ $totalToday }}ml / {{ $goal }}ml</h3>
                
                <!-- Smart Insight Motivational prompt -->
                <p style="color:var(--vg-text-muted);font-size:.85rem;margin-bottom:1.5rem;font-weight:600;min-height:36px;display:flex;align-items:center;justify-content:center;">
                    @if($percentage >= 100)
                        <span style="color:#10b981;font-weight:800;text-shadow:0 0 10px rgba(16,185,129,0.2);">🎉 Daily Goal Completed! Fully fueled and ready to dominate!</span>
                    @elseif($totalToday == 0)
                        <span>You're {{ number_format($goal) }}ml away from today's goal. Hydration improves recovery and focus! 💪</span>
                    @else
                        <span>You're only {{ number_format($goal - $totalToday) }}ml away from your goal! Keep up the great pace. ⚡</span>
                    @endif
                </p>

                <!-- Quick Addition Form with button checks -->
                <form action="{{ route('water.store') }}" method="POST" onsubmit="handleLogClick(this); return true;" style="display:flex;flex-wrap:wrap;justify-content:center;gap:.75rem;margin-bottom:1.5rem;">
                    @csrf
                    <button type="submit" name="amount_ml" value="250" class="quick-log-btn">+250ml</button>
                    <button type="submit" name="amount_ml" value="500" class="quick-log-btn">+500ml</button>
                    <button type="submit" name="amount_ml" value="750" class="quick-log-btn">+750ml</button>
                </form>

                <!-- Custom Amount Form with improved hierarchy -->
                <div style="border-top:1px solid rgba(255,255,255,.05);padding-top:1.5rem;margin-top:0.5rem;">
                    <form action="{{ route('water.store') }}" method="POST" onsubmit="preventDoubleSubmit(this)" style="display:flex;flex-direction:column;gap:12px;max-width:280px;margin:0 auto;">
                        @csrf
                        <div style="position:relative;width:100%;">
                            <input type="number" name="amount_ml" placeholder="Enter custom amount" min="50" max="3000" required 
                                   style="width:100%;padding:.75rem 3rem .75rem 1rem;border-radius:14px;background:rgba(255,255,255,.03);border:1px solid var(--vg-border);color:var(--vg-text-strong);outline:none;font-size:.95rem;font-weight:700;text-align:left;transition:all 0.2s;"
                                   onfocus="this.style.borderColor='var(--vg-accent)';this.style.boxShadow='0 0 10px rgba(139,92,246,0.15)'"
                                   onblur="this.style.borderColor='var(--vg-border)';this.style.boxShadow='none'">
                            <span style="position:absolute;right:16px;top:50%;transform:translateY(-50%);font-size:.8rem;color:var(--vg-text-muted);font-weight:800;">ml</span>
                        </div>
                        <button type="submit" class="log-btn" style="width:100%;background:linear-gradient(135deg, #3b82f6, #8b5cf6);border:none;color:#fff;padding:.75rem;border-radius:14px;font-size:.85rem;font-weight:800;cursor:pointer;transition:all .3s cubic-bezier(0.4, 0, 0.2, 1);box-shadow:0 4px 15px rgba(59,130,246,0.25);" onmouseover="this.style.transform='translateY(-2px)';this.style.boxShadow='0 8px 20px rgba(59,130,246,0.45)'" onmouseout="this.style.transform='none';this.style.boxShadow='0 4px 15px rgba(59,130,246,0.25)'">
                            Log Intake
                        </button>
                    </form>
                </div>
            </div>

            <!-- Goal Edit State -->
            <div id="goalEditState" class="hidden" style="padding:1.5rem 0;">
                <h4 style="font-size:1.1rem;font-weight:800;color:var(--vg-text-strong);margin-bottom:1rem;">Adjust Daily Goal</h4>
                <form action="{{ route('water.goal') }}" method="POST" style="display:flex;flex-direction:column;gap:1.25rem;max-width:240px;margin:0 auto;">
                    @csrf
                    <div style="position:relative;">
                        <input type="number" name="daily_water_goal" value="{{ $goal }}" min="500" max="10000" required 
                               style="width:100%;padding:.6rem 1rem;border-radius:12px;background:rgba(255,255,255,.05);border:1px solid var(--vg-border);color:var(--vg-text-strong);outline:none;font-size:1.1rem;font-weight:800;text-align:center;">
                        <span style="position:absolute;right:16px;top:50%;transform:translateY(-50%);font-size:.85rem;color:var(--vg-text-muted);font-weight:700;">ml</span>
                    </div>
                    <div style="display:flex;gap:.5rem;">
                        <button type="button" onclick="toggleGoalEdit()" style="flex:1;background:rgba(255,255,255,.05);border:1px solid var(--vg-border);color:var(--vg-text-strong);padding:.6rem;border-radius:12px;font-size:.8rem;font-weight:700;cursor:pointer;transition:all .2s;" onmouseover="this.style.background='rgba(255,255,255,.1)'" onmouseout="this.style.background='rgba(255,255,255,.05)'">
                            Cancel
                        </button>
                        <button type="submit" style="flex:1;background:linear-gradient(135deg, #8b5cf6, #ec4899);border:none;color:#fff;padding:.6rem;border-radius:12px;font-size:.8rem;font-weight:700;cursor:pointer;transition:all .2s;box-shadow:0 4px 12px rgba(139,92,246,0.25);" onmouseover="this.style.transform='translateY(-1px)'" onmouseout="this.style.transform='none'">
                            Save Goal
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Weekly History & Today's Logs Panel -->
        <div style="background:var(--vg-panel);border:1px solid var(--vg-border);border-radius:24px;padding:1.5rem;display:flex;flex-direction:column;" class="fade-in-up delay-2">
            <h3 style="font-size:1.1rem;font-weight:800;color:var(--vg-text-strong);margin-bottom:1.5rem;">Weekly History</h3>
            
            <!-- Thicker Graph with height:210px and tooltips -->
            <div style="display:flex;align-items:flex-end;justify-content:space-between;height:210px;padding-bottom:1rem;margin-bottom:0.75rem;border-bottom:1px solid rgba(255,255,255,.03);">
                @php $days = ['Mon','Tue','Wed','Thu','Fri','Sat','Sun']; @endphp
                @foreach($days as $day)
                    @php 
                        $val = $history->get($day, 0); 
                        $h = min(100, ($val / $goal) * 100);
                    @endphp
                    <div style="flex:1;height:100%;display:flex;flex-direction:column;justify-content:flex-end;align-items:center;gap:.5rem;position:relative;" class="graph-column-group">
                        <span class="graph-tooltip">{{ $val }}ml</span>
                        <div class="graph-bar {{ $h >= 100 ? 'bar-complete' : '' }}" style="height:{{ max(6, $h) }}%;" title="{{ $day }}: {{ $val }}ml"></div>
                        <span class="graph-day-label">{{ $day }}</span>
                    </div>
                @endforeach
            </div>

            <!-- Stats Bar Panel -->
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:0.75rem;margin-bottom:1rem;background:rgba(255,255,255,0.02);border:1px solid var(--vg-border);padding:0.75rem 1rem;border-radius:16px;">
                <div style="display:flex;flex-direction:column;gap:2px;text-align:left;">
                    <span style="font-size:0.65rem;color:var(--vg-text-muted);font-weight:700;text-transform:uppercase;letter-spacing:0.05em;">Average Intake</span>
                    <span style="font-size:1rem;color:var(--vg-text-strong);font-weight:900;">2.8 Liters</span>
                </div>
                <div style="display:flex;flex-direction:column;gap:2px;text-align:left;">
                    <span style="font-size:0.65rem;color:var(--vg-text-muted);font-weight:700;text-transform:uppercase;letter-spacing:0.05em;">Weekly Success</span>
                    <span style="font-size:1rem;color:#10b981;font-weight:900;">85% Met</span>
                </div>
            </div>
            
            <div style="margin-top:.5rem;padding-top:.5rem;flex:1;display:flex;flex-direction:column;">
                <h4 style="font-size:.85rem;font-weight:800;color:var(--vg-text-strong);margin-bottom:.75rem;">Today's Logs</h4>
                <div style="display:flex;flex-direction:column;gap:.5rem;max-height:170px;overflow-y:auto;flex:1;padding-right:4px;">
                    @forelse($intakes as $intake)
                        <div style="display:flex;justify-content:space-between;align-items:center;background:rgba(255,255,255,.01);padding:.55rem 1rem;border-radius:12px;border:1px solid rgba(255,255,255,.04);transition: all .2s;">
                            <div style="display:flex;align-items:center;gap:10px;">
                                <span style="font-size:1.1rem;">💧</span>
                                <div style="display:flex;flex-direction:column;">
                                    <span style="font-size:.85rem;color:var(--vg-text-strong);font-weight:700;">{{ $intake->amount_ml }}ml</span>
                                    <span style="font-size:.65rem;color:var(--vg-text-muted);font-weight:600;">{{ $intake->created_at->format('h:i A') }}</span>
                                </div>
                            </div>
                            
                            <!-- Delete Button Form -->
                            <form action="{{ route('water.destroy', $intake->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this water log?');" style="margin:0;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" style="background:transparent;border:none;color:var(--vg-text-muted);cursor:pointer;padding:6px;border-radius:8px;transition:all .2s;display:flex;align-items:center;justify-content:center;" onmouseover="this.style.color='#ef4444';this.style.background='rgba(239,68,68,0.08)'" onmouseout="this.style.color='var(--vg-text-muted)';this.style.background='transparent'">
                                    <i data-lucide="trash-2" style="width:14px;height:14px;"></i>
                                </button>
                            </form>
                        </div>
                    @empty
                        <!-- Visual Mock Logs and Hydration Tip when empty -->
                        <div style="display:flex;flex-direction:column;gap:.75rem;">
                            <div style="background:rgba(255,255,255,.01);padding:.55rem 1rem;border-radius:12px;border:1px dashed rgba(255,255,255,.06);display:flex;justify-content:space-between;align-items:center;opacity:0.4;">
                                <div style="display:flex;align-items:center;gap:10px;">
                                    <span style="font-size:1.1rem;">💧</span>
                                    <div style="display:flex;flex-direction:column;text-align:left;">
                                        <span style="font-size:.85rem;color:var(--vg-text-strong);font-weight:700;">+500ml</span>
                                        <span style="font-size:.65rem;color:var(--vg-text-muted);font-weight:600;">08:30 AM (Example Log)</span>
                                    </div>
                                </div>
                                <span style="font-size:0.65rem;font-weight:800;color:var(--vg-text-muted);text-transform:uppercase;">Mock</span>
                            </div>
                            <div style="background:rgba(255,255,255,.01);padding:.55rem 1rem;border-radius:12px;border:1px dashed rgba(255,255,255,.06);display:flex;justify-content:space-between;align-items:center;opacity:0.4;">
                                <div style="display:flex;align-items:center;gap:10px;">
                                    <span style="font-size:1.1rem;">💧</span>
                                    <div style="display:flex;flex-direction:column;text-align:left;">
                                        <span style="font-size:.85rem;color:var(--vg-text-strong);font-weight:700;">+250ml</span>
                                        <span style="font-size:.65rem;color:var(--vg-text-muted);font-weight:600;">11:10 AM (Example Log)</span>
                                    </div>
                                </div>
                                <span style="font-size:0.65rem;font-weight:800;color:var(--vg-text-muted);text-transform:uppercase;">Mock</span>
                            </div>
                            <div style="background:rgba(59,130,246,0.04);border:1px solid rgba(59,130,246,0.15);padding:1rem;border-radius:16px;text-align:left;display:flex;gap:12px;align-items:flex-start;">
                                <span style="font-size:1.3rem;color:#3b82f6;">💡</span>
                                <div style="display:flex;flex-direction:column;gap:4px;">
                                    <span style="font-size:.8rem;font-weight:800;color:var(--vg-text-strong);">Hydration Tip</span>
                                    <p style="font-size:.72rem;color:var(--vg-text-muted);line-height:1.4;margin:0;">Drink a glass of water immediately after waking up to activate organs and boost natural recovery speeds.</p>
                                </div>
                            </div>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <!-- Mini Recovery Benefits Card to balance empty bottom space -->
    <div style="margin-top:1.5rem;" class="fade-in-up delay-3">
        <div style="background:linear-gradient(135deg, rgba(59,130,246,0.06), rgba(139,92,246,0.06));border:1px solid rgba(59,130,246,0.15);border-radius:24px;padding:1.25rem 2rem;display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:1.5rem;">
            <div style="display:flex;align-items:center;gap:12px;">
                <div style="background:rgba(59,130,246,0.1);padding:10px;border-radius:12px;color:#3b82f6;">
                    <i data-lucide="sparkles" style="width:20px;height:20px;"></i>
                </div>
                <div>
                    <h4 style="font-size:.9rem;font-weight:900;color:var(--vg-text-strong);margin:0 0 2px;">Daily Recovery Benefits</h4>
                    <p style="font-size:.75rem;color:var(--vg-text-muted);margin:0;">Active hydration boosts performance and physical recovery rates</p>
                </div>
            </div>
            <div style="display:flex;gap:1.5rem;align-items:center;">
                <div style="text-align:center;">
                    <span style="display:block;font-size:1.1rem;font-weight:900;color:#10b981;">+12%</span>
                    <span style="font-size:.65rem;color:var(--vg-text-muted);font-weight:700;text-transform:uppercase;">Energy Level</span>
                </div>
                <div style="border-left:1px solid rgba(255,255,255,0.08);height:30px;"></div>
                <div style="text-align:center;">
                    <span style="display:block;font-size:1.1rem;font-weight:900;color:#3b82f6;">+8%</span>
                    <span style="font-size:.65rem;color:var(--vg-text-muted);font-weight:700;text-transform:uppercase;">Muscle Repair</span>
                </div>
                <div style="border-left:1px solid rgba(255,255,255,0.08);height:30px;"></div>
                <div style="text-align:center;">
                    <span style="display:block;font-size:1.1rem;font-weight:900;color:#8b5cf6;">+15%</span>
                    <span style="font-size:.65rem;color:var(--vg-text-muted);font-weight:700;text-transform:uppercase;">Mental Focus</span>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function toggleGoalEdit() {
        const view = document.getElementById('goalViewState');
        const edit = document.getElementById('goalEditState');
        view.classList.toggle('hidden');
        edit.classList.toggle('hidden');
    }

    function preventDoubleSubmit(form) {
        const buttons = form.querySelectorAll('.log-btn');
        buttons.forEach(btn => {
            btn.disabled = true;
            btn.style.opacity = '0.5';
            btn.style.cursor = 'not-allowed';
        });
        return true;
    }

    function handleLogClick(form) {
        const activeBtn = document.activeElement;
        if (activeBtn && activeBtn.name === 'amount_ml') {
            const val = activeBtn.value;
            activeBtn.innerHTML = `+${val}ml ✓`;
            activeBtn.style.background = '#10b981';
            activeBtn.style.color = '#fff';
            activeBtn.style.borderColor = 'transparent';
        }
    }
</script>
@endsection
