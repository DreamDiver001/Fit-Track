<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Models\User; use App\Models\Workout; use App\Models\Exercise; use App\Models\WorkoutLog; use App\Models\Goal;

class DashboardController extends Controller {
    public function index() {
        $stats = [
            'users'    => User::count(),
            'workouts' => Workout::count(),
            'exercises'=> Exercise::count(),
            'logs'     => WorkoutLog::count(),
            'goals'    => Goal::count(),
        ];
        $recentUsers = User::latest()->take(6)->get();
        $recentLogs  = WorkoutLog::with(['user','workout'])->latest()->take(5)->get();
        return view('admin.dashboard', compact('stats','recentUsers','recentLogs'));
    }
}
