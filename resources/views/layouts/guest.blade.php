<!DOCTYPE html>
<html lang="en" data-theme="light">
<head>
    <meta charset="UTF-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>@yield('title','FitTrack') – FitTrack</title>
    <link href="https://cdn.jsdelivr.net/npm/daisyui@4.12.10/dist/full.min.css" rel="stylesheet"/>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-base-200 min-h-screen flex flex-col">
<nav class="navbar bg-white shadow-sm px-6">
    <div class="flex-1"><a href="{{ route('home') }}" class="text-xl font-extrabold text-green-700">💪 FitTrack</a></div>
    <div class="flex-none gap-2">
        <a href="{{ route('login') }}"    class="btn btn-sm btn-outline">Log In</a>
        <a href="{{ route('register') }}" class="btn btn-sm btn-success text-white">Sign Up</a>
    </div>
</nav>
<main class="flex-1 flex items-center justify-center py-12 px-4">@yield('content')</main>
<footer class="bg-white border-t py-4 text-center text-sm text-gray-400">© {{ date('Y') }} FitTrack.</footer>
</body>
</html>
