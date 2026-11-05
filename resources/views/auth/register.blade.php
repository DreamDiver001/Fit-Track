@extends('layouts.guest')
@section('title','Sign Up')
@section('content')
<div class="card bg-white shadow-lg w-full max-w-sm">
    <div class="card-body p-8">
        <div class="text-center mb-6">
            <div class="text-4xl mb-2">🏋️</div>
            <h2 class="text-2xl font-bold">Create your account</h2>
            <p class="text-sm text-gray-500 mt-1">Join FitTrack and start tracking today</p>
        </div>
        @if($errors->any())
            <div class="alert alert-error text-sm mb-4">
                <ul>@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
            </div>
        @endif
        <form method="POST" action="{{ route('register') }}" class="space-y-4">
            @csrf
            <div>
                <label class="label"><span class="label-text font-medium">Name</span></label>
                <input type="text" name="name" value="{{ old('name') }}" required autofocus
                       class="input input-bordered w-full @error('name') input-error @enderror" placeholder="Your full name"/>
                @error('name')<span class="text-error text-xs">{{ $message }}</span>@enderror
            </div>
            <div>
                <label class="label"><span class="label-text font-medium">Email</span></label>
                <input type="email" name="email" value="{{ old('email') }}" required
                       class="input input-bordered w-full @error('email') input-error @enderror" placeholder="you@example.com"/>
                @error('email')<span class="text-error text-xs">{{ $message }}</span>@enderror
            </div>
            <div>
                <label class="label"><span class="label-text font-medium">Password</span></label>
                <input type="password" name="password" required
                       class="input input-bordered w-full @error('password') input-error @enderror" placeholder="Minimum 8 characters"/>
                @error('password')<span class="text-error text-xs">{{ $message }}</span>@enderror
            </div>
            <div>
                <label class="label"><span class="label-text font-medium">Confirm Password</span></label>
                <input type="password" name="password_confirmation" required
                       class="input input-bordered w-full" placeholder="Repeat password"/>
            </div>
            <button type="submit" class="btn btn-success text-white w-full">Create Account</button>
        </form>
        <p class="text-center text-sm text-gray-500 mt-4">
            Already have an account? <a href="{{ route('login') }}" class="text-green-600 font-medium hover:underline">Log In</a>
        </p>
    </div>
</div>
@endsection
