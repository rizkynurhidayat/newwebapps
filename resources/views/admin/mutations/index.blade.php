<x-admin-layout>
    <x-slot name="header">Mutasi Lokasi Barang Antar Ruangan</x-slot>

    <div class="space-y-6 pt-2">
        <!-- Header & Action Toolbar -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h2 class="text-xl font-bold text-slate-900 tracking-tight">Mutasi Antar Ruangan</h2>
                <p class="text-xs text-slate-500 mt-0.5">Catat dan pantau perpindahan fisik barang atau aset dari satu lokasi ke lokasi lain</p>
            </div>
            <a href="{{ route('mutations.create') }}" 
               class="inline-flex items-center px-4 py-2 bg-indigo-600 hover:bg-indigo-500 text-white text-xs font-semibold rounded-xl shadow-sm shadow-indigo-600/30 transition-all">
                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Catat Mutasi Baru
            </a>
        </div>

        <!-- Search Bar -->
        <div class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-xs">
            <form method="GET" action="{{ route('mutations.index') }}" class="flex items-center gap-3">
                <div class="relative flex-1">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    </div>
                    <input type="text" name="search" value="{{ $search }}" placeholder="Cari kode mutasi, nama barang, atau nama staf penanggung jawab..." 
                           class="w-full pl-9 pr-4 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-900 focus:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                </div>
                <button type="submit" class="px-4 py-2 bg-slate-900 text-white text-xs font-semibold rounded-xl hover:bg-slate-800 transition-colors">
                    Cari
                </button>
                @if($search)
                <a href="{{ route('mutations.index') }}" class="px-3 py-2 bg-slate-100 text-slate-600 text-xs font-semibold rounded-xl hover:bg-slate-200 transition-colors">
                    Reset
                </a>
                @endif
            </form>
        </div>

        <!-- Table Mutations -->
        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="bg-slate-50 text-slate-500 uppercase tracking-wider font-semibold border-b border-slate-100">
                        <tr>
                            <th class="px-6 py-3.5">Kode Mutasi</th>
                            <th class="px-6 py-3.5">Nama Barang</th>
                            <th class="px-6 py-3.5">Perpindahan Lokasi</th>
                            <th class="px-6 py-3.5">Jumlah</th>
                            <th class="px-6 py-3.5">Tanggal Mutasi</th>
                            <th class="px-6 py-3.5">Dipindahkan Oleh</th>
                            <th class="px-6 py-3.5">Alasan / Keterangan</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($mutations as $mut)
                        <tr class="hover:bg-slate-50/70 transition-colors">
                            <td class="px-6 py-4 font-mono font-bold text-slate-900">{{ $mut->mutation_code }}</td>
                            <td class="px-6 py-4">
                                <a href="{{ route('items.show', $mut->item) }}" class="font-bold text-indigo-600 hover:text-indigo-800 block">
                                    {{ $mut->item->name }}
                                </a>
                                <span class="text-[10px] text-slate-400 font-mono">{{ $mut->item->code }}</span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center space-x-2 text-xs">
                                    <span class="text-slate-600 font-medium">{{ $mut->fromLocation->name }}</span>
                                    <svg class="w-3.5 h-3.5 text-indigo-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                                    <span class="font-bold text-slate-900">{{ $mut->toLocation->name }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-4 font-semibold text-slate-800">{{ $mut->quantity }} {{ $mut->item->unit }}</td>
                            <td class="px-6 py-4 text-slate-600">{{ $mut->mutation_date->format('d/m/Y') }}</td>
                            <td class="px-6 py-4 text-slate-700 font-medium">{{ $mut->mover->name }}</td>
                            <td class="px-6 py-4 text-slate-500 max-w-xs truncate">{{ $mut->reason ?? '-' }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center text-slate-400">
                                Tidak ada data mutasi perpindahan barang.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($mutations->hasPages())
            <div class="px-6 py-4 border-t border-slate-100">
                {{ $mutations->links() }}
            </div>
            @endif
        </div>
    </div>
</x-admin-layout>
