<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Peninjauan Laporan K3: K3-{{ str_pad($report->id, 4, '0', STR_PAD_LEFT) }}</title>
    
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    
    <style>
        /* Gaya CSS (Dibiarkan sama) */
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
        .nav-link {
            transition: all 0.2s;
        }
        .nav-link.active {
            background-color: #e6f7ff !important;
            color: #0d6efd !important;
            border-left: 4px solid #0d6efd;
            font-weight: 600;
        }
        .card-form {
            border: none;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
        }
        .form-control, .form-select {
            border-radius: 8px;
            padding: 10px 15px;
        }
        .form-control:disabled, .form-select:disabled {
            background-color: #f8f9fa;
            border: 1px solid #e9ecef;
            color: #495057;
            opacity: 1;
        }
        .btn-k3-update {
            background-color: #0d6efd;
            border-color: #0d6efd;
            font-weight: 600;
            border-radius: 8px;
        }
        .btn-k3-update:hover {
            background-color: #0b5ed7;
            border-color: #0b5ed7;
        }
        .log-entry {
            border-left: 4px solid #0d6efd; /* Garis biru di samping log */
            padding-left: 15px;
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
        
        <h1 class="h3 fw-bold mb-2">Peninjauan Laporan K3: K3-{{ str_pad($report->id, 4, '0', STR_PAD_LEFT) }}</h1>
        <p class="text-muted mb-4">Laporan Diajukan oleh {{ $report->reported_by }} pada {{ $report->created_at->format('d F Y') }}</p>

        <a href="{{ url()->previous() }}" class="btn btn-sm btn-outline-secondary mb-4">
             <i class="bi bi-arrow-left me-1"></i> Kembali
        </a>

        <div class="card card-form p-4">
            {{-- FORM UPDATE STATUS DAN PRIORITAS --}}
            <form action="{{ route('reports.update', $report->id) }}" method="POST">
                @csrf
                @method('PUT')
                
                {{-- INFORMASI KEJADIAN (READONLY) --}}
                <h5 class="mb-3 text-primary fw-semibold">Informasi Kejadian (Pelapor)</h5>
                <hr>
                
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="tanggalLapor" class="form-label fw-semibold">Tanggal Lapor</label>
                        <input type="text" class="form-control" id="tanggalLapor" value="{{ $report->created_at->format('d M Y, H:i') }}" readonly disabled>
                    </div>
                    
                    <div class="col-md-6 mb-3">
                        <label for="waktuKejadian" class="form-label fw-semibold">Tanggal & Waktu Kejadian</label>
                        <input type="text" class="form-control" id="waktuKejadian" value="{{ $report->incident_datetime ? \Carbon\Carbon::parse($report->incident_datetime)->format('d M Y, H:i') : 'N/A' }}" readonly disabled>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="lokasi" class="form-label fw-semibold">Lokasi Kejadian</label>
                        <input type="text" class="form-control" id="lokasi" value="{{ $report->location }}" readonly disabled>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="jenisInsiden" class="form-label fw-semibold">Jenis Insiden</label>
                        <input type="text" class="form-control" id="jenisInsiden" value="{{ $report->type }}" readonly disabled>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="dampak" class="form-label fw-semibold">Perkiraan Dampak</label>
                        <input type="text" class="form-control" id="dampak" value="{{ $report->impact }}" readonly disabled>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="pihakTerlibat" class="form-label fw-semibold">Pihak/Orang yang Terlibat</label>
                        <input type="text" class="form-control" id="pihakTerlibat" value="{{ $report->involved_parties ?? 'Tidak ada data' }}" readonly disabled>
                    </div>
                </div>

                <div class="mb-3">
                    <label for="deskripsi" class="form-label fw-semibold">Keterangan/Deskripsi Insiden</label>
                    <textarea class="form-control" id="deskripsi" rows="4" readonly disabled>{{ $report->description }}</textarea>
                </div>

                {{-- LAMPIRAN BUKTI (Diambil dari show.blade.php yang sudah disempurnakan) --}}
                <div class="mb-4">
                    <label class="form-label fw-semibold">Lampiran Bukti</label>
                    @if($report->media_path)
                        @php
                            $extension = pathinfo($report->media_path, PATHINFO_EXTENSION);
                            $extension = strtoupper($extension);
                        @endphp
                        <p class="ms-2">
                            <i class="bi bi-paperclip me-1"></i> 
                            <a href="{{ asset('storage/' . $report->media_path) }}" target="_blank">
                                Lihat File Bukti ({{ $extension }})
                            </a>
                        </p>
                    @else
                        <p class="ms-2 text-muted">Tidak ada lampiran.</p>
                    @endif
                </div>
                
                {{-- KEPUTUSAN SUPERVISOR (FORM BISA DIEDIT) --}}
                <h5 class="mt-4 mb-3 text-success fw-semibold">Keputusan Supervisor K3</h5>
                <hr>
                
                {{-- BIDANG BARU: FEEDBACK SPV --}}
                <div class="mb-4">
                    <label for="spv_feedback" class="form-label fw-semibold">Feedback / Catatan Tindak Lanjut</label>
                    <textarea class="form-control @error('spv_feedback') is-invalid @enderror" id="spv_feedback" name="spv_feedback" rows="5" placeholder="Tuliskan hasil investigasi singkat, tindak lanjut yang diperlukan, atau keputusan peninjauan di sini.">{{ old('spv_feedback', $report->spv_feedback) }}</textarea>
                    @error('spv_feedback') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="status" class="form-label fw-semibold text-success">Status Insiden <span class="text-danger">*</span></label>
                        <select class="form-select" id="status" name="status" required>
                            @foreach($statuses as $status)
                                <option value="{{ $status->name }}" @if($report->status == $status->name) selected @endif>
                                    {{ $status->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="priority" class="form-label fw-semibold text-success">Prioritas Insiden <span class="text-danger">*</span></label>
                        <select class="form-select" id="priority" name="priority" required>
                            @foreach($priorities as $priority)
                                <option value="{{ $priority->name }}" @if($report->priority == $priority->name) selected @endif>
                                    {{ $priority->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="d-grid mt-4">
                    <button type="submit" class="btn btn-lg btn-k3-update">
                        <i class="bi bi-save me-2"></i> Perbarui Status & Prioritas Laporan
                    </button>
                </div>
            </form>
            {{-- AKHIR FORM --}}
        </div>
        
        {{-- RIWAYAT STATUS LOG BARU --}}
        <h5 class="mt-4 mb-3 text-secondary fw-semibold">Riwayat Status Laporan</h5>
        <hr>
        
        @forelse($report->statusLogs->sortByDesc('action_at') as $log)
            <div class="log-entry border-start border-3 border-{{ $log->new_status == 'Closed' ? 'secondary' : 'secondary' }} ps-3 mb-3 card card-k3 p-3">
                <p class="small mb-1">
                    <strong>{{ $log->user->name ?? 'Sistem' }}</strong> mengubah status pada 
                    {{ \Carbon\Carbon::parse($log->action_at)->format('d M Y H:i') }}
                </p>
                
                @if ($log->old_status != $log->new_status)
                    <p class="small mb-0">Status: 
                        <span class="badge text-bg-secondary">{{ $log->old_status ?? 'Diajukan' }}</span> 
                        <i class="bi bi-arrow-right"></i> 
                        <span class="badge text-bg-info">{{ $log->new_status }}</span>
                    </p>
                @endif
                
                @if ($log->old_priority != $log->new_priority)
                    <p class="small mb-0">Prioritas diubah dari 
                        <span class="badge text-bg-secondary">{{ $log->old_priority ?? 'Rendah' }}</span> ke 
                        <span class="badge text-bg-danger">{{ $log->new_priority }}</span>
                    </p>
                @endif
                
                @if ($log->feedback)
                    <p class="small mt-1 mb-0 fst-italic">Catatan: "{{ $log->feedback }}"</p>
                @endif
            </div>
        @empty
            <p class="text-muted small">Belum ada riwayat perubahan status.</p>
        @endforelse
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>