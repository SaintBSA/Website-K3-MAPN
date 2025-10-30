<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request; // <-- DIPERBAIKI: Harus diimpor
use App\Models\Report;
use App\Models\MasterOption;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @param Request $request <-- DIPERBAIKI: Menerima objek Request
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index(Request $request)
    {
        // 1. Definisikan Base Query dan Terapkan Filter Tanggal
        $baseQuery = Report::query();

        if ($request->filled('date_from')) {
            // Filter berdasarkan tanggal dibuat (created_at)
            $baseQuery->whereDate('created_at', '>=', $request->input('date_from'));
        }

        if ($request->filled('date_to')) {
            $baseQuery->whereDate('created_at', '<=', $request->input('date_to'));
        }
        
        // 2. Ambil Data Ringkasan Statistik (Menggunakan clone baseQuery)
        // Clone diperlukan karena setiap operasi count() akan mengakhiri builder
        $totalReports = (clone $baseQuery)->count();
        $pendingReports = (clone $baseQuery)->where('status', 'Pending')->count();
        $closedReports = (clone $baseQuery)->where('status', 'Closed')->count();

        // 3. Ambil Data Urgent Reports untuk Tabel
        $urgentReports = (clone $baseQuery)->where('status', 'Pending')
                               ->orderByRaw("FIELD(priority, 'Tinggi', 'Sedang', 'Rendah')")
                               ->orderBy('created_at', 'desc') 
                               ->limit(10) 
                               ->get();
        
        // 4. Query untuk Data Chart Jenis Insiden
        $incidentTypes = MasterOption::where('category', 'jenis')->pluck('name'); 
        $incidentCounts = [];
        
        foreach ($incidentTypes as $type) {
            // Terapkan baseQuery filter ke query chart
            $incidentCounts[] = (clone $baseQuery)->where('type', $type)->count(); 
        }

        $chartData = [
            'urgentReports' => $urgentReports,
            'incidentTypeLabels' => $incidentTypes,
            'incidentTypeCounts' => $incidentCounts,
            'date_from' => $request->input('date_from'),
            'date_to' => $request->input('date_to'),
        ];

        // 5. Kirim semua variabel ke view 'home'
        return view('home', compact('totalReports', 'pendingReports', 'closedReports', 'chartData'));
    }
}