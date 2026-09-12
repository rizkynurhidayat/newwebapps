<x-admin-layout>
    <x-slot name="header">Detail Barang: {{ $item->name }}</x-slot>

    <div class="space-y-6 pt-2" x-data="{ activeTab: 'loans' }">
        <!-- Breadcrumb & Top Bar -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div class="flex items-center space-x-2 text-xs text-slate-500">
                <a href="{{ route('items.index') }}" class="hover:text-slate-900 font-medium">&larr; Kembali ke Katalog Barang</a>
                <span>&bull;</span>
                <span class="font-mono text-indigo-600 font-bold">{{ $item->code }}</span>
            </div>
            <div class="flex items-center space-x-2">
                <a href="{{ route('items.barcode', $item) }}" target="_blank"
                   class="inline-flex items-center px-3 py-2 bg-white border border-slate-200 hover:bg-slate-50 text-slate-700 text-xs font-semibold rounded-xl shadow-xs transition-colors">
                    <svg class="w-4 h-4 mr-1.5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/></svg>
                    Cetak Label
                </a>
                @if(auth()->user()->canManageInventory())
                <a href="{{ route('items.edit', $item) }}" 
                   class="inline-flex items-center px-3.5 py-2 bg-slate-900 hover:bg-slate-800 text-white text-xs font-semibold rounded-xl shadow-xs transition-colors">
                    <svg class="w-3.5 h-3.5 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                    Edit Data
                </a>
                @endif
            </div>
        </div>

        <!-- Overview Card Profil Barang -->
        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 p-6 sm:p-8">
                <!-- Foto Barang -->
                <div class="flex flex-col items-center justify-center p-4 bg-slate-50 rounded-2xl border border-slate-100">
                    <div class="w-full aspect-4/3 rounded-xl bg-white border border-slate-200 flex items-center justify-center overflow-hidden shadow-xs">
                        @if($item->image_path)
                        <img src="{{ asset('storage/' . $item->image_path) }}" alt="{{ $item->name }}" class="w-full h-full object-cover">
                        @else
                        <div class="text-center p-6">
                            <svg class="w-16 h-16 mx-auto text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                            <span class="text-xs text-slate-400 mt-2 block">Belum ada foto</span>
                        </div>
                        @endif
                    </div>
                    <div class="mt-4 text-center">
                        <span class="font-mono text-sm font-bold text-slate-900 block">{{ $item->barcode ?? $item->code }}</span>
                        <span class="text-[11px] text-slate-400">Barcode / Serial Number</span>
                    </div>
                </div>

                <!-- Informasi Spesifikasi & Status -->
                <div class="md:col-span-2 flex flex-col justify-between">
                    <div>
                        <div class="flex flex-wrap items-center gap-2 mb-2">
                            <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold {{ $item->status->badgeColor() }}">
                                {{ $item->status->label() }}
                            </span>
                            <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold {{ $item->condition->badgeColor() }}">
                                Kondisi: {{ $item->condition->label() }}
                            </span>
                            <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold bg-slate-100 text-slate-700">
                                {{ $item->is_consumable ? 'Habis Pakai (ATK)' : 'Aset Tetap' }}
                            </span>
                        </div>

                        <h1 class="text-2xl font-black text-slate-900 tracking-tight">{{ $item->name }}</h1>
                        <p class="text-xs text-slate-500 mt-2 whitespace-pre-line leading-relaxed">{{ $item->description ?? 'Tidak ada catatan deskripsi tambahan.' }}</p>
                    </div>

                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 pt-6 mt-6 border-t border-slate-100 text-xs">
                        <div>
                            <span class="text-slate-400 block text-[11px]">Kategori</span>
                            <strong class="text-slate-900 text-sm font-bold">{{ $item->category->name }}</strong>
                        </div>
                        <div>
                            <span class="text-slate-400 block text-[11px]">Lokasi Ruangan</span>
                            <strong class="text-slate-900 text-sm font-bold">{{ $item->location->name }}</strong>
                        </div>
                        <div>
                            <span class="text-slate-400 block text-[11px]">Jumlah Stok</span>
                            <strong class="text-slate-900 text-sm font-bold">{{ $item->stock }} {{ $item->unit }}</strong>
                        </div>
                        <div>
                            <span class="text-slate-400 block text-[11px]">Harga Perolehan</span>
                            <strong class="text-indigo-600 text-sm font-bold">{{ $item->formattedPrice() }}</strong>
                        </div>
                    </div>

                    @if($item->vendor)
                    <div class="pt-4 mt-4 border-t border-slate-100 flex items-center justify-between text-xs text-slate-500">
                        <span>Vendor: <strong class="text-slate-700">{{ $item->vendor->name }}</strong> ({{ $item->vendor->phone ?? 'Tanpa Telp' }})</span>
                        <span>Tgl Perolehan: <strong class="text-slate-700">{{ $item->purchase_date?->format('d F Y') ?? '-' }}</strong></span>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Tab Riwayat Historis: Peminjaman, Mutasi, Stok -->
        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
            <!-- Tab Headers -->
            <div class="flex border-b border-slate-200 px-6 pt-2 bg-slate-50/70">
                <button @click="activeTab = 'loans'" 
                        :class="activeTab === 'loans' ? 'border-indigo-600 text-indigo-600 font-bold bg-white' : 'border-transparent text-slate-500 hover:text-slate-700 font-medium'"
                        class="px-5 py-3 border-b-2 text-xs transition-colors rounded-t-xl">
                    Riwayat Peminjaman ({{ $item->loans->count() }})
                </button>
                <button @click="activeTab = 'mutations'" 
                        :class="activeTab === 'mutations' ? 'border-indigo-600 text-indigo-600 font-bold bg-white' : 'border-transparent text-slate-500 hover:text-slate-700 font-medium'"
                        class="px-5 py-3 border-b-2 text-xs transition-colors rounded-t-xl">
                    Riwayat Mutasi Ruangan ({{ $item->mutations->count() }})
                </button>
                <button @click="activeTab = 'stock'" 
                        :class="activeTab === 'stock' ? 'border-indigo-600 text-indigo-600 font-bold bg-white' : 'border-transparent text-slate-500 hover:text-slate-700 font-medium'"
                        class="px-5 py-3 border-b-2 text-xs transition-colors rounded-t-xl">
                    Log Penyesuaian Stok ({{ $item->stockLogs->count() }})
                </button>
            </div>

            <!-- Tab 1: Peminjaman -->
            <div x-show="activeTab === 'loans'" class="p-6">
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs">
                        <thead class="text-slate-500 uppercase tracking-wider font-semibold border-b border-slate-100">
                            <tr>
                                <th class="pb-3">Kode Pinjam</th>
                                <th class="pb-3">Peminjam</th>
                                <th class="pb-3">Tgl Pinjam</th>
                                <th class="pb-3">Batas Kembali</th>
                                <th class="pb-3">Tgl Kembali</th>
                                <th class="pb-3">Kondisi Kembali</th>
                                <th class="pb-3">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse($item->loans as $loan)
                            <tr>
                                <td class="py-3 font-mono font-semibold text-slate-800">{{ $loan->loan_code }}</td>
                                <td class="py-3 font-medium text-slate-900">{{ $loan->borrower->name }}</td>
                                <td class="py-3 text-slate-600">{{ $loan->loan_date->format('d/m/Y') }}</td>
                                <td class="py-3 text-slate-600">{{ $loan->due_date->format('d/m/Y') }}</td>
                                <td class="py-3 text-slate-600">{{ $loan->return_date?->format('d/m/Y') ?? '-' }}</td>
                                <td class="py-3">
                                    @if($loan->return_condition)
                                    <span class="inline-flex px-2 py-0.5 rounded-full text-[10px] font-semibold {{ $loan->return_condition->badgeColor() }}">
                                        {{ $loan->return_condition->label() }}
                                    </span>
                                    @else
                                    <span class="text-slate-400">-</span>
                                    @endif
                                </td>
                                <td class="py-3">
                                    <span class="inline-flex px-2 py-0.5 rounded-full text-[10px] font-semibold {{ $loan->status->badgeColor() }}">
                                        {{ $loan->status->label() }}
                                    </span>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="py-8 text-center text-slate-400">Belum ada riwayat peminjaman untuk barang ini.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Tab 2: Mutasi Ruangan -->
            <div x-show="activeTab === 'mutations'" class="p-6" style="display: none;">
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs">
                        <thead class="text-slate-500 uppercase tracking-wider font-semibold border-b border-slate-100">
                            <tr>
                                <th class="pb-3">Kode Mutasi</th>
                                <th class="pb-3">Dari Ruangan</th>
                                <th class="pb-3">Ke Ruangan</th>
                                <th class="pb-3">Dipindahkan Oleh</th>
                                <th class="pb-3">Tanggal</th>
                                <th class="pb-3">Alasan</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse($item->mutations as $mut)
                            <tr>
                                <td class="py-3 font-mono font-semibold text-slate-800">{{ $mut->mutation_code }}</td>
                                <td class="py-3 text-slate-700">{{ $mut->fromLocation->name }}</td>
                                <td class="py-3 font-semibold text-indigo-600">{{ $mut->toLocation->name }}</td>
                                <td class="py-3 text-slate-600">{{ $mut->mover->name }}</td>
                                <td class="py-3 text-slate-600">{{ $mut->mutation_date->format('d/m/Y') }}</td>
                                <td class="py-3 text-slate-500 max-w-xs truncate">{{ $mut->reason ?? '-' }}</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="py-8 text-center text-slate-400">Belum pernah terjadi mutasi lokasi untuk barang ini.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Tab 3: Log Stok -->
            <div x-show="activeTab === 'stock'" class="p-6" style="display: none;">
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs">
                        <thead class="text-slate-500 uppercase tracking-wider font-semibold border-b border-slate-100">
                            <tr>
                                <th class="pb-3">Tipe</th>
                                <th class="pb-3">Kuantitas</th>
                                <th class="pb-3">Sebelum</th>
                                <th class="pb-3">Sesudah</th>
                                <th class="pb-3">Waktu</th>
                                <th class="pb-3">Dicatat Oleh</th>
                                <th class="pb-3">Keterangan</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse($item->stockLogs as $log)
                            <tr>
                                <td class="py-3">
                                    <span class="inline-flex px-2 py-0.5 rounded text-[10px] font-bold uppercase {{ $log->type->badgeColor() }}">
                                        {{ $log->type->label() }}
                                    </span>
                                </td>
                                <td class="py-3 font-bold text-slate-900">{{ $log->quantity }}</td>
                                <td class="py-3 text-slate-600">{{ $log->before_stock }}</td>
                                <td class="py-3 font-bold text-slate-800">{{ $log->after_stock }}</td>
                                <td class="py-3 text-slate-500">{{ $log->created_at->format('d/m/Y H:i') }}</td>
                                <td class="py-3 text-slate-600">{{ $log->creator->name ?? '-' }}</td>
                                <td class="py-3 text-slate-500 max-w-xs truncate">{{ $log->notes ?? '-' }}</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="py-8 text-center text-slate-400">Belum ada riwayat aktivitas penyesuaian stok.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-admin-layout>
