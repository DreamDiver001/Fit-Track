@extends('layouts.app')
@section('title','Create Exercise')
@section('content')
<div class="max-w-2xl mx-auto px-4 py-8">
    <h1 class="text-2xl font-bold text-gray-800 mb-1">Create Exercise</h1>
    <p class="text-gray-500 text-sm mb-6">Add a new exercise to the public library.</p>
    @if($errors->any())
    <div class="alert alert-error text-sm mb-5">
        <div><strong>Whoops! Please fix the errors:</strong>
            <ul class="list-disc ml-4 mt-1">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
        </div>
    </div>
    @endif
    <div class="bg-white rounded-2xl shadow-sm border p-6">
        <form method="POST" action="{{ route('admin.exercises.store') }}" enctype="multipart/form-data" class="space-y-5">
            @csrf
            <div>
                <label class="label"><span class="label-text font-medium">Exercise Name *</span></label>
                <input type="text" name="name" value="{{ old('name') }}" required
                       class="input input-bordered w-full @error('name') input-error @enderror"
                       placeholder="e.g. Barbell Back Squat"/>
                @error('name')<span class="text-error text-xs">{{ $message }}</span>@enderror
            </div>
            <div>
                <label class="label"><span class="label-text font-medium">Description</span></label>
                <textarea name="description" rows="2" class="textarea textarea-bordered w-full" placeholder="Brief description…">{{ old('description') }}</textarea>
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="label"><span class="label-text font-medium">Muscle Group *</span></label>
                    <select name="muscle_group" class="select select-bordered w-full @error('muscle_group') select-error @enderror" required>
                        <option value="" disabled selected>Select…</option>
                        @foreach(['chest'=>'Chest','back'=>'Back','legs'=>'Legs','core'=>'Core','arms'=>'Arms','shoulders'=>'Shoulders','full_body'=>'Full Body'] as $val=>$label)
                            <option value="{{ $val }}" {{ old('muscle_group') === $val ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                    @error('muscle_group')<span class="text-error text-xs">{{ $message }}</span>@enderror
                </div>
                <div>
                    <label class="label"><span class="label-text font-medium">Equipment *</span></label>
                    <select name="equipment" class="select select-bordered w-full @error('equipment') select-error @enderror" required>
                        <option value="" disabled selected>Select…</option>
                        @foreach(['barbell'=>'Barbell','dumbbell'=>'Dumbbell','bodyweight'=>'Bodyweight','machine'=>'Machine','cable'=>'Cable','other'=>'Other'] as $val=>$label)
                            <option value="{{ $val }}" {{ old('equipment') === $val ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                    @error('equipment')<span class="text-error text-xs">{{ $message }}</span>@enderror
                </div>
            </div>
            <div>
                <label class="label"><span class="label-text font-medium">Instructions (optional)</span></label>
                <textarea name="instructions" rows="4" class="textarea textarea-bordered w-full" placeholder="Step-by-step instructions…">{{ old('instructions') }}</textarea>
            </div>
            <div>
                <label class="label"><span class="label-text font-medium">Exercise Image</span></label>
                <div class="border-2 border-dashed border-gray-300 rounded-xl p-5 bg-gray-50 text-center">
                    <div class="text-3xl mb-2">📷</div>
                    <p class="text-sm text-gray-500 mb-3">Upload a demonstration image</p>
                    <input type="file" name="image" class="file-input file-input-bordered file-input-sm w-full max-w-xs" accept="image/*"/>
                    <p class="text-xs text-gray-400 mt-2">PNG, JPG, WEBP – max 2MB</p>
                </div>
                @error('image')<span class="text-error text-xs">{{ $message }}</span>@enderror
            </div>
            <div class="flex gap-3 pt-2">
                <button type="submit" class="btn btn-success text-white flex-1">Save Exercise</button>
                <a href="{{ route('admin.exercises.index') }}" class="btn btn-outline">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection
