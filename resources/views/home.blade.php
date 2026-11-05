@extends('layouts.app')
@section('title','Home')
@section('content')
<div class="hero min-h-64 bg-gradient-to-br from-green-700 to-green-500 text-white">
    <div class="hero-content text-center py-16">
        <div class="max-w-xl">
            <div class="text-sm font-semibold text-green-200 uppercase tracking-widest mb-2">Your Fitness Companion</div>
            <h1 class="text-4xl md:text-5xl font-extrabold mb-4 leading-tight">Track Your Fitness Journey</h1>
            <p class="text-green-100 mb-8 text-lg">Log workouts, track progress, and crush your goals with FitTrack.</p>
            <div class="flex gap-3 justify-center flex-wrap">
                <a href="{{ route('workouts.index') }}" class="btn bg-white text-green-700 font-bold border-0 hover:bg-green-50">Browse Workouts</a>
                @guest <a href="{{ route('register') }}" class="btn btn-outline border-white text-white hover:bg-white hover:text-green-700">Create Free Account</a> @endguest
            </div>
        </div>
    </div>
</div>
<div class="bg-white border-b">
    <div class="max-w-5xl mx-auto px-4 py-5 grid grid-cols-3 gap-4 text-center">
        <div><div class="text-2xl font-bold text-green-600">500+</div><div class="text-xs text-gray-500">Exercises</div></div>
        <div><div class="text-2xl font-bold text-blue-600">100+</div><div class="text-xs text-gray-500">Workouts</div></div>
        <div><div class="text-2xl font-bold text-orange-500">10K+</div><div class="text-xs text-gray-500">Users</div></div>
    </div>
</div>
<div class="max-w-5xl mx-auto px-4 py-10">
    <div class="flex items-center justify-between mb-6">
        <h2 class="text-xl font-bold text-gray-800">🔥 Featured Workouts</h2>
        <a href="{{ route('workouts.index') }}" class="text-green-600 text-sm font-medium hover:underline">View all →</a>
    </div>
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
        @foreach($workouts as $workout) @include('partials.workout_card') @endforeach
    </div>
</div>
<div class="bg-white py-12">
    <div class="max-w-5xl mx-auto px-4">
        <h2 class="text-2xl font-bold text-center text-gray-800 mb-8">Why FitTrack?</h2>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 text-center">
            <div class="p-6 rounded-2xl border bg-base-100"><div class="text-4xl mb-3">🏋️</div><h3 class="font-bold text-gray-800 mb-2">Log Workouts</h3><p class="text-sm text-gray-500">Record every session with exercises, duration, effort, and notes.</p></div>
            <div class="p-6 rounded-2xl border bg-base-100"><div class="text-4xl mb-3">📊</div><h3 class="font-bold text-gray-800 mb-2">Track Progress</h3><p class="text-sm text-gray-500">Visualize your improvements over time with your personal dashboard.</p></div>
            <div class="p-6 rounded-2xl border bg-base-100"><div class="text-4xl mb-3">🎯</div><h3 class="font-bold text-gray-800 mb-2">Set Goals</h3><p class="text-sm text-gray-500">Define fitness goals and track milestones as you progress.</p></div>
        </div>
        @guest <div class="text-center mt-8"><a href="{{ route('register') }}" class="btn btn-success text-white btn-lg px-10">Get Started for Free</a></div> @endguest
    </div>
</div>
@endsection
