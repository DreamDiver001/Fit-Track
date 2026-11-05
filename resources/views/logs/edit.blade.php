@extends('layouts.app')
@section('title','Edit Workout Log')
@section('content')
<div class="max-w-2xl mx-auto px-4 py-8">
    <h1 class="text-2xl font-bold text-gray-800 mb-1">Edit Workout Log</h1>
    <p class="text-gray-500 text-sm mb-6">Update your workout session details.</p>
    @if($errors->any())
    <div class="alert alert-error text-sm mb-5">
        <ul>@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
    </div>
    @endif
    <div class="bg-white rounded-2xl shadow-sm border p-6">
        <form method="POST" action="{{ route('logs.update', $log) }}" class="space-y-5">
            @csrf @method('PUT')
            <div>
                <label class="label"><span class="label-text font-medium">Workout *</span></label>
                <select name="workout_id" class="select select-bordered w-full" required>
                    @foreach($workouts as $w)
                        <option value="{{ $w->id }}" {{ old('workout_id', $log->workout_id) == $w->id ? 'selected' : '' }}>{{ $w->title }}</option>
                    @endforeach
                </select>
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="label"><span class="label-text font-medium">Date *</span></label>
                    <input type="date" name="logged_at" value="{{ old('logged_at', $log->logged_at->format('Y-m-d')) }}" required class="input input-bordered w-full"/>
                </div>
                <div>
                    <label class="label"><span class="label-text font-medium">Duration (minutes)</span></label>
                    <input type="number" name="duration_minutes" value="{{ old('duration_minutes', $log->duration_minutes) }}" min="1" class="input input-bordered w-full"/>
                </div>
            </div>
            <div>
                <label class="label"><span class="label-text font-medium">How did it feel? *</span></label>
                <div class="flex gap-6 flex-wrap">
                    @foreach(['great'=>'😄 Great','okay'=>'😐 Okay','tough'=>'😓 Tough'] as $val=>$label)
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="radio" name="effort_rating" value="{{ $val }}" class="radio radio-success" {{ old('effort_rating', $log->effort_rating) === $val ? 'checked' : '' }}/>
                        <span class="text-sm">{{ $label }}</span>
                    </label>
                    @endforeach
                </div>
            </div>
            <div>
                <label class="label"><span class="label-text font-medium">Calories Burned</span></label>
                <input type="number" name="calories_burned" value="{{ old('calories_burned', $log->calories_burned) }}" min="0" class="input input-bordered w-full"/>
            </div>
            <div>
                <label class="label"><span class="label-text font-medium">Notes</span></label>
                <textarea name="notes" rows="3" class="textarea textarea-bordered w-full">{{ old('notes', $log->notes) }}</textarea>
            </div>
            <div>
                <label class="label"><span class="label-text font-medium">Tags</span></label>
                <div class="flex flex-wrap gap-4">
                    @foreach(['morning'=>'🌅 Morning','personal_record'=>'🏆 Personal Record','outdoors'=>'🌳 Outdoors','with_partner'=>'👥 With Partner','gym'=>'🏢 Gym','home'=>'🏠 Home'] as $val=>$label)
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" name="tags[]" value="{{ $val }}" class="checkbox checkbox-success checkbox-sm" {{ in_array($val, old('tags', $log->tags_array)) ? 'checked' : '' }}/>
                        <span class="text-sm">{{ $label }}</span>
                    </label>
                    @endforeach
                </div>
            </div>
            <div class="flex gap-3 pt-2">
                <button type="submit" class="btn btn-success text-white flex-1">Update Log</button>
                <a href="{{ route('logs.index') }}" class="btn btn-outline">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection
