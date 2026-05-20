@extends('layouts.app')

@section('title', 'Manage Availability')

@section('content')
<div style="max-width:1280px;margin:0 auto;padding-bottom: 4rem;">

    {{-- Header --}}
    <div style="margin-bottom:2.5rem;" class="fade-in-up">
        <h1 style="font-size:2.4rem;font-weight:900;background:linear-gradient(135deg,#fff 20%,#c4b5fd 60%,#f9a8d4 90%);-webkit-background-clip:text;background-clip:text;color:transparent;margin-bottom:.5rem;">
            Manage Your Schedule ⏰
        </h1>
        <p style="color:rgba(255,255,255,.4);font-size:1rem;font-weight:500;">Configure your available slots and keep your trainees informed.</p>
    </div>

    {{-- Top Row: Stats & Add Slot --}}
    <div style="display:grid;grid-template-columns:1fr 1.3fr;gap:2rem;margin-bottom:3rem;">
        
        {{-- Booking Summary --}}
        <div class="fade-in-up delay-1" style="background:rgba(255,255,255,.03);border:1px solid rgba(139,92,246,.15);border-radius:28px;padding:2rem;display:flex;flex-direction:column;justify-content:flex-start;position:relative;overflow:hidden;">
            <div style="position:absolute;top:-20px;right:-20px;width:120px;height:120px;background:rgba(139,92,246,.05);border-radius:50%;filter:blur(40px);"></div>
            <h2 style="font-size:1.1rem;font-weight:800;color:#fff;margin-bottom:1.5rem;display:flex;align-items:center;gap:10px;padding-top:0.5rem;">
                <i data-lucide="bar-chart-3" style="color:var(--vg-accent);"></i> This Week's Summary
            </h2>
            
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:1rem;margin-bottom:1.5rem;">
                <div style="background:rgba(255,255,255,0.02);padding:1rem;border-radius:16px;border:1px solid rgba(255,255,255,0.05);">
                    <p style="font-size:.65rem;color:rgba(255,255,255,.4);font-weight:700;text-transform:uppercase;margin-bottom:4px;">Earnings</p>
                    <p style="font-size:1.4rem;font-weight:900;color:#10b981;">₹{{ number_format($weeklyEarnings ?? 0) }}</p>
                </div>
                <div style="background:rgba(255,255,255,0.02);padding:1rem;border-radius:16px;border:1px solid rgba(255,255,255,0.05);">
                    <p style="font-size:.65rem;color:rgba(255,255,255,.4);font-weight:700;text-transform:uppercase;margin-bottom:4px;">Cancelled</p>
                    <p style="font-size:1.4rem;font-weight:900;color:#f43f5e;">{{ $cancelledBookingsCount ?? 0 }}</p>
                </div>
                <div style="background:rgba(255,255,255,0.02);padding:1rem;border-radius:16px;border:1px solid rgba(255,255,255,0.05);">
                    <p style="font-size:.65rem;color:rgba(255,255,255,.4);font-weight:700;text-transform:uppercase;margin-bottom:4px;">Filled</p>
                    <p style="font-size:1.4rem;font-weight:900;color:#fff;">{{ $weeklyBookingsCount ?? 0 }}</p>
                </div>
                <div style="background:rgba(255,255,255,0.02);padding:1rem;border-radius:16px;border:1px solid rgba(255,255,255,0.05);">
                    <p style="font-size:.65rem;color:rgba(255,255,255,.4);font-weight:700;text-transform:uppercase;margin-bottom:4px;">Capacity</p>
                    <p style="font-size:1.4rem;font-weight:900;color:var(--vg-accent);">{{ $totalSlotsCount ?? 0 }}</p>
                </div>
            </div>

            {{-- Today's Brief --}}
            <div style="margin-bottom:1.5rem;">
                <h3 style="font-size:.8rem;font-weight:700;color:rgba(255,255,255,.5);text-transform:uppercase;margin-bottom:10px;display:flex;align-items:center;gap:6px;">
                    <i data-lucide="clock" style="width:14px;height:14px;"></i> Today's Schedule
                </h3>
                @if(isset($todaysBookings) && $todaysBookings->count() > 0)
                    <div style="display:flex;flex-direction:column;gap:8px;">
                        @foreach($todaysBookings as $booking)
                            <div style="background:rgba(255,255,255,0.03);padding:8px 12px;border-radius:12px;display:flex;justify-content:space-between;align-items:center;border-left:3px solid #10b981;">
                                <span style="font-size:.8rem;color:#fff;font-weight:600;">{{ $booking->trainee->name ?? 'Trainee' }}</span>
                                <span style="font-size:.75rem;color:rgba(255,255,255,0.4);">{{ \Carbon\Carbon::parse($booking->session_date)->format('h:i A') }}</span>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p style="font-size:.8rem;color:rgba(255,255,255,.2);font-style:italic;">No bookings for today yet.</p>
                @endif
            </div>

            {{-- Schedule Insights / Tips --}}
            <div style="background:rgba(139, 92, 246, 0.03);border:1px dashed rgba(139, 92, 246, 0.15);border-radius:18px;padding:1.1rem;margin-bottom:1.5rem;box-shadow:inset 0 1px 1px rgba(255,255,255,0.02);">
                <h4 style="font-size:.75rem;font-weight:700;color:var(--vg-accent);margin:0 0 6px 0;display:flex;align-items:center;gap:6px;text-transform:uppercase;letter-spacing:.05em;">
                    <i data-lucide="sparkles" style="width:12px;height:12px;color:#fbbf24;"></i> Schedule Insights
                </h4>
                <p style="font-size:.7rem;color:rgba(255,255,255,0.45);line-height:1.45;margin:0;">
                    🌅 Morning slots (07:00 AM - 10:00 AM) are currently the most popular for trainees. Keep at least 5 slots open weekly during these peak times to maximize booking rates!
                </p>
            </div>
            
            <div style="margin-top:auto;padding-top:1.5rem;border-top:1px solid rgba(255,255,255,.05);padding-bottom:1rem;">
                <div style="display:flex;justify-content:space-between;font-size:.8rem;color:rgba(255,255,255,.5);margin-bottom:8px;">
                    <span>Utilization Rate</span>
                    <span style="font-weight:700;color:#fff;">{{ $totalSlotsCount > 0 ? min(100, round(($weeklyBookingsCount / $totalSlotsCount) * 100)) : 0 }}%</span>
                </div>
                <div style="width:100%;height:6px;background:rgba(255,255,255,.05);border-radius:3px;overflow:hidden;">
                    <div style="width:{{ $totalSlotsCount > 0 ? min(100, ($weeklyBookingsCount / $totalSlotsCount) * 100) : 0 }}%;height:100%;background:linear-gradient(90deg,var(--vg-accent),#10b981);"></div>
                </div>
            </div>
        </div>

        {{-- Add Time Slot Form --}}
        <div class="fade-in-up delay-2" style="background:rgba(255,255,255,.02);border:1px solid rgba(255,255,255,.06);border-radius:28px;padding:2rem;">
            <h2 style="font-size:1.1rem;font-weight:800;color:#fff;margin-bottom:1.5rem;display:flex;align-items:center;gap:10px;">
                <i data-lucide="plus-circle" style="color:#6ee7b7;"></i> Add New Slots
            </h2>
            
            <form method="POST" action="{{ route('trainer.availability.store') }}" onsubmit="return validateForm()">
                @csrf
                
                {{-- Multi-Day Selection --}}
                <div style="margin-bottom:1.5rem;">
                    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:10px;">
                        <label style="font-size:.75rem;color:rgba(255,255,255,.4);font-weight:700;text-transform:uppercase;letter-spacing:.05em;margin:0;">Select Days <span style="color:#f43f5e;">*</span></label>
                        <button type="button" onclick="toggleAllDays()" id="toggle-all-btn" style="background:none;border:none;color:var(--vg-accent);font-size:.75rem;font-weight:700;cursor:pointer;padding:0;transition:color .2s;" onmouseover="this.style.color='#fff'" onmouseout="this.style.color='var(--vg-accent)'">Select All</button>
                    </div>
                    <div style="display:flex;gap:8px;flex-wrap:wrap;" id="day-selector">
                        @foreach(['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'] as $idx => $day)
                            <label style="cursor:pointer;">
                                <input type="checkbox" name="day_of_week[]" value="{{ $idx }}" style="display:none;" class="day-checkbox" id="day-{{ $idx }}">
                                <div class="day-pill" style="padding:8px 16px;border-radius:12px;background:rgba(255,255,255,.03);border:1px solid rgba(255,255,255,.1);color:rgba(255,255,255,.5);font-size:.85rem;font-weight:600;transition:all .2s;">
                                    {{ $day }}
                                </div>
                            </label>
                        @endforeach
                    </div>
                </div>

                {{-- Quick Slot Generator --}}
                <div style="margin-bottom:1.8rem;background:rgba(255,255,255,0.01);border:1px solid rgba(255,255,255,0.04);border-radius:20px;padding:1.2rem;">
                    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:12px;">
                        <label style="font-size:.75rem;color:rgba(255,255,255,.4);font-weight:700;text-transform:uppercase;letter-spacing:.05em;margin:0;display:flex;align-items:center;gap:6px;">
                            <i data-lucide="zap" style="width:14px;height:14px;color:#fbbf24;"></i> Quick Time Presets
                        </label>
                        
                        {{-- Duration selector --}}
                        <div style="display:flex;background:rgba(0,0,0,0.3);border:1px solid rgba(255,255,255,0.08);border-radius:8px;padding:2px;">
                            <button type="button" onclick="setPresetDuration(30)" class="dur-pill" id="dur-30" style="background:none;border:none;color:rgba(255,255,255,0.4);font-size:.7rem;font-weight:700;padding:4px 8px;border-radius:6px;cursor:pointer;transition:all .2s;">30m</button>
                            <button type="button" onclick="setPresetDuration(60)" class="dur-pill active" id="dur-60" style="background:var(--vg-accent);border:none;color:#fff;font-size:.7rem;font-weight:700;padding:4px 8px;border-radius:6px;cursor:pointer;transition:all .2s;box-shadow:0 2px 6px var(--vg-accent-glow);">60m</button>
                            <button type="button" onclick="setPresetDuration(90)" class="dur-pill" id="dur-90" style="background:none;border:none;color:rgba(255,255,255,0.4);font-size:.7rem;font-weight:700;padding:4px 8px;border-radius:6px;cursor:pointer;transition:all .2s;">90m</button>
                        </div>
                    </div>

                    {{-- Period tabs --}}
                    <div style="display:flex;gap:6px;margin-bottom:12px;border-bottom:1px solid rgba(255,255,255,0.03);padding-bottom:8px;">
                        <button type="button" onclick="showPresetTab('morning')" id="tab-morning" class="preset-tab active" style="background:none;border:none;color:#fff;font-size:.75rem;font-weight:700;padding:4px 8px;cursor:pointer;position:relative;">🌅 Morning</button>
                        <button type="button" onclick="showPresetTab('afternoon')" id="tab-afternoon" class="preset-tab" style="background:none;border:none;color:rgba(255,255,255,0.4);font-size:.75rem;font-weight:700;padding:4px 8px;cursor:pointer;position:relative;">☀️ Afternoon</button>
                        <button type="button" onclick="showPresetTab('evening')" id="tab-evening" class="preset-tab" style="background:none;border:none;color:rgba(255,255,255,0.4);font-size:.75rem;font-weight:700;padding:4px 8px;cursor:pointer;position:relative;">🌆 Evening</button>
                    </div>

                    {{-- Presets Grid --}}
                    <div id="presets-morning" class="presets-panel" style="display:flex;gap:6px;flex-wrap:wrap;">
                        @foreach(['07:00' => '07:00 AM', '08:00' => '08:00 AM', '09:00' => '09:00 AM', '10:00' => '10:00 AM', '11:00' => '11:00 AM'] as $val => $lbl)
                            <button type="button" onclick="selectPresetTime('{{ $val }}', this)" class="time-preset-chip" style="background:rgba(255,255,255,0.02);border:1px solid rgba(255,255,255,0.06);color:rgba(255,255,255,0.6);padding:6px 12px;border-radius:10px;font-size:.75rem;font-weight:600;cursor:pointer;transition:all .2s;outline:none;">{{ $lbl }}</button>
                        @endforeach
                    </div>
                    <div id="presets-afternoon" class="presets-panel" style="display:none;gap:6px;flex-wrap:wrap;">
                        @foreach(['12:00' => '12:00 PM', '13:00' => '01:00 PM', '14:00' => '02:00 PM', '15:00' => '03:00 PM', '16:00' => '04:00 PM'] as $val => $lbl)
                            <button type="button" onclick="selectPresetTime('{{ $val }}', this)" class="time-preset-chip" style="background:rgba(255,255,255,0.02);border:1px solid rgba(255,255,255,0.06);color:rgba(255,255,255,0.6);padding:6px 12px;border-radius:10px;font-size:.75rem;font-weight:600;cursor:pointer;transition:all .2s;outline:none;">{{ $lbl }}</button>
                        @endforeach
                    </div>
                    <div id="presets-evening" class="presets-panel" style="display:none;gap:6px;flex-wrap:wrap;">
                        @foreach(['17:00' => '05:00 PM', '18:00' => '06:00 PM', '19:00' => '07:00 PM', '20:00' => '08:00 PM', '21:00' => '09:00 PM'] as $val => $lbl)
                            <button type="button" onclick="selectPresetTime('{{ $val }}', this)" class="time-preset-chip" style="background:rgba(255,255,255,0.02);border:1px solid rgba(255,255,255,0.06);color:rgba(255,255,255,0.6);padding:6px 12px;border-radius:10px;font-size:.75rem;font-weight:600;cursor:pointer;transition:all .2s;outline:none;">{{ $lbl }}</button>
                        @endforeach
                    </div>
                </div>

                <div style="display:grid;grid-template-columns:1fr 1fr;gap:1.5rem;margin-bottom:1.5rem;">
                    <div>
                        <label style="display:block;font-size:.75rem;color:rgba(255,255,255,.4);font-weight:700;text-transform:uppercase;margin-bottom:10px;">Start Time <span style="color:#f43f5e;">*</span></label>
                        <div style="display:flex;gap:6px;align-items:center;background:rgba(0,0,0,.2);border:1px solid rgba(255,255,255,.1);border-radius:12px;padding:6px 12px;transition:all 0.2s;" id="start_time_wrapper">
                            <select id="start_hour" onchange="updateHiddenTime('start')" style="background:none;border:none;color:#fff;font-size:1rem;outline:none;cursor:pointer;width:100%;padding:4px 0;text-align:center;font-family:inherit;">
                                @for($h=1; $h<=12; $h++)
                                    <option value="{{ sprintf('%02d', $h) }}" style="background:#1e1e24;color:#fff;" {{ $h === 9 ? 'selected' : '' }}>{{ sprintf('%02d', $h) }}</option>
                                @endfor
                            </select>
                            <span style="color:rgba(255,255,255,0.4);font-weight:700;user-select:none;">:</span>
                            <select id="start_minute" onchange="updateHiddenTime('start')" style="background:none;border:none;color:#fff;font-size:1rem;outline:none;cursor:pointer;width:100%;padding:4px 0;text-align:center;font-family:inherit;">
                                @foreach(['00', '15', '30', '45'] as $m)
                                    <option value="{{ $m }}" style="background:#1e1e24;color:#fff;">{{ $m }}</option>
                                @endforeach
                            </select>
                            <select id="start_ampm" onchange="updateHiddenTime('start')" style="background:none;border:none;color:var(--vg-accent);font-size:0.9rem;font-weight:700;outline:none;cursor:pointer;width:100%;padding:4px 0;text-align:center;font-family:inherit;">
                                <option value="AM" style="background:#1e1e24;color:#fff;">AM</option>
                                <option value="PM" style="background:#1e1e24;color:#fff;">PM</option>
                            </select>
                        </div>
                        <input type="hidden" name="start_time" id="start_time" value="09:00">
                    </div>
                    <div>
                        <label style="display:block;font-size:.75rem;color:rgba(255,255,255,.4);font-weight:700;text-transform:uppercase;margin-bottom:10px;">End Time <span style="color:#f43f5e;">*</span></label>
                        <div style="display:flex;gap:6px;align-items:center;background:rgba(0,0,0,.2);border:1px solid rgba(255,255,255,.1);border-radius:12px;padding:6px 12px;transition:all 0.2s;" id="end_time_wrapper">
                            <select id="end_hour" onchange="updateHiddenTime('end')" style="background:none;border:none;color:#fff;font-size:1rem;outline:none;cursor:pointer;width:100%;padding:4px 0;text-align:center;font-family:inherit;">
                                @for($h=1; $h<=12; $h++)
                                    <option value="{{ sprintf('%02d', $h) }}" style="background:#1e1e24;color:#fff;" {{ $h === 10 ? 'selected' : '' }}>{{ sprintf('%02d', $h) }}</option>
                                @endfor
                            </select>
                            <span style="color:rgba(255,255,255,0.4);font-weight:700;user-select:none;">:</span>
                            <select id="end_minute" onchange="updateHiddenTime('end')" style="background:none;border:none;color:#fff;font-size:1rem;outline:none;cursor:pointer;width:100%;padding:4px 0;text-align:center;font-family:inherit;">
                                @foreach(['00', '15', '30', '45'] as $m)
                                    <option value="{{ $m }}" style="background:#1e1e24;color:#fff;">{{ $m }}</option>
                                @endforeach
                            </select>
                            <select id="end_ampm" onchange="updateHiddenTime('end')" style="background:none;border:none;color:var(--vg-accent);font-size:0.9rem;font-weight:700;outline:none;cursor:pointer;width:100%;padding:4px 0;text-align:center;font-family:inherit;">
                                <option value="AM" style="background:#1e1e24;color:#fff;">AM</option>
                                <option value="PM" style="background:#1e1e24;color:#fff;">PM</option>
                            </select>
                        </div>
                        <input type="hidden" name="end_time" id="end_time" value="10:00">
                    </div>
                </div>

                <div style="display:grid;grid-template-columns:1.5fr 1fr;gap:1.5rem;margin-bottom:2rem;align-items:end;">
                    <div>
                        <label style="display:block;font-size:.75rem;color:rgba(255,255,255,.4);font-weight:700;text-transform:uppercase;margin-bottom:10px;">Session Type</label>
                        <select name="session_type" style="width:100%;background:rgba(0,0,0,.2);border:1px solid rgba(255,255,255,.1);border-radius:12px;padding:12px;color:#fff;outline:none;cursor:pointer;">
                            <option value="General">General Training</option>
                            <option value="Strength">Strength & Conditioning</option>
                            <option value="Yoga">Yoga / Flexibility</option>
                            <option value="HIIT">HIIT / Cardio</option>
                            <option value="Meditation">Mindfulness / Meditation</option>
                        </select>
                    </div>
                    <div style="display:flex;align-items:center;gap:10px;height:48px;">
                        <input type="checkbox" name="is_recurring" id="is_recurring" value="1" style="width:20px;height:20px;accent-color:var(--vg-accent);">
                        <label for="is_recurring" style="font-size:.9rem;color:rgba(255,255,255,.7);font-weight:500;cursor:pointer;">Repeat weekly</label>
                    </div>
                </div>

                <button type="submit" style="width:100%;background:var(--vg-gradient);color:#fff;padding:14px;border-radius:16px;font-weight:800;font-size:1rem;border:none;box-shadow:0 10px 20px var(--vg-accent-glow);cursor:pointer;transition:all .3s;" onmouseover="this.style.transform='translateY(-2px)';this.style.boxShadow='0 15px 30px var(--vg-accent-glow)'" onmouseout="this.style.transform='';this.style.boxShadow='0 10px 20px var(--vg-accent-glow)'">
                    Create Time Slots
                </button>
            </form>
        </div>
    </div>

    {{-- Weekly Calendar View --}}
    <div class="fade-in-up delay-3" style="background:rgba(255,255,255,.02);border:1px solid rgba(255,255,255,.06);border-radius:28px;padding:2rem;">
        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:2rem;">
            <h2 style="font-size:1.25rem;font-weight:800;color:#fff;display:flex;align-items:center;gap:12px;">
                <i data-lucide="calendar-days" style="color:#fbbf24;"></i> Weekly Calendar
            </h2>
            <div style="display:flex;gap:8px;">
                <button onclick="openBlockDatesModal()" style="background:rgba(255,255,255,.05);border:1px solid rgba(255,255,255,.1);color:rgba(255,255,255,.6);padding:8px 16px;border-radius:12px;font-size:.85rem;font-weight:600;cursor:pointer;">
                    Block Specific Dates
                </button>
            </div>
        </div>

        <div style="display:grid;grid-template-columns:repeat(7, 1fr);gap:1rem;min-height:400px;">
            @php
                $days = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
                $todayIdx = (int)date('w');
            @endphp
            @foreach($days as $idx => $dayName)
                @php $isToday = $idx === $todayIdx; @endphp
                <div onclick="selectDay({{ $idx }})" style="background:{{ $isToday ? 'rgba(139,92,246,0.08)' : 'rgba(255,255,255,.02)' }};border:1px solid {{ $isToday ? 'rgba(139,92,246,0.4)' : 'rgba(255,255,255,.04)' }};border-radius:20px;padding:1rem;display:flex;flex-direction:column;gap:12px;cursor:pointer;transition:all .2s;position:relative;" class="calendar-col">
                    @if($isToday)
                        <div style="position:absolute;top:-10px;left:50%;transform:translateX(-50%);background:var(--vg-accent);color:#fff;font-size:.6rem;font-weight:900;padding:2px 8px;border-radius:4px;text-transform:uppercase;letter-spacing:.05em;box-shadow:0 0 10px var(--vg-accent-glow);">Today</div>
                    @endif
                    <div style="text-align:center;padding-bottom:.8rem;border-bottom:1px solid rgba(255,255,255,.03);pointer-events:none;">
                        <p style="font-size:.7rem;color:{{ $isToday ? 'var(--vg-accent)' : 'rgba(255,255,255,.3)' }};font-weight:800;text-transform:uppercase;letter-spacing:.05em;margin-bottom:2px;">{{ substr($dayName, 0, 3) }}</p>
                        <p style="font-size:1rem;font-weight:700;color:{{ $isToday ? '#fff' : 'rgba(255,255,255,.8)' }};">{{ $dayName }}</p>
                    </div>

                    <div style="flex:1;display:flex;flex-direction:column;gap:10px;pointer-events:all;">
                        @if(isset($groupedAvailabilities[$idx]))
                            @foreach($groupedAvailabilities[$idx] as $slot)
                                @php $isBooked = $slot->is_booked_this_week; @endphp
                                <div class="slot-card" onclick="editSlot('{{ $slot->id }}', '{{ \Carbon\Carbon::parse($slot->start_time)->format('H:i') }}', '{{ \Carbon\Carbon::parse($slot->end_time)->format('H:i') }}', '{{ $slot->session_type }}', {{ $slot->is_recurring ? 'true' : 'false' }})" style="background:rgba(255,255,255,.03);border:1px solid {{ $isBooked ? 'rgba(245,158,11,.3)' : 'rgba(16,185,129,.3)' }};border-radius:16px;padding:12px;position:relative;transition:all .2s;cursor:pointer;">
                                    <div style="display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:8px;">
                                        <span style="font-size:.75rem;font-weight:800;color:{{ $isBooked ? '#f59e0b' : '#10b981' }};">{{ \Carbon\Carbon::parse($slot->start_time)->format('h:i A') }}</span>
                                        <div style="display:flex;gap:6px;">
                                            <button type="button" style="background:none;border:none;padding:0;cursor:pointer;color:rgba(255,255,255,.3);transition:color .2s;">
                                                <i data-lucide="edit-3" style="width:12px;height:12px;"></i>
                                            </button>
                                            <form id="delete-form-{{ $slot->id }}" action="{{ route('trainer.availability.destroy', $slot->id) }}" method="POST" style="display:none;">
                                                @csrf
                                                @method('DELETE')
                                            </form>
                                            <button type="button" onclick="event.stopPropagation(); confirmDeleteSlot('{{ $slot->id }}')" style="background:none;border:none;padding:0;cursor:pointer;color:rgba(244,63,94,.4);transition:color .2s;" onmouseover="this.style.color='#f43f5e'" onmouseout="this.style.color='rgba(244,63,94,.4)'">
                                                <i data-lucide="trash-2" style="width:12px;height:12px;"></i>
                                            </button>
                                        </div>
                                    </div>
                                    <p style="font-size:.7rem;color:rgba(255,255,255,.4);font-weight:700;margin-bottom:8px;text-transform:uppercase;">{{ $slot->session_type }}</p>
                                    
                                    @if($isBooked)
                                        <div style="display:flex;align-items:center;gap:4px;font-size:.6rem;color:#f59e0b;font-weight:800;background:rgba(245,158,11,.1);border:1px solid rgba(245,158,11,.2);padding:4px 8px;border-radius:6px;width:fit-content;">
                                            <i data-lucide="calendar" style="width:10px;height:10px;"></i>
                                            Booked
                                        </div>
                                    @else
                                        <div style="display:flex;align-items:center;gap:4px;font-size:.6rem;color:#10b981;font-weight:800;background:rgba(16,185,129,.1);padding:4px 8px;border-radius:6px;width:fit-content;">
                                            <i data-lucide="check" style="width:10px;height:10px;"></i>
                                            Available
                                        </div>
                                    @endif
                                </div>
                            @endforeach
                        @else
                            <div style="flex:1;display:flex;align-items:center;justify-content:center;border:1px dashed rgba(255,255,255,.05);border-radius:16px;">
                                <p style="font-size:.65rem;color:rgba(255,255,255,.15);font-weight:700;text-transform:uppercase;">Empty</p>
                            </div>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    </div>

</div>

@push('modals')
{{-- Edit Slot Modal --}}
<div id="editModal" style="display:none;position:fixed;top:0;left:0;width:100%;height:100vh;background:rgba(0,0,0,.85);backdrop-filter:blur(12px);-webkit-backdrop-filter:blur(12px);z-index:9999;align-items:center;justify-content:center;padding:20px;">
    <div style="background:rgba(20,20,20,.95);border:1px solid rgba(255,255,255,.1);border-radius:28px;padding:2.5rem;width:100%;max-width:520px;position:relative;box-shadow:0 25px 50px -12px rgba(0, 0, 0, 0.5);">
        <button onclick="closeEditModal()" style="position:absolute;top:1.5rem;right:1.5rem;background:none;border:none;color:rgba(255,255,255,.4);cursor:pointer;">
            <i data-lucide="x"></i>
        </button>
        <h2 style="font-size:1.5rem;font-weight:900;color:#fff;margin-bottom:2rem;">Edit Time Slot</h2>
        <form id="editForm" method="POST" action="">
            @csrf
            @method('PUT')
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:1.5rem;margin-bottom:1.5rem;">
                <div>
                    <label style="display:block;font-size:.75rem;color:rgba(255,255,255,.4);font-weight:700;text-transform:uppercase;margin-bottom:10px;">Start Time</label>
                    <div style="display:flex;gap:6px;align-items:center;background:rgba(0,0,0,.4);border:1px solid rgba(255,255,255,.1);border-radius:12px;padding:6px 12px;transition:all 0.2s;" id="edit_start_time_wrapper">
                        <select id="edit_start_hour" onchange="updateHiddenEditTime('start')" style="background:none;border:none;color:#fff;font-size:1rem;outline:none;cursor:pointer;width:100%;padding:4px 0;text-align:center;font-family:inherit;">
                            @for($h=1; $h<=12; $h++)
                                <option value="{{ sprintf('%02d', $h) }}" style="background:#1e1e24;color:#fff;">{{ sprintf('%02d', $h) }}</option>
                            @endfor
                        </select>
                        <span style="color:rgba(255,255,255,0.4);font-weight:700;user-select:none;">:</span>
                        <select id="edit_start_minute" onchange="updateHiddenEditTime('start')" style="background:none;border:none;color:#fff;font-size:1rem;outline:none;cursor:pointer;width:100%;padding:4px 0;text-align:center;font-family:inherit;">
                            @foreach(['00', '15', '30', '45'] as $m)
                                <option value="{{ $m }}" style="background:#1e1e24;color:#fff;">{{ $m }}</option>
                            @endforeach
                        </select>
                        <select id="edit_start_ampm" onchange="updateHiddenEditTime('start')" style="background:none;border:none;color:var(--vg-accent);font-size:0.9rem;font-weight:700;outline:none;cursor:pointer;width:100%;padding:4px 0;text-align:center;font-family:inherit;">
                            <option value="AM" style="background:#1e1e24;color:#fff;">AM</option>
                            <option value="PM" style="background:#1e1e24;color:#fff;">PM</option>
                        </select>
                    </div>
                    <input type="hidden" name="start_time" id="edit_start_time">
                </div>
                <div>
                    <label style="display:block;font-size:.75rem;color:rgba(255,255,255,.4);font-weight:700;text-transform:uppercase;margin-bottom:10px;">End Time</label>
                    <div style="display:flex;gap:6px;align-items:center;background:rgba(0,0,0,.4);border:1px solid rgba(255,255,255,.1);border-radius:12px;padding:6px 12px;transition:all 0.2s;" id="edit_end_time_wrapper">
                        <select id="edit_end_hour" onchange="updateHiddenEditTime('end')" style="background:none;border:none;color:#fff;font-size:1rem;outline:none;cursor:pointer;width:100%;padding:4px 0;text-align:center;font-family:inherit;">
                            @for($h=1; $h<=12; $h++)
                                <option value="{{ sprintf('%02d', $h) }}" style="background:#1e1e24;color:#fff;">{{ sprintf('%02d', $h) }}</option>
                            @endfor
                        </select>
                        <span style="color:rgba(255,255,255,0.4);font-weight:700;user-select:none;">:</span>
                        <select id="edit_end_minute" onchange="updateHiddenEditTime('end')" style="background:none;border:none;color:#fff;font-size:1rem;outline:none;cursor:pointer;width:100%;padding:4px 0;text-align:center;font-family:inherit;">
                            @foreach(['00', '15', '30', '45'] as $m)
                                <option value="{{ $m }}" style="background:#1e1e24;color:#fff;">{{ $m }}</option>
                            @endforeach
                        </select>
                        <select id="edit_end_ampm" onchange="updateHiddenEditTime('end')" style="background:none;border:none;color:var(--vg-accent);font-size:0.9rem;font-weight:700;outline:none;cursor:pointer;width:100%;padding:4px 0;text-align:center;font-family:inherit;">
                            <option value="AM" style="background:#1e1e24;color:#fff;">AM</option>
                            <option value="PM" style="background:#1e1e24;color:#fff;">PM</option>
                        </select>
                    </div>
                    <input type="hidden" name="end_time" id="edit_end_time">
                </div>
            </div>
            <div style="margin-bottom:1.5rem;">
                <label style="display:block;font-size:.75rem;color:rgba(255,255,255,.4);font-weight:700;text-transform:uppercase;margin-bottom:10px;">Session Type</label>
                <select name="session_type" id="edit_session_type" style="width:100%;background:rgba(0,0,0,.4);border:1px solid rgba(255,255,255,.1);border-radius:12px;padding:12px;color:#fff;outline:none;">
                    <option value="General">General Training</option>
                    <option value="Strength">Strength & Conditioning</option>
                    <option value="Yoga">Yoga / Flexibility</option>
                    <option value="HIIT">HIIT / Cardio</option>
                    <option value="Meditation">Mindfulness / Meditation</option>
                </select>
            </div>
            <div style="display:flex;align-items:center;gap:10px;margin-bottom:2rem;">
                <input type="checkbox" name="is_recurring" id="edit_is_recurring" value="1" style="width:20px;height:20px;accent-color:var(--vg-accent);">
                <label for="edit_is_recurring" style="font-size:.9rem;color:rgba(255,255,255,.7);font-weight:500;">Repeat every week</label>
            </div>
            <button type="submit" style="width:100%;background:var(--vg-gradient);color:#fff;padding:14px;border-radius:16px;font-weight:800;font-size:1rem;border:none;box-shadow:0 10px 20px var(--vg-accent-glow);cursor:pointer;transition:all .3s;" onmouseover="this.style.transform='translateY(-2px)';this.style.boxShadow='0 15px 30px var(--vg-accent-glow)'" onmouseout="this.style.transform='';this.style.boxShadow='0 10px 20px var(--vg-accent-glow)'">Save Changes</button>
        </form>
    </div>
</div>

{{-- Custom Confirm Delete Modal --}}
<div id="customConfirmModal" style="display:none;position:fixed;top:0;left:0;width:100%;height:100vh;background:rgba(0,0,0,.85);backdrop-filter:blur(12px);-webkit-backdrop-filter:blur(12px);z-index:9999;align-items:center;justify-content:center;padding:20px;">
    <div style="background:#0f172a;border:1px solid rgba(244,63,94,0.3);border-radius:28px;padding:2.5rem 2rem 2rem;width:100%;max-width:400px;text-align:center;box-shadow:0 20px 40px rgba(244,63,94,0.15);position:relative;display:flex;flex-direction:column;align-items:center;gap:1rem;">
        <div style="width:64px;height:64px;border-radius:50%;background:rgba(244,63,94,0.1);border:2px solid rgba(244,63,94,0.25);display:flex;align-items:center;justify-content:center;font-size:1.8rem;margin-bottom:0.5rem;animation:pulseRed 2s infinite;">
            ⚠️
        </div>
        <h3 style="font-size:1.3rem;font-weight:800;color:#fff;margin:0;">Remove Time Slot</h3>
        <p style="font-size:0.9rem;color:rgba(255,255,255,0.6);line-height:1.6;margin:0;">
            Are you sure you want to remove this availability slot? Active bookings on this slot will need to be cancelled manually.
        </p>
        <div style="width:100%;display:flex;gap:12px;margin-top:1.5rem;justify-content:center;">
            <button type="button" onclick="closeConfirmDelete()" style="flex:1;padding:11px 20px;font-weight:700;font-size:0.85rem;border-radius:12px;background:rgba(255,255,255,0.05);border:1px solid rgba(255,255,255,0.1);color:#fff;cursor:pointer;transition:all 0.2s;" onmouseover="this.style.background='rgba(255,255,255,0.1)'" onmouseout="this.style.background='rgba(255,255,255,0.05)'">
                Cancel
            </button>
            <button type="button" id="confirmDeleteBtn" style="flex:1;padding:11px 20px;font-weight:700;font-size:0.85rem;border-radius:12px;background:linear-gradient(135deg,#f43f5e,#be123c);border:none;color:#fff;cursor:pointer;transition:all 0.2s;box-shadow:0 4px 15px rgba(244,63,94,0.35);" onmouseover="this.style.transform='translateY(-1px)';this.style.boxShadow='0 6px 20px rgba(244, 63, 94, 0.55)'" onmouseout="this.style.transform='';this.style.boxShadow='0 4px 15px rgba(244, 63, 94, 0.35)'">
                Remove
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
@endpush

<style>
    .day-checkbox:checked + .day-pill {
        background: var(--vg-accent) !important;
        border-color: var(--vg-accent) !important;
        color: #fff !important;
        box-shadow: 0 4px 12px var(--vg-accent-glow);
    }
    .slot-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 20px rgba(0,0,0,0.2);
    }
    .calendar-col:hover {
        border-color: rgba(255,255,255,0.1) !important;
        background: rgba(255,255,255,0.04) !important;
    }
    input[type="time"]::-webkit-calendar-picker-indicator {
        filter: invert(1);
        cursor: pointer;
    }
</style>

<script>
    let selectedDuration = 60; // default 60 minutes
    let selectedStartTime = null;

    function updateHiddenTime(prefix) {
        const hour = document.getElementById(prefix + '_hour').value;
        const minute = document.getElementById(prefix + '_minute').value;
        const ampm = document.getElementById(prefix + '_ampm').value;
        
        let hr = parseInt(hour, 10);
        if (ampm === 'PM' && hr < 12) hr += 12;
        if (ampm === 'AM' && hr === 12) hr = 0;
        
        const formattedHour = String(hr).padStart(2, '0');
        const formattedTime = `${formattedHour}:${minute}`;
        
        document.getElementById(prefix + '_time').value = formattedTime;
        
        // Highlight wrapper border when changed
        const wrapper = document.getElementById(prefix + '_time_wrapper');
        if (wrapper) {
            wrapper.style.borderColor = 'var(--vg-accent)';
            setTimeout(() => {
                wrapper.style.borderColor = 'rgba(255,255,255,.1)';
            }, 300);
        }
    }

    function updateHiddenEditTime(prefix) {
        const hour = document.getElementById('edit_' + prefix + '_hour').value;
        const minute = document.getElementById('edit_' + prefix + '_minute').value;
        const ampm = document.getElementById('edit_' + prefix + '_ampm').value;
        
        let hr = parseInt(hour, 10);
        if (ampm === 'PM' && hr < 12) hr += 12;
        if (ampm === 'AM' && hr === 12) hr = 0;
        
        const formattedHour = String(hr).padStart(2, '0');
        const formattedTime = `${formattedHour}:${minute}`;
        
        document.getElementById('edit_' + prefix + '_time').value = formattedTime;
        
        // Highlight wrapper border when changed
        const wrapper = document.getElementById('edit_' + prefix + '_time_wrapper');
        if (wrapper) {
            wrapper.style.borderColor = 'var(--vg-accent)';
            setTimeout(() => {
                wrapper.style.borderColor = 'rgba(255,255,255,.1)';
            }, 300);
        }
    }

    function setPresetDuration(mins) {
        selectedDuration = mins;
        
        // Update active class on duration pills
        document.querySelectorAll('.dur-pill').forEach(pill => {
            pill.style.background = 'none';
            pill.style.color = 'rgba(255,255,255,0.4)';
            pill.style.boxShadow = 'none';
        });
        
        const activePill = document.getElementById('dur-' + mins);
        if (activePill) {
            activePill.style.background = 'var(--vg-accent)';
            activePill.style.color = '#fff';
            activePill.style.boxShadow = '0 2px 6px var(--vg-accent-glow)';
        }
        
        // Recalculate end time if a start time was already selected
        if (selectedStartTime) {
            calculateEndTime();
        }
    }

    function showPresetTab(tabName) {
        // Toggle active panels
        document.querySelectorAll('.presets-panel').forEach(panel => {
            panel.style.display = 'none';
        });
        document.getElementById('presets-' + tabName).style.display = 'flex';
        
        // Toggle tab highlights
        document.querySelectorAll('.preset-tab').forEach(tab => {
            tab.style.color = 'rgba(255,255,255,0.4)';
        });
        const activeTab = document.getElementById('tab-' + tabName);
        if (activeTab) {
            activeTab.style.color = '#fff';
        }
    }

    function selectPresetTime(timeString, element) {
        selectedStartTime = timeString;
        
        // Parse timeString (24h "HH:MM")
        const [hr24, minute] = timeString.split(':');
        let hr = parseInt(hr24, 10);
        let ampm = 'AM';
        if (hr >= 12) {
            ampm = 'PM';
            if (hr > 12) hr -= 12;
        }
        if (hr === 0) hr = 12;
        
        const formattedHour = String(hr).padStart(2, '0');
        
        // Set custom dropdown selections
        document.getElementById('start_hour').value = formattedHour;
        document.getElementById('start_minute').value = minute;
        document.getElementById('start_ampm').value = ampm;
        
        // Set hidden input
        document.getElementById('start_time').value = timeString;
        
        calculateEndTime();
        
        // Highlight active preset chip
        document.querySelectorAll('.time-preset-chip').forEach(chip => {
            chip.style.background = 'rgba(255,255,255,0.02)';
            chip.style.borderColor = 'rgba(255,255,255,0.06)';
            chip.style.color = 'rgba(255,255,255,0.6)';
            chip.style.boxShadow = 'none';
        });
        
        element.style.background = 'rgba(139, 92, 246, 0.15)';
        element.style.borderColor = 'var(--vg-accent)';
        element.style.color = '#fff';
        element.style.boxShadow = '0 0 8px rgba(139, 92, 246, 0.2)';
    }

    function calculateEndTime() {
        if (!selectedStartTime) return;
        
        const [hours, minutes] = selectedStartTime.split(':').map(Number);
        let totalMinutes = hours * 60 + minutes + selectedDuration;
        
        // Calculate new hours and minutes
        let newHours = Math.floor(totalMinutes / 60) % 24;
        let newMinutes = totalMinutes % 60;
        
        // Round minutes to nearest 15 for safety
        newMinutes = Math.round(newMinutes / 15) * 15;
        if (newMinutes === 60) {
            newHours = (newHours + 1) % 24;
            newMinutes = 0;
        }
        
        // Format to string pad
        const formatted24Hour = String(newHours).padStart(2, '0');
        const formattedMinutes = String(newMinutes).padStart(2, '0');
        
        const time24String = `${formatted24Hour}:${formattedMinutes}`;
        document.getElementById('end_time').value = time24String;
        
        // Parse for dropdown selections
        let hr = newHours;
        let ampm = 'AM';
        if (hr >= 12) {
            ampm = 'PM';
            if (hr > 12) hr -= 12;
        }
        if (hr === 0) hr = 12;
        
        const formattedHour = String(hr).padStart(2, '0');
        
        document.getElementById('end_hour').value = formattedHour;
        document.getElementById('end_minute').value = formattedMinutes;
        document.getElementById('end_ampm').value = ampm;
    }

    function updateDayPills() {
        const checkboxes = document.querySelectorAll('.day-checkbox');
        checkboxes.forEach(cb => {
            const pill = cb.nextElementSibling;
            if (cb.checked) {
                pill.style.background = 'var(--vg-accent)';
                pill.style.borderColor = 'var(--vg-accent)';
                pill.style.color = '#fff';
                pill.style.boxShadow = '0 4px 12px var(--vg-accent-glow)';
            } else {
                pill.style.background = 'rgba(255,255,255,.03)';
                pill.style.borderColor = 'rgba(255,255,255,.1)';
                pill.style.color = 'rgba(255,255,255,.5)';
                pill.style.boxShadow = 'none';
            }
        });
        
        const btn = document.getElementById('toggle-all-btn');
        if (btn) {
            const allChecked = Array.from(checkboxes).every(cb => cb.checked);
            btn.innerText = allChecked ? 'Clear All' : 'Select All';
        }
    }

    function toggleAllDays() {
        const checkboxes = document.querySelectorAll('.day-checkbox');
        const allChecked = Array.from(checkboxes).every(cb => cb.checked);
        
        checkboxes.forEach(cb => {
            cb.checked = !allChecked;
        });
        
        updateDayPills();
    }

    function selectDay(idx) {
        const checkbox = document.getElementById('day-' + idx);
        if (checkbox) {
            checkbox.checked = !checkbox.checked;
            updateDayPills();
        }
        
        // Trigger scroll to form if mobile
        if (window.innerWidth < 768) {
            document.querySelector('form').scrollIntoView({ behavior: 'smooth' });
        }
    }

    document.addEventListener('DOMContentLoaded', function() {
        document.querySelectorAll('.day-checkbox').forEach(cb => {
            cb.addEventListener('change', updateDayPills);
        });
        updateDayPills();
    });

    function validateForm() {
        const checkedDays = document.querySelectorAll('.day-checkbox:checked');
        if (checkedDays.length === 0) {
            alert('Please select at least one day.');
            return false;
        }

        const start = document.getElementById('start_time').value;
        const end = document.getElementById('end_time').value;

        if (start && end && start >= end) {
            alert('End time must be after start time.');
            return false;
        }

        return true;
    }

    function editSlot(id, start, end, type, recurring) {
        const form = document.getElementById('editForm');
        form.action = `/trainer/availability/${id}`;
        
        // Parse start time (24h "HH:MM")
        const [startHr24, startMin] = start.split(':');
        let startHr = parseInt(startHr24, 10);
        let startAmpm = 'AM';
        if (startHr >= 12) {
            startAmpm = 'PM';
            if (startHr > 12) startHr -= 12;
        }
        if (startHr === 0) startHr = 12;
        const formattedStartHour = String(startHr).padStart(2, '0');
        
        // Parse end time (24h "HH:MM")
        const [endHr24, endMin] = end.split(':');
        let endHr = parseInt(endHr24, 10);
        let endAmpm = 'AM';
        if (endHr >= 12) {
            endAmpm = 'PM';
            if (endHr > 12) endHr -= 12;
        }
        if (endHr === 0) endHr = 12;
        const formattedEndHour = String(endHr).padStart(2, '0');

        // Populate Edit Dropdowns
        document.getElementById('edit_start_hour').value = formattedStartHour;
        document.getElementById('edit_start_minute').value = startMin;
        document.getElementById('edit_start_ampm').value = startAmpm;
        
        document.getElementById('edit_end_hour').value = formattedEndHour;
        document.getElementById('edit_end_minute').value = endMin;
        document.getElementById('edit_end_ampm').value = endAmpm;

        // Set hidden fields
        document.getElementById('edit_start_time').value = start;
        document.getElementById('edit_end_time').value = end;
        
        document.getElementById('edit_session_type').value = type;
        document.getElementById('edit_is_recurring').checked = recurring;
        
        document.getElementById('editModal').style.display = 'flex';
        document.body.style.overflow = 'hidden';
    }

    function closeEditModal() {
        document.getElementById('editModal').style.display = 'none';
        document.body.style.overflow = '';
    }

    let slotIdToDelete = null;

    function confirmDeleteSlot(id) {
        slotIdToDelete = id;
        const modal = document.getElementById('customConfirmModal');
        modal.style.display = 'flex';
        document.body.style.overflow = 'hidden';
    }

    function closeConfirmDelete() {
        const modal = document.getElementById('customConfirmModal');
        modal.style.display = 'none';
        document.body.style.overflow = '';
        slotIdToDelete = null;
    }

    document.addEventListener('DOMContentLoaded', function() {
        const confirmDeleteBtn = document.getElementById('confirmDeleteBtn');
        if (confirmDeleteBtn) {
            confirmDeleteBtn.addEventListener('click', function() {
                if (slotIdToDelete) {
                    document.getElementById('delete-form-' + slotIdToDelete).submit();
                }
            });
        }
    });

    function openBlockDatesModal() {
        alert('Block Dates feature coming soon! You will be able to select specific calendar dates to mark as unavailable.');
    }

    window.onclick = function(event) {
        const editModal = document.getElementById('editModal');
        const deleteModal = document.getElementById('customConfirmModal');
        if (event.target == editModal) {
            closeEditModal();
        }
        if (event.target == deleteModal) {
            closeConfirmDelete();
        }
    }
</script>
@endsection
