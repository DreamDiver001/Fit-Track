<?php
namespace App\Http\Controllers;
use App\Models\Goal;
use Illuminate\Http\Request;

class GoalController extends Controller {
    public function index() {
        $goals = Goal::where('user_id',auth()->id())->latest()->get();
        return view('goals.index', compact('goals'));
    }
    public function create() { return view('goals.create'); }
    public function store(Request $request) {
        $data = $request->validate([
            'title'         => 'required|string|max:255',
            'goal_type'     => 'required|in:weight_loss,endurance,strength,habit,custom',
            'target_value'  => 'nullable|numeric|min:0',
            'current_value' => 'nullable|numeric|min:0',
            'unit'          => 'nullable|string|max:50',
            'deadline'      => 'nullable|date|after:today',
            'notes'         => 'nullable|string|max:1000',
        ]);
        $data['user_id']    = auth()->id();
        $data['status']     = 'in_progress';
        // Store the starting value so progress can be calculated correctly later
        $data['start_value'] = $data['current_value'] ?? null;
        Goal::create($data);
        return redirect()->route('goals.index')->with('success','Goal created! 🎯');
    }
    public function edit(Goal $goal) {
        $this->authorize('update',$goal);
        return view('goals.edit', compact('goal'));
    }
    public function update(Request $request, Goal $goal) {
        $this->authorize('update',$goal);
        $data = $request->validate([
            'title'         => 'required|string|max:255',
            'goal_type'     => 'required|in:weight_loss,endurance,strength,habit,custom',
            'target_value'  => 'nullable|numeric|min:0',
            'current_value' => 'nullable|numeric|min:0',
            'unit'          => 'nullable|string|max:50',
            'deadline'      => 'nullable|date',
            'status'        => 'required|in:in_progress,completed,abandoned',
            'notes'         => 'nullable|string|max:1000',
        ]);
        // BUG-008: a goal created without a current value had no starting point,
        // so weight-loss progress stayed at 0% forever. Record it on first update.
        if ($goal->start_value === null && isset($data['current_value'])) {
            $data['start_value'] = $data['current_value'];
        }
        $goal->update($data);
        return redirect()->route('goals.index')->with('success','Goal updated!');
    }
    public function destroy(Goal $goal) {
        $this->authorize('delete',$goal);
        $goal->delete();
        return redirect()->route('goals.index')->with('success','Goal deleted.');
    }
}
