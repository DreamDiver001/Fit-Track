@extends('layouts.app')
@section('title','Create Workout')
@section('content')
<div class="max-w-2xl mx-auto px-4 py-8">
    <h1 class="text-2xl font-bold text-gray-800 mb-1">Create Workout</h1>
    <p class="text-gray-500 text-sm mb-6">Add a new workout to the public catalog.</p>
    @if($errors->any())
    <div class="alert alert-error text-sm mb-5">
        <ul>@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
    </div>
    @endif
    <div class="bg-white rounded-2xl shadow-sm border p-6">
        <form method="POST" action="{{ route('admin.workouts.store') }}" enctype="multipart/form-data" class="space-y-5">
            @csrf
            <div>
                <label class="label"><span class="label-text font-medium">Title *</span></label>
                <input type="text" name="title" value="{{ old('title') }}" required class="input input-bordered w-full @error('title') input-error @enderror" placeholder="e.g. Full Body HIIT"/>
                @error('title')<span class="text-error text-xs">{{ $message }}</span>@enderror
            </div>
            <div>
                <label class="label"><span class="label-text font-medium">Description</span></label>
                <textarea name="description" rows="3" class="textarea textarea-bordered w-full" placeholder="Describe the workout…">{{ old('description') }}</textarea>
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="label"><span class="label-text font-medium">Category *</span></label>
                    <select name="category" class="select select-bordered w-full" required>
                        @foreach(['cardio'=>'Cardio','strength'=>'Strength','flexibility'=>'Flexibility','hiit'=>'HIIT','core'=>'Core','full_body'=>'Full Body'] as $val=>$label)
                            <option value="{{ $val }}" {{ old('category') === $val ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="label"><span class="label-text font-medium">Difficulty *</span></label>
                    <select name="difficulty" class="select select-bordered w-full" required>
                        @foreach(['easy'=>'Easy','medium'=>'Medium','hard'=>'Hard'] as $val=>$label)
                            <option value="{{ $val }}" {{ old('difficulty') === $val ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="label"><span class="label-text font-medium">Duration (min)</span></label>
                    <input type="number" name="estimated_duration" value="{{ old('estimated_duration') }}" min="1" class="input input-bordered w-full" placeholder="45"/>
                </div>
                <div>
                    <label class="label"><span class="label-text font-medium">Calories</span></label>
                    <input type="number" name="estimated_calories" value="{{ old('estimated_calories') }}" min="0" class="input input-bordered w-full" placeholder="300"/>
                </div>
            </div>
            <div>
                <label class="label"><span class="label-text font-medium">Workout Image</span></label>
                <div class="border-2 border-dashed border-gray-300 rounded-xl p-5 bg-gray-50 text-center">
                    <div class="text-3xl mb-2">📷</div>
                    <p class="text-sm text-gray-500 mb-3">Upload a cover image (PNG, JPG, WEBP – max 2MB)</p>
                    <input type="file" name="image" class="file-input file-input-bordered file-input-sm w-full max-w-xs" accept="image/*"/>
                </div>
                @error('image')<span class="text-error text-xs">{{ $message }}</span>@enderror
            </div>
            <div class="flex items-center gap-3">
                <input type="checkbox" name="is_public" id="is_public" class="checkbox checkbox-success" checked/>
                <label for="is_public" class="text-sm font-medium cursor-pointer">Make this workout public</label>
            </div>
            <div>
                <label class="label"><span class="label-text font-medium">Assign Exercises (optional)</span></label>
                <div class="space-y-2 max-h-60 overflow-y-auto border rounded-xl p-3">
                    @foreach($exercises as $ex)
                    <label class="flex items-center gap-3 p-2 hover:bg-gray-50 rounded-lg cursor-pointer">
                        <input type="checkbox" name="exercise_ids[]" value="{{ $ex->id }}" class="checkbox checkbox-success checkbox-sm"/>
                        <div class="flex-1">
                            <span class="text-sm font-medium">{{ $ex->name }}</span>
                            <span class="badge badge-ghost badge-xs ml-1">{{ $ex->muscle_group_label }}</span>
                        </div>
                    </label>
                    @endforeach
                </div>
            </div>
            <div class="flex gap-3 pt-2">
                <button type="submit" class="btn btn-success text-white flex-1">Create Workout</button>
                <a href="{{ route('admin.workouts.index') }}" class="btn btn-outline">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection
