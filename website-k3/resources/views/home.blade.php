<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Pelaporan K3 - MAPN Group</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    
    <style>
        /* Gaya Kustom dari HTML Anda */
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f4f7f9;
        }
        .sidebar {
            width: 250px;
            min-height: 100vh;
            background-color: #ffffff;
            box-shadow: 2px 0 5px rgba(0, 0, 0, 0.05);
            position: fixed;
            top: 0;
            left: 0;
            padding-top: 20px;
        }
        .main-content {
            margin-left: 250px;
            padding: 30px;
        }
        .card-k3 {
            border: none;
            border-radius: 12px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.05);
            transition: transform 0.2s;
        }
        .card-k3:hover {
            transform: translateY(-3px);
        }
        .report-number {
            font-size: 3rem;
            font-weight: 700;
        }
        /* Warna untuk Status */
        .status-total { color: #212529; }
        .status-pending { color: #ffc107; }
        .status-closed { color: #28a745; }
        
        .nav-link.active {
            background-color: #e6f7ff !important;
            color: #0d6efd !important;
            border-left: 4px solid #0d6efd;
            font-weight: 600;
        }
        .nav-link {
            transition: all 0.2s;
        }
        /* Style Tambahan untuk Unassigned */
        .unassigned-card {
            max-width: 600px;
            margin: 100px auto;
            text-align: center;
        }
    </style>
</head>
<body>

@php
    $role = Auth::user()->role;
    $currentRoute = Route::currentRouteName();
    $isActive = Auth::user()->is_active ?? true;
@endphp

@if ($role && $isActive)
    {{-- ======================================================= --}}
    {{-- TAMPILAN DASHBOARD LENGKAP UNTUK USER/SPV (role != null) --}}
    {{-- ======================================================= --}}
    
    {{-- SIDEBAR LENGKAP --}}
    <div class="sidebar d-flex flex-column">
        <div class="p-3 mb-4 text-center">
    <h5 class="fw-bold text-primary">K3 MAPN System ({{ strtoupper(Auth::user()->role) == 'ADMIN' ? 'USER' : strtoupper(Auth::user()->role) }})</h5>
</div>
        <nav class="nav flex-column px-3">
            <a class="nav-link {{ $currentRoute == 'home' ? 'active' : 'text-dark' }} rounded mb-1" href="{{ route('home') }}">
                <i class="bi bi-speedometer2 me-2"></i> Dashboard
            </a>
            
            {{-- FITUR 3: INPUT FORM LAPORAN (Hanya Admin) --}}
            @if($role === 'admin')
                <a class="nav-link {{ $currentRoute == 'reports.create' ? 'active' : 'text-dark' }} rounded mb-1" href="{{ route('reports.create') }}">
                    <i class="bi bi-file-earmark-text me-2"></i> Tambah Kejadian
                </a>
            @endif

            {{-- FITUR 5: LIHAT RIWAYAT DETAIL LAPORAN (Admin & SPV) --}}
            @php $isReportActive = in_array($currentRoute, ['reports.index', 'reports.show', 'reports.edit']); @endphp
            <a class="nav-link {{ $isReportActive ? 'active' : 'text-dark' }} rounded mb-1" href="{{ route('reports.index') }}">
                <i class="bi bi-card-checklist me-2"></i> Laporan
            </a>

            {{-- PENGATURAN PROFIL (Admin & SPV) --}}
            <a class="nav-link {{ $currentRoute == 'profile.edit' ? 'active' : 'text-dark' }} rounded mb-1" href="{{ route('profile.edit') }}">
                <i class="bi bi-person-circle me-2"></i> Profil & Akun
            </a>

            {{-- USER MANAGEMENT (Hanya SPV) --}}
            @if($role === 'spv')
                <a class="nav-link {{ $currentRoute == 'user.index' ? 'active' : 'text-dark' }} rounded mb-1" href="{{ route('user.index') }}">
                    <i class="bi bi-people me-2"></i> User Management
                </a>
            @endif
            
            {{-- PENGATURAN MASTER (Hanya SPV) --}}
            @if($role === 'spv')
                <a class="nav-link {{ $currentRoute == 'master.settings' ? 'active' : 'text-dark' }} rounded mb-1" href="{{ route('master.settings') }}">
                    <i class="bi bi-gear me-2"></i> Master Settings
                </a>
            @endif
            
            <a class="nav-link text-danger mt-4 rounded" href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                <i class="bi bi-box-arrow-right me-2"></i> Keluar
            </a>
            <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">@csrf</form>
        </nav>
    </div>

    <div class="main-content">
        {{-- Tombol Ajukan Laporan --}}
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h3 fw-bold">Dashboard Pelaporan K3</h1>
            @if($role === 'admin')
                <a href="{{ route('reports.create') }}" class="btn btn-primary btn-sm rounded-pill px-3">
                    <i class="bi bi-plus me-1"></i> Ajukan Laporan Baru
                </a>
            @endif
        </div>
        
        {{-- FILTER DASHBOARD BARU --}}
        <div class="card card-k3 p-4 mb-4">
            <h5 class="fw-bold mb-3">Filter Data Berdasarkan Tanggal Lapor</h5>
            <form method="GET" action="{{ route('home') }}">
                <div class="row g-3 align-items-end">
                    
                    {{-- INPUT TANGGAL DARI --}}
                    <div class="col-md-3">
                        <label for="date_from" class="form-label">Dari Tanggal</label>
                        <input type="date" name="date_from" id="date_from" class="form-control" value="{{ $chartData['date_from'] ?? '' }}">
                    </div>
                    
                    {{-- INPUT TANGGAL SAMPAI --}}
                    <div class="col-md-3">
                        <label for="date_to" class="form-label">Sampai Tanggal</label>
                        <input type="date" name="date_to" id="date_to" class="form-control" value="{{ $chartData['date_to'] ?? '' }}">
                    </div>

                    {{-- TOMBOL FILTER (Diperlebar menjadi col-md-3) --}}
                    <div class="col-md-3">
                        <button type="submit" class="btn btn-primary w-100">Filter</button>
                    </div>
                    
                    {{-- TOMBOL RESET (Diperlebar menjadi col-md-3) --}}
                    <div class="col-md-3">
                        <a href="{{ route('home') }}" class="btn btn-outline-secondary w-100">Reset</a>
                    </div>

                </div>
            </form>
        </div>
        
        {{-- Ringkasan Data --}}
        <div class="row mb-5">
            <div class="col-md-4">
                <div class="card card-k3 p-3">
                    <p class="text-muted mb-1">Total Laporan</p>
                    <div class="d-flex align-items-center">
                        <i class="bi bi-file-earmark-text status-total fs-4 me-3"></i>
                        <span class="report-number status-total">{{ $totalReports }}</span>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card card-k3 p-3">
                    <p class="text-muted mb-1">Laporan Tertunda (Pending)</p>
                    <div class="d-flex align-items-center">
                        <i class="bi bi-clock status-pending fs-4 me-3"></i>
                        <span class="report-number status-pending">{{ $pendingReports }}</span>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card card-k3 p-3">
                    <p class="text-muted mb-1">Laporan Selesai (Closed)</p>
                    <div class="d-flex align-items-center">
                        <i class="bi bi-check-circle status-closed fs-4 me-3"></i>
                        <span class="report-number status-closed">{{ $closedReports }}</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Grafik --}}
        <div class="row mb-5">
             <div class="col-md-8">
                <div class="card card-k3 p-4">
                    <h5 class="fw-bold mb-3">Tren Pelaporan Bulanan (6 Bulan Terakhir)</h5>
                    <canvas id="monthlyTrendChart" height="351"></canvas>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card card-k3 p-4">
                    <h5 class="fw-bold mb-3">Laporan Berdasarkan Jenis Insiden</h5>
                    <canvas id="incidentTypeChart"></canvas>
                </div>
            </div>
        </div>

        {{-- Tabel Mendesak --}}
        @if($role === 'admin' || $role === 'spv')
        <div class="row">
            <div class="col-12">
                <div class="card card-k3 p-4">
                    <h5 class="fw-bold mb-3">Laporan Tertunda Paling Mendesak</h5>
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th>ID</th>
                                    <th>Jenis Insiden</th>
                                    <th>Prioritas</th>
                                    <th>Tanggal Lapor</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($chartData['urgentReports'] as $report)
                                    @php
                                        $priorityClass = ['Tinggi' => 'danger', 'Sedang' => 'warning', 'Rendah' => 'secondary'][$report->priority] ?? 'primary';
                                        $detailRoute = ($role === 'spv') ? route('reports.edit', $report->id) : route('reports.show', $report->id);
                                    @endphp
                                    <tr>
                                        <td>K3-{{ str_pad($report->id, 4, '0', STR_PAD_LEFT) }}</td>
                                        <td>{{ $report->type }}</td>
                                        <td><span class="badge text-bg-{{ $priorityClass }}">{{ $report->priority }}</span></td>
                                        <td>{{ $report->created_at->format('d/m/Y') }}</td>
                                        <td>
                                            <a href="{{ $detailRoute }}" class="btn btn-sm btn-outline-primary">Detail</a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr><td colspan="5" class="text-center">Tidak ada laporan Pending yang mendesak.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        @endif
        
        <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.3/dist/chart.umd.min.js"></script>
        <script>
            // Data Nyata untuk Chart Jenis Insiden
            const incidentTypeLabels = @json($chartData['incidentTypeLabels'] ?? ['N/A']);
            const incidentTypeCounts = @json($chartData['incidentTypeCounts'] ?? [0]);

            const chartColors = ['#dc3545', '#ffc107', '#0d6efd', '#28a745'];
            const monthlyData = {labels: ['Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep'], datasets: [{label: 'Jumlah Laporan K3', data: [180, 150, 210, 195, 230, 15], backgroundColor: 'rgba(13, 110, 253, 0.5)', borderColor: '#0d6efd', borderWidth: 2, borderRadius: 5, fill: true, tension: 0.3}]};
            const incidentTypeData = {labels: incidentTypeLabels, datasets: [{label: 'Jumlah Laporan', data: incidentTypeCounts, backgroundColor: chartColors.slice(0, incidentTypeLabels.length), hoverOffset: 4}]};

            new Chart(document.getElementById('monthlyTrendChart'), {type: 'bar', data: monthlyData, options: {responsive: true, plugins: {legend: { display: false }}, scales: {y: {beginAtZero: true}}}});
            new Chart(document.getElementById('incidentTypeChart'), {type: 'doughnut', data: incidentTypeData, options: {responsive: true, plugins: {legend: { position: 'bottom' }}}});
        </script>
        
    </div>
    
@else
    {{-- TAMPILAN TERBATAS UNTUK UNASSIGNED USER (role == null) --}}
    
    <div class="container unassigned-card">
        <div class="card card-k3 p-5">
            <h1 class="h3 fw-bold mb-3 text-primary">Selamat Datang, {{ Auth::user()->name }} 👋</h1>
            <p class="text-muted mb-4">
                Akun Anda telah berhasil didaftarkan. Anda saat ini belum memiliki akses penuh ke sistem pelaporan K3
                <br>
                <br>
                Mohon tunggu hingga Supervisor menetapkan peran Anda
            </p>
            
            <hr>
            
            {{-- Tombol Logout --}}
            <a class="btn btn-danger mt-3" href="{{ route('logout') }}" 
               onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                <i class="bi bi-box-arrow-right me-2"></i> Logout
            </a>
            <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">@csrf</form>
        </div>
    </div>
@endif

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>