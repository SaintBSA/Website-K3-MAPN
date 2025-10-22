<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Master Settings - MAPN Group</title>
    {{-- Import CSS yang sudah Anda gunakan --}}
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        /* Gaya Kustom dari Sidebar/Dashboard Anda */
        body { font-family: 'Inter', sans-serif; background-color: #f4f7f9; }
        .sidebar { width: 250px; min-height: 100vh; background-color: #ffffff; box-shadow: 2px 0 5px rgba(0, 0, 0, 0.05); position: fixed; top: 0; left: 0; padding-top: 20px; }
        .main-content { margin-left: 250px; padding: 30px; }
        .card-k3 { border: none; border-radius: 12px; box-shadow: 0 4px 10px rgba(0, 0, 0, 0.05); }
        .nav-link.active { background-color: #e6f7ff !important; color: #0d6efd !important; border-left: 4px solid #0d6efd; font-weight: 600; }
        .nav-link { transition: all 0.2s; color: #212529; }
    </style>
</head>
<body>

    {{-- SIDEBAR --}}
    <div class="sidebar d-flex flex-column">
        <div class="p-3 mb-4 text-center">
            <h5 class="fw-bold text-primary">K3 MAPN System ({{ strtoupper(Auth::user()->role) }})</h5>
        </div>
        <nav class="nav flex-column px-3">
            <a class="nav-link text-dark rounded mb-1" href="{{ route('home') }}">
                <i class="bi bi-speedometer2 me-2"></i> Dashboard
            </a>
            <a class="nav-link text-dark rounded mb-1" href="{{ route('reports.index') }}">
                <i class="bi bi-card-checklist me-2"></i> Laporan
            </a>
            <a class="nav-link text-dark rounded mb-1" href="{{ route('profile.edit') }}">
                <i class="bi bi-person-circle me-2"></i> Profil & Akun
            </a>
            <a class="nav-link text-dark rounded mb-1" href="{{ route('user.index') }}">
        <i class="bi bi-people me-2"></i> User Management
    </a>
            <a class="nav-link active rounded mb-1" href="{{ route('master.settings') }}">
                <i class="bi bi-gear-fill me-2"></i> Master Settings
            </a>
            <a class="nav-link text-danger mt-4 rounded" href="{{ route('logout') }}" 
               onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                <i class="bi bi-box-arrow-right me-2"></i> Keluar
            </a>
             <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none"> @csrf </form>
        </nav>
    </div>

    <div class="main-content">
        <h1 class="h3 fw-bold mb-4">Master Settings</h1>
        
        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        @if (session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif
        @if ($errors->any())
            <div class="alert alert-danger">Gagal menambahkan opsi baru. Pastikan nama tidak duplikat dalam kategori yang sama.</div>
        @endif

        {{-- FORM TAMBAH OPSI BARU --}}
        <div class="card card-k3 p-4 mb-5">
    <h5 class="fw-bold mb-3">Tambah Opsi Baru</h5>
    <form method="POST" action="{{ route('master.store') }}">
        @csrf
        <div class="row g-3 align-items-end"> {{-- **PENTING: Tambahkan align-items-end di sini** --}}
            
            {{-- KOLOM 1: KATEGORI (4 kolom lebar) --}}
            <div class="col-md-4">
                <label for="category" class="form-label">Kategori</label>
                <select name="category" id="category" class="form-select @error('category') is-invalid @enderror" required>
                    <option value="">Pilih Kategori</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat }}" {{ old('category') == $cat ? 'selected' : '' }}>{{ $cat }}</option>
                    @endforeach
                </select>
                @error('category') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
            
            {{-- KOLOM 2: NAMA OPSI BARU (5 kolom lebar) --}}
            <div class="col-md-5">
                <label for="name" class="form-label">Nama Opsi Baru</label>
                <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}" required>
                @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
            
            {{-- KOLOM 3: TOMBOL SUBMIT (3 kolom lebar) --}}
            <div class="col-md-3 d-flex align-items-end"> 
                {{-- d-flex align-items-end memastikan tombol sejajar dengan dasar input di sebelahnya --}}
                <button type="submit" class="btn btn-primary w-100">Tambah Opsi</button>
            </div>
        </div>
    </form>
</div>

        {{-- TABEL MASTER OPTIONS YANG DIKELOMPOKKAN --}}
        <h2 class="fw-bold mb-4">Daftar Opsi Master</h2>

        @forelse($options as $category => $items)
            <div class="card card-k3 p-4 mb-4">
                <h5 class="fw-bold text-success mb-3">{{ strtoupper($category) }}</h5>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead>
                            <tr>
                                <th style="width: 70%;">Nama Opsi</th>
                                <th style="width: 15%;" class="text-center">Status (Aktif)</th>
                                <th style="width: 15%;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($items as $item)
                                <tr>
                                    <td>{{ $item->name }}</td>
                                    <td class="text-center">
                                        <span class="badge text-bg-{{ $item->is_active ? 'success' : 'secondary' }}">
                                            {{ $item->is_active ? 'Aktif' : 'Nonaktif' }}
                                        </span>
                                    </td>
                                    <td>
                                        <form method="POST" action="{{ route('master.update', $item->id) }}" style="display:inline;">
                                            @csrf
                                            @method('PUT')
                                            {{-- Hidden input untuk trigger toggle status --}}
                                            <input type="hidden" name="toggle_status" value="1"> 
                                            <button type="submit" class="btn btn-sm btn-{{ $item->is_active ? 'outline-danger' : 'outline-success' }}">
                                                {{ $item->is_active ? 'Nonaktifkan' : 'Aktifkan' }}
                                            </button>
                                        </form>
                                        
                                        {{-- Penghapusan dihilangkan, hanya Nonaktifkan/Aktifkan --}}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @empty
            <p class="alert alert-info">Belum ada opsi master yang ditambahkan.</p>
        @endforelse

    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>