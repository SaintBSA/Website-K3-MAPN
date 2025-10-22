<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Riwayat Laporan K3 - MAPN Group</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    
    <style>
        /* Style Dasar dan Sidebar (Dibiarkan sama) */
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
        .nav-link.active {
            background-color: #e6f7ff !important;
            color: #0d6efd !important;
            border-left: 4px solid #0d6efd;
            font-weight: 600;
        }
        /* Gaya Khusus untuk Tabel Riwayat */
        .card-table {
            border: none;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
        }
        .table thead th {
            font-weight: 600;
            vertical-align: middle;
        }
    </style>
</head>
<body>

    <div class="sidebar d-flex flex-column">
        <div class="p-3 mb-4 text-center">
            <h5 class="fw-bold text-primary">K3 MAPN System ({{ strtoupper(Auth::user()->role) }})</h5>
        </div>
        <nav class="nav flex-column px-3">
    
    {{-- Tentukan route saat ini untuk membandingkan --}}
    @php $currentRoute = Route::currentRouteName(); @endphp
    
    {{-- DASHBOARD --}}
    <a class="nav-link {{ $currentRoute == 'home' ? 'active' : 'text-dark' }} rounded mb-1" href="{{ route('home') }}">
        <i class="bi bi-speedometer2 me-2"></i> Dashboard
    </a>
    
    {{-- FITUR 3: INPUT FORM LAPORAN (Hanya Admin) --}}
    @if(Auth::user()->role === 'admin')
        <a class="nav-link {{ $currentRoute == 'reports.create' ? 'active' : 'text-dark' }} rounded mb-1" href="{{ route('reports.create') }}">
            <i class="bi bi-file-earmark-text me-2"></i> Tambah Kejadian
        </a>
    @endif

    {{-- FITUR 5: LAPORAN (Riwayat, Show, Edit) --}}
    {{-- Aktif jika berada di halaman index, show, atau edit laporan --}}
    @php
        $isReportActive = in_array($currentRoute, ['reports.index', 'reports.show', 'reports.edit']);
    @endphp
    <a class="nav-link {{ $isReportActive ? 'active' : 'text-dark' }} rounded mb-1" href="{{ route('reports.index') }}">
        <i class="bi bi-card-checklist me-2"></i> Laporan
    </a>

    {{-- PENGATURAN PROFIL (TIDAK AKTIF di halaman ini) --}}
    <a class="nav-link text-dark rounded mb-1" href="{{ route('profile.edit') }}">
        <i class="bi bi-person-circle me-2"></i> Profil & Akun
    </a>

    @if(Auth::user()->role === 'spv')
    <a class="nav-link text-dark rounded mb-1" href="{{ route('user.index') }}">
        <i class="bi bi-people me-2"></i> User Management
    </a>
@endif
    
    {{-- PENGATURAN MASTER (Hanya SPV) --}}
    @if(Auth::user()->role === 'spv')
        <a class="nav-link {{ $currentRoute == 'master.settings' ? 'active' : 'text-dark' }} rounded mb-1" href="{{ route('master.settings') }}">
            <i class="bi bi-gear me-2"></i> Master Settings
        </a>
    @endif
    
    {{-- LOGOUT --}}
    <a class="nav-link text-danger mt-4 rounded" href="{{ route('logout') }}" 
       onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
        <i class="bi bi-box-arrow-right me-2"></i> Keluar
    </a>
    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
        @csrf
    </form>
