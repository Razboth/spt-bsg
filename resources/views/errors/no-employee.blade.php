<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Data Pegawai Belum Tersedia</title>
    <link href="{{ asset('assets/plugins/global/plugins.bundle.css') }}" rel="stylesheet" type="text/css"/>
    <link href="{{ asset('assets/css/style.bundle.css') }}" rel="stylesheet" type="text/css"/>
</head>
<body class="bg-body">
<div class="d-flex flex-column flex-center min-vh-100 p-10">
    <div class="card shadow-sm w-100" style="max-width: 560px">
        <div class="card-body text-center py-15">
            <i class="fa-solid fa-user-slash fs-5x text-warning mb-8"></i>
            <h1 class="fw-bold text-dark fs-2 mb-4">Data Pegawai Belum Tersedia</h1>
            <p class="text-muted fs-5 mb-8">
                {{ $message ?? 'Akun Anda belum terhubung dengan data pegawai.' }}
            </p>
            <form action="{{ route('logout') }}" method="POST" class="d-inline">
                @csrf
                <button type="submit" class="btn btn-light me-2">Keluar</button>
            </form>
            <a href="{{ url()->previous() }}" class="btn btn-primary">Kembali</a>
        </div>
    </div>
</div>
</body>
</html>
