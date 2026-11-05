@extends('layouts.app')
@section('title','Edit Exercise')
@section('content')
<div class="max-w-2xl mx-auto px-4 py-8">
    <h1 class="text-2xl font-bold text-gray-800 mb-1">Edit Exercise</h1>
    <p class="text-gray-500 text-sm mb-6">Update this exercise in the library.</p>
    @if($errors->any())
    <div class="alert alert-error text-sm mb-5">
        <ul>@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
    </div>
    @endif
    <div class="bg-white rounded-2xl shadow-sm border p-6">
        <form method="POST" action="{{ route('admin.exercises.update', $exercise) }}" enctype="multipart/form-data" class="space-y-5">
            @csrf @method('PUT')
            <div>
                <label class="label"><span class="label-text font-medium">Exercise Name *</span></label>
                <input type="text" name="name" value="{{ old('name', $exercise->name) }}" required class="input input-bordered w-full @error('name') input-error @enderror"/>
                @error('name')<span class="text-error text-xs">{{ $message }}</span>@enderror
            </div>
            <div>
                <label class="label"><span class="label-text font-medium">Description</span></label>
                <textarea name="description" rows="2" class="textarea textarea-bordered w-full">{{ old('description', $exercise->description) }}</textarea>
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="label"><span class="label-text font-medium">Muscle Group *</span></label>
                    <select name="muscle_group" class="select select-bordered w-full" required>
                        @foreach(['chest'=>'Chest','back'=>'Back','legs'=>'Legs','core'=>'Core','arms'=>'Arms','shoulders'=>'Shoulders','full_body'=>'Full Body'] as $val=>$label)
                            <option value="{{ $val }}" {{ old('muscle_group', $exercise->muscle_group) === $val ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="label"><span class="label-text font-medium">Equipment *</span></label>
                    <select name="equipment" class="select select-bordered w-full" required>
                        @foreach(['barbell'=>'Barbell','dumbbell'=>'Dumbbell','bodyweight'=>'Bodyweight','machine'=>'Machine','cable'=>'Cable','other'=>'Other'] as $val=>$label)
                            <option value="{{ $val }}" {{ old('equipment', $exercise->equipment) === $val ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div>
                <label class="label"><span class="label-text font-medium">Instructions</span></label>
                <textarea name="instructions" rows="4" class="textarea textarea-bordered w-full">{{ old('instructions', $exercise->instructions) }}</textarea>
            </div>
            <div>
                <label class="label"><span class="label-text font-medium">Exercise Image</span></label>
                @if($exercise->image_path)
                    <div class="mb-2">
                        <img src="{{ Storage::url($exercise->image_path) }}" class="h-24 rounded-lg object-cover" alt="current"/>
                        <p class="text-xs text-gray-400 mt-1">Current image – upload a new one to replace</p>
                    </div>
                @endif
                <div class="border-2 border-dashed border-gray-300 rounded-xl p-5 bg-gray-50 text-center">
                    <input type="file" name="image" class="file-input file-input-bordered file-input-sm w-full max-w-xs" accept="image/*"/>
                    <p class="text-xs text-gray-400 mt-2">PNG, JPG, WEBP – max 2MB. Leave blank to keep current.</p>
                </div>
                @error('image')<span class="text-error text-xs">{{ $message }}</span>@enderror
            </div>
            <div class="flex gap-3 pt-2">
                <button type="submit" class="btn btn-success text-white flex-1">Update Exercise</button>
                <a href="{{ route('admin.exercises.index') }}" class="btn btn-outline">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection
