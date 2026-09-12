<x-admin-layout>
    <x-slot name="header">Catat Transaksi Stok Baru</x-slot>

    <div class="max-w-2xl mx-auto py-6" x-data="{
        selectedItemStock: '',
        selectedItemUnit: '',
        updateItemInfo(event) {
            const opt = event.target.options[event.target.selectedIndex];
            this.selectedItemStock = opt.getAttribute('data-stock') || '';
            this.selectedItemUnit = opt.getAttribute('data-unit') || '';
        }
    }">
        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs p-6 sm:p-8">
            <div class="mb-6 pb-4 border-b border-slate-100 flex items-center justify-between">
                <div>
                    <h2 class="text-base font-bold text-slate-900">Pencatatan Penyesuaian Stok</h2>
                    <p class="text-xs text-slate-500">Catat penerimaan barang baru, pengeluaran pemakaian, atau stock opname</p>
                </div>
                <a href="{{ route('stock.index') }}" class="text-xs text-slate-600 hover:text-slate-900 font-medium">&larr; Kembali</a>
            </div>

            <form method="POST" action="{{ route('stock.store') }}" class="space-y-4">
                @csrf

                <div>
                    <label for="item_id" class="block text-xs font-semibold text-slate-700 mb-1">Pilih Barang <span class="text-rose-500">*</span></label>
                    <select id="item_id" name="item_id" required @change="updateItemInfo($event)"
                            class="w-full px-3.5 py-2.5 bg-white border border-slate-300 rounded-xl text-xs text-slate-900 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        <option value="">-- Pilih Barang --</option>
                        @foreach($items as $itm)
                        <option value="{{ $itm->id }}" data-stock="{{ $itm->stock }}" data-unit="{{ $itm->unit }}" {{ old('item_id') == $itm->id ? 'selected' : '' }}>
                            {{ $itm->code }} - {{ $itm->name }} (Stok saat ini: {{ $itm->stock }} {{ $itm->unit }})
                        </option>
                        @endforeach
                    </select>
                </div>

                <div x-show="selectedItemStock" class="p-3 bg-slate-50 rounded-xl border border-slate-200 text-xs flex items-center">
                    <svg class="w-4 h-4 mr-2 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <span>Stok Saat Ini: <strong class="text-slate-900 font-bold" x-text="selectedItemStock + ' ' + selectedItemUnit"></strong></span>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="type" class="block text-xs font-semibold text-slate-700 mb-1">Jenis Transaksi <span class="text-rose-500">*</span></label>
                        <select id="type" name="type" required
                                class="w-full px-3.5 py-2 bg-white border border-slate-300 rounded-xl text-xs text-slate-900 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                            @foreach($types as $tp)
                            <option value="{{ $tp->value }}" {{ old('type') == $tp->value ? 'selected' : '' }}>{{ $tp->label() }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label for="quantity" class="block text-xs font-semibold text-slate-700 mb-1">Kuantitas Unit <span class="text-rose-500">*</span></label>
                        <input type="number" id="quantity" name="quantity" value="{{ old('quantity', 1) }}" min="1" required
                               class="w-full px-3.5 py-2 bg-white border border-slate-300 rounded-xl text-xs text-slate-900 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        <p class="text-[11px] text-slate-400 mt-1">Untuk tipe "Penyesuaian", angka ini adalah total stok aktual setelah dihitung.</p>
                    </div>
                </div>

                <div>
                    <label for="notes" class="block text-xs font-semibold text-slate-700 mb-1">Catatan Transaksi / Nomor Referensi PO <span class="text-rose-500">*</span></label>
                    <textarea id="notes" name="notes" rows="3" required placeholder="Contoh: Penerimaan restock barang dari PO #2026-088 atau pemakaian rutin operasional divisi..."
                              class="w-full px-3.5 py-2 bg-white border border-slate-300 rounded-xl text-xs text-slate-900 focus:outline-none focus:ring-2 focus:ring-indigo-500">{{ old('notes') }}</textarea>
                </div>

                <div class="flex items-center justify-end space-x-3 pt-4 border-t border-slate-100">
                    <a href="{{ route('stock.index') }}" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold rounded-xl transition-colors">
                        Batal
                    </a>
                    <button type="submit" class="px-6 py-2 bg-indigo-600 hover:bg-indigo-500 text-white text-xs font-semibold rounded-xl shadow-sm shadow-indigo-600/30 transition-all">
                        Simpan Transaksi Stok
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-admin-layout>
