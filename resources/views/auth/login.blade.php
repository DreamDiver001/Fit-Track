@extends('layouts.guest')
@section('title','Log In')
@section('content')
<div class="card bg-white shadow-lg w-full max-w-sm">
    <div class="card-body p-8">
        <div class="text-center mb-6">
            <div class="text-4xl mb-2">💪</div>
            <h2 class="text-2xl font-bold">Welcome back</h2>
            <p class="text-sm text-gray-500 mt-1">Log in to track your fitness journey</p>
        </div>
        @if($errors->any())
            <div class="alert alert-error text-sm mb-4">
                <ul>@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
            </div>
        @endif
        <form method="POST" action="{{ route('login') }}" class="space-y-4">
            @csrf
            <div>
                <label class="label"><span class="label-text font-medium">Email</span></label>
                <input type="email" name="email" value="{{ old('email') }}" required autofocus
                       class="input input-bordered w-full @error('email') input-error @enderror"
                       placeholder="you@example.com"/>
            </div>
            <div>
                <label class="label"><span class="label-text font-medium">Password</span></label>
                <input type="password" name="password" required
                       class="input input-bordered w-full @error('password') input-error @enderror"
                       placeholder="••••••••"/>
            </div>
            <div class="flex items-center gap-2">
                <input type="checkbox" name="remember" id="remember" class="checkbox checkbox-success checkbox-sm"/>
                <label for="remember" class="text-sm text-gray-600 cursor-pointer">Remember me</label>
            </div>
            <button type="submit" class="btn btn-success text-white w-full">Log In</button>
        </form>
        <p class="text-center text-sm text-gray-500 mt-4">
            Don't have an account? <a href="{{ route('register') }}" class="text-green-600 font-medium hover:underline">Sign Up</a>
        </p>
    </div>
</div>
@endsection
