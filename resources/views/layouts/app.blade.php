<!DOCTYPE html>
<html lang="en" data-theme="light">
<head>
    <meta charset="UTF-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title','FitTrack') – FitTrack</title>
    <link href="https://cdn.jsdelivr.net/npm/daisyui@4.12.10/dist/full.min.css" rel="stylesheet"/>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-base-200 min-h-screen flex flex-col">

<nav class="navbar bg-white shadow-sm sticky top-0 z-50 px-4 lg:px-8">
    <div class="flex-1">
        <a href="{{ route('home') }}" class="flex items-center gap-2 text-xl font-extrabold text-green-700 hover:text-green-800">
            💪 <span>FitTrack</span>
        </a>
    </div>
    <div class="flex-none gap-1 hidden md:flex items-center">
        <a href="{{ route('workouts.index') }}"  class="btn btn-sm btn-ghost">Workouts</a>
        <a href="{{ route('exercises.index') }}" class="btn btn-sm btn-ghost">Exercises</a>
        @auth
            <a href="{{ route('dashboard') }}"   class="btn btn-sm btn-ghost">Dashboard</a>
            <a href="{{ route('logs.index') }}"  class="btn btn-sm btn-ghost">My Logs</a>
            <a href="{{ route('goals.index') }}" class="btn btn-sm btn-ghost">My Goals</a>
            @if(auth()->user()->is_admin)
                <a href="{{ route('admin.dashboard') }}" class="btn btn-sm btn-error text-white">Admin</a>
            @endif
            <form method="POST" action="{{ route('logout') }}" class="inline">
                @csrf
                <button class="btn btn-sm btn-outline text-red-500 border-red-300">Logout</button>
            </form>
            <div class="avatar placeholder ml-1">
                <div class="bg-green-600 text-white rounded-full w-9 text-sm font-bold">
                    <span>{{ strtoupper(substr(auth()->user()->name,0,2)) }}</span>
                </div>
            </div>
        @else
            <a href="{{ route('login') }}"    class="btn btn-sm btn-outline">Log In</a>
            <a href="{{ route('register') }}" class="btn btn-sm btn-success text-white">Sign Up</a>
        @endauth
    </div>
    <div class="flex-none md:hidden">
        <div class="dropdown dropdown-end">
            <button tabindex="0" class="btn btn-ghost">☰</button>
            <ul tabindex="0" class="dropdown-content menu bg-base-100 rounded-box z-50 w-52 p-2 shadow-lg">
                <li><a href="{{ route('workouts.index') }}">Workouts</a></li>
                <li><a href="{{ route('exercises.index') }}">Exercises</a></li>
                @auth
                    <li><a href="{{ route('dashboard') }}">Dashboard</a></li>
                    <li><a href="{{ route('logs.index') }}">My Logs</a></li>
                    <li><a href="{{ route('goals.index') }}">My Goals</a></li>
                    @if(auth()->user()->is_admin)<li><a href="{{ route('admin.dashboard') }}">Admin</a></li>@endif
                    <li><form method="POST" action="{{ route('logout') }}">@csrf<button class="w-full text-left px-4 py-2 text-red-500">Logout</button></form></li>
                @else
                    <li><a href="{{ route('login') }}">Log In</a></li>
                    <li><a href="{{ route('register') }}">Sign Up</a></li>
                @endauth
            </ul>
        </div>
    </div>
</nav>

<div class="max-w-5xl mx-auto w-full px-4 mt-3">
    @if(session('success'))
        <div class="alert alert-success text-sm"><span>{{ session('success') }}</span></div>
    @endif
    @if(session('error'))
        <div class="alert alert-error text-sm"><span>{{ session('error') }}</span></div>
    @endif
</div>

<main class="flex-1">@yield('content')</main>

<footer class="bg-white border-t mt-10 py-5 text-center text-sm text-gray-400">
    <div class="flex justify-center gap-6 mb-2 text-gray-500">
        <a href="{{ route('workouts.index') }}"  class="hover:text-green-600">Workouts</a>
        <a href="{{ route('exercises.index') }}" class="hover:text-green-600">Exercises</a>
        @auth
            <a href="{{ route('dashboard') }}"   class="hover:text-green-600">Dashboard</a>
            <a href="{{ route('goals.index') }}"  class="hover:text-green-600">Goals</a>
        @endauth
    </div>
    © {{ date('Y') }} FitTrack. All rights reserved.
</footer>

{{-- Custom Confirm Modal --}}
<dialog id="confirmModal" class="modal">
    <div class="modal-box">
        <h3 class="font-bold text-lg mb-1">⚠️ Are you sure?</h3>
        <p id="confirmMessage" class="text-gray-500 text-sm mb-6">This action cannot be undone.</p>
        <div class="flex gap-3 justify-end">
            <button onclick="document.getElementById('confirmModal').close()" class="btn btn-ghost">Cancel</button>
            <button id="confirmBtn" class="btn btn-error text-white">Delete</button>
        </div>
    </div>
    <form method="dialog" class="modal-backdrop"><button>close</button></form>
</dialog>

<script>
function confirmAction(formId, message) {
    document.getElementById('confirmMessage').textContent = message || 'This action cannot be undone.';
    document.getElementById('confirmBtn').onclick = function() {
        document.getElementById(formId).submit();
    };
    document.getElementById('confirmModal').showModal();
}
</script>
</body>
</html>
