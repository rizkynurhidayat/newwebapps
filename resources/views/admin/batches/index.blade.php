@extends('layouts.admin')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold tracking-tight text-slate-900">Jadwal & Lot Batch Produksi</h1>
            <p class="text-sm text-slate-500 mt-1">Daftar pesanan lot produksi, lini kerja, shift, dan pencatatan output aktual.</p>
        </div>
        <a href="{{ route('admin.batches.create') }}" class="inline-flex items-center px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-semibold rounded-lg shadow-sm transition-colors">
            <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            + Buat Batch Baru
        </a>
    </div>

    <!-- Filter Bar -->
    <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-sm">
        <form method="GET" action="{{ route('admin.batches.index') }}" class="grid grid-cols-1 sm:grid-cols-4 gap-3">
            <div>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nomor lot atau produk..." 
                       class="w-full text-xs rounded-lg border-slate-200 focus:border-emerald-500 focus:ring-emerald-500">
            </div>
            <div>
                <select name="line_id" class="w-full text-xs rounded-lg border-slate-200 focus:border-emerald-500 focus:ring-emerald-500">
                    <option value="">-- Semua Lini Produksi --</option>
                    @foreach($lines as $l)
                        <option value="{{ $l->id }}" {{ request('line_id') == $l->id ? 'selected' : '' }}>{{ $l->line_code }} - {{ $l->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <select name="status" class="w-full text-xs rounded-lg border-slate-200 focus:border-emerald-500 focus:ring-emerald-500">
                    <option value="">-- Semua Status --</option>
                    <option value="draft" {{ request('status') === 'draft' ? 'selected' : '' }}>Draft</option>
                    <option value="in_production" {{ request('status') === 'in_production' ? 'selected' : '' }}>Sedang Diproduksi</option>
                    <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>Selesai</option>
                </select>
            </div>
            <div class="flex items-center space-x-2">
                <button type="submit" class="w-full py-2 bg-slate-800 hover:bg-slate-900 text-white text-xs font-medium rounded-lg transition-colors">
                    Filter Batch
                </button>
                @if(request()->anyFilled(['search', 'line_id', 'status']))
                    <a href="{{ route('admin.batches.index') }}" class="px-3 py-2 text-xs text-slate-500 hover:text-slate-700">Reset</a>
                @endif
            </div>
        </form>
    </div>

    <!-- Batches Table -->
    <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50 text-[11px] font-semibold uppercase text-slate-500 border-b border-slate-200">
                        <th class="py-3 px-4">Nomor Lot / Batch</th>
                        <th class="py-3 px-4">Produk</th>
                        <th class="py-3 px-4">Lini Produksi</th>
                        <th class="py-3 px-4">Tanggal & Shift</th>
                        <th class="py-3 px-4">Target vs Aktual</th>
                        <th class="py-3 px-4">Pemeriksaan QC</th>
                        <th class="py-3 px-4">Status</th>
                        <th class="py-3 px-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-xs">
                    @forelse($batches as $b)
                        <tr class="hover:bg-slate-50/80 transition-colors">
                            <td class="py-3 px-4 font-mono font-bold text-slate-900">
                                <a href="{{ route('admin.batches.show', $b) }}" class="text-emerald-600 hover:underline">
                                    {{ $b->batch_number }}
                                </a>
                            </td>
                            <td class="py-3 px-4">
                                <div class="font-medium text-slate-900">{{ $b->product?->name ?? '-' }}</div>
                                <div class="text-[11px] text-slate-400 font-mono">{{ $b->product?->part_number ?? '-' }}</div>
                            </td>
                            <td class="py-3 px-4 text-slate-600 font-medium">{{ $b->productionLine?->line_code ?? '-' }}</td>
                            <td class="py-3 px-4">
                                <div class="text-slate-900">{{ $b->production_date?->format('d/m/Y') }}</div>
                                <div class="text-[11px] text-slate-400">{{ $b->shift?->value ?? '-' }}</div>
                            </td>
                            <td class="py-3 px-4 font-mono">
                                <span class="font-bold text-slate-900">{{ number_format($b->actual_qty) }}</span>
                                <span class="text-slate-400">/ {{ number_format($b->target_qty) }}</span>
                            </td>
                            <td class="py-3 px-4">
                                @if($b->quality_inspections_count > 0)
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                        {{ $b->quality_inspections_count }} Kali QC
                                    </span>
                                @else
                                    <a href="{{ route('admin.inspections.create', ['batch_id' => $b->id]) }}" class="text-[11px] font-semibold text-amber-600 hover:underline">
                                        + Periksa Sekarang
                                    </a>
                                @endif
                            </td>
                            <td class="py-3 px-4">
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-medium {{ $b->status->badgeColor() }}">
                                    {{ $b->status->label() }}
                                </span>
                            </td>
                            <td class="py-3 px-4 text-right space-x-2">
                                <a href="{{ route('admin.batches.show', $b) }}" class="text-xs font-medium text-slate-600 hover:text-emerald-600">Detail</a>
                                <a href="{{ route('admin.batches.edit', $b) }}" class="text-xs font-medium text-slate-600 hover:text-emerald-600">Edit</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="py-8 text-center text-slate-400">Belum ada batch produksi terdaftar.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($batches->hasPages())
            <div class="p-4 border-t border-slate-100">
                {{ $batches->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
