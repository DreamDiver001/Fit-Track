@extends('layouts.app')
@section('title', $workout->title)
@section('content')
<div class="max-w-4xl mx-auto px-4 py-8">
    <div class="text-sm breadcrumbs mb-4">
        <ul>
            <li><a href="{{ route('home') }}" class="text-green-600">Home</a></li>
            <li><a href="{{ route('workouts.index') }}" class="text-green-600">Workouts</a></li>
            <li>{{ $workout->title }}</li>
        </ul>
    </div>
    <div class="flex flex-wrap gap-2 mb-2">
        <span class="badge {{ $workout->category_color }}">{{ $workout->category_label }}</span>
        <span class="badge {{ $workout->difficulty_color }}">{{ ucfirst($workout->difficulty) }}</span>
    </div>
    <h1 class="text-3xl font-extrabold text-gray-800 mb-2">{{ $workout->title }}</h1>
    <p class="text-gray-500 mb-6">{{ $workout->description }}</p>
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8">
        <div class="bg-white rounded-xl p-4 text-center shadow-sm border"><div class="text-2xl font-bold text-green-600">{{ $workout->estimated_duration ?? '–' }}</div><div class="text-xs text-gray-500">Minutes</div></div>
        <div class="bg-white rounded-xl p-4 text-center shadow-sm border"><div class="text-2xl font-bold text-blue-600">{{ $workout->exercises->count() }}</div><div class="text-xs text-gray-500">Exercises</div></div>
        <div class="bg-white rounded-xl p-4 text-center shadow-sm border"><div class="text-2xl font-bold text-red-500">{{ ucfirst($workout->difficulty) }}</div><div class="text-xs text-gray-500">Difficulty</div></div>
        <div class="bg-white rounded-xl p-4 text-center shadow-sm border"><div class="text-2xl font-bold text-orange-500">{{ $workout->estimated_calories ?? '–' }}</div><div class="text-xs text-gray-500">~Calories</div></div>
    </div>
    @if($workout->exercises->isNotEmpty())
    <div class="bg-white rounded-2xl shadow-sm border p-6 mb-6">
        <h2 class="text-lg font-bold mb-4">Exercises in this Workout</h2>
        <div class="overflow-x-auto">
            <table class="table table-zebra">
                <thead><tr><th>#</th><th>Exercise</th><th>Sets</th><th>Reps</th><th>Rest</th><th>Muscle Group</th></tr></thead>
                <tbody>
                    @foreach($workout->exercises as $i => $exercise)
                    <tr>
                        <td>{{ $i+1 }}</td>
                        <td class="font-medium">{{ $exercise->name }}</td>
                        <td>{{ $exercise->pivot->sets ?? '–' }}</td>
                        <td>{{ $exercise->pivot->reps ?? '–' }}</td>
                        <td>{{ $exercise->pivot->rest_seconds ? $exercise->pivot->rest_seconds.'s' : '–' }}</td>
                        <td><span class="badge badge-outline badge-sm">{{ $exercise->muscle_group_label }}</span></td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif
    <div class="flex gap-3">
        @auth
            <a href="{{ route('logs.create', ['workout_id' => $workout->id]) }}" class="btn btn-success text-white px-8">Log This Workout</a>
        @else
            <a href="{{ route('login') }}" class="btn btn-success text-white px-8">Login to Log This Workout</a>
        @endauth
        <a href="{{ route('workouts.index') }}" class="btn btn-outline">← Back</a>
    </div>
</div>
@endsection
