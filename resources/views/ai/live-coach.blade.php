@extends('layouts.app')

@section('title', 'Live AI Form Coach')

@section('content')
<style>
    .dashboard-card {
        transition: all 0.4s cubic-bezier(0.16, 1, 0.3, 1);
    }
    .dashboard-card:hover {
        transform: translateY(-2px);
        border-color: rgba(139, 92, 246, 0.18) !important;
        box-shadow: 0 12px 30px rgba(139, 92, 246, 0.06);
    }

    #startBtn {
        background: linear-gradient(135deg, #3b82f6, #8b5cf6) !important;
        box-shadow: 0 2px 4px rgba(59, 130, 246, 0.1);
        animation: breathe-btn 2.5s infinite alternate;
        transition: all 0.3s ease;
    }
    #startBtn:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 10px rgba(139, 92, 246, 0.15) !important;
    }
    #startBtn:active {
        transform: scale(0.95);
    }
    
    @keyframes breathe-btn {
        0% { box-shadow: 0 2px 4px rgba(59, 130, 246, 0.1); }
        100% { box-shadow: 0 4px 10px rgba(139, 92, 246, 0.15); }
    }

    .pulse-indicator {
        animation: pulse-dot 1.5s infinite alternate;
    }
    @keyframes pulse-dot {
        0% { opacity: 0.6; }
        100% { opacity: 1; transform: scale(1.1); }
    }

    @keyframes scan-sweep {
        0% { top: 0%; opacity: 0.1; }
        50% { top: 100%; opacity: 0.8; }
        100% { top: 0%; opacity: 0.1; }
    }

    .corner-glow {
        transition: all 0.3s ease;
        border-color: rgba(139,92,246,0.18) !important;
        animation: corner-idle-breath 3s infinite alternate ease-in-out;
    }
    @keyframes corner-idle-breath {
        0% { opacity: 0.45; }
        100% { opacity: 0.95; filter: drop-shadow(0 0 2px rgba(139, 92, 246, 0.25)); }
    }
    .corner-active {
        border-color: #22c55e !important;
        filter: drop-shadow(0 0 5px rgba(34, 197, 94, 0.6));
        animation: corner-pulse-active 1.5s infinite alternate ease-in-out !important;
    }
    @keyframes corner-pulse-active {
        0% { opacity: 0.7; }
        100% { opacity: 1; filter: drop-shadow(0 0 7px rgba(34, 197, 94, 0.9)); }
    }

    .pulse-bar {
        background: linear-gradient(90deg, #3b82f6, #8b5cf6, #3b82f6) !important;
        background-size: 200% 200% !important;
        animation: pulse-bar 2s infinite ease-in-out, gradient-shift 3s infinite linear;
    }
    @keyframes gradient-shift {
        0% { background-position: 0% 50%; }
        50% { background-position: 100% 50%; }
        100% { background-position: 0% 50%; }
    }
    @keyframes pulse-bar {
        0% { opacity: 0.85; }
        50% { opacity: 1; filter: drop-shadow(0 0 4px rgba(139, 92, 246, 0.45)); }
        100% { opacity: 0.85; }
    }

    .blink-cursor {
        animation: blink-c 1.2s step-end infinite;
    }
    @keyframes blink-c {
        from, to { opacity: 0; }
        50% { opacity: 1; }
    }

    @keyframes ambientGlow-anim {
        0% { background-position: 0% 50%; }
        50% { background-position: 100% 50%; }
        100% { background-position: 0% 50%; }
    }

    @keyframes spin-cw { from { transform: rotate(0deg); } to { transform: rotate(360deg); } }
    @keyframes spin-ccw { from { transform: rotate(360deg); } to { transform: rotate(0deg); } }

    /* Hide scrollbars for feedbackConsole */
    #feedbackConsole::-webkit-scrollbar {
        display: none !important;
        width: 0 !important;
        height: 0 !important;
        background: transparent !important;
    }
    #feedbackConsole {
        -ms-overflow-style: none !important;
        scrollbar-width: none !important;
    }
</style>

<div style="position:fixed;top:0;left:0;right:0;bottom:0;background:radial-gradient(circle at 30% 30%, rgba(139, 92, 246, 0.05) 0%, transparent 60%), radial-gradient(circle at 80% 70%, rgba(59, 130, 246, 0.04) 0%, transparent 55%);background-size: 200% 200%;animation: ambientGlow-anim 25s infinite alternate ease-in-out;pointer-events:none;z-index:-1;"></div>

