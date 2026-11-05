<?php
namespace App\Http\Controllers;
use App\Models\Exercise;
use Illuminate\Http\Request;

class ExerciseController extends Controller {
    public function index(Request $request) {
        $query = Exercise::query();
        if ($request->filled('search'))       $query->where('name','like','%'.$request->search.'%');
        if ($request->filled('muscle_group')) $query->where('muscle_group', $request->muscle_group);
        if ($request->filled('equipment'))    $query->where('equipment', $request->equipment);
        $exercises = $query->latest()->paginate(12)->withQueryString();
        return view('exercises.index', compact('exercises'));
    }
}
