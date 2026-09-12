<x-admin-layout>
    <x-slot name="header">Catat Mutasi Lokasi Barang Baru</x-slot>

    <div class="max-w-2xl mx-auto py-6" x-data="{
        selectedItemLocation: '',
        selectedItemId: '{{ old('item_id') }}',
        updateLocation(event) {
            const selectedOpt = event.target.options[event.target.selectedIndex];
            this.selectedItemLocation = selectedOpt.getAttribute('data-location') || '';
        }
    }">
        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs p-6 sm:p-8">
            <div class="mb-6 pb-4 border-b border-slate-100 flex items-center justify-between">
                <div>
                    <h2 class="text-base font-bold text-slate-900">Formulir Mutasi Barang</h2>
                    <p class="text-xs text-slate-500">Pindahkan fisik barang ke gedung atau ruangan baru</p>
                </div>
                <a href="{{ route('mutations.index') }}" class="text-xs text-slate-600 hover:text-slate-900 font-medium">&larr; Kembali</a>
            </div>

            <form method="POST" action="{{ route('mutations.store') }}" class="space-y-4">
                @csrf

                <div>
                    <label for="item_id" class="block text-xs font-semibold text-slate-700 mb-1">Pilih Barang yang Dimutasi <span class="text-rose-500">*</span></label>
                    <select id="item_id" name="item_id" required @change="updateLocation($event)"
                            class="w-full px-3.5 py-2.5 bg-white border border-slate-300 rounded-xl text-xs text-slate-900 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        <option value="">-- Pilih Barang --</option>
                        @foreach($items as $itm)
                        <option value="{{ $itm->id }}" data-location="{{ $itm->location->name }}" {{ old('item_id') == $itm->id ? 'selected' : '' }}>
                            {{ $itm->code }} - {{ $itm->name }} (Saat ini: {{ $itm->location->name }})
                        </option>
                        @endforeach
                    </select>
                </div>

                <div x-show="selectedItemLocation" class="p-3 bg-slate-50 rounded-xl border border-slate-200 text-xs flex items-center">
                    <svg class="w-4 h-4 mr-2 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/></svg>
                    <span>Lokasi Asal Saat Ini: <strong class="text-slate-900 font-semibold" x-text="selectedItemLocation"></strong></span>
                </div>

                <div>
                    <label for="to_location_id" class="block text-xs font-semibold text-slate-700 mb-1">Lokasi / Ruangan Tujuan Baru <span class="text-rose-500">*</span></label>
                    <select id="to_location_id" name="to_location_id" required
                            class="w-full px-3.5 py-2.5 bg-white border border-slate-300 rounded-xl text-xs text-slate-900 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        <option value="">-- Pilih Lokasi Tujuan --</option>
                        @foreach($locations as $loc)
                        <option value="{{ $loc->id }}" {{ old('to_location_id') == $loc->id ? 'selected' : '' }}>
                            {{ $loc->name }} ({{ $loc->code }} &bull; PIC: {{ $loc->pic_name ?? '-' }})
                        </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="quantity" class="block text-xs font-semibold text-slate-700 mb-1">Jumlah Unit yang Dipindahkan <span class="text-rose-500">*</span></label>
                    <input type="number" id="quantity" name="quantity" value="{{ old('quantity', 1) }}" min="1" required
                           class="w-full px-3.5 py-2 bg-white border border-slate-300 rounded-xl text-xs text-slate-900 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                </div>

                <div>
                    <label for="reason" class="block text-xs font-semibold text-slate-700 mb-1">Alasan / Keterangan Mutasi</label>
                    <textarea id="reason" name="reason" rows="3" placeholder="Contoh: Kebutuhan penambahan inventaris staf baru di divisi pemasaran..."
                              class="w-full px-3.5 py-2 bg-white border border-slate-300 rounded-xl text-xs text-slate-900 focus:outline-none focus:ring-2 focus:ring-indigo-500">{{ old('reason') }}</textarea>
                </div>

                <div class="flex items-center justify-end space-x-3 pt-4 border-t border-slate-100">
                    <a href="{{ route('mutations.index') }}" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold rounded-xl transition-colors">
                        Batal
                    </a>
                    <button type="submit" class="px-6 py-2 bg-indigo-600 hover:bg-indigo-500 text-white text-xs font-semibold rounded-xl shadow-sm shadow-indigo-600/30 transition-all">
                        Simpan Mutasi
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-admin-layout>
