@extends('layouts.app')
@section('title','My Workout Logs')
@section('content')
<div class="max-w-4xl mx-auto px-4 py-8">
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-bold text-gray-800">My Workout Logs</h1>
        <a href="{{ route('logs.create') }}" class="btn btn-success text-white">+ Log Workout</a>
    </div>
    @if($logs->isEmpty())
        <div class="alert alert-info"><span>No workout logs yet. <a href="{{ route('logs.create') }}" class="font-bold underline">Log your first workout!</a></span></div>
    @else
        <div class="bg-white rounded-2xl shadow-sm border overflow-hidden">
            <table class="table">
                <thead class="bg-gray-50"><tr><th>Workout</th><th>Date</th><th>Duration</th><th>Effort</th><th>Calories</th><th>Actions</th></tr></thead>
                <tbody>
                    @foreach($logs as $log)
                    <tr class="hover">
                        <td class="font-medium">{{ $log->workout->title }}</td>
                        <td class="text-sm text-gray-500">{{ $log->logged_at->format('M d, Y') }}</td>
                        <td>{{ $log->duration_minutes ? $log->duration_minutes.' min' : '–' }}</td>
                        <td>{{ $log->effort_label }}</td>
                        <td>{{ $log->calories_burned ? $log->calories_burned.' kcal' : '–' }}</td>
                        <td>
                            <div class="flex gap-1">
                                <a href="{{ route('logs.edit', $log) }}" class="btn btn-xs btn-ghost">✏️ Edit</a>
                                <form id="del-log-{{ $log->id }}" method="POST" action="{{ route('logs.destroy', $log) }}">
                                    @csrf @method('DELETE')
                                    <button type="button" onclick="confirmAction('del-log-{{ $log->id }}', 'Delete this workout log? This cannot be undone.')" class="btn btn-xs btn-ghost text-red-400">🗑 Delete</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="mt-6 flex justify-center">{{ $logs->links() }}</div>
    @endif
</div>
@endsection
