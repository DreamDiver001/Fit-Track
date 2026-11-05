@extends('layouts.app')
@section('title','Log a Workout')
@section('content')
<div class="max-w-2xl mx-auto px-4 py-8">
    <h1 class="text-2xl font-bold text-gray-800 mb-1">Log a Workout</h1>
    <p class="text-gray-500 text-sm mb-6">Record your training session details.</p>
    @if($errors->any())
    <div class="alert alert-error text-sm mb-5">
        <div><strong>Whoops! Please fix the following errors:</strong>
            <ul class="list-disc ml-4 mt-1">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
        </div>
    </div>
    @endif
    <div class="bg-white rounded-2xl shadow-sm border p-6">
        <form method="POST" action="{{ route('logs.store') }}" class="space-y-5">
            @csrf
            <div>
                <label class="label"><span class="label-text font-medium">Workout *</span></label>
                <select name="workout_id" class="select select-bordered w-full @error('workout_id') select-error @enderror" required>
                    <option value="" disabled {{ old('workout_id') ? '' : 'selected' }}>Select a workout</option>
                    @foreach($workouts as $w)
                        <option value="{{ $w->id }}" {{ old('workout_id', request('workout_id')) == $w->id ? 'selected' : '' }}>{{ $w->title }}</option>
                    @endforeach
                </select>
                @error('workout_id')<span class="text-error text-xs">{{ $message }}</span>@enderror
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="label"><span class="label-text font-medium">Date *</span></label>
                    <input type="date" name="logged_at" value="{{ old('logged_at', date('Y-m-d')) }}" required class="input input-bordered w-full @error('logged_at') input-error @enderror"/>
                    @error('logged_at')<span class="text-error text-xs">{{ $message }}</span>@enderror
                </div>
                <div>
                    <label class="label"><span class="label-text font-medium">Duration (minutes)</span></label>
                    <input type="number" name="duration_minutes" value="{{ old('duration_minutes') }}" min="1" max="600" class="input input-bordered w-full" placeholder="e.g. 45"/>
                </div>
            </div>
            <div>
                <label class="label"><span class="label-text font-medium">How did it feel? *</span></label>
                <div class="flex gap-6 flex-wrap">
                    @foreach(['great'=>'😄 Great','okay'=>'😐 Okay','tough'=>'😓 Tough'] as $val=>$label)
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="radio" name="effort_rating" value="{{ $val }}" class="radio radio-success" {{ old('effort_rating','okay') === $val ? 'checked' : '' }}/>
                        <span class="text-sm">{{ $label }}</span>
                    </label>
                    @endforeach
                </div>
                @error('effort_rating')<span class="text-error text-xs">{{ $message }}</span>@enderror
            </div>
            <div>
                <label class="label"><span class="label-text font-medium">Calories Burned (optional)</span></label>
                <input type="number" name="calories_burned" value="{{ old('calories_burned') }}" min="0" class="input input-bordered w-full" placeholder="e.g. 420"/>
            </div>
            <div>
                <label class="label"><span class="label-text font-medium">Notes (optional)</span></label>
                <textarea name="notes" rows="3" class="textarea textarea-bordered w-full" placeholder="How did it go? Any personal records?">{{ old('notes') }}</textarea>
            </div>
            <div>
                <label class="label"><span class="label-text font-medium">Tags</span></label>
                <div class="flex flex-wrap gap-4">
                    @foreach(['morning'=>'🌅 Morning','personal_record'=>'🏆 Personal Record','outdoors'=>'🌳 Outdoors','with_partner'=>'👥 With Partner','gym'=>'🏢 Gym','home'=>'🏠 Home'] as $val=>$label)
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" name="tags[]" value="{{ $val }}" class="checkbox checkbox-success checkbox-sm" {{ in_array($val, old('tags',[])) ? 'checked' : '' }}/>
                        <span class="text-sm">{{ $label }}</span>
                    </label>
                    @endforeach
                </div>
            </div>
            <div class="flex gap-3 pt-2">
                <button type="submit" class="btn btn-success text-white flex-1">Save Workout Log</button>
                <a href="{{ route('logs.index') }}" class="btn btn-outline">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection
