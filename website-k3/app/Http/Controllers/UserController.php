<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    public function index()
    {
        $users = User::where('id', '!=', Auth::id())
                     ->orderBy('name')
                     ->get();

        $roles = ['admin', 'spv']; 

        return view('user.index', compact('users', 'roles'));
    }

    public function updateRole(Request $request, User $user)
    {
        if ($user->id == Auth::id()) {
            return back()->with('error', 'Anda tidak dapat mengubah role akun Anda sendiri.');
        }

        $request->validate([
            'role' => ['required', 'string', Rule::in(['admin', 'spv', 'unassigned'])],
        ]);
        
        $newRole = $request->role === 'unassigned' ? null : $request->role;

        $user->role = $newRole;
        $user->save();

        return back()->with('success', "Role user $user->name berhasil diubah menjadi " . ($newRole ?? 'Unassigned') . ".");
    }

    public function destroy(User $user)
    {
        if ($user->id == Auth::id()) {
            return back()->with('error', 'Anda tidak dapat menghapus akun Anda sendiri.');
        }

        $userName = $user->name;
        $user->delete();

        return back()->with('success', "Akun $userName berhasil dihapus.");
    }

    public function toggleStatus(User $user)
    {
        if ($user->id == Auth::id()) {
            return back()->with('error', 'Anda tidak dapat menonaktifkan akun Anda sendiri.');
        }

        $user->is_active = $user->is_active == 1 ? 0 : 1;
        $user->save();

        $statusText = $user->is_active ? 'Aktif' : 'Nonaktif';

        return back()->with('success', "Status user $user->name berhasil diubah menjadi $statusText.");
    }
}