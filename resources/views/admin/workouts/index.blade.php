@extends('layouts.app')
@section('title','Manage Workouts')
@section('content')
<div class="max-w-5xl mx-auto px-4 py-8">
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Workout Management</h1>
        <a href="{{ route('admin.workouts.create') }}" class="btn btn-success text-white">+ Create Workout</a>
    </div>
    <form method="GET" class="flex gap-3 mb-5">
        <input name="search" value="{{ request('search') }}" placeholder="Search workouts…" class="input input-bordered flex-1"/>
        <button class="btn btn-success text-white">Search</button>
        @if(request('search'))<a href="{{ route('admin.workouts.index') }}" class="btn btn-ghost">Clear</a>@endif
    </form>
    <div class="bg-white rounded-2xl shadow-sm border overflow-hidden">
        <table class="table">
            <thead class="bg-gray-50"><tr><th>Title</th><th>Category</th><th>Difficulty</th><th>Exercises</th><th>Public</th><th>Actions</th></tr></thead>
            <tbody>
                @foreach($workouts as $w)
                <tr class="hover">
                    <td class="font-medium">{{ $w->title }}</td>
                    <td><span class="badge {{ $w->category_color }} badge-sm">{{ $w->category_label }}</span></td>
                    <td><span class="badge {{ $w->difficulty_color }} badge-sm">{{ ucfirst($w->difficulty) }}</span></td>
                    <td>{{ $w->exercises_count }}</td>
                    <td>{{ $w->is_public ? '✅' : '🔒' }}</td>
                    <td>
                        <div class="flex gap-1">
                            <a href="{{ route('admin.workouts.edit', $w) }}" class="btn btn-xs btn-ghost">✏️ Edit</a>
                            <form id="del-wk-{{ $w->id }}" method="POST" action="{{ route('admin.workouts.destroy', $w) }}">
                                @csrf @method('DELETE')
                                <button type="button" onclick="confirmAction('del-wk-{{ $w->id }}', 'Delete workout \'{{ addslashes($w->title) }}\'? This cannot be undone.')" class="btn btn-xs btn-error text-white">Delete</button>
                            </form>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div class="mt-5 flex justify-center">{{ $workouts->links() }}</div>
</div>
@endsection