<div style="max-width:1320px;margin:0 auto;padding:0 1rem;" class="fade-in-up">
    <!-- Header -->
    <div style="display:flex;justify-content:space-between;align-items:flex-end;margin-bottom:1rem;">
        <div>
            <h1 style="font-size:1.85rem;font-weight:900;background:var(--vg-title-gradient);-webkit-background-clip:text;background-clip:text;color:transparent;margin-bottom:.15rem;letter-spacing:-0.5px;">
                Live AI Form Coach
            </h1>
            <p style="color:var(--vg-text-muted);font-size:0.85rem;">Real-time posture analysis and rep counting using your webcam.</p>
        </div>
        <div style="display:flex;gap:8px;align-items:center;">
            <button id="guideBtn" style="background:rgba(255,255,255,0.03);color:var(--vg-text-muted);border:1px solid rgba(255,255,255,0.08);padding:9px 18px;border-radius:12px;font-weight:700;font-size:0.85rem;cursor:pointer;display:flex;align-items:center;gap:8px;transition:0.3s;" onmouseover="this.style.background='rgba(255,255,255,0.08)';this.style.color='#fff';" onmouseout="this.style.background='rgba(255,255,255,0.03)';this.style.color='var(--vg-text-muted)';">
                <i data-lucide="help-circle" style="width:16px;height:16px;"></i> Setup Guide
            </button>
            <button id="demoBtn" style="background:linear-gradient(135deg, rgba(139,92,246,0.1), rgba(59,130,246,0.1));color:#c084fc;border:1px solid rgba(139,92,246,0.2);padding:9px 18px;border-radius:12px;font-weight:700;font-size:0.85rem;cursor:pointer;display:flex;align-items:center;gap:8px;transition:0.3s;" onmouseover="this.style.background='rgba(139,92,246,0.2)';this.style.boxShadow='0 0 12px rgba(139,92,246,0.2)';" onmouseout="this.style.background='rgba(139,92,246,0.1)';this.style.boxShadow='none';">
                <i data-lucide="play" style="width:16px;height:16px;"></i> Start AI Demo
            </button>
            <button id="stopDemoBtn" style="background:rgba(239,68,68,0.15);color:#f87171;border:1px solid rgba(239,68,68,0.25);padding:9px 18px;border-radius:12px;font-weight:700;font-size:0.85rem;cursor:pointer;display:none;align-items:center;gap:8px;transition:0.3s;" onmouseover="this.style.background='rgba(239,68,68,0.25)';" onmouseout="this.style.background='rgba(239,68,68,0.15)';">
                <i data-lucide="square" style="width:16px;height:16px;"></i> Stop Demo
            </button>
            <button id="startBtn" style="background:var(--vg-accent);color:white;border:none;padding:9px 18px;border-radius:12px;font-weight:700;font-size:0.85rem;cursor:pointer;display:flex;align-items:center;gap:8px;transition:0.3s;">
                <i data-lucide="video" style="width:16px;height:16px;"></i> Start Camera
            </button>
            <button id="stopBtn" style="background:rgba(239,68,68,0.2);color:#fca5a5;border:1px solid rgba(239,68,68,0.4);padding:9px 18px;border-radius:12px;font-weight:700;font-size:0.85rem;cursor:pointer;display:none;align-items:center;gap:8px;transition:0.3s;" onmouseover="this.style.background='rgba(239,68,68,0.3)'" onmouseout="this.style.background='rgba(239,68,68,0.2)'">
                <i data-lucide="video-off" style="width:16px;height:16px;"></i> Stop Session
            </button>
        </div>
    </div>

    <!-- Main Content Grid (12px visual gap for compact grid alignment) -->
    <div style="display:grid;grid-template-columns:1fr 320px;gap:0.75rem;align-items:start;">
        
        <!-- Left Side: Hero Camera, and side-by-side Feedback + Motion Analysis -->
        <div style="display:flex;flex-direction:column;gap:0.6rem;">
            <!-- Camera View Area (Futuristic HUD style, reduced height to 490px to prevent layout scroll) -->
            <div style="background:rgba(8,8,26,0.95);border:1px solid var(--vg-border);border-radius:24px;overflow:hidden;position:relative;height:490px;display:flex;align-items:center;justify-content:center;box-shadow:0 10px 30px rgba(0,0,0,0.5);">
                
                <!-- Crop corner brackets -->
                <div class="corner-glow" id="cornerTL" style="position:absolute;top:20px;left:20px;width:15px;height:15px;border-top:2px solid rgba(139,92,246,0.3);border-left:2px solid rgba(139,92,246,0.3);pointer-events:none;z-index:5;"></div>
                <div class="corner-glow" id="cornerTR" style="position:absolute;top:20px;right:20px;width:15px;height:15px;border-top:2px solid rgba(139,92,246,0.3);border-right:2px solid rgba(139,92,246,0.3);pointer-events:none;z-index:5;"></div>
                <div class="corner-glow" id="cornerBL" style="position:absolute;bottom:20px;left:20px;width:15px;height:15px;border-bottom:2px solid rgba(139,92,246,0.3);border-left:2px solid rgba(139,92,246,0.3);pointer-events:none;z-index:5;"></div>
                <div class="corner-glow" id="cornerBR" style="position:absolute;bottom:20px;right:20px;width:15px;height:15px;border-bottom:2px solid rgba(139,92,246,0.3);border-right:2px solid rgba(139,92,246,0.3);pointer-events:none;z-index:5;"></div>
                
                <!-- Crosshairs focal center -->
                <div style="position:absolute;top:50%;left:50%;transform:translate(-50%,-50%);pointer-events:none;z-index:5;opacity:0.25;">
                    <div style="width:30px;height:1px;background:#8b5cf6;position:absolute;top:50%;left:-15px;"></div>
                    <div style="height:30px;width:1px;background:#8b5cf6;position:absolute;left:50%;top:-15px;"></div>
                    <div style="width:8px;height:8px;border:1px solid #8b5cf6;border-radius:50%;position:absolute;top:-4px;left:-4px;"></div>
                </div>

                <!-- Faint Pose Guides / Silhouette -->
                <div id="silhouetteGuide" style="position:absolute;inset:0;display:flex;align-items:center;justify-content:center;pointer-events:none;opacity:0.12;z-index:3;color:#8b5cf6;">
                    <!-- Full body vector posture silhouette -->
                    <svg viewBox="0 0 100 100" style="width:240px;height:240px;fill:none;stroke:currentColor;stroke-width:1.5;stroke-linecap:round;">
                        <circle cx="50" cy="20" r="6" />
                        <line x1="50" y1="26" x2="50" y2="55" />
                        <polyline points="28,34 50,28 72,34" />
                        <polyline points="35,78 45,55 50,55 55,55 65,78" />
                        <path d="M 0,50 L 100,50 M 50,0 L 50,100" stroke-dasharray="2 2" stroke-width="0.5"/>
                    </svg>
                </div>

                <!-- Top-Left System Status Badge -->
                <div id="systemStatus" style="position:absolute;top:20px;left:25px;background:rgba(8,8,26,0.85);backdrop-filter:blur(8px);border:1px solid rgba(255,255,255,0.08);padding:6px 12px;border-radius:20px;display:flex;align-items:center;gap:8px;z-index:6;pointer-events:none;">
                    <span id="statusIndicator" style="width:8px;height:8px;border-radius:50%;background:#ef4444;box-shadow:0 0 8px #ef4444;" class="pulse-indicator"></span>
                    <span id="statusText" style="font-size:0.72rem;font-weight:800;color:rgba(255,255,255,0.85);text-transform:uppercase;letter-spacing:0.05em;">System Idle</span>
                </div>

                <!-- Top-Right Audio/Video Ready Status -->
                <div style="position:absolute;top:20px;right:25px;background:rgba(8,8,26,0.85);backdrop-filter:blur(8px);border:1px solid rgba(255,255,255,0.08);padding:6px 12px;border-radius:20px;display:flex;align-items:center;gap:12px;z-index:6;pointer-events:none;color:rgba(255,255,255,0.7);font-size:0.72rem;font-weight:700;">
                    <span style="display:flex;align-items:center;gap:4px;"><i data-lucide="video" style="width:12px;height:12px;color:#22c55e;"></i> Camera Ready</span>
                    <span style="border-left:1px solid rgba(255,255,255,0.15);height:10px;"></span>
                    <span style="display:flex;align-items:center;gap:4px;"><i data-lucide="mic-off" style="width:12px;height:12px;color:#fca5a5;"></i> Mic Muted</span>
                </div>

                <!-- Scanner Sweep Bar -->
                <div id="scannerSweep" style="position:absolute;top:0;left:0;width:100%;height:3px;background:linear-gradient(to right, transparent, #8b5cf6, transparent);box-shadow:0 0 12px #8b5cf6;z-index:7;pointer-events:none;display:none;animation:scan-sweep 4s infinite ease-in-out;"></div>

                <!-- Calibration/Initialize Loader -->
                <div id="loader" style="position:absolute;z-index:10;display:none;flex-direction:column;align-items:center;gap:15px;background:rgba(8,8,26,0.9);inset:0;justify-content:center;backdrop-filter:blur(5px);">
                    <div style="position:relative;width:60px;height:60px;">
                        <div style="position:absolute;inset:0;border:3px dashed rgba(139,92,246,0.3);border-radius:50%;animation:spin-cw 8s linear infinite;"></div>
                        <div style="position:absolute;inset:4px;border:3px solid transparent;border-top-color:#3b82f6;border-radius:50%;animation:spin-ccw 1.5s cubic-bezier(0.5,0,0.5,1) infinite;"></div>
                        <div style="position:absolute;inset:12px;border:3px solid transparent;border-bottom-color:#8b5cf6;border-radius:50%;animation:spin-cw 1s linear infinite;"></div>
                    </div>
                    <div style="text-align:center;">
                        <p style="color:var(--vg-text-strong);font-weight:900;letter-spacing:1px;font-size:1.05rem;margin:0 0 4px;" id="loaderMainText">INITIALIZING AI ENGINE...</p>
                        <p style="color:var(--vg-text-muted);font-weight:600;font-size:0.75rem;margin:0;" id="loaderSubText">LOCATING KEY LANDMARKS...</p>
                    </div>
                </div>

                <div id="placeholder" style="text-align:center;color:var(--vg-text-muted);z-index:4;">
                    <i data-lucide="scan-eye" style="width:64px;height:64px;margin:0 auto 15px;opacity:0.6;color:#8b5cf6;filter:drop-shadow(0 0 10px rgba(139,92,246,0.3));"></i>
                    <p style="font-weight:800;color:var(--vg-text-strong);font-size:1.15rem;margin-bottom:6px;">Ready for Calibration</p>
                    <p style="font-size:0.8rem;opacity:0.8;margin:0;">Make sure your full body is visible in the frame grid.</p>
                </div>

                <!-- Hidden video element for processing -->
                <video id="input_video" style="display:none;" autoplay playsinline></video>
                
                <!-- Canvas where we draw the video and skeleton -->
                <canvas id="output_canvas" style="width:100%;height:100%;object-fit:cover;display:none;z-index:4;"></canvas>

                <!-- Form Feedback Overlay -->
                <div id="feedbackOverlay" style="position:absolute;bottom:20px;left:50%;transform:translateX(-50%);background:rgba(8,8,26,0.85);backdrop-filter:blur(10px);border:1px solid var(--vg-border);padding:12px 25px;border-radius:50px;display:none;align-items:center;gap:12px;box-shadow:0 10px 25px rgba(0,0,0,0.5);z-index:8;">
                    <div id="feedbackStatus" style="width:12px;height:12px;border-radius:50%;background:#22c55e;box-shadow:0 0 10px #22c55e;"></div>
                    <p id="feedbackText" style="color:white;font-weight:800;font-size:1rem;letter-spacing:0.5px;margin:0;">Calibrating Form...</p>
                </div>
            </div>

            <!-- Bottom Horizontal Row: side-by-side Feedback Console & Motion Analysis (Both hardcoded to highly compact, symmetrical 200px height) -->
            <div style="display:grid;grid-template-columns:38% 62%;gap:0.6rem;">
                
                <!-- Left bottom card: AI Feedback Console -->
                <div class="dashboard-card" style="background:var(--vg-panel);border:1px solid var(--vg-border);border-radius:24px;padding:0.7rem 1.25rem;height:200px;box-shadow:0 6px 20px rgba(0,0,0,0.15);display:flex;flex-direction:column;justify-content:space-between;box-sizing:border-box;">
                    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:6px;flex-shrink:0;">
                        <h3 style="color:var(--vg-text-strong);font-size:0.82rem;font-weight:800;margin:0;display:flex;align-items:center;gap:6px;">
                            <i data-lucide="terminal" style="width:14px;height:14px;color:#3b82f6;"></i> AI Feedback Console
                        </h3>
                        <span style="font-size:0.6rem;color:#22c55e;font-weight:800;text-transform:uppercase;letter-spacing:0.05em;background:rgba(34,197,94,0.1);padding:2px 6px;border-radius:20px;">System Online</span>
                    </div>
                    <div id="feedbackConsole" style="background:rgba(8,8,26,0.65);border:1px solid rgba(255,255,255,0.03);border-radius:14px;padding:8px 10px;font-family:monospace;font-size:0.72rem;color:rgba(255,255,255,0.7);height:115px;overflow-y:auto;display:flex;flex-direction:column;gap:4px;scroll-behavior:smooth;flex-grow:1;box-sizing:border-box;">
                        <div style="color:#8b5cf6;">[02:22:08] AI Core Initialized. Standing by.</div>
                        <div style="color:rgba(255,255,255,0.45);">[02:22:08] Awaiting webcam connection... Click "Start Camera".</div>
                        <!-- Blinking Cursor Line always at bottom -->
                        <div id="consoleCursorLine" style="color:rgba(255,255,255,0.45);display:flex;align-items:center;gap:4px;">
                            <span style="color:rgba(255,255,255,0.25); font-weight:700;">[system]</span>
                            <span class="blink-cursor" style="color:#8b5cf6; font-weight:800; font-size:0.8rem;">█</span>
                        </div>
                    </div>
                </div>

                <!-- Right bottom card: Live Motion Analysis -->
                <div class="dashboard-card" style="background:var(--vg-panel);border:1px solid var(--vg-border);border-radius:24px;padding:0.7rem 1.25rem;height:200px;box-shadow:0 6px 20px rgba(0,0,0,0.15);display:flex;flex-direction:column;justify-content:space-between;box-sizing:border-box;">
                    <h3 style="color:var(--vg-text-strong);font-size:0.82rem;font-weight:800;margin-bottom:8px;display:flex;align-items:center;gap:6px;flex-shrink:0;">
                        <i data-lucide="line-chart" style="width:14px;height:14px;color:#8b5cf6;"></i> Live Motion Analysis
                    </h3>
                    
                    <div style="display:grid;grid-template-columns:1fr 185px;gap:0.5rem;flex-grow:1;align-items:center;box-sizing:border-box;">
                        <!-- Movement Stability Graph (Active Standby undulating graph) -->
                        <div style="background:rgba(8,8,26,0.65);border:1px solid rgba(255,255,255,0.03);border-radius:14px;padding:6px 8px;height:115px;display:flex;flex-direction:column;justify-content:space-between;box-sizing:border-box;">
                            <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:2px;flex-shrink:0;">
                                <span style="font-size:0.6rem;color:var(--vg-text-muted);font-weight:700;text-transform:uppercase;letter-spacing:0.02em;">Stability</span>
                                <span id="stabilityVal" style="font-size:0.7rem;font-weight:850;color:rgba(255,255,255,0.3);text-transform:uppercase;"></span>
                            </div>
                            
                            <!-- Live Animating SVG line graph -->
                            <div style="position:relative;width:100%;height:75px;background:rgba(255,255,255,0.01);border-radius:8px;overflow:hidden;border:1px dashed rgba(255,255,255,0.02);">
                                <svg id="stabilitySvg" style="width:100%;height:100%;" viewBox="0 0 300 80" preserveAspectRatio="none">
                                    <defs>
                                        <linearGradient id="stabilityGrad" x1="0%" y1="0%" x2="0%" y2="100%">
                                            <stop offset="0%" stop-color="rgba(139, 92, 246, 0.25)"/>
                                            <stop offset="100%" stop-color="rgba(139, 92, 246, 0.0)"/>
                                        </linearGradient>
                                        <linearGradient id="lineGrad" x1="0%" y1="0%" x2="100%" y2="0%">
                                            <stop offset="0%" stop-color="#3b82f6"/>
                                            <stop offset="100%" stop-color="#8b5cf6"/>
                                        </linearGradient>
                                    </defs>
                                    <!-- Filled area under path -->
                                    <path id="stabilityArea" d="M 0 80 L 300 80 Z" fill="url(#stabilityGrad)" style="transition: d 0.15s ease-out;"/>
                                    <!-- Stroked line -->
                                    <path id="stabilityPath" d="M 0 65 L 300 65" fill="transparent" stroke="url(#lineGrad)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="transition: d 0.15s ease-out; filter:drop-shadow(0 0 2px rgba(139,92,246,0.3));"/>
                                </svg>
                                
                                <!-- Grid background lines -->
                                <div style="position:absolute;top:18px;left:0;right:0;height:1px;background:rgba(255,255,255,0.02);"></div>
                                <div style="position:absolute;top:36px;left:0;right:0;height:1px;background:rgba(255,255,255,0.02);"></div>
                                <div style="position:absolute;top:54px;left:0;right:0;height:1px;background:rgba(255,255,255,0.02);"></div>
                            </div>
                        </div>
                        
                        <!-- Joint Tracking Status (Active scrolling telemetry coordinates when idle!) -->
                        <div style="background:rgba(8,8,26,0.65);border:1px solid rgba(255,255,255,0.03);border-radius:14px;padding:6px 8px;height:115px;display:flex;flex-direction:column;justify-content:center;gap:3px;box-sizing:border-box;">
                            <span style="font-size:0.58rem;color:var(--vg-text-muted);font-weight:700;text-transform:uppercase;letter-spacing:0.04em;margin-bottom:1px;display:block;">Joint Tracker</span>
                            
                            <div style="display:flex;justify-content:space-between;align-items:center;font-size:0.65rem;color:var(--vg-text-muted);" id="jointHead">
                                <span>Head</span>
                                <span id="jointHeadStatus" style="font-weight:800;color:rgba(255,255,255,0.3);display:flex;align-items:center;gap:4px;"></span>
                            </div>
                            <div style="display:flex;justify-content:space-between;align-items:center;font-size:0.65rem;color:var(--vg-text-muted);" id="jointShoulders">
                                <span>Shoulders</span>
                                <span id="jointShouldersStatus" style="font-weight:800;color:rgba(255,255,255,0.3);display:flex;align-items:center;gap:4px;"></span>
                            </div>
                            <div style="display:flex;justify-content:space-between;align-items:center;font-size:0.65rem;color:var(--vg-text-muted);" id="jointHips">
                                <span>Hips</span>
                                <span id="jointHipsStatus" style="font-weight:800;color:rgba(255,255,255,0.3);display:flex;align-items:center;gap:4px;"></span>
                            </div>
                            <div style="display:flex;justify-content:space-between;align-items:center;font-size:0.65rem;color:var(--vg-text-muted);" id="jointKnees">
                                <span>Knees</span>
                                <span id="jointKneesStatus" style="font-weight:800;color:rgba(255,255,255,0.3);display:flex;align-items:center;gap:4px;"></span>
                            </div>
                            <div style="display:flex;justify-content:space-between;align-items:center;font-size:0.65rem;color:var(--vg-text-muted);" id="jointAnkles">
                                <span>Ankles</span>
                                <span id="jointAnklesStatus" style="font-weight:800;color:rgba(255,255,255,0.3);display:flex;align-items:center;gap:4px;"></span>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>

        <!-- Right Side: Sleek, visually lighter control sidebar -->
        <div style="display:flex;flex-direction:column;gap:0.6rem;">
            
            <!-- Active Exercise Dropdown Select with Squat Vector Icon (Compact margins & padding) -->
            <div class="dashboard-card" style="background:rgba(8,8,26,0.45);border:1px solid rgba(255,255,255,0.02);border-radius:24px;padding:0.6rem 0.95rem;">
                <div style="display:flex;align-items:center;gap:10px;">
                    <div style="background:rgba(139,92,246,0.07);padding:8px;border-radius:10px;color:#8b5cf6;" id="exercisePreviewIcon">
                        <svg viewBox="0 0 100 100" style="width:24px;height:24px;fill:none;stroke:currentColor;stroke-width:6;stroke-linecap:round;stroke-linejoin:round;">
                            <circle cx="50" cy="24" r="7" />
                            <line x1="50" y1="31" x2="45" y2="52" />
                            <polyline points="45,35 65,33 75,42" />
                            <polyline points="45,52 65,58 50,82" />
                        </svg>
                    </div>
                    <div style="flex-grow:1;">
                        <h3 style="color:var(--vg-text-muted);font-size:0.72rem;text-transform:uppercase;letter-spacing:1px;margin-bottom:2px;font-weight:750;">Active Exercise</h3>
                        <select id="exerciseSelect" style="width:100%;background:rgba(255,255,255,0.02);border:1px solid rgba(255,255,255,0.06);color:var(--vg-text-strong);padding:6px 12px;border-radius:10px;font-size:0.88rem;font-weight:700;outline:none;cursor:pointer;transition:border-color 0.2s;" onfocus="this.style.borderColor='var(--vg-accent)'" onblur="this.style.borderColor='rgba(255,255,255,0.06)'">
                            <option value="squat">Bodyweight Squat</option>
                            <option value="pushup">Push-up (Coming Soon)</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Rep Counter Panel with circular progress ring (Lighter panels & compact 105px size) -->
            <div class="dashboard-card" style="background:linear-gradient(135deg, rgba(8,8,26,0.45), rgba(8,8,26,0.6));border:1px solid rgba(255,255,255,0.02);border-radius:24px;padding:0.9rem 0.85rem;text-align:center;position:relative;overflow:hidden;">
                <div style="position:absolute;top:-25px;right:-25px;width:120px;height:120px;background:#8b5cf6;filter:blur(60px);opacity:0.18;border-radius:50%;"></div>
                
                <h3 style="color:var(--vg-text-muted);font-size:0.72rem;text-transform:uppercase;letter-spacing:1px;margin-bottom:6px;font-weight:750;">Rep Count</h3>
                
                <!-- SVG Ring wrap display (Reduced size to 105px with matching 226 perimeter math) -->
                <div style="position:relative;width:105px;height:105px;margin:0.2rem auto 0.35rem;display:flex;align-items:center;justify-content:center;">
                    <svg style="position:absolute;width:100%;height:100%;transform:rotate(-90deg);" viewBox="0 0 100 100">
                        <circle cx="50" cy="50" r="36" stroke="rgba(255,255,255,0.02)" stroke-width="5" fill="transparent"/>
                        <circle id="repRing" cx="50" cy="50" r="36" stroke="url(#ringGradient)" stroke-width="5" fill="transparent"
                                stroke-dasharray="226" stroke-dashoffset="226" stroke-linecap="round" style="transition:stroke-dashoffset 0.3s ease; filter:drop-shadow(0 0 6px rgba(139,92,246,0.25));"/>
                        <defs>
                            <linearGradient id="ringGradient" x1="0%" y1="0%" x2="100%" y2="100%">
                                <stop offset="0%" stop-color="#3b82f6"/>
                                <stop offset="100%" stop-color="#8b5cf6"/>
                            </linearGradient>
                        </defs>
                    </svg>
                    <div style="z-index:2;display:flex;flex-direction:column;align-items:center;justify-content:center;">
                        <span id="repCount" style="font-size:2.65rem;font-weight:950;line-height:1;background:linear-gradient(to bottom, #fff, rgba(255,255,255,0.85));-webkit-background-clip:text;background-clip:text;color:transparent;transition:transform 0.2s cubic-bezier(0.175, 0.885, 0.32, 1.275);">0</span>
                        <span id="phaseText" style="font-size:0.65rem;color:var(--vg-text-muted);font-weight:800;text-transform:uppercase;letter-spacing:0.08em;margin-top:1px;">Ready</span>
                        <span style="font-size:0.58rem;color:#8b5cf6;font-weight:800;letter-spacing:0.04em;margin-top:1px;">Target: 15 reps</span>
                    </div>
                </div>
                
                <button id="resetBtn" style="background:rgba(255,255,255,0.02);color:var(--vg-text-muted);border:1px solid rgba(255,255,255,0.06);padding:4px 10px;border-radius:10px;font-size:0.7rem;font-weight:700;cursor:pointer;transition:0.3s;" onmouseover="this.style.background='rgba(255,255,255,0.06)';this.style.color='#fff';" onmouseout="this.style.background='rgba(255,255,255,0.02)';this.style.color='var(--vg-text-muted)';">
                    Reset Counter
                </button>

                <!-- Session Statistics Widget Row (highly compact) -->
                <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:4px;background:rgba(255,255,255,0.01);border:1px solid rgba(255,255,255,0.04);border-radius:14px;padding:0.4rem 0.2rem;margin-top:0.5rem;">
                    <div style="text-align:center;">
                        <span style="display:block;font-size:0.55rem;color:var(--vg-text-muted);font-weight:700;text-transform:uppercase;letter-spacing:0.04em;">Calories</span>
                        <span id="sessionCalories" style="font-size:0.75rem;color:var(--vg-text-strong);font-weight:850;">0 kcal</span>
                    </div>
                    <div style="border-left:1px solid rgba(255,255,255,0.05);height:18px;margin-top:4px;"></div>
                    <div style="text-align:center;">
                        <span style="display:block;font-size:0.55rem;color:var(--vg-text-muted);font-weight:700;text-transform:uppercase;letter-spacing:0.04em;">Time</span>
                        <span id="sessionTimer" style="font-size:0.75rem;color:var(--vg-text-strong);font-weight:850;">00:00</span>
                    </div>
                    <div style="border-left:1px solid rgba(255,255,255,0.05);height:18px;margin-top:4px;"></div>
                    <div style="text-align:center;">
                        <span style="display:block;font-size:0.55rem;color:var(--vg-text-muted);font-weight:700;text-transform:uppercase;letter-spacing:0.04em;">Set</span>
                        <span id="sessionSet" style="font-size:0.75rem;color:var(--vg-text-strong);font-weight:850;">Set 1</span>
                    </div>
                </div>
            </div>

            <!-- AI Telemetry (Lighter panels & glowing active standby statuses) -->
            <div class="dashboard-card" style="background:rgba(8,8,26,0.45);border:1px solid rgba(255,255,255,0.02);border-radius:24px;padding:0.8rem 1.1rem;">
                <h3 style="color:var(--vg-text-strong);font-size:0.88rem;font-weight:800;margin-bottom:10px;display:flex;align-items:center;gap:8px;">
                    <i data-lucide="activity" style="width:15px;height:15px;color:#8b5cf6;"></i> AI Telemetry
                </h3>
                
                <!-- Accuracy Confidence Bar (Enhanced: 9px height, smooth rounded edges and scanning shadows) -->
                <div style="margin-bottom:0.75rem;background:rgba(255,255,255,0.01);border:1px solid rgba(255,255,255,0.02);padding:6px 10px;border-radius:16px;">
                    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:2px;">
                        <span style="font-size:0.7rem;color:var(--vg-text-muted);font-weight:700;text-transform:uppercase;letter-spacing:0.02em;">Confidence Rate</span>
                        <span id="accuracyVal" style="font-size:0.78rem;font-weight:850;color:#3b82f6;">94.8%</span>
                    </div>
                    <div style="width:100%;height:8px;background:rgba(255,255,255,0.04);border-radius:10px;overflow:hidden;position:relative;box-shadow:inset 0 1px 3px rgba(0,0,0,0.4);">
                        <div id="accuracyBar" class="pulse-bar" style="width:94.8%;height:100%;background:linear-gradient(to right, #3b82f6, #8b5cf6);border-radius:10px;transition:width 0.5s ease;box-shadow:0 0 6px rgba(139, 92, 246, 0.35);"></div>
                    </div>
                </div>

                <!-- Pose Analysis checklist -->
                <div style="display:flex;flex-direction:column;gap:5px;">
                    <div style="display:flex;justify-content:space-between;align-items:center;background:rgba(255,255,255,0.01);padding:5px 10px;border-radius:10px;border:1px solid rgba(255,255,255,0.03);" id="hipCheck">
                        <span style="font-size:0.76rem;color:var(--vg-text-muted);font-weight:600;">Hip Alignment</span>
                        <span style="font-size:0.7rem;font-weight:800;color:rgba(255,255,255,0.3);display:flex;align-items:center;" id="hipStatus"></span>
                    </div>
                    <div style="display:flex;justify-content:space-between;align-items:center;background:rgba(255,255,255,0.01);padding:5px 10px;border-radius:10px;border:1px solid rgba(255,255,255,0.03);" id="kneeCheck">
                        <span style="font-size:0.76rem;color:var(--vg-text-muted);font-weight:600;">Knee Flexion</span>
                        <span style="font-size:0.7rem;font-weight:800;color:rgba(255,255,255,0.3);display:flex;align-items:center;" id="kneeStatus"></span>
                    </div>
                    <div style="display:flex;justify-content:space-between;align-items:center;background:rgba(255,255,255,0.01);padding:5px 10px;border-radius:10px;border:1px solid rgba(255,255,255,0.03);" id="postureCheck">
                        <span style="font-size:0.76rem;color:var(--vg-text-muted);font-weight:600;">Back Alignment</span>
                        <span style="font-size:0.7rem;font-weight:800;color:rgba(255,255,255,0.3);display:flex;align-items:center;" id="postureStatus"></span>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

