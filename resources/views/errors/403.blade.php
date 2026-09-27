<!DOCTYPE html>
<html lang="id" class="h-full bg-slate-900">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>403 - Akses Ditolak | Six Sigma Manufacturing System</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="h-full flex items-center justify-center p-6 text-slate-100">
    <div class="max-w-md w-full text-center space-y-6 bg-slate-800/80 border border-slate-700 p-8 rounded-2xl shadow-2xl backdrop-blur">
        <div class="w-16 h-16 mx-auto rounded-full bg-rose-500/10 border border-rose-500/30 flex items-center justify-center text-rose-500">
            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
            </svg>
        </div>
        <div>
            <span class="text-xs font-bold uppercase tracking-wider text-rose-400 font-mono">HTTP 403 &bull; Akses Dibatasi</span>
            <h1 class="text-2xl font-black text-white mt-1">Otorisasi Tidak Memadai</h1>
            <p class="text-xs text-slate-300 mt-2 leading-relaxed">
                {{ $exception->getMessage() ?: 'Anda tidak memiliki wewenang untuk mengakses halaman atau tindakan ini sesuai pembagian peran (Role-Based Access Control) pabrik.' }}
            </p>
        </div>
        <div class="pt-2">
            <a href="{{ route('dashboard') }}" class="inline-flex items-center justify-center px-5 py-2.5 bg-emerald-600 hover:bg-emerald-500 text-white text-xs font-semibold rounded-lg shadow-lg shadow-emerald-600/30 transition-all">
                &larr; Kembali ke Dashboard Eksekutif
            </a>
        </div>
    </div>
</body>
</html>
