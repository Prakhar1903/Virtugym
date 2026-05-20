<?php

namespace App\Http\Controllers;

use App\Models\TrainerAvailability;
use App\Models\Booking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class TrainerAvailabilityController extends Controller
{
    public function index()
    {
        $trainer = Auth::user();
        $availabilities = TrainerAvailability::where('trainer_id', $trainer->id)
            ->orderBy('day_of_week')
            ->orderBy('start_time')
            ->get();

        $startOfWeek = now()->startOfWeek();
        $endOfWeek = now()->endOfWeek();
        
        // Weekly Stats
        $confirmedBookings = Booking::where('trainer_id', $trainer->id)
            ->whereBetween('session_date', [$startOfWeek, $endOfWeek])
            ->where('status', 'confirmed')
            ->get();
            
        $weeklyBookingsCount = $confirmedBookings->count();
        $weeklyEarnings = $confirmedBookings->sum('amount');
        
        $cancelledBookingsCount = Booking::where('trainer_id', $trainer->id)
            ->whereBetween('session_date', [$startOfWeek, $endOfWeek])
            ->where('status', 'cancelled')
            ->count();
            
        // Weekly capacity is simply the total count of availability slot configurations
        $totalSlotsCount = $availabilities->count(); 
        
        // Today's Bookings
        $todaysBookings = Booking::where('trainer_id', $trainer->id)
            ->whereDate('session_date', now()->toDateString())
            ->where('status', 'confirmed')
            ->get();

        // Group availabilities by day for the calendar view
        $groupedAvailabilities = $availabilities->groupBy('day_of_week');

        // Load all weekly confirmed bookings in memory to prevent heavy database queries in loop
        $weeklyBookings = Booking::where('trainer_id', $trainer->id)
            ->whereBetween('session_date', [$startOfWeek, $endOfWeek])
            ->where('status', 'confirmed')
            ->get();

        // Check each slot's booked status for the current week specifically
        foreach ($availabilities as $slot) {
            $slotTime = \Carbon\Carbon::parse($slot->start_time)->format('H:i');
            
            $slot->is_booked_this_week = $weeklyBookings->contains(function ($booking) use ($slot, $slotTime) {
                // Match day of week
                $bookingDay = (int)\Carbon\Carbon::parse($booking->session_date)->format('w');
                if ($bookingDay !== (int)$slot->day_of_week) {
                    return false;
                }
                
                // Match start time
                $bookingTime = \Carbon\Carbon::parse($booking->session_date)->format('H:i');
                return $bookingTime === $slotTime;
            });
                
            $slot->bookings_count = Booking::where('trainer_id', $trainer->id)
                ->where('day_of_week', (int)$slot->day_of_week)
                ->where('status', 'confirmed')
                ->count();
        }
            
        return view('trainer.availability', compact(
            'availabilities', 
            'groupedAvailabilities', 
            'weeklyBookingsCount', 
            'totalSlotsCount',
            'weeklyEarnings',
            'cancelledBookingsCount',
            'todaysBookings'
        ));
    }
    
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'day_of_week' => 'sometimes|array',
            'day_of_week.*' => 'integer|min:0|max:6',
            'single_day' => 'sometimes|integer|min:0|max:6',
            'start_time' => 'required',
            'end_time' => 'required',
            'session_type' => 'nullable|string',
            'is_recurring' => 'nullable'
        ]);
        
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $days = $request->day_of_week ?? [$request->single_day];

        $newStart = \Carbon\Carbon::parse($request->start_time)->format('H:i');
        $newEnd = \Carbon\Carbon::parse($request->end_time)->format('H:i');

        if ($newStart >= $newEnd) {
            return redirect()->back()->with('error', 'End time must be after start time.');
        }

        foreach ($days as $day) {
            // Check for overlapping availability slots
            $existingSlots = TrainerAvailability::where('trainer_id', Auth::id())
                ->where('day_of_week', (int)$day)
                ->get();
                
            $hasOverlap = false;
            foreach ($existingSlots as $slot) {
                $slotStart = \Carbon\Carbon::parse($slot->start_time)->format('H:i');
                $slotEnd = \Carbon\Carbon::parse($slot->end_time)->format('H:i');
                
                if ($newStart < $slotEnd && $newEnd > $slotStart) {
                    $hasOverlap = true;
                    break;
                }
            }

            if ($hasOverlap) {
                return redirect()->back()->with('error', 'The selected time slot overlaps with an existing availability.');
            }

            TrainerAvailability::create([
                'trainer_id' => Auth::id(),
                'day_of_week' => (int)$day,
                'start_time' => $request->start_time,
                'end_time' => $request->end_time,
                'session_type' => $request->session_type ?? 'General',
                'is_recurring' => $request->has('is_recurring'),
                'is_booked' => false
            ]);
        }
        
        return redirect()->back()->with('success', 'Availability slots added successfully!');
    }

    public function update(Request $request, $id)
    {
        $availability = TrainerAvailability::where('trainer_id', Auth::id())->findOrFail($id);
        
        $validator = Validator::make($request->all(), [
            'start_time' => 'required',
            'end_time' => 'required',
            'session_type' => 'nullable|string',
            'is_recurring' => 'nullable'
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator);
        }

        $newStart = \Carbon\Carbon::parse($request->start_time)->format('H:i');
        $newEnd = \Carbon\Carbon::parse($request->end_time)->format('H:i');

        if ($newStart >= $newEnd) {
            return redirect()->back()->with('error', 'End time must be after start time.');
        }

        $existingSlots = TrainerAvailability::where('trainer_id', Auth::id())
            ->where('day_of_week', $availability->day_of_week)
            ->where('_id', '!=', $id)
            ->get();
            
        $hasOverlap = false;
        foreach ($existingSlots as $slot) {
            $slotStart = \Carbon\Carbon::parse($slot->start_time)->format('H:i');
            $slotEnd = \Carbon\Carbon::parse($slot->end_time)->format('H:i');
            
            if ($newStart < $slotEnd && $newEnd > $slotStart) {
                $hasOverlap = true;
                break;
            }
        }

        if ($hasOverlap) {
            return redirect()->back()->with('error', 'The updated time slot overlaps with an existing availability.');
        }

        $availability->update([
            'start_time' => $request->start_time,
            'end_time' => $request->end_time,
            'session_type' => $request->session_type ?? $availability->session_type,
            'is_recurring' => $request->has('is_recurring')
        ]);

        return redirect()->back()->with('success', 'Slot updated successfully!');
    }
    
    public function destroy($id)
    {
        $availability = TrainerAvailability::where('trainer_id', Auth::id())->findOrFail($id);
        $availability->delete();
        
        return redirect()->back()->with('success', 'Availability slot removed!');
    }
    
    public function getAvailableSlots($trainer_id, $date)
    {
        $dayOfWeek = (int)date('w', strtotime($date));
        
        // Debug
        \Log::info('Searching for slots - Trainer: ' . $trainer_id . ', Day: ' . $dayOfWeek);
        
        $slots = TrainerAvailability::where('trainer_id', $trainer_id)
            ->where('day_of_week', $dayOfWeek)
            ->get();

        $bookedTimes = Booking::where('trainer_id', $trainer_id)
            ->whereDate('session_date', $date)
            ->where('status', 'confirmed')
            ->get()
            ->map(function ($booking) {
                return date('H:i', strtotime($booking->session_date));
            })
            ->all();
        
        \Log::info('Found ' . $slots->count() . ' slots');
        
        $availableSlots = [];
        foreach ($slots as $slot) {
            $startTime = date('H:i', strtotime($slot->start_time));

            if (in_array($startTime, $bookedTimes, true)) {
                continue;
            }

            $availableSlots[] = [
                'id' => (string)$slot->_id,
                'start_time' => $startTime,
                'end_time' => date('H:i', strtotime($slot->end_time))
            ];
        }
        
        return response()->json($availableSlots);
    }
}
