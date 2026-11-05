<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Models\Exercise;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ExerciseController extends Controller {
    public function index(Request $request) {
        $query = Exercise::withCount('workouts');
        if ($request->filled('search'))       $query->where('name','like','%'.$request->search.'%');
        if ($request->filled('muscle_group')) $query->where('muscle_group',$request->muscle_group);
        if ($request->filled('equipment'))    $query->where('equipment',$request->equipment);
        $exercises = $query->latest()->paginate(15)->withQueryString();
        return view('admin.exercises.index', compact('exercises'));
    }
    public function create() { return view('admin.exercises.create'); }
    public function store(Request $request) {
        $data = $request->validate([
            'name'         => 'required|string|max:255',
            'description'  => 'nullable|string',
            'muscle_group' => 'required|in:chest,back,legs,core,arms,shoulders,full_body',
            'equipment'    => 'required|in:barbell,dumbbell,bodyweight,machine,cable,other',
            'instructions' => 'nullable|string',
            'image'        => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);
        if ($request->hasFile('image')) {
            $data['image_path'] = $request->file('image')->store('exercises','public');
        }
        unset($data['image']);
        Exercise::create($data);
        return redirect()->route('admin.exercises.index')->with('success','Exercise created!');
    }
    public function edit(Exercise $exercise) { return view('admin.exercises.edit', compact('exercise')); }
    public function update(Request $request, Exercise $exercise) {
        $data = $request->validate([
            'name'         => 'required|string|max:255',
            'description'  => 'nullable|string',
            'muscle_group' => 'required|in:chest,back,legs,core,arms,shoulders,full_body',
            'equipment'    => 'required|in:barbell,dumbbell,bodyweight,machine,cable,other',
            'instructions' => 'nullable|string',
            'image'        => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);
        if ($request->hasFile('image')) {
            if ($exercise->image_path) Storage::disk('public')->delete($exercise->image_path);
            $data['image_path'] = $request->file('image')->store('exercises','public');
        }
        unset($data['image']);
        $exercise->update($data);
        return redirect()->route('admin.exercises.index')->with('success','Exercise updated!');
    }
    public function destroy(Exercise $exercise) {
        if ($exercise->image_path) Storage::disk('public')->delete($exercise->image_path);
        $exercise->delete();
        return back()->with('success','Exercise deleted.');
    }
}
