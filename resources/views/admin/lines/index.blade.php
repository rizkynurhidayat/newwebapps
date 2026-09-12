@extends('layouts.admin')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold tracking-tight text-slate-900">Lini Produksi & Mesin</h1>
            <p class="text-sm text-slate-500 mt-1">Stasiun kerja manufaktur, area perakitan, dan sel permesinan CNC.</p>
        </div>
        <a href="{{ route('admin.lines.create') }}" class="inline-flex items-center px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-semibold rounded-lg shadow-sm transition-colors">
            <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            + Tambah Lini Baru
        </a>
    </div>

    <!-- Table of Lines -->
    <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50 text-[11px] font-semibold uppercase text-slate-500 border-b border-slate-200">
                        <th class="py-3 px-4">Kode Lini</th>
                        <th class="py-3 px-4">Nama Lini / Mesin</th>
                        <th class="py-3 px-4">Lokasi Pabrik</th>
                        <th class="py-3 px-4">Total Batch</th>
                        <th class="py-3 px-4">Status</th>
                        <th class="py-3 px-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-xs">
                    @forelse($lines as $line)
                        <tr class="hover:bg-slate-50/80 transition-colors">
                            <td class="py-3 px-4 font-mono font-bold text-slate-900">{{ $line->line_code }}</td>
                            <td class="py-3 px-4">
                                <div class="font-medium text-slate-900">{{ $line->name }}</div>
                                <div class="text-[11px] text-slate-400 truncate max-w-xs">{{ $line->description }}</div>
                            </td>
                            <td class="py-3 px-4 text-slate-600">{{ $line->location ?? '-' }}</td>
                            <td class="py-3 px-4 font-mono font-medium">{{ $line->production_batches_count }} lot</td>
                            <td class="py-3 px-4">
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-medium {{ $line->status === 'operational' ? 'bg-emerald-100 text-emerald-800' : ($line->status === 'maintenance' ? 'bg-amber-100 text-amber-800' : 'bg-slate-100 text-slate-600') }}">
                                    {{ ucfirst($line->status) }}
                                </span>
                            </td>
                            <td class="py-3 px-4 text-right space-x-2">
                                <a href="{{ route('admin.lines.edit', $line) }}" class="text-xs font-medium text-slate-600 hover:text-emerald-600">Edit</a>
                                <form action="{{ route('admin.lines.destroy', $line) }}" method="POST" class="inline" onsubmit="return confirm('Hapus lini produksi ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-xs font-medium text-rose-600 hover:text-rose-800">Hapus</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-8 text-center text-slate-400">Belum ada lini produksi terdaftar.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($lines->hasPages())
            <div class="p-4 border-t border-slate-100">
                {{ $lines->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
