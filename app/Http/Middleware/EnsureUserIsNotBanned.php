<?php
namespace App\Http\Middleware;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * BUG-005: the ban check used to run only at login, so a user banned while
 * signed in kept full access until their session expired. This middleware
 * runs on every web request and ends the session of a banned user.
 */
class EnsureUserIsNotBanned {
    public function handle(Request $request, Closure $next) {
        if (Auth::check() && Auth::user()->is_banned) {
            Auth::guard('web')->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
            return redirect()->route('login')
                ->withErrors(['email' => 'Your account has been suspended.']);
        }
        return $next($request);
    }
}
