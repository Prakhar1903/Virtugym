<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Show the profile edit form.
     */
    public function edit(Request $request): View
    {
        $user = $request->user();
        if ($user->name === strtoupper($user->name)) {
            $user->name = ucwords(strtolower($user->name));
        }
        return view('profile.edit', [
            'user' => $user,
        ]);
    }

    /**
     * Update profile information (name, email, fitness data, etc.)
     */
    public function update(Request $request): RedirectResponse
    {
        $user = $request->user();
        
        $rules = [
            'name'             => ['required', 'string', 'max:255'],
            'email'            => ['required', 'email', 'max:255'],
            'dob'              => ['required', 'date', 'before:today'],
            'gender'           => ['required', 'in:male,female,other'],
            'profile_photo'    => ['nullable', 'image', 'max:2048'],
        ];

        if ($user->role === 'trainee') {
            $rules['weight']        = ['required', 'numeric', 'min:20', 'max:300'];
            $rules['target_weight'] = ['required', 'numeric', 'min:20', 'max:300'];
            $rules['height']        = ['required', 'numeric', 'min:50', 'max:250'];
            $rules['fitness_level'] = ['required', 'in:beginner,intermediate,advanced,expert'];
            $rules['goal']          = ['required', 'in:weight_loss,muscle_gain,endurance,flexibility,general_fitness'];
            $rules['workout_days']  = ['required', 'integer', 'min:1', 'max:7'];
            $rules['injuries']      = ['nullable', 'string', 'max:1000'];
            $rules['upi_id']        = ['nullable', 'string', 'max:100'];
        } else {
            $rules['bio']              = ['nullable', 'string', 'max:1000'];
            $rules['specialization']   = ['nullable', 'string', 'max:255'];
            $rules['experience_years'] = ['nullable', 'integer', 'min:0', 'max:50'];
            $rules['hourly_rate']      = ['nullable', 'numeric', 'min:0'];
            $rules['certifications']   = ['nullable', 'string', 'max:1000'];
            $rules['languages']        = ['nullable', 'string', 'max:255'];
            $rules['session_types']    = ['nullable', 'array'];
            $rules['portfolio_link']   = ['nullable', 'url', 'max:255'];
        }

        $request->validate($rules);

        $user->fill($request->only([
            'name', 'email', 'gender', 'weight', 'height', 'target_weight',
            'fitness_level', 'goal', 'workout_days', 'injuries',
            'bio', 'specialization', 'experience_years', 'hourly_rate',
            'upi_id', 'certifications', 'languages', 'portfolio_link', 'dob'
        ]));
        
        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }
        
        if ($request->input('dob')) {
            $user->age = \Carbon\Carbon::parse($request->input('dob'))->age;
        }
        
        $user->equipment = $request->input('equipment', []);
        $user->session_types = $request->input('session_types', []);

        if ($request->hasFile('profile_photo')) {
            $path = $request->file('profile_photo')->store('profile_photos', 'public');
            $user->profile_photo = $path;
        }

        $user->save();

        return Redirect::route('profile.edit')->with('success', 'Profile updated successfully! ✅');
    }

    /**
     * Change password.
     */
    public function updatePassword(Request $request): RedirectResponse
    {
        $request->validate([
            'current_password' => ['required'],
            'password'         => ['required', 'confirmed', Password::min(8)],
        ]);

        $user = $request->user();

        if (!Hash::check($request->current_password, $user->password)) {
            return back()->withErrors(['current_password' => 'The current password is incorrect.'])->withInput();
        }

        $user->password = Hash::make($request->password);
        $user->save();

        return Redirect::route('profile.edit')->with('success', 'Password changed successfully! 🔒');
    }

    /**
     * Delete account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();
        Auth::logout();
        $user->delete();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}