</nav>
    </div>

    <div class="main-content">
        
        <h1 class="h3 fw-bold mb-4">Riwayat Laporan K3</h1>
        <p class="text-muted mb-4">Daftar semua laporan K3 yang telah diajukan di fasilitas produksi.</p>

        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="card card-table p-4">
            
            <h5 class="fw-bold mb-3">Filter Laporan</h5>
    <form method="GET" action="{{ route('reports.index') }}" class="mb-4">
        <div class="row g-3 align-items-end">
            
            <div class="col-md-2">
                <label for="filter_type" class="form-label">Jenis Insiden</label>
                <select name="type" id="filter_type" class="form-select">
                    <option value="">Semua Jenis</option>
                    @foreach($types as $type)
                        <option value="{{ $type->name }}" {{ request('type') == $type->name ? 'selected' : '' }}>
                            {{ $type->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-2">
                <label for="filter_location" class="form-label">Lokasi</label>
                <select name="location" id="filter_location" class="form-select">
                    <option value="">Semua Lokasi</option>
                    @foreach($locations as $location)
                        <option value="{{ $location->name }}" {{ request('location') == $location->name ? 'selected' : '' }}>
                            {{ $location->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            
            {{-- FILTER TANGGAL KEJADIAN (DARI) --}}
            <div class="col-md-2">
                <label for="date_from" class="form-label">Kejadian Dari Tanggal</label>
                <input type="date" name="date_from" id="date_from" class="form-control" value="{{ request('date_from') }}">
            </div>
            
            {{-- FILTER TANGGAL KEJADIAN (SAMPAI) --}}
            <div class="col-md-2">
                <label for="date_to" class="form-label">Kejadian Sampai Tanggal</label>
                <input type="date" name="date_to" id="date_to" class="form-control" value="{{ request('date_to') }}">
            </div>

            <div class="col-md-2">
                <button type="submit" class="btn btn-primary w-100">Cari</button>
            </div>
            
            <div class="col-md-2">
                <a href="{{ route('reports.index') }}" class="btn btn-outline-secondary w-100">Reset Filter</a>
            </div>

        </div>
    </form>
    
    <h5 class="fw-bold mb-3">Semua Laporan ({{ $reports->count() }} Data)</h5>

            <div class="table-responsive">
                <table class="table table-striped align-middle mb-0">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>
                                Tanggal Lapor
                                @php
                                    $sort = request('sort', 'latest');
                                    $nextSort = $sort == 'latest' ? 'oldest' : 'latest';
                                @endphp
                                <a href="{{ route('reports.index', array_merge(request()->query(), ['sort' => $nextSort])) }}" class="text-decoration-none ms-1">
                                    <i class="bi bi-sort-down {{ $sort == 'latest' ? 'text-primary' : 'text-muted' }}"></i>
                                </a>
                            </th>
                            <th>Lokasi</th>
                            <th>Jenis Insiden</th>
                            <th>Dampak</th>
                            <th>Status</th>
                            <th>Prioritas</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($reports as $report)
                            @php
                                $statusColor = ['Pending' => 'warning', 'In Progress' => 'info', 'Closed' => 'success', 'Overdue' => 'danger', 'Not Applicable' => 'secondary'];
                                $priorityColor = ['Tinggi' => 'danger', 'Sedang' => 'warning', 'Rendah' => 'secondary'];
                                $currentStatusColor = $statusColor[$report->status] ?? 'secondary';
                                $currentPriorityColor = $priorityColor[$report->priority] ?? 'secondary';
                                
                                $detailRoute = (Auth::user()->role === 'spv') 
                                    ? route('reports.edit', $report->id) 
                                    : route('reports.show', $report->id);
                            @endphp
                            <tr>
                                <td><span class="fw-semibold">K3-{{ str_pad($report->id, 4, '0', STR_PAD_LEFT) }}</span></td>
                                <td>{{ $report->created_at->format('d M Y, H:i') }}</td>
                                <td>{{ $report->location }}</td>
                                <td>{{ $report->type }}</td>
                                <td>{{ $report->impact }}</td>
                                <td><span class="badge text-bg-{{ $currentStatusColor }}">{{ $report->status }}</span></td>
                                <td><span class="badge text-bg-{{ $currentPriorityColor }}">{{ $report->priority }}</span></td>
                                <td>
                                    {{-- Tombol Detail/Review --}}
                                    <a href="{{ $detailRoute }}" class="btn btn-sm btn-outline-primary me-1">
                                        <i class="bi bi-arrows-expand me-1"></i> Detail
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center py-4">Tidak ada data laporan ditemukan.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>