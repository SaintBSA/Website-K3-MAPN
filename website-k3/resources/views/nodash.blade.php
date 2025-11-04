<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Akses Terbatas</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
    body {
        font-family: 'Inter', sans-serif;
        background-color: #f4f7f9;
    }

    .unassigned-card {
        max-width: 600px;
        margin: 100px auto;
        text-align: center;
    }

    .card-k3 {
        border: none;
        border-radius: 12px;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.05);
    }
    </style>
</head>

<body>

    <div class="container unassigned-card">
        <div class="card card-k3 p-5">
            <h1 class="h3 fw-bold mb-3 text-primary">Selamat Datang, {{ Auth::user()->name }} 👋</h1>
            <p class="text-muted mb-4">
                Akun Anda telah berhasil didaftarkan. Anda saat ini belum memiliki akses penuh ke sistem pelaporan K3.
                <br>
                <br>
                Mohon tunggu hingga Supervisor menetapkan akses Anda.
            </p>

            <hr>

            <a class="btn btn-danger mt-3" href="{{ route('logout') }}"
                onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                <i class="bi bi-box-arrow-right me-2"></i> Logout
            </a>
            <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">@csrf</form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>