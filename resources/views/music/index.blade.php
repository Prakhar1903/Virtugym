@extends('layouts.app')

@section('title', 'Workout Music')

@section('content')
<div class="layout-container" style="max-width:1200px;margin:0 auto;padding-bottom: 3rem;">
    <!-- Title Section -->
    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:2rem;flex-wrap:wrap;gap:1.5rem;" class="fade-in-up">
        <div>
            <h1 style="font-size:2.2rem;font-weight:900;background:var(--vg-title-gradient);-webkit-background-clip:text;background-clip:text;color:transparent;margin-bottom:.4rem;">Workout Music 🎵</h1>
            <p style="color:var(--vg-text-muted);font-size:.9rem;">Search YouTube and play high-energy tracks while you train.</p>
        </div>
        <a href="{{ route('bookings.index') }}" style="background:var(--vg-panel);border:1px solid var(--vg-border);color:var(--vg-text-strong);padding:10px 20px;border-radius:12px;font-size:.85rem;font-weight:600;text-decoration:none;transition:all .2s;" onmouseover="this.style.background='var(--vg-accent-soft)'" onmouseout="this.style.background='var(--vg-panel)'">
            My Sessions
        </a>
    </div>

    @if(!$hasBookedSession)
        <div style="background:var(--vg-panel);border:1px solid var(--vg-border);border-radius:24px;padding:4rem 2rem;text-align:center;" class="fade-in-up delay-1">
            <div style="font-size:4rem;margin-bottom:1.5rem;filter:drop-shadow(0 0 15px var(--vg-accent-glow));">🎸</div>
            <h2 style="font-size:1.5rem;font-weight:800;color:var(--vg-text-strong);margin-bottom:1rem;">Book a session to unlock workout music</h2>
            <p style="color:var(--vg-text-muted);font-size:1rem;max-width:500px;margin:0 auto 2rem;">Music search is available for users with a confirmed trainer session to keep the energy high during workouts.</p>
            @if(Auth::user()->role === 'trainee')
                <a href="{{ route('trainee.trainers') }}" style="display:inline-block;background:var(--vg-gradient);color:#fff;padding:12px 32px;border-radius:12px;font-weight:700;text-decoration:none;box-shadow:0 8px 25px var(--vg-accent-glow);transition:all .2s;" onmouseover="this.style.transform='translateY(-2px)'" onmouseout="this.style.transform='none'">
                    Browse Trainers
                </a>
            @endif
        </div>
    @elseif(!$youtubeConfigured)
        <div style="background:rgba(245,158,11,.1);border-left:4px solid #f59e0b;padding:1.5rem;border-radius:16px;color:#fcd34d;" class="fade-in-up delay-1">
            <h2 style="font-weight:800;font-size:1.1rem;margin-bottom:.5rem;">YouTube API key needed</h2>
            <p style="font-size:.9rem;opacity:.9;">Add <code>YOUTUBE_API_KEY</code> to your <code>.env</code> file to enable music searching.</p>
        </div>
    @else
        <div style="display:grid;grid-template-columns:minmax(0, 1.25fr) minmax(0, 0.75fr);gap:2rem;" class="music-layout">
            <style>
                @media(max-width: 992px) { .music-layout { grid-template-columns: 1fr !important; } }
                .search-input {
                    background: var(--vg-sidebar) !important;
                    border: 1px solid var(--vg-border) !important;
                    color: var(--vg-text-strong) !important;
                }
                .search-input:focus {
                    border-color: var(--vg-accent) !important;
                    box-shadow: 0 0 15px var(--vg-accent-glow) !important;
                }
                .song-item {
                    background: var(--vg-panel);
                    border: 1px solid var(--vg-border);
                    transition: all .2s;
                }
                .song-item:hover {
                    border-color: var(--vg-accent);
                    background: var(--vg-accent-soft);
                    transform: translateX(5px);
                }
                .chip {
                    background: rgba(255, 255, 255, 0.05);
                    border: 1px solid rgba(255, 255, 255, 0.1);
                    color: rgba(255, 255, 255, 0.7);
                    padding: 6px 14px;
                    border-radius: 50px;
                    font-size: 0.8rem;
                    cursor: pointer;
                    transition: all 0.2s;
                    font-weight: 600;
                }
                .chip:hover {
                    background: var(--vg-accent-soft);
                    border-color: var(--vg-accent);
                    color: #fff;
                    transform: translateY(-1px);
                }
                .mood-btn {
                    background: rgba(255, 255, 255, 0.04);
                    border: 1px solid rgba(255, 255, 255, 0.08);
                    color: #fff;
                    padding: 8px 16px;
                    border-radius: 12px;
                    font-size: 0.85rem;
                    cursor: pointer;
                    transition: all 0.2s;
                    display: flex;
                    align-items: center;
                    gap: 6px;
                    font-weight: 700;
                }
                .mood-btn:hover {
                    background: linear-gradient(135deg, rgba(139, 92, 246, 0.25), rgba(236, 72, 153, 0.25));
                    border-color: var(--vg-accent);
                    transform: scale(1.03);
                }
                .trending-card {
                    background: rgba(255, 255, 255, 0.03);
                    border: 1px solid rgba(255, 255, 255, 0.06);
                    border-radius: 16px;
                    padding: 10px;
                    display: flex;
                    align-items: center;
                    gap: 12px;
                    cursor: pointer;
                    transition: all 0.2s;
                    text-align: left;
                }
                .trending-card:hover {
                    background: var(--vg-accent-soft);
                    border-color: var(--vg-accent);
                    transform: scale(1.02);
                }
                
                /* Bar visualizer animation */
                .visualizer-container {
                    display: flex;
                    align-items: flex-end;
                    gap: 3px;
                    height: 20px;
                }
                .visualizer-bar {
                    width: 3px;
                    background: var(--vg-accent);
                    border-radius: 1px;
                    animation: bounce 1s ease-in-out infinite alternate;
                }
                @keyframes bounce {
                    0% { height: 4px; }
                    100% { height: 20px; }
                }
                .visualizer-bar:nth-child(2) { animation-delay: 0.1s; background: #ec4899; }
                .visualizer-bar:nth-child(3) { animation-delay: 0.25s; }
                .visualizer-bar:nth-child(4) { animation-delay: 0.05s; background: #ec4899; }
                .visualizer-bar:nth-child(5) { animation-delay: 0.3s; }
                .visualizer-bar:nth-child(6) { animation-delay: 0.15s; background: #ec4899; }
                
                /* Fake waveform visualizer */
                .waveform-bar {
                    flex: 1;
                    height: 2px;
                    background: rgba(255, 255, 255, 0.12);
                    border-radius: 1px;
                    transition: height 0.15s ease;
                }
                .waveform-container.playing .waveform-bar {
                    background: linear-gradient(to top, #8b5cf6, #ec4899);
                }
            </style>
            
            <!-- LEFT SECTION -->
            <section style="display:flex;flex-direction:column;gap:2rem;min-width:0;" class="fade-in-up delay-1">
                <!-- Search panel -->
                <div style="background:var(--vg-panel);border:1px solid var(--vg-border);border-radius:24px;padding:2rem;">
                    <form id="musicSearchForm" style="display:flex;gap:12px;margin-bottom:1rem;">
                        <input id="musicQuery" type="search" placeholder="Search songs, artists, workout mixes..."
                               class="search-input" style="flex:1;padding:14px 20px;border-radius:14px;outline:none;font-size:.95rem;" required>
                        <button id="musicSearchButton" type="submit" style="background:var(--vg-gradient);color:#fff;padding:0 24px;border-radius:14px;font-weight:700;cursor:pointer;border:none;box-shadow:0 4px 15px var(--vg-accent-glow);transition:all .2s;">
                            Search
                        </button>
                    </form>

                    <!-- Genre Chips -->
                    <div style="display:flex;flex-wrap:wrap;gap:8px;margin-bottom:1.5rem;">
                        <button class="chip" onclick="searchGenre('Phonk')">⚡ Phonk</button>
                        <button class="chip" onclick="searchGenre('EDM Workout Mix')">🎧 EDM</button>
                        <button class="chip" onclick="searchGenre('Gym Rap Motivation')">🎤 Gym Rap</button>
                        <button class="chip" onclick="searchGenre('Workout LoFi Beats')">☕ LoFi</button>
                        <button class="chip" onclick="searchGenre('Cardio Training Mix')">🏃 Cardio</button>
                        <button class="chip" onclick="searchGenre('Focus Gym Beats')">🧠 Focus</button>
                    </div>

                    <!-- Quick Mood Buttons -->
                    <div style="margin-bottom:1rem;">
                        <p style="font-size:0.75rem;color:var(--vg-text-muted);font-weight:700;letter-spacing:0.05em;margin-bottom:0.6rem;text-transform:uppercase;">Quick Mood Selectors</p>
                        <div style="display:flex;flex-wrap:wrap;gap:10px;">
                            <button class="mood-btn" onclick="searchGenre('Intense Workout Motivation Power')">🔥 Intense</button>
                            <button class="mood-btn" onclick="searchGenre('Focus Workout Beats Chill')">🧠 Focus</button>
                            <button class="mood-btn" onclick="searchGenre('Post Workout Recovery Stretch')">😌 Recovery</button>
                            <button class="mood-btn" onclick="searchGenre('High Energy Cardio Pop')">⚡ Energy</button>
                            <button class="mood-btn" onclick="searchGenre('Late Night Gym Training Beats')">🌙 Night Workout</button>
                        </div>
                    </div>
                </div>

                <!-- Results list -->
                <div style="background:var(--vg-panel);border:1px solid var(--vg-border);border-radius:24px;padding:2rem;">
                    <h3 id="listTitle" style="font-size:1.1rem;font-weight:800;color:var(--vg-text-strong);margin-bottom:1.2rem;">Search Results</h3>
                    <p id="musicStatus" style="font-size:.8rem;color:var(--vg-text-muted);margin-bottom:1.5rem;display:flex;align-items:center;gap:6px;">
                        <i data-lucide="info" style="width:14px;height:14px;"></i>
                        Select a genre chip, mood, or enter a query above.
                    </p>
                    <div id="songResults" style="display:flex;flex-direction:column;gap:12px;">
                        <!-- Fallback empty list state -->
                        <div style="text-align:center;padding:2.5rem 1rem;color:var(--vg-text-muted);">
                            <div style="font-size:2rem;margin-bottom:0.8rem;">🔍</div>
                            No active search yet. Choose a mood or type a song name.
                        </div>
                    </div>
                </div>

                <!-- Trending Workouts & Recently Played -->
                <style>
                    @media (max-width: 768px) {
                        .grid-responsive-2col { grid-template-columns: 1fr !important; }
                    }
                </style>
                <div class="grid-responsive-2col" style="display:grid;grid-template-columns:1fr 1fr;gap:1.5rem;">
                    <!-- Trending Workouts -->
                    <div style="background:var(--vg-panel);border:1px solid var(--vg-border);border-radius:24px;padding:1.5rem;">
                        <h4 style="font-size:0.95rem;font-weight:800;color:#fff;margin-bottom:1rem;display:flex;align-items:center;gap:6px;">
                            <span>🔥</span> Trending Workouts
                        </h4>
                        <div style="display:flex;flex-direction:column;gap:10px;">
                            <button class="trending-card" onclick="playTrending('K4DyBUG242c', 'NCS: Workout Music Mix [No Copyright]', 'NoCopyrightSounds')">
                                <img src="https://img.youtube.com/vi/K4DyBUG242c/0.jpg" alt="" style="width:60px;height:40px;object-fit:cover;border-radius:8px;">
                                <div style="min-width:0;">
                                    <div style="font-size:0.82rem;font-weight:700;color:#fff;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">Cardio Boost</div>
                                    <div style="font-size:0.7rem;color:var(--vg-text-muted);">NoCopyrightSounds</div>
                                </div>
                            </button>
                            <button class="trending-card" onclick="playTrending('F82A5yDkiQ4', 'Gym Workout Beats - Till I Collapse (Inst. Edit)', 'Workout Beats')">
                                <img src="https://img.youtube.com/vi/F82A5yDkiQ4/0.jpg" alt="" style="width:60px;height:40px;object-fit:cover;border-radius:8px;">
                                <div style="min-width:0;">
                                    <div style="font-size:0.82rem;font-weight:700;color:#fff;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">Heavy Lifting</div>
                                    <div style="font-size:0.7rem;color:var(--vg-text-muted);">Workout Beats</div>
                                </div>
                            </button>
                            <button class="trending-card" onclick="playTrending('jfKfPfyJRdk', 'VirtuGym Clean Lo-Fi Workout Background Beats', 'VirtuGym Premium')">
                                <img src="https://img.youtube.com/vi/jfKfPfyJRdk/0.jpg" alt="" style="width:60px;height:40px;object-fit:cover;border-radius:8px;">
                                <div style="min-width:0;">
                                    <div style="font-size:0.82rem;font-weight:700;color:#fff;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">Focus Mode</div>
                                    <div style="font-size:0.7rem;color:var(--vg-text-muted);">VirtuGym Premium</div>
                                </div>
                            </button>
                            <button class="trending-card" onclick="playTrending('24C8r8JupYY', 'NEFFEX - Destiny [High-Energy Workout]', 'NEFFEX Music')">
                                <img src="https://img.youtube.com/vi/24C8r8JupYY/0.jpg" alt="" style="width:60px;height:40px;object-fit:cover;border-radius:8px;">
                                <div style="min-width:0;">
                                    <div style="font-size:0.82rem;font-weight:700;color:#fff;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">Yoga Flow</div>
                                    <div style="font-size:0.7rem;color:var(--vg-text-muted);">NEFFEX Music</div>
                                </div>
                            </button>
                        </div>
                    </div>

                    <!-- Recently Played -->
                    <div style="background:var(--vg-panel);border:1px solid var(--vg-border);border-radius:24px;padding:1.5rem;">
                        <h4 style="font-size:0.95rem;font-weight:800;color:#fff;margin-bottom:1rem;display:flex;align-items:center;gap:6px;">
                            <span>🕒</span> Recent Sessions
                        </h4>
                        <div id="recentSongsList" style="display:flex;flex-direction:column;gap:10px;">
                            <div style="text-align:center;padding:2rem 0;color:var(--vg-text-muted);font-size:0.78rem;">
                                No recently played tracks.
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- RIGHT SECTION: Redesigned Player Panel -->
            <aside style="position:sticky;top:2rem;height:fit-content;min-width:0;" class="fade-in-up delay-2">
                <div style="background:var(--vg-panel);border:1px solid var(--vg-border);border-radius:24px;overflow:hidden;box-shadow:0 20px 50px rgba(0,0,0,.3);position:relative;">
                    
                    <!-- Blur background effect -->
                    <div id="playerBlurBg" style="
                        position:absolute;inset:0;
                        background-size:cover;background-position:center;
                        filter:blur(30px) brightness(0.25);
                        opacity:0.6;z-index:0;
                        transition:background-image 0.5s ease;
                    "></div>

                    <div style="position:relative;z-index:1;">
                        <!-- Header with Visualizer -->
                        <div style="padding:1.5rem;border-bottom:1px solid rgba(255,255,255,0.06);display:flex;justify-content:space-between;align-items:center;">
                            <div>
                                <p style="font-size:.65rem;color:#a78bfa;text-transform:uppercase;letter-spacing:.1em;margin:0;font-weight:800;">Now Playing</p>
                            </div>
                            <!-- Mini equalizer visualizer -->
                            <div id="headerVisualizer" class="visualizer-container" style="opacity:0;transition:opacity 0.3s;">
                                <div class="visualizer-bar"></div>
                                <div class="visualizer-bar"></div>
                                <div class="visualizer-bar"></div>
                                <div class="visualizer-bar"></div>
                                <div class="visualizer-bar"></div>
                                <div class="visualizer-bar"></div>
                            </div>
                        </div>

                        <!-- Active Player Content -->
                        <div id="activePlayerContent" style="display:none;padding:2rem 1.5rem;text-align:center;">
                            <!-- Large Album Art Thumbnail -->
                            <div style="position:relative;width:80%;aspect-ratio:1/1;margin:0 auto 1.5rem;border-radius:18px;overflow:hidden;box-shadow:0 15px 35px rgba(0,0,0,0.5);border:1px solid rgba(255,255,255,0.1);">
                                <img id="playerTrackArt" src="" alt="" style="width:100%;height:100%;object-fit:cover;">
                                <div style="position:absolute;inset:0;background:linear-gradient(to top, rgba(0,0,0,0.4), transparent);"></div>
                            </div>

                            <h2 id="nowPlayingTitle" style="font-weight:900;font-size:1.15rem;line-height:1.3;color:#fff;margin:0 0 0.25rem;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">Choose a track</h2>
                            <p id="nowPlayingChannel" style="font-size:.8rem;color:var(--vg-text-muted);margin:0 0 1.5rem;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;"></p>

                            <!-- Fake interactive waveform visualizer -->
                            <div id="playerWaveform" class="waveform-container" style="display:flex;align-items:center;gap:3px;height:36px;margin-bottom:1.5rem;">
                                <!-- Waveform bars created dynamically -->
                            </div>

                            <!-- Duration and play state details -->
                            <div style="display:flex;justify-content:space-between;font-size:0.72rem;color:var(--vg-text-muted);margin-bottom:1.5rem;padding:0 8px;">
                                <span id="playerCurrentTime">00:00</span>
                                <span style="display:flex;align-items:center;gap:4px;color:#a78bfa;font-weight:700;">
                                    <span id="playerPlaybackStatus">PAUSED</span>
                                </span>
                                <span id="playerTotalTime">03:45</span>
                            </div>

                            <!-- Playback controls -->
                            <div style="display:flex;align-items:center;justify-content:center;gap:1.5rem;margin-bottom:1.5rem;">
                                <button onclick="window.GymPlayer.setVolume(window.GymPlayer.getVolume() - 10)" style="background:rgba(255,255,255,0.06);color:#fff;border:none;width:36px;height:36px;border-radius:50%;display:flex;align-items:center;justify-content:center;cursor:pointer;">
                                    <i data-lucide="volume-1" style="width:16px;height:16px;"></i>
                                </button>
                                <button id="playerMainPlayBtn" onclick="window.GymPlayer.toggle()" style="background:linear-gradient(135deg,#8b5cf6,#ec4899);color:#fff;border:none;width:56px;height:56px;border-radius:50%;display:flex;align-items:center;justify-content:center;cursor:pointer;box-shadow:0 8px 20px rgba(139,92,246,0.4);transition:transform 0.2s;">
                                    <i data-lucide="play" style="width:24px;height:24px;fill:currentColor;"></i>
                                </button>
                                <button onclick="window.GymPlayer.setVolume(window.GymPlayer.getVolume() + 10)" style="background:rgba(255,255,255,0.06);color:#fff;border:none;width:36px;height:36px;border-radius:50%;display:flex;align-items:center;justify-content:center;cursor:pointer;">
                                    <i data-lucide="volume-2" style="width:16px;height:16px;"></i>
                                </button>
                            </div>

                            <!-- Volume slider -->
                            <div style="display:flex;align-items:center;gap:8px;padding:0 12px;margin-bottom:2rem;">
                                <i data-lucide="volume-1" style="width:14px;height:14px;color:var(--vg-text-muted);"></i>
                                <input id="playerVolumeSlider" type="range" min="0" max="100" style="flex:1;height:4px;border-radius:2px;outline:none;background:rgba(255,255,255,0.1);accent-color:#8b5cf6;cursor:pointer;">
                                <i data-lucide="volume-2" style="width:14px;height:14px;color:var(--vg-text-muted);"></i>
                            </div>

                            <!-- Workout sync suggestions section -->
                            <div style="border-top:1px solid rgba(255,255,255,0.06);padding-top:1.5rem;text-align:left;">
                                <p style="font-size:0.7rem;color:#a78bfa;font-weight:800;letter-spacing:0.05em;margin-bottom:0.8rem;text-transform:uppercase;">💪 Suggested Workout Sync</p>
                                <div style="display:flex;flex-direction:column;gap:8px;">
                                    <button onclick="playWorkoutSync('Chest Day')" style="display:flex;justify-content:space-between;align-items:center;width:100%;background:rgba(255,255,255,0.03);border:1px solid rgba(255,255,255,0.06);color:#fff;padding:8px 12px;border-radius:10px;font-size:0.8rem;font-weight:700;cursor:pointer;transition:all 0.2s;" onmouseover="this.style.borderColor='var(--vg-accent)'" onmouseout="this.style.borderColor='rgba(255,255,255,0.06)'">
                                        <span>Chest Day</span>
                                        <span style="font-size:0.7rem;color:var(--vg-text-muted);display:flex;align-items:center;gap:4px;">NEFFEX - Crown <i data-lucide="play" style="width:10px;height:10px;"></i></span>
                                    </button>
                                    <button onclick="playWorkoutSync('Heavy Strength')" style="display:flex;justify-content:space-between;align-items:center;width:100%;background:rgba(255,255,255,0.03);border:1px solid rgba(255,255,255,0.06);color:#fff;padding:8px 12px;border-radius:10px;font-size:0.8rem;font-weight:700;cursor:pointer;transition:all 0.2s;" onmouseover="this.style.borderColor='var(--vg-accent)'" onmouseout="this.style.borderColor='rgba(255,255,255,0.06)'">
                                        <span>Heavy Strength</span>
                                        <span style="font-size:0.7rem;color:var(--vg-text-muted);display:flex;align-items:center;gap:4px;">NEFFEX - Fight Back <i data-lucide="play" style="width:10px;height:10px;"></i></span>
                                    </button>
                                    <button onclick="playWorkoutSync('Cardio Session')" style="display:flex;justify-content:space-between;align-items:center;width:100%;background:rgba(255,255,255,0.03);border:1px solid rgba(255,255,255,0.06);color:#fff;padding:8px 12px;border-radius:10px;font-size:0.8rem;font-weight:700;cursor:pointer;transition:all 0.2s;" onmouseover="this.style.borderColor='var(--vg-accent)'" onmouseout="this.style.borderColor='rgba(255,255,255,0.06)'">
                                        <span>Cardio Session</span>
                                        <span style="font-size:0.7rem;color:var(--vg-text-muted);display:flex;align-items:center;gap:4px;">NEFFEX - Cold <i data-lucide="play" style="width:10px;height:10px;"></i></span>
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Empty State illustration -->
                        <div id="playerEmptyState" style="padding:4rem 2rem;text-align:center;">
                            <div style="width:100px;height:100px;background:rgba(139,92,246,0.1);border:1px dashed rgba(139,92,246,0.4);border-radius:50%;display:flex;align-items:center;justify-content:center;margin:0 auto 1.5rem;position:relative;">
                                <span style="font-size:3rem;animation:pulse 2s infinite;">🎵</span>
                                <span style="position:absolute;top:5px;right:5px;font-size:1.2rem;">✨</span>
                            </div>
                            <h3 style="font-size:1.1rem;font-weight:800;color:#fff;margin-bottom:0.5rem;">Ready to Pump?</h3>
                            <p style="font-size:0.82rem;color:var(--vg-text-muted);line-height:1.4;margin:0 0 1.5rem;">Search and load your workout soundtrack to activate premium playback features.</p>
                            <button onclick="searchGenre('Workout Mix Motivation')" style="background:var(--vg-gradient);color:#fff;border:none;padding:10px 24px;border-radius:12px;font-size:0.8rem;font-weight:700;cursor:pointer;box-shadow:0 4px 15px var(--vg-accent-glow);">
                                Load Energy Track
                            </button>
                        </div>

                        <!-- Footer notes -->
                        <div style="padding:1.2rem;font-size:.75rem;color:var(--vg-text-muted);line-height:1.5;background:rgba(0,0,0,.2);border-top:1px solid rgba(255,255,255,0.03);">
                            <div style="display:flex;gap:8px;align-items:flex-start;">
                                <i data-lucide="youtube" style="width:16px;height:16px;color:#ef4444;flex-shrink:0;"></i>
                                <span>Global persistent engine continues playing audio across pages seamlessly.</span>
                            </div>
                        </div>
                    </div>
                </div>
            </aside>
        </div>
    @endif
</div>

@if($hasBookedSession && $youtubeConfigured)
<script>
    const form = document.getElementById('musicSearchForm');
    const query = document.getElementById('musicQuery');
    const searchButton = document.getElementById('musicSearchButton');
    const results = document.getElementById('songResults');
    const statusText = document.getElementById('musicStatus');
    const listTitle = document.getElementById('listTitle');
    const playerBlurBg = document.getElementById('playerBlurBg');
    
    // Player UI elements
    const activePlayerContent = document.getElementById('activePlayerContent');
    const playerEmptyState = document.getElementById('playerEmptyState');
    const nowTitle = document.getElementById('nowPlayingTitle');
    const nowChannel = document.getElementById('nowPlayingChannel');
    const playerTrackArt = document.getElementById('playerTrackArt');
    const playerPlaybackStatus = document.getElementById('playerPlaybackStatus');
    const playerMainPlayBtn = document.getElementById('playerMainPlayBtn');
    const playerVolumeSlider = document.getElementById('playerVolumeSlider');
    const headerVisualizer = document.getElementById('headerVisualizer');
    
    let activeSearch = 0;

    // Build the fake waveform bars
    const waveform = document.getElementById('playerWaveform');
    if (waveform) {
        waveform.innerHTML = '';
        for (let i = 0; i < 30; i++) {
            const bar = document.createElement('div');
            bar.className = 'waveform-bar';
            // Set random heights to look like a waveform
            const h = Math.floor(Math.random() * 26) + 4;
            bar.style.height = h + 'px';
            waveform.appendChild(bar);
        }
    }

    // Function to animate the waveform slightly when playing
    let waveformInterval = null;
    function startWaveformAnimation() {
        if (waveformInterval) clearInterval(waveformInterval);
        waveform.classList.add('playing');
        waveformInterval = setInterval(() => {
            const bars = waveform.querySelectorAll('.waveform-bar');
            bars.forEach(bar => {
                const baseH = parseInt(bar.style.height);
                // Wiggle the height
                const delta = Math.floor(Math.random() * 7) - 3;
                const nextH = Math.max(4, Math.min(32, baseH + delta));
                bar.style.height = nextH + 'px';
            });
        }, 150);
    }

    function stopWaveformAnimation() {
        if (waveformInterval) {
            clearInterval(waveformInterval);
            waveformInterval = null;
        }
        waveform.classList.remove('playing');
    }

    function escapeHtml(value) {
        const node = document.createElement('div');
        node.textContent = value || '';
        return node.innerHTML;
    }

    function playSong(song) {
        if (window.GymPlayer && typeof window.GymPlayer.play === 'function') {
            window.GymPlayer.play(song);
        }
    }

    function syncPlayerPanel(song, playing) {
        if (!song) {
            playerEmptyState.style.display = 'block';
            activePlayerContent.style.display = 'none';
            headerVisualizer.style.opacity = '0';
            playerBlurBg.style.backgroundImage = 'none';
            return;
        }

        playerEmptyState.style.display = 'none';
        activePlayerContent.style.display = 'block';
        
        nowTitle.textContent = song.title;
        nowChannel.textContent = song.channel;
        
        const thumbUrl = song.thumbnail || `https://img.youtube.com/vi/${song.video_id}/0.jpg`;
        playerTrackArt.src = thumbUrl;
        playerBlurBg.style.backgroundImage = `url('${thumbUrl}')`;
        
        // Update play button state and visualizers
        if (playing) {
            playerPlaybackStatus.textContent = 'PLAYING';
            playerPlaybackStatus.style.color = '#34d399';
            playerMainPlayBtn.innerHTML = `<i data-lucide="pause" style="width:24px;height:24px;fill:currentColor;"></i>`;
            headerVisualizer.style.opacity = '1';
            startWaveformAnimation();
        } else {
            playerPlaybackStatus.textContent = 'PAUSED';
            playerPlaybackStatus.style.color = '#f59e0b';
            playerMainPlayBtn.innerHTML = `<i data-lucide="play" style="width:24px;height:24px;fill:currentColor;"></i>`;
            headerVisualizer.style.opacity = '0';
            stopWaveformAnimation();
        }
        
        if (typeof lucide !== 'undefined') lucide.createIcons();
    }

    // Sync volume slider
    if (playerVolumeSlider) {
        if (window.GymPlayer) playerVolumeSlider.value = window.GymPlayer.getVolume();
        playerVolumeSlider.addEventListener('input', (e) => {
            if (window.GymPlayer) window.GymPlayer.setVolume(e.target.value);
        });
    }

    function renderSongs(songs, isRecommendations = false) {
        results.innerHTML = '';
        listTitle.textContent = isRecommendations ? '🔥 Recommended Tracks' : '🔍 Search Results';

        if (!songs.length) {
            statusText.innerHTML = '<i data-lucide="alert-circle" style="width:14px;height:14px;"></i> No songs found.';
            if (typeof lucide !== 'undefined') lucide.createIcons();
            return;
        }

        statusText.innerHTML = `<i data-lucide="check-circle" style="width:14px;height:14px;"></i> ${songs.length} tracks loaded`;
        if (typeof lucide !== 'undefined') lucide.createIcons();

        songs.forEach((song) => {
            const button = document.createElement('button');
            button.type = 'button';
            button.className = 'song-item';
            button.style.width = '100%';
            button.style.display = 'flex';
            button.style.alignItems = 'center';
            button.style.gap = '1rem';
            button.style.padding = '12px';
            button.style.borderRadius = '16px';
            button.style.cursor = 'pointer';
            button.style.textAlign = 'left';
            button.style.color = 'var(--vg-text-strong)';
            
            button.innerHTML = `
                <img src="${song.thumbnail || ''}" alt="" style="width:80px;height:50px;object-fit:cover;border-radius:8px;background:rgba(255,255,255,.05);">
                <div style="flex:1;min-width:0;">
                    <span style="display:block;font-weight:700;font-size:.9rem;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">${escapeHtml(song.title)}</span>
                    <span style="display:block;font-size:.75rem;color:var(--vg-text-muted);margin-top:2px;">${escapeHtml(song.channel)}</span>
                </div>
                <div style="background:var(--vg-accent-soft);color:var(--vg-accent);width:32px;height:32px;border-radius:50%;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                    <i data-lucide="play" style="width:14px;height:14px;fill:currentColor;"></i>
                </div>
            `;
            button.addEventListener('click', () => playSong(song));
            results.appendChild(button);
        });
        if (typeof lucide !== 'undefined') lucide.createIcons();
    }

    async function executeSearch(term) {
        if (term.length < 2) return;

        const searchId = ++activeSearch;
        statusText.innerHTML = '<i data-lucide="loader" class="animate-spin" style="width:14px;height:14px;"></i> Querying YouTube...';
        if (typeof lucide !== 'undefined') lucide.createIcons();
        results.innerHTML = '';
        searchButton.disabled = true;
        searchButton.style.opacity = '0.7';
        searchButton.textContent = 'Searching...';

        try {
            const response = await fetch(`{{ route('music.search') }}?q=${encodeURIComponent(term)}`, {
                headers: { 'Accept': 'application/json' }
            });
            const data = await response.json().catch(() => ({}));

            if (searchId !== activeSearch) return;

            if (!response.ok) {
                throw new Error(data.message || 'Search failed.');
            }

            renderSongs(data.songs || [], false);
        } catch (error) {
            if (searchId !== activeSearch) return;
            statusText.innerHTML = `<i data-lucide="alert-triangle" style="width:14px;height:14px;"></i> ${error.message}`;
            if (typeof lucide !== 'undefined') lucide.createIcons();
        } finally {
            if (searchId === activeSearch) {
                searchButton.disabled = false;
                searchButton.style.opacity = '1';
                searchButton.textContent = 'Search';
            }
        }
    }

    form.addEventListener('submit', (event) => {
        event.preventDefault();
        executeSearch(query.value.trim());
    });

    // Genre Chip searches
    window.searchGenre = function(genre) {
        query.value = genre;
        executeSearch(genre);
    };

    // play trending track
    window.playTrending = function(videoId, title, channel) {
        const song = {
            video_id: videoId,
            title: title,
            channel: channel,
            thumbnail: `https://img.youtube.com/vi/${videoId}/0.jpg`
        };
        playSong(song);
    };

    // Playback sync suggestions
    window.playWorkoutSync = function(type) {
        let videoId, title, channel;
        if (type === 'Chest Day') {
            videoId = '4bS1W1nE_U0';
            title = 'NEFFEX - Crown [Power Gym Beat]';
            channel = 'NEFFEX Music';
        } else if (type === 'Heavy Strength') {
            videoId = '2S24-y0Ij3Y';
            title = 'NEFFEX - Fight Back [Workout Motivation]';
            channel = 'NEFFEX Music';
        } else { // Cardio
            videoId = 'B3_m9z2p4J0';
            title = 'NEFFEX - Cold [Aggressive Training Beat]';
            channel = 'NEFFEX Music';
        }
        
        playTrending(videoId, title, channel);
    };

    // Render Recent Played list from local storage
    function renderRecentPlayed() {
        const listDiv = document.getElementById('recentSongsList');
        if (!listDiv) return;

        try {
            const history = JSON.parse(localStorage.getItem('virtugym-music-history') || '[]');
            if (!history.length) {
                listDiv.innerHTML = `
                    <div style="text-align:center;padding:2rem 0;color:var(--vg-text-muted);font-size:0.78rem;">
                        No recently played tracks.
                    </div>
                `;
                return;
            }

            listDiv.innerHTML = '';
            // Render up to 4 recent tracks
            history.slice(0, 4).forEach(song => {
                const btn = document.createElement('button');
                btn.className = 'trending-card';
                btn.style.width = '100%';
                
                const thumb = song.thumbnail || `https://img.youtube.com/vi/${song.video_id}/0.jpg`;
                btn.innerHTML = `
                    <img src="${thumb}" alt="" style="width:48px;height:32px;object-fit:cover;border-radius:6px;">
                    <div style="min-width:0;flex:1;">
                        <div style="font-size:0.78rem;font-weight:700;color:#fff;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">${escapeHtml(song.title)}</div>
                        <div style="font-size:0.68rem;color:var(--vg-text-muted);">${escapeHtml(song.channel)}</div>
                    </div>
                `;
                btn.addEventListener('click', () => playSong(song));
                listDiv.appendChild(btn);
            });
        } catch(e) {
            console.error('Failed to load recent history', e);
        }
    }

    // Load recommendations initially (NEFFEX fallback tracks)
    async function loadInitialRecommendations() {
        try {
            const response = await fetch(`{{ route('music.search') }}?q=NEFFEX`, {
                headers: { 'Accept': 'application/json' }
            });
            const data = await response.json();
            renderSongs((data.songs || []).slice(0, 5), true);
        } catch(e) {
            // Load custom local fallbacks if offline/API fails
            const fallbacks = [
                { video_id: '83R59AnBY90', title: 'NEFFEX - Grateful [Clean Gym Motivation]', channel: 'NEFFEX Music', thumbnail: 'https://img.youtube.com/vi/83R59AnBY90/0.jpg' },
                { video_id: '2S24-y0Ij3Y', title: 'NEFFEX - Fight Back [Workout Motivation]', channel: 'NEFFEX Music', thumbnail: 'https://img.youtube.com/vi/2S24-y0Ij3Y/0.jpg' },
                { video_id: 'B3_m9z2p4J0', title: 'NEFFEX - Cold [Aggressive Training Beat]', channel: 'NEFFEX Music', thumbnail: 'https://img.youtube.com/vi/B3_m9z2p4J0/0.jpg' }
            ];
            renderSongs(fallbacks, true);
        }
    }

    // Set up cross-tab/frame events for direct UI sync
    window.addEventListener('gym-song-changed', (e) => {
        syncPlayerPanel(e.detail, window.GymPlayer ? window.GymPlayer.isPlaying() : false);
        renderRecentPlayed();
    });

    window.addEventListener('gym-play-state-changed', (e) => {
        if (window.GymPlayer) {
            syncPlayerPanel(window.GymPlayer.getActiveSong(), e.detail.playing);
        }
    });

    window.addEventListener('gym-history-updated', () => {
        renderRecentPlayed();
    });

    window.addEventListener('gym-player-time-update', (e) => {
        const cur = e.detail.currentTime;
        const dur = e.detail.duration;
        
        const curMin = Math.floor(cur / 60).toString().padStart(2, '0');
        const curSec = Math.floor(cur % 60).toString().padStart(2, '0');
        const currentField = document.getElementById('playerCurrentTime');
        if (currentField) currentField.textContent = `${curMin}:${curSec}`;
        
        if (dur > 0) {
            const durMin = Math.floor(dur / 60).toString().padStart(2, '0');
            const durSec = Math.floor(dur % 60).toString().padStart(2, '0');
            const totalField = document.getElementById('playerTotalTime');
            if (totalField) totalField.textContent = `${durMin}:${durSec}`;
            
            // Sync waveform bars progress color
            const bars = document.querySelectorAll('.waveform-bar');
            const activeBarCount = Math.floor((cur / dur) * bars.length);
            bars.forEach((bar, idx) => {
                if (idx < activeBarCount) {
                    bar.style.background = 'linear-gradient(to top, #8b5cf6, #ec4899)';
                } else {
                    bar.style.background = 'rgba(255, 255, 255, 0.12)';
                }
            });
        }
    });

    // Initialize state
    window.addEventListener('DOMContentLoaded', () => {
        if (typeof lucide !== 'undefined') lucide.createIcons();
        
        // Initial render of recent played
        renderRecentPlayed();

        // Check if there is an active song already playing
        if (window.GymPlayer) {
            const active = window.GymPlayer.getActiveSong();
            const playing = window.GymPlayer.isPlaying();
            syncPlayerPanel(active, playing);
            
            if (playing) {
                // If already playing, sync and show results based on it
                searchGenre('Workout Mix Motivation');
            } else {
                loadInitialRecommendations();
            }
        } else {
            loadInitialRecommendations();
        }
    });
</script>
@endif
@endsection
