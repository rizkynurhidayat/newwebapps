<x-admin-layout>
    <x-slot name="header">Histori & Transaksi Stok Barang</x-slot>

    <div class="space-y-6 pt-2">
        <!-- Header & Action Toolbar -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h2 class="text-xl font-bold text-slate-900 tracking-tight">Log & Transaksi Stok</h2>
                <p class="text-xs text-slate-500 mt-0.5">Catat penerimaan barang masuk (restock), barang keluar, dan penyesuaian opname</p>
            </div>
            <a href="{{ route('stock.create') }}" 
               class="inline-flex items-center px-4 py-2 bg-indigo-600 hover:bg-indigo-500 text-white text-xs font-semibold rounded-xl shadow-sm shadow-indigo-600/30 transition-all">
                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Catat Transaksi Stok
            </a>
        </div>

        <!-- Search & Filter Bar -->
        <div class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-xs">
            <form method="GET" action="{{ route('stock.index') }}" class="flex flex-col sm:flex-row items-center gap-3">
                <div class="relative flex-1 w-full">
                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    </div>
                    <input type="text" name="search" value="{{ $search }}" placeholder="Cari nama barang, kode inventaris, atau keterangan transaksi..." 
                           class="w-full pl-10 pr-4 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-900 focus:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                </div>

                <div class="flex items-center space-x-2 w-full sm:w-auto">
                    <select name="type" class="px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-800 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        <option value="">Semua Tipe</option>
                        @foreach($types as $t)
                        <option value="{{ $t->value }}" {{ $type == $t->value ? 'selected' : '' }}>{{ $t->label() }}</option>
                        @endforeach
                    </select>
                    <button type="submit" class="px-4 py-2 bg-slate-900 text-white text-xs font-semibold rounded-xl hover:bg-slate-800 transition-colors">
                        Filter
                    </button>
                    @if($type || $search)
                    <a href="{{ route('stock.index') }}" class="px-3 py-2 bg-slate-100 text-slate-600 text-xs font-semibold rounded-xl hover:bg-slate-200 transition-colors">
                        Reset
                    </a>
                    @endif
                </div>
            </form>
        </div>

        <!-- Table Stock Logs -->
        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="bg-slate-50 text-slate-500 uppercase tracking-wider font-semibold border-b border-slate-100">
                        <tr>
                            <th class="px-6 py-3.5">Tipe Transaksi</th>
                            <th class="px-6 py-3.5">Nama Barang</th>
                            <th class="px-6 py-3.5">Jumlah Perubahan</th>
                            <th class="px-6 py-3.5">Stok Sebelum</th>
                            <th class="px-6 py-3.5">Stok Sesudah</th>
                            <th class="px-6 py-3.5">Waktu Pencatatan</th>
                            <th class="px-6 py-3.5">Dicatat Oleh</th>
                            <th class="px-6 py-3.5">Keterangan</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($stockLogs as $log)
                        <tr class="hover:bg-slate-50/70 transition-colors">
                            <td class="px-6 py-4">
                                <span class="inline-flex px-2.5 py-1 rounded text-[10px] font-bold uppercase {{ $log->type->badgeColor() }}">
                                    {{ $log->type->label() }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <a href="{{ route('items.show', $log->item) }}" class="font-bold text-slate-900 hover:text-indigo-600 block">
                                    {{ $log->item->name }}
                                </a>
                                <span class="text-[10px] text-slate-400 font-mono">{{ $log->item->code }}</span>
                            </td>
                            <td class="px-6 py-4 font-bold text-sm text-slate-900">
                                {{ $log->type === \App\Enums\StockLogType::In ? '+' : ($log->type === \App\Enums\StockLogType::Out ? '-' : '') }}{{ $log->quantity }} {{ $log->item->unit }}
                            </td>
                            <td class="px-6 py-4 text-slate-500">{{ $log->before_stock }}</td>
                            <td class="px-6 py-4 font-bold text-slate-800">{{ $log->after_stock }}</td>
                            <td class="px-6 py-4 text-slate-600">{{ $log->created_at->format('d/m/Y H:i') }}</td>
                            <td class="px-6 py-4 text-slate-700 font-medium">{{ $log->creator->name ?? '-' }}</td>
                            <td class="px-6 py-4 text-slate-500 max-w-xs truncate">{{ $log->notes ?? '-' }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="px-6 py-12 text-center text-slate-400">
                                Tidak ada data histori transaksi stok.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($stockLogs->hasPages())
            <div class="px-6 py-4 border-t border-slate-100">
                {{ $stockLogs->links() }}
            </div>
            @endif
        </div>
    </div>
</x-admin-layout>
