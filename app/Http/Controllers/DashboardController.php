<?php
namespace App\Http\Controllers;
use App\Models\WorkoutLog;
use App\Models\Goal;
use Carbon\Carbon;

class DashboardController extends Controller {
    public function index() {
        $uid = auth()->id();
        $recentLogs   = WorkoutLog::where('user_id',$uid)->with('workout')->latest('logged_at')->take(5)->get();
        $goals        = Goal::where('user_id',$uid)->where('status','in_progress')->latest()->take(3)->get();
        $totalLogs    = WorkoutLog::where('user_id',$uid)->count();
        $totalGoals   = Goal::where('user_id',$uid)->count();
        $totalMinutes = (int) WorkoutLog::where('user_id',$uid)->sum('duration_minutes');
        $streak = 0;
        $day = Carbon::today();
        while (WorkoutLog::where('user_id',$uid)->whereDate('logged_at',$day)->exists()) {
            $streak++;
            $day = $day->copy()->subDay();
            if ($streak > 365) break;
        }
        return view('dashboard', compact('recentLogs','goals','totalLogs','totalGoals','totalMinutes','streak'));
    }
}
