<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Report;
use App\Models\MasterOption;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ReportController extends Controller
{

public function index(Request $request) 
    {
        // 1. Ambil data master untuk dropdown filter
        $types = MasterOption::where('category', 'jenis')->where('is_active', 1)->get();
        $locations = MasterOption::where('category', 'lokasi')->where('is_active', 1)->get();

        // 2. Inisiasi Query Dasar
        $reportsQuery = Report::query();

        // 3. Terapkan Filter

        // Filter Jenis (type)
        if ($request->filled('type')) {
            $reportsQuery->where('type', $request->input('type'));
        }

        // Filter Lokasi (location)
        if ($request->filled('location')) {
            $reportsQuery->where('location', $request->input('location'));
        }
        
        // Filter Tanggal Kejadian (incident_datetime)
        // Kolom incident_datetime harus ada di tabel reports
        if ($request->filled('date_from')) {
            $reportsQuery->whereDate('incident_datetime', '>=', $request->input('date_from'));
        }
        
        if ($request->filled('date_to')) {
            $reportsQuery->whereDate('incident_datetime', '<=', $request->input('date_to'));
        }
        
        // Catatan: Filter 'search' telah dihapus dari controller ini.

        // 4. Terapkan Sorting (Tanggal Lapor)
        $sortBy = $request->input('sort', 'latest');

        if ($sortBy == 'oldest') {
            $reportsQuery->orderBy('created_at', 'asc');
        } else {
            $reportsQuery->orderBy('created_at', 'desc');
        }

        // 5. Eksekusi Query dan Ambil Data
        $reports = $reportsQuery->get();

        // Kirimkan semua data ke view, termasuk filter yang aktif
        return view('reports.index', compact('reports', 'types', 'locations'));
    }

    protected function getChartData()
{
    // Mengambil laporan tertunda paling mendesak (contoh: 5 laporan prioritas tertinggi)
    $urgentReports = Report::where('status', 'Pending')
                           ->orderByRaw("FIELD(priority, 'Tinggi', 'Sedang', 'Rendah')")
                           ->limit(5)
                           ->get();

    return [
        'urgentReports' => $urgentReports,
        // Chart.js data akan tetap menggunakan dummy, kecuali Anda ingin mengembangkannya
    ];
}

    public function create()
{
        // Ambil hanya data Lokasi, Jenis, dan Dampak yang akan ditampilkan
        $locations = MasterOption::where('category', 'lokasi')->where('is_active', 1)->get();
        $types = MasterOption::where('category', 'jenis')->where('is_active', 1)->get();
        $impacts = MasterOption::where('category', 'dampak')->where('is_active', 1)->get();
        
        // Prioritas dan Status tidak perlu diambil karena diset otomatis/disabled
        return view('reports.create', compact('locations', 'types', 'impacts'));
    }

    public function store(Request $request)
    {
        // Validasi input
        $request->validate([
            'description' => 'required|string',
            'location' => 'required|string',
            'type' => 'required|string',
            'impact' => 'required|string',
            
            // Bidang Baru
            'incident_datetime' => 'nullable|date', // Diganti dari tanggalWaktu ke incident_datetime
            'involved_parties' => 'nullable|string|max:255', // Diganti dari pihakTerlibat ke involved_parties
            'media' => 'nullable|file|mimes:jpg,jpeg,png,mp4|max:20480', // Asumsi satu file upload
        ]);

        // Buat objek laporan baru
        $report = new Report;
        $report->description = $request->description;
        $report->location = $request->location;
        $report->type = $request->type;
        $report->impact = $request->impact;
        
        // Data Otomatis & Baru
        $report->incident_datetime = $request->incident_datetime; // Simpan waktu kejadian
        $report->involved_parties = $request->involved_parties; // Simpan pihak terlibat
        $report->reported_by = Auth::user()->name; 
        
        // Status dan Prioritas Awal (Sesuai Konsep Desain)
        $report->status = 'Pending'; // Set default Status
        $report->priority = 'Rendah'; // Set default Prioritas (akan diubah SPV)

    if ($request->hasFile('media')) {
        $file = $request->file('media');
        $fileName = $file->hashName(); // Nama file unik
        $folderPath = 'reports';      // Subfolder di dalam disk 'public'

        try {
            // Gunakan Storage Facade untuk penyimpanan yang lebih eksplisit
            // Menyimpan ke: storage/app/public/reports/
            $filePath = Storage::disk('public')->putFileAs($folderPath, $file, $fileName);

            // Jika penyimpanan berhasil, simpan path relatif di database: reports/namafile.png
            $report->media_path = $filePath; 

        } catch (\Exception $e) {
            // DEBUGGING: Tambahkan log jika penyimpanan gagal
            \Log::error('File upload failed: ' . $e->getMessage());
            // Anda mungkin ingin mengembalikan error ke user di sini
            return back()->withInput()->withErrors(['media' => 'Gagal mengunggah file. Cek izin folder.']);
        }
    }
    // END: LOGIKA PENYIMPANAN YANG EKSPLISIT

    // 3. SIMPAN OBJEK KE DATABASE
    $report->save();

        return redirect()->route('reports.index')->with('success', 'Laporan berhasil diajukan! Menunggu peninjauan Tim K3.');
    }

    public function edit($id)
    {
        $report = Report::findOrFail($id);
        $statuses = MasterOption::where('category', 'status')->where('is_active', 1)->get();
        $priorities = MasterOption::where('category', 'prioritas')->where('is_active', 1)->get();
        
        return view('reports.edit', compact('report', 'statuses', 'priorities'));
    }

public function update(Request $request, $id)
{
    $report = Report::findOrFail($id);
    
    // 1. Tambahkan validasi untuk feedback
    $request->validate([
        'status' => 'required|string',
        'priority' => 'required|string',
        'spv_feedback' => 'nullable|string', // Validasi feedback (opsional)
    ]);

    // 2. Simpan nilai baru
    $report->status = $request->status;
    $report->priority = $request->priority;
    
    // 3. Simpan feedback dari SPV (nilai bisa null)
    $report->spv_feedback = $request->spv_feedback; 
    
    $report->save();

    return redirect()->route('reports.index')->with('success', 'Status dan feedback laporan berhasil diperbarui!');
}

    public function show($id)
    {
        $report = Report::findOrFail($id);
        return view('reports.show', compact('report'));
    }
}