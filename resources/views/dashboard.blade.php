@extends('layouts.app')
@section('title','Dashboard')
@section('content')
<div class="max-w-5xl mx-auto px-4 py-8">
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">My Dashboard</h1>
            <p class="text-gray-500 text-sm">Welcome back, {{ auth()->user()->name }}!</p>
        </div>
        <a href="{{ route('logs.create') }}" class="btn btn-success text-white">+ Log Workout</a>
    </div>
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8">
        <div class="bg-white rounded-xl p-5 shadow-sm border text-center"><div class="text-3xl font-bold text-green-600">{{ $totalLogs }}</div><div class="text-sm text-gray-500 mt-1">Total Workouts</div></div>
        <div class="bg-white rounded-xl p-5 shadow-sm border text-center"><div class="text-3xl font-bold text-blue-600">{{ $totalGoals }}</div><div class="text-sm text-gray-500 mt-1">Goals Created</div></div>
        <div class="bg-white rounded-xl p-5 shadow-sm border text-center"><div class="text-3xl font-bold text-orange-500">{{ number_format($totalMinutes / 60, 1) }}h</div><div class="text-sm text-gray-500 mt-1">Total Time</div></div>
        <div class="bg-white rounded-xl p-5 shadow-sm border text-center"><div class="text-3xl font-bold text-red-500">{{ $streak }} 🔥</div><div class="text-sm text-gray-500 mt-1">Day Streak</div></div>
    </div>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div class="bg-white rounded-2xl shadow-sm border p-5">
            <div class="flex items-center justify-between mb-4">
                <h2 class="font-bold text-gray-800">Recent Workout Logs</h2>
                <a href="{{ route('logs.index') }}" class="text-green-600 text-sm hover:underline">View all →</a>
            </div>
            @forelse($recentLogs as $log)
            <div class="flex items-center justify-between p-3 bg-gray-50 rounded-xl mb-2">
                <div>
                    <div class="font-medium text-sm">{{ $log->workout->title }}</div>
                    <div class="text-xs text-gray-400">{{ $log->logged_at->format('M d') }} · {{ $log->duration_minutes ?? '?' }}min · {{ $log->effort_label }}</div>
                </div>
                <div class="flex gap-1">
                    <a href="{{ route('logs.edit', $log) }}" class="btn btn-xs btn-ghost">✏️</a>
                    <form id="dash-del-{{ $log->id }}" method="POST" action="{{ route('logs.destroy', $log) }}">
                        @csrf @method('DELETE')
                        <button type="button" onclick="confirmAction('dash-del-{{ $log->id }}', 'Delete this workout log? This cannot be undone.')" class="btn btn-xs btn-ghost text-red-400">🗑</button>
                    </form>
                </div>
            </div>
            @empty
            <p class="text-sm text-gray-400 text-center py-4">No workout logs yet. <a href="{{ route('logs.create') }}" class="text-green-600">Log your first workout!</a></p>
            @endforelse
        </div>
        <div class="bg-white rounded-2xl shadow-sm border p-5">
            <div class="flex items-center justify-between mb-4">
                <h2 class="font-bold text-gray-800">Active Goals</h2>
                <a href="{{ route('goals.index') }}" class="text-green-600 text-sm hover:underline">View all →</a>
            </div>
            @forelse($goals as $goal)
            <div class="mb-4">
                <div class="flex justify-between text-sm mb-1">
                    <span class="font-medium text-gray-800">{{ $goal->title }}</span>
                    <span class="text-gray-400">{{ $goal->progress_percent }}%</span>
                </div>
                <progress class="progress progress-success w-full" value="{{ $goal->progress_percent }}" max="100"></progress>
            </div>
            @empty
            <p class="text-sm text-gray-400 text-center py-4">No goals yet. <a href="{{ route('goals.create') }}" class="text-green-600">Set your first goal!</a></p>
            @endforelse
        </div>
    </div>
</div>
@endsection
