<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    /**
     * Menampilkan daftar semua user kecuali SPV yang sedang login.
     */
    public function index()
    {
        // Mengambil semua user, kecuali diri sendiri (SPV yang sedang login)
        // dan memastikan user memiliki kolom role untuk ditampilkan
        $users = User::where('id', '!=', Auth::id())
                     ->orderBy('name')
                     ->get();

        // Daftar role yang valid (Anda bisa menambah 'unassigned' jika Anda menggunakan nilai default)
        $roles = ['admin', 'spv']; 

        return view('user.index', compact('users', 'roles'));
    }

    /**
     * Memperbarui role user tertentu.
     */
    public function updateRole(Request $request, User $user)
    {
        // Pencegahan keamanan: SPV tidak boleh mengubah role diri sendiri
        if ($user->id == Auth::id()) {
            return back()->with('error', 'Anda tidak dapat mengubah role akun Anda sendiri.');
        }

        // 1. Validasi Role Baru: Memungkinkan nilai 'unassigned' (yang akan kita konversi ke null)
        $request->validate([
            'role' => ['required', 'string', Rule::in(['admin', 'spv', 'unassigned'])], // Tambahkan 'unassigned'
        ]);
        
        // 2. Konversi 'unassigned' menjadi null untuk database
        $newRole = $request->role === 'unassigned' ? null : $request->role;

        $user->role = $newRole;
        $user->save();

        return back()->with('success', "Role user $user->name berhasil diubah menjadi " . ($newRole ?? 'Unassigned') . ".");
    }

    public function destroy(User $user)
    {
        // Pencegahan keamanan: SPV tidak boleh menghapus akun sendiri
        if ($user->id == Auth::id()) {
            return back()->with('error', 'Anda tidak dapat menghapus akun Anda sendiri.');
        }

        $userName = $user->name;
        $user->delete();

        return back()->with('success', "Akun $userName berhasil dihapus.");
    }
}