@extends('layouts.app')

@section('title', 'AI Fitness Coach')

@section('content')
<style>
    /* Hide scrollbars for recentQueriesList, workspaceContent, and chatMessages */
    #recentQueriesList::-webkit-scrollbar,
    #workspaceContent::-webkit-scrollbar,
    #chatMessages::-webkit-scrollbar {
        display: none !important;
        width: 0 !important;
        height: 0 !important;
        background: transparent !important;
    }
    #recentQueriesList,
    #workspaceContent,
    #chatMessages {
        -ms-overflow-style: none !important;
        scrollbar-width: none !important;
    }

    /* Premium glow and custom CSS animations */
    .glow-purple {
        box-shadow: 0 0 20px rgba(139, 92, 246, 0.15);
    }
    .neon-border {
        border-color: rgba(139, 92, 246, 0.25);
    }
    .sidebar-menu-btn {
        transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
    }
    .sidebar-menu-btn.active {
        background: rgba(139, 92, 246, 0.12) !important;
        border: 1px solid rgba(139, 92, 246, 0.35) !important;
        border-left: 4px solid #8b5cf6 !important;
        color: #fff !important;
        box-shadow: 0 0 10px rgba(139, 92, 246, 0.1);
    }
    .chat-pill-input {
        background: rgba(10, 10, 26, 0.6);
        border: 1px solid rgba(139, 92, 246, 0.2);
    }
    .chat-pill-input:focus-within {
        border-color: rgba(139, 92, 246, 0.6);
        box-shadow: inset 0 2px 8px rgba(0,0,0,0.8), 0 0 15px rgba(139, 92, 246, 0.35) !important;
    }
    #chatInput {
        background-color: transparent !important;
        background: transparent !important;
        border: none !important;
        outline: none !important;
        box-shadow: none !important;
    }
    #chatInput:focus {
        background-color: transparent !important;
        background: transparent !important;
        border: none !important;
        outline: none !important;
        box-shadow: none !important;
    }
    #chatInput:-webkit-autofill,
    #chatInput:-webkit-autofill:hover, 
    #chatInput:-webkit-autofill:focus, 
    #chatInput:-webkit-autofill:active {
        -webkit-box-shadow: 0 0 0 30px #0a0a1a inset !important;
        -webkit-text-fill-color: white !important;
        transition: background-color 5000s ease-in-out 0s;
    }
    /* Animated online indicator dot */
    .online-indicator {
        position: relative;
        display: inline-block;
    }
    .online-indicator::after {
        content: '';
        position: absolute;
        width: 8px;
        height: 8px;
        background-color: #22c55e;
        border-radius: 50%;
        left: 0;
        top: 50%;
        transform: translateY(-50%);
        box-shadow: 0 0 8px #22c55e;
        animation: pulse-green 2s infinite;
    }
    @keyframes pulse-green {
        0% {
            box-shadow: 0 0 0 0 rgba(34, 197, 94, 0.7);
        }
        70% {
            box-shadow: 0 0 0 8px rgba(34, 197, 94, 0);
        }
        100% {
            box-shadow: 0 0 0 0 rgba(34, 197, 94, 0);
        }
    }

    /* Markdown rendering styles */
    .markdown-content p {
        margin-bottom: 0.75rem;
    }
    .markdown-content p:last-child {
        margin-bottom: 0;
    }
    .markdown-content strong {
        color: #fff;
        font-weight: 700;
    }
    .markdown-content ul, .markdown-content ol {
        margin-left: 1.25rem;
        margin-bottom: 0.75rem;
        list-style-position: outside;
    }
    .markdown-content ul {
        list-style-type: disc;
    }
    .markdown-content ol {
        list-style-type: decimal;
    }
    .markdown-content li {
        margin-bottom: 0.25rem;
    }
    .markdown-content h1, .markdown-content h2, .markdown-content h3, .markdown-content h4 {
        color: #fff;
        font-weight: 700;
        margin-top: 1rem;
        margin-bottom: 0.5rem;
    }
    .markdown-content h1 { font-size: 1.25rem; }
    .markdown-content h2 { font-size: 1.15rem; }
    .markdown-content h3 { font-size: 1.05rem; }
    .markdown-content h4 { font-size: 1rem; }
    .markdown-content code {
        background-color: rgba(255, 255, 255, 0.08);
        padding: 0.125rem 0.25rem;
        border-radius: 0.25rem;
        font-family: monospace;
        font-size: 0.875rem;
    }
    .markdown-content pre {
        background-color: rgba(10, 10, 26, 0.8);
        border: 1px solid rgba(255, 255, 255, 0.08);
        padding: 0.75rem;
        border-radius: 0.5rem;
        overflow-x: auto;
        margin-bottom: 0.75rem;
    }
    .markdown-content pre code {
        background-color: transparent;
        padding: 0;
        border-radius: 0;
    }
</style>

