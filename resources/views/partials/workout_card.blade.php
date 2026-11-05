<div class="card bg-white shadow-sm border border-gray-100 hover:shadow-md transition-shadow">
    @if($workout->image_path)
        <figure><img src="{{ Storage::url($workout->image_path) }}" alt="{{ $workout->title }}" class="w-full h-40 object-cover"/></figure>
    @else
        <figure>
            <div class="w-full h-40 flex items-center justify-center text-5xl
                @if($workout->category==='hiit') bg-gradient-to-br from-orange-400 to-red-500
                @elseif($workout->category==='strength') bg-gradient-to-br from-blue-500 to-blue-700
                @elseif($workout->category==='flexibility') bg-gradient-to-br from-purple-400 to-purple-600
                @elseif($workout->category==='cardio') bg-gradient-to-br from-green-400 to-green-600
                @elseif($workout->category==='core') bg-gradient-to-br from-cyan-400 to-cyan-600
                @else bg-gradient-to-br from-gray-400 to-gray-600 @endif">
                @if($workout->category==='hiit') 🔥
                @elseif($workout->category==='strength') 💪
                @elseif($workout->category==='flexibility') 🧘
                @elseif($workout->category==='cardio') 🏃
                @elseif($workout->category==='core') 🎯
                @else 🏋️ @endif
            </div>
        </figure>
    @endif
    <div class="card-body p-4">
        <h3 class="font-bold text-gray-800 text-base leading-tight">{{ $workout->title }}</h3>
        <p class="text-xs text-gray-500 line-clamp-2">{{ $workout->description }}</p>
        <div class="flex flex-wrap gap-1 mt-2">
            <span class="badge {{ $workout->category_color }} badge-sm">{{ $workout->category_label }}</span>
            <span class="badge {{ $workout->difficulty_color }} badge-sm">{{ ucfirst($workout->difficulty) }}</span>
            @if($workout->estimated_duration)<span class="badge badge-ghost badge-sm">{{ $workout->estimated_duration }}min</span>@endif
        </div>
        <div class="mt-3">
            <a href="{{ route('workouts.show', $workout) }}" class="btn btn-success btn-sm text-white w-full">View Details</a>
        </div>
    </div>
</div>
