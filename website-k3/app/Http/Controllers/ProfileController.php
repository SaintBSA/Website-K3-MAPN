<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use App\Models\User; // Pastikan model User diimpor

class ProfileController extends Controller
{
    /**
     * Menampilkan formulir pengaturan profil.
     */
    public function edit()
    {
        // Mengarahkan ke view resources/views/profile/settings.blade.php
        return view('profiles.settings');
    }

    /**
     * Memperbarui data profil (Nama, Email, Password).
     */
    public function update(Request $request)
    {
        $user = Auth::user();

        // 1. Validasi Data
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            // Email harus unik di tabel 'users', kecuali untuk ID pengguna saat ini
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            // Password baru bersifat opsional ('nullable'), minimal 8 karakter, dan harus cocok dengan field 'password_confirmation'
            'password' => ['nullable', 'string', 'min:8', 'confirmed'], 
        ]);

        // 2. Update Data Dasar (Nama dan Email)
        $user->name = $request->name;
        //$user->email = $request->email;

        // 3. Update Password (hanya jika field password diisi)
        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }

        // 4. Simpan perubahan ke database
        $user->save();

        // 5. Redirect kembali dengan pesan sukses
        return redirect()->route('profile.edit')->with('success', 'Pengaturan profil berhasil diperbarui!');
    }
}