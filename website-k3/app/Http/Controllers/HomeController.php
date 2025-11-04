<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
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
     * @param Request $request
     * @return \Illuminate\Contracts\Support\Renderable
     */
    
    public function index(Request $request)
    {
        $user = Auth::user();

        if (!$user->role || !$user->is_active) {
            return view('nodash'); 
        }

        $baseQuery = Report::query();

        if ($request->filled('date_from')) {
            $baseQuery->whereDate('created_at', '>=', $request->input('date_from'));
        }

        if ($request->filled('date_to')) {
            $baseQuery->whereDate('created_at', '<=', $request->input('date_to'));
        }
        
        $totalReports = (clone $baseQuery)->count();
        $pendingReports = (clone $baseQuery)->where('status', 'Pending')->count();
        $closedReports = (clone $baseQuery)->where('status', 'Closed')->count();

        $urgentReports = (clone $baseQuery)->where('status', 'Pending')
                               ->orderByRaw("FIELD(priority, 'Tinggi', 'Sedang', 'Rendah')")
                               ->orderBy('created_at', 'desc') 
                               ->limit(10) 
                               ->get();
        
        $incidentTypes = MasterOption::where('category', 'jenis')->pluck('name'); 
        $incidentCounts = [];
        
        foreach ($incidentTypes as $type) {
            $incidentCounts[] = (clone $baseQuery)->where('type', $type)->count(); 
        }

        $chartData = [
            'urgentReports' => $urgentReports,
            'incidentTypeLabels' => $incidentTypes,
            'incidentTypeCounts' => $incidentCounts,
            'date_from' => $request->input('date_from'),
            'date_to' => $request->input('date_to'),
        ];

        return view('home', compact('totalReports', 'pendingReports', 'closedReports', 'chartData'));
    }
}