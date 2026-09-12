@extends('layouts.admin')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold tracking-tight text-slate-900">Taksonomi Cacat Produk (Defects)</h1>
            <p class="text-sm text-slate-500 mt-1">Daftar jenis cacat fisik, tingkat keparahan (*Severity*), dan kategori akar masalah (*Ishikawa 5M+1E*).</p>
        </div>
        <a href="{{ route('admin.defects.create') }}" class="inline-flex items-center px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-semibold rounded-lg shadow-sm transition-colors">
            <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            + Tambah Jenis Cacat
        </a>
    </div>

    <!-- Filter & Search Bar -->
    <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-sm">
        <form method="GET" action="{{ route('admin.defects.index') }}" class="grid grid-cols-1 sm:grid-cols-4 gap-3">
            <div>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari kode atau nama cacat..." 
                       class="w-full text-xs rounded-lg border-slate-200 focus:border-emerald-500 focus:ring-emerald-500">
            </div>
            <div>
                <select name="category_id" class="w-full text-xs rounded-lg border-slate-200 focus:border-emerald-500 focus:ring-emerald-500">
                    <option value="">-- Semua Kategori Cacat --</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}" {{ request('category_id') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <select name="severity" class="w-full text-xs rounded-lg border-slate-200 focus:border-emerald-500 focus:ring-emerald-500">
                    <option value="">-- Semua Tingkat Keparahan --</option>
                    <option value="minor" {{ request('severity') === 'minor' ? 'selected' : '' }}>Minor</option>
                    <option value="major" {{ request('severity') === 'major' ? 'selected' : '' }}>Major</option>
                    <option value="critical" {{ request('severity') === 'critical' ? 'selected' : '' }}>Critical</option>
                </select>
            </div>
            <div class="flex items-center space-x-2">
                <button type="submit" class="w-full py-2 bg-slate-800 hover:bg-slate-900 text-white text-xs font-medium rounded-lg transition-colors">
                    Filter Data
                </button>
                @if(request()->anyFilled(['search', 'category_id', 'severity']))
                    <a href="{{ route('admin.defects.index') }}" class="px-3 py-2 text-xs text-slate-500 hover:text-slate-700">Reset</a>
                @endif
            </div>
        </form>
    </div>

    <!-- Defect Types Table -->
    <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50 text-[11px] font-semibold uppercase text-slate-500 border-b border-slate-200">
                        <th class="py-3 px-4">Kode</th>
                        <th class="py-3 px-4">Jenis Cacat</th>
                        <th class="py-3 px-4">Kategori Mutu</th>
                        <th class="py-3 px-4">Keparahan (Severity)</th>
                        <th class="py-3 px-4">Kategori 5M+1E</th>
                        <th class="py-3 px-4">Frekuensi Temuan</th>
                        <th class="py-3 px-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-xs">
                    @forelse($defectTypes as $type)
                        <tr class="hover:bg-slate-50/80 transition-colors">
                            <td class="py-3 px-4 font-mono font-bold text-slate-900">{{ $type->code }}</td>
                            <td class="py-3 px-4">
                                <div class="font-medium text-slate-900">{{ $type->name }}</div>
                                <div class="text-[11px] text-slate-400 truncate max-w-xs">{{ $type->description }}</div>
                            </td>
                            <td class="py-3 px-4 text-slate-600 font-medium">{{ $type->defectCategory?->name ?? '-' }}</td>
                            <td class="py-3 px-4">
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] {{ $type->severity->badgeColor() }}">
                                    {{ $type->severity->label() }}
                                </span>
                            </td>
                            <td class="py-3 px-4">
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] {{ $type->default_5m_category->badgeColor() }}">
                                    {{ $type->default_5m_category->label() }}
                                </span>
                            </td>
                            <td class="py-3 px-4 font-mono font-medium {{ $type->inspection_defects_count > 0 ? 'text-slate-900' : 'text-slate-400' }}">
                                {{ $type->inspection_defects_count }} kali
                            </td>
                            <td class="py-3 px-4 text-right space-x-2">
                                <a href="{{ route('admin.defects.edit', $type) }}" class="text-xs font-medium text-slate-600 hover:text-emerald-600">Edit</a>
                                <form action="{{ route('admin.defects.destroy', $type) }}" method="POST" class="inline" onsubmit="return confirm('Hapus jenis cacat ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-xs font-medium text-rose-600 hover:text-rose-800">Hapus</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-8 text-center text-slate-400">Belum ada jenis cacat terdaftar.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($defectTypes->hasPages())
            <div class="p-4 border-t border-slate-100">
                {{ $defectTypes->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
