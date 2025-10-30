<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrasi Akun - MAPN Group</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    
    <style>
        /* Gaya Kustom dari Halaman Login */
        body {
            font-family: 'Inter', sans-serif; 
            background-color: #f8f9fa;
        }
        .k3-background {
            /* Path harus menunjuk ke public/assets/image/safetybg.jpeg */
            background-image: url('{{ asset('assets/image/safetybg.jpeg') }}'); 
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
        }
        .k3-background::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.1);
            backdrop-filter: blur(2px);
        }
        .register-container {
            background-color: #ffffff; 
            padding: 40px;
            border-radius: 12px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
            max-width: 450px; /* Sedikit lebih lebar untuk form registrasi */
            width: 100%;
            position: relative;
            z-index: 10;
        }
        .logo-mapn {
            max-width: 120px;
            height: auto;
            margin-bottom: 25px !important;
        }
        .register-title {
            font-weight: 600;
            color: #343a40;
        }
        /* Style untuk form-floating */
        .form-floating input.form-control {
            border-radius: 8px;
            border-color: #ced4da; 
            min-height: calc(3.5rem + 2px); 
            padding-top: 1.5rem; 
        }
        .form-floating > label {
            padding: 1rem .75rem;
            font-size: 0.9rem;
        }
        .btn-primary {
            background-color: #0d6efd; 
            border-color: #0d6efd;
            border-radius: 8px;
            font-weight: 600;
        }
        /* Style untuk validasi Laravel */
        .form-floating .is-invalid {
            border-color: #dc3545!important;
        }
        .invalid-feedback {
            display: block;
            margin-top: 5px;
            text-align: left;
        }
    </style>
</head>
<body>

<div class="k3-background">
    <div class="register-container text-center">
        
        <img src="{{ asset('assets/OIP.jpeg') }}" alt="MAPN Logo" class="logo-mapn mb-4">
        
        <h1 class="h5 mb-2 register-title">Daftar Akun Baru</h1>
        <p class="text-muted mb-4">Silakan daftarkan diri Anda ke Sistem Pelaporan K3.</p>

        <form method="POST" action="{{ route('register') }}">
            @csrf

            {{-- NAMA LENGKAP --}}
            <div class="form-floating mb-3">
                <input id="name" type="text" class="form-control @error('name') is-invalid @enderror" 
                       name="name" value="{{ old('name') }}" required autocomplete="name" placeholder="Nama Lengkap" autofocus>
                <label for="name">Nama Lengkap</label>
                @error('name')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            {{-- EMAIL --}}
            <div class="form-floating mb-3">
                <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" 
                       name="email" value="{{ old('email') }}" required autocomplete="email" placeholder="Alamat Email">
                <label for="email">Alamat Email Perusahaan</label>
                @error('email')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            
            {{-- PASSWORD --}}
            <div class="form-floating mb-3">
                <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" 
                       name="password" required autocomplete="new-password" placeholder="Kata Sandi">
                <label for="password">Kata Sandi</label>
                @error('password')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            {{-- KONFIRMASI PASSWORD --}}
            <div class="form-floating mb-4">
                <input id="password-confirm" type="password" class="form-control" 
                       name="password_confirmation" required autocomplete="new-password" placeholder="Ulangi Kata Sandi">
                <label for="password-confirm">Ulangi Kata Sandi</label>
            </div>

            <div class="d-grid gap-2">
                <button type="submit" class="btn btn-lg btn-primary">
                    <i class="bi bi-person-plus me-2"></i> Daftar Akun
                </button>
            </div>
        </form>
        
        <hr class="my-4">
        
        <p class="mb-0 text-muted">
            Sudah punya akun? <a href="{{ route('login') }}" class="text-decoration-none fw-semibold">Masuk di sini</a>
        </p>
        
        <p class="mb-0 text-muted small mt-3">&copy; 2024 MAPN Group. Health & Safety System.</p>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>