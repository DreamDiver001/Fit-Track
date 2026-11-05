@extends('layouts.app')
@section('title','Manage Exercises')
@section('content')
<div class="max-w-5xl mx-auto px-4 py-8">
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Exercise Management</h1>
        <a href="{{ route('admin.exercises.create') }}" class="btn btn-success text-white">+ Create Exercise</a>
    </div>
    <form method="GET" class="flex flex-wrap gap-3 mb-5">
        <input name="search" value="{{ request('search') }}" placeholder="Search exercises…" class="input input-bordered flex-1 min-w-40"/>
        <select name="muscle_group" class="select select-bordered">
            <option value="">All Muscle Groups</option>
            @foreach(['chest'=>'Chest','back'=>'Back','legs'=>'Legs','core'=>'Core','arms'=>'Arms','shoulders'=>'Shoulders','full_body'=>'Full Body'] as $val=>$label)
                <option value="{{ $val }}" {{ request('muscle_group') === $val ? 'selected' : '' }}>{{ $label }}</option>
            @endforeach
        </select>
        <select name="equipment" class="select select-bordered">
            <option value="">All Equipment</option>
            @foreach(['barbell'=>'Barbell','dumbbell'=>'Dumbbell','bodyweight'=>'Bodyweight','machine'=>'Machine','cable'=>'Cable','other'=>'Other'] as $val=>$label)
                <option value="{{ $val }}" {{ request('equipment') === $val ? 'selected' : '' }}>{{ $label }}</option>
            @endforeach
        </select>
        <button class="btn btn-success text-white">Search</button>
        @if(request()->hasAny(['search','muscle_group','equipment']))<a href="{{ route('admin.exercises.index') }}" class="btn btn-ghost">Clear</a>@endif
    </form>
    <div class="bg-white rounded-2xl shadow-sm border overflow-hidden">
        <table class="table">
            <thead class="bg-gray-50"><tr><th>Exercise</th><th>Muscle Group</th><th>Equipment</th><th>Used In</th><th>Actions</th></tr></thead>
            <tbody>
                @foreach($exercises as $ex)
                <tr class="hover">
                    <td>
                        <div class="flex items-center gap-2">
                            @if($ex->image_path)
                                <img src="{{ Storage::url($ex->image_path) }}" class="w-9 h-9 rounded-lg object-cover"/>
                            @else
                                <div class="w-9 h-9 rounded-lg bg-gray-100 flex items-center justify-center text-lg">💪</div>
                            @endif
                            <span class="font-medium text-sm">{{ $ex->name }}</span>
                        </div>
                    </td>
                    <td><span class="badge badge-outline badge-sm">{{ $ex->muscle_group_label }}</span></td>
                    <td><span class="badge badge-ghost badge-sm">{{ $ex->equipment_label }}</span></td>
                    <td class="text-sm text-gray-500">{{ $ex->workouts_count }} workouts</td>
                    <td>
                        <div class="flex gap-1">
                            <a href="{{ route('admin.exercises.edit', $ex) }}" class="btn btn-xs btn-ghost">✏️ Edit</a>
                            <form id="del-ex-{{ $ex->id }}" method="POST" action="{{ route('admin.exercises.destroy', $ex) }}">
                                @csrf @method('DELETE')
                                <button type="button" onclick="confirmAction('del-ex-{{ $ex->id }}', 'Delete exercise \'{{ addslashes($ex->name) }}\'? This cannot be undone.')" class="btn btn-xs btn-error text-white">Delete</button>
                            </form>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div class="mt-5 flex justify-center">{{ $exercises->links() }}</div>
</div>
@endsection
