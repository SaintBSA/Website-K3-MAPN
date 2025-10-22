<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\MasterOption; // Import Model
use Illuminate\Validation\Rule;

class MasterOptionController extends Controller
{
    /**
     * Menampilkan daftar semua Master Options (Diurutkan berdasarkan Category).
     */
    public function index()
    {
        // 1. Ambil semua data dan kelompokkan berdasarkan category
        $options = MasterOption::all()->groupBy('category');
        
        // 2. Ambil daftar unik kategori yang ada (untuk form input baru)
        $categories = MasterOption::select('category')->distinct()->pluck('category');

        // 3. Kirim kedua variabel ke view
        return view('master.settings', compact('options', 'categories')); // <-- PASTIKAN 'categories' ADA DI SINI
    }

    /**
     * Menyimpan Master Option baru.
     */
    public function store(Request $request)
    {
        $request->validate([
            'category' => ['required', 'string', 'max:255'],
            'name' => [
                'required', 
                'string', 
                'max:255',
                // Pastikan nama unik dalam kategori yang sama
                Rule::unique('master_options')->where(function ($query) use ($request) {
                    return $query->where('category', $request->category);
                }),
            ],
            // is_active akan default ke 1
        ]);
        
        MasterOption::create([
            'category' => $request->category,
            'name' => $request->name,
            'is_active' => 1, // Default aktif saat dibuat
        ]);

        return back()->with('success', 'Opsi master baru berhasil ditambahkan.');
    }

    /**
     * Memperbarui status is_active atau nama opsi yang sudah ada.
     */
    public function update(Request $request, MasterOption $masterOption)
    {
        // Logika ini digunakan untuk mengaktifkan/menonaktifkan atau mengubah nama
        if ($request->has('toggle_status')) {
            // Hanya mengubah status
            $masterOption->is_active = $masterOption->is_active == 1 ? 0 : 1;
            $masterOption->save();
            return back()->with('success', 'Status opsi master berhasil diubah.');
        } 
        
        // Logika jika ingin mengubah nama atau kategori (opsional, tergantung kebutuhan)
        // Kita fokus pada toggle status dulu untuk kemudahan plug and play.

        return back();
    }

    /**
     * Menghapus Master Option.
     */
    public function destroy(MasterOption $masterOption)
    {
        // Disarankan untuk TIDAK menghapus, tetapi menonaktifkan.
        // Namun, jika perlu dihapus:
        // $masterOption->delete();
        // return back()->with('success', 'Opsi master berhasil dihapus.');

        // Kita akan menggunakan update/toggle untuk menonaktifkan daripada menghapus permanen
        return back()->with('error', 'Penghapusan permanen tidak diizinkan. Gunakan tombol nonaktifkan.');
    }
}