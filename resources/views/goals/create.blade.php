@extends('layouts.app')
@section('title','Create Goal')
@section('content')
<div class="max-w-xl mx-auto px-4 py-8">
    <h1 class="text-2xl font-bold text-gray-800 mb-1">Create a New Goal</h1>
    <p class="text-gray-500 text-sm mb-6">Set a measurable fitness goal to keep yourself on track.</p>
    @if($errors->any())
    <div class="alert alert-error text-sm mb-5">
        <ul>@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
    </div>
    @endif
    <div class="bg-white rounded-2xl shadow-sm border p-6">
        <form method="POST" action="{{ route('goals.store') }}" class="space-y-5">
            @csrf
            <div>
                <label class="label"><span class="label-text font-medium">Goal Title *</span></label>
                <input type="text" name="title" value="{{ old('title') }}" required
                       class="input input-bordered w-full @error('title') input-error @enderror"
                       placeholder="e.g. Lose 5kg by June 2026"/>
                @error('title')<span class="text-error text-xs">{{ $message }}</span>@enderror
            </div>
            <div>
                <label class="label"><span class="label-text font-medium">Goal Type *</span></label>
                <select name="goal_type" class="select select-bordered w-full" required>
                    @foreach(['weight_loss'=>'Weight Loss','endurance'=>'Endurance','strength'=>'Strength','habit'=>'Habit','custom'=>'Custom'] as $val=>$label)
                        <option value="{{ $val }}" {{ old('goal_type') === $val ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div class="grid grid-cols-3 gap-3">
                <div>
                    <label class="label"><span class="label-text font-medium">Starting Value</span></label>
                    <input type="number" name="current_value" value="{{ old('current_value') }}" step="0.01" min="0" class="input input-bordered w-full" placeholder="e.g. 120"/>
                    <span class="text-xs text-gray-400">Your value right now</span>
                </div>
                <div>
                    <label class="label"><span class="label-text font-medium">Target Value</span></label>
                    <input type="number" name="target_value" value="{{ old('target_value') }}" step="0.01" min="0" class="input input-bordered w-full" placeholder="e.g. 100"/>
                    <span class="text-xs text-gray-400">What you want to reach</span>
                </div>
                <div>
                    <label class="label"><span class="label-text font-medium">Unit</span></label>
                    <input type="text" name="unit" value="{{ old('unit') }}" class="input input-bordered w-full" placeholder="kg / min"/>
                </div>
            </div>
            <div>
                <label class="label"><span class="label-text font-medium">Deadline (optional)</span></label>
                <input type="date" name="deadline" value="{{ old('deadline') }}" class="input input-bordered w-full"/>
            </div>
            <div>
                <label class="label"><span class="label-text font-medium">Notes (optional)</span></label>
                <textarea name="notes" rows="3" class="textarea textarea-bordered w-full" placeholder="Any extra context or motivation…">{{ old('notes') }}</textarea>
            </div>
            <div class="flex gap-3 pt-2">
                <button type="submit" class="btn btn-success text-white flex-1">Create Goal 🎯</button>
                <a href="{{ route('goals.index') }}" class="btn btn-outline">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection
