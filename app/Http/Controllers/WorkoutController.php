<?php
namespace App\Http\Controllers;
use App\Models\Workout;
use Illuminate\Http\Request;

class WorkoutController extends Controller {
    public function index(Request $request) {
        $query = Workout::where('is_public', true);
        if ($request->filled('search'))     $query->where('title','like','%'.$request->search.'%');
        if ($request->filled('category'))   $query->where('category', $request->category);
        if ($request->filled('difficulty')) $query->where('difficulty', $request->difficulty);
        $workouts = $query->latest()->paginate(9)->withQueryString();
        return view('workouts.index', compact('workouts'));
    }
    public function show(Workout $workout) {
        // BUG-003: private workouts were reachable by guessing the URL
        abort_if(!$workout->is_public && !auth()->user()?->is_admin, 404);
        $workout->load('exercises');
        return view('workouts.show', compact('workout'));
    }
}