<div class="w-full">
    <!-- Main Grid (Left Sidebar, Center Chat, Right Action Cards) -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
        
        <!-- COLUMN 1: LEFT SIDEBAR (Col span 2) -->
        <div class="lg:col-span-2 bg-gray-900/60 backdrop-blur-md border border-gray-800 rounded-2xl p-4 flex flex-col h-[800px] shadow-2xl">
            <!-- Reset Button -->
            <button onclick="startNewChat()" class="w-full flex items-center justify-between px-4 py-3 border border-dashed border-purple-500/30 hover:border-purple-500/70 bg-purple-500/5 hover:bg-purple-500/10 text-purple-400 hover:text-purple-300 rounded-xl transition duration-300 group">
                <span class="font-bold text-sm tracking-wide">New Chat</span>
                <span class="text-xs bg-purple-600/20 group-hover:bg-purple-600/30 px-1.5 py-0.5 rounded text-purple-300 font-mono">⌘K</span>
            </button>

            <!-- Recent Queries List (Max 4 Items, smaller container) -->
            <div class="mt-6 flex-1 flex flex-col min-h-0">
                <span class="text-[10px] uppercase tracking-wider text-gray-500 font-bold block mb-2.5">Recent Actions</span>
                <div id="recentQueriesList" class="max-h-[350px] overflow-y-auto space-y-1.5 pr-1">
                    <!-- Dynamically populated -->
                </div>
            </div>

            <!-- Sidebar Footer (Vertical Stats & controls) -->
            <div class="mt-auto pt-3 border-t border-gray-800 flex flex-col gap-3">
                <!-- Session Context Token indicator -->
                <div class="flex items-center justify-between text-xs text-gray-400 font-mono bg-gray-950/40 border border-gray-800 px-3 py-3 rounded-lg">
                    <span>Context:</span>
                    <span class="text-purple-400 font-bold"><span id="tokenIndicatorCount">0</span>/32K (<span id="tokenIndicatorPercent">0</span>%)</span>
                </div>
                
                <!-- Model Badge with Pulsing Blue Dot -->
                <div class="flex items-center justify-between text-xs text-gray-400 font-mono bg-gray-950/40 border border-gray-800 px-3 py-3 rounded-lg">
                    <span>Model:</span>
                    <div class="flex items-center gap-1 font-semibold text-cyan-400">
                        <span class="w-1.5 h-1.5 bg-cyan-400 rounded-full inline-block animate-pulse shadow-[0_0_8px_#22d3ee]"></span>
                        <span>Gemini-Pro</span>
                    </div>
                </div>

                <!-- Clear Chat Button -->
                <button onclick="clearChatHistory()" class="w-full flex items-center justify-center gap-1.5 text-[11px] text-red-400 hover:text-red-300 font-semibold bg-red-950/20 border border-red-500/20 py-2.5 rounded-xl transition duration-200 shadow-sm" title="Clear Chat History">
                    <i data-lucide="trash-2" class="w-3.5 h-3.5"></i>
                    <span>Clear Chat</span>
                </button>
            </div>
        </div>

        <!-- COLUMN 2: CENTER WORKSTATION (Col span 8) -->
        <div id="centerWorkstation" class="lg:col-span-8 bg-gray-900/40 backdrop-blur-md border border-purple-500/10 rounded-2xl flex flex-col h-[800px] shadow-2xl relative overflow-hidden transition-all duration-300">
            
            <!-- Workstation Header -->
            <div class="bg-gray-900/80 px-6 py-4 border-b border-gray-800 flex items-center justify-between">
                <div class="flex items-center gap-3.5">
                    <div class="bg-gradient-to-tr from-purple-600 to-indigo-600 p-2 rounded-xl shadow-lg shadow-purple-600/10 border border-purple-500/20">
                        <i data-lucide="bot" class="w-5 h-5 text-white"></i>
                    </div>
                    <div>
                        <h2 class="font-bold text-white text-base tracking-wide" id="activeWorkspaceTitle">VirtuCoach AI</h2>
                        <span class="text-xs text-gray-400 pl-4 online-indicator">Online</span>
                    </div>
                </div>
            </div>

            <!-- Workspace Action Panels Container -->
            <div class="flex-1 overflow-y-auto p-6 flex flex-col justify-between min-h-0" id="workspaceContent">
                
                <!-- PANEL 1: GENERAL CHAT -->
                <div id="panel-chat" class="tab-panel flex flex-col h-full justify-between min-h-0 flex-1">
                    <!-- Chat Message Area (Expanded Viewport Height) -->
                    <div id="chatMessages" class="flex-1 overflow-y-auto space-y-4 mb-4 pr-1 min-h-[500px]">
                        <!-- Welcoming prompt chips & onboarding hints inside empty chat space -->
                        <div class="flex flex-col gap-4 max-w-[90%] mt-2" id="onboardingWelcomeContainer">
                            <!-- Welcome bubble -->
                            <div class="bg-[#13132e] border border-gray-800 text-gray-200 rounded-2xl rounded-tl-none p-4 pr-10 shadow-lg relative group">
                                <p class="text-sm leading-relaxed">👋 Hi! I'm VirtuCoach, your AI fitness assistant. Ask me anything about custom workouts, diet advice, form checks, or daily motivation!</p>
                                <button onclick="copyMessageText(this)" class="absolute right-2.5 top-2.5 text-gray-500 hover:text-gray-300 opacity-0 group-hover:opacity-100 transition duration-200 p-1 rounded hover:bg-gray-800" title="Copy Response">
                                    <i data-lucide="copy" class="w-3.5 h-3.5"></i>
                                </button>
                            </div>

                            <!-- Onboarding Hints -->
                            <div class="flex items-start gap-2 bg-purple-950/10 p-3 rounded-xl border border-purple-500/10">
                                <i data-lucide="help-circle" class="w-4 h-4 text-purple-400 shrink-0 mt-0.5 animate-pulse"></i>
                                <div class="text-[10px] text-gray-400 leading-normal">
                                    <strong>💡 Onboarding Hint:</strong> Use the workspace switcher on the right to toggle active panels, or type custom queries below.
                                </div>
                            </div>
                        </div>
                    </div>
 
                    <!-- AI Active Triggers (Non-scrollable chip tags above typing field) -->
                    <div class="mb-3">
                        <span class="text-[10px] uppercase tracking-wider text-gray-500 font-bold block mb-2">AI Active Triggers</span>
                        <div class="flex flex-wrap gap-2">
                            <!-- Trigger 1 -->
                            <button onclick="setPrompt('Create a personalized workout plan')" class="flex items-center gap-1.5 px-3 py-1.5 bg-purple-600/10 hover:bg-purple-600/20 border border-purple-500/20 hover:border-purple-500/40 rounded-full text-xs text-purple-300 font-semibold transition duration-200">
                                <span>✨</span> Generate Workout
                            </button>
                            <!-- Trigger 2 -->
                            <button onclick="setPrompt('What is my personalized nutrition guide today?')" class="flex items-center gap-1.5 px-3 py-1.5 bg-green-600/10 hover:bg-green-600/20 border border-green-500/20 hover:border-green-500/40 rounded-full text-xs text-green-300 font-semibold transition duration-200">
                                <span>🥗</span> Nutrition Advice
                            </button>
                            <!-- Trigger 3 -->
                            <button onclick="setPrompt('Predict my progress based on my profile')" class="flex items-center gap-1.5 px-3 py-1.5 bg-cyan-600/10 hover:bg-cyan-600/20 border border-cyan-500/20 hover:border-cyan-500/40 rounded-full text-xs text-cyan-300 font-semibold transition duration-200">
                                <span>📈</span> Progress Predictor
                            </button>
                            <!-- Trigger 4 -->
                            <button onclick="setPrompt('Give me some daily fitness motivation!')" class="flex items-center gap-1.5 px-3 py-1.5 bg-red-600/10 hover:bg-red-600/20 border border-red-500/20 hover:border-red-500/40 rounded-full text-xs text-red-300 font-semibold transition duration-200">
                                <span>⚡</span> Daily Motivation
                            </button>
                        </div>
                    </div>

                    <!-- Input Form Bar with deep inner shadows and border glow -->
                    <div class="space-y-2 pt-2 border-t border-gray-800/40">
                        <div class="chat-pill-input shadow-[inset_0_2px_8px_rgba(0,0,0,0.8)] rounded-full px-5 py-3.5 flex items-center gap-3">
                            <button id="voiceInput" class="text-gray-400 hover:text-purple-400 transition" title="Voice Input">
                                <i data-lucide="mic" class="w-5 h-5"></i>
                            </button>
                            <input type="text" id="chatInput" placeholder="Ask your fitness question..." 
                                   class="flex-1 bg-transparent focus:bg-transparent border-0 outline-none focus:ring-0 text-white placeholder-gray-500 text-sm">
                            <button id="sendChat" class="bg-purple-600 hover:bg-purple-500 text-white p-2.5 rounded-full transition shadow-lg shadow-purple-500/20 flex items-center justify-center shrink-0">
                                <i data-lucide="arrow-up" class="w-4.5 h-4.5"></i>
                            </button>
                        </div>
                        <p class="text-[10px] text-center text-gray-500 font-medium">VirtuCoach AI can make mistakes. Verify important health and workout details.</p>
                    </div>
                </div>

                <!-- PANEL 2: WORKOUT RECOMMENDER -->
                <div id="panel-workout" class="tab-panel hidden space-y-4 flex-1">
                    <div class="bg-gray-800/40 border border-gray-700/50 rounded-2xl p-6 glow-purple">
                        <div class="flex items-center justify-between mb-4 pb-4 border-b border-gray-700/50">
                            <div>
                                <h3 class="font-bold text-lg text-white">🏋️ AI Workout Generator</h3>
                                <p class="text-xs text-gray-400 mt-0.5">Custom routines calibrated to your current physical parameters</p>
                            </div>
                            <button onclick="getWorkoutRecommendation()" class="px-4 py-2 bg-purple-600/20 hover:bg-purple-600 text-purple-300 hover:text-white rounded-xl text-xs font-semibold border border-purple-500/30 transition">
                                🔄 Generate New
                            </button>
                        </div>
                        <div id="workoutRecommendation" class="text-sm text-gray-300 leading-relaxed min-h-[150px]">
                            <button onclick="getWorkoutRecommendation()" class="w-full py-12 text-center text-purple-400 hover:text-purple-300 font-semibold">
                                ⚡ Click to generate your personalized program
                            </button>
                        </div>
                    </div>
                </div>

                <!-- PANEL 3: NUTRITION ADVISOR -->
                <div id="panel-nutrition" class="tab-panel hidden space-y-4 flex-1">
                    <div class="bg-gray-800/40 border border-gray-700/50 rounded-2xl p-6 glow-purple">
                        <div class="flex items-center justify-between mb-4 pb-4 border-b border-gray-700/50">
                            <div>
                                <h3 class="font-bold text-lg text-white">🥗 Nutrition Advisor</h3>
                                <p class="text-xs text-gray-400 mt-0.5">Meal logs analysis and recommended macro caloric split</p>
                            </div>
                            <button onclick="getNutritionAdvice()" class="px-4 py-2 bg-green-600/20 hover:bg-green-600 text-green-300 hover:text-white rounded-xl text-xs font-semibold border border-green-500/30 transition">
                                🔄 Refresh Guide
                            </button>
                        </div>
                        <div id="nutritionAdvice" class="text-sm text-gray-300 leading-relaxed min-h-[150px]">
                            <button onclick="getNutritionAdvice()" class="w-full py-12 text-center text-green-400 hover:text-green-300 font-semibold">
                                🥦 Load my personalized nutrition plan
                            </button>
                        </div>
                    </div>
                </div>

                <!-- PANEL 4: PROGRESS FORECAST -->
                <div id="panel-progress" class="tab-panel hidden space-y-4 flex-1">
                    <div class="bg-gray-800/40 border border-gray-700/50 rounded-2xl p-6 glow-purple">
                        <div class="flex items-center justify-between mb-4 pb-4 border-b border-gray-700/50">
                            <div>
                                <h3 class="font-bold text-lg text-white">📊 Progress Predictor</h3>
                                <p class="text-xs text-gray-400 mt-0.5">AI projection model estimating target completion milestones</p>
                            </div>
                            <button onclick="getProgressPrediction()" class="px-4 py-2 bg-cyan-600/20 hover:bg-cyan-600 text-cyan-300 hover:text-white rounded-xl text-xs font-semibold border border-cyan-500/30 transition">
                                🔄 Update Forecast
                            </button>
                        </div>
                        <div id="progressPrediction" class="text-sm text-gray-300 leading-relaxed min-h-[150px]">
                            <button onclick="getProgressPrediction()" class="w-full py-12 text-center text-cyan-400 hover:text-cyan-300 font-semibold">
                                📈 Predict my completion roadmap
                            </button>
                        </div>
                    </div>
                </div>

                <!-- PANEL 5: FORM CHECK (TEXT) -->
                <div id="panel-form-check" class="tab-panel hidden space-y-4 flex-1">
                    <div class="bg-gray-800/40 border border-gray-700/50 rounded-2xl p-6 glow-purple">
                        <div class="mb-4 pb-4 border-b border-gray-700/50">
                            <h3 class="font-bold text-lg text-white">📹 Live Form Check</h3>
                            <p class="text-xs text-gray-400 mt-0.5">Describe your execution of any exercise to scan for form inconsistencies</p>
                        </div>
                        
                        <div class="grid grid-cols-1 gap-4">
                            <div>
                                <label class="text-xs font-semibold text-gray-400 block mb-1.5">Exercise Name</label>
                                <input type="text" id="formExercise" placeholder="e.g. Squats, Deadlift, Bench Press" 
                                       class="w-full px-4 py-3 bg-gray-950/60 border border-gray-800 rounded-xl text-white placeholder-gray-500 text-sm focus:outline-none focus:border-purple-500 transition">
                            </div>
                            <div>
                                <label class="text-xs font-semibold text-gray-400 block mb-1.5">Movement Description</label>
                                <textarea id="formDescription" rows="4" placeholder="Describe your posture, hand positioning, depth, or path..." 
                                          class="w-full px-4 py-3 bg-gray-950/60 border border-gray-800 rounded-xl text-white placeholder-gray-500 text-sm focus:outline-none focus:border-purple-500 transition"></textarea>
                            </div>
                            <button onclick="analyzeForm()" class="w-full py-3 bg-gradient-to-r from-purple-600 to-blue-600 hover:from-purple-500 hover:to-blue-500 text-white rounded-xl font-semibold text-sm transition shadow-lg shadow-purple-600/10">
                                Run Form Diagnostics
                            </button>
                        </div>

                        <div id="formAnalysisResult" class="mt-6 text-sm text-gray-300 leading-relaxed"></div>
                    </div>
                </div>

                <!-- PANEL 6: CUSTOM PLAN BUILDER -->
                <div id="panel-custom-plan" class="tab-panel hidden space-y-4 flex-1">
                    <div class="bg-gray-800/40 border border-gray-700/50 rounded-2xl p-6 glow-purple">
                        <div class="mb-4 pb-4 border-b border-gray-700/50">
                            <h3 class="font-bold text-lg text-white">📋 Custom Workout Plan</h3>
                            <p class="text-xs text-gray-400 mt-0.5">Compile structured circuits tailored to specific goals and active durations</p>
                        </div>
                        
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="text-xs font-semibold text-gray-400 block mb-1.5">Workout Goal</label>
                                <select id="planGoal" class="w-full px-4 py-3 bg-gray-950/60 border border-gray-800 rounded-xl text-white focus:outline-none focus:border-purple-500 text-sm transition">
                                    <option value="weight_loss">Weight Loss</option>
                                    <option value="muscle_gain">Muscle Gain</option>
                                    <option value="endurance">Endurance</option>
                                    <option value="general_fitness">General Fitness</option>
                                </select>
                            </div>
                            <div>
                                <label class="text-xs font-semibold text-gray-400 block mb-1.5">Duration (Minutes)</label>
                                <input type="number" id="planDuration" placeholder="e.g. 45" value="30" 
                                       class="w-full px-4 py-3 bg-gray-950/60 border border-gray-800 rounded-xl text-white placeholder-gray-500 text-sm focus:outline-none focus:border-purple-500 transition">
                            </div>
                        </div>
                        <button onclick="generateCustomPlan()" class="w-full mt-4 py-3 bg-gradient-to-r from-purple-600 to-blue-600 hover:from-purple-500 hover:to-blue-500 text-white rounded-xl font-semibold text-sm transition shadow-lg shadow-purple-600/10">
                            Generate Training Outline
                        </button>

                        <div id="customPlanResult" class="mt-6 text-sm text-gray-300 leading-relaxed"></div>
                    </div>
                </div>

                <!-- PANEL 7: DAILY MOTIVATION -->
                <div id="panel-motivation" class="tab-panel hidden space-y-4 flex-1">
                    <div class="bg-gray-800/40 border border-gray-700/50 rounded-2xl p-6 glow-purple text-center flex flex-col items-center justify-center min-h-[300px]">
                        <span class="text-3xl mb-4">⚡</span>
                        <h3 class="font-bold text-xl text-white mb-2">Daily Motivation</h3>
                        <div id="motivation" class="text-gray-300 italic text-base leading-relaxed max-w-lg mb-6">
                            <button onclick="getMotivation()" class="text-purple-400 hover:text-purple-300 font-semibold">
                                Get Daily Inspirational Quote
                            </button>
                        </div>
                    </div>
                </div>

            </div>
        </div>

        <!-- COLUMN 3: RIGHT SIDEBAR (Col span 2) - Houses the Switched Workspace -->
        <div id="rightSidebar" class="lg:col-span-2 bg-gray-900/60 backdrop-blur-md border border-gray-800 rounded-2xl p-4 flex flex-col h-[800px] shadow-2xl transition-all duration-300">
            <!-- AI Active Workspace switcher (Arranged vertically) -->
            <div>
                <span class="text-[10px] uppercase tracking-wider text-gray-500 font-bold block mb-3">AI Active Workspace</span>
                <div class="space-y-2" id="sidebarMenu">
                    <button onclick="switchTab('chat')" id="btn-tab-chat" class="sidebar-menu-btn active w-full flex items-center gap-3 px-4 py-3 bg-gray-800/10 hover:bg-gray-800/40 border border-gray-800 hover:border-purple-500/20 rounded-xl text-left text-xs font-semibold text-gray-300 transition">
                        <i data-lucide="message-square" class="w-4 h-4 text-purple-400 shrink-0"></i>
                        <span>Chat Session</span>
                    </button>
                    <button onclick="switchTab('workout')" id="btn-tab-workout" class="sidebar-menu-btn w-full flex items-center gap-3 px-4 py-3 bg-gray-800/10 hover:bg-gray-800/40 border border-gray-800 hover:border-purple-500/20 rounded-xl text-left text-xs font-semibold text-gray-300 transition">
                        <i data-lucide="dumbbell" class="w-4 h-4 text-blue-400 shrink-0"></i>
                        <span>Workout Generator</span>
                    </button>
                    <button onclick="switchTab('nutrition')" id="btn-tab-nutrition" class="sidebar-menu-btn w-full flex items-center gap-3 px-4 py-3 bg-gray-800/10 hover:bg-gray-800/40 border border-gray-800 hover:border-purple-500/20 rounded-xl text-left text-xs font-semibold text-gray-300 transition">
                        <i data-lucide="salad" class="w-4 h-4 text-green-400 shrink-0"></i>
                        <span>Nutrition Guide</span>
                    </button>
                    <button onclick="switchTab('progress')" id="btn-tab-progress" class="sidebar-menu-btn w-full flex items-center gap-3 px-4 py-3 bg-gray-800/10 hover:bg-gray-800/40 border border-gray-800 hover:border-purple-500/20 rounded-xl text-left text-xs font-semibold text-gray-300 transition">
                        <i data-lucide="trending-up" class="w-4 h-4 text-cyan-400 shrink-0"></i>
                        <span>Progress Forecast</span>
                    </button>
                    <button onclick="switchTab('form-check')" id="btn-tab-form-check" class="sidebar-menu-btn w-full flex items-center gap-3 px-4 py-3 bg-gray-800/10 hover:bg-gray-800/40 border border-gray-800 hover:border-purple-500/20 rounded-xl text-left text-xs font-semibold text-gray-300 transition">
                        <i data-lucide="video" class="w-4 h-4 text-yellow-400 shrink-0"></i>
                        <span>Live Form Check</span>
                    </button>
                    <button onclick="switchTab('custom-plan')" id="btn-tab-custom-plan" class="sidebar-menu-btn w-full flex items-center gap-3 px-4 py-3 bg-gray-800/10 hover:bg-gray-800/40 border border-gray-800 hover:border-purple-500/20 rounded-xl text-left text-xs font-semibold text-gray-300 transition">
                        <i data-lucide="file-text" class="w-4 h-4 text-orange-400 shrink-0"></i>
                        <span>Workout Plans</span>
                    </button>
                    <button onclick="switchTab('motivation')" id="btn-tab-motivation" class="sidebar-menu-btn w-full flex items-center gap-3 px-4 py-3 bg-gray-800/10 hover:bg-gray-800/40 border border-gray-800 hover:border-purple-500/20 rounded-xl text-left text-xs font-semibold text-gray-300 transition">
                        <i data-lucide="zap" class="w-4 h-4 text-red-400 shrink-0"></i>
                        <span>Daily Motivation</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/marked/marked.min.js"></script>