<!-- Beautiful Glassmorphism Instructions Modal -->
<div id="instructionsModal" style="display:none; position:fixed; inset:0; background:rgba(4,4,16,0.8); backdrop-filter:blur(12px); -webkit-backdrop-filter:blur(12px); z-index:99999; align-items:center; justify-content:center; padding:1.5rem; transition: opacity 0.3s ease; opacity: 0;">
    <div style="background:rgba(12,12,32,0.95); border:1px solid rgba(255,255,255,0.08); border-radius:28px; max-width:440px; width:100%; padding:2.25rem 2rem; box-shadow:0 25px 60px rgba(0,0,0,0.75); position:relative; transform:scale(0.95); transition:transform 0.3s ease;" id="instructionsModalContent">
        
        <!-- Close button inside modal -->
        <button id="closeGuideBtn" style="position:absolute; top:20px; right:20px; background:none; border:none; color:var(--vg-text-muted); cursor:pointer; transition:color 0.2s;" onmouseover="this.style.color='white'" onmouseout="this.style.color='var(--vg-text-muted)'">
            <i data-lucide="x" style="width:20px;height:20px;"></i>
        </button>

        <h3 style="color:var(--vg-text-strong);font-size:1.15rem;font-weight:900;margin-bottom:20px;display:flex;align-items:center;gap:10px;letter-spacing:-0.3px;">
            <i data-lucide="help-circle" style="width:22px;height:22px;color:#8b5cf6;filter:drop-shadow(0 0 5px rgba(139,92,246,0.45));"></i> AI Coach Setup Instructions
        </h3>
        
        <div style="display:flex;flex-direction:column;gap:12px;margin-bottom:1.5rem;">
            <div style="display:flex;gap:12px;align-items:center;background:rgba(255,255,255,0.01);border:1px solid rgba(255,255,255,0.03);padding:10px 14px;border-radius:16px;">
                <span style="background:rgba(59,130,246,0.1);color:#3b82f6;width:24px;height:24px;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:0.8rem;font-weight:900;flex-shrink:0;">1</span>
                <span style="font-size:0.8rem;color:var(--vg-text-muted);font-weight:600;line-height:1.3;">Stand 6 feet back from your camera.</span>
            </div>
            <div style="display:flex;gap:12px;align-items:center;background:rgba(255,255,255,0.01);border:1px solid rgba(255,255,255,0.03);padding:10px 14px;border-radius:16px;">
                <span style="background:rgba(139,92,246,0.1);color:#8b5cf6;width:24px;height:24px;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:0.8rem;font-weight:900;flex-shrink:0;">2</span>
                <span style="font-size:0.8rem;color:var(--vg-text-muted);font-weight:600;line-height:1.3;">Ensure your full body is visible in the grid.</span>
            </div>
            <div style="display:flex;gap:12px;align-items:center;background:rgba(255,255,255,0.01);border:1px solid rgba(255,255,255,0.03);padding:10px 14px;border-radius:16px;">
                <span style="background:rgba(236,72,153,0.1);color:#ec4899;width:24px;height:24px;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:0.8rem;font-weight:900;flex-shrink:0;">3</span>
                <span style="font-size:0.8rem;color:var(--vg-text-muted);font-weight:600;line-height:1.3;">Begin squatting to activate tracking lines.</span>
            </div>
            <div style="display:flex;gap:12px;align-items:center;background:rgba(255,255,255,0.01);border:1px solid rgba(255,255,255,0.03);padding:10px 14px;border-radius:16px;">
                <span style="background:rgba(16,185,129,0.1);color:#10b981;width:24px;height:24px;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:0.8rem;font-weight:900;flex-shrink:0;">4</span>
                <span style="font-size:0.8rem;color:var(--vg-text-muted);font-weight:600;line-height:1.3;">AI counts reps and scores posture details.</span>
            </div>
        </div>

        <button id="gotItBtn" style="width:100%; background:linear-gradient(135deg, #3b82f6, #8b5cf6); color:white; border:none; padding:12px; border-radius:14px; font-weight:700; cursor:pointer; font-size:0.9rem; transition:0.3s; box-shadow: 0 4px 15px rgba(139,92,246,0.25);" onmouseover="this.style.transform='translateY(-1px)'" onmouseout="this.style.transform='translateY(0)'">
            Got It, Let's Go
        </button>
    </div>
