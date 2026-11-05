@extends('layouts.app')
@section('title','Edit Goal')
@section('content')
<div class="max-w-xl mx-auto px-4 py-8">
    <h1 class="text-2xl font-bold text-gray-800 mb-1">Edit Goal</h1>
    <p class="text-gray-500 text-sm mb-6">Update your progress or goal details.</p>
    @if($errors->any())
    <div class="alert alert-error text-sm mb-5">
        <ul>@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
    </div>
    @endif
    <div class="bg-white rounded-2xl shadow-sm border p-6">
        <form method="POST" action="{{ route('goals.update', $goal) }}" class="space-y-5">
            @csrf @method('PUT')
            <div>
                <label class="label"><span class="label-text font-medium">Goal Title *</span></label>
                <input type="text" name="title" value="{{ old('title', $goal->title) }}" required class="input input-bordered w-full"/>
            </div>
            <div>
                <label class="label"><span class="label-text font-medium">Goal Type *</span></label>
                <select name="goal_type" class="select select-bordered w-full" required>
                    @foreach(['weight_loss'=>'Weight Loss','endurance'=>'Endurance','strength'=>'Strength','habit'=>'Habit','custom'=>'Custom'] as $val=>$label)
                        <option value="{{ $val }}" {{ old('goal_type', $goal->goal_type) === $val ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div class="grid grid-cols-3 gap-3">
                <div>
                    <label class="label"><span class="label-text font-medium">Current Value</span></label>
                    <input type="number" name="current_value" value="{{ old('current_value', $goal->current_value) }}" step="0.01" class="input input-bordered w-full"/>
                    <span class="text-xs text-gray-400">Update as you progress</span>
                </div>
                <div>
                    <label class="label"><span class="label-text font-medium">Target Value</span></label>
                    <input type="number" name="target_value" value="{{ old('target_value', $goal->target_value) }}" step="0.01" class="input input-bordered w-full"/>
                    <span class="text-xs text-gray-400">Started at: {{ $goal->start_value ?? '?' }}</span>
                </div>
                <div>
                    <label class="label"><span class="label-text font-medium">Unit</span></label>
                    <input type="text" name="unit" value="{{ old('unit', $goal->unit) }}" class="input input-bordered w-full"/>
                </div>
            </div>
            <div>
                <label class="label"><span class="label-text font-medium">Deadline</span></label>
                <input type="date" name="deadline" value="{{ old('deadline', $goal->deadline?->format('Y-m-d')) }}" class="input input-bordered w-full"/>
            </div>
            <div>
                <label class="label"><span class="label-text font-medium">Status *</span></label>
                <select name="status" class="select select-bordered w-full" required>
                    @foreach(['in_progress'=>'In Progress','completed'=>'Completed','abandoned'=>'Abandoned'] as $val=>$label)
                        <option value="{{ $val }}" {{ old('status', $goal->status) === $val ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="label"><span class="label-text font-medium">Notes</span></label>
                <textarea name="notes" rows="3" class="textarea textarea-bordered w-full">{{ old('notes', $goal->notes) }}</textarea>
            </div>
            <div class="flex gap-3 pt-2">
                <button type="submit" class="btn btn-success text-white flex-1">Update Goal</button>
                <a href="{{ route('goals.index') }}" class="btn btn-outline">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection
