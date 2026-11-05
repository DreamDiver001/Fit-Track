@extends('layouts.app')
@section('title','My Goals')
@section('content')
<div class="max-w-3xl mx-auto px-4 py-8">
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-bold text-gray-800">My Fitness Goals</h1>
        <a href="{{ route('goals.create') }}" class="btn btn-success text-white btn-sm">+ New Goal</a>
    </div>
    @if($goals->isEmpty())
        <div class="alert alert-info"><span>No goals yet. <a href="{{ route('goals.create') }}" class="font-bold underline">Create your first goal!</a></span></div>
    @else
        <div class="space-y-4">
        @foreach($goals as $goal)
        <div class="bg-white rounded-2xl shadow-sm border p-5">
            <div class="flex items-start justify-between">
                <div class="flex-1">
                    <div class="flex gap-2 mb-1 flex-wrap">
                        <span class="badge badge-outline badge-sm">{{ $goal->goal_type_label }}</span>
                        <span class="badge {{ $goal->status_color }} badge-sm text-white">{{ ucfirst(str_replace('_',' ',$goal->status)) }}</span>
                    </div>
                    <h3 class="font-bold text-gray-800">{{ $goal->title }}</h3>
                    @if($goal->notes)<p class="text-sm text-gray-500 mt-1">{{ $goal->notes }}</p>@endif
                    @if($goal->target_value)
                    <div class="mt-3">
                        <div class="flex justify-between text-xs text-gray-500 mb-1">
                            <span>
                                @if($goal->goal_type === 'weight_loss')
                                    Start: {{ $goal->start_value ?? '?' }} {{ $goal->unit }} → Now: {{ $goal->current_value ?? '?' }} {{ $goal->unit }} → Target: {{ $goal->target_value }} {{ $goal->unit }}
                                @else
                                    {{ $goal->current_value ?? 0 }} / {{ $goal->target_value }} {{ $goal->unit }}
                                @endif
                            </span>
                            <span>{{ $goal->progress_percent }}%</span>
                        </div>
                        <progress class="progress progress-success w-full" value="{{ $goal->progress_percent }}" max="100"></progress>
                    </div>
                    @endif
                    @if($goal->deadline)
                        <p class="text-xs text-gray-400 mt-2">🗓 Deadline: {{ $goal->deadline->format('M d, Y') }}</p>
                    @endif
                </div>
                <div class="flex gap-1 ml-4">
                    <a href="{{ route('goals.edit', $goal) }}" class="btn btn-xs btn-ghost">✏️</a>
                    <form id="del-goal-{{ $goal->id }}" method="POST" action="{{ route('goals.destroy', $goal) }}">
                        @csrf @method('DELETE')
                        <button type="button" onclick="confirmAction('del-goal-{{ $goal->id }}', 'Delete this goal? This cannot be undone.')" class="btn btn-xs btn-ghost text-red-400">🗑</button>
                    </form>
                </div>
            </div>
        </div>
        @endforeach
        </div>
    @endif
</div>
@endsection
