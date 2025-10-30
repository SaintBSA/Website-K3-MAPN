<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Report;
use App\Models\MasterOption;
use App\Models\StatusLog; // <-- IMPORT MODEL LOG
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

        // 3. Terapkan Filter (Logic sama seperti sebelumnya)
        if ($request->filled('type')) {
            $reportsQuery->where('type', $request->input('type'));
        }

        if ($request->filled('location')) {
            $reportsQuery->where('location', $request->input('location'));
        }
        
        if ($request->filled('date_from')) {
            $reportsQuery->whereDate('incident_datetime', '>=', $request->input('date_from'));
        }
        
        if ($request->filled('date_to')) {
            $reportsQuery->whereDate('incident_datetime', '<=', $request->input('date_to'));
        }

        // 4. Terapkan Sorting
        $sortBy = $request->input('sort', 'latest');

        if ($sortBy == 'oldest') {
            $reportsQuery->orderBy('created_at', 'asc');
        } else {
            $reportsQuery->orderBy('created_at', 'desc');
        }

        // 5. Eksekusi Query dan Ambil Data
        $reports = $reportsQuery->get();

        return view('reports.index', compact('reports', 'types', 'locations'));
    }

    public function create()
    {
        $locations = MasterOption::where('category', 'lokasi')->where('is_active', 1)->get();
        $types = MasterOption::where('category', 'jenis')->where('is_active', 1)->get();
        $impacts = MasterOption::where('category', 'dampak')->where('is_active', 1)->get();
        
        return view('reports.create', compact('locations', 'types', 'impacts'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'description' => 'required|string',
            'location' => 'required|string',
            'type' => 'required|string',
            'impact' => 'required|string',
            
            'incident_datetime' => 'nullable|date',
            'involved_parties' => 'nullable|string|max:255',
            'media' => 'nullable|file|mimes:jpg,jpeg,png,mp4|max:20480',
        ]);

        $report = new Report;
        $report->description = $request->description;
        $report->location = $request->location;
        $report->type = $request->type;
        $report->impact = $request->impact;
        
        $report->incident_datetime = $request->incident_datetime;
        $report->involved_parties = $request->involved_parties;
        $report->reported_by = Auth::user()->name; 
        
        $report->status = 'Pending'; 
        $report->priority = 'Rendah'; 

        if ($request->hasFile('media')) {
            $file = $request->file('media');
            $fileName = $file->hashName();
            $folderPath = 'reports'; 
            
            try {
                 $filePath = Storage::disk('public')->putFileAs($folderPath, $file, $fileName);
                 $report->media_path = $filePath; 
            } catch (\Exception $e) {
                 \Log::error('File upload failed: ' . $e->getMessage());
                 return back()->withInput()->withErrors(['media' => 'Gagal mengunggah file. Cek izin folder.']);
            }
        }

        $report->save();
        
        // CATAT LOG AWAL: Status awal 'Pending' dan Prioritas 'Rendah'
        StatusLog::create([
            'report_id' => $report->id,
            'user_id' => Auth::id(),
            'old_status' => null, // Status awal belum ada
            'new_status' => $report->status,
            'old_priority' => null,
            'new_priority' => $report->priority,
            'feedback' => 'Laporan awal diajukan oleh ' . $report->reported_by . '.', 
            'action_at' => now(), 
        ]);


        return redirect()->route('reports.index')->with('success', 'Laporan berhasil diajukan! Menunggu peninjauan Tim K3.');
    }

    public function edit($id)
    {
        // MUAT LOG HISTORY di sini
        $report = Report::with('statusLogs.user')->findOrFail($id); 
        $statuses = MasterOption::where('category', 'status')->where('is_active', 1)->get();
        $priorities = MasterOption::where('category', 'prioritas')->where('is_active', 1)->get();
        
        return view('reports.edit', compact('report', 'statuses', 'priorities'));
    }

    public function update(Request $request, $id)
    {
        $report = Report::findOrFail($id);
        
        // Simpan nilai lama sebelum update
        $oldStatus = $report->status;
        $oldPriority = $report->priority;
        
        // 1. Validasi
        $request->validate([
            'status' => 'required|string',
            'priority' => 'required|string',
            'spv_feedback' => 'nullable|string', 
        ]);

        // 2. Update data Report
        $report->status = $request->status;
        $report->priority = $request->priority;
        $report->spv_feedback = $request->spv_feedback; 
        
        $report->save();

        // 3. LOGIKA TRACKING BARU: Catat Perubahan Status/Prioritas
        $feedbackProvided = $request->filled('spv_feedback');
        
        // Cek apakah ada perubahan status ATAU perubahan prioritas
        if ($oldStatus != $report->status || $oldPriority != $report->priority || $feedbackProvided) {
            
            StatusLog::create([
                'report_id' => $report->id,
                'user_id' => Auth::id(),
                'old_status' => $oldStatus,
                'new_status' => $report->status,
                'old_priority' => $oldPriority,
                'new_priority' => $report->priority,
                'feedback' => $request->spv_feedback, 
                'action_at' => now(), 
            ]);
            
        } 
        // Jika tidak ada perubahan status/prioritas dan tidak ada feedback baru, tidak perlu log.


        return redirect()->route('reports.index')->with('success', 'Status dan feedback laporan berhasil diperbarui!');
    }

    public function show($id)
    {
        // MUAT LOG HISTORY di sini
        $report = Report::with('statusLogs.user')->findOrFail($id);
        return view('reports.show', compact('report'));
    }
}