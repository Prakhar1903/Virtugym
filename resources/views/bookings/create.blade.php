@extends('layouts.app')

@section('title', 'Book a Trainer')

@section('content')
@php($hourlyRate = (float) ($trainer->hourly_rate ?: 500))

<style>
    .booking-container {
        max-width: 1000px;
        margin: 0 auto;
        padding: 2rem 1rem;
        display: grid;
        grid-template-columns: 1.2fr 1fr;
        gap: 2.5rem;
        align-items: start;
    }
    @media (max-width: 900px) {
        .booking-container { grid-template-columns: 1fr; }
    }
    .booking-card {
        background: var(--vg-panel);
        border: 1px solid var(--vg-border);
        border-radius: 24px;
        overflow: hidden;
    }
    .booking-header {
        background: var(--vg-gradient);
        padding: 2rem;
        text-align: center;
        color: #fff;
    }
    .trainer-preview {
        background: var(--vg-sidebar);
        border: 1px solid var(--vg-border);
        border-radius: 20px;
        padding: 1.5rem;
        margin-bottom: 2rem;
    }
    .form-label {
        display: block;
        font-size: 0.8rem;
        font-weight: 700;
        color: var(--vg-text-muted);
        text-transform: uppercase;
        letter-spacing: 0.05em;
        margin-bottom: 8px;
    }
    .form-input {
        width: 100%;
        background: var(--vg-sidebar);
        border: 1px solid var(--vg-border);
        border-radius: 12px;
        padding: 12px 16px;
        color: #fff;
        font-size: 1rem;
        transition: all 0.3s;
    }
    .form-input:focus {
        border-color: var(--vg-accent);
        outline: none;
        box-shadow: 0 0 0 2px var(--vg-accent-soft);
    }
    .summary-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 12px 0;
        border-bottom: 1px solid var(--vg-border);
    }
    .summary-row:last-child {
        border-bottom: none;
        margin-top: 10px;
        padding-top: 20px;
    }
</style>

<div class="booking-container">
    {{-- Left: Booking Form --}}
    <div class="booking-card">
        <div class="booking-header">
            <h1 style="font-size:1.8rem;font-weight:900;margin-bottom:0.5rem;">Book Your Session</h1>
            <p style="opacity:0.9;font-size:0.95rem;">Select your preferred date and time</p>
        </div>
        
        <form method="POST" action="{{ route('initiate.payment', $trainer->id) }}" style="padding: 2rem;">
            @csrf
            
            @if($errors->any())
                <div style="background:rgba(244,63,94,0.1);border-left:4px solid #f43f5e;color:#f43f5e;padding:1rem;border-radius:8px;margin-bottom:2rem;font-size:0.85rem;">
                    @foreach($errors->all() as $error)
                        <p>● {{ $error }}</p>
                    @endforeach
                </div>
            @endif
            
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:1.5rem;margin-bottom:1.5rem;">
                <div>
                    <label class="form-label">Select Date</label>
                    <input type="date" name="session_date" id="session_date" class="form-input" 
                           min="{{ date('Y-m-d', strtotime('+1 day')) }}" required>
                </div>
                <div>
                    <label class="form-label">Select Time</label>
                    <select name="session_time" id="session_time" class="form-input" required>
                        <option value="">Choose a date...</option>
                    </select>
                </div>
            </div>

            <div style="margin-bottom:1.5rem;">
                <label class="form-label">Session Type</label>
                <select name="session_type" class="form-input" required>
                    <option value="Strength Training">Strength Training</option>
                    <option value="Cardio & HIIT">Cardio & HIIT</option>
                    <option value="Flexibility & Yoga">Flexibility & Yoga</option>
                    <option value="Personal Coaching">Personal Coaching</option>
                </select>
            </div>
            
            <div style="margin-bottom:1.5rem;">
                <label class="form-label">Duration</label>
                <select name="duration" id="duration" class="form-input" required>
                    <option value="30">30 minutes</option>
                    <option value="60" selected>60 minutes</option>
                    <option value="90">90 minutes</option>
                    <option value="120">120 minutes</option>
                </select>
            </div>
            
            <div style="margin-bottom:2.5rem;">
                <label class="form-label">Special Requests</label>
                <textarea name="special_requests" rows="3" class="form-input" placeholder="Any specific goals or injuries to mention?"></textarea>
            </div>
            
            <div style="display:flex;gap:1rem;">
                <a href="{{ route('trainee.trainers') }}" style="flex:1;text-align:center;padding:14px;border-radius:12px;font-weight:700;color:var(--vg-text-muted);background:var(--vg-sidebar);border:1px solid var(--vg-border);text-decoration:none;transition:all .3s ease;" onmouseover="this.style.background='rgba(255,255,255,.05)';this.style.color='#fff'" onmouseout="this.style.background='var(--vg-sidebar)';this.style.color='var(--vg-text-muted)'">Cancel</a>
                <button type="submit" style="flex:2;background:var(--vg-gradient);color:#fff;padding:14px;border-radius:12px;font-weight:800;border:none;cursor:pointer;box-shadow:0 8px 20px var(--vg-accent-glow);transition:all .3s ease;" onmouseover="this.style.transform='translateY(-1px)';this.style.boxShadow='0 12px 25px var(--vg-accent-glow)'" onmouseout="this.style.transform='none';this.style.boxShadow='0 8px 20px var(--vg-accent-glow)'">
                    Proceed to Payment →
                </button>
            </div>
        </form>
    </div>

    {{-- Right: Order Summary --}}
    <div style="position:sticky;top:2rem;">
        <div class="trainer-preview">
            <div style="display:flex;gap:1rem;align-items:center;margin-bottom:1.5rem;">
                <div style="width:60px;height:60px;border-radius:50%;background:var(--vg-gradient);display:flex;align-items:center;justify-content:center;font-size:1.5rem;font-weight:800;color:#fff;">
                    {{ substr($trainer->name, 0, 1) }}
                </div>
                <div>
                    <h3 style="font-size:1.1rem;font-weight:800;color:#fff;">{{ $trainer->name }}</h3>
                    <p style="color:var(--vg-text-muted);font-size:0.8rem;">{{ $trainer->specialization ?? 'Personal Trainer' }}</p>
                </div>
            </div>
            <div style="display:flex;justify-content:space-between;font-size:0.85rem;">
                <span style="color:var(--vg-text-muted);">Rate: ₹{{ number_format($hourlyRate) }}/hr</span>
                <span style="color:#fbbf24;font-weight:700;">★ {{ $trainer->rating ?? '4.8' }}</span>
            </div>
        </div>

        <div style="background:var(--vg-panel);border:1px solid var(--vg-border);border-radius:24px;padding:1.5rem;">
            <h3 style="font-size:0.9rem;font-weight:700;color:#fff;margin-bottom:1rem;text-transform:uppercase;letter-spacing:0.05em;">Order Summary</h3>
            
            <div class="summary-row">
                <span style="color:var(--vg-text-muted);font-size:0.85rem;">Session Date</span>
                <span style="color:#fff;font-weight:600;font-size:0.85rem;" id="summaryDate">—</span>
            </div>
            <div class="summary-row">
                <span style="color:var(--vg-text-muted);font-size:0.85rem;">Session Time</span>
                <span style="color:#fff;font-weight:600;font-size:0.85rem;" id="summaryTime">—</span>
            </div>
            <div class="summary-row">
                <span style="color:var(--vg-text-muted);font-size:0.85rem;">Duration</span>
                <span style="color:#fff;font-weight:600;font-size:0.85rem;" id="summaryDuration">60 minutes</span>
            </div>
            <div class="summary-row">
                <span style="color:#fff;font-weight:800;">Total Amount</span>
                <span style="color:var(--vg-accent);font-size:1.5rem;font-weight:900;" id="amountDisplay">₹{{ number_format($hourlyRate) }}</span>
            </div>
        </div>
    </div>
