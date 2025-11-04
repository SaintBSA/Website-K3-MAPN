<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\MasterOption; // Import Model
use Illuminate\Validation\Rule;

class MasterOptionController extends Controller
{
    public function index()
    {
        $options = MasterOption::all()->groupBy('category');
        
        $categories = MasterOption::select('category')->distinct()->pluck('category');

        return view('master.settings', compact('options', 'categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'category' => ['required', 'string', 'max:255'],
            'name' => [
                'required', 
                'string', 
                'max:255',
                Rule::unique('master_options')->where(function ($query) use ($request) {
                    return $query->where('category', $request->category);
                }),
            ],
        ]);
        
        MasterOption::create([
            'category' => $request->category,
            'name' => $request->name,
            'is_active' => 1,
        ]);

        return back()->with('success', 'Opsi master baru berhasil ditambahkan.');
    }

    public function update(Request $request, MasterOption $masterOption)
    {
        if ($request->has('toggle_status')) {
            $masterOption->is_active = $masterOption->is_active == 1 ? 0 : 1;
            $masterOption->save();
            return back()->with('success', 'Status opsi master berhasil diubah.');
        } 

        return back();
    }

    public function destroy(MasterOption $masterOption)
    {
        // $masterOption->delete();
        // return back()->with('success', 'Opsi master berhasil dihapus.');

        return back()->with('error', 'Penghapusan permanen tidak diizinkan. Gunakan tombol nonaktifkan.');
    }
}