<script>
    const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
    
    // Tab switching functionality (keeps Right Sidebar open and stable at col-span-6)
    function switchTab(tabId) {
        // Toggle visibility of panels
        document.querySelectorAll('.tab-panel').forEach(panel => {
            panel.classList.add('hidden');
        });
        const targetPanel = document.getElementById('panel-' + tabId);
        if (targetPanel) {
            targetPanel.classList.remove('hidden');
        }

        // Toggle active states in sidebar menu
        document.querySelectorAll('.sidebar-menu-btn').forEach(btn => {
            btn.classList.remove('active');
        });
        const activeBtn = document.getElementById('btn-tab-' + tabId);
        if (activeBtn) {
            activeBtn.classList.add('active');
        }

        // Update workstation header title
        const titles = {
            'chat': 'VirtuCoach Chat',
            'workout': 'AI Workout Recommendation',
            'nutrition': 'Nutrition Advice',
            'progress': 'Progress Forecast',
            'form-check': 'Live Form Check',
            'custom-plan': 'Custom Workout Plan',
            'motivation': 'Daily Motivation'
        };
        document.getElementById('activeWorkspaceTitle').innerText = titles[tabId] || 'VirtuCoach AI';

        // Auto trigger recommendations on tab activation to keep UI rich
        if (tabId === 'workout' && !window.workoutRecommendationLoaded) {
            getWorkoutRecommendation();
            window.workoutRecommendationLoaded = true;
        } else if (tabId === 'nutrition' && !window.nutritionAdviceLoaded) {
            getNutritionAdvice();
            window.nutritionAdviceLoaded = true;
        } else if (tabId === 'progress' && !window.progressPredictionLoaded) {
            getProgressPrediction();
            window.progressPredictionLoaded = true;
        } else if (tabId === 'motivation' && !window.motivationLoaded) {
            getMotivation();
            window.motivationLoaded = true;
        }
    }

    // Chat functionality
    const chatInput = document.getElementById('chatInput');
    const sendButton = document.getElementById('sendChat');
    const chatMessages = document.getElementById('chatMessages');
    
    function setPrompt(text) {
        switchTab('chat');
        chatInput.value = text;
        chatInput.focus();
        sendMessage();
    }
    
    // Dynamic typing status management
    function setTypingState(isActive, statusText = "VirtuCoach is thinking") {
        const existing = document.getElementById('typingBubble');
        if (existing) {
            existing.remove();
        }
        
        if (isActive) {
            const div = document.createElement('div');
            div.id = 'typingBubble';
            div.className = 'flex justify-start mb-3';
            div.innerHTML = `
                <div class="bg-[#13132e] border border-gray-800 text-gray-400 rounded-2xl rounded-tl-none p-3.5 max-w-[85%] shadow-md flex items-center gap-2">
                    <span class="text-xs italic">${escapeHtml(statusText)}</span>
                    <div class="flex space-x-1 items-center pt-0.5">
                        <div class="w-1.5 h-1.5 bg-purple-500 rounded-full animate-bounce" style="animation-delay: 0ms"></div>
                        <div class="w-1.5 h-1.5 bg-purple-400 rounded-full animate-bounce" style="animation-delay: 150ms"></div>
                        <div class="w-1.5 h-1.5 bg-purple-300 rounded-full animate-bounce" style="animation-delay: 300ms"></div>
                    </div>
                </div>
            `;
            chatMessages.appendChild(div);
            chatMessages.scrollTop = chatMessages.scrollHeight;
        }
    }

    // Copy to clipboard helper
    function copyMessageText(btn) {
        const container = btn.closest('.relative').querySelector('.markdown-content') || btn.closest('.relative').querySelector('p');
        if (container) {
            // Get all text content excluding the copy button itself
            const tempDiv = container.cloneNode(true);
            const btnInClone = tempDiv.querySelector('button');
            if (btnInClone) btnInClone.remove();
            const textToCopy = tempDiv.innerText.trim();
            
            navigator.clipboard.writeText(textToCopy).then(() => {
                const originalHtml = btn.innerHTML;
                btn.innerHTML = `<i data-lucide="check" class="w-3.5 h-3.5 text-green-400"></i>`;
                if (window.lucide) window.lucide.createIcons();
                setTimeout(() => {
                    btn.innerHTML = originalHtml;
                    if (window.lucide) window.lucide.createIcons();
                }, 2000);
            }).catch(err => {
                console.error("Failed to copy text: ", err);
            });
        }
    }

    // Dynamic suggested follow-ups
    function getSuggestedPrompts(msgText) {
        const lower = msgText.toLowerCase();
        if (lower.includes('workout') || lower.includes('routine') || lower.includes('exercise')) {
            return ["Save this plan", "Modify for home gym", "Add warmup exercises"];
        }
        if (lower.includes('nutrition') || lower.includes('diet') || lower.includes('meal') || lower.includes('calor')) {
            return ["Show shopping list", "Adjust daily calories", "High protein alternatives"];
        }
        if (lower.includes('predict') || lower.includes('progress') || lower.includes('forecast')) {
            return ["How to speed up?", "Provide schedule", "Adjust duration"];
        }
        return ["Explain further", "Give exercises", "What's next?"];
    }

    // Render follow up prompts
    function renderFollowUps(suggestions) {
        // Remove old follow-ups
        document.querySelectorAll('.follow-up-row').forEach(row => row.remove());

        const div = document.createElement('div');
        div.className = 'flex flex-wrap gap-2 mt-2 mb-4 justify-start follow-up-row animate-fade-in';
        suggestions.forEach(prompt => {
            const button = document.createElement('button');
            button.className = 'bg-purple-950/20 hover:bg-purple-900/35 border border-purple-500/20 hover:border-purple-500/40 text-purple-300 hover:text-purple-200 px-3.5 py-1.5 rounded-full text-xs font-semibold transition cursor-pointer';
            button.textContent = prompt;
            button.onclick = () => setPrompt(prompt);
            div.appendChild(button);
        });
        chatMessages.appendChild(div);
        chatMessages.scrollTop = chatMessages.scrollHeight;
    }



    // Query sanitizer to clean typos like "workut"
    function cleanQueryText(text) {
        let cleaned = text.trim();
        // Correct typos
        cleaned = cleaned.replace(/\bworkut\b/gi, 'workout');
        cleaned = cleaned.replace(/\bexersice\b/gi, 'exercise');
        cleaned = cleaned.replace(/\bnutriton\b/gi, 'nutrition');
        cleaned = cleaned.replace(/\bprogess\b/gi, 'progress');
        
        // Capitalize first letter
        if (cleaned.length > 0) {
            cleaned = cleaned.charAt(0).toUpperCase() + cleaned.slice(1);
        }
        return cleaned;
    }

    // Detect and format raw JSON responses from the AI into readable Markdown
    function formatAIMessage(message) {
        const trimmed = message.trim();
        // Check if message looks like JSON
        if (trimmed.startsWith('{') && trimmed.endsWith('}')) {
            try {
                const data = JSON.parse(trimmed);
                // Only pretty-print if it looks like a workout/AI response object
                if (data.workout_name || data.exercises || data.plan_name || data.circuits || data.daily_calories) {
                    return formatWorkoutJson(data);
                }
            } catch (e) {
                // Not valid JSON – treat as plain text
            }
        }
        return message;
    }

    // Convert a workout JSON object into a clean Markdown string
    function formatWorkoutJson(data) {
        let md = '';

        if (data.workout_name) {
            md += `## 💪 ${data.workout_name}\n\n`;
        }
        if (data.plan_name) {
            md += `## 📋 ${data.plan_name}\n`;
            if (data.difficulty) md += `**Difficulty:** ${data.difficulty}\n\n`;
        }

        if (data.warmup && data.warmup.length) {
            md += `### 🔥 Warm-up\n`;
            data.warmup.forEach(w => {
                md += `- **${w.exercise}** — ${w.duration}s\n`;
            });
            md += '\n';
        }

        if (data.exercises && data.exercises.length) {
            md += `### 🏋️ Exercises\n`;
            data.exercises.forEach(ex => {
                md += `- **${ex.name}** — ${ex.sets} sets × ${ex.reps} reps`;
                if (ex.rest) md += ` *(rest ${ex.rest}s)*`;
                if (ex.notes) md += `\n  *💡 ${ex.notes}*`;
                md += '\n';
            });
            md += '\n';
        }

        if (data.circuits && data.circuits.length) {
            md += `### 🔄 Circuits\n`;
            data.circuits.forEach((c, i) => {
                md += `**Circuit ${i + 1}** — ${c.rounds} rounds\n`;
                c.exercises.forEach(ex => {
                    md += `- ${ex.name}: ${ex.reps} reps (rest ${ex.rest}s)\n`;
                });
            });
            md += '\n';
        }

        if (data.cooldown && data.cooldown.length) {
            md += `### 🧘 Cool-down\n`;
            data.cooldown.forEach(c => {
                md += `- **${c.exercise}** — ${c.duration}s\n`;
            });
            md += '\n';
        }

        if (data.tips && data.tips.length) {
            md += `### 💡 Tips\n`;
            data.tips.forEach(t => md += `- ${t}\n`);
            md += '\n';
        }

        if (data.motivation) {
            md += `> ✨ *${data.motivation}*\n`;
        }

        return md.trim();
    }

    function addMessage(message, isUser = false, save = true) {
        const div = document.createElement('div');
        div.className = `flex ${isUser ? 'justify-end' : 'justify-start'} mb-3`;
        
        if (isUser) {
            div.innerHTML = `
                <div class="bg-purple-900/30 border border-purple-500/35 text-purple-100 rounded-2xl rounded-tr-none p-3.5 max-w-[85%] shadow-md">
                    <p class="text-sm whitespace-pre-wrap leading-relaxed">${escapeHtml(message)}</p>
                </div>
            `;
        } else {
            // Pre-process the message to convert raw JSON to Markdown if needed
            const processedMessage = formatAIMessage(message);

            let formattedHtml = '';
            if (typeof marked !== 'undefined') {
                formattedHtml = marked.parse(processedMessage);
            } else {
                formattedHtml = `<p class="text-sm whitespace-pre-wrap leading-relaxed">${escapeHtml(processedMessage)}</p>`;
            }
            
            div.innerHTML = `
                <div class="relative group max-w-[85%] w-full">
                    <div class="bg-[#13132e] border border-gray-800 text-gray-200 rounded-2xl rounded-tl-none p-3.5 pr-10 shadow-md relative markdown-content">
                        ${formattedHtml}
                        <button onclick="copyMessageText(this)" class="absolute right-2.5 top-2.5 text-gray-500 hover:text-gray-300 opacity-0 group-hover:opacity-100 transition duration-200 p-1 rounded hover:bg-gray-800" title="Copy Response">
                            <i data-lucide="copy" class="w-3.5 h-3.5"></i>
                        </button>
                    </div>
                </div>
            `;
        }
        chatMessages.appendChild(div);
        chatMessages.scrollTop = chatMessages.scrollHeight;
        
        if (window.lucide) {
            window.lucide.createIcons();
        }

        if (save) {
            saveMessageToHistory(message, isUser);
        }
    }
    
    function escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }
    
    function getSessions() {
        try {
            return JSON.parse(localStorage.getItem('virtugym_ai_sessions') || '[]');
        } catch (e) {
            console.error('Error parsing sessions', e);
            return [];
        }
    }

    function saveSessions(sessions) {
        localStorage.setItem('virtugym_ai_sessions', JSON.stringify(sessions));
    }

    function getCurrentSessionId() {
        let currentId = localStorage.getItem('virtugym_current_session_id');
        if (!currentId) {
            currentId = 'session_' + Date.now() + '_' + Math.random().toString(36).substr(2, 9);
            localStorage.setItem('virtugym_current_session_id', currentId);
        }
        return currentId;
    }

    function getActiveSession() {
        const sessions = getSessions();
        const currentId = getCurrentSessionId();
        let active = sessions.find(s => s.id === currentId);
        if (!active) {
            active = {
                id: currentId,
                title: 'New Chat',
                messages: [],
                timestamp: new Date().toISOString()
            };
            sessions.push(active);
            saveSessions(sessions);
        }
        return active;
    }

    function updateActiveSession(updatedSession) {
        const sessions = getSessions();
        const index = sessions.findIndex(s => s.id === updatedSession.id);
        if (index !== -1) {
            sessions[index] = updatedSession;
        } else {
            sessions.push(updatedSession);
        }
        saveSessions(sessions);
    }

    function saveMessageToHistory(message, isUser) {
        const activeSession = getActiveSession();
        const messageToSave = isUser ? cleanQueryText(message) : message;

        activeSession.messages.push({
            text: messageToSave,
            isUser: isUser,
            timestamp: new Date().toISOString()
        });

        if (isUser && activeSession.title === 'New Chat') {
            activeSession.title = messageToSave.substring(0, 30) + (messageToSave.length > 30 ? '...' : '');
        }

        activeSession.timestamp = new Date().toISOString();
        updateActiveSession(activeSession);

        updateHistoryPanel();
        updateTokenCount();
    }

    function updateHistoryPanel() {
        const historyList = document.getElementById('recentQueriesList');
        if (!historyList) return;

        const sessions = getSessions();
        const activeSessions = sessions.filter(s => s.messages && s.messages.length > 0);

        activeSessions.sort((a, b) => new Date(b.timestamp) - new Date(a.timestamp));

        const recentSessions = activeSessions.slice(0, 5);

        if (recentSessions.length === 0) {
            historyList.innerHTML = `
                <div class="text-center py-6 text-xs text-gray-500">
                    No recent activities
                </div>
            `;
            return;
        }

        let html = '';
        recentSessions.forEach(session => {
            const isActive = session.id === getCurrentSessionId();
            html += `
                <div class="group w-full flex items-center justify-between px-3 py-2 hover:bg-gray-800/40 rounded-xl text-xs transition border ${isActive ? 'bg-purple-900/10 border-purple-500/30 text-purple-300 font-semibold shadow-inner shadow-purple-950/20' : 'border-transparent text-gray-400 hover:text-gray-200'}">
                    <button onclick="loadSession('${session.id}')" class="flex-1 text-left truncate min-w-0 flex items-center gap-2">
                        <i data-lucide="message-square" class="w-3.5 h-3.5 ${isActive ? 'text-purple-400' : 'text-gray-500'} shrink-0"></i>
                        <span class="truncate">${escapeHtml(session.title)}</span>
                    </button>
                    <button onclick="event.stopPropagation(); deleteSession('${session.id}')" class="opacity-0 group-hover:opacity-100 hover:text-red-400 p-0.5 rounded transition shrink-0 ml-1" title="Delete Chat">
                        <i data-lucide="trash-2" class="w-3 h-3 text-gray-500 hover:text-red-400"></i>
                    </button>
                </div>
            `;
        });

        historyList.innerHTML = html;
        if (window.lucide) {
            window.lucide.createIcons();
        }
    }

    function escapeHtmlForJsAttribute(text) {
        return text.replace(/'/g, "\\'").replace(/"/g, '&quot;');
    }

    function loadSession(sessionId) {
        localStorage.setItem('virtugym_current_session_id', sessionId);
        loadChatHistory();
        switchTab('chat');
    }

    function deleteSession(sessionId) {
        if (confirm("Are you sure you want to delete this chat session?")) {
            let sessions = getSessions();
            sessions = sessions.filter(s => s.id !== sessionId);
            saveSessions(sessions);

            if (localStorage.getItem('virtugym_current_session_id') === sessionId) {
                localStorage.removeItem('virtugym_current_session_id');
            }

            loadChatHistory();
        }
    }

    function startNewChat() {
        const newId = 'session_' + Date.now() + '_' + Math.random().toString(36).substr(2, 9);
        localStorage.setItem('virtugym_current_session_id', newId);

        window.workoutRecommendationLoaded = false;
        window.nutritionAdviceLoaded = false;
        window.progressPredictionLoaded = false;
        window.motivationLoaded = false;

        loadChatHistory();
    }

    function loadChatHistory() {
        const chatMessages = document.getElementById('chatMessages');
        chatMessages.innerHTML = '';

        const activeSession = getActiveSession();
        const history = activeSession.messages || [];

        if (history.length > 0) {
            history.forEach(item => {
                addMessage(item.text, item.isUser, false);
            });
        } else {
            chatMessages.innerHTML = `
                <div class="flex flex-col gap-4 max-w-[90%] mt-2" id="onboardingWelcomeContainer">
                    <!-- Welcome bubble -->
                    <div class="bg-[#13132e] border border-gray-800 text-gray-200 rounded-2xl rounded-tl-none p-4 pr-10 shadow-lg relative group">
                        <p class="text-sm leading-relaxed">👋 Hi! I'm VirtuCoach, your AI fitness assistant. Ask me anything about custom workouts, diet advice, form checks, or daily motivation!</p>
                        <button onclick="copyMessageText(this)" class="absolute right-2.5 top-2.5 text-gray-500 hover:text-gray-300 opacity-0 group-hover:opacity-100 transition duration-200 p-1 rounded hover:bg-gray-800" title="Copy Response">
                            <i data-lucide="copy" class="w-3.5 h-3.5"></i>
                        </button>
                    </div>

                    <!-- Onboarding Hints -->
                    <div class="flex items-start gap-2 bg-purple-950/10 p-3 rounded-xl border border-purple-500/10">
                        <i data-lucide="help-circle" class="w-4 h-4 text-purple-400 shrink-0 mt-0.5 animate-pulse"></i>
                        <div class="text-[10px] text-gray-400 leading-normal">
                            <strong>💡 Onboarding Hint:</strong> Use the workspace switcher on the right to toggle active panels, or type custom queries below.
                        </div>
                    </div>
                </div>
            `;
            if (window.lucide) {
                window.lucide.createIcons();
            }
        }

        updateHistoryPanel();
        updateTokenCount();
    }

    function clearChatHistory() {
        if (confirm("Are you sure you want to clear/delete the current chat session?")) {
            const currentId = localStorage.getItem('virtugym_current_session_id');
            if (currentId) {
                let sessions = getSessions();
                sessions = sessions.filter(s => s.id !== currentId);
                saveSessions(sessions);
                localStorage.removeItem('virtugym_current_session_id');
            }

            // Reset indicators
            window.workoutRecommendationLoaded = false;
            window.nutritionAdviceLoaded = false;
            window.progressPredictionLoaded = false;
            window.motivationLoaded = false;

            // Clean up outputs in panels
            document.getElementById('workoutRecommendation').innerHTML = `
                <button onclick="getWorkoutRecommendation()" class="w-full py-12 text-center text-purple-400 hover:text-purple-300 font-semibold">
                    ⚡ Click to generate your personalized program
                </button>
            `;
            document.getElementById('nutritionAdvice').innerHTML = `
                <button onclick="getNutritionAdvice()" class="w-full py-12 text-center text-green-400 hover:text-green-300 font-semibold">
                    🥦 Load my personalized nutrition plan
                </button>
            `;
            document.getElementById('progressPrediction').innerHTML = `
                <button onclick="getProgressPrediction()" class="w-full py-12 text-center text-cyan-400 hover:text-cyan-300 font-semibold">
                    📈 Predict my completion roadmap
                </button>
            `;
            document.getElementById('formAnalysisResult').innerHTML = '';
            document.getElementById('customPlanResult').innerHTML = '';
            document.getElementById('motivation').innerHTML = `
                <button onclick="getMotivation()" class="text-purple-400 hover:text-purple-300 font-semibold">
                    Get Daily Inspirational Quote
                </button>
            `;

            loadChatHistory();
        }
    }

    function updateTokenCount() {
        const activeSession = getActiveSession();
        const history = activeSession.messages || [];
        let totalChars = 0;
        history.forEach(item => {
            totalChars += item.text.length;
        });
        const tokenCount = Math.round(totalChars / 4);
        const percent = Math.min(100, Math.round((tokenCount / 32768) * 100));

        const countEl = document.getElementById('tokenIndicatorCount');
        const percentEl = document.getElementById('tokenIndicatorPercent');
        if (countEl && percentEl) {
            countEl.innerText = tokenCount.toLocaleString();
            percentEl.innerText = percent;
        }
    }

    async function sendMessage() {
        const message = chatInput.value.trim();
        if (!message) return;

        // Clean user input text
        const cleanedMessage = cleanQueryText(message);

        addMessage(cleanedMessage, true);
        chatInput.value = '';

        // Remove old follow-ups immediately
        document.querySelectorAll('.follow-up-row').forEach(row => row.remove());

        // Contextual typing state texts for added realism
        let thinkingMessage = "VirtuCoach is thinking";
        const lowercaseMsg = cleanedMessage.toLowerCase();
        if (lowercaseMsg.includes('workout') || lowercaseMsg.includes('routine') || lowercaseMsg.includes('exercise')) {
            thinkingMessage = "Compiling workout schedule";
        } else if (lowercaseMsg.includes('nutrition') || lowercaseMsg.includes('diet') || lowercaseMsg.includes('meal') || lowercaseMsg.includes('calor')) {
            thinkingMessage = "Analyzing diet and nutritional data";
        } else if (lowercaseMsg.includes('predict') || lowercaseMsg.includes('progress') || lowercaseMsg.includes('forecast')) {
            thinkingMessage = "Running progressive projections";
        } else if (lowercaseMsg.includes('motivation') || lowercaseMsg.includes('quote')) {
            thinkingMessage = "Searching motivational databases";
        }

        setTypingState(true, thinkingMessage);

        try {
            const response = await fetch('/ai/chat', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ message: cleanedMessage })
            });

            const data = await response.json();

            setTypingState(false);

            if (data.success) {
                addMessage(data.response);

                // Show suggested follow-up prompts
                const followups = getSuggestedPrompts(cleanedMessage);
                renderFollowUps(followups);
            } else {
                addMessage("Sorry, I encountered an error. Please try again.");
            }
        } catch (error) {
            console.error('Error:', error);
            setTypingState(false);
            addMessage("Network error. Please check your connection.");
        }
    }

    sendButton.addEventListener('click', sendMessage);
    chatInput.addEventListener('keypress', (e) => {
        if (e.key === 'Enter') sendMessage();
    });

    // Keyboard shortcut handler (Ctrl + K or Cmd + K to reset chat)
    document.addEventListener('keydown', (e) => {
        if ((e.ctrlKey || e.metaKey) && e.key === 'k') {
            e.preventDefault();
            startNewChat();
        }
    });

    document.addEventListener('DOMContentLoaded', () => {
        // Migrate old flat history to session-based history
        const oldChats = localStorage.getItem('virtugym_ai_chats');
        if (oldChats) {
            try {
                const messages = JSON.parse(oldChats);
                if (messages.length > 0) {
                    const sessionId = 'session_migrated_' + Date.now();
                    const title = messages[0].text.substring(0, 30) + (messages[0].text.length > 30 ? '...' : '');
                    const migratedSession = {
                        id: sessionId,
                        title: title,
                        messages: messages,
                        timestamp: new Date().toISOString()
                    };
                    const sessions = getSessions();
                    sessions.push(migratedSession);
                    saveSessions(sessions);
                    localStorage.setItem('virtugym_current_session_id', sessionId);
                }
            } catch (e) {
                console.error('Migration error', e);
            }
            localStorage.removeItem('virtugym_ai_chats');
        }

        loadChatHistory();
        // Initialize default tab
        switchTab('chat');
    });
    
    // Workout Recommendation
    async function getWorkoutRecommendation() {
        const container = document.getElementById('workoutRecommendation');
        container.innerHTML = '<div class="flex flex-col items-center justify-center py-12 text-purple-300 gap-2"><div class="w-6 h-6 border-2 border-purple-500 border-t-transparent rounded-full animate-spin"></div><span>Analyzing profile stats & generating program...</span></div>';
        
        try {
            const response = await fetch('/ai/recommend-workout');
            const data = await response.json();
            if (data.success && data.data) {
                displayWorkoutRecommendation(data.data);
            } else {
                container.innerHTML = '<p class="text-red-400 text-center py-6">Unable to generate recommendation.</p><button onclick="getWorkoutRecommendation()" class="text-purple-400 mt-2 block mx-auto underline">Try Again</button>';
            }
        } catch (error) {
            container.innerHTML = '<p class="text-red-400 text-center py-6">Error loading recommendation.</p>';
        }
    }
    
    function displayWorkoutRecommendation(workout) {
        let html = '<div class="space-y-4">';
        html += `<h4 class="font-extrabold text-purple-400 text-lg">${workout.workout_name || 'Your Personalized Workout'}</h4>`;
        
        if (workout.warmup) {
            html += '<div><strong class="text-gray-200 text-sm flex items-center gap-1.5"><span class="w-1.5 h-1.5 bg-purple-500 rounded-full inline-block"></span> Warm-up</strong><ul class="ml-4 mt-1.5 text-gray-400 space-y-1 text-sm">';
            workout.warmup.forEach(w => {
                html += `<li>• ${w.exercise}: ${w.duration} sec</li>`;
            });
            html += '</ul></div>';
        }
        
        if (workout.exercises) {
            html += '<div><strong class="text-gray-200 text-sm flex items-center gap-1.5"><span class="w-1.5 h-1.5 bg-blue-500 rounded-full inline-block"></span> Main Program</strong><ul class="ml-4 mt-1.5 text-gray-400 space-y-1.5 text-sm">';
            workout.exercises.forEach(ex => {
                html += `<li>• <strong>${ex.name}</strong>: ${ex.sets} sets × ${ex.reps} reps (rest ${ex.rest}s)</li>`;
            });
            html += '</ul></div>';
        }
        
        if (workout.cooldown) {
            html += '<div><strong class="text-gray-200 text-sm flex items-center gap-1.5"><span class="w-1.5 h-1.5 bg-teal-500 rounded-full inline-block"></span> Cool-down</strong><ul class="ml-4 mt-1.5 text-gray-400 space-y-1 text-sm">';
            workout.cooldown.forEach(c => {
                html += `<li>• ${c.exercise}: ${c.duration} sec</li>`;
            });
            html += '</ul></div>';
        }
        
        if (workout.motivation) {
            html += `<div class="bg-purple-950/20 border border-purple-500/10 rounded-xl p-3.5 mt-4"><p class="text-purple-300 italic text-sm">✨ "${workout.motivation}"</p></div>`;
        }
        
        html += '</div>';
        document.getElementById('workoutRecommendation').innerHTML = html;
    }
    
    // Nutrition Advice
    async function getNutritionAdvice() {
        const container = document.getElementById('nutritionAdvice');
        container.innerHTML = '<div class="flex flex-col items-center justify-center py-12 text-green-300 gap-2"><div class="w-6 h-6 border-2 border-green-500 border-t-transparent rounded-full animate-spin"></div><span>Calculating target nutritional values...</span></div>';
        
        try {
            const response = await fetch('/ai/nutrition-advice');
            const data = await response.json();
            if (data.success && data.data) {
                displayNutritionAdvice(data.data);
            } else {
                container.innerHTML = '<p class="text-red-400 text-center py-6">Unable to load advice.</p>';
            }
        } catch (error) {
            container.innerHTML = '<p class="text-red-400 text-center py-6">Error loading nutrition guide.</p>';
        }
    }
    
    function displayNutritionAdvice(nutrition) {
        let html = '<div class="space-y-4 text-gray-300">';
        
        // Caloric intake
        html += `
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div class="bg-gray-950/40 p-4 border border-gray-800 rounded-xl">
                <span class="text-xs text-gray-400 font-semibold block">Daily Target Calories</span>
                <span class="text-2xl font-black text-white mt-1 block">${nutrition.daily_calories || '2,000 - 2,200'} kcal</span>
            </div>
            <div class="bg-gray-950/40 p-4 border border-gray-800 rounded-xl">
                <span class="text-xs text-gray-400 font-semibold block">Hydration Target</span>
                <span class="text-sm font-bold text-green-400 mt-2 block">💧 ${nutrition.hydration || 'Drink 2.5 - 3.0L water daily'}</span>
            </div>
        </div>`;

        // Macros split
        html += `
        <div>
            <strong class="text-gray-200 text-sm block mb-2">Macro Nutrient Split</strong>
            <div class="grid grid-cols-3 gap-3">
                <div class="bg-gray-950/30 p-3 rounded-lg border border-gray-850 text-center">
                    <span class="text-[10px] text-gray-500 block">🥩 Protein</span>
                    <span class="text-base font-bold text-white block mt-0.5">${nutrition.protein || '150 - 180g'}</span>
                </div>
                <div class="bg-gray-950/30 p-3 rounded-lg border border-gray-850 text-center">
                    <span class="text-[10px] text-gray-500 block">🍚 Carbs</span>
                    <span class="text-base font-bold text-white block mt-0.5">${nutrition.carbs || '200 - 250g'}</span>
                </div>
                <div class="bg-gray-950/30 p-3 rounded-lg border border-gray-850 text-center">
                    <span class="text-[10px] text-gray-500 block">🥑 Fats</span>
                    <span class="text-base font-bold text-white block mt-0.5">${nutrition.fats || '50 - 60g'}</span>
                </div>
            </div>
        </div>`;

        if (nutrition.meal_ideas) {
            html += '<div><strong class="text-gray-200 text-sm block mb-1.5">🍽️ Recommended Meal Ideas:</strong><ul class="ml-4 space-y-1 text-sm text-gray-400">';
            nutrition.meal_ideas.forEach(meal => {
                html += `<li>• ${meal}</li>`;
            });
            html += '</ul></div>';
        }
        
        html += '</div>';
        document.getElementById('nutritionAdvice').innerHTML = html;
    }
    
    // Progress Prediction
    async function getProgressPrediction() {
        const container = document.getElementById('progressPrediction');
        container.innerHTML = '<div class="flex flex-col items-center justify-center py-12 text-cyan-300 gap-2"><div class="w-6 h-6 border-2 border-cyan-500 border-t-transparent rounded-full animate-spin"></div><span>Running predictive projection models...</span></div>';
        
        try {
            const response = await fetch('/ai/predict-progress');
            const data = await response.json();
            if (data.success && data.data) {
                displayProgressPrediction(data.data);
            } else {
                container.innerHTML = '<p class="text-red-400 text-center py-6">Unable to estimate completion roadmap.</p>';
            }
        } catch (error) {
            container.innerHTML = '<p class="text-red-400 text-center py-6">Error loading forecast.</p>';
        }
    }
    
    function displayProgressPrediction(prediction) {
        let html = '<div class="space-y-4 text-gray-300">';
        
        html += `
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div class="bg-gray-950/40 p-4 border border-gray-800 rounded-xl">
                <span class="text-xs text-gray-400 font-semibold block">Weeks to Goal Target</span>
                <span class="text-2xl font-black text-white mt-1 block">${prediction.weeks_to_goal || '8 - 12'} Weeks</span>
            </div>
            <div class="bg-gray-950/40 p-4 border border-gray-800 rounded-xl">
                <span class="text-xs text-gray-400 font-semibold block">Confidence Level</span>
                <div class="flex items-center gap-2 mt-2">
                    <div class="flex-1 bg-gray-800 h-2 rounded-full overflow-hidden">
                        <div class="bg-cyan-500 h-full rounded-full" style="width: ${prediction.confidence_percentage || '75'}%"></div>
                    </div>
                    <span class="text-sm font-bold text-white">${prediction.confidence_percentage || '75'}%</span>
                </div>
            </div>
        </div>`;

        html += `
        <div class="bg-gray-950/30 p-4 border border-gray-800 rounded-xl">
            <strong class="text-gray-200 text-xs block mb-1.5">Recommended Active Frequency</strong>
            <p class="text-sm text-cyan-400 font-semibold">🏋️ ${prediction.recommended_frequency || '4'} days per week</p>
        </div>`;

        if (prediction.suggestions) {
            html += '<div><strong class="text-gray-200 text-sm block mb-1.5">💡 Strategy Improvements:</strong><ul class="ml-4 space-y-1 text-sm text-gray-400">';
            prediction.suggestions.forEach(s => {
                html += `<li>• ${s}</li>`;
            });
            html += '</ul></div>';
        }
        
        if (prediction.motivation_quote) {
            html += `<div class="bg-cyan-950/20 border border-cyan-500/10 rounded-xl p-3.5"><p class="text-cyan-300 italic text-sm">✨ "${prediction.motivation_quote}"</p></div>`;
        }
        
        html += '</div>';
        document.getElementById('progressPrediction').innerHTML = html;
    }
    
    // Form Analysis
    async function analyzeForm() {
        const exercise = document.getElementById('formExercise').value;
        const description = document.getElementById('formDescription').value;
        
        if (!exercise || !description) {
            alert('Please enter both exercise name and description');
            return;
        }
        
        const resultDiv = document.getElementById('formAnalysisResult');
        resultDiv.innerHTML = '<div class="flex items-center justify-center py-6 text-yellow-300 gap-2"><div class="w-5 h-5 border-2 border-yellow-500 border-t-transparent rounded-full animate-spin"></div><span>Running diagnostics...</span></div>';
        
        try {
            const response = await fetch('/ai/analyze-form', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                },
                body: JSON.stringify({ exercise, description })
            });
            const data = await response.json();
            if (data.success && data.data) {
                displayFormAnalysis(data.data);
            } else {
                resultDiv.innerHTML = '<p class="text-red-400 text-center">Analysis diagnostics failed.</p>';
            }
        } catch (error) {
            resultDiv.innerHTML = '<p class="text-red-400 text-center">Error communicating with diagnostics API.</p>';
        }
    }
    
    function displayFormAnalysis(analysis) {
        let html = '<div class="mt-4 space-y-4 border-t border-gray-700/50 pt-5">';
        html += `<p class="text-sm"><strong class="text-gray-200">✅ Form Execution Index:</strong> <span class="font-bold text-yellow-400 text-base">${analysis.form_quality || 'Good'}</span></p>`;
        
        if (analysis.correct_points) {
            html += '<div><strong class="text-gray-200 text-xs block mb-1">👍 Movement Positives</strong><ul class="ml-4 space-y-0.5 text-sm">';
            analysis.correct_points.forEach(p => {
                html += `<li class="text-green-400">✓ ${p}</li>`;
            });
            html += '</ul></div>';
        }
        
        if (analysis.corrections) {
            html += '<div><strong class="text-gray-200 text-xs block mb-1">📝 Posture Adjustments</strong><ul class="ml-4 space-y-0.5 text-sm">';
            analysis.corrections.forEach(c => {
                html += `<li class="text-blue-400">→ ${c}</li>`;
            });
            html += '</ul></div>';
        }
        
        if (analysis.tips) {
            html += '<div><strong class="text-gray-200 text-xs block mb-1">💡 Professional Tips</strong><ul class="ml-4 space-y-0.5 text-sm text-gray-400">';
            analysis.tips.forEach(t => {
                html += `<li>• ${t}</li>`;
            });
            html += '</ul></div>';
        }
        
        if (analysis.encouragement) {
            html += `<p class="text-purple-400 italic text-sm bg-purple-950/20 p-3 rounded-xl border border-purple-500/10">✨ ${analysis.encouragement}</p>`;
        }
        html += '</div>';
        
        document.getElementById('formAnalysisResult').innerHTML = html;
    }
    
    // Custom Plan
    async function generateCustomPlan() {
        const goal = document.getElementById('planGoal').value;
        const duration = document.getElementById('planDuration').value;
        
        const resultDiv = document.getElementById('customPlanResult');
        resultDiv.innerHTML = '<div class="flex items-center justify-center py-6 text-orange-300 gap-2"><div class="w-5 h-5 border-2 border-orange-500 border-t-transparent rounded-full animate-spin"></div><span>Generating workout plan outline...</span></div>';
        
        try {
            const response = await fetch('/ai/generate-plan', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                },
                body: JSON.stringify({ goal, duration })
            });
            const data = await response.json();
            if (data.success && data.data) {
                displayCustomPlan(data.data);
            } else {
                resultDiv.innerHTML = '<p class="text-red-400 text-center">Outline compiling failed.</p>';
            }
        } catch (error) {
            resultDiv.innerHTML = '<p class="text-red-400 text-center">Error compiling outline.</p>';
        }
    }
    
    function displayCustomPlan(plan) {
        let html = '<div class="mt-4 space-y-4 border-t border-gray-700/50 pt-5">';
        html += `<h4 class="font-extrabold text-orange-400 text-base">${plan.plan_name || 'Your Custom Plan'}</h4>`;
        html += `<p class="text-xs text-gray-400">⚡ Estimated Difficulty: <span class="text-white font-semibold">${plan.difficulty || 'Intermediate'}</span></p>`;
        
        if (plan.circuits) {
            plan.circuits.forEach((circuit, idx) => {
                html += `<div class="bg-gray-950/40 p-4 border border-gray-800 rounded-xl mt-2.5">
                    <strong class="text-gray-200 text-sm block mb-1">Circuit ${idx + 1}: ${circuit.rounds} rounds</strong>
                    <ul class="ml-4 space-y-1 text-sm text-gray-400">`;
                circuit.exercises.forEach(ex => {
                    html += `<li>• ${ex.name}: ${ex.reps} reps (rest ${ex.rest}s)</li>`;
                });
                html += '</ul></div>';
            });
        }
        
        if (plan.tips) {
            html += '<div><strong class="text-gray-200 text-sm block mb-1.5">💡 Execution Guidance:</strong><ul class="ml-4 space-y-1 text-xs text-gray-400">';
            plan.tips.forEach(tip => {
                html += `<li>• ${tip}</li>`;
            });
            html += '</ul></div>';
        }
        html += '</div>';
        
        document.getElementById('customPlanResult').innerHTML = html;
    }
    
    // Motivation
    async function getMotivation() {
        const container = document.getElementById('motivation');
        container.innerHTML = '<div class="flex items-center justify-center py-6 text-red-300 gap-2"><div class="w-5 h-5 border-2 border-red-500 border-t-transparent rounded-full animate-spin"></div><span>Connecting...</span></div>';
        
        try {
            const response = await fetch('/ai/motivation');
            const data = await response.json();
            if (data.success) {
                container.innerHTML = `
                    <p class="text-red-400 italic text-lg font-semibold max-w-lg">"${data.quote}"</p>
                    <button onclick="getMotivation()" class="mt-6 px-4 py-2 bg-red-600/20 hover:bg-red-600 text-red-300 hover:text-white rounded-xl text-xs font-semibold border border-red-500/30 transition">
                        🔄 Refresh Quote
                    </button>
                `;
            } else {
                container.innerHTML = '<p class="text-red-400">Unable to load daily quote.</p>';
            }
        } catch (error) {
            container.innerHTML = '<p class="text-red-400">Error loading motivation.</p>';
        }
    }
</script>
@endsection