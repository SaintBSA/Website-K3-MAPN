<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Report; // <-- Tambahkan ini
use App\Models\MasterOption; // <-- Tambahkan ini
use Illuminate\Support\Facades\Auth; // <-- Tambahkan ini

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
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
{
    // 1. Ambil Data Ringkasan Statistik
    $totalReports = Report::count();
    $pendingReports = Report::where('status', 'Pending')->count();
    $closedReports = Report::where('status', 'Closed')->count();

    // 2. Ambil Data Urgent Reports untuk Tabel
    $urgentReports = Report::where('status', 'Pending')
                           // Urutkan laporan Pending: Tinggi > Sedang > Rendah
                           // Ini mengurutkan berdasarkan prioritas yang didefinisikan secara eksplisit.
                           ->orderByRaw("FIELD(priority, 'Tinggi', 'Sedang', 'Rendah')")
                           ->orderBy('created_at', 'desc') // Urutan waktu terbaru
                           ->limit(5)
                           ->get();
    
    // Siapkan data chart
    $incidentTypes = MasterOption::where('category', 'jenis')->pluck('name'); 
    $incidentCounts = [];
    
    foreach ($incidentTypes as $type) {
        $incidentCounts[] = Report::where('type', $type)->count();
    }

    $chartData = [
        'urgentReports' => $urgentReports,
        'incidentTypeLabels' => $incidentTypes,
        'incidentTypeCounts' => $incidentCounts,
    ];

    // 3. Kirim semua variabel ke view 'home'
    return view('home', compact('totalReports', 'pendingReports', 'closedReports', 'chartData'));
}
}
