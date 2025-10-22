<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pengaturan Profil</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        /* CSS DARI FILE LAIN UNTUK KONSISTENSI */
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
        }
        .nav-link.active {
            background-color: #e6f7ff !important;
            color: #0d6efd !important;
            border-left: 4px solid #0d6efd;
            font-weight: 600;
        }
        .nav-link {
            transition: all 0.2s;
            color: #212529; /* Pastikan teks non-aktif berwarna gelap */
        }
    </style>
</head>
<body>

<div class="sidebar d-flex flex-column">
    <div class="p-3 mb-4 text-center">
        <h5 class="fw-bold text-primary">K3 MAPN System ({{ strtoupper(Auth::user()->role) }})</h5>
    </div>
    
    {{-- START: NAVIGASI YANG DIMINTA --}}
    <nav class="nav flex-column px-3">
        
        {{-- DASHBOARD --}}
        <a class="nav-link text-dark rounded mb-1" href="{{ route('home') }}">
            <i class="bi bi-speedometer2 me-2"></i> Dashboard
        </a>
        
        {{-- FITUR 3: INPUT FORM LAPORAN (Hanya Admin) --}}
        @if(Auth::user()->role === 'admin')
            <a class="nav-link text-dark rounded mb-1" href="{{ route('reports.create') }}">
                <i class="bi bi-file-earmark-text me-2"></i> Tambah Kejadian
            </a>
        @endif

        {{-- FITUR 5: LIHAT RIWAYAT DETAIL LAPORAN (Admin & SPV) --}}
        @if(Auth::user()->role === 'admin' || Auth::user()->role === 'spv')
            <a class="nav-link text-dark rounded mb-1" href="{{ route('reports.index') }}">
                <i class="bi bi-card-checklist me-2"></i> Laporan
            </a>
        @endif

        {{-- PENGATURAN PROFIL (Admin & SPV) - AKTIF DI HALAMAN INI --}}
        <a class="nav-link active rounded mb-1" href="{{ route('profile.edit') }}">
            <i class="bi bi-person-circle me-2"></i> Profil & Akun
        </a>

        @if(Auth::user()->role === 'spv')
    <a class="nav-link text-dark rounded mb-1" href="{{ route('user.index') }}">
        <i class="bi bi-people me-2"></i> User Management
    </a>
@endif
        
        {{-- PENGATURAN MASTER (Hanya SPV) --}}
        @if(Auth::user()->role === 'spv')
            <a class="nav-link text-dark rounded mb-1" href="{{ route('master.settings') }}">
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
    {{-- END: NAVIGASI YANG DIMINTA --}}
</div>

<div class="main-content">
    <div class="container-fluid">
        <h1 class="h3 fw-bold mb-4">Pengaturan Profil</h1>

        <div class="row">
            <div class="col-md-6">
                <div class="card card-k3">
                    <div class="card-header bg-primary text-white" style="border-top-left-radius: 12px; border-top-right-radius: 12px;">
                        Update Informasi Akun
                    </div>
                    <div class="card-body">
                        
                        {{-- Pesan Sukses --}}
                        @if (session('success'))
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                Berhasil! {{ session('success') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        @endif

                        {{-- Formulir Update --}}
                        <form method="POST" action="{{ route('profile.update') }}">
                            @csrf
                            @method('PUT') {{-- Penting: Gunakan method PUT --}}

                            {{-- Nama --}}
                            <div class="mb-3">
                                <label for="name" class="form-label">Nama Lengkap</label>
                                <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', Auth::user()->name) }}" required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Email --}}
                            <div class="mb-3">
                                <label for="email" class="form-label">Alamat Email</label>
                                <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email', Auth::user()->email) }}" required>
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <hr class="my-4">
                            <p class="text-muted small">Isi kolom di bawah ini HANYA JIKA Anda ingin mengubah password Anda.</p>

                            {{-- Password Baru --}}
                            <div class="mb-3">
                                <label for="password" class="form-label">Password Baru</label>
                                <input type="password" class="form-control @error('password') is-invalid @enderror" id="password" name="password" placeholder="Minimal 8 karakter">
                                @error('password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Konfirmasi Password --}}
                            <div class="mb-3">
                                <label for="password_confirmation" class="form-label">Konfirmasi Password Baru</label>
                                <input type="password" class="form-control" id="password_confirmation" name="password_confirmation">
                                <small class="form-text text-muted">Harus sama dengan Password Baru di atas.</small>
                            </div>
                            
                            <button type="submit" class="btn btn-success mt-3"><i class="bi bi-save me-2"></i> Simpan Perubahan</button>
                        </form>

                    </div>
                </div>
            </div>
            
            <div class="col-md-6">
                <div class="card card-k3">
                    <div class="card-header bg-primary text-white" style="border-top-left-radius: 12px; border-top-right-radius: 12px;">
                        Informasi Akun
                    </div>
                    <div class="card-body">
                        <dl class="row">
                            <dt class="col-sm-4">Role Akun</dt>
                            <dd class="col-sm-8"><span class="badge text-bg-primary">{{ strtoupper(Auth::user()->role) }}</span></dd>

                            <dt class="col-sm-4">Nama User</dt>
                            <dd class="col-sm-8">{{ Auth::user()->name }}</dd>

                            <dt class="col-sm-4">Email User</dt>
                            <dd class="col-sm-8">{{ Auth::user()->email }}</dd>
                            
                            <dt class="col-sm-4">Bergabung Sejak</dt>
                            <dd class="col-sm-8">{{ Auth::user()->created_at->format('d M Y') }}</dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>