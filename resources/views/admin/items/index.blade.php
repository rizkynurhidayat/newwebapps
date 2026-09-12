<x-admin-layout>
    <x-slot name="header">Katalog & Manajemen Barang</x-slot>

    <div class="space-y-6 pt-2">
        <!-- Header & Action Toolbar -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h2 class="text-xl font-bold text-slate-900 tracking-tight">Katalog Barang & Aset</h2>
                <p class="text-xs text-slate-500 mt-0.5">Daftar seluruh aset inventaris dan barang operasional perusahaan</p>
            </div>
            <div class="flex items-center space-x-2.5">
                @if(auth()->user()->canManageInventory())
                <a href="{{ route('items.create') }}" 
                   class="inline-flex items-center px-4 py-2 bg-indigo-600 hover:bg-indigo-500 text-white text-xs font-semibold rounded-xl shadow-sm shadow-indigo-600/30 transition-all">
                    <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    Tambah Barang Baru
                </a>
                @endif
            </div>
        </div>

        <!-- Filter & Search Bar -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs" x-data="{ advancedOpen: {{ ($categoryId || $locationId || $status || $condition || $isConsumable !== null) ? 'true' : 'false' }} }">
            <form method="GET" action="{{ route('items.index') }}" class="space-y-3">
                <div class="flex flex-col sm:flex-row items-center gap-3">
                    <div class="relative flex-1 w-full">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                        </div>
                        <input type="text" name="search" value="{{ $search }}" placeholder="Cari nama barang, kode inventaris, atau barcode..." 
                               class="w-full pl-10 pr-4 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-900 focus:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    </div>
                    <div class="flex items-center space-x-2 w-full sm:w-auto">
                        <button type="button" @click="advancedOpen = !advancedOpen" 
                                class="w-full sm:w-auto px-3.5 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold rounded-xl flex items-center justify-center transition-colors">
                            <svg class="w-3.5 h-3.5 mr-1.5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/></svg>
                            Filter Lanjutan
                            <svg class="w-3 h-3 ml-1 transition-transform" :class="advancedOpen ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                        </button>
                        <button type="submit" class="px-4 py-2 bg-slate-900 text-white text-xs font-semibold rounded-xl hover:bg-slate-800 transition-colors">
                            Terapkan
                        </button>
                    </div>
                </div>

                <!-- Advanced Filters Panel -->
                <div x-show="advancedOpen" x-transition class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-5 gap-3 pt-3 border-t border-slate-100">
                    <div>
                        <label class="block text-[11px] font-semibold text-slate-600 mb-1">Kategori</label>
                        <select name="category_id" class="w-full px-2.5 py-1.5 bg-slate-50 border border-slate-200 rounded-lg text-xs text-slate-800 focus:outline-none focus:ring-1 focus:ring-indigo-500">
                            <option value="">Semua Kategori</option>
                            @foreach($categories as $c)
                            <option value="{{ $c->id }}" {{ $categoryId == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-[11px] font-semibold text-slate-600 mb-1">Lokasi / Ruangan</label>
                        <select name="location_id" class="w-full px-2.5 py-1.5 bg-slate-50 border border-slate-200 rounded-lg text-xs text-slate-800 focus:outline-none focus:ring-1 focus:ring-indigo-500">
                            <option value="">Semua Ruangan</option>
                            @foreach($locations as $l)
                            <option value="{{ $l->id }}" {{ $locationId == $l->id ? 'selected' : '' }}>{{ $l->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-[11px] font-semibold text-slate-600 mb-1">Kondisi</label>
                        <select name="condition" class="w-full px-2.5 py-1.5 bg-slate-50 border border-slate-200 rounded-lg text-xs text-slate-800 focus:outline-none focus:ring-1 focus:ring-indigo-500">
                            <option value="">Semua Kondisi</option>
                            @foreach($conditions as $cond)
                            <option value="{{ $cond->value }}" {{ $condition == $cond->value ? 'selected' : '' }}>{{ $cond->label() }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-[11px] font-semibold text-slate-600 mb-1">Status Ketersediaan</label>
                        <select name="status" class="w-full px-2.5 py-1.5 bg-slate-50 border border-slate-200 rounded-lg text-xs text-slate-800 focus:outline-none focus:ring-1 focus:ring-indigo-500">
                            <option value="">Semua Status</option>
                            @foreach($statuses as $st)
                            <option value="{{ $st->value }}" {{ $status == $st->value ? 'selected' : '' }}>{{ $st->label() }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-[11px] font-semibold text-slate-600 mb-1">Tipe Barang</label>
                        <select name="is_consumable" class="w-full px-2.5 py-1.5 bg-slate-50 border border-slate-200 rounded-lg text-xs text-slate-800 focus:outline-none focus:ring-1 focus:ring-indigo-500">
                            <option value="">Semua Tipe</option>
                            <option value="0" {{ $isConsumable === '0' ? 'selected' : '' }}>Aset Tetap</option>
                            <option value="1" {{ $isConsumable === '1' ? 'selected' : '' }}>Barang Habis Pakai (ATK)</option>
                        </select>
                    </div>
                </div>
            </form>
        </div>

        <!-- Table Items -->
        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="bg-slate-50 text-slate-500 uppercase tracking-wider font-semibold border-b border-slate-100">
                        <tr>
                            <th class="px-6 py-3.5">Foto & Kode</th>
                            <th class="px-6 py-3.5">Nama Barang</th>
                            <th class="px-6 py-3.5">Kategori & Lokasi</th>
                            <th class="px-6 py-3.5">Stok / Satuan</th>
                            <th class="px-6 py-3.5">Kondisi</th>
                            <th class="px-6 py-3.5">Status</th>
                            <th class="px-6 py-3.5 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($items as $item)
                        <tr class="hover:bg-slate-50/70 transition-colors">
                            <td class="px-6 py-4">
                                <div class="flex items-center space-x-3">
                                    <div class="w-12 h-12 rounded-xl bg-slate-100 border border-slate-200 flex items-center justify-center shrink-0 overflow-hidden">
                                        @if($item->image_path)
                                        <img src="{{ asset('storage/' . $item->image_path) }}" alt="{{ $item->name }}" class="w-full h-full object-cover">
                                        @else
                                        <svg class="w-6 h-6 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                                        @endif
                                    </div>
                                    <div>
                                        <span class="font-bold text-indigo-600 block">{{ $item->code }}</span>
                                        <span class="text-[10px] text-slate-400">{{ $item->barcode ?? 'Tanpa Barcode' }}</span>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <a href="{{ route('items.show', $item) }}" class="font-bold text-slate-900 hover:text-indigo-600 transition-colors block text-xs">
                                    {{ $item->name }}
                                </a>
                                <p class="text-[11px] text-slate-500 mt-0.5 max-w-xs truncate">{{ $item->description ?? '-' }}</p>
                            </td>
                            <td class="px-6 py-4">
                                <span class="font-semibold text-slate-800 block">{{ $item->category->name }}</span>
                                <span class="text-[11px] text-slate-500 flex items-center mt-0.5">
                                    <svg class="w-3 h-3 mr-1 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/></svg>
                                    {{ $item->location->name }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-baseline space-x-1">
                                    <span class="font-bold text-slate-900 text-sm {{ ($item->is_consumable && $item->stock <= $item->min_stock) ? 'text-rose-600' : '' }}">
                                        {{ $item->stock }}
                                    </span>
                                    <span class="text-slate-500 text-[11px]">{{ $item->unit }}</span>
                                </div>
                                @if($item->is_consumable && $item->stock <= $item->min_stock)
                                <span class="inline-block mt-0.5 text-[10px] font-bold text-rose-600 bg-rose-50 px-1.5 py-0.5 rounded">
                                    Stok Menipis!
                                </span>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold {{ $item->condition->badgeColor() }}">
                                    {{ $item->condition->label() }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-semibold {{ $item->status->badgeColor() }}">
                                    {{ $item->status->label() }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-right space-x-1.5 whitespace-nowrap">
                                <a href="{{ route('items.show', $item) }}" class="inline-flex items-center px-2.5 py-1.5 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-lg text-xs font-medium transition-colors" title="Lihat Detail">
                                    Detail
                                </a>
                                <a href="{{ route('items.barcode', $item) }}" target="_blank" class="inline-flex items-center px-2.5 py-1.5 bg-indigo-50 hover:bg-indigo-100 text-indigo-700 rounded-lg text-xs font-medium transition-colors" title="Cetak Label Barcode">
                                    Label
                                </a>
                                @if(auth()->user()->canManageInventory())
                                <a href="{{ route('items.edit', $item) }}" class="inline-flex items-center px-2.5 py-1.5 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-lg text-xs font-medium transition-colors">
                                    Edit
                                </a>
                                <form method="POST" action="{{ route('items.destroy', $item) }}" class="inline-block" onsubmit="return confirm('Apakah Anda yakin ingin menghapus barang ini?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="inline-flex items-center px-2.5 py-1.5 bg-rose-50 hover:bg-rose-100 text-rose-600 rounded-lg text-xs font-medium transition-colors">
                                        Hapus
                                    </button>
                                </form>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center text-slate-400">
                                Tidak ada data barang yang sesuai dengan kriteria filter pencarian.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($items->hasPages())
            <div class="px-6 py-4 border-t border-slate-100">
                {{ $items->links() }}
            </div>
            @endif
        </div>
    </div>
</x-admin-layout>
