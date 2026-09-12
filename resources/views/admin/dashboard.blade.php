@extends('layouts.admin')

@section('content')
<div class="space-y-6">
    <!-- Page Header & Action Shortcuts -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 pb-2">
        <div>
            <h1 class="text-2xl font-bold tracking-tight text-slate-900">Dashboard Pengendalian Mutu & Six Sigma</h1>
            <p class="text-sm text-slate-500 mt-1">Pemantauan real-time performa proses produksi, tingkat cacat (*defect*), dan kapabilitas sigma lini pabrik.</p>
        </div>
        <div class="flex flex-wrap items-center gap-2">
            <a href="{{ route('admin.inspections.create') }}" class="inline-flex items-center px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-semibold rounded-lg shadow-sm transition-colors">
                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                + Input Inspeksi QC
            </a>
            <a href="{{ route('admin.batches.create') }}" class="inline-flex items-center px-4 py-2 bg-slate-900 hover:bg-slate-800 text-white text-xs font-semibold rounded-lg shadow-sm transition-colors">
                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                + Jadwal Batch Baru
            </a>
            <a href="{{ route('admin.analytics.index') }}" class="inline-flex items-center px-4 py-2 bg-white hover:bg-slate-50 text-slate-700 border border-slate-200 text-xs font-semibold rounded-lg shadow-sm transition-colors">
                <svg class="w-4 h-4 mr-1.5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z"/>
                </svg>
                Analisis DMAIC Lengkap
            </a>
        </div>
    </div>

    <!-- 5 Core Six Sigma Metric Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4">
        <!-- Sigma Level Card -->
        <div class="bg-white rounded-xl p-5 border border-slate-200 shadow-sm relative overflow-hidden">
            <div class="flex items-center justify-between">
                <span class="text-xs font-semibold uppercase tracking-wider text-slate-400">Rata-rata Sigma</span>
                <span class="px-2 py-0.5 rounded text-[10px] font-bold {{ $avgSigmaLevel >= 4.0 ? 'bg-emerald-100 text-emerald-800' : ($avgSigmaLevel >= 3.0 ? 'bg-amber-100 text-amber-800' : 'bg-rose-100 text-rose-800') }}">
                    {{ $avgSigmaLevel >= 4.0 ? 'Optimal' : ($avgSigmaLevel >= 3.0 ? 'Warning' : 'Critical') }}
                </span>
            </div>
            <div class="mt-3 flex items-baseline space-x-2">
                <span class="text-3xl font-extrabold text-slate-900">{{ number_format($avgSigmaLevel, 2) }}</span>
                <span class="text-sm font-semibold text-slate-400">&sigma;</span>
            </div>
            <p class="text-xs text-slate-500 mt-2">Target standar industri: &ge; 4.0 &sigma;</p>
            <div class="w-full bg-slate-100 rounded-full h-1.5 mt-3">
                <div class="bg-emerald-500 h-1.5 rounded-full" style="width: {{ min(100, ($avgSigmaLevel / 6) * 100) }}%"></div>
            </div>
        </div>

        <!-- Yield Percentage Card -->
        <div class="bg-white rounded-xl p-5 border border-slate-200 shadow-sm">
            <div class="flex items-center justify-between">
                <span class="text-xs font-semibold uppercase tracking-wider text-slate-400">Process Yield</span>
                <div class="p-1.5 rounded-lg bg-emerald-50 text-emerald-600">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>
            <div class="mt-3 flex items-baseline space-x-1">
                <span class="text-3xl font-extrabold text-slate-900">{{ number_format($avgYield, 1) }}</span>
                <span class="text-sm font-semibold text-slate-500">%</span>
            </div>
            <p class="text-xs text-slate-500 mt-2">Tingkat produk lolos QC tanpa cacat</p>
        </div>

        <!-- DPMO Card -->
        <div class="bg-white rounded-xl p-5 border border-slate-200 shadow-sm">
            <div class="flex items-center justify-between">
                <span class="text-xs font-semibold uppercase tracking-wider text-slate-400">Rata-rata DPMO</span>
                <div class="p-1.5 rounded-lg bg-purple-50 text-purple-600">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                    </svg>
                </div>
            </div>
            <div class="mt-3 flex items-baseline space-x-1">
                <span class="text-2xl font-extrabold text-slate-900">{{ number_format($avgDpmo, 0, ',', '.') }}</span>
            </div>
            <p class="text-xs text-slate-500 mt-2">Defects Per Million Opportunities</p>
        </div>

        <!-- Total Production Output -->
        <div class="bg-white rounded-xl p-5 border border-slate-200 shadow-sm">
            <div class="flex items-center justify-between">
                <span class="text-xs font-semibold uppercase tracking-wider text-slate-400">Output Produksi</span>
                <div class="p-1.5 rounded-lg bg-blue-50 text-blue-600">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                    </svg>
                </div>
            </div>
            <div class="mt-3 flex items-baseline space-x-1">
                <span class="text-2xl font-extrabold text-slate-900">{{ number_format($totalOutput, 0, ',', '.') }}</span>
                <span class="text-xs font-medium text-slate-400">unit</span>
            </div>
            <p class="text-xs text-slate-500 mt-2">Dari {{ $totalBatches }} batch diproduksi</p>
        </div>

        <!-- Active CAPA Actions -->
        <div class="bg-white rounded-xl p-5 border border-slate-200 shadow-sm">
            <div class="flex items-center justify-between">
                <span class="text-xs font-semibold uppercase tracking-wider text-slate-400">Tiket CAPA Aktif</span>
                <div class="p-1.5 rounded-lg bg-amber-50 text-amber-600">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                </div>
            </div>
            <div class="mt-3 flex items-baseline space-x-1">
                <span class="text-2xl font-extrabold {{ $openCapaCount > 0 ? 'text-amber-600' : 'text-slate-900' }}">{{ $openCapaCount }}</span>
                <span class="text-xs font-medium text-slate-400">tindakan</span>
            </div>
            <p class="text-xs text-slate-500 mt-2">Perbaikan cacat sedang berjalan</p>
        </div>
    </div>

    <!-- Interactive Charts Row (Pareto 80/20 & SPC p-Chart) -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Pareto Analysis Card -->
        <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-5">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h2 class="text-sm font-bold text-slate-900">Diagram Pareto Cacat Produk (Prinsip 80/20)</h2>
                    <p class="text-xs text-slate-500">20% jenis cacat dominan yang menyebabkan 80% masalah mutu (*Vital Few*).</p>
                </div>
                <span class="text-[11px] font-semibold px-2 py-0.5 bg-indigo-50 text-indigo-700 rounded border border-indigo-200">
                    {{ $paretoData['vital_few_count'] ?? 0 }} Vital Few
                </span>
            </div>
            <div class="h-64">
                <canvas id="paretoChart"></canvas>
            </div>
        </div>

        <!-- SPC p-Chart Control Chart -->
        <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-5">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h2 class="text-sm font-bold text-slate-900">Grafik Kendali Mutu (SPC p-Chart)</h2>
                    <p class="text-xs text-slate-500">Proporsi cacat per batch vs Batas Kendali Atas (UCL) & Bawah (LCL).</p>
                </div>
                <div class="flex items-center space-x-2 text-[11px] font-mono">
                    <span class="text-rose-600">UCL: {{ number_format($spcData['ucl'] * 100, 1) }}%</span>
                    <span class="text-emerald-600">CL: {{ number_format($spcData['cl'] * 100, 1) }}%</span>
                </div>
            </div>
            <div class="h-64">
                <canvas id="spcChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Bottom Tables: Recent Quality Inspections & Active Batches -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Recent Quality Inspections (2 Cols) -->
        <div class="lg:col-span-2 bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
            <div class="p-5 border-b border-slate-100 flex items-center justify-between">
                <div>
                    <h2 class="text-sm font-bold text-slate-900">Hasil Pemeriksaan QC Terkini</h2>
                    <p class="text-xs text-slate-500">Rekap inspeksi sampel dan perhitungan metrik Six Sigma per lot.</p>
                </div>
                <a href="{{ route('admin.inspections.index') }}" class="text-xs font-semibold text-emerald-600 hover:text-emerald-700">Lihat Semua &rarr;</a>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-slate-50 text-[11px] font-semibold uppercase text-slate-500 border-b border-slate-200">
                            <th class="py-3 px-4">No. Inspeksi</th>
                            <th class="py-3 px-4">Produk & Lot</th>
                            <th class="py-3 px-4">Sampel (N)</th>
                            <th class="py-3 px-4">Cacat (D)</th>
                            <th class="py-3 px-4">DPMO</th>
                            <th class="py-3 px-4">Sigma Level</th>
                            <th class="py-3 px-4">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-xs">
                        @forelse($recentInspections as $ins)
                            <tr class="hover:bg-slate-50/80 transition-colors">
                                <td class="py-3 px-4 font-mono font-semibold text-slate-900">
                                    <a href="{{ route('admin.inspections.show', $ins) }}" class="text-emerald-600 hover:underline">
                                        {{ $ins->inspection_number }}
                                    </a>
                                </td>
                                <td class="py-3 px-4">
                                    <div class="font-medium text-slate-900 truncate max-w-[180px]">{{ $ins->productionBatch?->product?->name ?? '-' }}</div>
                                    <div class="text-[11px] text-slate-400 font-mono">{{ $ins->productionBatch?->batch_number ?? '-' }}</div>
                                </td>
                                <td class="py-3 px-4 font-medium">{{ $ins->sample_size_inspected }}</td>
                                <td class="py-3 px-4 font-semibold {{ $ins->defective_units_qty > 0 ? 'text-rose-600' : 'text-emerald-600' }}">
                                    {{ $ins->defective_units_qty }} unit
                                </td>
                                <td class="py-3 px-4 font-mono">{{ number_format($ins->dpmo, 0, ',', '.') }}</td>
                                <td class="py-3 px-4">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-bold font-mono {{ $ins->sigma_level >= 4.0 ? 'bg-emerald-100 text-emerald-800' : ($ins->sigma_level >= 3.0 ? 'bg-amber-100 text-amber-800' : 'bg-rose-100 text-rose-800') }}">
                                        {{ $ins->sigma_level }} &sigma;
                                    </span>
                                </td>
                                <td class="py-3 px-4">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-medium border {{ $ins->result_status->badgeColor() }}">
                                        {{ $ins->result_status->label() }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="py-8 text-center text-slate-400">Belum ada data pemeriksaan QC.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Active / Scheduled Batches (1 Col) -->
        <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-5 flex flex-col">
            <div class="flex items-center justify-between mb-3">
                <h2 class="text-sm font-bold text-slate-900">Lot Produksi Aktif</h2>
                <a href="{{ route('admin.batches.index') }}" class="text-xs font-semibold text-emerald-600 hover:text-emerald-700">Lihat Semua</a>
            </div>
            <div class="space-y-3 flex-1 overflow-y-auto">
                @forelse($recentBatches as $batch)
                    <div class="p-3 rounded-lg border border-slate-100 bg-slate-50/50 hover:bg-slate-50 transition-colors">
                        <div class="flex items-center justify-between">
                            <span class="font-mono text-xs font-bold text-slate-900">{{ $batch->batch_number }}</span>
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-medium {{ $batch->status->badgeColor() }}">
                                {{ $batch->status->label() }}
                            </span>
                        </div>
                        <p class="text-xs font-medium text-slate-800 mt-1 truncate">{{ $batch->product?->name ?? '-' }}</p>
                        <div class="flex items-center justify-between text-[11px] text-slate-500 mt-2">
                            <span>{{ $batch->productionLine?->line_code ?? '-' }}</span>
                            <span class="font-mono">{{ number_format($batch->actual_qty) }} / {{ number_format($batch->target_qty) }} pcs</span>
                        </div>
                    </div>
                @empty
                    <p class="text-xs text-slate-400 text-center py-6">Tidak ada batch produksi aktif saat ini.</p>
                @endforelse
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    // 1. Render Pareto 80/20 Chart
    const paretoItems = @json($paretoData['items'] ?? []);
    if (paretoItems.length > 0) {
        const labels = paretoItems.map(item => item.defect_name.length > 18 ? item.defect_name.substring(0, 18) + '...' : item.defect_name);
        const counts = paretoItems.map(item => item.count);
        const cumulatives = paretoItems.map(item => item.cumulative_percentage);

        const ctxPareto = document.getElementById('paretoChart').getContext('2d');
        new Chart(ctxPareto, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [
                    {
                        label: 'Frekuensi Cacat (Unit)',
                        data: counts,
                        backgroundColor: '#10b981',
                        borderRadius: 4,
                        yAxisID: 'y',
                    },
                    {
                        label: 'Persentase Kumulatif (%)',
                        data: cumulatives,
                        type: 'line',
                        borderColor: '#f59e0b',
                        backgroundColor: '#f59e0b',
                        borderWidth: 2,
                        pointRadius: 4,
                        yAxisID: 'y1',
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        type: 'linear',
                        position: 'left',
                        beginAtZero: true,
                        title: { display: true, text: 'Jumlah Cacat' }
                    },
                    y1: {
                        type: 'linear',
                        position: 'right',
                        min: 0,
                        max: 100,
                        grid: { drawOnChartArea: false },
                        title: { display: true, text: 'Kumulatif %' }
                    }
                }
            }
        });
    }

    // 2. Render SPC p-Chart
    const spcPoints = @json($spcData['points'] ?? []);
    if (spcPoints.length > 0) {
        const spcLabels = spcPoints.map(p => p.label);
        const pPercentages = spcPoints.map(p => p.p * 100);
        const uclLine = Array(spcPoints.length).fill({{ $spcData['ucl'] * 100 }});
        const clLine = Array(spcPoints.length).fill({{ $spcData['cl'] * 100 }});
        const lclLine = Array(spcPoints.length).fill({{ $spcData['lcl'] * 100 }});

        const ctxSpc = document.getElementById('spcChart').getContext('2d');
        new Chart(ctxSpc, {
            type: 'line',
            data: {
                labels: spcLabels,
                datasets: [
                    {
                        label: 'Defect Rate (%)',
                        data: pPercentages,
                        borderColor: '#0284c7',
                        backgroundColor: '#0284c7',
                        borderWidth: 2,
                        pointRadius: 4,
                    },
                    {
                        label: 'UCL (Batas Atas)',
                        data: uclLine,
                        borderColor: '#ef4444',
                        borderDash: [5, 5],
                        borderWidth: 1.5,
                        pointRadius: 0,
                    },
                    {
                        label: 'CL (Garis Tengah)',
                        data: clLine,
                        borderColor: '#10b981',
                        borderWidth: 1.5,
                        pointRadius: 0,
                    },
                    {
                        label: 'LCL (Batas Bawah)',
                        data: lclLine,
                        borderColor: '#64748b',
                        borderDash: [5, 5],
                        borderWidth: 1.5,
                        pointRadius: 0,
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        title: { display: true, text: 'Proporsi Cacat (%)' }
                    }
                }
            }
        });
    }
});
</script>
@endpush
@endsection
