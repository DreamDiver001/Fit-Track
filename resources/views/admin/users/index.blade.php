@extends('layouts.app')
@section('title','User Management')
@section('content')
<div class="max-w-5xl mx-auto px-4 py-8">
    <div class="flex items-center gap-3 mb-6">
        <h1 class="text-2xl font-bold text-gray-800">User Management</h1>
        <span class="badge badge-error text-white">Admin</span>
    </div>
    <form method="GET" class="flex gap-3 mb-5">
        <input name="search" value="{{ request('search') }}" placeholder="Search by name or email…" class="input input-bordered flex-1"/>
        <button class="btn btn-success text-white">Search</button>
        @if(request('search'))<a href="{{ route('admin.users.index') }}" class="btn btn-ghost">Clear</a>@endif
    </form>
    <div class="bg-white rounded-2xl shadow-sm border overflow-hidden">
        <table class="table">
            <thead class="bg-gray-50"><tr><th>User</th><th>Email</th><th>Joined</th><th>Logs</th><th>Goals</th><th>Status</th><th>Actions</th></tr></thead>
            <tbody>
                @foreach($users as $u)
                <tr class="hover">
                    <td>
                        <div class="flex items-center gap-2">
                            <div class="avatar placeholder"><div class="{{ $u->is_admin ? 'bg-red-500' : 'bg-green-500' }} text-white rounded-full w-8 text-xs font-bold"><span>{{ strtoupper(substr($u->name,0,2)) }}</span></div></div>
                            <div><div class="font-medium text-sm">{{ $u->name }}</div>@if($u->is_admin)<span class="badge badge-error badge-xs text-white">Admin</span>@endif</div>
                        </div>
                    </td>
                    <td class="text-sm text-gray-500">{{ $u->email }}</td>
                    <td class="text-sm text-gray-500">{{ $u->created_at->format('M d, Y') }}</td>
                    <td class="text-sm">{{ $u->workout_logs_count }}</td>
                    <td class="text-sm">{{ $u->goals_count }}</td>
                    <td>
                        @if($u->is_banned)<span class="badge badge-error badge-sm text-white">Banned</span>
                        @else<span class="badge badge-success badge-sm text-white">Active</span>@endif
                    </td>
                    <td>
                        <div class="flex gap-1 flex-wrap">
                            @if(!$u->is_admin)
                                <form method="POST" action="{{ route('admin.users.toggleBan', $u) }}">
                                    @csrf
                                    <button class="btn btn-xs {{ $u->is_banned ? 'btn-success' : 'btn-warning' }}">{{ $u->is_banned ? 'Unban' : 'Ban' }}</button>
                                </form>
                                <form id="del-user-{{ $u->id }}" method="POST" action="{{ route('admin.users.destroy', $u) }}">
                                    @csrf @method('DELETE')
                                    <button type="button" onclick="confirmAction('del-user-{{ $u->id }}', 'Permanently delete user {{ addslashes($u->name) }}? All their data will be removed.')" class="btn btn-xs btn-error text-white">Delete</button>
                                </form>
                            @else<span class="text-xs text-gray-400">Protected</span>@endif
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div class="mt-5 flex justify-center">{{ $users->links() }}</div>
</div>
@endsection
