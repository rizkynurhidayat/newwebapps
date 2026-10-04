@extends('layouts.admin')

@section('content')
<div class="space-y-6">
    <!-- Page Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold tracking-tight text-slate-900">Analitik Mutu & Metodologi Six Sigma (DMAIC)</h1>
            <p class="text-sm text-slate-500 mt-1">Alat analisis statistik komprehensif: Diagram Pareto 80/20, Grafik Kendali Mutu (SPC p-Chart), dan Diagram Sebab-Akibat Ishikawa (5M+1E).</p>
        </div>
        <button onclick="window.print()" class="inline-flex items-center px-4 py-2 bg-white hover:bg-slate-50 text-slate-700 border border-slate-200 text-xs font-semibold rounded-lg shadow-sm transition-colors">
            <svg class="w-4 h-4 mr-1.5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
            </svg>
            Cetak Laporan Mutu
        </button>
    </div>

    <!-- Filter Multi-Kriteria Bar -->
    <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-sm">
        <form method="GET" action="{{ route('admin.analytics.index') }}" class="grid grid-cols-1 sm:grid-cols-5 gap-3">
            <div>
                <label class="block text-[11px] font-medium text-slate-500 mb-1">Produk Manufaktur</label>
                <select name="product_id" class="w-full text-xs rounded-lg border-slate-200 focus:border-emerald-500 focus:ring-emerald-500">
                    <option value="">-- Semua Produk --</option>
                    @foreach($products as $p)
                        <option value="{{ $p->id }}" {{ request('product_id') == $p->id ? 'selected' : '' }}>{{ $p->part_number }} - {{ $p->name }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-[11px] font-medium text-slate-500 mb-1">Lini Produksi</label>
                <select name="line_id" class="w-full text-xs rounded-lg border-slate-200 focus:border-emerald-500 focus:ring-emerald-500">
                    <option value="">-- Semua Lini --</option>
                    @foreach($lines as $l)
                        <option value="{{ $l->id }}" {{ request('line_id') == $l->id ? 'selected' : '' }}>{{ $l->line_code }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-[11px] font-medium text-slate-500 mb-1">Dari Tanggal</label>
                <input type="date" name="start_date" value="{{ request('start_date') }}" class="w-full text-xs rounded-lg border-slate-200 focus:border-emerald-500 focus:ring-emerald-500">
            </div>

            <div>
                <label class="block text-[11px] font-medium text-slate-500 mb-1">Sampai Tanggal</label>
                <input type="date" name="end_date" value="{{ request('end_date') }}" class="w-full text-xs rounded-lg border-slate-200 focus:border-emerald-500 focus:ring-emerald-500">
            </div>

            <div class="flex items-end space-x-2">
                <button type="submit" class="w-full py-2 bg-slate-900 hover:bg-slate-800 text-white text-xs font-semibold rounded-lg transition-colors">
                    Filter Analisis
                </button>
                @if(request()->anyFilled(['product_id', 'line_id', 'start_date', 'end_date']))
                    <a href="{{ route('admin.analytics.index') }}" class="px-3 py-2 text-xs text-slate-500 hover:text-slate-700">Reset</a>
                @endif
            </div>
        </form>
    </div>

    <!-- Summary KPI Cards -->
    <div class="grid grid-cols-2 sm:grid-cols-5 gap-4">
        <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-sm text-center">
            <span class="text-[10px] text-slate-400 uppercase font-semibold block">Total Sampel Diuji (N)</span>
            <span class="text-xl font-extrabold text-slate-900 mt-1 block font-mono">{{ number_format($totalInspected) }}</span>
            <span class="text-[10px] text-slate-400">Unit fisik diperiksa</span>
        </div>

        <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-sm text-center">
            <span class="text-[10px] text-slate-400 uppercase font-semibold block">Total Cacat Fisik (D)</span>
            <span class="text-xl font-extrabold text-rose-600 mt-1 block font-mono">{{ number_format($totalDefects) }}</span>
            <span class="text-[10px] text-slate-400">Dari {{ $totalDefectiveUnits }} unit reject</span>
        </div>

        <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-sm text-center">
            <span class="text-[10px] text-slate-400 uppercase font-semibold block">Rata-rata Yield</span>
            <span class="text-xl font-extrabold text-emerald-600 mt-1 block font-mono">{{ $avgYield }}%</span>
            <span class="text-[10px] text-slate-400">Tingkat kelulusan</span>
        </div>

        <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-sm text-center">
            <span class="text-[10px] text-slate-400 uppercase font-semibold block">Rata-rata DPMO</span>
            <span class="text-xl font-extrabold text-purple-600 mt-1 block font-mono">{{ number_format($avgDpmo, 0, ',', '.') }}</span>
            <span class="text-[10px] text-slate-400">Cacat per 1 juta peluang</span>
        </div>

        <div class="col-span-2 sm:col-span-1 bg-white p-4 rounded-xl border border-slate-200 shadow-sm text-center">
            <span class="text-[10px] text-slate-400 uppercase font-semibold block">Tingkat Sigma Rata-rata</span>
            <div class="flex items-center justify-center space-x-1 mt-1">
                <span class="text-2xl font-black text-amber-500 font-mono">{{ $avgSigma }}</span>
                <span class="text-xs font-bold text-amber-400">&sigma;</span>
            </div>
            <span class="text-[10px] font-semibold text-slate-500">Benchmark: 4.0 &sigma;</span>
        </div>
    </div>

    <!-- Section 1: Diagram Pareto (Prinsip 80/20) -->
    <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-6 space-y-4">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2 border-b border-slate-100 pb-3">
            <div>
                <h2 class="text-base font-bold text-slate-900">1. Diagram Pareto Cacat (Prinsip 80/20 - Analisis Prioritas)</h2>
                <p class="text-xs text-slate-500 mt-0.5">Fokuskan tindakan perbaikan pada 20% jenis cacat utama (*Vital Few*) yang menghasilkan 80% dampak kualitas.</p>
            </div>
            <span class="px-2.5 py-1 bg-amber-50 text-amber-800 border border-amber-200 rounded-lg text-xs font-bold font-mono">
                {{ $pareto['vital_few_count'] }} Jenis Cacat Vital Few
            </span>
        </div>

        <div class="h-80">
            <canvas id="analyticsParetoChart"></canvas>
        </div>

        <!-- Pareto Breakdown Table -->
        <div class="overflow-x-auto pt-2">
            <table class="w-full text-left border-collapse text-xs">
                <thead>
                    <tr class="bg-slate-50 text-[11px] font-semibold uppercase text-slate-500 border-b border-slate-200">
                        <th class="py-2.5 px-3">Peringkat</th>
                        <th class="py-2.5 px-3">Kode & Nama Cacat</th>
                        <th class="py-2.5 px-3">Tingkat Keparahan</th>
                        <th class="py-2.5 px-3">Jumlah Cacat</th>
                        <th class="py-2.5 px-3">Kontribusi %</th>
                        <th class="py-2.5 px-3">Kumulatif %</th>
                        <th class="py-2.5 px-3">Klasifikasi</th>
                        <th class="py-2.5 px-3 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($pareto['items'] as $idx => $item)
                        <tr class="hover:bg-slate-50/80 transition-colors {{ $item['is_vital_few'] ? 'bg-amber-50/20' : '' }}">
                            <td class="py-2.5 px-3 font-mono font-bold text-slate-500">#{{ $idx + 1 }}</td>
                            <td class="py-2.5 px-3 font-medium text-slate-900">
                                <span class="font-mono text-slate-500 mr-1">[{{ $item['defect_code'] }}]</span>
                                {{ $item['defect_name'] }}
                            </td>
                            <td class="py-2.5 px-3">
                                <span class="inline-flex items-center px-2.5 py-1 rounded-md text-[11px] font-bold shadow-xs {{ $item['severity'] === 'critical' ? 'bg-rose-100 text-rose-950 border border-rose-300' : ($item['severity'] === 'major' ? 'bg-amber-100 text-amber-950 border border-amber-300' : 'bg-sky-100 text-sky-950 border border-sky-300') }}">
                                    {{ ucfirst($item['severity']) }}
                                </span>
                            </td>
                            <td class="py-2.5 px-3 font-bold font-mono text-slate-900">{{ $item['count'] }} unit</td>
                            <td class="py-2.5 px-3 font-mono text-slate-700">{{ $item['percentage'] }}%</td>
                            <td class="py-2.5 px-3 font-mono font-bold {{ $item['cumulative_percentage'] <= 80 ? 'text-amber-600' : 'text-slate-500' }}">
                                {{ $item['cumulative_percentage'] }}%
                            </td>
                            <td class="py-2.5 px-3">
                                @if($item['is_vital_few'])
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-md text-[11px] font-bold bg-amber-100 text-amber-950 border border-amber-400">
                                        Vital Few (Prioritas 80%)
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-md text-[11px] font-bold bg-slate-100 text-slate-900 border border-slate-300">
                                        Trivial Many
                                    </span>
                                @endif
                            </td>
                            <td class="py-2.5 px-3 text-right">
                                <a href="{{ route('admin.capa.create', ['defect_type_id' => $item['defect_type_id']]) }}" class="text-xs font-semibold text-emerald-600 hover:text-emerald-700">
                                    + CAPA Perbaikan
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="py-6 text-center text-slate-400">Tidak ada data cacat untuk parameter filter ini.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Section 2: Statistical Process Control (p-Chart SPC) -->
    <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-6 space-y-4">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2 border-b border-slate-100 pb-3">
            <div>
                <h2 class="text-base font-bold text-slate-900">2. Grafik Kendali Proses Statistik (SPC p-Chart)</h2>
                <p class="text-xs text-slate-500 mt-0.5">Memantau variasi proporsi produk cacat per lot secara kronologis terhadap Batas Kendali Atas (UCL), Garis Tengah (CL), dan Batas Bawah (LCL).</p>
            </div>
            <div class="flex items-center space-x-3 text-xs font-mono">
                <span class="px-2 py-0.5 bg-rose-50 text-rose-700 border border-rose-200 rounded font-bold">UCL: {{ number_format($spc['ucl'] * 100, 2) }}%</span>
                <span class="px-2 py-0.5 bg-emerald-50 text-emerald-700 border border-emerald-200 rounded font-bold">CL: {{ number_format($spc['cl'] * 100, 2) }}%</span>
                <span class="px-2 py-0.5 bg-slate-100 text-slate-700 border border-slate-200 rounded font-bold">LCL: {{ number_format($spc['lcl'] * 100, 2) }}%</span>
            </div>
        </div>

        <div class="h-80">
            <canvas id="analyticsSpcChart"></canvas>
        </div>
    </div>

    <!-- Section 3: Matriks Analisis Akar Masalah Ishikawa (Fishbone 5M+1E) -->
    <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-6 space-y-4">
        <div class="border-b border-slate-100 pb-3">
            <h2 class="text-base font-bold text-slate-900">3. Matriks Diagram Tulang Ikan (Ishikawa / Fishbone 5M+1E)</h2>
            <p class="text-xs text-slate-500 mt-0.5">Pengelompokan akar penyebab kegagalan mutu berdasarkan faktor Manusia, Mesin, Metode, Material, Pengukuran, dan Lingkungan.</p>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
            @foreach($ishikawa as $categoryKey => $cat)
                <div class="p-4 rounded-xl border border-slate-200 bg-slate-50/50 flex flex-col justify-between">
                    <div>
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-bold uppercase tracking-wider text-slate-800">{{ $cat['label'] }}</span>
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-bold {{ $cat['badge_color'] }}">
                                {{ $cat['percentage'] }}%
                            </span>
                        </div>
                        <div class="mt-2 text-2xl font-black text-slate-900 font-mono">
                            {{ number_format($cat['count']) }} <span class="text-xs font-normal text-slate-400">titik cacat</span>
                        </div>
                        <div class="mt-3 space-y-1.5">
                            <span class="text-[10px] font-semibold text-slate-400 uppercase block">Temuan Lapangan QC:</span>
                            @forelse($cat['sample_notes'] as $note)
                                <div class="text-xs text-slate-700 bg-white p-2 rounded border border-slate-100 italic">
                                    &ldquo;{{ $note }}&rdquo;
                                </div>
                            @empty
                                <span class="text-xs text-slate-400 italic">Belum ada temuan spesifik.</span>
                            @endforelse
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <!-- Section 4: Kesimpulan & Evaluasi Mutu Six Sigma (Sesuai Flowchart Sistem No 9 & 10) -->
    <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-6 space-y-6">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2 border-b border-slate-100 pb-3">
            <div>
                <div class="flex items-center space-x-2">
                    <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-slate-900 text-white uppercase font-mono">Tahap 9 & 10 Flowchart</span>
                    <span class="text-xs text-slate-400">&bull;</span>
                    <span class="text-xs text-slate-500 font-medium">Output Kesimpulan & Laporan</span>
                </div>
                <h2 class="text-base font-bold text-slate-900 mt-1">4. Kesimpulan Kualitas & Output Laporan Pengendalian Mutu</h2>
                <p class="text-xs text-slate-500 mt-0.5">Ringkasan kesimpulan matematis otomatis: Tingkat Sigma, Cacat Dominan dari Pareto Vital Few, dan Status Kendali Proses SPC.</p>
            </div>
            <button onclick="window.print()" class="inline-flex items-center px-4 py-2 bg-slate-900 hover:bg-slate-800 text-white text-xs font-semibold rounded-lg shadow-sm transition-colors">
                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                </svg>
                Cetak Output Laporan Lengkap
            </button>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <!-- Box 1: Tingkat Sigma -->
            <div class="p-4 rounded-xl border border-slate-200 bg-slate-50/50 flex flex-col justify-between">
                <div>
                    <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block">A. Nilai Sigma Terkalkulasi</span>
                    <div class="flex items-center space-x-2 mt-2">
                        <span class="text-3xl font-black text-slate-900 font-mono">{{ $conclusion['sigma_eval']['level'] }}</span>
                        <span class="text-lg font-bold text-amber-500">&sigma;</span>
                    </div>
                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-bold border mt-2 {{ $conclusion['sigma_eval']['badge_color'] }}">
                        {{ $conclusion['sigma_eval']['category'] }}
                    </span>
                    <p class="text-xs text-slate-600 mt-2 leading-relaxed">
                        {{ $conclusion['sigma_eval']['description'] }}
                    </p>
                </div>
            </div>

            <!-- Box 2: Cacat Dominan (Pareto Vital Few) -->
            <div class="p-4 rounded-xl border border-slate-200 bg-slate-50/50 flex flex-col justify-between">
                <div>
                    <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block">B. Cacat Dominan (Vital Few)</span>
                    @if($conclusion['dominant_defect'])
                        <div class="mt-2">
                            <span class="text-xl font-extrabold text-rose-600 font-mono block">
                                [{{ $conclusion['dominant_defect']['code'] }}] {{ $conclusion['dominant_defect']['name'] }}
                            </span>
                            <span class="text-xs text-slate-500 mt-1 block">
                                Menyumbang <strong class="text-slate-800 font-mono">{{ $conclusion['dominant_defect']['percentage'] }}%</strong> dari seluruh temuan cacat ({{ $conclusion['dominant_defect']['count'] }} unit).
                            </span>
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-medium bg-rose-100 text-rose-800 mt-2">
                                Keparahan: {{ ucfirst($conclusion['dominant_defect']['severity']) }}
                            </span>
                        </div>
                    @else
                        <span class="text-xs text-slate-400 italic block mt-3">Tidak ada data cacat terdeteksi.</span>
                    @endif
                </div>
            </div>

            <!-- Box 3: Status Kendali Proses SPC -->
            <div class="p-4 rounded-xl border border-slate-200 bg-slate-50/50 flex flex-col justify-between">
                <div>
                    <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block">C. Status Kendali Proses (p-Chart)</span>
                    <div class="mt-2">
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold border {{ $conclusion['control_status']['badge_color'] }}">
                            {{ $conclusion['control_status']['label'] }}
                        </span>
                        <p class="text-xs text-slate-600 mt-2 leading-relaxed">
                            {{ $conclusion['control_status']['note'] }}
                        </p>
                        @if(! $conclusion['control_status']['is_in_control'])
                            <div class="mt-3">
                                <a href="{{ route('admin.capa.create') }}" class="inline-flex items-center px-3 py-1.5 bg-rose-600 hover:bg-rose-700 text-white text-xs font-semibold rounded-lg shadow-sm">
                                    + Buat Tiket Tindakan Korektif (CAPA)
                                </a>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Rekomendasi Tindakan -->
        <div class="p-4 rounded-xl bg-slate-900 text-slate-200">
            <span class="text-xs font-bold uppercase tracking-wider text-emerald-400 block mb-2">Rekomendasi Tindak Lanjut Manajerial:</span>
            <ul class="space-y-1.5 text-xs text-slate-300">
                @foreach($conclusion['recommendations'] as $rec)
                    <li class="flex items-start space-x-2">
                        <span class="text-emerald-400 font-bold">&check;</span>
                        <span>{{ $rec }}</span>
                    </li>
                @endforeach
            </ul>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    // 1. Render Analytics Pareto Chart
    const paretoItems = @json($pareto['items'] ?? []);
    if (paretoItems.length > 0) {
        const labels = paretoItems.map(item => item.defect_name.length > 20 ? item.defect_name.substring(0, 20) + '...' : item.defect_name);
        const counts = paretoItems.map(item => item.count);
        const cumulatives = paretoItems.map(item => item.cumulative_percentage);

        const ctxPareto = document.getElementById('analyticsParetoChart').getContext('2d');
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
                        borderWidth: 2.5,
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
                        beginAtZero: true,
                        title: { display: true, text: 'Jumlah Unit Cacat' }
                    },
                    y1: {
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

    // 2. Render Analytics SPC p-Chart
    const spcPoints = @json($spc['points'] ?? []);
    if (spcPoints.length > 0) {
        const spcLabels = spcPoints.map(p => p.label + ' (' + p.batch_number + ')');
        const pPercentages = spcPoints.map(p => p.p * 100);
        const uclLine = Array(spcPoints.length).fill({{ $spc['ucl'] * 100 }});
        const clLine = Array(spcPoints.length).fill({{ $spc['cl'] * 100 }});
        const lclLine = Array(spcPoints.length).fill({{ $spc['lcl'] * 100 }});

        const ctxSpc = document.getElementById('analyticsSpcChart').getContext('2d');
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
                        pointRadius: 5,
                        pointHoverRadius: 7,
                    },
                    {
                        label: 'UCL (Batas Kendali Atas)',
                        data: uclLine,
                        borderColor: '#ef4444',
                        borderDash: [6, 4],
                        borderWidth: 2,
                        pointRadius: 0,
                    },
                    {
                        label: 'CL (Garis Tengah / Rata-rata)',
                        data: clLine,
                        borderColor: '#10b981',
                        borderWidth: 2,
                        pointRadius: 0,
                    },
                    {
                        label: 'LCL (Batas Kendali Bawah)',
                        data: lclLine,
                        borderColor: '#64748b',
                        borderDash: [6, 4],
                        borderWidth: 2,
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
