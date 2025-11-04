<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Report;
use App\Models\MasterOption;
use App\Models\StatusLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ReportController extends Controller
{

    public function index(Request $request) 
    {
        $types = MasterOption::where('category', 'jenis')->where('is_active', 1)->get();
        $locations = MasterOption::where('category', 'lokasi')->where('is_active', 1)->get();

        $reportsQuery = Report::query();

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

        $sortBy = $request->input('sort', 'latest');

        if ($sortBy == 'oldest') {
            $reportsQuery->orderBy('created_at', 'asc');
        } else {
            $reportsQuery->orderBy('created_at', 'desc');
        }

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
        
        StatusLog::create([
            'report_id' => $report->id,
            'user_id' => Auth::id(),
            'old_status' => null,
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
        $report = Report::with('statusLogs.user')->findOrFail($id); 
        $statuses = MasterOption::where('category', 'status')->where('is_active', 1)->get();
        $priorities = MasterOption::where('category', 'prioritas')->where('is_active', 1)->get();
        
        return view('reports.edit', compact('report', 'statuses', 'priorities'));
    }

    public function update(Request $request, $id)
    {
        $report = Report::findOrFail($id);
        $oldStatus = $report->status;
        $oldPriority = $report->priority;
        
        $request->validate([
            'status' => 'required|string',
            'priority' => 'required|string',
            'spv_feedback' => 'nullable|string', 
        ]);

        $report->status = $request->status;
        $report->priority = $request->priority;
        $report->spv_feedback = $request->spv_feedback; 
        
        $report->save();

        $feedbackProvided = $request->filled('spv_feedback');
        
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


        return redirect()->route('reports.index')->with('success', 'Status dan feedback laporan berhasil diperbarui!');
    }

    public function show($id)
    {
        $report = Report::with('statusLogs.user')->findOrFail($id);
        return view('reports.show', compact('report'));
    }
}