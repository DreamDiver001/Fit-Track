@extends('layouts.app')
@section('title','Exercises')
@section('content')
<div class="max-w-5xl mx-auto px-4 py-8">
    <h1 class="text-2xl font-bold text-gray-800 mb-6">Exercise Library</h1>
    <form method="GET" action="{{ route('exercises.index') }}" class="flex flex-wrap gap-3 mb-7">
        <input name="search" value="{{ request('search') }}" placeholder="Search exercises…" class="input input-bordered flex-1 min-w-48"/>
        <select name="muscle_group" class="select select-bordered">
            <option value="">All Muscle Groups</option>
            @foreach(['chest'=>'Chest','back'=>'Back','legs'=>'Legs','core'=>'Core','arms'=>'Arms','shoulders'=>'Shoulders','full_body'=>'Full Body'] as $val=>$label)
                <option value="{{ $val }}" @selected(request('muscle_group')===$val)>{{ $label }}</option>
            @endforeach
        </select>
        <select name="equipment" class="select select-bordered">
            <option value="">All Equipment</option>
            @foreach(['barbell'=>'Barbell','dumbbell'=>'Dumbbell','bodyweight'=>'Bodyweight','machine'=>'Machine','cable'=>'Cable','other'=>'Other'] as $val=>$label)
                <option value="{{ $val }}" @selected(request('equipment')===$val)>{{ $label }}</option>
            @endforeach
        </select>
        <button class="btn btn-success text-white">Search</button>
        @if(request()->hasAny(['search','muscle_group','equipment'])) <a href="{{ route('exercises.index') }}" class="btn btn-ghost">Clear</a> @endif
    </form>
    @if($exercises->isEmpty())
        <div class="alert alert-info"><span>No exercises found.</span></div>
    @else
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
            @foreach($exercises as $exercise)
            <div class="card bg-white shadow-sm border hover:shadow-md transition-shadow">
                @if($exercise->image_path)
                    <figure><img src="{{ Storage::url($exercise->image_path) }}" class="w-full h-32 object-cover" alt="{{ $exercise->name }}"/></figure>
                @else
                    <figure><div class="w-full h-32 bg-gradient-to-br from-slate-200 to-slate-300 flex items-center justify-center text-4xl">
                        @if($exercise->muscle_group==='chest') 💪
                        @elseif($exercise->muscle_group==='legs') 🦵
                        @elseif($exercise->muscle_group==='back') 🏋️
                        @elseif($exercise->muscle_group==='core') 🎯
                        @elseif($exercise->muscle_group==='arms') 💪
                        @elseif($exercise->muscle_group==='shoulders') 🔝
                        @else 🏃 @endif
                    </div></figure>
                @endif
                <div class="card-body p-4">
                    <h3 class="font-bold text-sm">{{ $exercise->name }}</h3>
                    <p class="text-xs text-gray-500 line-clamp-2">{{ $exercise->description }}</p>
                    <div class="flex gap-1 flex-wrap mt-1">
                        <span class="badge badge-outline badge-sm">{{ $exercise->muscle_group_label }}</span>
                        <span class="badge badge-ghost badge-sm">{{ $exercise->equipment_label }}</span>
                    </div>
                    @if($exercise->instructions)
                    <details class="mt-2">
                        <summary class="text-xs text-green-600 cursor-pointer font-medium">View instructions</summary>
                        <p class="text-xs text-gray-500 mt-1 leading-relaxed">{{ $exercise->instructions }}</p>
                    </details>
                    @endif
                </div>
            </div>
            @endforeach
        </div>
        <div class="mt-8 flex justify-center">{{ $exercises->links() }}</div>
    @endif
</div>
@endsection
