<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cetak Label Aset - {{ $item->code }}</title>
    @vite(['resources/css/app.css'])
    <style>
        @media print {
            body {
                background: white !important;
                padding: 0 !important;
            }
            .no-print {
                display: none !important;
            }
            .print-card {
                border: 2px solid black !important;
                box-shadow: none !important;
                page-break-inside: avoid;
            }
        }
    </style>
</head>
<body class="bg-slate-100 min-h-screen flex flex-col items-center justify-center p-6 font-sans">
    <!-- Action Bar -->
    <div class="mb-6 flex items-center space-x-3 no-print">
        <button onclick="window.print()" class="px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-semibold rounded-xl shadow-md transition-all flex items-center">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
            Cetak Label Sekarang
        </button>
        <button onclick="window.close()" class="px-4 py-2.5 bg-white border border-slate-200 text-slate-700 text-xs font-semibold rounded-xl hover:bg-slate-50 transition-all">
            Tutup
        </button>
    </div>

    <!-- Asset Tag Label (Ukuran Standar Stiker Aset Perusahaan) -->
    <div class="print-card w-[380px] bg-white border-2 border-slate-900 rounded-xl p-5 shadow-lg text-slate-900">
        <!-- Header Tag -->
        <div class="flex items-center justify-between border-b-2 border-slate-900 pb-2 mb-3">
            <div class="flex items-center space-x-2">
                <div class="w-6 h-6 bg-slate-900 text-white rounded flex items-center justify-center font-bold text-xs">
                    ★
                </div>
                <div>
                    <h3 class="text-xs font-black uppercase tracking-wider leading-tight">ASET MILIK PERUSAHAAN</h3>
                    <p class="text-[9px] text-slate-500 font-semibold leading-tight">DILARANG MEMINDAHKAN TANPA IZIN</p>
                </div>
            </div>
            <span class="text-[10px] font-bold text-slate-700 font-mono">{{ $item->category->code }}</span>
        </div>

        <!-- Body Info -->
        <div class="space-y-1 text-center py-1">
            <h4 class="text-sm font-black text-slate-900 leading-tight uppercase line-clamp-2">{{ $item->name }}</h4>
            <p class="text-[11px] text-slate-600 font-medium">Lokasi: <strong>{{ $item->location->name }}</strong></p>
        </div>

        <!-- Simulated Barcode Display -->
        <div class="my-3 py-2 px-4 bg-slate-50 rounded border border-slate-200 text-center">
            <!-- Simulated Barcode Lines -->
            <div class="h-10 flex items-center justify-center space-x-0.5 overflow-hidden">
                @for($i = 0; $i < 48; $i++)
                <div class="bg-black h-full {{ $i % 3 === 0 ? 'w-[3px]' : ($i % 2 === 0 ? 'w-[1.5px]' : 'w-[1px]') }}"></div>
                @endfor
            </div>
            <div class="font-mono text-xs font-bold tracking-widest mt-1.5 text-slate-900">
                {{ $item->barcode ?? $item->code }}
            </div>
        </div>

        <!-- Footer Tag -->
        <div class="border-t border-slate-200 pt-2 flex items-center justify-between text-[9px] text-slate-500">
            <span>Kode: <strong class="text-slate-800 font-mono">{{ $item->code }}</strong></span>
            <span>Tgl: {{ $item->purchase_date?->format('d/m/Y') ?? date('d/m/Y') }}</span>
        </div>
    </div>
</body>
</html>
