@extends('layouts.app')
@section('title','Admin Dashboard')
@section('content')
<div class="max-w-5xl mx-auto px-4 py-8">
    <div class="flex items-center gap-3 mb-2">
        <h1 class="text-2xl font-bold text-gray-800">Admin Dashboard</h1>
        <span class="badge badge-error text-white">Superuser</span>
    </div>
    <p class="text-gray-500 text-sm mb-6">Manage users, workouts, exercises, and site settings.</p>
    <div class="grid grid-cols-2 md:grid-cols-5 gap-4 mb-8">
        <div class="bg-white rounded-xl p-4 text-center shadow-sm border"><div class="text-2xl font-bold text-green-600">{{ $stats['users'] }}</div><div class="text-xs text-gray-500">Users</div></div>
        <div class="bg-white rounded-xl p-4 text-center shadow-sm border"><div class="text-2xl font-bold text-blue-600">{{ $stats['workouts'] }}</div><div class="text-xs text-gray-500">Workouts</div></div>
        <div class="bg-white rounded-xl p-4 text-center shadow-sm border"><div class="text-2xl font-bold text-purple-600">{{ $stats['exercises'] }}</div><div class="text-xs text-gray-500">Exercises</div></div>
        <div class="bg-white rounded-xl p-4 text-center shadow-sm border"><div class="text-2xl font-bold text-orange-500">{{ $stats['logs'] }}</div><div class="text-xs text-gray-500">Workout Logs</div></div>
        <div class="bg-white rounded-xl p-4 text-center shadow-sm border"><div class="text-2xl font-bold text-red-500">{{ $stats['goals'] }}</div><div class="text-xs text-gray-500">Goals</div></div>
    </div>
    <div class="flex gap-3 mb-8 flex-wrap">
        <a href="{{ route('admin.workouts.create') }}"  class="btn btn-success text-white btn-sm">+ Create Workout</a>
        <a href="{{ route('admin.exercises.create') }}" class="btn btn-info text-white btn-sm">+ Create Exercise</a>
        <a href="{{ route('admin.users.index') }}"      class="btn btn-ghost btn-sm">👥 Manage Users</a>
        <a href="{{ route('admin.workouts.index') }}"   class="btn btn-ghost btn-sm">🏋️ Manage Workouts</a>
        <a href="{{ route('admin.exercises.index') }}"  class="btn btn-ghost btn-sm">💪 Manage Exercises</a>
    </div>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div class="bg-white rounded-2xl shadow-sm border p-5">
            <div class="flex justify-between items-center mb-3">
                <h2 class="font-bold">Recent Users</h2>
                <a href="{{ route('admin.users.index') }}" class="text-green-600 text-sm">View all →</a>
            </div>
            @foreach($recentUsers as $u)
            <div class="flex items-center justify-between py-2 border-b last:border-0">
                <div class="flex items-center gap-2">
                    <div class="avatar placeholder"><div class="bg-gray-200 rounded-full w-7 text-xs"><span>{{ strtoupper(substr($u->name,0,2)) }}</span></div></div>
                    <div><div class="text-sm font-medium">{{ $u->name }}</div><div class="text-xs text-gray-400">{{ $u->email }}</div></div>
                </div>
                <div class="flex gap-1 items-center">
                    @if($u->is_admin)<span class="badge badge-error badge-xs text-white">Admin</span>@endif
                    @if($u->is_banned)<span class="badge badge-ghost badge-xs">Banned</span>@endif
                </div>
            </div>
            @endforeach
        </div>
        <div class="bg-white rounded-2xl shadow-sm border p-5">
            <h2 class="font-bold mb-3">Recent Workout Logs</h2>
            @foreach($recentLogs as $log)
            <div class="flex items-center justify-between py-2 border-b last:border-0">
                <div>
                    <div class="text-sm font-medium">{{ $log->workout->title }}</div>
                    <div class="text-xs text-gray-400">{{ $log->user->name }} · {{ $log->logged_at->format('M d') }}</div>
                </div>
                <span class="badge badge-ghost badge-sm">{{ $log->duration_minutes ?? '?' }}min</span>
            </div>
            @endforeach
        </div>
    </div>
</div>
@endsection
