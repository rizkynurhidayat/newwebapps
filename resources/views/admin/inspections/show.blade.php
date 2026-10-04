@extends('layouts.admin')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <div class="flex items-center space-x-3">
                <h1 class="text-2xl font-bold tracking-tight text-slate-900">{{ $inspection->inspection_number }}</h1>
                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold shadow-xs {{ $inspection->result_status->badgeColor() }}">
                    {{ $inspection->result_status->label() }}
                </span>
            </div>
            <p class="text-sm text-slate-500 mt-1">Laporan resmi hasil inspeksi kualitas dan kalkulasi tingkat Sigma per batch.</p>
        </div>
        <div class="flex items-center space-x-2">
            <button onclick="window.print()" class="px-3 py-2 bg-white hover:bg-slate-50 text-slate-700 border border-slate-200 text-xs font-semibold rounded-lg shadow-sm">
                Cetak Sertifikat QC
            </button>
            <a href="{{ route('admin.inspections.index') }}" class="px-4 py-2 bg-slate-900 hover:bg-slate-800 text-white text-xs font-semibold rounded-lg shadow-sm">
                &larr; Kembali ke Daftar
            </a>
        </div>
    </div>

    <!-- Six Sigma KPI Scorecard -->
    <div class="bg-gradient-to-br from-slate-900 via-slate-800 to-slate-900 rounded-2xl p-6 text-white shadow-lg border border-slate-700">
        <div class="flex items-center justify-between border-b border-slate-700/80 pb-4 mb-4">
            <div>
                <span class="text-xs font-bold uppercase tracking-wider text-emerald-400">Six Sigma Quality Metric Certificate</span>
                <p class="text-xs text-slate-400 mt-0.5">Metodologi DMAIC &bull; Konversi Standar Pergeseran 1.5 &sigma;</p>
            </div>
            <div class="text-right">
                <span class="text-xs text-slate-400 block font-mono">Lot: {{ $inspection->productionBatch?->batch_number }}</span>
                <span class="text-xs font-semibold text-slate-200">{{ $inspection->productionBatch?->product?->name }}</span>
            </div>
        </div>

        <div class="grid grid-cols-2 sm:grid-cols-5 gap-4 text-center">
            <div class="p-3 bg-slate-800/80 rounded-xl border border-slate-700/60">
                <span class="text-[10px] text-slate-400 uppercase font-semibold block">Sampel / Cacat</span>
                <span class="text-lg font-bold text-white mt-1 block font-mono">{{ $inspection->sample_size_inspected }} / {{ $inspection->total_defects_count }}</span>
                <span class="text-[10px] text-slate-400">Unit N / Cacat D</span>
            </div>

            <div class="p-3 bg-slate-800/80 rounded-xl border border-slate-700/60">
                <span class="text-[10px] text-slate-400 uppercase font-semibold block">DPU</span>
                <span class="text-lg font-bold text-white mt-1 block font-mono">{{ $inspection->dpu }}</span>
                <span class="text-[10px] text-slate-400">Defects Per Unit</span>
            </div>

            <div class="p-3 bg-slate-800/80 rounded-xl border border-slate-700/60">
                <span class="text-[10px] text-slate-400 uppercase font-semibold block">DPMO</span>
                <span class="text-lg font-bold text-purple-300 mt-1 block font-mono">{{ number_format($inspection->dpmo, 0, ',', '.') }}</span>
                <span class="text-[10px] text-slate-400">Peluang Cacat/Juta</span>
            </div>

            <div class="p-3 bg-slate-800/80 rounded-xl border border-slate-700/60">
                <span class="text-[10px] text-slate-400 uppercase font-semibold block">Process Yield</span>
                <span class="text-lg font-bold text-emerald-400 mt-1 block font-mono">{{ $inspection->yield_percentage }}%</span>
                <span class="text-[10px] text-slate-400">Tingkat Lolos QC</span>
            </div>

            <div class="col-span-2 sm:col-span-1 p-3 bg-slate-800/80 rounded-xl border border-slate-700/60">
                <span class="text-[10px] text-slate-400 uppercase font-semibold block">Sigma Level</span>
                <div class="flex items-center justify-center space-x-1 mt-1">
                    <span class="text-2xl font-black text-amber-400 font-mono">{{ $inspection->sigma_level }}</span>
                    <span class="text-xs font-bold text-amber-300">&sigma;</span>
                </div>
                <span class="text-[10px] font-semibold text-amber-400">
                    {{ $inspection->sigma_level >= 4.0 ? 'Kualitas Optimal' : ($inspection->sigma_level >= 3.0 ? 'Batas Kontrol' : 'Reject') }}
                </span>
            </div>
        </div>
    </div>

    <!-- Inspection Metadata Details -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div class="bg-white rounded-xl border border-slate-200 p-5 shadow-sm space-y-3">
            <h2 class="text-xs font-bold uppercase tracking-wider text-slate-400">Informasi Produksi & Batch</h2>
            <div class="grid grid-cols-2 gap-2 text-xs">
                <span class="text-slate-500">Nomor Lot Produksi:</span>
                <a href="{{ route('admin.batches.show', $inspection->productionBatch) }}" class="font-mono font-bold text-emerald-600 hover:underline">
                    {{ $inspection->productionBatch?->batch_number }}
                </a>

                <span class="text-slate-500">Part Produk:</span>
                <span class="font-medium text-slate-900">{{ $inspection->productionBatch?->product?->name }}</span>

                <span class="text-slate-500">Part Number:</span>
                <span class="font-mono text-slate-700">{{ $inspection->productionBatch?->product?->part_number }}</span>

                <span class="text-slate-500">Lini & Mesin:</span>
                <span class="font-medium text-slate-900">{{ $inspection->productionBatch?->productionLine?->name }}</span>

                <span class="text-slate-500">Shift Kerja:</span>
                <span class="text-slate-700">{{ $inspection->productionBatch?->shift?->value }}</span>
            </div>
        </div>

        <div class="bg-white rounded-xl border border-slate-200 p-5 shadow-sm space-y-3">
            <h2 class="text-xs font-bold uppercase tracking-wider text-slate-400">Informasi Pengujian Mutu</h2>
            <div class="grid grid-cols-2 gap-2 text-xs">
                <span class="text-slate-500">Petugas Pemeriksa (QC):</span>
                <span class="font-medium text-slate-900">{{ $inspection->inspector?->name }}</span>

                <span class="text-slate-500">Waktu Pemeriksaan:</span>
                <span class="text-slate-800 font-medium">
                    {{ $inspection->period_label }}
                    <span class="text-[11px] text-slate-400 block font-normal">{{ $inspection->inspection_time?->format('d F Y, H:i') }} WIB</span>
                </span>

                <span class="text-slate-500">Tahap Inspeksi:</span>
                <span class="font-medium text-slate-900">{{ $inspection->inspection_stage?->label() }}</span>

                <span class="text-slate-500">Peluang Cacat (CTQ):</span>
                <span class="font-bold text-indigo-600">{{ $inspection->productionBatch?->product?->defect_opportunities_per_unit }} Titik per Unit</span>

                <span class="text-slate-500">Unit Lulus:</span>
                <span class="font-bold text-emerald-600 font-mono">{{ $inspection->passed_qty }} pcs ({{ $inspection->yield_percentage }}%)</span>
            </div>
        </div>
    </div>

    <!-- Defect Breakdown Table -->
    <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="p-5 border-b border-slate-100 flex items-center justify-between">
            <div>
                <h2 class="text-sm font-bold text-slate-900">Rincian Temuan Cacat (*Defect Breakdown*)</h2>
                <p class="text-xs text-slate-500">Detail jenis cacat yang ditemukan saat pengujian sampling.</p>
            </div>
            @if($inspection->inspectionDefects->count() > 0)
                <a href="{{ route('admin.capa.create') }}" class="px-3 py-1.5 bg-amber-50 hover:bg-amber-100 text-amber-800 border border-amber-200 text-xs font-semibold rounded-lg transition-colors">
                    + Buat Tiket CAPA Perbaikan
                </a>
            @endif
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50 text-[11px] font-semibold uppercase text-slate-500 border-b border-slate-200">
                        <th class="py-3 px-4">Kode Cacat</th>
                        <th class="py-3 px-4">Nama Jenis Cacat</th>
                        <th class="py-3 px-4">Keparahan</th>
                        <th class="py-3 px-4">Jumlah (Qty)</th>
                        <th class="py-3 px-4">Kategori 5M+1E</th>
                        <th class="py-3 px-4">Catatan Temuan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-xs">
                    @forelse($inspection->inspectionDefects as $defect)
                        <tr class="hover:bg-slate-50/80 transition-colors">
                            <td class="py-3 px-4 font-mono font-bold text-slate-900">{{ $defect->defectType?->code }}</td>
                            <td class="py-3 px-4 font-medium text-slate-900">{{ $defect->defectType?->name }}</td>
                            <td class="py-3 px-4">
                                <span class="inline-flex items-center px-2.5 py-1 rounded-md text-[11px] font-bold shadow-xs {{ $defect->defectType?->severity->badgeColor() }}">
                                    {{ $defect->defectType?->severity->label() }}
                                </span>
                            </td>
                            <td class="py-3 px-4 font-mono font-bold text-rose-600">{{ $defect->defect_qty }} unit</td>
                            <td class="py-3 px-4">
                                <span class="inline-flex items-center px-2.5 py-1 rounded-md text-[11px] font-bold shadow-xs {{ $defect->root_cause_category->badgeColor() }}">
                                    {{ $defect->root_cause_category->label() }}
                                </span>
                            </td>
                            <td class="py-3 px-4 text-slate-600">{{ $defect->root_cause_notes ?: '-' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-8 text-center text-emerald-600 font-medium">
                                <div class="inline-flex items-center space-x-2">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                    <span>Tidak ditemukan cacat fisik pada sampel ini (*Zero Defects - Perfect Yield*).</span>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
