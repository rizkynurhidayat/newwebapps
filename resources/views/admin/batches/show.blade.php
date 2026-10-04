@extends('layouts.admin')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <div class="flex items-center space-x-3">
                <h1 class="text-2xl font-bold tracking-tight text-slate-900">{{ $batch->batch_number }}</h1>
                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold shadow-xs {{ $batch->status->badgeColor() }}">
                    {{ $batch->status->label() }}
                </span>
            </div>
            <p class="text-sm text-slate-500 mt-1">Diproduksi tanggal {{ $batch->production_date?->format('d F Y') }} &bull; {{ $batch->shift?->value }}</p>
        </div>
        <div class="flex items-center space-x-2">
            @if(auth()->user()->isAdmin() || auth()->user()->isEmployee())
                <a href="{{ route('admin.batches.edit', $batch) }}" class="px-3 py-2 bg-white hover:bg-slate-50 text-slate-700 border border-slate-200 text-xs font-semibold rounded-lg shadow-sm">
                    Edit Batch
                </a>
            @endif
            @if(auth()->user()->isAdmin() || auth()->user()->isStaff())
                <a href="{{ route('admin.inspections.create', ['batch_id' => $batch->id]) }}" class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-semibold rounded-lg shadow-sm">
                    + Input Pemeriksaan QC
                </a>
            @endif
        </div>
    </div>

    <!-- Overview Grid -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div class="bg-white rounded-xl border border-slate-200 p-4 shadow-sm">
            <span class="text-xs text-slate-400 uppercase font-semibold">Produk Manufaktur</span>
            <div class="text-sm font-bold text-slate-900 mt-1">{{ $batch->product?->name }}</div>
            <div class="text-xs font-mono text-slate-500 mt-0.5">{{ $batch->product?->part_number }}</div>
            <div class="mt-2 text-[11px] text-indigo-600 font-medium bg-indigo-50 px-2 py-0.5 rounded inline-block">
                {{ $batch->product?->defect_opportunities_per_unit }} Titik Peluang Cacat (CTQ)
            </div>
        </div>

        <div class="bg-white rounded-xl border border-slate-200 p-4 shadow-sm">
            <span class="text-xs text-slate-400 uppercase font-semibold">Lini & Supervisor</span>
            <div class="text-sm font-bold text-slate-900 mt-1">{{ $batch->productionLine?->name }}</div>
            <div class="text-xs text-slate-500 mt-0.5">{{ $batch->productionLine?->location }}</div>
            <div class="text-xs text-slate-600 mt-2">PIC: <span class="font-medium">{{ $batch->supervisor?->name ?? '-' }}</span></div>
        </div>

        <div class="bg-white rounded-xl border border-slate-200 p-4 shadow-sm">
            <span class="text-xs text-slate-400 uppercase font-semibold">Progress Target Output</span>
            <div class="text-2xl font-extrabold text-slate-900 mt-1 font-mono">
                {{ number_format($batch->actual_qty) }} <span class="text-xs font-normal text-slate-400">/ {{ number_format($batch->target_qty) }} {{ $batch->product?->unit }}</span>
            </div>
            @php $progress = $batch->target_qty > 0 ? min(100, round(($batch->actual_qty / $batch->target_qty) * 100, 1)) : 0; @endphp
            <div class="w-full bg-slate-100 rounded-full h-1.5 mt-2">
                <div class="bg-emerald-500 h-1.5 rounded-full" style="width: {{ $progress }}%"></div>
            </div>
            <span class="text-[10px] text-slate-500 mt-1 block">{{ $progress }}% target terpenuhi</span>
        </div>

        <div class="bg-white rounded-xl border border-slate-200 p-4 shadow-sm">
            <span class="text-xs text-slate-400 uppercase font-semibold">Catatan Produksi</span>
            <p class="text-xs text-slate-600 mt-1 italic">{{ $batch->notes ?: 'Tidak ada catatan khusus.' }}</p>
        </div>
    </div>

    <!-- Quality Inspection Logs for this batch -->
    <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="p-5 border-b border-slate-100 flex items-center justify-between">
            <div>
                <h2 class="text-sm font-bold text-slate-900">Riwayat Pemeriksaan Kualitas (QC)</h2>
                <p class="text-xs text-slate-500">Hasil sampling, perhitungan DPU, DPMO, dan tingkat Sigma untuk lot ini.</p>
            </div>
            <span class="text-xs font-semibold px-2 py-0.5 bg-slate-100 rounded text-slate-700">
                {{ $batch->qualityInspections->count() }} Kali Pemeriksaan
            </span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50 text-[11px] font-semibold uppercase text-slate-500 border-b border-slate-200">
                        <th class="py-3 px-4">No. Inspeksi</th>
                        <th class="py-3 px-4">Waktu</th>
                        <th class="py-3 px-4">Tahap</th>
                        <th class="py-3 px-4">Sampel (N)</th>
                        <th class="py-3 px-4">Lulus / Cacat</th>
                        <th class="py-3 px-4">DPMO</th>
                        <th class="py-3 px-4">Sigma Level</th>
                        <th class="py-3 px-4">Yield %</th>
                        <th class="py-3 px-4">Hasil</th>
                        <th class="py-3 px-4 text-right">Detail</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-xs">
                    @forelse($batch->qualityInspections as $ins)
                        <tr class="hover:bg-slate-50/80 transition-colors">
                            <td class="py-3 px-4 font-mono font-bold text-emerald-600">
                                <a href="{{ route('admin.inspections.show', $ins) }}" class="hover:underline">
                                    {{ $ins->inspection_number }}
                                </a>
                            </td>
                            <td class="py-3 px-4 text-slate-600">{{ $ins->period_short_label ?? $ins->inspection_time?->format('d/m/Y H:i') }}</td>
                            <td class="py-3 px-4">{{ $ins->inspection_stage?->label() }}</td>
                            <td class="py-3 px-4 font-mono font-medium">{{ $ins->sample_size_inspected }}</td>
                            <td class="py-3 px-4 font-mono">
                                <span class="text-emerald-700 font-semibold">{{ $ins->passed_qty }}</span> / 
                                <span class="text-rose-600 font-semibold">{{ $ins->defective_units_qty }}</span>
                            </td>
                            <td class="py-3 px-4 font-mono">{{ number_format($ins->dpmo, 0, ',', '.') }}</td>
                            <td class="py-3 px-4">
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-bold font-mono {{ $ins->sigma_level >= 4.0 ? 'bg-emerald-100 text-emerald-800' : ($ins->sigma_level >= 3.0 ? 'bg-amber-100 text-amber-800' : 'bg-rose-100 text-rose-800') }}">
                                    {{ $ins->sigma_level }} &sigma;
                                </span>
                            </td>
                            <td class="py-3 px-4 font-bold font-mono">{{ $ins->yield_percentage }}%</td>
                            <td class="py-3 px-4">
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-medium border {{ $ins->result_status->badgeColor() }}">
                                    {{ $ins->result_status->label() }}
                                </span>
                            </td>
                            <td class="py-3 px-4 text-right">
                                <a href="{{ route('admin.inspections.show', $ins) }}" class="text-xs font-semibold text-emerald-600 hover:text-emerald-700">Lihat &rarr;</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10" class="py-8 text-center text-slate-400">Belum ada pemeriksaan kualitas untuk lot ini.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
