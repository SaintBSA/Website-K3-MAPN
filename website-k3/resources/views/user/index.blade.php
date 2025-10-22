<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Management - MAPN Group</title>
    {{-- Sertakan CSS dari file lain --}}
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        /* CSS yang konsisten dari file lain */
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
            <a class="nav-link active rounded mb-1" href="{{ route('user.index') }}">
                <i class="bi bi-people me-2"></i> User Management
            </a>
            <a class="nav-link text-dark rounded mb-1" href="{{ route('master.settings') }}">
                <i class="bi bi-gear me-2"></i> Master Settings
            </a>
            <a class="nav-link text-danger mt-4 rounded" href="{{ route('logout') }}" 
               onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                <i class="bi bi-box-arrow-right me-2"></i> Keluar
            </a>
             <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none"> @csrf </form>
        </nav>
    </div>

    <div class="main-content">
        <h1 class="h3 fw-bold mb-4">User Role Management</h1>
        
        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        @if (session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif
        @if ($errors->any())
            <div class="alert alert-danger">Gagal memperbarui role.</div>
        @endif

        <div class="card card-k3 p-4 mb-5">
    <h5 class="fw-bold mb-3">Daftar Pengguna Aktif (Kecuali Anda)</h5>
    
    <div class="table-responsive">
        <table class="table table-striped align-middle mb-0">
            <thead>
                <tr>
                    <th style="width: 25%;">Nama</th>
                    <th style="width: 30%;">Email</th>
                    <th style="width: 15%;">Role</th>
                    <th style="width: 15%;" class="text-end">Ubah Role</th>
                </tr>
            </thead>
            <tbody>
                @forelse($users as $user)
                    <tr>
                        <td>{{ $user->name }}</td>
                        <td>{{ $user->email }}</td>
                        <td>
                            <span class="badge text-bg-{{ $user->role == 'admin' ? 'danger' : ($user->role == 'spv' ? 'primary' : 'secondary') }}">
                                {{ strtoupper($user->role ?? 'Unassigned') }}
                            </span>
                        </td>
                        <td class="text-end"> {{-- Seluruh aksi ditaruh di sini dan diratakan kanan --}}
                            {{-- FORM UPDATE ROLE --}}
                            <form method="POST" action="{{ route('user.update.role', $user->id) }}" style="display: flex; gap: 5px; justify-content: flex-end;">
                                @csrf
                                @method('PUT')
                                
                                <select name="role" class="form-select form-select-sm" style="width: 150px;" required>
                                    <option value="unassigned" {{ is_null($user->role) ? 'selected' : '' }}>Unassigned</option>
                                    @foreach($roles as $role)
                                        <option value="{{ $role }}" {{ $user->role == $role ? 'selected' : '' }}>
                                            {{ strtoupper($role) }}
                                        </option>
                                    @endforeach
                                </select>
                                <button type="submit" class="btn btn-sm btn-success" style="width: 81px;">Update</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="text-center">Tidak ada pengguna lain yang terdaftar.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>