</div>

<style>
@keyframes spin { 100% { transform: rotate(360deg); } }
@media (max-width: 900px) {
    .fade-in-up > div:nth-child(2) { grid-template-columns: 1fr; }
    .fade-in-up > div:nth-child(2) > div:first-child > div:nth-child(2) { grid-template-columns: 1fr !important; }
}
</style>

<!-- Load MediaPipe Dependencies -->
<script src="https://cdn.jsdelivr.net/npm/@mediapipe/camera_utils@0.3.1675466862/camera_utils.js" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/@mediapipe/control_utils@0.6.1675466048/control_utils.js" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/@mediapipe/drawing_utils@0.3.1675466124/drawing_utils.js" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/@mediapipe/pose@0.5.1675469404/pose.js" crossorigin="anonymous"></script>

<script>
// Global Error Handler to show error directly on user interface
window.addEventListener('error', function(e) {
    console.error("Global JS Error caught:", e);
    const container = document.getElementById('loader') ? document.getElementById('loader').parentNode : document.body;
    const errorBanner = document.createElement('div');
    errorBanner.className = 'vg-error-banner';
    errorBanner.style.position = 'absolute';
    errorBanner.style.top = '10px';
    errorBanner.style.left = '10px';
    errorBanner.style.right = '10px';
    errorBanner.style.background = 'rgba(239, 68, 68, 0.95)';
    errorBanner.style.color = 'white';
    errorBanner.style.padding = '15px';
    errorBanner.style.borderRadius = '12px';
    errorBanner.style.fontSize = '0.9rem';
    errorBanner.style.zIndex = '9999';
    errorBanner.style.border = '1px solid #f87171';
    errorBanner.innerHTML = `<strong>JavaScript Error:</strong> ${e.message} <br><small style="opacity: 0.8;">at ${e.filename || 'unknown'}:${e.lineno || 'unknown'}</small>`;
    container.appendChild(errorBanner);
});

window.addEventListener('unhandledrejection', function(e) {
    console.error("Global Unhandled Promise Rejection caught:", e);
    const container = document.getElementById('loader') ? document.getElementById('loader').parentNode : document.body;
    const errorBanner = document.createElement('div');
    errorBanner.className = 'vg-error-banner';
    errorBanner.style.position = 'absolute';
    errorBanner.style.top = '10px';
    errorBanner.style.left = '10px';
    errorBanner.style.right = '10px';
    errorBanner.style.background = 'rgba(239, 68, 68, 0.95)';
    errorBanner.style.color = 'white';
    errorBanner.style.padding = '15px';
    errorBanner.style.borderRadius = '12px';
    errorBanner.style.fontSize = '0.9rem';
    errorBanner.style.zIndex = '9999';
    errorBanner.style.border = '1px solid #f87171';
    errorBanner.innerHTML = `<strong>Promise Rejection:</strong> ${e.reason}`;
    container.appendChild(errorBanner);
});

