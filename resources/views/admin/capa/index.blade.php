@extends('layouts.admin')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold tracking-tight text-slate-900">Tindakan Korektif & Preventif (CAPA)</h1>
            <p class="text-sm text-slate-500 mt-1">Siklus *Improve & Control* Six Sigma: mendokumentasikan analisis akar masalah 5-Why dan tindakan eliminasi cacat.</p>
        </div>
        <a href="{{ route('admin.capa.create') }}" class="inline-flex items-center px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-semibold rounded-lg shadow-sm transition-colors">
            <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            + Buat Tiket CAPA Baru
        </a>
    </div>

    <!-- Filter Bar -->
    <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-sm">
        <form method="GET" action="{{ route('admin.capa.index') }}" class="grid grid-cols-1 sm:grid-cols-3 gap-3">
            <div>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nomor CAPA, judul, atau jenis cacat..." 
                       class="w-full text-xs rounded-lg border-slate-200 focus:border-emerald-500 focus:ring-emerald-500">
            </div>
            <div>
                <select name="status" class="w-full text-xs rounded-lg border-slate-200 focus:border-emerald-500 focus:ring-emerald-500">
                    <option value="">-- Semua Status CAPA --</option>
                    @foreach($statuses as $st)
                        <option value="{{ $st->value }}" {{ request('status') === $st->value ? 'selected' : '' }}>{{ $st->label() }}</option>
                    @endforeach
                </select>
            </div>
            <div class="flex items-center space-x-2">
                <button type="submit" class="w-full py-2 bg-slate-800 hover:bg-slate-900 text-white text-xs font-medium rounded-lg transition-colors">
                    Filter Tiket
                </button>
                @if(request()->anyFilled(['search', 'status']))
                    <a href="{{ route('admin.capa.index') }}" class="px-3 py-2 text-xs text-slate-500 hover:text-slate-700">Reset</a>
                @endif
            </div>
        </form>
    </div>

    <!-- CAPA Table -->
    <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50 text-[11px] font-semibold uppercase text-slate-500 border-b border-slate-200">
                        <th class="py-3 px-4">No. CAPA</th>
                        <th class="py-3 px-4">Judul Perbaikan Mutu</th>
                        <th class="py-3 px-4">Target Jenis Cacat</th>
                        <th class="py-3 px-4">Penanggung Jawab (PIC)</th>
                        <th class="py-3 px-4">Tenggat Waktu</th>
                        <th class="py-3 px-4">Status</th>
                        <th class="py-3 px-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-xs">
                    @forelse($capaActions as $capa)
                        <tr class="hover:bg-slate-50/80 transition-colors">
                            <td class="py-3 px-4 font-mono font-bold text-slate-900">
                                <a href="{{ route('admin.capa.show', $capa) }}" class="text-emerald-600 hover:underline">
                                    {{ $capa->capa_number }}
                                </a>
                            </td>
                            <td class="py-3 px-4">
                                <div class="font-semibold text-slate-900">{{ $capa->title }}</div>
                                <div class="text-[11px] text-slate-400 truncate max-w-sm">{{ $capa->problem_statement }}</div>
                            </td>
                            <td class="py-3 px-4">
                                <span class="font-mono text-slate-700 font-medium">[{{ $capa->defectType?->code }}]</span>
                                <span class="text-slate-900">{{ $capa->defectType?->name }}</span>
                            </td>
                            <td class="py-3 px-4 font-medium text-slate-800">{{ $capa->assignedTo?->name ?? '-' }}</td>
                            <td class="py-3 px-4 font-mono text-slate-600">{{ $capa->target_completion_date?->format('d/m/Y') }}</td>
                            <td class="py-3 px-4">
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-medium {{ $capa->status->badgeColor() }}">
                                    {{ $capa->status->label() }}
                                </span>
                            </td>
                            <td class="py-3 px-4 text-right space-x-2">
                                <a href="{{ route('admin.capa.show', $capa) }}" class="text-xs font-semibold text-emerald-600 hover:text-emerald-700">Detail</a>
                                <a href="{{ route('admin.capa.edit', $capa) }}" class="text-xs font-medium text-slate-600 hover:text-emerald-600">Edit</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-8 text-center text-slate-400">Belum ada tiket tindakan perbaikan (CAPA).</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($capaActions->hasPages())
            <div class="p-4 border-t border-slate-100">
                {{ $capaActions->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
