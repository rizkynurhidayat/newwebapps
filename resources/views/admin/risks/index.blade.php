@extends('layouts.admin')

@section('content')
<div class="space-y-6">
    <!-- Page Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <div class="flex items-center space-x-2">
                <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-800">
                    Modul K3 Manufaktur
                </span>
                <span class="text-xs text-slate-400">&bull;</span>
                <span class="text-xs text-slate-500 font-medium">Lini Mesin Stamping Press</span>
            </div>
            <h1 class="text-2xl font-bold tracking-tight text-slate-900 mt-1">Analisa Resiko Kerja Mesin Stamping Press</h1>
            <p class="text-sm text-slate-500 mt-0.5">Pemantauan bahaya mekanis, penilaian kemungkinan (likelihood) & keparahan (severity), serta pengendalian risiko otomatis.</p>
        </div>
        <div class="flex items-center space-x-2">
            <button onclick="window.print()" class="inline-flex items-center px-3.5 py-2 bg-white hover:bg-slate-50 text-slate-700 border border-slate-200 text-xs font-semibold rounded-lg shadow-sm transition-colors">
                <svg class="w-4 h-4 mr-1.5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                </svg>
                Cetak Laporan K3
            </button>
            @if(auth()->user()->isAdmin() || auth()->user()->isEmployee())
                <a href="{{ route('admin.risks.create') }}" class="inline-flex items-center px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-semibold rounded-lg shadow-sm shadow-emerald-500/20 transition-colors">
                    <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    + Tambah Analisa Bahaya
                </a>
            @endif
        </div>
    </div>

    <!-- Poin 4 fitur.jpeg: Dashboard K3 Menampilkan Jumlah Bahaya, Jumlah Resiko, Tingkat Resiko -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- 1. Jumlah Bahaya -->
        <div class="bg-white p-5 rounded-xl border border-slate-200 shadow-sm relative overflow-hidden">
            <div class="flex items-center justify-between">
                <div>
                    <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Jumlah Bahaya</span>
                    <div class="text-3xl font-extrabold text-slate-900 mt-1 font-mono">{{ $summary['total_hazards'] }}</div>
                    <span class="text-xs text-slate-500 mt-1 block">Titik bahaya teridentifikasi</span>
                </div>
                <div class="w-12 h-12 rounded-xl bg-sky-50 text-sky-600 flex items-center justify-center">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                </div>
            </div>
            <div class="mt-3 pt-3 border-t border-slate-100 text-[11px] text-slate-500">
                Area: Lini Stamping Press 150T - 300T
            </div>
        </div>

        <!-- 2. Jumlah Resiko -->
        <div class="bg-white p-5 rounded-xl border border-slate-200 shadow-sm relative overflow-hidden">
            <div class="flex items-center justify-between">
                <div>
                    <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Jumlah Resiko</span>
                    <div class="text-3xl font-extrabold text-slate-900 mt-1 font-mono">{{ $summary['total_risks'] }}</div>
                    <span class="text-xs text-slate-500 mt-1 block">Potensi insiden kerja</span>
                </div>
                <div class="w-12 h-12 rounded-xl bg-purple-50 text-purple-600 flex items-center justify-center">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                    </svg>
                </div>
            </div>
            <div class="mt-3 pt-3 border-t border-slate-100 text-[11px] text-slate-500">
                Rata-rata Skor: <strong class="font-mono text-slate-700">{{ $summary['avg_score'] }} / 25</strong>
            </div>
        </div>

        <!-- 3. Tingkat Resiko: Distribusi Kategori -->
        <div class="col-span-1 sm:col-span-2 bg-white p-5 rounded-xl border border-slate-200 shadow-sm">
            <div class="flex items-center justify-between mb-2">
                <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Tingkat Resiko (Kategori)</span>
                <span class="text-[11px] text-slate-400">Formula: $Risk = L \times S$</span>
            </div>
            <div class="grid grid-cols-4 gap-2 pt-1 text-center">
                <div class="p-2.5 rounded-lg bg-emerald-50 border border-emerald-200">
                    <span class="text-[10px] uppercase font-bold text-emerald-900 block">Rendah (1-4)</span>
                    <span class="text-xl font-black text-emerald-950 font-mono mt-0.5 block">{{ $summary['distribution']['low'] }}</span>
                    <span class="text-[10px] text-emerald-800 font-medium">Low</span>
                </div>
                <div class="p-2.5 rounded-lg bg-amber-50 border border-amber-200">
                    <span class="text-[10px] uppercase font-bold text-amber-900 block">Sedang (5-9)</span>
                    <span class="text-xl font-black text-amber-950 font-mono mt-0.5 block">{{ $summary['distribution']['medium'] }}</span>
                    <span class="text-[10px] text-amber-800 font-medium">Medium</span>
                </div>
                <div class="p-2.5 rounded-lg bg-orange-50 border border-orange-200">
                    <span class="text-[10px] uppercase font-bold text-orange-900 block">Tinggi (10-15)</span>
                    <span class="text-xl font-black text-orange-950 font-mono mt-0.5 block">{{ $summary['distribution']['high'] }}</span>
                    <span class="text-[10px] text-orange-800 font-medium">High</span>
                </div>
                <div class="p-2.5 rounded-lg bg-rose-50 border border-rose-200">
                    <span class="text-[10px] uppercase font-bold text-rose-900 block">Ekstrem (16-25)</span>
                    <span class="text-xl font-black text-rose-950 font-mono mt-0.5 block">{{ $summary['distribution']['extreme'] }}</span>
                    <span class="text-[10px] text-rose-800 font-medium">Extreme</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Matriks Risiko 5x5 (Likelihood vs Severity) -->
    <div class="bg-white p-6 rounded-xl border border-slate-200 shadow-sm space-y-4">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2 border-b border-slate-100 pb-3">
            <div>
                <h2 class="text-base font-bold text-slate-900">Matriks Pemetaan Risiko 5 &times; 5 Mesin Stamping Press</h2>
                <p class="text-xs text-slate-500 mt-0.5">Pemetaan visual koordinat Kemungkinan Terjadi (*Likelihood*) vs Tingkat Keparahan (*Severity*).</p>
            </div>
            <div class="flex items-center space-x-2 text-[11px]">
                <span class="inline-flex items-center px-2 py-0.5 rounded bg-emerald-100 text-emerald-800 font-semibold">1-4: Rendah</span>
                <span class="inline-flex items-center px-2 py-0.5 rounded bg-amber-100 text-amber-800 font-semibold">5-9: Sedang</span>
                <span class="inline-flex items-center px-2 py-0.5 rounded bg-orange-100 text-orange-800 font-semibold">10-15: Tinggi</span>
                <span class="inline-flex items-center px-2 py-0.5 rounded bg-rose-100 text-rose-800 font-semibold">16-25: Ekstrem</span>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-center border-collapse text-xs">
                <thead>
                    <tr>
                        <th rowspan="2" class="p-2 bg-slate-100 text-slate-700 font-bold border border-slate-200 w-28">Likelihood (L)</th>
                        <th colspan="5" class="p-2 bg-slate-100 text-slate-700 font-bold border border-slate-200">Severity / Keparahan Dampak (S)</th>
                    </tr>
                    <tr class="bg-slate-50 text-[11px] text-slate-600 font-semibold">
                        <th class="p-2 border border-slate-200 w-1/5">1 - Sangat Ringan</th>
                        <th class="p-2 border border-slate-200 w-1/5">2 - Ringan</th>
                        <th class="p-2 border border-slate-200 w-1/5">3 - Sedang</th>
                        <th class="p-2 border border-slate-200 w-1/5">4 - Berat</th>
                        <th class="p-2 border border-slate-200 w-1/5">5 - Bencana / Fatal</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $likelihoodLabels = [
                            5 => '5 - Sangat Sering',
                            4 => '4 - Sering',
                            3 => '3 - Sedang',
                            2 => '2 - Jarang',
                            1 => '1 - Sangat Jarang',
                        ];
                    @endphp
                    @foreach([5, 4, 3, 2, 1] as $l)
                        <tr>
                            <td class="p-2 font-bold bg-slate-50 text-slate-700 border border-slate-200 text-left">
                                {{ $likelihoodLabels[$l] }}
                            </td>
                            @for($s = 1; $s <= 5; $s++)
                                @php
                                    $cell = $matrix[$l][$s];
                                    $cellScore = $cell['score'];
                                    $cellCount = $cell['count'];
                                    $bgClass = match(true) {
                                        $cellScore <= 4 => 'bg-emerald-50 text-emerald-900 border-emerald-200 hover:bg-emerald-100',
                                        $cellScore <= 9 => 'bg-amber-50 text-amber-900 border-amber-200 hover:bg-amber-100',
                                        $cellScore <= 15 => 'bg-orange-50 text-orange-900 border-orange-200 hover:bg-orange-100',
                                        default => 'bg-rose-50 text-rose-900 border-rose-200 hover:bg-rose-100',
                                    };
                                @endphp
                                <td class="p-3 border transition-colors {{ $bgClass }} align-top">
                                    <div class="flex items-center justify-between">
                                        <span class="text-xs font-mono font-bold">{{ $cellScore }}</span>
                                        @if($cellCount > 0)
                                            <span class="inline-flex items-center justify-center w-5 h-5 rounded-full text-[11px] font-bold bg-slate-900 text-white shadow-sm">
                                                {{ $cellCount }}
                                            </span>
                                        @endif
                                    </div>
                                    @if($cellCount > 0)
                                        <div class="mt-1 space-y-1 text-left">
                                            @foreach($cell['items'] as $item)
                                                <div class="text-[10px] leading-tight font-medium truncate" title="{{ $item->hazard_name }}">
                                                    &bull; [{{ $item->hazard_code }}] {{ $item->hazard_name }}
                                                </div>
                                            @endforeach
                                        </div>
                                    @endif
                                </td>
                            @endfor
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <!-- Filter & Data Table -->
    <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="p-4 border-b border-slate-200 bg-slate-50/50 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <h3 class="text-sm font-bold text-slate-900">Daftar Hasil Analisa Bahaya & Resiko Kerja Mesin Press</h3>
            
            <form method="GET" action="{{ route('admin.risks.index') }}" class="flex items-center space-x-2">
                <select name="level" class="text-xs rounded-lg border-slate-200 focus:border-emerald-500 focus:ring-emerald-500" onchange="this.form.submit()">
                    <option value="">-- Semua Kategori Resiko --</option>
                    <option value="low" {{ request('level') === 'low' ? 'selected' : '' }}>Rendah (Low)</option>
                    <option value="medium" {{ request('level') === 'medium' ? 'selected' : '' }}>Sedang (Medium)</option>
                    <option value="high" {{ request('level') === 'high' ? 'selected' : '' }}>Tinggi (High)</option>
                    <option value="extreme" {{ request('level') === 'extreme' ? 'selected' : '' }}>Ekstrem (Extreme)</option>
                </select>

                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama bahaya..." class="text-xs rounded-lg border-slate-200 focus:border-emerald-500 focus:ring-emerald-500 w-44">
                
                <button type="submit" class="px-3 py-1.5 bg-slate-900 hover:bg-slate-800 text-white text-xs font-semibold rounded-lg">Cari</button>
                @if(request()->anyFilled(['level', 'search']))
                    <a href="{{ route('admin.risks.index') }}" class="px-2 py-1.5 text-xs text-slate-500 hover:text-slate-700">Reset</a>
                @endif
            </form>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-xs">
                <thead>
                    <tr class="bg-slate-50 text-[11px] font-semibold uppercase text-slate-500 border-b border-slate-200">
                        <th class="py-3 px-4">Kode</th>
                        <th class="py-3 px-4">Nama Bahaya & Area Mesin</th>
                        <th class="py-3 px-4">Deskripsi Dampak Risiko</th>
                        <th class="py-3 px-3 text-center">Likelihood (L)</th>
                        <th class="py-3 px-3 text-center">Severity (S)</th>
                        <th class="py-3 px-3 text-center">Risk Score (L &times; S)</th>
                        <th class="py-3 px-4 text-center">Tingkat Resiko</th>
                        <th class="py-3 px-4">Tindakan Pengendalian (Mitigasi)</th>
                        @if(auth()->user()->isAdmin() || auth()->user()->isEmployee())
                            <th class="py-3 px-4 text-right">Aksi</th>
                        @endif
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($risks as $risk)
                        <tr class="hover:bg-slate-50/80 transition-colors">
                            <td class="py-3 px-4 font-mono font-bold text-slate-600">{{ $risk->hazard_code }}</td>
                            <td class="py-3 px-4">
                                <span class="font-bold text-slate-900 block">{{ $risk->hazard_name }}</span>
                                <span class="text-[11px] text-slate-500 mt-0.5 block flex items-center">
                                    <svg class="w-3 h-3 mr-1 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                                    </svg>
                                    {{ $risk->machine_area }}
                                </span>
                            </td>
                            <td class="py-3 px-4 text-slate-700 max-w-xs">{{ $risk->risk_description }}</td>
                            <td class="py-3 px-3 text-center font-mono font-bold text-slate-800">{{ $risk->likelihood }}</td>
                            <td class="py-3 px-3 text-center font-mono font-bold text-slate-800">{{ $risk->severity }}</td>
                            <td class="py-3 px-3 text-center">
                                <span class="text-sm font-black font-mono {{ $risk->risk_score >= 10 ? 'text-rose-600' : ($risk->risk_score >= 5 ? 'text-amber-600' : 'text-emerald-600') }}">
                                    {{ $risk->risk_score }}
                                </span>
                            </td>
                            <td class="py-3 px-4 text-center">
                                <span class="inline-flex items-center px-2.5 py-1 rounded-md text-[11px] font-bold shadow-xs border {{ $risk->risk_level->badgeColor() }}">
                                    {{ $risk->risk_level->label() }}
                                </span>
                            </td>
                            <td class="py-3 px-4 text-slate-600 max-w-sm">
                                <p class="line-clamp-2 text-[11px]">{{ $risk->control_measures ?? 'Belum ada mitigasi tertulis.' }}</p>
                                @if($risk->pic)
                                    <span class="text-[10px] text-slate-400 block mt-1">PIC: {{ $risk->pic }}</span>
                                @endif
                            </td>
                            @if(auth()->user()->isAdmin() || auth()->user()->isEmployee())
                                <td class="py-3 px-4 text-right space-x-2 whitespace-nowrap">
                                    <a href="{{ route('admin.risks.edit', $risk) }}" class="text-xs font-semibold text-emerald-600 hover:text-emerald-700">
                                        Edit
                                    </a>
                                    @if(auth()->user()->isAdmin())
                                        <form method="POST" action="{{ route('admin.risks.destroy', $risk) }}" class="inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus data bahaya ini?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-xs font-semibold text-rose-600 hover:text-rose-700">
                                                Hapus
                                            </button>
                                        </form>
                                    @endif
                                </td>
                            @endif
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ (auth()->user()->isAdmin() || auth()->user()->isEmployee()) ? 9 : 8 }}" class="py-8 text-center text-slate-400">Belum ada data analisa resiko bahaya mesin stamping press.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($risks->hasPages())
            <div class="p-4 border-t border-slate-200">
                {{ $risks->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