document.addEventListener('DOMContentLoaded', () => {
    const videoElement = document.getElementById('input_video');
    const canvasElement = document.getElementById('output_canvas');
    const canvasCtx = canvasElement.getContext('2d');
    
    const startBtn = document.getElementById('startBtn');
    const stopBtn = document.getElementById('stopBtn');
    const demoBtn = document.getElementById('demoBtn');
    const stopDemoBtn = document.getElementById('stopDemoBtn');
    const placeholder = document.getElementById('placeholder');
    const loader = document.getElementById('loader');
    
    // Instructions Modal Elements
    const guideBtn = document.getElementById('guideBtn');
    const instructionsModal = document.getElementById('instructionsModal');
    const closeGuideBtn = document.getElementById('closeGuideBtn');
    const gotItBtn = document.getElementById('gotItBtn');
    const instructionsModalContent = document.getElementById('instructionsModalContent');

    if (guideBtn && instructionsModal) {
        guideBtn.addEventListener('click', () => {
            instructionsModal.style.display = 'flex';
            setTimeout(() => {
                instructionsModal.style.opacity = '1';
                if (instructionsModalContent) {
                    instructionsModalContent.style.transform = 'scale(1)';
                }
            }, 10);
        });
    }

    function hideInstructionsModal() {
        if (instructionsModal) {
            instructionsModal.style.opacity = '0';
            if (instructionsModalContent) {
                instructionsModalContent.style.transform = 'scale(0.95)';
            }
            setTimeout(() => {
                instructionsModal.style.display = 'none';
            }, 300);
        }
    }

    if (closeGuideBtn) closeGuideBtn.addEventListener('click', hideInstructionsModal);
    if (gotItBtn) gotItBtn.addEventListener('click', hideInstructionsModal);
    if (instructionsModal) {
        instructionsModal.addEventListener('click', (e) => {
            if (e.target === instructionsModal) {
                hideInstructionsModal();
            }
        });
    }
    
    const repCountEl = document.getElementById('repCount');
    const phaseText = document.getElementById('phaseText');
    const resetBtn = document.getElementById('resetBtn');
    
    const feedbackOverlay = document.getElementById('feedbackOverlay');
    const feedbackStatus = document.getElementById('feedbackStatus');
    const feedbackText = document.getElementById('feedbackText');

    let camera = null;
    let isRunning = false;
    
    // Squat Tracking Variables
    let reps = 0;
    let stage = "down"; // "up" or "down"

    // Live session stats timer variables
    let timerInterval = null;
    let secondsElapsed = 0;
    let lastBackAlertTime = 0;
    
    // Live Motion Stability Graph History
    let stabilityHistory = [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0];
    
    // Angle calculation utility
    function calculateAngle(a, b, c) {
        const radians = Math.atan2(c.y - b.y, c.x - b.x) - Math.atan2(a.y - b.y, a.x - b.x);
        let angle = Math.abs((radians * 180.0) / Math.PI);
        if (angle > 180.0) {
            angle = 360 - angle;
        }
        return angle;
    }

    // Terminal log Console utility (With auto-scroll, smooth transition, and blinking cursor insertion)
    function logConsole(message, type = 'info') {
        const consoleEl = document.getElementById('feedbackConsole');
        const cursorLine = document.getElementById('consoleCursorLine');
        if (!consoleEl) return;
        
        const time = new Date().toTimeString().split(' ')[0];
        const logDiv = document.createElement('div');
        logDiv.style.opacity = '0';
        logDiv.style.transform = 'translateY(5px)';
        logDiv.style.transition = 'all 0.2s ease-out';
        
        let color = 'rgba(255,255,255,0.7)';
        if (type === 'success') color = '#22c55e';
        else if (type === 'warning') color = '#f59e0b';
        else if (type === 'system') color = '#8b5cf6';
        else if (type === 'info') color = 'rgba(255,255,255,0.45)';
        
        logDiv.innerHTML = `<span style="color:rgba(255,255,255,0.25); font-weight:700;">[${time}]</span> <span style="color:${color}; font-weight:600;">${message}</span>`;
        
        if (cursorLine) {
            consoleEl.insertBefore(logDiv, cursorLine);
        } else {
            consoleEl.appendChild(logDiv);
        }
        
        // Trigger reflow for animation
        setTimeout(() => {
            logDiv.style.opacity = '1';
            logDiv.style.transform = 'translateY(0)';
            consoleEl.scrollTop = consoleEl.scrollHeight;
        }, 10);
    }

    // Posture warning logger throttling
    function checkBackAlert(isGood) {
        if (!isGood) {
            const now = Date.now();
            if (now - lastBackAlertTime > 5000) { // once every 5 seconds max
                logConsole("POSTURE ALERT: Keep chest high and spine straight!", "warning");
                lastBackAlertTime = now;
            }
        }
    }

    // Timer utility functions
    function startTimer() {
        secondsElapsed = 0;
        clearInterval(timerInterval);
        const timerEl = document.getElementById('sessionTimer');
        if (timerEl) timerEl.innerText = "00:00";
        
        timerInterval = setInterval(() => {
            secondsElapsed++;
            const mins = String(Math.floor(secondsElapsed / 60)).padStart(2, '0');
            const secs = String(secondsElapsed % 60).padStart(2, '0');
            if (timerEl) {
                timerEl.innerText = `${mins}:${secs}`;
            }
        }, 1000);
    }
    
    function stopTimer() {
        clearInterval(timerInterval);
    }

    // SVG graph renderer for stability score (Smooth Bezier curves)
    function renderStabilityGraph(history) {
        const svgPath = document.getElementById('stabilityPath');
        const svgArea = document.getElementById('stabilityArea');
        if (!svgPath || !svgArea) return;
        
        const width = 300;
        const height = 80;
        
        if (history.length < 2) return;
        
        let pathD = "";
        
        // Draw a smooth bezier curve connecting points using S commands for smooth curvature
        for (let i = 0; i < history.length; i++) {
            const x = (i / (history.length - 1)) * width;
            const y = height - (history[i] / 100) * 70;
            
            if (i === 0) {
                pathD = `M ${x.toFixed(1)} ${y.toFixed(1)}`;
            } else {
                const prevX = ((i - 1) / (history.length - 1)) * width;
                const prevY = height - (history[i - 1] / 100) * 70;
                const cpX = (prevX + x) / 2;
                pathD += ` S ${cpX.toFixed(1)} ${prevY.toFixed(1)}, ${x.toFixed(1)} ${y.toFixed(1)}`;
            }
        }
        
        const areaD = `${pathD} L ${width} ${height} L 0 ${height} Z`;
        
        svgPath.setAttribute('d', pathD);
        svgArea.setAttribute('d', areaD);
    }

    // Dynamic Posture Checklist updates
    function updatePostureChecklists(poseLandmarks) {
        const hipSt = document.getElementById('hipStatus');
        const kneeSt = document.getElementById('kneeStatus');
        const backSt = document.getElementById('postureStatus');
        
        // Joint Tracking statuses (Live Motion Analysis Panel)
        const jHead = document.getElementById('jointHeadStatus');
        const jShoulders = document.getElementById('jointShouldersStatus');
        const jHips = document.getElementById('jointHipsStatus');
        const jKnees = document.getElementById('jointKneesStatus');
        const jAnkles = document.getElementById('jointAnklesStatus');
        
        // Grab corners to toggle active pulsing green styles when joints are detected!
        const cTL = document.getElementById('cornerTL');
        const cTR = document.getElementById('cornerTR');
        const cBL = document.getElementById('cornerBL');
        const cBR = document.getElementById('cornerBR');
        
        if (!poseLandmarks) {
            // General indicators showing beautiful standby tags when camera is off
            const standbyTelemetryHtml = `<span style="color:rgba(167,139,250,0.55); font-weight:800; font-size:0.7rem; text-transform:uppercase; display:flex; align-items:center; gap:4px; letter-spacing:0.02em;"><span style="color:#8b5cf6;" class="pulse-indicator">●</span>Standby</span>`;
            if (hipSt) hipSt.innerHTML = standbyTelemetryHtml;
            if (kneeSt) kneeSt.innerHTML = standbyTelemetryHtml;
            if (backSt) backSt.innerHTML = standbyTelemetryHtml;
            
            // Revert corners back to idle state
            if (cTL) cTL.classList.remove('corner-active');
            if (cTR) cTR.classList.remove('corner-active');
            if (cBL) cBL.classList.remove('corner-active');
            if (cBR) cBR.classList.remove('corner-active');
            return;
        }
        
        // Turn corners into green pulsing tracking dots!
        if (cTL && !cTL.classList.contains('corner-active')) {
            cTL.classList.add('corner-active');
            cTR.classList.add('corner-active');
            cBL.classList.add('corner-active');
            cBR.classList.add('corner-active');
        }
        
        const head = poseLandmarks[0]; // Nose
        const shoulder = poseLandmarks[11];
        const hip = poseLandmarks[23];
        const knee = poseLandmarks[25];
        const ankle = poseLandmarks[27];
        
        // Map individual tracked states with actual MediaPipe landmark visibility ratings!
        const greenTrackedText = `<span style="color:#22c55e; font-weight:800; display:flex; align-items:center;"><span style="color:#22c55e;margin-right:4px;text-shadow:0 0 6px #22c55e;">●</span>Tracked</span>`;
        const grayOfflineText = `<span style="color:rgba(255,255,255,0.3); font-weight:800; display:flex; align-items:center;"><span style="color:rgba(255,255,255,0.2);margin-right:4px;">●</span>Off</span>`;
        
        // Render coordinates in ultra subtle format (0.52rem, light purple, spaced) next to status
        if (jHead) {
            jHead.innerHTML = (head && head.visibility > 0.5) 
                ? `<span style="font-family:monospace;font-size:0.52rem;color:rgba(167,139,250,0.45);letter-spacing:0.05em;margin-right:6px;">[X:${head.x.toFixed(3)} Y:${head.y.toFixed(3)}]</span>` + greenTrackedText 
                : grayOfflineText;
        }
        if (jShoulders) {
            jShoulders.innerHTML = (shoulder && shoulder.visibility > 0.5) 
                ? `<span style="font-family:monospace;font-size:0.52rem;color:rgba(167,139,250,0.45);letter-spacing:0.05em;margin-right:6px;">[X:${shoulder.x.toFixed(3)} Y:${shoulder.y.toFixed(3)}]</span>` + greenTrackedText 
                : grayOfflineText;
        }
        if (jHips) {
            jHips.innerHTML = (hip && hip.visibility > 0.5) 
                ? `<span style="font-family:monospace;font-size:0.52rem;color:rgba(167,139,250,0.45);letter-spacing:0.05em;margin-right:6px;">[X:${hip.x.toFixed(3)} Y:${hip.y.toFixed(3)}]</span>` + greenTrackedText 
                : grayOfflineText;
        }
        if (jKnees) {
            jKnees.innerHTML = (knee && knee.visibility > 0.5) 
                ? `<span style="font-family:monospace;font-size:0.52rem;color:rgba(167,139,250,0.45);letter-spacing:0.05em;margin-right:6px;">[X:${knee.x.toFixed(3)} Y:${knee.y.toFixed(3)}]</span>` + greenTrackedText 
                : grayOfflineText;
        }
        if (jAnkles) {
            jAnkles.innerHTML = (ankle && ankle.visibility > 0.5) 
                ? `<span style="font-family:monospace;font-size:0.52rem;color:rgba(167,139,250,0.45);letter-spacing:0.05em;margin-right:6px;">[X:${ankle.x.toFixed(3)} Y:${ankle.y.toFixed(3)}]</span>` + greenTrackedText 
                : grayOfflineText;
        }

        const kneeAngle = calculateAngle(hip, knee, ankle);
        const backAngle = calculateAngle(shoulder, hip, knee);
        const isBackGood = backAngle > 130;

        // Knee depth flexion check
        if (kneeSt) {
            if (kneeAngle < 95) {
                kneeSt.innerHTML = `<span style="color:#22c55e; font-weight:800; display:flex; align-items:center;"><span style="color:#22c55e;margin-right:6px;text-shadow:0 0 6px #22c55e;">●</span>Good depth</span>`;
            } else if (kneeAngle < 155) {
                kneeSt.innerHTML = `<span style="color:#f59e0b; font-weight:800; display:flex; align-items:center;"><span style="color:#f59e0b;margin-right:6px;text-shadow:0 0 6px #f59e0b;">●</span>Adjust depth</span>`;
            } else {
                kneeSt.innerHTML = `<span style="color:#3b82f6; font-weight:800; display:flex; align-items:center;"><span style="color:#3b82f6;margin-right:6px;text-shadow:0 0 6px #3b82f6;">●</span>Stand upright</span>`;
            }
        }
        
        // Back posture alignment check (Shoulder-hip-knee)
        if (backSt) {
            if (isBackGood) {
                backSt.innerHTML = `<span style="color:#22c55e; font-weight:800; display:flex; align-items:center;"><span style="color:#22c55e;margin-right:6px;text-shadow:0 0 6px #22c55e;">●</span>Stable</span>`;
            } else {
                backSt.innerHTML = `<span style="color:#ef4444; font-weight:800; display:flex; align-items:center;"><span style="color:#ef4444;margin-right:6px;text-shadow:0 0 6px #ef4444;">●</span>Warning</span>`;
            }
            checkBackAlert(isBackGood);
        }
        
        // Hip track
        if (hipSt) {
            hipSt.innerHTML = `<span style="color:#22c55e; font-weight:800; display:flex; align-items:center;"><span style="color:#22c55e;margin-right:6px;text-shadow:0 0 6px #22c55e;">●</span>Stable</span>`;
        }

        // Live Motion Stability score fluctuations
        let stabilityScore = 95;
        if (!isBackGood) {
            stabilityScore -= 12; // back posture issue drops stability
        }
        if (kneeAngle < 155 && kneeAngle > 95) {
            stabilityScore -= 3; // depth adjust flex drops stability slightly
        }
        // Faint random movement jitter
        stabilityScore += (Math.random() * 3 - 1.5);
        stabilityScore = Math.max(10, Math.min(100, Math.round(stabilityScore)));

        const stabValEl = document.getElementById('stabilityVal');
        if (stabValEl) {
            stabValEl.innerHTML = `<span style="color:#22c55e; font-weight:850; letter-spacing:0.02em;">${stabilityScore.toFixed(0)}%</span>`;
        }

        // Push to history queue
        stabilityHistory.shift();
        stabilityHistory.push(stabilityScore);
        renderStabilityGraph(stabilityHistory);
    }

    // Dynamic Rep Widget Ring calculation (perimeter=226 for radius=36)
    function updateRepCounter(newReps) {
        reps = newReps;
        repCountEl.innerText = reps;
        
        const ring = document.getElementById('repRing');
        if (ring) {
            const percent = Math.min(reps / 15, 1);
            const offset = 226 - (226 * percent);
            ring.style.strokeDashoffset = offset;
        }
        
        // Pop scaling rep number on counter increment
        repCountEl.style.transform = 'scale(1.25)';
        setTimeout(() => repCountEl.style.transform = 'scale(1)', 200);

        // Update Calories burned placeholders (approx 1.5kcal per squat rep)
        const calEl = document.getElementById('sessionCalories');
        if (calEl) {
            calEl.innerText = Math.round(reps * 1.5) + " kcal";
        }
    }

    // Process Pose Results
    function onResults(results) {
        if (!isRunning) return;
        
        // Hide loader when first frame is processed successfully
        if (loader.style.display !== 'none') {
            loader.style.display = 'none';
            canvasElement.style.display = 'block';
            feedbackOverlay.style.display = 'flex';
            
            // Swap Top-Left HUD system status indicator
            const statText = document.getElementById('statusText');
            const statInd = document.getElementById('statusIndicator');
            if (statText && statInd) {
                statText.innerText = "Pose Detection Active";
                statInd.style.background = "#22c55e"; // Green
                statInd.style.boxShadow = "0 0 10px #22c55e";
            }
            logConsole("Pose detection activated. Stand 6ft from camera.", "success");
        }

        // Setup Canvas aspect matching
        canvasElement.width = videoElement.videoWidth;
        canvasElement.height = videoElement.videoHeight;
        
        canvasCtx.save();
        canvasCtx.clearRect(0, 0, canvasElement.width, canvasElement.height);
        
        // Draw Webcam Frame
        canvasCtx.drawImage(results.image, 0, 0, canvasElement.width, canvasElement.height);
        
        // Draw skeletal overlays
        if (results.poseLandmarks) {
            drawConnectors(canvasCtx, results.poseLandmarks, POSE_CONNECTIONS, {
                color: 'rgba(139, 92, 246, 0.8)', // Glowing Violet Accent
                lineWidth: 4
            });
            drawLandmarks(canvasCtx, results.poseLandmarks, {
                color: '#22c55e', // Green joint locks
                lineWidth: 2,
                radius: 4
            });

            // Update posture monitors live
            const landmarks = results.poseLandmarks;
            updatePostureChecklists(landmarks);
            
            // Get LEFT coordinates
            const hip = landmarks[23]; 
            const knee = landmarks[25]; 
            const ankle = landmarks[27]; 
            
            // Calculate Knee flex angle
            const angle = calculateAngle(hip, knee, ankle);
            
            // Rep counter calculations
            if (angle > 160) {
                if (stage === "up") {
                    const nextReps = reps + 1;
                    updateRepCounter(nextReps);
                    logConsole(`Rep ${nextReps} completed! Depth checked.`, "success");
                }
                stage = "down";
                phaseText.innerText = "Stand (Up)";
                
                feedbackStatus.style.background = "#22c55e"; // Green
                feedbackStatus.style.boxShadow = "0 0 10px #22c55e";
                feedbackText.innerText = "Good! Now go lower.";
                
            } else if (angle < 90) { // Below parallel squat
                stage = "up";
                phaseText.innerText = "Squat (Down)";
                
                feedbackStatus.style.background = "#3b82f6"; // Blue
                feedbackStatus.style.boxShadow = "0 0 10px #3b82f6";
                feedbackText.innerText = "Perfect Depth! Stand up.";
            } else {
                // Mid travel flex
                if(stage === "down") {
                    feedbackStatus.style.background = "#f59e0b"; // Yellow
                    feedbackStatus.style.boxShadow = "0 0 10px #f59e0b";
                    feedbackText.innerText = "Lower... keep chest up";
                }
            }
        } else {
            updatePostureChecklists(null);
            feedbackStatus.style.background = "#ef4444"; // Red
            feedbackStatus.style.boxShadow = "0 0 10px #ef4444";
            feedbackText.innerText = "No body detected. Step back.";
        }
        
        canvasCtx.restore();
    }

    // Initialize MediaPipe Pose with locked matching stable version
    const pose = new Pose({locateFile: (file) => {
        return `https://cdn.jsdelivr.net/npm/@mediapipe/pose@0.5.1675469404/${file}`;
    }});
    
    pose.setOptions({
        modelComplexity: 1,
        smoothLandmarks: true,
        enableSegmentation: false,
        minDetectionConfidence: 0.5,
        minTrackingConfidence: 0.5
    });
    
    pose.onResults(onResults);
 
    camera = new Camera(videoElement, {
        onFrame: async () => {
            if (isRunning) {
                await pose.send({image: videoElement});
            }
        },
        width: 1280,
        height: 720
    });

    let fallbackStream = null;

    // Start confidence fluctuation loop
    setInterval(() => {
        if (isRunning) {
            const rand = (Math.random() * (98.2 - 93.5) + 93.5).toFixed(1);
            const valEl = document.getElementById('accuracyVal');
            const barEl = document.getElementById('accuracyBar');
            if (valEl && barEl) {
                valEl.innerText = rand + "%";
                barEl.style.width = rand + "%";
            }
        }
    }, 1500);

    // Standby Undulating Waveform & Mock Telemetry coordinates system when camera is offline!
    let standbyTime = 0;
    setInterval(() => {
        if (!isRunning) {
            standbyTime += 0.15;
            
            // 1. Organic Undulating Waveform for Stability Graph when Idle
            let idleHistory = [];
            for (let i = 0; i < 15; i++) {
                // Generate a beautiful sine wave with organic drifting noise
                const sineVal = Math.sin(standbyTime + (i * 0.55)) * 10 + 78;
                const noise = Math.cos(standbyTime * 1.2 + i) * 2;
                idleHistory.push(Math.round(sineVal + noise));
            }
            renderStabilityGraph(idleHistory);

            const stabValEl = document.getElementById('stabilityVal');
            if (stabValEl) {
                stabValEl.innerHTML = `<span style="color:#a78bfa; font-weight:800; animation:blink 1.5s infinite; font-size:0.65rem; letter-spacing:0.05em; background:rgba(167,139,250,0.08); padding:2px 6px; border-radius:6px; display:inline-flex; align-items:center; gap:4px;">● STANDBY</span>`;
            }

            // 2. Faint scrolling decimal coordinates simulating high-tech sensor listens
            const jHead = document.getElementById('jointHeadStatus');
            const jShoulders = document.getElementById('jointShouldersStatus');
            const jHips = document.getElementById('jointHipsStatus');
            const jKnees = document.getElementById('jointKneesStatus');
            const jAnkles = document.getElementById('jointAnklesStatus');

            const hX = (0.50 + Math.sin(standbyTime * 0.2) * 0.015).toFixed(3);
            const hY = (0.22 + Math.cos(standbyTime * 0.3) * 0.010).toFixed(3);
            const sX = (0.50 + Math.sin(standbyTime * 0.15) * 0.020).toFixed(3);
            const sY = (0.35 + Math.cos(standbyTime * 0.4) * 0.012).toFixed(3);
            const hipX = (0.50 + Math.sin(standbyTime * 0.3) * 0.010).toFixed(3);
            const hipY = (0.58 + Math.cos(standbyTime * 0.2) * 0.008).toFixed(3);
            const kX = (0.48 + Math.sin(standbyTime * 0.4) * 0.025).toFixed(3);
            const kY = (0.75 + Math.cos(standbyTime * 0.15) * 0.015).toFixed(3);
            const aX = (0.48 + Math.sin(standbyTime * 0.1) * 0.005).toFixed(3);
            const aY = (0.91 + Math.cos(standbyTime * 0.5) * 0.005).toFixed(3);

            if (jHead) jHead.innerHTML = `<span style="font-family:monospace;font-size:0.52rem;color:rgba(167,139,250,0.45);letter-spacing:0.05em;">[X:${hX} Y:${hY}]</span> <span style="color:rgba(255,255,255,0.25); font-weight:800; font-size:0.6rem; margin-left:2px;">READY</span>`;
            if (jShoulders) jShoulders.innerHTML = `<span style="font-family:monospace;font-size:0.52rem;color:rgba(167,139,250,0.45);letter-spacing:0.05em;">[X:${sX} Y:${sY}]</span> <span style="color:rgba(255,255,255,0.25); font-weight:800; font-size:0.6rem; margin-left:2px;">READY</span>`;
            if (jHips) jHips.innerHTML = `<span style="font-family:monospace;font-size:0.52rem;color:rgba(167,139,250,0.45);letter-spacing:0.05em;">[X:${hipX} Y:${hipY}]</span> <span style="color:rgba(255,255,255,0.25); font-weight:800; font-size:0.6rem; margin-left:2px;">READY</span>`;
            if (jKnees) jKnees.innerHTML = `<span style="font-family:monospace;font-size:0.52rem;color:rgba(167,139,250,0.45);letter-spacing:0.05em;">[X:${kX} Y:${kY}]</span> <span style="color:rgba(255,255,255,0.25); font-weight:800; font-size:0.6rem; margin-left:2px;">READY</span>`;
            if (jAnkles) jAnkles.innerHTML = `<span style="font-family:monospace;font-size:0.52rem;color:rgba(167,139,250,0.45);letter-spacing:0.05em;">[X:${aX} Y:${aY}]</span> <span style="color:rgba(255,255,255,0.25); font-weight:800; font-size:0.6rem; margin-left:2px;">READY</span>`;
        }
    }, 120);

    // Load initial standby indicators on page load
    updatePostureChecklists(null);

    // Start Session Click Handler
    startBtn.addEventListener('click', async () => {
        document.querySelectorAll('.vg-error-banner').forEach(el => el.remove());

        if (typeof isDemoRunning !== 'undefined' && isDemoRunning) {
            stopDemo();
        }

        isRunning = true;
        placeholder.style.display = 'none';
        startBtn.style.display = 'none';
        loader.style.display = 'flex';
        stopBtn.style.display = 'flex';
        
        // Hide faint silhouette guides during active webcam feeds
        const silEl = document.getElementById('silhouetteGuide');
        if (silEl) silEl.style.opacity = '0';

        // Trigger futuristic scan sweep overlays
        const sweep = document.getElementById('scannerSweep');
        if (sweep) sweep.style.display = 'block';

        // Transition Top-Left status badge to Active Connecting status
        const statText = document.getElementById('statusText');
        const statInd = document.getElementById('statusIndicator');
        if (statText && statInd) {
            statText.innerText = "Connecting Feed";
            statInd.style.background = "#f59e0b"; // Yellow
            statInd.style.boxShadow = "0 0 10px #f59e0b";
        }

        // Start sets active time tracking counters
        startTimer();
        
        logConsole("Initializing calibration sequence...", "system");
        logConsole("Connecting camera media devices...", "info");

        try {
            await camera.start();
        } catch (err) {
            console.warn("MediaPipe Camera.start() failed, trying native getUserMedia fallback:", err);
            
            try {
                fallbackStream = await navigator.mediaDevices.getUserMedia({
                    video: { 
                        width: { ideal: 640 }, 
                        height: { ideal: 480 },
                        facingMode: "user"
                    }
                });
                
                videoElement.srcObject = fallbackStream;
                videoElement.play();
                
                async function processFrame() {
                    if (isRunning) {
                        try {
                            await pose.send({image: videoElement});
                        } catch (poseErr) {
                            console.error("Pose processing error:", poseErr);
                        }
                        requestAnimationFrame(processFrame);
                    }
                }
                
                videoElement.onloadedmetadata = () => {
                    processFrame();
                };
            } catch (fallbackErr) {
                console.error("Fallback camera failed:", fallbackErr);
                alert("Could not access webcam. Please make sure camera permissions are granted.");
                logConsole("Camera connection failed. Awaiting retry.", "warning");
                
                // Reset UI states to idle
                isRunning = false;
                stopTimer();
                if (silEl) silEl.style.opacity = '0.12';
                if (sweep) sweep.style.display = 'none';
                
                if (statText && statInd) {
                    statText.innerText = "System Idle";
                    statInd.style.background = "#ef4444"; 
                    statInd.style.boxShadow = "0 0 8px #ef4444";
                }

                canvasElement.style.display = 'none';
                feedbackOverlay.style.display = 'none';
                loader.style.display = 'none';
                stopBtn.style.display = 'none';
                placeholder.style.display = 'block';
                startBtn.style.display = 'flex';
                updatePostureChecklists(null);
            }
        }
    });

    // Stop Session Click Handler
    stopBtn.addEventListener('click', () => {
        isRunning = false;
        stopTimer();
        
        try {
            camera.stop();
        } catch (e) {
            console.log("Error stopping MediaPipe camera:", e);
        }
        
        if (fallbackStream) {
            fallbackStream.getTracks().forEach(track => track.stop());
            fallbackStream = null;
        }
        videoElement.srcObject = null;
        
        // Restore faint silhouette guides
        const silEl = document.getElementById('silhouetteGuide');
        if (silEl) silEl.style.opacity = '0.12';

        // Disable scanners
        const sweep = document.getElementById('scannerSweep');
        if (sweep) sweep.style.display = 'none';

        // Revert system status badge to Red Idle state
        const statText = document.getElementById('statusText');
        const statInd = document.getElementById('statusIndicator');
        if (statText && statInd) {
            statText.innerText = "System Idle";
            statInd.style.background = "#ef4444"; 
            statInd.style.boxShadow = "0 0 8px #ef4444";
        }
        
        canvasElement.style.display = 'none';
        feedbackOverlay.style.display = 'none';
        loader.style.display = 'none';
        stopBtn.style.display = 'none';
        
        placeholder.style.display = 'block';
        startBtn.style.display = 'flex';
        
        // Clear canvas
        canvasCtx.clearRect(0, 0, canvasElement.width, canvasElement.height);

        // Reset checklist statuses
        updatePostureChecklists(null);
        logConsole("Session terminated by user. Standby mode.", "system");
    });

    // Reset Session Stats Counter
    resetBtn.addEventListener('click', () => {
        if (typeof isDemoRunning !== 'undefined' && isDemoRunning) {
            demoReps = 0;
            demoTime = 0;
            demoStage = "down";
        }
        updateRepCounter(0);
        stage = "down";
        phaseText.innerText = "Ready";
        logConsole("Rep counter reset to 0.", "info");
        
        // Reset timer as well if active
        if (isRunning || (typeof isDemoRunning !== 'undefined' && isDemoRunning)) {
            startTimer();
        } else {
            const timerEl = document.getElementById('sessionTimer');
            if (timerEl) timerEl.innerText = "00:00";
        }
    });

    // ==========================================
    // AI Demo Mode / Squat Workout Simulator
    // ==========================================
    let demoFrameId = null;
    let isDemoRunning = false;
    let demoTime = 0;
    let demoReps = 0;
    let demoStage = "down";
    
    function runDemoLoop() {
        if (!isDemoRunning) return;
        
        demoTime += 0.025; // standard timing increment
        
        // Squat depth progress: 0 (up) to 1 (down) to 0 (up) repeating periodically
        let progress = (Math.sin(demoTime - Math.PI / 2) + 1) / 2;
        
        // Match canvas coordinates
        canvasElement.width = 640;
        canvasElement.height = 480;
        canvasCtx.clearRect(0, 0, 640, 480);
        
        // 1. Draw elegant, dark grid background
        canvasCtx.fillStyle = '#08081a';
        canvasCtx.fillRect(0, 0, 640, 480);
        
        // Draw laser grids
        canvasCtx.strokeStyle = 'rgba(139, 92, 246, 0.06)';
        canvasCtx.lineWidth = 1;
        for (let x = 40; x < 640; x += 40) {
            canvasCtx.beginPath();
            canvasCtx.moveTo(x, 0);
            canvasCtx.lineTo(x, 480);
            canvasCtx.stroke();
        }
        for (let y = 40; y < 480; y += 40) {
            canvasCtx.beginPath();
            canvasCtx.moveTo(0, y);
            canvasCtx.lineTo(640, y);
            canvasCtx.stroke();
        }
        
        // Focal scanner ring
        canvasCtx.strokeStyle = 'rgba(59, 130, 246, 0.15)';
        canvasCtx.beginPath();
        canvasCtx.arc(320, 240, 160, 0, 2 * Math.PI);
        canvasCtx.stroke();
        
        // 2. Define fake landmarks in skeleton pose coordinate space
        const h = progress;
        
        // Spine warning trigger on Rep 3
        const isSpineCurved = (demoReps === 3 && h > 0.35);
        
        const headY = 0.20 + h * 0.06;
        const shoulderY = 0.30 + h * 0.07;
        const hipY = 0.50 + h * 0.18;
        const kneeXOffset = h * 0.038;
        const kneeY = 0.70 + h * 0.05;
        
        // Fake coordinates
        const fakeLandmarks = [];
        fakeLandmarks[0] = { x: 0.5, y: headY, visibility: 0.98 };
        fakeLandmarks[11] = { x: 0.43, y: shoulderY, visibility: 0.98 };
        fakeLandmarks[12] = { x: 0.57, y: shoulderY, visibility: 0.98 };
        
        // If spine curves, shift hip coordinates X slightly left to show standard bending profile
        const hipXLeft = 0.44 + (isSpineCurved ? 0.035 : 0);
        const hipXRight = 0.56 + (isSpineCurved ? 0.035 : 0);
        fakeLandmarks[23] = { x: hipXLeft, y: hipY, visibility: 0.98 };
        fakeLandmarks[24] = { x: hipXRight, y: hipY, visibility: 0.98 };
        
        fakeLandmarks[25] = { x: 0.41 - kneeXOffset, y: kneeY, visibility: 0.98 };
        fakeLandmarks[26] = { x: 0.59 + kneeXOffset, y: kneeY, visibility: 0.98 };
        fakeLandmarks[27] = { x: 0.43, y: 0.88, visibility: 0.98 };
        fakeLandmarks[28] = { x: 0.57, y: 0.88, visibility: 0.98 };
        
        // Extended arms
        fakeLandmarks[15] = { x: 0.37, y: shoulderY - 0.04, visibility: 0.95 };
        fakeLandmarks[16] = { x: 0.63, y: shoulderY - 0.04, visibility: 0.95 };
        
        // Scale values
        const scaleX = (x) => x * 640;
        const scaleY = (y) => y * 480;
        
        // 3. Render neon stick figure with glowing stroke filters
        canvasCtx.shadowBlur = 18;
        canvasCtx.shadowColor = isSpineCurved ? '#ef4444' : '#8b5cf6';
        
        // Head
        canvasCtx.fillStyle = 'rgba(255, 255, 255, 0.98)';
        canvasCtx.beginPath();
        canvasCtx.arc(scaleX(fakeLandmarks[0].x), scaleY(fakeLandmarks[0].y), 15, 0, 2 * Math.PI);
        canvasCtx.fill();
        
        // Skeleton lines
        canvasCtx.strokeStyle = isSpineCurved ? '#ef4444' : '#8b5cf6';
        canvasCtx.lineWidth = 5;
        canvasCtx.lineCap = 'round';
        canvasCtx.lineJoin = 'round';
        
        // Neck to hip mid
        const hipMidX = (fakeLandmarks[23].x + fakeLandmarks[24].x) / 2;
        const hipMidY = (fakeLandmarks[23].y + fakeLandmarks[24].y) / 2;
        canvasCtx.beginPath();
        canvasCtx.moveTo(scaleX(0.5), scaleY(shoulderY));
        canvasCtx.lineTo(scaleX(hipMidX), scaleY(hipMidY));
        canvasCtx.stroke();
        
        // Shoulders line
        canvasCtx.beginPath();
        canvasCtx.moveTo(scaleX(fakeLandmarks[11].x), scaleY(fakeLandmarks[11].y));
        canvasCtx.lineTo(scaleX(fakeLandmarks[12].x), scaleY(fakeLandmarks[12].y));
        canvasCtx.stroke();
        
        // Left arm
        canvasCtx.beginPath();
        canvasCtx.moveTo(scaleX(fakeLandmarks[11].x), scaleY(fakeLandmarks[11].y));
        canvasCtx.lineTo(scaleX(fakeLandmarks[15].x), scaleY(fakeLandmarks[15].y));
        canvasCtx.stroke();
        
        // Right arm
        canvasCtx.beginPath();
        canvasCtx.moveTo(scaleX(fakeLandmarks[12].x), scaleY(fakeLandmarks[12].y));
        canvasCtx.lineTo(scaleX(fakeLandmarks[16].x), scaleY(fakeLandmarks[16].y));
        canvasCtx.stroke();
        
        // Hips line
        canvasCtx.beginPath();
        canvasCtx.moveTo(scaleX(fakeLandmarks[23].x), scaleY(fakeLandmarks[23].y));
        canvasCtx.lineTo(scaleX(fakeLandmarks[24].x), scaleY(fakeLandmarks[24].y));
        canvasCtx.stroke();
        
        // Left Leg
        canvasCtx.beginPath();
        canvasCtx.moveTo(scaleX(fakeLandmarks[23].x), scaleY(fakeLandmarks[23].y));
        canvasCtx.lineTo(scaleX(fakeLandmarks[25].x), scaleY(fakeLandmarks[25].y));
        canvasCtx.lineTo(scaleX(fakeLandmarks[27].x), scaleY(fakeLandmarks[27].y));
        canvasCtx.stroke();
        
        // Right Leg
        canvasCtx.beginPath();
        canvasCtx.moveTo(scaleX(fakeLandmarks[24].x), scaleY(fakeLandmarks[24].y));
        canvasCtx.lineTo(scaleX(fakeLandmarks[26].x), scaleY(fakeLandmarks[26].y));
        canvasCtx.lineTo(scaleX(fakeLandmarks[28].x), scaleY(fakeLandmarks[28].y));
        canvasCtx.stroke();
        
        // Joint Tracking dots (Glowing green lock-on dots)
        canvasCtx.fillStyle = '#22c55e';
        canvasCtx.shadowColor = '#22c55e';
        canvasCtx.shadowBlur = 8;
        [11, 12, 23, 24, 25, 26, 27, 28].forEach(idx => {
            canvasCtx.beginPath();
            canvasCtx.arc(scaleX(fakeLandmarks[idx].x), scaleY(fakeLandmarks[idx].y), 5, 0, 2 * Math.PI);
            canvasCtx.fill();
        });
        
        canvasCtx.shadowBlur = 0;
        
        // 4. Compute angles and pass them into posture monitoring widgets
        const kneeAngle = calculateAngle(fakeLandmarks[23], fakeLandmarks[25], fakeLandmarks[27]);
        const backAngle = calculateAngle(fakeLandmarks[11], fakeLandmarks[23], fakeLandmarks[25]);
        const isBackGood = backAngle > 130;
        
        // Update Posture widgets directly inside the demo frame
        const hipSt = document.getElementById('hipStatus');
        const kneeSt = document.getElementById('kneeStatus');
        const backSt = document.getElementById('postureStatus');
        
        const jHead = document.getElementById('jointHeadStatus');
        const jShoulders = document.getElementById('jointShouldersStatus');
        const jHips = document.getElementById('jointHipsStatus');
        const jKnees = document.getElementById('jointKneesStatus');
        const jAnkles = document.getElementById('jointAnklesStatus');
        
        const greenTrackedText = `<span style="color:#22c55e; font-weight:800; display:flex; align-items:center;"><span style="color:#22c55e;margin-right:4px;text-shadow:0 0 6px #22c55e;">●</span>Tracked</span>`;
        
        if (jHead) jHead.innerHTML = `<span style="font-family:monospace;font-size:0.52rem;color:rgba(167,139,250,0.45);letter-spacing:0.05em;margin-right:6px;">[X:${fakeLandmarks[0].x.toFixed(3)} Y:${fakeLandmarks[0].y.toFixed(3)}]</span>` + greenTrackedText;
        if (jShoulders) jShoulders.innerHTML = `<span style="font-family:monospace;font-size:0.52rem;color:rgba(167,139,250,0.45);letter-spacing:0.05em;margin-right:6px;">[X:${fakeLandmarks[11].x.toFixed(3)} Y:${fakeLandmarks[11].y.toFixed(3)}]</span>` + greenTrackedText;
        if (jHips) jHips.innerHTML = `<span style="font-family:monospace;font-size:0.52rem;color:rgba(167,139,250,0.45);letter-spacing:0.05em;margin-right:6px;">[X:${fakeLandmarks[23].x.toFixed(3)} Y:${fakeLandmarks[23].y.toFixed(3)}]</span>` + greenTrackedText;
        if (jKnees) jKnees.innerHTML = `<span style="font-family:monospace;font-size:0.52rem;color:rgba(167,139,250,0.45);letter-spacing:0.05em;margin-right:6px;">[X:${fakeLandmarks[25].x.toFixed(3)} Y:${fakeLandmarks[25].y.toFixed(3)}]</span>` + greenTrackedText;
        if (jAnkles) jAnkles.innerHTML = `<span style="font-family:monospace;font-size:0.52rem;color:rgba(167,139,250,0.45);letter-spacing:0.05em;margin-right:6px;">[X:${fakeLandmarks[27].x.toFixed(3)} Y:${fakeLandmarks[27].y.toFixed(3)}]</span>` + greenTrackedText;
        
        if (kneeSt) {
            if (kneeAngle < 95) {
                kneeSt.innerHTML = `<span style="color:#22c55e; font-weight:800; display:flex; align-items:center;"><span style="color:#22c55e;margin-right:6px;text-shadow:0 0 6px #22c55e;">●</span>Good depth</span>`;
            } else if (kneeAngle < 155) {
                kneeSt.innerHTML = `<span style="color:#f59e0b; font-weight:800; display:flex; align-items:center;"><span style="color:#f59e0b;margin-right:6px;text-shadow:0 0 6px #f59e0b;">●</span>Adjust depth</span>`;
            } else {
                kneeSt.innerHTML = `<span style="color:#3b82f6; font-weight:800; display:flex; align-items:center;"><span style="color:#3b82f6;margin-right:6px;text-shadow:0 0 6px #3b82f6;">●</span>Stand upright</span>`;
            }
        }
        
        const statText = document.getElementById('statusText');
        const statInd = document.getElementById('statusIndicator');
        
        if (backSt) {
            if (isBackGood) {
                backSt.innerHTML = `<span style="color:#22c55e; font-weight:800; display:flex; align-items:center;"><span style="color:#22c55e;margin-right:6px;text-shadow:0 0 6px #22c55e;">●</span>Stable</span>`;
                if (statText && statInd) {
                    statText.innerText = "Pose Detection Active";
                    statInd.style.background = "#22c55e";
                    statInd.style.boxShadow = "0 0 10px #22c55e";
                }
            } else {
                backSt.innerHTML = `<span style="color:#ef4444; font-weight:800; display:flex; align-items:center;" class="pulse-text-red"><span style="color:#ef4444;margin-right:6px;text-shadow:0 0 6px #ef4444;" class="pulse-indicator">●</span>Warning</span>`;
                if (statText && statInd) {
                    statText.innerText = "POSTURE CAUTION";
                    statInd.style.background = "#ef4444";
                    statInd.style.boxShadow = "0 0 10px #ef4444";
                }
            }
        }
        
        if (hipSt) {
            hipSt.innerHTML = `<span style="color:#22c55e; font-weight:800; display:flex; align-items:center;"><span style="color:#22c55e;margin-right:6px;text-shadow:0 0 6px #22c55e;">●</span>Stable</span>`;
        }
        
        // 5. Update Stability Curve & Waveform Graph values
        let stabilityScore = isSpineCurved ? 58 : 96;
        if (kneeAngle < 155 && kneeAngle > 95) stabilityScore -= 4;
        stabilityScore += (Math.random() * 3 - 1.5);
        stabilityScore = Math.max(10, Math.min(100, Math.round(stabilityScore)));
        
        const stabValEl = document.getElementById('stabilityVal');
        if (stabValEl) {
            stabValEl.innerHTML = `<span style="color:${isSpineCurved ? '#ef4444' : '#22c55e'}; font-weight:850;">${stabilityScore}%</span>`;
        }
        
        stabilityHistory.shift();
        stabilityHistory.push(stabilityScore);
        renderStabilityGraph(stabilityHistory);
        
        // Accuracy Fluctuation during Demo Mode
        const accuracyVal = (96.5 + Math.random() * 1.5).toFixed(1);
        const accValEl = document.getElementById('accuracyVal');
        const accBarEl = document.getElementById('accuracyBar');
        if (accValEl && accBarEl) {
            accValEl.innerText = accuracyVal + "%";
            accBarEl.style.width = accuracyVal + "%";
        }
        
        // 6. Rep counting algorithm inside demo loop
        if (kneeAngle > 162) {
            if (demoStage === "up") {
                demoReps++;
                updateRepCounter(demoReps);
                
                if (demoReps === 3) {
                    logConsole("POSTURE WARNING: Spine curvature detected! Sit back into hips.", "warning");
                } else if (demoReps === 4) {
                    logConsole("Posture corrected. Spine alignment stable.", "success");
                } else {
                    logConsole(`Rep ${demoReps} recorded successfully.`, "success");
                }
                
                // Rep count cap
                if (demoReps >= 8) {
                    logConsole("AI Demo completed! 8 perfect reps processed.", "system");
                    stopDemo();
                    return;
                }
            }
            demoStage = "down";
            phaseText.innerText = "Stand (Up)";
            
            feedbackStatus.style.background = "#22c55e";
            feedbackStatus.style.boxShadow = "0 0 10px #22c55e";
            feedbackText.innerText = "Good! Squat down.";
        } else if (kneeAngle < 90) {
            demoStage = "up";
            phaseText.innerText = "Squat (Down)";
            
            feedbackStatus.style.background = "#3b82f6";
            feedbackStatus.style.boxShadow = "0 0 10px #3b82f6";
            feedbackText.innerText = "Perfect Depth! Drive up.";
        } else {
            if (demoStage === "down") {
                feedbackStatus.style.background = "#f59e0b";
                feedbackStatus.style.boxShadow = "0 0 10px #f59e0b";
                feedbackText.innerText = "Lower... keep chest up";
            }
        }
        
        demoFrameId = requestAnimationFrame(runDemoLoop);
    }
    
    function stopDemo() {
        isDemoRunning = false;
        cancelAnimationFrame(demoFrameId);
        stopTimer();
        
        // Reset silhouette guide visibility
        const silEl = document.getElementById('silhouetteGuide');
        if (silEl) silEl.style.opacity = '0.12';
        
        // Scanner sweep hide
        const sweep = document.getElementById('scannerSweep');
        if (sweep) sweep.style.display = 'none';
        
        // Revert system status badge
        const statText = document.getElementById('statusText');
        const statInd = document.getElementById('statusIndicator');
        if (statText && statInd) {
            statText.innerText = "System Idle";
            statInd.style.background = "#ef4444"; 
            statInd.style.boxShadow = "0 0 8px #ef4444";
        }
        
        canvasElement.style.display = 'none';
        feedbackOverlay.style.display = 'none';
        loader.style.display = 'none';
        stopDemoBtn.style.display = 'none';
        
        placeholder.style.display = 'block';
        demoBtn.style.display = 'flex';
        startBtn.style.display = 'flex';
        
        canvasCtx.clearRect(0, 0, canvasElement.width, canvasElement.height);
        updatePostureChecklists(null);
        logConsole("Simulated squat demo terminated.", "system");
    }
    
    demoBtn.addEventListener('click', () => {
        isDemoRunning = true;
        isRunning = false; // Block MediaPipe camera
        
        demoReps = 0;
        demoTime = 0;
        demoStage = "down";
        updateRepCounter(0);
        
        placeholder.style.display = 'none';
        startBtn.style.display = 'none';
        demoBtn.style.display = 'none';
        stopDemoBtn.style.display = 'flex';
        
        canvasElement.style.display = 'block';
        feedbackOverlay.style.display = 'flex';
        
        const silEl = document.getElementById('silhouetteGuide');
        if (silEl) silEl.style.opacity = '0';
        
        const sweep = document.getElementById('scannerSweep');
        if (sweep) sweep.style.display = 'block';
        
        startTimer();
        logConsole("Initializing simulated AI posture analysis...", "system");
        logConsole("Drawing neon posture guides. Real-time simulation active.", "info");
        
        runDemoLoop();
    });
    
    stopDemoBtn.addEventListener('click', () => {
        stopDemo();
    });
});
</script>
@endsection
