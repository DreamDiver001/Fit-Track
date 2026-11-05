<?php
namespace App\Http\Controllers;
use App\Models\WorkoutLog;
use App\Models\Workout;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class WorkoutLogController extends Controller {
    public function index() {
        $logs = WorkoutLog::where('user_id',auth()->id())->with('workout')->latest('logged_at')->paginate(10);
        return view('logs.index', compact('logs'));
    }
    public function create() {
        $workouts = Workout::where('is_public',true)->orderBy('title')->get();
        return view('logs.create', compact('workouts'));
    }
    public function store(Request $request) {
        $data = $request->validate($this->rules());
        $data['user_id'] = auth()->id();
        $data['tags']    = !empty($data['tags']) ? implode(',', $data['tags']) : '';
        WorkoutLog::create($data);
        return redirect()->route('logs.index')->with('success','Workout logged successfully! 💪');
    }
    public function edit(WorkoutLog $log) {
        $this->authorize('update', $log);
        $workouts = Workout::where('is_public',true)->orderBy('title')->get();
        return view('logs.edit', compact('log','workouts'));
    }
    public function update(Request $request, WorkoutLog $log) {
        $this->authorize('update', $log);
        $data = $request->validate($this->rules());
        $data['tags'] = !empty($data['tags']) ? implode(',', $data['tags']) : '';
        $log->update($data);
        return redirect()->route('logs.index')->with('success','Workout log updated!');
    }
    public function destroy(WorkoutLog $log) {
        $this->authorize('delete', $log);
        $log->delete();
        return redirect()->route('logs.index')->with('success','Workout log deleted.');
    }

    /** Shared validation rules for store and update (previously duplicated). */
    private function rules(): array {
        return [
            // BUG-004: only public workouts can be logged (the form only lists those)
            'workout_id'       => ['required', Rule::exists('workouts', 'id')->where('is_public', true)],
            // BUG-007: a log records a workout that already happened
            'logged_at'        => 'required|date|before_or_equal:today',
            'duration_minutes' => 'nullable|integer|min:1|max:600',
            'calories_burned'  => 'nullable|integer|min:0',
            'effort_rating'    => 'required|in:great,okay,tough',
            'notes'            => 'nullable|string|max:1000',
            'tags'             => 'nullable|array',
            'tags.*'           => 'string',
        ];
    }
}
