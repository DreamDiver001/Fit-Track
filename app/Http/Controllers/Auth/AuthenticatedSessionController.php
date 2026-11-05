<?php
namespace App\Http\Controllers\Auth;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthenticatedSessionController extends Controller {
    public function create() { return view('auth.login'); }
    public function store(Request $request) {
        $request->validate([
            'email'    => 'required|string|email',
            'password' => 'required|string',
        ]);
        if (!Auth::attempt($request->only('email','password'), $request->boolean('remember'))) {
            return back()->withErrors(['email'=>'The provided credentials do not match our records.'])->onlyInput('email');
        }
        $request->session()->regenerate();
        if (auth()->user()->is_banned) {
            Auth::logout();
            return back()->withErrors(['email'=>'Your account has been suspended.']);
        }
        return redirect()->intended(route('dashboard'));
    }
    public function destroy(Request $request) {
        Auth::guard('web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/');
    }
}