</div>

<script>
    const trainerId = '{{ $trainer->id }}';
    const hourlyRate = Number({{ json_encode($hourlyRate) }});
    const dateInput = document.getElementById('session_date');
    const timeSelect = document.getElementById('session_time');
    const durationSelect = document.getElementById('duration');
    const summaryDate = document.getElementById('summaryDate');
    const summaryTime = document.getElementById('summaryTime');
    const summaryDuration = document.getElementById('summaryDuration');
    const amountDisplay = document.getElementById('amountDisplay');
    
    function formatDate(dateStr) {
        if (!dateStr) return '—';
        const date = new Date(dateStr);
        return date.toLocaleDateString('en-IN', { day: '2-digit', month: '2-digit', year: 'numeric' }).split('/').join('/');
    }

    dateInput.addEventListener('change', function() {
        const date = this.value;
        summaryDate.textContent = formatDate(date);
        
        if (!date) return;
        
        timeSelect.innerHTML = '<option value="">Loading...</option>';
        
        fetch(`/trainer/available-slots/${trainerId}/${date}`)
            .then(response => response.json())
            .then(slots => {
                timeSelect.innerHTML = '<option value="">Select time slot</option>';
                if (slots && slots.length > 0) {
                    slots.forEach(slot => {
                        const option = document.createElement('option');
                        option.value = slot.start_time;
                        option.textContent = `${slot.start_time} - ${slot.end_time}`;
                        timeSelect.appendChild(option);
                    });
                } else {
                    timeSelect.innerHTML = '<option value="">No slots available</option>';
                }
            })
            .catch(error => {
                timeSelect.innerHTML = '<option value="">Error loading slots</option>';
            });
    });

    timeSelect.addEventListener('change', function() {
        summaryTime.textContent = this.value || '—';
    });

    durationSelect.addEventListener('change', function() {
        const mins = parseInt(this.value);
        summaryDuration.textContent = mins + ' minutes';
        const total = (hourlyRate * mins) / 60;
        amountDisplay.textContent = '₹' + total.toLocaleString('en-IN');
    });
</script>
@endsection
