<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Models\Workout; use App\Models\Exercise;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class WorkoutController extends Controller {
    public function index(Request $request) {
        $query = Workout::withCount('exercises');
        if ($request->filled('search')) $query->where('title','like','%'.$request->search.'%');
        $workouts = $query->latest()->paginate(15)->withQueryString();
        return view('admin.workouts.index', compact('workouts'));
    }
    public function create() {
        $exercises = Exercise::orderBy('name')->get();
        return view('admin.workouts.create', compact('exercises'));
    }
    public function store(Request $request) {
        $data = $request->validate($this->rules());
        if ($request->hasFile('image')) {
            $data['image_path'] = $request->file('image')->store('workouts','public');
        }
        $data['is_public'] = $request->has('is_public') ? 1 : 0;
        unset($data['image'], $data['exercise_ids'], $data['ex_sets'], $data['ex_reps'], $data['ex_rest']);
        $workout = Workout::create($data);
        $this->syncExercises($request, $workout);
        return redirect()->route('admin.workouts.index')->with('success','Workout created!');
    }
    public function edit(Workout $workout) {
        $exercises = Exercise::orderBy('name')->get();
        $workout->load('exercises');
        return view('admin.workouts.edit', compact('workout','exercises'));
    }
    public function update(Request $request, Workout $workout) {
        $data = $request->validate($this->rules());
        if ($request->hasFile('image')) {
            if ($workout->image_path) Storage::disk('public')->delete($workout->image_path);
            $data['image_path'] = $request->file('image')->store('workouts','public');
        }
        $data['is_public'] = $request->has('is_public') ? 1 : 0;
        unset($data['image'], $data['exercise_ids'], $data['ex_sets'], $data['ex_reps'], $data['ex_rest']);
        $workout->update($data);
        $this->syncExercises($request, $workout);
        return redirect()->route('admin.workouts.index')->with('success','Workout updated!');
    }
    public function destroy(Workout $workout) {
        if ($workout->image_path) Storage::disk('public')->delete($workout->image_path);
        $workout->delete();
        return back()->with('success','Workout deleted.');
    }

    private function rules(): array {
        return [
            'title'              => 'required|string|max:255',
            'description'        => 'nullable|string',
            'category'           => 'required|in:cardio,strength,flexibility,hiit,core,full_body',
            'difficulty'         => 'required|in:easy,medium,hard',
            'estimated_duration' => 'nullable|integer|min:1',
            'estimated_calories' => 'nullable|integer|min:0',
            'image'              => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            // BUG-006: unknown exercise ids used to crash with a SQL foreign-key error (500)
            'exercise_ids'       => 'nullable|array',
            'exercise_ids.*'     => 'integer|distinct|exists:exercises,id',
            'ex_sets.*'          => 'nullable|integer|min:1',
            'ex_reps.*'          => 'nullable|integer|min:1',
            'ex_rest.*'          => 'nullable|integer|min:0',
        ];
    }

    /**
     * BUG-001: the edit form only sends exercise_ids, so the old code re-synced every
     * exercise with sets/reps/rest = null and wiped the existing plan. Now values that
     * are not submitted are kept from the current pivot row, and the existing order is kept.
     */
    private function syncExercises(Request $request, Workout $workout): void {
        $existing  = $workout->exercises()->get()->keyBy('id');
        $nextOrder = (int) $existing->max('pivot.sort_order');
        $sync = [];
        foreach (array_values((array) $request->input('exercise_ids', [])) as $i => $exId) {
            $old = $existing->get((int) $exId)?->pivot;
            $sync[(int) $exId] = [
                'sets'         => $request->input("ex_sets.$i", $old?->sets),
                'reps'         => $request->input("ex_reps.$i", $old?->reps),
                'rest_seconds' => $request->input("ex_rest.$i", $old?->rest_seconds),
                'sort_order'   => $old?->sort_order ?? ++$nextOrder,
            ];
        }
        $workout->exercises()->sync($sync);
    }
}
