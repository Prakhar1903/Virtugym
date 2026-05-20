<?php

namespace App\Http\Controllers;

use App\Models\WaterIntake;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class WaterIntakeController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $today = Carbon::today();
        
        $intakes = WaterIntake::where('user_id', $user->id)
            ->where('date', $today)
            ->get();
            
        $totalToday = $intakes->sum('amount_ml');
        $goal = $user->daily_water_goal ?? 3000; // Default goal: 3L
        $percentage = min(100, round(($totalToday / $goal) * 100));
        
        $history = WaterIntake::where('user_id', $user->id)
            ->where('date', '>=', Carbon::today()->subDays(6))
            ->get()
            ->groupBy(function($date) {
                return Carbon::parse($date->date)->format('D');
            })
            ->map(function($day) {
                return $day->sum('amount_ml');
            });

        // Calculate streak over last 30 days
        $allIntakes = WaterIntake::where('user_id', $user->id)
            ->where('date', '>=', Carbon::today()->subDays(30))
            ->get()
            ->groupBy(function($log) {
                return Carbon::parse($log->date)->format('Y-m-d');
            });

        $streak = 0;
        $checkDate = Carbon::today();
        
        // If today has met the goal, start checking from today
        $todayTotal = isset($allIntakes[$checkDate->format('Y-m-d')]) ? $allIntakes[$checkDate->format('Y-m-d')]->sum('amount_ml') : 0;
        
        if ($todayTotal >= $goal) {
            $streak = 1;
            $checkDate->subDay();
            while (true) {
                $dateStr = $checkDate->format('Y-m-d');
                $dayTotal = isset($allIntakes[$dateStr]) ? $allIntakes[$dateStr]->sum('amount_ml') : 0;
                if ($dayTotal >= $goal) {
                    $streak++;
                    $checkDate->subDay();
                } else {
                    break;
                }
            }
        } else {
            // Today hasn't met the goal yet, check yesterday and backwards
            $checkDate->subDay();
            while (true) {
                $dateStr = $checkDate->format('Y-m-d');
                $dayTotal = isset($allIntakes[$dateStr]) ? $allIntakes[$dateStr]->sum('amount_ml') : 0;
                if ($dayTotal >= $goal) {
                    $streak++;
                    $checkDate->subDay();
                } else {
                    break;
                }
            }
        }

        return view('water.index', compact('totalToday', 'goal', 'percentage', 'history', 'intakes', 'streak'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'amount_ml' => 'required|integer|min:50|max:2000',
        ]);

        WaterIntake::create([
            'user_id' => Auth::id(),
            'amount_ml' => $request->amount_ml,
            'date' => Carbon::today(),
        ]);

        if ($request->expectsJson()) {
            return response()->json(['success' => true]);
        }

        return redirect()->back()->with('success', 'Water intake added!');
    }

    public function updateGoal(Request $request)
    {
        $request->validate([
            'daily_water_goal' => 'required|integer|min:500|max:10000',
        ]);

        $user = Auth::user();
        $user->daily_water_goal = $request->daily_water_goal;
        $user->save();

        return redirect()->back()->with('success', 'Daily water goal updated successfully!');
    }

    public function destroy($id)
    {
        $intake = WaterIntake::where('user_id', Auth::id())->where('id', $id)->firstOrFail();
        $intake->delete();

        return redirect()->back()->with('success', 'Water log deleted successfully!');
    }
}
