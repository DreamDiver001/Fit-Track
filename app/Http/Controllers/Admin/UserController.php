<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller {
    public function index(Request $request) {
        $query = User::withCount(['workoutLogs','goals']);
        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(fn($q) => $q->where('name','like',"%$s%")->orWhere('email','like',"%$s%"));
        }
        $users = $query->latest()->paginate(15)->withQueryString();
        return view('admin.users.index', compact('users'));
    }
    public function toggleBan(User $user) {
        if ($user->is_admin) return back()->with('error','Cannot ban an admin.');
        $user->update(['is_banned' => !$user->is_banned]);
        return back()->with('success', $user->is_banned ? 'User banned.' : 'User unbanned.');
    }
    public function destroy(User $user) {
        if ($user->is_admin) return back()->with('error','Cannot delete an admin.');
        $user->delete();
        return back()->with('success','User deleted.');
    }
}
