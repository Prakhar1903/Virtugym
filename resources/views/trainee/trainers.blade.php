
@extends('layouts.app')

@section('title', 'Find Trainers')

@section('content')
<style>
    .search-container {
        background: var(--vg-panel);
        border: 1px solid var(--vg-border);
        border-radius: 20px;
        padding: 1.5rem;
        margin-bottom: 2.5rem;
        display: flex;
        gap: 1rem;
        flex-wrap: wrap;
        align-items: center;
    }
    .search-input-group {
        flex: 1;
        min-width: 250px;
        position: relative;
    }
    .search-input {
        width: 100%;
        background: var(--vg-sidebar);
        border: 1px solid var(--vg-border);
        border-radius: 12px;
        padding: 12px 12px 12px 40px;
        color: #fff;
        font-size: 0.9rem;
        transition: all 0.3s;
    }
    .search-input:focus {
        border-color: var(--vg-accent);
        box-shadow: 0 0 0 2px var(--vg-accent-soft);
        outline: none;
    }
    .filter-select {
        background: var(--vg-sidebar);
        border: 1px solid var(--vg-border);
        border-radius: 12px;
        padding: 10px 16px;
        color: #fff;
        font-size: 0.85rem;
        min-width: 150px;
        cursor: pointer;
    }
    .trainer-card {
        background: var(--vg-panel);
        border: 1px solid var(--vg-border);
        border-radius: 24px;
        overflow: hidden;
        transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        position: relative;
    }
    .trainer-card:hover {
        transform: translateY(-8px);
        border-color: var(--vg-accent);
        box-shadow: 0 20px 40px rgba(0,0,0,0.4);
    }
    .trainer-header {
        height: 120px;
        background: var(--vg-gradient);
        position: relative;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .availability-badge {
        position: absolute;
        top: 1rem;
        right: 1rem;
        padding: 4px 12px;
        border-radius: 50px;
        font-size: 0.7rem;
        font-weight: 700;
        background: rgba(16, 185, 129, 0.2);
        color: #10b981;
        border: 1px solid rgba(16, 185, 129, 0.3);
    }
    .availability-badge.busy {
        background: rgba(244, 63, 94, 0.2);
        color: #f43f5e;
        border: 1px solid rgba(244, 63, 94, 0.3);
    }
    .trainer-avatar {
        width: 90px;
        height: 90px;
        border-radius: 50%;
        background: var(--vg-bg);
        border: 4px solid var(--vg-panel-strong);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 2.5rem;
        position: absolute;
        bottom: -45px;
        box-shadow: 0 8px 16px rgba(0,0,0,0.3);
    }
    .tag {
        font-size: 0.65rem;
        padding: 2px 8px;
        border-radius: 4px;
        background: var(--vg-accent-soft);
        color: var(--vg-accent);
        border: 1px solid var(--vg-border);
        font-weight: 600;
    }
</style>

<div style="max-width:1400px;margin:0 auto;padding:2rem 1rem;">
    <div style="margin-bottom:3rem;">
        <h1 style="font-size:2.5rem;font-weight:900;background:var(--vg-title-gradient);-webkit-background-clip:text;background-clip:text;color:transparent;margin-bottom:.5rem;">Find Your Perfect Trainer 🏋️</h1>
        <p style="color:var(--vg-text-muted);font-size:1rem;">Work with top-tier fitness experts tailored to your goals.</p>
    </div>

    {{-- Search & Filter Bar --}}
    <form action="{{ route('trainee.trainers') }}" method="GET" class="search-container">
        <div class="search-input-group">
            <i data-lucide="search" style="position:absolute;left:14px;top:50%;transform:translateY(-50%);width:18px;height:18px;color:var(--vg-text-muted);"></i>
            <input type="text" name="search" value="{{ request('search') }}" class="search-input" placeholder="Search by name or specialization...">
        </div>
        
        <select name="specialization" class="filter-select">
            <option value="">All Specializations</option>
            <option value="Strength" {{ request('specialization') == 'Strength' ? 'selected' : '' }}>Strength Training</option>
            <option value="Yoga" {{ request('specialization') == 'Yoga' ? 'selected' : '' }}>Yoga & Flexibility</option>
            <option value="HIIT" {{ request('specialization') == 'HIIT' ? 'selected' : '' }}>HIIT & Cardio</option>
            <option value="Nutrition" {{ request('specialization') == 'Nutrition' ? 'selected' : '' }}>Nutrition Coaching</option>
        </select>

        <select name="price_range" class="filter-select">
            <option value="">Any Price</option>
            <option value="0-500" {{ request('price_range') == '0-500' ? 'selected' : '' }}>Under ₹500</option>
            <option value="501-1000" {{ request('price_range') == '501-1000' ? 'selected' : '' }}>₹500 - ₹1000</option>
            <option value="1000+" {{ request('price_range') == '1000+' ? 'selected' : '' }}>₹1000+</option>
        </select>

        <button type="submit" style="background:var(--vg-accent);color:#fff;padding:10px 24px;border-radius:12px;font-weight:700;border:none;cursor:pointer;transition:all .2s;" onmouseover="this.style.background='var(--vg-accent-glow)'" onmouseout="this.style.background='var(--vg-accent)'">
            Apply Filters
        </button>
    </form>
    
    @if(isset($trainers) && $trainers->count() > 0)
        <div style="display:grid;grid-template-columns:repeat(auto-fill, minmax(340px, 1fr));gap:2rem;">
            @foreach($trainers as $trainer)
                @php
                    $initial = substr($trainer->name, 0, 1);
                    $isBusy = (crc32($trainer->id) % 3 == 0); // Mocking busy state
                @endphp
                <div class="trainer-card">
                    <div class="trainer-header">
                        <span class="availability-badge {{ $isBusy ? 'busy' : '' }}">
                            {{ $isBusy ? '● Busy Today' : '● Available Today' }}
                        </span>
                        <div class="trainer-avatar" style="overflow: hidden; display: flex; align-items: center; justify-content: center; background: var(--vg-bg);">
                            @if($trainer->profile_photo)
                                <img src="{{ asset('storage/' . $trainer->profile_photo) }}" alt="{{ $trainer->name }}" style="width: 100%; height: 100%; object-fit: cover;">
                            @else
                                {{ $initial }}
                            @endif
                        </div>
                    </div>

                    <div style="padding:3.5rem 1.5rem 1.5rem;">
                        <div style="text-align:center;margin-bottom:1.5rem;">
                            <h3 style="font-size:1.4rem;font-weight:800;color:#fff;margin-bottom:4px; display: inline-flex; align-items: center; justify-content: center; gap: 6px; width: 100%;">
                                {{ $trainer->name }}
                                @if($trainer->is_verified)
                                    <span title="Verified Trainer" style="background: #10b981; color: #fff; width: 16px; height: 16px; border-radius: 50%; display: inline-flex; align-items: center; justify-content: center; font-size: 9px; font-weight: 900; box-shadow: 0 0 8px rgba(16,185,129,0.5);">✓</span>
                                @endif
                            </h3>
                            <div style="display:flex;justify-content:center;gap:6px;flex-wrap:wrap;margin-bottom:8px;">
                                @php
                                    $specs = explode(',', $trainer->specialization ?? 'Strength,HIIT');
                                @endphp
                                @foreach($specs as $spec)
                                    <span class="tag">{{ trim($spec) }}</span>
                                @endforeach
                            </div>
                            <p style="color:var(--vg-text-muted);font-size:0.85rem;line-height:1.5;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden;">
                                {{ $trainer->bio ?? 'Certified fitness professional dedicated to helping you achieve your body goals through science-backed training methods.' }}
                            </p>
                        </div>

                        <div style="background:var(--vg-sidebar);border-radius:16px;padding:1rem;margin-bottom:1.5rem;display:grid;grid-template-columns:1fr 1fr;gap:1rem;">
                            <div style="text-align:center;border-right:1px solid var(--vg-border);">
                                <p style="font-size:0.65rem;color:var(--vg-text-faint);text-transform:uppercase;margin-bottom:4px;">Experience</p>
                                <p style="font-size:1rem;font-weight:700;color:#fff;">{{ $trainer->experience_years ?? 5 }}+ Years</p>
                            </div>
                            <div style="text-align:center;">
                                <p style="font-size:0.65rem;color:var(--vg-text-faint);text-transform:uppercase;margin-bottom:4px;">Clients</p>
                                <p style="font-size:1rem;font-weight:700;color:#fff;">{{ crc32($trainer->id) % 50 + 10 }}+</p>
                            </div>
                        </div>

                        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:1.5rem;padding:0 0.5rem;">
                            <div>
                                <p style="font-size:0.7rem;color:var(--vg-text-muted);margin-bottom:2px;">Hourly Rate</p>
                                <p style="font-size:1.5rem;font-weight:900;color:var(--vg-accent);">₹{{ number_format($trainer->hourly_rate ?? 499) }}</p>
                            </div>
                            <div style="text-align:right;">
                                <p style="font-size:0.7rem;color:var(--vg-text-muted);margin-bottom:2px;">Rating</p>
                                <p style="font-size:1.1rem;font-weight:700;color:#fbbf24;">★ {{ $trainer->rating ?? '4.8' }}</p>
                            </div>
                        </div>

                        <a href="{{ route('book.trainer.create', $trainer->id) }}" 
                           style="display:block;text-align:center;background:var(--vg-gradient);color:#fff;padding:14px;border-radius:16px;font-weight:800;text-decoration:none;box-shadow:0 8px 20px var(--vg-accent-glow);transition:all .3s;"
                           onmouseover="this.style.transform='scale(1.02)';this.style.boxShadow='0 12px 25px var(--vg-accent-glow)'"
                           onmouseout="this.style.transform='scale(1)';this.style.boxShadow='0 8px 20px var(--vg-accent-glow)'">
                            Book a Session →
                        </a>
                    </div>
                </div>
            @endforeach
        </div>
        <div style="margin-top:3rem;">
            {{ $trainers->links() }}
        </div>
    @else
        <div style="text-align:center;padding:5rem 2rem;background:var(--vg-panel);border:1px dashed var(--vg-border-strong);border-radius:30px;">
            <div style="font-size:4rem;margin-bottom:1.5rem;opacity:.5;">🔍</div>
            <h3 style="font-size:1.5rem;font-weight:800;color:#fff;margin-bottom:1rem;">No trainers found matching your criteria</h3>
            <p style="color:var(--vg-text-muted);margin-bottom:2rem;">Try adjusting your filters or search terms to see more results.</p>
            <a href="{{ route('trainee.trainers') }}" style="color:var(--vg-accent);font-weight:700;text-decoration:none;border-bottom:2px solid var(--vg-accent);">Clear all filters</a>
        </div>
    @endif
</div>
@endsection

