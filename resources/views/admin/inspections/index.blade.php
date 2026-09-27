@extends('layouts.admin')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold tracking-tight text-slate-900">Pemeriksaan Kualitas (QC Inspections)</h1>
            <p class="text-sm text-slate-500 mt-1">Rekap sampling mutu, perhitungan otomatis DPU, DPMO, Process Yield %, dan Tingkat Sigma.</p>
        </div>
        @if(auth()->user()->isAdmin() || auth()->user()->isStaff())
            <a href="{{ route('admin.inspections.create') }}" class="inline-flex items-center px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-semibold rounded-lg shadow-sm transition-colors">
                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                + Input Pemeriksaan Baru
            </a>
        @endif
    </div>

    <!-- Filter Bar -->
    <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-sm">
        <form method="GET" action="{{ route('admin.inspections.index') }}" class="grid grid-cols-1 sm:grid-cols-4 gap-3">
            <div>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari no. inspeksi, lot, atau part..." 
                       class="w-full text-xs rounded-lg border-slate-200 focus:border-emerald-500 focus:ring-emerald-500">
            </div>
            <div>
                <select name="stage" class="w-full text-xs rounded-lg border-slate-200 focus:border-emerald-500 focus:ring-emerald-500">
                    <option value="">-- Semua Tahap Inspeksi --</option>
                    <option value="incoming" {{ request('stage') === 'incoming' ? 'selected' : '' }}>Incoming Inspection</option>
                    <option value="in_process" {{ request('stage') === 'in_process' ? 'selected' : '' }}>In-Process QC (IPQC)</option>
                    <option value="final_qa" {{ request('stage') === 'final_qa' ? 'selected' : '' }}>Final QA</option>
                </select>
            </div>
            <div>
                <select name="result" class="w-full text-xs rounded-lg border-slate-200 focus:border-emerald-500 focus:ring-emerald-500">
                    <option value="">-- Semua Hasil Kelulusan --</option>
                    <option value="passed" {{ request('result') === 'passed' ? 'selected' : '' }}>Lulus (Passed)</option>
                    <option value="conditional" {{ request('result') === 'conditional' ? 'selected' : '' }}>Lulus Bersyarat (Rework)</option>
                    <option value="rejected" {{ request('result') === 'rejected' ? 'selected' : '' }}>Ditolak / Afkir</option>
                </select>
            </div>
            <div class="flex items-center space-x-2">
                <button type="submit" class="w-full py-2 bg-slate-800 hover:bg-slate-900 text-white text-xs font-medium rounded-lg transition-colors">
                    Filter Data
                </button>
                @if(request()->anyFilled(['search', 'stage', 'result']))
                    <a href="{{ route('admin.inspections.index') }}" class="px-3 py-2 text-xs text-slate-500 hover:text-slate-700">Reset</a>
                @endif
            </div>
        </form>
    </div>

    <!-- Inspections Table -->
    <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50 text-[11px] font-semibold uppercase text-slate-500 border-b border-slate-200">
                        <th class="py-3 px-4">No. Inspeksi</th>
                        <th class="py-3 px-4">Lot / Batch</th>
                        <th class="py-3 px-4">Produk Manufaktur</th>
                        <th class="py-3 px-4">Tahap QC</th>
                        <th class="py-3 px-4">Sampel (N)</th>
                        <th class="py-3 px-4">Cacat Fisik (D)</th>
                        <th class="py-3 px-4">DPMO</th>
                        <th class="py-3 px-4">Tingkat Sigma</th>
                        <th class="py-3 px-4">Yield %</th>
                        <th class="py-3 px-4">Hasil</th>
                        <th class="py-3 px-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-xs">
                    @forelse($inspections as $ins)
                        <tr class="hover:bg-slate-50/80 transition-colors">
                            <td class="py-3 px-4 font-mono font-bold text-slate-900">
                                <a href="{{ route('admin.inspections.show', $ins) }}" class="text-emerald-600 hover:underline">
                                    {{ $ins->inspection_number }}
                                </a>
                            </td>
                            <td class="py-3 px-4 font-mono font-medium text-slate-700">
                                <a href="{{ route('admin.batches.show', $ins->productionBatch) }}" class="hover:underline">
                                    {{ $ins->productionBatch?->batch_number ?? '-' }}
                                </a>
                            </td>
                            <td class="py-3 px-4">
                                <div class="font-medium text-slate-900 truncate max-w-[160px]">{{ $ins->productionBatch?->product?->name ?? '-' }}</div>
                                <div class="text-[11px] text-slate-400 font-mono">{{ $ins->productionBatch?->product?->part_number ?? '-' }}</div>
                            </td>
                            <td class="py-3 px-4 text-slate-600">{{ $ins->inspection_stage?->label() }}</td>
                            <td class="py-3 px-4 font-mono font-medium">{{ $ins->sample_size_inspected }}</td>
                            <td class="py-3 px-4 font-mono font-semibold {{ $ins->defective_units_qty > 0 ? 'text-rose-600' : 'text-emerald-600' }}">
                                {{ $ins->defective_units_qty }} unit
                            </td>
                            <td class="py-3 px-4 font-mono font-medium">{{ number_format($ins->dpmo, 0, ',', '.') }}</td>
                            <td class="py-3 px-4">
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-bold font-mono {{ $ins->sigma_level >= 4.0 ? 'bg-emerald-100 text-emerald-800' : ($ins->sigma_level >= 3.0 ? 'bg-amber-100 text-amber-800' : 'bg-rose-100 text-rose-800') }}">
                                    {{ $ins->sigma_level }} &sigma;
                                </span>
                            </td>
                            <td class="py-3 px-4 font-bold font-mono text-slate-900">{{ $ins->yield_percentage }}%</td>
                            <td class="py-3 px-4">
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-medium border {{ $ins->result_status->badgeColor() }}">
                                    {{ $ins->result_status->label() }}
                                </span>
                            </td>
                            <td class="py-3 px-4 text-right">
                                <a href="{{ route('admin.inspections.show', $ins) }}" class="text-xs font-semibold text-emerald-600 hover:text-emerald-700">Detail &rarr;</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="11" class="py-8 text-center text-slate-400">Belum ada pemeriksaan kualitas yang tercatat.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($inspections->hasPages())
            <div class="p-4 border-t border-slate-100">
                {{ $inspections->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
