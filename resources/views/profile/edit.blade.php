@extends('layouts.app')

@section('title', 'Edit Profile')

@section('content')
<div style="max-width:860px;margin:0 auto;text-align:left !important;">

    <h1 style="text-align: left !important; font-size:1.6rem;font-weight:900;background:linear-gradient(135deg,#fff 20%,#c4b5fd);-webkit-background-clip:text;background-clip:text;color:transparent;margin-bottom:1.8rem; display:block; width:100%;" class="fade-in-up">
        ⚙️ Edit Profile
    </h1>

    {{-- Success Alert --}}
    @if(session('success'))
        <div style="background:rgba(16,185,129,.12);border:1px solid rgba(16,185,129,.3);border-left:4px solid #10b981;border-radius:12px;padding:14px 18px;color:#6ee7b7;display:flex;align-items:center;gap:10px;margin-bottom:1.5rem;" class="fade-in-up">
            <span style="font-size:1.2rem;">✅</span>
            <p style="font-weight:500;">{{ session('success') }}</p>
        </div>
    @endif

    {{-- ===== PROFILE INFO FORM ===== --}}
    <div class="fade-in-up delay-1" style="background:rgba(255,255,255,.03);border:1px solid rgba(139,92,246,.18);border-radius:24px;padding:2rem;margin-bottom:1.5rem;">
        <div style="display:flex;align-items:center;gap:12px;margin-bottom:1.6rem;padding-bottom:1rem;border-bottom:1px solid rgba(139,92,246,.12);">
            <div style="width:44px;height:44px;border-radius:14px;background:linear-gradient(135deg,rgba(139,92,246,.3),rgba(236,72,153,.2));display:flex;align-items:center;justify-content:center;font-size:1.3rem;">👤</div>
            <div>
                <h2 style="font-size:1.05rem;font-weight:800;color:#e2d9f3;">Profile Information</h2>
                <p style="font-size:.75rem;color:rgba(255,255,255,.3);">Update your personal details and professional credentials</p>
            </div>
        </div>

        <form method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data" style="display:flex;flex-direction:column;gap:1.2rem;">
            @csrf
            @method('PATCH')

            {{-- Profile Photo Upload Section (Always show, especially for Trainer) --}}
            <div style="display: flex; align-items: center; gap: 20px; background: rgba(255,255,255,0.02); border: 1px solid rgba(139,92,246,0.15); padding: 1.5rem; border-radius: 18px; margin-bottom: 0.5rem;">
                <div style="position: relative; width: 80px; height: 80px; border-radius: 50%; background: rgba(139,92,246,0.1); border: 2px solid rgba(139,92,246,0.3); display: flex; align-items: center; justify-content: center; overflow: hidden; flex-shrink: 0;" id="photo-preview-container">
                    @if($user->profile_photo)
                        <img src="{{ asset('storage/' . $user->profile_photo) }}" id="photo-preview" style="width: 100%; height: 100%; object-fit: cover;">
                    @else
                        <span id="photo-placeholder" style="font-size: 2rem;">👤</span>
                    @endif
                </div>
                <div style="flex: 1;">
                    <label style="display:block;font-size:.73rem;font-weight:700;color:rgba(196,181,253,.65);letter-spacing:.04em;margin-bottom:8px;">PROFILE PHOTO</label>
                    <div style="display: flex; gap: 10px; align-items: center;">
                        <label class="btn-outline" style="cursor: pointer; padding: 8px 16px; font-size: 0.8rem; border-radius: 10px; margin: 0; display: inline-flex; align-items: center; gap: 6px; background: rgba(255,255,255,0.03); border: 1px solid rgba(139,92,246,0.25); color: #fff;">
                            <i data-lucide="upload" style="width: 14px; height: 14px;"></i> Upload Photo
                            <input type="file" name="profile_photo" accept="image/*" onchange="previewProfilePhoto(this)" style="display: none;">
                        </label>
                        <span style="font-size: 0.75rem; color: rgba(255,255,255,0.3);">PNG, JPG up to 2MB</span>
                    </div>
                </div>
            </div>

            {{-- Name & Email --}}
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:1rem;">
                <div>
                    <label style="display:block;font-size:.73rem;font-weight:700;color:rgba(196,181,253,.65);letter-spacing:.04em;margin-bottom:6px;">FULL NAME</label>
                    <input type="text" name="name" value="{{ old('name', $user->name) }}" required
                           style="width:100%;padding:11px 14px;background:rgba(255,255,255,.05);border:1px solid {{ $errors->has('name') ? 'rgba(239,68,68,.5)' : 'rgba(139,92,246,.25)' }};border-radius:12px;color:#fff;font-size:.88rem;outline:none;transition:border-color .2s;"
                           onfocus="this.style.borderColor='rgba(139,92,246,.6)';this.style.background='rgba(139,92,246,.08)'"
                           onblur="this.style.borderColor='rgba(139,92,246,.25)';this.style.background='rgba(255,255,255,.05)'">
                    @error('name')<p style="color:#f87171;font-size:.72rem;margin-top:3px;">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label style="display:block;font-size:.73rem;font-weight:700;color:rgba(196,181,253,.65);letter-spacing:.04em;margin-bottom:6px;">EMAIL ADDRESS</label>
                    <input type="email" name="email" value="{{ old('email', $user->email) }}" required
                           style="width:100%;padding:11px 14px;background:rgba(255,255,255,.05);border:1px solid {{ $errors->has('email') ? 'rgba(239,68,68,.5)' : 'rgba(139,92,246,.25)' }};border-radius:12px;color:#fff;font-size:.88rem;outline:none;transition:border-color .2s;"
                           onfocus="this.style.borderColor='rgba(139,92,246,.6)';this.style.background='rgba(139,92,246,.08)'"
                           onblur="this.style.borderColor='rgba(139,92,246,.25)';this.style.background='rgba(255,255,255,.05)'">
                    @error('email')<p style="color:#f87171;font-size:.72rem;margin-top:3px;">{{ $message }}</p>@enderror
                </div>
            </div>

            {{-- Age / Gender (2 Column Grid for Trainer, 5 Column Grid with Weight/Height/Target Weight/DOB for Trainee) --}}
            @if($user->role === 'trainee')
            <div style="display:grid;grid-template-columns:repeat(auto-fit, minmax(140px, 1fr));gap:1rem;">
                <div>
                    <label style="display:block;font-size:.73rem;font-weight:700;color:rgba(196,181,253,.65);letter-spacing:.04em;margin-bottom:6px;">DATE OF BIRTH</label>
                    <input type="date" name="dob" value="{{ old('dob', $user->dob) }}" required
                           style="width:100%;padding:11px 14px;background:rgba(255,255,255,.05);border:1px solid rgba(139,92,246,.25);border-radius:12px;color:#fff;font-size:.88rem;outline:none;"
                           onfocus="this.style.borderColor='rgba(139,92,246,.6)'" onblur="this.style.borderColor='rgba(139,92,246,.25)'">
                    @error('dob')<p style="color:#f87171;font-size:.72rem;margin-top:3px;">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label style="display:block;font-size:.73rem;font-weight:700;color:rgba(196,181,253,.65);letter-spacing:.04em;margin-bottom:6px;">GENDER</label>
                    <select name="gender" required style="width:100%;padding:11px 14px;background:rgba(8,8,26,.9);border:1px solid rgba(139,92,246,.25);border-radius:12px;color:#fff;font-size:.88rem;outline:none;"
                            onfocus="this.style.borderColor='rgba(139,92,246,.6)'" onblur="this.style.borderColor='rgba(139,92,246,.25)'">
                        <option value="" disabled {{ old('gender', $user->gender) == '' ? 'selected' : '' }}>Select</option>
                        <option value="male"   {{ old('gender',$user->gender)=='male'   ? 'selected' : '' }}>Male</option>
                        <option value="female" {{ old('gender',$user->gender)=='female' ? 'selected' : '' }}>Female</option>
                        <option value="other"  {{ old('gender',$user->gender)=='other'  ? 'selected' : '' }}>Other</option>
                    </select>
                    @error('gender')<p style="color:#f87171;font-size:.72rem;margin-top:3px;">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label style="display:block;font-size:.73rem;font-weight:700;color:rgba(196,181,253,.65);letter-spacing:.04em;margin-bottom:6px;">CURRENT WEIGHT (kg)</label>
                    <input type="number" step=".1" name="weight" id="current_weight_input" value="{{ old('weight', $user->weight) }}" placeholder="e.g. 70" required
                           style="width:100%;padding:11px 14px;background:rgba(255,255,255,.05);border:1px solid rgba(139,92,246,.25);border-radius:12px;color:#fff;font-size:.88rem;outline:none;"
                           onfocus="this.style.borderColor='rgba(139,92,246,.6)'" onblur="this.style.borderColor='rgba(139,92,246,.25)'" oninput="calculateBMI()">
                    @error('weight')<p style="color:#f87171;font-size:.72rem;margin-top:3px;">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label style="display:block;font-size:.73rem;font-weight:700;color:rgba(196,181,253,.65);letter-spacing:.04em;margin-bottom:6px;">TARGET WEIGHT (kg)</label>
                    <input type="number" step=".1" name="target_weight" value="{{ old('target_weight', $user->target_weight) }}" placeholder="e.g. 65" required
                           style="width:100%;padding:11px 14px;background:rgba(255,255,255,.05);border:1px solid rgba(139,92,246,.25);border-radius:12px;color:#fff;font-size:.88rem;outline:none;"
                           onfocus="this.style.borderColor='rgba(139,92,246,.6)'" onblur="this.style.borderColor='rgba(139,92,246,.25)'">
                    @error('target_weight')<p style="color:#f87171;font-size:.72rem;margin-top:3px;">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label style="display:block;font-size:.73rem;font-weight:700;color:rgba(196,181,253,.65);letter-spacing:.04em;margin-bottom:6px;">HEIGHT (cm)</label>
                    <input type="number" name="height" id="height_input" value="{{ old('height', $user->height) }}" placeholder="e.g. 175" required
                           style="width:100%;padding:11px 14px;background:rgba(255,255,255,.05);border:1px solid rgba(139,92,246,.25);border-radius:12px;color:#fff;font-size:.88rem;outline:none;"
                           onfocus="this.style.borderColor='rgba(139,92,246,.6)'" onblur="this.style.borderColor='rgba(139,92,246,.25)'" oninput="calculateBMI()">
                    @error('height')<p style="color:#f87171;font-size:.72rem;margin-top:3px;">{{ $message }}</p>@enderror
                </div>
            </div>

            {{-- BMI Status Card --}}
            <div style="background:rgba(255,255,255,0.02);border:1px solid rgba(139,92,246,0.15);border-radius:16px;padding:1.2rem;display:flex;align-items:center;justify-content:space-between;margin-top:0.5rem;flex-wrap:wrap;gap:1rem;">
                <div style="display:flex;align-items:center;gap:12px;">
                    <div style="width:40px;height:40px;border-radius:12px;background:rgba(16,185,129,0.15);border:1px solid rgba(16,185,129,0.3);display:flex;align-items:center;justify-content:center;font-size:1.2rem;">📊</div>
                    <div>
                        <h4 style="font-size:0.9rem;font-weight:800;color:#fff;margin:0;">Body Mass Index (BMI)</h4>
                        <p style="font-size:0.75rem;color:rgba(255,255,255,0.3);margin:2px 0 0 0;">Auto-calculated from weight and height</p>
                    </div>
                </div>
                <div style="display:flex;align-items:center;gap:16px;">
                    <div style="text-align:right; margin-right: 0.5rem;">
                        <span id="bmi_value" style="font-size:1.6rem;font-weight:900;color:#10b981;font-family:monospace;">--</span>
                        <span id="bmi_status" style="display:block;font-size:0.7rem;font-weight:800;text-transform:uppercase;color:rgba(255,255,255,0.4);letter-spacing:0.05em;margin-top:2px;">--</span>
                    </div>
                </div>
            </div>
            @else
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:1rem;">
                <div>
                    <label style="display:block;font-size:.73rem;font-weight:700;color:rgba(196,181,253,.65);letter-spacing:.04em;margin-bottom:6px;">DATE OF BIRTH</label>
                    <input type="date" name="dob" value="{{ old('dob', $user->dob) }}" required
                           style="width:100%;padding:11px 14px;background:rgba(255,255,255,.05);border:1px solid rgba(139,92,246,.25);border-radius:12px;color:#fff;font-size:.88rem;outline:none;"
                           onfocus="this.style.borderColor='rgba(139,92,246,.6)'" onblur="this.style.borderColor='rgba(139,92,246,.25)'">
                    @error('dob')<p style="color:#f87171;font-size:.72rem;margin-top:3px;">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label style="display:block;font-size:.73rem;font-weight:700;color:rgba(196,181,253,.65);letter-spacing:.04em;margin-bottom:6px;">GENDER</label>
                    <select name="gender" required style="width:100%;padding:11px 14px;background:rgba(8,8,26,.9);border:1px solid rgba(139,92,246,.25);border-radius:12px;color:#fff;font-size:.88rem;outline:none;"
                            onfocus="this.style.borderColor='rgba(139,92,246,.6)'" onblur="this.style.borderColor='rgba(139,92,246,.25)'">
                        <option value="" disabled {{ old('gender', $user->gender) == '' ? 'selected' : '' }}>Select</option>
                        <option value="male"   {{ old('gender',$user->gender)=='male'   ? 'selected' : '' }}>Male</option>
                        <option value="female" {{ old('gender',$user->gender)=='female' ? 'selected' : '' }}>Female</option>
                        <option value="other"  {{ old('gender',$user->gender)=='other'  ? 'selected' : '' }}>Other</option>
                    </select>
                    @error('gender')<p style="color:#f87171;font-size:.72rem;margin-top:3px;">{{ $message }}</p>@enderror
                </div>
            </div>
            @endif

            {{-- Trainee-specific sections --}}
            @if($user->role === 'trainee')
            {{-- Fitness Level / Goal / Workout Days --}}
            <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:1rem;">
                <div>
                    <label style="display:block;font-size:.73rem;font-weight:700;color:rgba(196,181,253,.65);letter-spacing:.04em;margin-bottom:6px;">FITNESS LEVEL</label>
                    <select name="fitness_level" required style="width:100%;padding:11px 14px;background:rgba(8,8,26,.9);border:1px solid rgba(139,92,246,.25);border-radius:12px;color:#fff;font-size:.88rem;outline:none;">
                        <option value="" disabled {{ old('fitness_level', $user->fitness_level) == '' ? 'selected' : '' }}>Select</option>
                        <option value="beginner"     {{ old('fitness_level',$user->fitness_level)=='beginner'     ? 'selected':'' }}>🌱 Beginner</option>
                        <option value="intermediate" {{ old('fitness_level',$user->fitness_level)=='intermediate' ? 'selected':'' }}>💪 Intermediate</option>
                        <option value="advanced"     {{ old('fitness_level',$user->fitness_level)=='advanced'     ? 'selected':'' }}>🏆 Advanced</option>
                        <option value="expert"       {{ old('fitness_level',$user->fitness_level)=='expert'       ? 'selected':'' }}>⚡ Expert</option>
                    </select>
                    @error('fitness_level')<p style="color:#f87171;font-size:.72rem;margin-top:3px;">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label style="display:block;font-size:.73rem;font-weight:700;color:rgba(196,181,253,.65);letter-spacing:.04em;margin-bottom:6px;">PRIMARY GOAL</label>
                    <select name="goal" required style="width:100%;padding:11px 14px;background:rgba(8,8,26,.9);border:1px solid rgba(139,92,246,.25);border-radius:12px;color:#fff;font-size:.88rem;outline:none;">
                        <option value="" disabled {{ old('goal', $user->goal) == '' ? 'selected' : '' }}>Select</option>
                        <option value="weight_loss"    {{ old('goal',$user->goal)=='weight_loss'    ? 'selected':'' }}>🎯 Weight Loss</option>
                        <option value="muscle_gain"    {{ old('goal',$user->goal)=='muscle_gain'    ? 'selected':'' }}>💪 Muscle Gain</option>
                        <option value="endurance"      {{ old('goal',$user->goal)=='endurance'      ? 'selected':'' }}>🏃 Endurance</option>
                        <option value="flexibility"    {{ old('goal',$user->goal)=='flexibility'    ? 'selected':'' }}>🧘 Flexibility</option>
                        <option value="general_fitness"{{ old('goal',$user->goal)=='general_fitness'? 'selected':'' }}>⭐ General Fitness</option>
                    </select>
                    @error('goal')<p style="color:#f87171;font-size:.72rem;margin-top:3px;">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label style="display:block;font-size:.73rem;font-weight:700;color:rgba(196,181,253,.65);letter-spacing:.04em;margin-bottom:6px;">WORKOUT DAYS/WEEK</label>
                    <select name="workout_days" required style="width:100%;padding:11px 14px;background:rgba(8,8,26,.9);border:1px solid rgba(139,92,246,.25);border-radius:12px;color:#fff;font-size:.88rem;outline:none;">
                        <option value="" disabled {{ old('workout_days', $user->workout_days) == '' ? 'selected' : '' }}>Select</option>
                        @for($i=1;$i<=7;$i++)
                            <option value="{{ $i }}" {{ old('workout_days',$user->workout_days)==$i ? 'selected':'' }}>{{ $i }} day{{ $i>1?'s':'' }}</option>
                        @endfor
                    </select>
                    @error('workout_days')<p style="color:#f87171;font-size:.72rem;margin-top:3px;">{{ $message }}</p>@enderror
                </div>
            </div>

            {{-- Equipment --}}
            <div>
                <label style="display:block;font-size:.73rem;font-weight:700;color:rgba(196,181,253,.65);letter-spacing:.04em;margin-bottom:8px;">EQUIPMENT AVAILABLE</label>
                <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(160px,1fr));gap:.5rem;">
                    @foreach(['dumbbells'=>'🏋️ Dumbbells','barbell'=>'🏋️ Barbell','resistance_bands'=>'🎯 Resistance Bands','kettlebells'=>'⚫ Kettlebells','pull_up_bar'=>'🔝 Pull-up Bar','treadmill'=>'🏃 Treadmill'] as $val=>$label)
                        <label style="display:flex;align-items:center;gap:8px;font-size:.8rem;color:rgba(255,255,255,.5);cursor:pointer;padding:8px 12px;background:rgba(255,255,255,.03);border:1px solid rgba(139,92,246,.15);border-radius:10px;transition:all .2s;"
                               onmouseover="this.style.borderColor='rgba(139,92,246,.4)';this.style.color='#c4b5fd'"
                               onmouseout="this.style.borderColor='rgba(139,92,246,.15)';this.style.color='rgba(255,255,255,.5)'">
                            <input type="checkbox" name="equipment[]" value="{{ $val }}" accent-color="#8b5cf6"
                                   {{ in_array($val, (array)($user->equipment ?? [])) ? 'checked' : '' }}
                                   style="accent-color:#8b5cf6;">
                            {{ $label }}
                        </label>
                    @endforeach
                </div>
            </div>

            {{-- Injuries --}}
            <div>
                <label style="display:block;font-size:.73rem;font-weight:700;color:rgba(196,181,253,.65);letter-spacing:.04em;margin-bottom:6px;">INJURIES / LIMITATIONS</label>
                <textarea name="injuries" rows="2" placeholder="List any injuries or physical limitations…"
                          style="width:100%;padding:11px 14px;background:rgba(255,255,255,.05);border:1px solid rgba(139,92,246,.25);border-radius:12px;color:#fff;font-size:.88rem;outline:none;resize:vertical;"
                          onfocus="this.style.borderColor='rgba(139,92,246,.6)'" onblur="this.style.borderColor='rgba(139,92,246,.25)'">{{ old('injuries', $user->injuries) }}</textarea>
            </div>

            <div>
                <label style="display:block;font-size:.73rem;font-weight:700;color:rgba(196,181,253,.65);letter-spacing:.04em;margin-bottom:6px;">UPI ID FOR REFUNDS</label>
                <input type="text" name="upi_id" value="{{ old('upi_id', $user->upi_id) }}" placeholder="name@upi"
                       style="width:100%;padding:11px 14px;background:rgba(255,255,255,.05);border:1px solid {{ $errors->has('upi_id') ? 'rgba(239,68,68,.5)' : 'rgba(139,92,246,.25)' }};border-radius:12px;color:#fff;font-size:.88rem;outline:none;"
                       onfocus="this.style.borderColor='rgba(139,92,246,.6)'" onblur="this.style.borderColor='rgba(139,92,246,.25)'">
                @error('upi_id')<p style="color:#f87171;font-size:.72rem;margin-top:3px;">{{ $message }}</p>@enderror
            </div>
            @endif

            {{-- Trainer-specific fields (Keep & Add section) --}}
            @if($user->role === 'trainer')
            <div style="border-top:1px solid rgba(139,92,246,.12);padding-top:1.5rem; display: flex; flex-direction: column; gap: 1.2rem;">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.5rem;">
                    <p style="font-size:.73rem;font-weight:700;color:rgba(196,181,253,.65);letter-spacing:.12em;margin:0;">TRAINER DETAILS & CREDENTIALS</p>
                    
                    {{-- Account Verification Status --}}
                    <div style="display: inline-flex; align-items: center; gap: 8px; background: {{ $user->is_verified ? 'rgba(16,185,129,0.1)' : 'rgba(245,158,11,0.1)' }}; border: 1px solid {{ $user->is_verified ? 'rgba(16,185,129,0.2)' : 'rgba(245,158,11,0.2)' }}; border-radius: 50px; padding: 6px 14px;">
                        <span style="width: 8px; height: 8px; background: {{ $user->is_verified ? '#10b981' : '#f59e0b' }}; border-radius: 50%;"></span>
                        <span style="font-size: 0.75rem; font-weight: 700; color: {{ $user->is_verified ? '#6ee7b7' : '#fcd34d' }};">
                            {{ $user->is_verified ? 'Account Verified ✅' : 'Verification Pending ⏳' }}
                        </span>
                    </div>
                </div>

                {{-- Specialization & Experience --}}
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:1rem;">
                    <div>
                        <label style="display:block;font-size:.73rem;font-weight:700;color:rgba(196,181,253,.65);letter-spacing:.04em;margin-bottom:6px;">SPECIALIZATION</label>
                        <input type="text" name="specialization" value="{{ old('specialization',$user->specialization) }}" placeholder="e.g. Strength Training, HIIT, Yoga"
                               style="width:100%;padding:11px 14px;background:rgba(255,255,255,.05);border:1px solid rgba(139,92,246,.25);border-radius:12px;color:#fff;font-size:.88rem;outline:none;"
                               onfocus="this.style.borderColor='rgba(139,92,246,.6)'" onblur="this.style.borderColor='rgba(139,92,246,.25)'">
                    </div>
                    <div>
                        <label style="display:block;font-size:.73rem;font-weight:700;color:rgba(196,181,253,.65);letter-spacing:.04em;margin-bottom:6px;">EXPERIENCE (YEARS)</label>
                        <input type="number" name="experience_years" value="{{ old('experience_years',$user->experience_years) }}" placeholder="5"
                               style="width:100%;padding:11px 14px;background:rgba(255,255,255,.05);border:1px solid rgba(139,92,246,.25);border-radius:12px;color:#fff;font-size:.88rem;outline:none;"
                               onfocus="this.style.borderColor='rgba(139,92,246,.6)'" onblur="this.style.borderColor='rgba(139,92,246,.25)'">
                    </div>
                </div>

                {{-- Hourly Rate & Languages Spoken --}}
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:1rem;">
                    <div>
                        <label style="display:block;font-size:.73rem;font-weight:700;color:rgba(196,181,253,.65);letter-spacing:.04em;margin-bottom:6px;">HOURLY RATE (₹)</label>
                        <input type="number" name="hourly_rate" value="{{ old('hourly_rate',$user->hourly_rate) }}" placeholder="500"
                               style="width:100%;padding:11px 14px;background:rgba(255,255,255,.05);border:1px solid rgba(139,92,246,.25);border-radius:12px;color:#fff;font-size:.88rem;outline:none;"
                               onfocus="this.style.borderColor='rgba(139,92,246,.6)'" onblur="this.style.borderColor='rgba(139,92,246,.25)'">
                    </div>
                    <div>
                        <label style="display:block;font-size:.73rem;font-weight:700;color:rgba(196,181,253,.65);letter-spacing:.04em;margin-bottom:6px;">LANGUAGES SPOKEN</label>
                        <input type="text" name="languages" value="{{ old('languages',$user->languages) }}" placeholder="e.g. English, Hindi, Spanish"
                               style="width:100%;padding:11px 14px;background:rgba(255,255,255,.05);border:1px solid rgba(139,92,246,.25);border-radius:12px;color:#fff;font-size:.88rem;outline:none;"
                               onfocus="this.style.borderColor='rgba(139,92,246,.6)'" onblur="this.style.borderColor='rgba(139,92,246,.25)'">
                    </div>
                </div>

                {{-- Available Session Types --}}
                <div>
                    <label style="display:block;font-size:.73rem;font-weight:700;color:rgba(196,181,253,.65);letter-spacing:.04em;margin-bottom:8px;">AVAILABLE SESSION TYPES</label>
                    <div style="display:flex;gap:10px;flex-wrap:wrap;">
                        @foreach(['Strength' => '💪 Strength', 'Cardio' => '🏃 Cardio', 'Yoga' => '🧘 Yoga', 'HIIT' => '⚡ HIIT'] as $val => $label)
                            <label style="display:flex;align-items:center;gap:8px;font-size:.8rem;color:rgba(255,255,255,.5);cursor:pointer;padding:8px 14px;background:rgba(255,255,255,.03);border:1px solid rgba(139,92,246,.15);border-radius:12px;transition:all .2s;"
                                   onmouseover="this.style.borderColor='rgba(139,92,246,.4)';this.style.color='#c4b5fd'"
                                   onmouseout="this.style.borderColor='rgba(139,92,246,.15)';this.style.color='rgba(255,255,255,.5)'">
                                <input type="checkbox" name="session_types[]" value="{{ $val }}" accent-color="#8b5cf6"
                                       {{ in_array($val, (array)($user->session_types ?? [])) ? 'checked' : '' }}
                                       style="accent-color:#8b5cf6;">
                                {{ $label }}
                            </label>
                        @endforeach
                    </div>
                </div>

                {{-- Certifications field --}}
                <div>
                    <label style="display:block;font-size:.73rem;font-weight:700;color:rgba(196,181,253,.65);letter-spacing:.04em;margin-bottom:6px;">CERTIFICATIONS / QUALIFICATIONS</label>
                    <textarea name="certifications" rows="2" placeholder="List your qualifications (e.g. NASM, ACE, CrossFit L1)..."
                              style="width:100%;padding:11px 14px;background:rgba(255,255,255,.05);border:1px solid rgba(139,92,246,.25);border-radius:12px;color:#fff;font-size:.88rem;outline:none;resize:vertical;"
                              onfocus="this.style.borderColor='rgba(139,92,246,.6)'" onblur="this.style.borderColor='rgba(139,92,246,.25)'">{{ old('certifications',$user->certifications) }}</textarea>
                </div>

                {{-- Social/portfolio link --}}
                <div>
                    <label style="display:block;font-size:.73rem;font-weight:700;color:rgba(196,181,253,.65);letter-spacing:.04em;margin-bottom:6px;">SOCIAL / PORTFOLIO LINK (OPTIONAL)</label>
                    <input type="url" name="portfolio_link" value="{{ old('portfolio_link',$user->portfolio_link) }}" placeholder="https://instagram.com/trainer_handle"
                           style="width:100%;padding:11px 14px;background:rgba(255,255,255,.05);border:1px solid rgba(139,92,246,.25);border-radius:12px;color:#fff;font-size:.88rem;outline:none;"
                           onfocus="this.style.borderColor='rgba(139,92,246,.6)'" onblur="this.style.borderColor='rgba(139,92,246,.25)'">
                </div>

                {{-- Bio --}}
                <div>
                    <label style="display:block;font-size:.73rem;font-weight:700;color:rgba(196,181,253,.65);letter-spacing:.04em;margin-bottom:6px;">BIO</label>
                    <textarea name="bio" rows="3" placeholder="Tell clients about yourself, your philosophy, and training approach..."
                              style="width:100%;padding:11px 14px;background:rgba(255,255,255,.05);border:1px solid rgba(139,92,246,.25);border-radius:12px;color:#fff;font-size:.88rem;outline:none;resize:vertical;"
                              onfocus="this.style.borderColor='rgba(139,92,246,.6)'" onblur="this.style.borderColor='rgba(139,92,246,.25)'">{{ old('bio',$user->bio) }}</textarea>
                </div>
            </div>
            @endif

            <div style="margin-top: 1rem;">
                <button type="submit"
                        style="background:linear-gradient(135deg,#8b5cf6,#ec4899);color:#fff;border:none;border-radius:12px;padding:13px 32px;font-size:.92rem;font-weight:700;cursor:pointer;box-shadow:0 8px 22px rgba(139,92,246,.35);transition:all .3s;position:relative;overflow:hidden;"
                        onmouseover="this.style.transform='translateY(-2px)';this.style.boxShadow='0 14px 32px rgba(139,92,246,.55)'"
                        onmouseout="this.style.transform='';this.style.boxShadow='0 8px 22px rgba(139,92,246,.35)'">
                    Save Profile →
                </button>
            </div>
        </form>
    </div>

    {{-- Script for live photo preview --}}
    <script>
        function previewProfilePhoto(input) {
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    let img = document.getElementById('photo-preview');
                    if (!img) {
                        img = document.createElement('img');
                        img.id = 'photo-preview';
                        img.style = 'width: 100%; height: 100%; object-fit: cover;';
                        const container = document.getElementById('photo-preview-container');
                        container.innerHTML = '';
                        container.appendChild(img);
                    }
                    img.src = e.target.result;
                }
                reader.readAsDataURL(input.files[0]);
            }
        }

        function calculateBMI() {
            const weightInput = document.getElementById('current_weight_input');
            const heightInput = document.getElementById('height_input');
            const bmiVal = document.getElementById('bmi_value');
            const bmiStatus = document.getElementById('bmi_status');
            
            if (!weightInput || !heightInput || !bmiVal || !bmiStatus) return;

            const weight = parseFloat(weightInput.value);
            const height = parseFloat(heightInput.value);
            
            if (weight && height && height > 0) {
                const heightInMeters = height / 100;
                const bmi = (weight / (heightInMeters * heightInMeters)).toFixed(1);
                bmiVal.innerText = bmi;
                
                let status = 'Normal';
                let color = '#10b981'; // Green
                
                if (bmi < 18.5) {
                    status = 'Underweight';
                    color = '#3b82f6'; // Blue
                } else if (bmi >= 18.5 && bmi < 25) {
                    status = 'Normal';
                    color = '#10b981'; // Green
                } else if (bmi >= 25 && bmi < 30) {
                    status = 'Overweight';
                    color = '#f59e0b'; // Amber
                } else {
                    status = 'Obese';
                    color = '#ef4444'; // Red
                }
                
                bmiVal.style.color = color;
                bmiStatus.innerText = status;
                bmiStatus.style.color = color;
            } else {
                bmiVal.innerText = '--';
                bmiVal.style.color = '#10b981';
                bmiStatus.innerText = '--';
                bmiStatus.style.color = 'rgba(255,255,255,0.4)';
            }
        }
        
        document.addEventListener('DOMContentLoaded', calculateBMI);
    </script>

    {{-- ===== CHANGE PASSWORD FORM ===== --}}
    <div class="fade-in-up delay-2" style="background:rgba(255,255,255,.03);border:1px solid rgba(139,92,246,.18);border-radius:24px;padding:2rem;margin-bottom:1.5rem;">
        <div style="display:flex;align-items:center;gap:12px;margin-bottom:1.6rem;padding-bottom:1rem;border-bottom:1px solid rgba(139,92,246,.12);">
            <div style="width:44px;height:44px;border-radius:14px;background:linear-gradient(135deg,rgba(59,130,246,.3),rgba(99,102,241,.2));display:flex;align-items:center;justify-content:center;font-size:1.3rem;">🔒</div>
            <div>
                <h2 style="font-size:1.05rem;font-weight:800;color:#e2d9f3;">Change Password</h2>
                <p style="font-size:.75rem;color:rgba(255,255,255,.3);">Use a strong, unique password for your account</p>
            </div>
        </div>

        <form method="POST" action="{{ route('profile.password') }}" style="display:flex;flex-direction:column;gap:1.1rem;">
            @csrf

            <div>
                <label style="display:block;font-size:.73rem;font-weight:700;color:rgba(196,181,253,.65);letter-spacing:.04em;margin-bottom:6px;">CURRENT PASSWORD</label>
                <input type="password" name="current_password"
                       style="width:100%;max-width:420px;padding:11px 14px;background:rgba(255,255,255,.05);border:1px solid {{ $errors->has('current_password') ? 'rgba(239,68,68,.5)' : 'rgba(139,92,246,.25)' }};border-radius:12px;color:#fff;font-size:.88rem;outline:none;"
                       onfocus="this.style.borderColor='rgba(139,92,246,.6)'" onblur="this.style.borderColor='rgba(139,92,246,.25)'"
                       placeholder="••••••••">
                @error('current_password')<p style="color:#f87171;font-size:.72rem;margin-top:3px;">{{ $message }}</p>@enderror
            </div>

            <div style="display:grid;grid-template-columns:1fr 1fr;gap:1rem;max-width:840px;">
                <div>
                    <label style="display:block;font-size:.73rem;font-weight:700;color:rgba(196,181,253,.65);letter-spacing:.04em;margin-bottom:6px;">NEW PASSWORD</label>
                    <input type="password" name="password"
                           style="width:100%;padding:11px 14px;background:rgba(255,255,255,.05);border:1px solid {{ $errors->has('password') ? 'rgba(239,68,68,.5)' : 'rgba(139,92,246,.25)' }};border-radius:12px;color:#fff;font-size:.88rem;outline:none;"
                           onfocus="this.style.borderColor='rgba(139,92,246,.6)'" onblur="this.style.borderColor='rgba(139,92,246,.25)'"
                           placeholder="Min. 8 characters">
                    @error('password')<p style="color:#f87171;font-size:.72rem;margin-top:3px;">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label style="display:block;font-size:.73rem;font-weight:700;color:rgba(196,181,253,.65);letter-spacing:.04em;margin-bottom:6px;">CONFIRM NEW PASSWORD</label>
                    <input type="password" name="password_confirmation"
                           style="width:100%;padding:11px 14px;background:rgba(255,255,255,.05);border:1px solid rgba(139,92,246,.25);border-radius:12px;color:#fff;font-size:.88rem;outline:none;"
                           onfocus="this.style.borderColor='rgba(139,92,246,.6)'" onblur="this.style.borderColor='rgba(139,92,246,.25)'"
                           placeholder="Repeat new password">
                </div>
            </div>

            <div>
                <button type="submit"
                        style="background:linear-gradient(135deg,#3b82f6,#6366f1);color:#fff;border:none;border-radius:12px;padding:13px 32px;font-size:.92rem;font-weight:700;cursor:pointer;box-shadow:0 8px 22px rgba(59,130,246,.3);transition:all .3s;"
                        onmouseover="this.style.transform='translateY(-2px)';this.style.boxShadow='0 14px 32px rgba(59,130,246,.5)'"
                        onmouseout="this.style.transform='';this.style.boxShadow='0 8px 22px rgba(59,130,246,.3)'">
                    Change Password 🔒
                </button>
            </div>
        </form>
    </div>

</div>
@endsection
