@extends('layouts.app')
@section('title','Workouts')
@section('content')
<div class="max-w-5xl mx-auto px-4 py-8">
    <h1 class="text-2xl font-bold text-gray-800 mb-6">All Workouts</h1>
    <form method="GET" action="{{ route('workouts.index') }}" class="flex flex-wrap gap-3 mb-7">
        <input name="search" value="{{ request('search') }}" placeholder="Search workouts…" class="input input-bordered flex-1 min-w-48"/>
        <select name="category" class="select select-bordered">
            <option value="">All Categories</option>
            @foreach(['cardio'=>'Cardio','strength'=>'Strength','flexibility'=>'Flexibility','hiit'=>'HIIT','core'=>'Core','full_body'=>'Full Body'] as $val=>$label)
                <option value="{{ $val }}" @selected(request('category')===$val)>{{ $label }}</option>
            @endforeach
        </select>
        <select name="difficulty" class="select select-bordered">
            <option value="">All Difficulties</option>
            <option value="easy"   @selected(request('difficulty')==='easy')>Easy</option>
            <option value="medium" @selected(request('difficulty')==='medium')>Medium</option>
            <option value="hard"   @selected(request('difficulty')==='hard')>Hard</option>
        </select>
        <button class="btn btn-success text-white">Search</button>
        @if(request()->hasAny(['search','category','difficulty'])) <a href="{{ route('workouts.index') }}" class="btn btn-ghost">Clear</a> @endif
    </form>
    @if($workouts->isEmpty())
        <div class="alert alert-info"><span>No workouts found. Try adjusting your filters.</span></div>
    @else
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
            @foreach($workouts as $workout) @include('partials.workout_card') @endforeach
        </div>
        <div class="mt-8 flex justify-center">{{ $workouts->links() }}</div>
    @endif
</div>
@endsection
