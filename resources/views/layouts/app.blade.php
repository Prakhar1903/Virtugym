<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>VirtuGym - @yield('title', 'Dashboard')</title>

    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <script>
        (function(){
            const savedTheme = localStorage.getItem('virtugym-theme') || 'aurora';
            document.documentElement.dataset.theme = savedTheme;
        })();
    </script>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="/css/style.css">
    <link rel="stylesheet" href="/css/dashboard-v2.css">
    <script src="https://unpkg.com/lucide@latest/dist/umd/lucide.min.js" defer></script>
    <script src="/js/virtugym-icons.js" defer></script>

    <style>
        :root {
            --vg-bg: #08081a;
            --vg-nav: rgba(8,8,26,.9);
            --vg-panel: rgba(255,255,255,.04);
            --vg-panel-strong: rgba(16,16,40,.97);
            --vg-sidebar: rgba(255,255,255,.03);
            --vg-border: rgba(139,92,246,.2);
            --vg-border-strong: rgba(139,92,246,.3);
            --vg-accent: #8b5cf6;
            --vg-accent-2: #ec4899;
            --vg-accent-soft: rgba(139,92,246,.15);
            --vg-accent-glow: rgba(139,92,246,.45);
            --vg-text: #fff;
            --vg-text-strong: #e2d9f3;
            --vg-text-muted: rgba(255,255,255,.42);
            --vg-text-faint: rgba(255,255,255,.25);
            --vg-gradient: linear-gradient(135deg, var(--vg-accent), var(--vg-accent-2));
            --vg-title-gradient: linear-gradient(135deg, #fff 20%, #c4b5fd 60%, #f9a8d4 90%);
            --vg-orb-1: rgba(139,92,246,.1);
            --vg-orb-2: rgba(236,72,153,.08);
            --vg-orb-3: rgba(59,130,246,.06);
        }
        html[data-theme="ember"] {
            --vg-bg: #160b08;
            --vg-nav: rgba(22,11,8,.92);
            --vg-panel: rgba(255,244,232,.055);
            --vg-panel-strong: rgba(38,18,12,.97);
            --vg-sidebar: rgba(255,236,214,.04);
            --vg-border: rgba(251,146,60,.22);
            --vg-border-strong: rgba(244,114,182,.34);
            --vg-accent: #f97316;
            --vg-accent-2: #e11d48;
            --vg-accent-soft: rgba(249,115,22,.16);
            --vg-accent-glow: rgba(249,115,22,.4);
            --vg-text-strong: #ffe4d2;
            --vg-text-muted: rgba(255,239,225,.46);
            --vg-text-faint: rgba(255,239,225,.26);
            --vg-title-gradient: linear-gradient(135deg, #fff7ed 20%, #fdba74 60%, #fb7185 90%);
            --vg-orb-1: rgba(249,115,22,.12);
            --vg-orb-2: rgba(225,29,72,.1);
            --vg-orb-3: rgba(251,191,36,.08);
        }
        html[data-theme="ocean"] {
            --vg-bg: #06131f;
            --vg-nav: rgba(6,19,31,.92);
            --vg-panel: rgba(236,253,245,.045);
            --vg-panel-strong: rgba(8,32,48,.97);
            --vg-sidebar: rgba(224,242,254,.035);
            --vg-border: rgba(14,165,233,.22);
            --vg-border-strong: rgba(45,212,191,.32);
            --vg-accent: #0ea5e9;
            --vg-accent-2: #14b8a6;
            --vg-accent-soft: rgba(14,165,233,.15);
            --vg-accent-glow: rgba(14,165,233,.36);
            --vg-text-strong: #d7f7ff;
            --vg-text-muted: rgba(224,242,254,.46);
            --vg-text-faint: rgba(224,242,254,.25);
            --vg-title-gradient: linear-gradient(135deg, #f0fdfa 20%, #7dd3fc 58%, #5eead4 90%);
            --vg-orb-1: rgba(14,165,233,.12);
            --vg-orb-2: rgba(20,184,166,.1);
            --vg-orb-3: rgba(59,130,246,.08);
        }
        html[data-theme="forest"] {
            --vg-bg: #07130d;
            --vg-nav: rgba(7,19,13,.92);
            --vg-panel: rgba(240,253,244,.045);
            --vg-panel-strong: rgba(10,32,22,.97);
            --vg-sidebar: rgba(220,252,231,.035);
            --vg-border: rgba(34,197,94,.22);
            --vg-border-strong: rgba(132,204,22,.32);
            --vg-accent: #22c55e;
            --vg-accent-2: #84cc16;
            --vg-accent-soft: rgba(34,197,94,.15);
            --vg-accent-glow: rgba(34,197,94,.34);
            --vg-text-strong: #dcfce7;
            --vg-text-muted: rgba(220,252,231,.46);
            --vg-text-faint: rgba(220,252,231,.25);
            --vg-title-gradient: linear-gradient(135deg, #f7fee7 20%, #86efac 58%, #bef264 90%);
            --vg-orb-1: rgba(34,197,94,.12);
            --vg-orb-2: rgba(132,204,22,.1);
            --vg-orb-3: rgba(20,184,166,.07);
        }
        html[data-theme="graphite"] {
            --vg-bg: #0c0f14;
            --vg-nav: rgba(12,15,20,.92);
            --vg-panel: rgba(248,250,252,.045);
            --vg-panel-strong: rgba(23,28,36,.97);
            --vg-sidebar: rgba(248,250,252,.035);
            --vg-border: rgba(148,163,184,.22);
            --vg-border-strong: rgba(203,213,225,.3);
            --vg-accent: #94a3b8;
            --vg-accent-2: #38bdf8;
            --vg-accent-soft: rgba(148,163,184,.15);
            --vg-accent-glow: rgba(56,189,248,.28);
            --vg-text-strong: #f1f5f9;
            --vg-text-muted: rgba(226,232,240,.45);
            --vg-text-faint: rgba(226,232,240,.25);
            --vg-title-gradient: linear-gradient(135deg, #ffffff 20%, #cbd5e1 58%, #7dd3fc 90%);
            --vg-orb-1: rgba(148,163,184,.1);
            --vg-orb-2: rgba(56,189,248,.08);
            --vg-orb-3: rgba(99,102,241,.06);
        }
        *{font-family:'Inter',sans-serif;box-sizing:border-box;}
        
        /* Custom Smooth Scrollbar Styles */
        ::-webkit-scrollbar {
            width: 8px;
            height: 8px;
        }
        ::-webkit-scrollbar-track {
            background: transparent;
        }
        ::-webkit-scrollbar-thumb {
            background: var(--vg-gradient);
            border-radius: 9999px;
            border: 2px solid var(--vg-bg);
        }
        ::-webkit-scrollbar-thumb:hover {
            opacity: 0.8;
        }
        * {
            scrollbar-width: thin;
            scrollbar-color: var(--vg-accent) var(--vg-bg);
        }

        body {
            background: var(--vg-bg);
            min-height: 100vh;
            color: var(--vg-text);
            overflow-x: hidden;
            transition: background .35s ease, color .35s ease;
        }

        /* Background layers */
        #stars{position:fixed;inset:0;z-index:0;pointer-events:none;}
        .orb{position:fixed;border-radius:50%;filter:blur(90px);pointer-events:none;z-index:0;}
        .o1{width:500px;height:500px;background:var(--vg-orb-1);top:-200px;left:-150px;animation:od 22s ease-in-out infinite;}
        .o2{width:400px;height:400px;background:var(--vg-orb-2);bottom:-100px;right:-100px;animation:od 28s ease-in-out infinite reverse;}
        .o3{width:250px;height:250px;background:var(--vg-orb-3);top:50%;right:15%;animation:od 18s ease-in-out infinite 4s;}
        @keyframes od{0%,100%{transform:translate(0,0);}33%{transform:translate(30px,-40px);}66%{transform:translate(-20px,25px);}}

        /* Navbar */
        .nav-dark {
            position: fixed;
            top: 0;
            width: 100%;
            z-index: 50;
            background: var(--vg-nav);
            backdrop-filter: blur(20px);
            border-bottom: 1px solid var(--vg-border);
        }
        .nav-inner {
            max-width: 1600px;
            margin: 0 auto;
            padding: 0 1.25rem;
            height: 64px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        .logo-pill {
            display: flex;
            align-items: center;
            gap: 12px;
            text-decoration: none;
        }
        .logo-badge {
            width: 38px;
            height: 38px;
            border-radius: 11px;
            background: var(--vg-gradient);
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 0 16px var(--vg-accent-glow);
            flex-shrink: 0;
        }
        .brand-name {
            font-size: .95rem;
            font-weight: 800;
            background: var(--vg-title-gradient);
            -webkit-background-clip: text;
            background-clip: text;
            color: transparent;
            letter-spacing: .05em;
        }
        .brand-sub {
            font-size: .58rem;
            color: var(--vg-text-faint);
            letter-spacing: .12em;
        }

        /* User avatar */
        .user-avatar {
            width: 38px;
            height: 38px;
            background: var(--vg-gradient);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 800;
            font-size: 16px;
            cursor: pointer;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
            box-shadow: 0 4px 12px var(--vg-accent-glow);
        }
        .user-avatar:hover {
            transform: scale(1.07);
            box-shadow: 0 6px 20px var(--vg-accent-glow);
        }

        /* Dropdown */
        .dropdown-menu {
            background: var(--vg-panel-strong);
            backdrop-filter: blur(20px);
            border-radius: 16px;
            box-shadow: 0 20px 40px rgba(0,0,0,.5), 0 0 0 1px var(--vg-border);
            min-width: 200px;
        }
        .dropdown-menu a, .dropdown-menu button {
            transition: all 0.2s ease;
            display: block;
            width: 100%;
        }
        .dropdown-menu a:hover, .dropdown-menu button:not(.theme-choice):hover {
            background: var(--vg-accent-soft);
            color: var(--vg-text-strong);
            padding-left: 20px;
        }
        .theme-grid {
            display: grid;
            grid-template-columns: repeat(5, minmax(0, 1fr));
            gap: 7px;
            padding: 10px 12px 12px;
            border-bottom: 1px solid var(--vg-border);
            margin-bottom: 4px;
        }
        .theme-choice {
            width: 28px;
            height: 28px;
            border: 1px solid var(--vg-border);
            border-radius: 50%;
            cursor: pointer;
            box-shadow: inset 0 0 0 3px rgba(255,255,255,.08);
            transition: transform .2s ease, border-color .2s ease, box-shadow .2s ease;
        }
        .theme-choice:hover,
        .theme-choice.active {
            transform: translateY(-2px);
            border-color: var(--vg-border-strong);
            box-shadow: 0 4px 10px var(--vg-accent-glow), inset 0 0 0 2px rgba(255,255,255,.08);
        }
        .theme-choice[data-theme-choice="aurora"] { background: linear-gradient(135deg,#8b5cf6,#ec4899); }
        .theme-choice[data-theme-choice="ember"] { background: linear-gradient(135deg,#f97316,#e11d48); }
        .theme-choice[data-theme-choice="ocean"] { background: linear-gradient(135deg,#0ea5e9,#14b8a6); }
        .theme-choice[data-theme-choice="forest"] { background: linear-gradient(135deg,#22c55e,#84cc16); }
        .theme-choice[data-theme-choice="graphite"] { background: linear-gradient(135deg,#94a3b8,#38bdf8); }
        .appearance-panel {
            background: rgba(255,255,255,.005);
            border: 1px solid rgba(255,255,255,.02);
            border-radius: 16px;
            padding: 14px;
            opacity: 0.35;
            transition: opacity 0.3s ease, border-color 0.3s ease, background 0.3s ease;
        }
        .appearance-panel:hover {
            opacity: 0.9;
            background: rgba(255,255,255,.02);
            border-color: rgba(255,255,255,.08);
        }
        .appearance-title {
            color: var(--vg-text-muted);
            font-size: .7rem;
            font-weight: 800;
            letter-spacing: .08em;
            margin-bottom: 10px;
            opacity: 0.65;
        }
        .theme-list {
            display: grid;
            grid-template-columns: 1fr;
            gap: 7px;
        }
        .theme-row {
            display: flex;
            align-items: center;
            gap: 9px;
            width: 100%;
            background: rgba(255,255,255,.01);
            border: 1px solid transparent;
            color: var(--vg-text-muted);
            border-radius: 10px;
            padding: 7px 9px;
            cursor: pointer;
            font-size: .75rem;
            font-weight: 700;
            transition: all .2s ease;
            opacity: 0.65;
        }
        .theme-row:hover {
            background: rgba(255,255,255,.04);
            color: var(--vg-text-strong);
            opacity: 0.95;
        }
        .theme-row.active {
            background: rgba(255,255,255,.08);
            border-color: rgba(255,255,255,.1);
            color: var(--vg-text-strong);
            opacity: 1;
        }
        .theme-dot {
            width: 14px;
            height: 14px;
            border-radius: 50%;
            flex-shrink: 0;
        }
        .vg-inline-icon {
            width: 1em;
            height: 1em;
            display: inline-block;
            vertical-align: -0.14em;
            margin-right: .35em;
            stroke-width: 2.4;
        }

        /* Sidebar */
        .sidebar {
            position: fixed;
            left: 0;
            top: 64px;
            height: calc(100vh - 64px);
            width: 240px;
            background: var(--vg-sidebar);
            backdrop-filter: blur(20px);
            border-right: 1px solid var(--vg-border);
            overflow-y: auto;
            z-index: 40;
        }
        .sidebar::-webkit-scrollbar{width:4px;}
        .sidebar::-webkit-scrollbar-thumb{background:var(--vg-gradient);border-radius:4px;}

        /* Prevent logo clipping and align sidebar properly */
        .logo-v2 {
            padding: 20px 20px 16px !important;
            display: flex;
            align-items: center;
            gap: 12px;
            border-bottom: 1px solid var(--vg-border) !important;
        }
        .logo-text-v2 {
            font-size: 14px !important;
            font-weight: 800 !important;
            letter-spacing: .05em !important;
            background: linear-gradient(135deg, #ffffff 20%, var(--vg-accent) 60%, var(--vg-accent-2) 90%) !important;
            -webkit-background-clip: text !important;
            background-clip: text !important;
            color: transparent !important;
        }
        .logo-icon-v2 {
            width: 34px !important;
            height: 34px !important;
            border-radius: 9px !important;
            background: var(--vg-gradient) !important;
            box-shadow: 0 0 12px var(--vg-accent-glow) !important;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 16px;
            flex-shrink: 0;
        }


        .sidebar-item {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 8px 12px;
            margin: 1px 8px;
            border-radius: 8px;
            color: var(--vg-text-muted);
            font-size: 12px;
            font-weight: 500;
            text-decoration: none;
            transition: all 0.2s ease;
            position: relative;
            margin-bottom: 2px;
        }
        .sidebar-item:hover {
            background: var(--vg-accent-soft);
            color: var(--vg-text-strong);
            transform: translateX(4px);
        }
        .sidebar-item.active {
            background: linear-gradient(135deg, var(--vg-accent-soft), rgba(255,255,255,.04));
            color: var(--vg-text-strong);
            border: 1px solid rgba(255, 255, 255, 0.08);
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.3);
        }
        .sidebar-item .s-icon {
            font-size: 1.2rem;
            width: 24px;
            text-align: center;
            flex-shrink: 0;
        }
        .sidebar-item .s-icon i {
            width: 1.05rem;
            height: 1.05rem;
            margin: 0 auto;
            stroke-width: 2.35;
        }

        /* Sidebar divider */
        .s-divider {
            height: 1px;
            background: var(--vg-border);
            margin: 12px 16px;
        }

        /* Streak widget */
        .streak-widget {
            background: linear-gradient(135deg, var(--vg-accent-soft), rgba(255,255,255,.04));
            border: 1px solid var(--vg-border-strong);
            border-radius: 16px;
            padding: 16px;
            text-align: center;
        }
        .streak-widget .s-num {
            font-size: 1.8rem;
            font-weight: 900;
            background: var(--vg-title-gradient);
            -webkit-background-clip: text;
            background-clip: text;
            color: transparent;
        }
        .streak-widget .s-lbl {
            font-size: .73rem;
            color: var(--vg-text-muted);
            font-weight: 500;
            margin-top: 2px;
        }

        /* Main content */
        .main-content {
            margin-left: 240px;
            padding: 88px 1.5rem 2rem;
            min-height: 100vh;
            position: relative;
            z-index: 10;
        }

        /* Alert banners */
        .alert-success {
            background: rgba(16,185,129,.12);
            border: 1px solid rgba(16,185,129,.3);
            border-left: 4px solid #10b981;
            border-radius: 12px;
            padding: 14px 18px;
            color: #6ee7b7;
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 1.5rem;
        }
        .alert-error {
            background: rgba(239,68,68,.12);
            border: 1px solid rgba(239,68,68,.3);
            border-left: 4px solid #ef4444;
            border-radius: 12px;
            padding: 14px 18px;
            color: #fca5a5;
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 1.5rem;
        }

        /* Animations */
        .fade-in-up {
            animation: fadeInUp 0.55s cubic-bezier(.23,1,.32,1) forwards;
            opacity: 0;
        }
        .delay-1 { animation-delay: 0.08s; }
        .delay-2 { animation-delay: 0.16s; }
        .delay-3 { animation-delay: 0.24s; }
        .delay-4 { animation-delay: 0.32s; }
        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(20px); }
            to   { opacity: 1; transform: translateY(0); }
        }

        /* Unread badge */
        #unreadBadge {
            background: #ef4444;
            color: white;
            font-size: .65rem;
            padding: 1px 6px;
            border-radius: 50px;
            font-weight: 700;
        }
        .music-toggle {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            border: 1px solid var(--vg-border);
            background: var(--vg-panel);
            color: var(--vg-text-muted);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            transition: all .2s ease;
        }
        .music-toggle:hover,
        .music-toggle.playing {
            color: var(--vg-text-strong);
            border-color: var(--vg-border-strong);
            background: var(--vg-accent-soft);
            box-shadow: 0 6px 18px var(--vg-accent-glow);
        }

        /* Mobile */
        @media (max-width: 900px) {
            .sidebar {
                transform: translateX(-100%);
                transition: transform 0.3s ease;
            }
            .sidebar.mobile-open {
                transform: translateX(0);
            }
            .main-content {
                margin-left: 0;
            }
            .hamburger { display: flex !important; }
        }
        .hamburger {
            display: none;
            flex-direction: column;
            gap: 5px;
            cursor: pointer;
            padding: 4px;
        }
        .hamburger span {
            width: 22px;
            height: 2px;
            background: var(--vg-text-strong);
            border-radius: 2px;
            transition: all .3s;
        }

        /* User info in nav */
        .nav-user-info p { line-height: 1.2; }
        .nav-user-name { font-size: .85rem; font-weight: 600; color: var(--vg-text-strong); }
        .nav-user-email { font-size: .72rem; color: var(--vg-text-faint); }
        html[data-theme] .bg-white,
        html[data-theme] .bg-gray-800\/50 {
            background-color: var(--vg-panel) !important;
            border-color: var(--vg-border) !important;
            color: var(--vg-text-strong);
        }
        html[data-theme] .bg-gray-700\/50,
        html[data-theme] .bg-gray-700,
        html[data-theme] .bg-gray-900\/50 {
            background-color: rgba(255,255,255,.055) !important;
        }
        html[data-theme] .border-gray-700,
        html[data-theme] .border-gray-600,
        html[data-theme] .border-gray-300 {
            border-color: var(--vg-border) !important;
        }
        html[data-theme] .text-gray-900,
        html[data-theme] .text-gray-800,
        html[data-theme] .text-gray-700,
        html[data-theme] .text-gray-300,
        html[data-theme] .text-gray-200,
        html[data-theme] .text-white {
            color: var(--vg-text-strong) !important;
        }
        html[data-theme] .text-gray-600,
        html[data-theme] .text-gray-500,
        html[data-theme] .text-gray-400 {
            color: var(--vg-text-muted) !important;
        }
        html[data-theme] input,
        html[data-theme] textarea {
            background-color: rgba(255,255,255,.055) !important;
            border-color: var(--vg-border) !important;
            color: var(--vg-text-strong) !important;
        }
        
        /* Premium dark dropdowns and options */
        select,
        html[data-theme] select {
            appearance: none !important;
            -webkit-appearance: none !important;
            -moz-appearance: none !important;
            background: #08081a url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='16' height='16' viewBox='0 0 24 24' fill='none' stroke='%23a78bfa' stroke-width='2.5' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpath d='m6 9 6 6 6-6'/%3E%3C/svg%3E") no-repeat right 16px center !important;
            background-size: 16px !important;
            border: 1px solid var(--vg-border) !important;
            color: var(--vg-text-strong) !important;
            border-radius: 12px;
            padding: 10px 40px 10px 16px !important;
            outline: none;
            cursor: pointer;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1) !important;
        }

        select:focus,
        html[data-theme] select:focus {
            border-color: var(--vg-accent) !important;
            box-shadow: 0 0 0 3px rgba(139, 92, 246, 0.15) !important;
            background-color: #0c0c26 !important;
        }

        /* Specific for form-input select overrides */
        .form-input select,
        select.form-input,
        html[data-theme] select.form-input {
            background: rgba(255, 255, 255, 0.04) url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='16' height='16' viewBox='0 0 24 24' fill='none' stroke='%23a78bfa' stroke-width='2.5' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpath d='m6 9 6 6 6-6'/%3E%3C/svg%3E") no-repeat right 16px center !important;
            background-size: 16px !important;
            padding: 12px 40px 12px 16px !important;
            border: 1px solid rgba(255, 255, 255, 0.08) !important;
        }

        .form-input select:focus,
        select.form-input:focus,
        html[data-theme] select.form-input:focus {
            border-color: var(--vg-accent) !important;
            background-color: rgba(255, 255, 255, 0.08) !important;
        }
        
        select option,
        html[data-theme] select option {
            background-color: #08081a !important;
            color: #ffffff !important;
            padding: 14px !important;
        }
        html[data-theme] input::placeholder,
        html[data-theme] textarea::placeholder {
            color: var(--vg-text-faint) !important;
        }
    </style>
</head>
<body>
    <!-- Top Progress Bar -->
    <div id="top-loading-bar" style="position: fixed; top: 0; left: 0; height: 3px; background: linear-gradient(to right, #8b5cf6, #ec4899); z-index: 99999; width: 0%; transition: width 0.4s ease, opacity 0.4s ease; opacity: 0; pointer-events: none;"></div>

    <!-- Global Toast Notifications Container -->
    <div id="global-toast-container" style="position: fixed; top: 80px; right: 24px; display: flex; flex-direction: column; gap: 12px; z-index: 10000; pointer-events: none;"></div>

    <script>
    (function() {
        // Global Toast System
        window.showToast = function(message, type = 'info') {
            const container = document.getElementById('global-toast-container');
            if (!container) return;

            const toast = document.createElement('div');
            toast.style.pointerEvents = 'auto';
            toast.style.background = 'rgba(15, 23, 42, 0.9)';
            toast.style.backdropFilter = 'blur(12px)';
            toast.style.webkitBackdropFilter = 'blur(12px)';
            toast.style.border = '1px solid rgba(139, 92, 246, 0.3)';
            toast.style.borderRadius = '16px';
            toast.style.padding = '14px 20px';
            toast.style.color = '#fff';
            toast.style.fontSize = '0.9rem';
            toast.style.fontWeight = '600';
            toast.style.display = 'flex';
            toast.style.alignItems = 'center';
            toast.style.gap = '12px';
            toast.style.boxShadow = '0 15px 35px rgba(0, 0, 0, 0.5), 0 0 20px rgba(139, 92, 246, 0.15)';
            toast.style.transform = 'translateX(50px)';
            toast.style.opacity = '0';
            toast.style.transition = 'all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275)';
            toast.style.minWidth = '320px';
            toast.style.maxWidth = '450px';

            let icon = 'ℹ️';
            if (message.includes('✨') || message.toLowerCase().includes('success') || message.toLowerCase().includes('added') || message.toLowerCase().includes('completed') || message.includes('✓')) {
                icon = '✨';
                toast.style.border = '1px solid rgba(16, 185, 129, 0.35)';
                toast.style.boxShadow = '0 15px 35px rgba(0, 0, 0, 0.5), 0 0 20px rgba(16, 185, 129, 0.15)';
            } else if (message.includes('❌') || message.toLowerCase().includes('error') || message.toLowerCase().includes('failed') || message.toLowerCase().includes('could not')) {
                icon = '❌';
                toast.style.border = '1px solid rgba(244, 63, 94, 0.35)';
                toast.style.boxShadow = '0 15px 35px rgba(0, 0, 0, 0.5), 0 0 20px rgba(244, 63, 94, 0.15)';
            } else if (message.includes('⚠️') || message.toLowerCase().includes('warning') || message.toLowerCase().includes('sure')) {
                icon = '⚠️';
                toast.style.border = '1px solid rgba(245, 158, 11, 0.35)';
                toast.style.boxShadow = '0 15px 35px rgba(0, 0, 0, 0.5), 0 0 20px rgba(245, 158, 11, 0.15)';
            }

            let cleanMessage = message;
            const emojiPrefixes = ['✨', '❌', '⚠️', 'ℹ️', '✓'];
            for (const prefix of emojiPrefixes) {
                if (cleanMessage.startsWith(prefix)) {
                    cleanMessage = cleanMessage.substring(prefix.length).trim();
                    break;
                }
            }

            toast.innerHTML = `
                <span style="font-size: 1.25rem; flex-shrink: 0;">${icon}</span>
                <span style="flex: 1; line-height: 1.4;">${cleanMessage}</span>
                <button type="button" onclick="this.parentElement.remove()" style="background: none; border: none; cursor: pointer; opacity: 0.5; font-size: 0.85rem; color: #fff; padding: 2px; display: flex; align-items: center; justify-content: center; transition: opacity 0.2s;" onmouseover="this.style.opacity=1" onmouseout="this.style.opacity=0.5">✕</button>
            `;

            container.appendChild(toast);

            setTimeout(() => {
                toast.style.transform = 'translateX(0)';
                toast.style.opacity = '1';
            }, 50);

            setTimeout(() => {
                toast.style.transform = 'translateX(50px)';
                toast.style.opacity = '0';
                setTimeout(() => {
                    toast.remove();
                }, 400);
            }, 4500);
        };

        // Override standard browser alert
        window.alert = function(msg) {
            window.showToast(msg);
        };
    })();
    </script>

    <canvas id="stars"></canvas>
    <div class="orb o1"></div>
    <div class="orb o2"></div>
    <div class="orb o3"></div>
    @if(Auth::user()->role === 'trainee')
    <div id="youtubeBackgroundPlayer" style="position:fixed;left:-9999px;top:-9999px;width:1px;height:1px;opacity:0;pointer-events:none;" aria-hidden="true"></div>
    @endif

    <!-- NAVBAR -->
    <nav class="nav-dark">
        <div class="nav-inner">
            <!-- Logo -->
            <a href="{{ route('dashboard') }}" class="logo-pill">
                <div class="logo-badge">
                    <img src="/images/logo.png" alt="VG" style="width:24px;height:24px;border-radius:50%;object-fit:cover;" onerror="this.style.display='none';this.parentElement.innerHTML='<span style=\'font-size:.75rem;font-weight:900;color:#fff;\'>VG</span>';">
                </div>
                <div>
                    <div class="brand-name">VIRTU GYM</div>
                    <div class="brand-sub">{{ ucwords(strtolower(Auth::user()->name ?? 'Virtual Trainer')) }}</div>
                </div>
            </a>

            <!-- Search Bar (Optional) -->
            <form action="{{ route('search') }}" method="GET" class="hidden md:flex items-center bg-gray-800/50 border border-gray-700 rounded-full px-4 py-1.5 ml-8 mr-auto max-w-sm w-full">
                <i data-lucide="search" class="w-4 h-4 text-gray-400 mr-2"></i>
                <input type="text" name="q" placeholder="Search clients, bookings..." class="bg-transparent border-none text-sm text-white placeholder-gray-500 w-full focus:outline-none" value="{{ request('q') }}" required>
            </form>

            <div style="display:flex;align-items:center;gap:1.2rem;">
                @if(Auth::user()->role === 'trainee')
                <button type="button" id="musicToggle" class="music-toggle" title="Toggle background music" aria-label="Toggle background music">
                    <i data-lucide="music" class="w-4 h-4"></i>
                </button>
                @endif
                
                <!-- Notification Bell -->
                <div class="relative" id="notificationDropdownContainer">
                    <button id="notificationBell" class="relative text-gray-400 hover:text-white transition focus:outline-none flex items-center justify-center" aria-label="Notifications" style="background: none; border: none; cursor: pointer; padding: 4px;">
                        <i data-lucide="bell" class="w-5 h-5"></i>
                        <span id="notificationBadge" class="absolute -top-1 -right-1 bg-red-500 text-white text-[10px] font-bold w-4 h-4 rounded-full flex items-center justify-center border border-gray-900" style="display: none; line-height: 1;">0</span>
                    </button>
                    <!-- Glassmorphic Dropdown -->
                    <div id="notificationDropdown" class="dropdown-menu absolute right-0 mt-3 py-2 w-80 max-h-[400px] overflow-y-auto z-50 hidden" style="border: 1px solid rgba(139,92,246,.15); background: rgba(17,12,28,0.96); backdrop-filter: blur(10px); border-radius: 12px; box-shadow: 0 10px 25px -5px rgba(0,0,0,0.5);">
                        <div class="flex items-center justify-between px-4 py-2 border-b border-gray-800" style="border-bottom: 1px solid rgba(139,92,246,.15); display: flex; justify-content: space-between; align-items: center;">
                            <span style="font-size: .85rem; font-weight: 700; color: var(--vg-text-strong);">Notifications</span>
                            <span id="notificationCountText" style="font-size: .72rem; color: var(--vg-text-faint);">0 unread</span>
                        </div>
                        <div id="notificationList" class="flex flex-col" style="display: flex; flex-direction: column;">
                            <div class="px-4 py-6 text-center text-gray-500 text-sm" style="padding: 24px 16px; text-align: center; color: var(--vg-text-faint); font-size: .82rem;">
                                Loading notifications...
                            </div>
                        </div>
                    </div>
                </div>

                <!-- User info -->
                <div class="nav-user-info hidden lg:block text-right border-l border-gray-700/50 pl-4">
                    <p class="nav-user-name">{{ ucwords(strtolower(Auth::user()->name ?? 'User')) }}</p>
                    <p class="nav-user-email">{{ Auth::user()->email ?? '' }}</p>
                </div>

                <!-- Avatar + dropdown -->
                <div class="relative" id="userDropdown">
                    <button onclick="toggleDropdown()" class="focus:outline-none flex items-center">
                        <div class="user-avatar">{{ substr(Auth::user()->name ?? 'U', 0, 1) }}</div>
                    </button>
                    <div id="dropdownMenu" class="dropdown-menu absolute right-0 mt-3 py-2 z-50 hidden">
                        <div style="padding:10px 16px 8px;border-bottom:1px solid rgba(139,92,246,.15);margin-bottom:4px;">
                            <p style="font-size:.82rem;font-weight:700;color:var(--vg-text-strong);">{{ Auth::user()->name ?? 'User' }}</p>
                            <p style="font-size:.72rem;color:var(--vg-text-faint);">{{ Auth::user()->email ?? '' }}</p>
                        </div>
                        <div class="theme-grid" aria-label="Appearance themes">
                            <button type="button" class="theme-choice" data-theme-choice="aurora" title="Aurora theme" aria-label="Aurora theme"></button>
                            <button type="button" class="theme-choice" data-theme-choice="ember" title="Ember theme" aria-label="Ember theme"></button>
                            <button type="button" class="theme-choice" data-theme-choice="ocean" title="Ocean theme" aria-label="Ocean theme"></button>
                            <button type="button" class="theme-choice" data-theme-choice="forest" title="Forest theme" aria-label="Forest theme"></button>
                            <button type="button" class="theme-choice" data-theme-choice="graphite" title="Graphite theme" aria-label="Graphite theme"></button>
                        </div>
                        <a href="{{ route('profile.edit') }}" style="padding:9px 16px;color:var(--vg-text-muted);font-size:.83rem;"><i data-lucide="user" class="vg-inline-icon"></i>Profile</a>
                        <a href="#" style="padding:9px 16px;color:var(--vg-text-muted);font-size:.83rem;"><i data-lucide="settings" class="vg-inline-icon"></i>Settings</a>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" style="padding:9px 16px;color:#f87171;font-size:.83rem;text-align:left;background:none;border:none;cursor:pointer;width:100%;">
                                <i data-lucide="log-out" class="vg-inline-icon"></i>Logout
                            </button>
                        </form>
                    </div>
                </div>

                <!-- Hamburger (mobile) -->
                <div class="hamburger" id="hamburger" onclick="toggleSidebar()">
                    <span></span><span></span><span></span>
                </div>
            </div>
        </div>
    </nav>

    <!-- SIDEBAR -->
    <aside id="sidebar" class="sidebar">
        <div class="logo-v2">
            <div class="logo-icon-v2">💪</div>
            <div>
                <div class="logo-text-v2">VIRTU GYM</div>
                <div class="logo-sub-v2">Virtual Trainer</div>
            </div>
        </div>
        <div style="padding:1.2rem 1rem;">
            <p style="font-size:.65rem;color:rgba(255,255,255,.2);font-weight:700;letter-spacing:.12em;padding:0 8px;margin-bottom:.6rem;">MAIN</p>
            <nav style="display:flex;flex-direction:column;">
                @if(Auth::user()->role == 'admin')
                <a href="{{ route('admin.dashboard') }}" class="sidebar-item {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                    <span class="s-icon"><i data-lucide="chart-no-axes-combined"></i></span><span>Dashboard</span>
                </a>
                <a href="{{ route('admin.users') }}" class="sidebar-item {{ request()->routeIs('admin.users') ? 'active' : '' }}">
                    <span class="s-icon"><i data-lucide="users"></i></span><span>Users</span>
                </a>
                <a href="{{ route('admin.trainers') }}" class="sidebar-item {{ request()->routeIs('admin.trainers') ? 'active' : '' }}">
                    <span class="s-icon"><i data-lucide="dumbbell"></i></span><span>Trainers</span>
                </a>
                <a href="{{ route('admin.bookings') }}" class="sidebar-item {{ request()->routeIs('admin.bookings') ? 'active' : '' }}">
                    <span class="s-icon"><i data-lucide="calendar-days"></i></span><span>Bookings</span>
                </a>
                <a href="{{ route('admin.withdrawals') }}" class="sidebar-item {{ request()->routeIs('admin.withdrawals') ? 'active' : '' }}">
                    <span class="s-icon"><i data-lucide="wallet"></i></span><span>Withdrawals</span>
                </a>
                @else
                <a href="{{ route('dashboard') }}" class="sidebar-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                    <span class="s-icon"><i data-lucide="chart-no-axes-combined"></i></span><span>Dashboard</span>
                </a>
                @if(Auth::user()->role == 'trainee')
                <a href="{{ route('analytics.index') }}" class="sidebar-item {{ request()->routeIs('analytics.*') ? 'active' : '' }}">
                    <span class="s-icon"><i data-lucide="trending-up"></i></span>
                    <span>Analytics</span>
                </a>
                @endif
                <a href="{{ route('workouts.index') }}" class="sidebar-item {{ request()->routeIs('workouts.*') ? 'active' : '' }}">
                    <span class="s-icon"><i data-lucide="dumbbell"></i></span>
                    <span>{{ Auth::user()->role == 'trainer' ? 'Client Workouts' : 'Workouts' }}</span>
                </a>
                <a href="{{ route('exercises.index') }}" class="sidebar-item {{ request()->routeIs('exercises.*') ? 'active' : '' }}">
                    <span class="s-icon"><i data-lucide="activity"></i></span>
                    <span>{{ Auth::user()->role == 'trainer' ? 'Exercise Library' : 'Exercises' }}</span>
                </a>
                @if(Auth::user()->role == 'trainee')
                <a href="{{ route('progress.index') }}" class="sidebar-item {{ request()->routeIs('progress.*') ? 'active' : '' }}">
                    <span class="s-icon"><i data-lucide="target"></i></span><span>Progress</span>
                </a>
                @endif
                <a href="{{ route('chat.index') }}" class="sidebar-item {{ request()->routeIs('chat.*') ? 'active' : '' }}">
                    <span class="s-icon"><i data-lucide="message-circle"></i></span><span>Messages</span>
                    <span id="unreadBadge" class="hidden" style="margin-left:auto;"></span>
                </a>

                @if(Auth::user()->role == 'trainee')
                <!-- AI COACH SIDEBAR LINK -->
                <a href="{{ route('ai.dashboard') }}" class="sidebar-item {{ request()->routeIs('ai.dashboard') ? 'active' : '' }}">
                    <span class="s-icon"><i data-lucide="bot"></i></span>
                    <span>AI Coach</span>
                </a>
                
                <a href="{{ route('ai.live-coach') }}" class="sidebar-item {{ request()->routeIs('ai.live-coach') ? 'active' : '' }}">
                    <span class="s-icon"><i data-lucide="camera"></i></span>
                    <span>Live Form Check</span>
                </a>
                @endif

                @if(Auth::user()->role == 'trainee')
                <a href="{{ route('water.index') }}" class="sidebar-item {{ request()->routeIs('water.*') ? 'active' : '' }}">
                    <span class="s-icon"><i data-lucide="droplets"></i></span><span>Water Tracker</span>
                </a>

                <a href="{{ route('mindfulness.index') }}" class="sidebar-item {{ request()->routeIs('mindfulness.*') ? 'active' : '' }}">
                    <span class="s-icon"><i data-lucide="leaf"></i></span><span>Mindfulness</span>
                </a>
                @endif

                @if(Auth::user()->role == 'trainer')
                <a href="{{ route('trainer.availability.index') }}" class="sidebar-item {{ request()->routeIs('trainer.availability.*') ? 'active' : '' }}">
                    <span class="s-icon"><i data-lucide="clock"></i></span><span>Availability</span>
                </a>
                <a href="{{ route('bookings.index') }}" class="sidebar-item {{ request()->routeIs('bookings.*') ? 'active' : '' }}">
                    <span class="s-icon"><i data-lucide="calendar-days"></i></span><span>Bookings</span>
                </a>
                <a href="{{ route('trainer.withdrawals') }}" class="sidebar-item {{ request()->routeIs('trainer.withdrawals') ? 'active' : '' }}">
                    <span class="s-icon"><i data-lucide="wallet"></i></span><span>Withdrawals</span>
                </a>
                @endif

                @if(Auth::user()->role == 'trainee')
                <a href="{{ route('bookings.index') }}" class="sidebar-item {{ request()->routeIs('bookings.*') ? 'active' : '' }}">
                    <span class="s-icon"><i data-lucide="calendar-days"></i></span><span>My Sessions</span>
                </a>
                <a href="{{ route('music.index') }}" class="sidebar-item {{ request()->routeIs('music.*') ? 'active' : '' }}">
                    <span class="s-icon"><i data-lucide="music"></i></span><span>Workout Music</span>
                </a>
                @endif
                @endif
            </nav>

            <div class="s-divider" style="margin-top:1rem;"></div>

            <div class="appearance-panel" style="margin:0 0 1rem;">
                <div class="appearance-title">APPEARANCE</div>
                <div class="theme-list">
                    <button type="button" class="theme-row" data-theme-choice="aurora">
                        <span class="theme-dot" style="background:linear-gradient(135deg,#8b5cf6,#ec4899);"></span>
                        Aurora
                    </button>
                    <button type="button" class="theme-row" data-theme-choice="ember">
                        <span class="theme-dot" style="background:linear-gradient(135deg,#f97316,#e11d48);"></span>
                        Ember
                    </button>
                    <button type="button" class="theme-row" data-theme-choice="ocean">
                        <span class="theme-dot" style="background:linear-gradient(135deg,#0ea5e9,#14b8a6);"></span>
                        Ocean
                    </button>
                    <button type="button" class="theme-row" data-theme-choice="forest">
                        <span class="theme-dot" style="background:linear-gradient(135deg,#22c55e,#84cc16);"></span>
                        Forest
                    </button>
                    <button type="button" class="theme-row" data-theme-choice="graphite">
                        <span class="theme-dot" style="background:linear-gradient(135deg,#94a3b8,#38bdf8);"></span>
                        Graphite
                    </button>
                </div>
            </div>


        </div>
    </aside>

    <!-- MAIN CONTENT -->
    <main class="main-content" id="mainContent">
        @if(session('success'))
            <div class="alert-success fade-in-up">
                <span style="font-size:1.2rem;"><i data-lucide="circle-check"></i></span>
                <p style="font-weight:500;">{{ session('success') }}</p>
            </div>
        @endif

        @if(session('error'))
            <div class="alert-error fade-in-up">
                <span style="font-size:1.2rem;"><i data-lucide="circle-x"></i></span>
                <p style="font-weight:500;">{{ session('error') }}</p>
            </div>
        @endif

        <div class="fade-in-up">
            @yield('content')
        </div>
    </main>



    <script>
    // Starfield
    (function(){
        const c=document.getElementById('stars'),ctx=c.getContext('2d');let W,H,S=[];
        function resize(){W=c.width=innerWidth;H=c.height=innerHeight;}
        function init(){S=Array.from({length:80},()=>({x:Math.random()*W,y:Math.random()*H,r:Math.random()*0.8+.2,a:Math.random()*0.4,da:(Math.random()-.5)*.003}));}
        let cachedAccent = null;
        let lastTheme = null;

        function getAccentColor(alpha) {
            const currentTheme = document.documentElement.dataset.theme;
            if (currentTheme !== lastTheme || !cachedAccent) {
                lastTheme = currentTheme;
                const accent = getComputedStyle(document.documentElement).getPropertyValue('--vg-accent').trim() || '#c4b5fd';
                if (accent.startsWith('#')) {
                    const hex = accent.replace('#','');
                    const value = hex.length === 3 ? hex.split('').map(ch => ch + ch).join('') : hex;
                    const num = parseInt(value, 16);
                    cachedAccent = [(num >> 16) & 255, (num >> 8) & 255, num & 255];
                } else {
                    cachedAccent = [196, 181, 253];
                }
            }
            return `rgba(${cachedAccent[0]},${cachedAccent[1]},${cachedAccent[2]},${alpha * 0.25})`;
        }
        function draw(){ctx.clearRect(0,0,W,H);S.forEach(s=>{s.a=Math.max(.05,Math.min(1,s.a+s.da));if(s.a<=.05||s.a>=1)s.da*=-1;ctx.beginPath();ctx.arc(s.x,s.y,s.r,0,Math.PI*2);ctx.fillStyle=getAccentColor(s.a);ctx.fill();});requestAnimationFrame(draw);}
        window.addEventListener('resize',()=>{resize();init();});resize();init();draw();
    })();

    // Appearance themes
    (function(){
        const themes = ['aurora', 'ember', 'ocean', 'forest', 'graphite'];
        const buttons = document.querySelectorAll('[data-theme-choice]');

        function setTheme(theme) {
            const nextTheme = themes.includes(theme) ? theme : 'aurora';
            document.documentElement.dataset.theme = nextTheme;
            localStorage.setItem('virtugym-theme', nextTheme);
            buttons.forEach(button => {
                button.classList.toggle('active', button.dataset.themeChoice === nextTheme);
            });
        }

        buttons.forEach(button => {
            button.addEventListener('click', function(){
                setTheme(this.dataset.themeChoice);
            });
        });

        setTheme(localStorage.getItem('virtugym-theme') || 'aurora');
    })();

    // Dropdown
    function toggleDropdown() {
        document.getElementById('dropdownMenu').classList.toggle('hidden');
    }
    document.addEventListener('click', function(e) {
        const d = document.getElementById('userDropdown');
        const m = document.getElementById('dropdownMenu');
        if (d && m && !d.contains(e.target)) m.classList.add('hidden');
    });

    // Mobile sidebar
    function toggleSidebar() {
        document.getElementById('sidebar').classList.toggle('mobile-open');
    }

    // Auto-dismiss alerts
    setTimeout(function() {
        document.querySelectorAll('.alert-success, .alert-error').forEach(function(el) {
            el.style.transition = 'opacity .4s';
            el.style.opacity = '0';
            setTimeout(()=>el.remove(), 400);
        });
    }, 5000);
    
    // Premium Top Loading Progress Bar
    (function() {
        const bar = document.getElementById('top-loading-bar');
        if (!bar) return;

        function startLoading() {
            bar.style.opacity = '1';
            bar.style.width = '0%';
            setTimeout(() => {
                bar.style.width = '70%';
            }, 50);
            setTimeout(() => {
                bar.style.width = '90%';
            }, 800);
        }

        // Intercept internal link clicks
        document.addEventListener('click', function(e) {
            const link = e.target.closest('a');
            if (!link) return;

            const url = link.getAttribute('href');
            if (!url || url.startsWith('#') || url.startsWith('javascript:') || link.target === '_blank' || e.metaKey || e.ctrlKey) {
                return;
            }

            if (link.hostname === window.location.hostname) {
                startLoading();
            }
        });

        // Intercept form submissions
        document.addEventListener('submit', function(e) {
            startLoading();
        });

        // Complete loading on page mount
        if (document.readyState === 'complete' || document.readyState === 'interactive') {
            bar.style.width = '100%';
            setTimeout(() => {
                bar.style.opacity = '0';
                setTimeout(() => {
                    bar.style.width = '0%';
                }, 400);
            }, 200);
        } else {
            window.addEventListener('DOMContentLoaded', function() {
                bar.style.width = '100%';
                setTimeout(() => {
                    bar.style.opacity = '0';
                    setTimeout(() => {
                        bar.style.width = '0%';
                    }, 400);
                }, 200);
            });
        }
    })();

    @if(Auth::user()->role === 'trainee')
    // ============================================================
    // PERSISTENT GYM PLAYER ENGINE (Trainee only)
    // ============================================================
    (function(){
        const toggle = document.getElementById('musicToggle');
        const STORAGE_KEY = 'virtugym-music-enabled';
        const SONG_KEY = 'virtugym-current-song';
        const VOLUME_KEY = 'virtugym-music-volume';
        const HISTORY_KEY = 'virtugym-music-history';
        const STATE_KEY = 'virtugym-music-playing';
        const TIME_KEY = 'virtugym-music-time';
        
        let timeInterval = null;
        function startTrackingTime() {
            if (timeInterval) clearInterval(timeInterval);
            timeInterval = setInterval(() => {
                if (player && typeof player.getCurrentTime === 'function' && isPlaying) {
                    const currentTime = player.getCurrentTime();
                    localStorage.setItem(TIME_KEY, currentTime.toString());
                    window.dispatchEvent(new CustomEvent('gym-player-time-update', {
                        detail: {
                            currentTime: currentTime,
                            duration: player.getDuration ? player.getDuration() : 0
                        }
                    }));
                }
            }, 1000);
        }

        function stopTrackingTime() {
            if (timeInterval) {
                clearInterval(timeInterval);
                timeInterval = null;
            }
        }
        
        let player = null;
        let isPlaying = false;
        let enabled = localStorage.getItem(STORAGE_KEY) !== 'off';
        let currentVolume = parseInt(localStorage.getItem(VOLUME_KEY) || '38');
        
        // Initial / Fallback song
        const defaultSong = {
            video_id: '83R59AnBY90',
            title: 'NEFFEX - Grateful [Clean Gym Motivation]',
            channel: 'NEFFEX Music',
            thumbnail: 'https://img.youtube.com/vi/83R59AnBY90/0.jpg'
        };

        function getActiveSong() {
            try {
                return JSON.parse(localStorage.getItem(SONG_KEY)) || defaultSong;
            } catch(e) {
                return defaultSong;
            }
        }

        function setActiveSong(song) {
            localStorage.setItem(SONG_KEY, JSON.stringify(song));
            addToHistory(song);
            updateFloatingPlayerUI();
            
            // Dispatch event for active music page to sync
            window.dispatchEvent(new CustomEvent('gym-song-changed', { detail: song }));
        }

        function addToHistory(song) {
            try {
                let history = JSON.parse(localStorage.getItem(HISTORY_KEY) || '[]');
                history = history.filter(s => s.video_id !== song.video_id);
                history.unshift(song);
                if (history.length > 8) history.pop();
                localStorage.setItem(HISTORY_KEY, JSON.stringify(history));
                window.dispatchEvent(new CustomEvent('gym-history-updated'));
            } catch(e) {}
        }

        function loadYouTubeApi() {
            if (window.YT && window.YT.Player) return Promise.resolve();
            if (window.virtugymYouTubeApiPromise) return window.virtugymYouTubeApiPromise;

            window.virtugymYouTubeApiPromise = new Promise(resolve => {
                const previousReady = window.onYouTubeIframeAPIReady;
                window.onYouTubeIframeAPIReady = function() {
                    if (typeof previousReady === 'function') previousReady();
                    resolve();
                };

                const script = document.createElement('script');
                script.src = 'https://www.youtube.com/iframe_api';
                document.head.appendChild(script);
            });

            return window.virtugymYouTubeApiPromise;
        }

        async function createPlayer() {
            if (player) return player;

            const song = getActiveSong();
            await loadYouTubeApi();

            player = new YT.Player('youtubeBackgroundPlayer', {
                width: '1',
                height: '1',
                videoId: song.video_id,
                playerVars: {
                    autoplay: 0,
                    controls: 0,
                    disablekb: 1,
                    fs: 0,
                    loop: 1,
                    modestbranding: 1,
                    playsinline: 1,
                    playlist: song.video_id,
                    rel: 0,
                },
                events: {
                    onReady: function(event) {
                        event.target.setVolume(currentVolume);
                        // If it was playing in the previous page, try to resume
                        if (enabled && localStorage.getItem(STATE_KEY) === 'true') {
                            startMusic(false);
                        }
                    },
                    onStateChange: function(event) {
                        if (event.data === YT.PlayerState.PLAYING) {
                            isPlaying = true;
                            localStorage.setItem(STATE_KEY, 'true');
                            updateStates(true);
                            startTrackingTime();
                        } else if (event.data === YT.PlayerState.PAUSED || event.data === YT.PlayerState.ENDED) {
                            isPlaying = false;
                            localStorage.setItem(STATE_KEY, 'false');
                            updateStates(false);
                            stopTrackingTime();
                        }
                    },
                    onError: function() {
                        isPlaying = false;
                        updateStates(false);
                    },
                },
            });

            return player;
        }

        async function startMusic(force = false) {
            if (!enabled && !force) return;
            const nextPlayer = await createPlayer();
            if (!nextPlayer || isPlaying || typeof nextPlayer.playVideo !== 'function') return;

            try {
                enabled = true;
                localStorage.setItem(STORAGE_KEY, 'on');
                localStorage.setItem(STATE_KEY, 'true');
                nextPlayer.unMute();
                nextPlayer.setVolume(currentVolume);
                
                // If the player loaded a different video previously
                const currentSong = getActiveSong();
                const playerUrl = nextPlayer.getVideoUrl ? nextPlayer.getVideoUrl() : '';
                const savedTime = parseFloat(localStorage.getItem(TIME_KEY) || '0');
                if (playerUrl && !playerUrl.includes(currentSong.video_id)) {
                    nextPlayer.loadVideoById({
                        videoId: currentSong.video_id,
                        startSeconds: savedTime,
                        suggestedQuality: 'default'
                    });
                } else {
                    if (savedTime > 0) {
                        nextPlayer.seekTo(savedTime, true);
                    }
                    nextPlayer.playVideo();
                }
                
                isPlaying = true;
                updateStates(true);
            } catch (error) {
                isPlaying = false;
                updateStates(false);
            }
        }

        function stopMusic() {
            isPlaying = false;
            localStorage.setItem(STATE_KEY, 'false');
            if (player && typeof player.pauseVideo === 'function') {
                player.pauseVideo();
            }
            updateStates(false);
        }

        function updateStates(playing) {
            toggle?.classList.toggle('playing', enabled && playing);
            toggle?.setAttribute('aria-pressed', enabled && playing ? 'true' : 'false');
            
            const miniWidget = document.getElementById('vg-mini-player');
            const miniPlayBtn = document.getElementById('vg-mini-play-btn');
            
            if (miniWidget) {
                miniWidget.classList.toggle('visible', true);
            }
            if (miniPlayBtn) {
                miniPlayBtn.innerHTML = playing ? 
                    `<svg style="width:14px;height:14px;fill:currentColor;" viewBox="0 0 24 24"><path d="M6 19h4V5H6v14zm8-14v14h4V5h-4z"/></svg>` :
                    `<svg style="width:14px;height:14px;fill:currentColor;margin-left:2px;" viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg>`;
            }

            // Sync visualizer on main music page if present
            window.dispatchEvent(new CustomEvent('gym-play-state-changed', { detail: { playing } }));
        }

        // Global control interface
        window.GymPlayer = {
            play: async function(song) {
                const songObj = typeof song === 'string' ? { video_id: song, title: 'Gym Mix', channel: 'VirtuGym', thumbnail: `https://img.youtube.com/vi/${song}/0.jpg` } : song;
                
                localStorage.setItem(TIME_KEY, '0');
                setActiveSong(songObj);
                
                enabled = true;
                localStorage.setItem(STORAGE_KEY, 'on');
                
                const nextPlayer = await createPlayer();
                if (nextPlayer && typeof nextPlayer.loadVideoById === 'function') {
                    nextPlayer.loadVideoById({
                        videoId: songObj.video_id,
                        startSeconds: 0,
                        suggestedQuality: 'default'
                    });
                    isPlaying = true;
                    updateStates(true);
                }
            },
            pause: function() {
                stopMusic();
            },
            toggle: function() {
                if (isPlaying) {
                    stopMusic();
                } else {
                    startMusic(true);
                }
            },
            setVolume: function(vol) {
                currentVolume = Math.max(0, Math.min(100, vol));
                localStorage.setItem(VOLUME_KEY, currentVolume);
                if (player && typeof player.setVolume === 'function') {
                    player.setVolume(currentVolume);
                }
                const slider = document.getElementById('vg-mini-volume');
                if (slider) slider.value = currentVolume;
            },
            getVolume: function() {
                return currentVolume;
            },
            isPlaying: function() {
                return isPlaying;
            },
            getActiveSong: getActiveSong
        };

        // UI Setup: Inject Glassmorphic Mini Player
        const miniPlayerHtml = `
            <div id="vg-mini-player" style="
                position: fixed;
                bottom: 24px;
                right: 24px;
                width: 320px;
                background: rgba(15, 10, 30, 0.85);
                backdrop-filter: blur(16px);
                -webkit-backdrop-filter: blur(16px);
                border: 1px solid rgba(139, 92, 246, 0.35);
                border-radius: 20px;
                padding: 12px 16px;
                z-index: 9999;
                box-shadow: 0 10px 30px rgba(0,0,0,0.5), 0 0 20px rgba(139, 92, 246, 0.15);
                display: flex;
                align-items: center;
                gap: 12px;
                transform: translateY(100px);
                opacity: 0;
                transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
                pointer-events: auto;
            ">
                <img id="vg-mini-thumb" src="" alt="" style="width:48px;height:48px;object-fit:cover;border-radius:10px;background:rgba(255,255,255,0.05);border:1px solid rgba(255,255,255,0.1);">
                <div style="flex: 1; min-width: 0;">
                    <div style="font-size: 0.65rem; color: #a78bfa; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 2px;">Now Playing</div>
                    <div id="vg-mini-title" style="font-size: 0.8rem; font-weight: 700; color: #fff; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; line-height: 1.2;">-</div>
                    <div id="vg-mini-channel" style="font-size: 0.7rem; color: rgba(255,255,255,0.5); white-space: nowrap; overflow: hidden; text-overflow: ellipsis; margin-top: 1px;">-</div>
                </div>
                <div style="display: flex; align-items: center; gap: 8px;">
                    <button id="vg-mini-play-btn" style="background: linear-gradient(135deg, #8b5cf6, #ec4899); color: #fff; width: 32px; height: 32px; border-radius: 50%; display: flex; align-items: center; justify-content: center; cursor: pointer; border: none; transition: transform 0.2s;">
                        <svg style="width:14px;height:14px;fill:currentColor;" viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg>
                    </button>
                    <div style="position: relative; display: flex; align-items: center;" onmouseover="document.getElementById('vg-mini-vol-popup').style.opacity='1'" onmouseout="document.getElementById('vg-mini-vol-popup').style.opacity='0'">
                        <button style="background: rgba(255,255,255,0.08); color: rgba(255,255,255,0.7); width: 28px; height: 28px; border-radius: 50%; display: flex; align-items: center; justify-content: center; cursor: pointer; border: none;">
                            <svg style="width:14px;height:14px;fill:currentColor;" viewBox="0 0 24 24"><path d="M3 9v6h4l5 5V4L7 9H3zm13.5 3c0-1.77-1.02-3.29-2.5-4.03v8.05c1.48-.73 2.5-2.25 2.5-4.02zM14 3.23v2.06c2.89.86 5 3.54 5 6.71s-2.11 5.85-5 6.71v2.06c4.01-.91 7-4.49 7-8.77s-2.99-7.86-7-8.77z"/></svg>
                        </button>
                        <div id="vg-mini-vol-popup" style="position: absolute; bottom: 32px; left: 50%; transform: translateX(-50%); background: rgba(15,10,30,0.95); border: 1px solid rgba(139,92,246,0.3); padding: 8px; border-radius: 8px; opacity: 0; transition: opacity 0.2s; pointer-events: auto;">
                            <input id="vg-mini-volume" type="range" min="0" max="100" style="writing-mode: bt-lr; -webkit-appearance: slider-vertical; width: 8px; height: 60px; background: rgba(255,255,255,0.1); border-radius: 4px; outline: none; cursor: pointer;">
                        </div>
                    </div>
                </div>
            </div>
            <style>
                #vg-mini-player.visible {
                    transform: translateY(0);
                    opacity: 1;
                }
            </style>
        `;
        document.body.insertAdjacentHTML('beforeend', miniPlayerHtml);

        function updateFloatingPlayerUI() {
            const song = getActiveSong();
            const thumb = document.getElementById('vg-mini-thumb');
            const title = document.getElementById('vg-mini-title');
            const channel = document.getElementById('vg-mini-channel');
            
            if (thumb) thumb.src = song.thumbnail || `https://img.youtube.com/vi/${song.video_id}/0.jpg`;
            if (title) title.textContent = song.title;
            if (channel) channel.textContent = song.channel;
        }

        // Event Listeners for Mini Player Controls
        document.getElementById('vg-mini-play-btn')?.addEventListener('click', () => {
            window.GymPlayer.toggle();
        });

        const volSlider = document.getElementById('vg-mini-volume');
        if (volSlider) {
            volSlider.value = currentVolume;
            volSlider.addEventListener('input', (e) => {
                window.GymPlayer.setVolume(e.target.value);
            });
        }

        toggle?.addEventListener('click', function(e){
            e.stopPropagation();
            window.GymPlayer.toggle();
        });

        function unlockFromGesture(event) {
            if (toggle && event.target && toggle.contains(event.target)) return;
            // Only trigger auto-play from gesture if it was already playing in the previous session/page
            if (localStorage.getItem(STATE_KEY) === 'true') {
                startMusic(false);
            }
        }

        updateFloatingPlayerUI();
        window.addEventListener('load', () => {
            createPlayer();
            // If active song is already set and user previously had playing=true, show widget immediately
            if (localStorage.getItem(STATE_KEY) === 'true') {
                document.getElementById('vg-mini-player')?.classList.add('visible');
            }
        });
        
        ['pointerdown', 'keydown', 'touchstart'].forEach(eventName => {
            window.addEventListener(eventName, unlockFromGesture, { passive: true });
        });
    })();
    @endif
    </script>

    <script>
    (function() {
        const bellBtn = document.getElementById('notificationBell');
        const dropdown = document.getElementById('notificationDropdown');
        const badge = document.getElementById('notificationBadge');
        const countText = document.getElementById('notificationCountText');
        const listContainer = document.getElementById('notificationList');
        const container = document.getElementById('notificationDropdownContainer');

        let notifications = [];

        async function fetchNotifications() {
            try {
                const response = await fetch('{{ route('notifications.index') }}');
                notifications = await response.json();
                renderNotifications();
            } catch (error) {
                console.error('Failed to fetch notifications:', error);
                listContainer.innerHTML = `
                    <div style="padding: 24px 16px; text-align: center; color: #f87171; font-size: .82rem;">
                        ⚠️ Failed to load notifications.
                    </div>
                `;
            }
        }

        function renderNotifications() {
            const count = notifications.length;
            
            // Update badge
            if (count > 0) {
                badge.textContent = count;
                badge.style.display = 'flex';
                countText.textContent = `${count} active`;
            } else {
                badge.style.display = 'none';
                countText.textContent = '0 active';
            }

            // Render list
            if (count === 0) {
                listContainer.innerHTML = `
                    <div style="padding: 32px 16px; text-align: center; color: var(--vg-text-faint); font-size: .82rem;">
                        <div style="font-size: 1.5rem; margin-bottom: 8px;">✨</div>
                        All caught up! No notifications.
                    </div>
                `;
                return;
            }

            const categoryIcons = {
                booking: '📅',
                workout: '💪',
                water: '💧',
                progress: '📈',
                admin: '🛡️',
                profile: '👤'
            };

            const categoryColors = {
                success: '#34d399',
                warning: '#fbbf24',
                info: '#60a5fa',
                danger: '#f87171'
            };

            listContainer.innerHTML = notifications.map(notif => {
                const icon = categoryIcons[notif.category] || '🔔';
                const color = categoryColors[notif.type] || 'var(--vg-accent)';
                return `
                    <a href="${notif.url}" class="block hover:bg-gray-800/40 transition-colors" style="display: flex; gap: 12px; padding: 12px 16px; border-bottom: 1px solid rgba(255,255,255,0.05); text-decoration: none; align-items: flex-start;">
                        <span style="font-size: 1.2rem; min-width: 24px; text-align: center; padding-top: 2px;">${icon}</span>
                        <div style="flex: 1; min-width: 0;">
                            <div style="display: flex; justify-content: space-between; align-items: baseline; gap: 8px; margin-bottom: 2px;">
                                <h4 style="font-size: .82rem; font-weight: 700; color: var(--vg-text-strong); margin: 0; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">${notif.title}</h4>
                                <span style="font-size: .65rem; color: var(--vg-text-faint); white-space: nowrap;">${notif.time_ago}</span>
                            </div>
                            <p style="font-size: .78rem; color: var(--vg-text-muted); margin: 0; line-height: 1.35; overflow: hidden; text-overflow: ellipsis; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical;">${notif.message}</p>
                        </div>
                    </a>
                `;
            }).join('');
        }

        // Toggle dropdown
        bellBtn?.addEventListener('click', function(e) {
            e.stopPropagation();
            const isHidden = dropdown.classList.contains('hidden');
            
            // Close other dropdowns
            document.querySelectorAll('.dropdown-menu').forEach(menu => {
                if (menu !== dropdown) menu.classList.add('hidden');
            });

            if (isHidden) {
                dropdown.classList.remove('hidden');
                fetchNotifications();
            } else {
                dropdown.classList.add('hidden');
            }
        });

        // Close when clicking outside
        document.addEventListener('click', function(e) {
            if (dropdown && !container.contains(e.target)) {
                dropdown.classList.add('hidden');
            }
        });

        // Initial fetch on load to show count immediately
        fetchNotifications();
    })();
    </script>
    @stack('modals')
    @stack('scripts')
</body>
</html>
