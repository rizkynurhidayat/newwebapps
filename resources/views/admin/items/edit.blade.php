<x-admin-layout>
    <x-slot name="header">Edit Barang: {{ $item->name }}</x-slot>

    <div class="max-w-4xl mx-auto py-6" x-data="{
        imagePreview: {{ $item->image_path ? "'" . asset('storage/' . $item->image_path) . "'" : 'null' }},
        isConsumable: {{ old('is_consumable', $item->is_consumable) ? 'true' : 'false' }},
        previewFile(event) {
            const file = event.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = (e) => { this.imagePreview = e.target.result; };
                reader.readAsDataURL(file);
            }
        }
    }">
        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs p-6 sm:p-8">
            <div class="mb-6 pb-4 border-b border-slate-100 flex items-center justify-between">
                <div>
                    <h2 class="text-base font-bold text-slate-900">Perbarui Informasi Barang</h2>
                    <p class="text-xs text-slate-500">Kode: <strong class="text-indigo-600 font-mono">{{ $item->code }}</strong></p>
                </div>
                <a href="{{ route('items.show', $item) }}" class="text-xs text-slate-600 hover:text-slate-900 font-medium">&larr; Kembali ke Detail</a>
            </div>

            <form method="POST" action="{{ route('items.update', $item) }}" enctype="multipart/form-data" class="space-y-6">
                @csrf
                @method('PUT')

                <!-- Section 1: Kategori & Identitas Utama -->
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-5">
                    <div>
                        <label for="category_id" class="block text-xs font-semibold text-slate-700 mb-1">Kategori Barang <span class="text-rose-500">*</span></label>
                        <select id="category_id" name="category_id" required
                                class="w-full px-3.5 py-2 bg-white border border-slate-300 rounded-xl text-xs text-slate-900 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                            @foreach($categories as $cat)
                            <option value="{{ $cat->id }}" {{ old('category_id', $item->category_id) == $cat->id ? 'selected' : '' }}>{{ $cat->name }} ({{ $cat->code }})</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label for="code" class="block text-xs font-semibold text-slate-700 mb-1">Kode Unik Barang <span class="text-rose-500">*</span></label>
                        <input type="text" id="code" name="code" value="{{ old('code', $item->code) }}" required
                               class="w-full px-3.5 py-2 bg-white border border-slate-300 rounded-xl text-xs text-slate-900 uppercase font-mono focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    </div>

                    <div>
                        <label for="barcode" class="block text-xs font-semibold text-slate-700 mb-1">Nomor Barcode / Serial Number</label>
                        <input type="text" id="barcode" name="barcode" value="{{ old('barcode', $item->barcode) }}"
                               class="w-full px-3.5 py-2 bg-white border border-slate-300 rounded-xl text-xs text-slate-900 font-mono focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    </div>
                </div>

                <div>
                    <label for="name" class="block text-xs font-semibold text-slate-700 mb-1">Nama Barang Lengkap <span class="text-rose-500">*</span></label>
                    <input type="text" id="name" name="name" value="{{ old('name', $item->name) }}" required
                           class="w-full px-3.5 py-2 bg-white border border-slate-300 rounded-xl text-xs text-slate-900 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                </div>

                <div>
                    <label for="description" class="block text-xs font-semibold text-slate-700 mb-1">Deskripsi & Spesifikasi Teknis</label>
                    <textarea id="description" name="description" rows="3"
                              class="w-full px-3.5 py-2 bg-white border border-slate-300 rounded-xl text-xs text-slate-900 focus:outline-none focus:ring-2 focus:ring-indigo-500">{{ old('description', $item->description) }}</textarea>
                </div>

                <!-- Section 2: Lokasi, Vendor & Tipe Barang -->
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-5 pt-4 border-t border-slate-100">
                    <div>
                        <label for="location_id" class="block text-xs font-semibold text-slate-700 mb-1">Lokasi / Ruangan Penyimpanan <span class="text-rose-500">*</span></label>
                        <select id="location_id" name="location_id" required
                                class="w-full px-3.5 py-2 bg-white border border-slate-300 rounded-xl text-xs text-slate-900 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                            @foreach($locations as $loc)
                            <option value="{{ $loc->id }}" {{ old('location_id', $item->location_id) == $loc->id ? 'selected' : '' }}>{{ $loc->name }} ({{ $loc->code }})</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label for="vendor_id" class="block text-xs font-semibold text-slate-700 mb-1">Vendor / Supplier Pengadaan</label>
                        <select id="vendor_id" name="vendor_id"
                                class="w-full px-3.5 py-2 bg-white border border-slate-300 rounded-xl text-xs text-slate-900 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                            <option value="">-- Tanpa Vendor / Beli Lepas --</option>
                            @foreach($vendors as $ven)
                            <option value="{{ $ven->id }}" {{ old('vendor_id', $item->vendor_id) == $ven->id ? 'selected' : '' }}>{{ $ven->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label for="is_consumable" class="block text-xs font-semibold text-slate-700 mb-1">Tipe Sifat Barang</label>
                        <select id="is_consumable" name="is_consumable" @change="isConsumable = ($event.target.value === '1')"
                                class="w-full px-3.5 py-2 bg-white border border-slate-300 rounded-xl text-xs text-slate-900 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                            <option value="0" {{ old('is_consumable', $item->is_consumable) ? '' : 'selected' }}>Aset Tetap (Device, Furnitur)</option>
                            <option value="1" {{ old('is_consumable', $item->is_consumable) ? 'selected' : '' }}>Barang Habis Pakai / ATK</option>
                        </select>
                    </div>
                </div>

                <!-- Section 3: Stok, Satuan & Batas Minimum -->
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-5">
                    <div>
                        <label for="unit" class="block text-xs font-semibold text-slate-700 mb-1">Satuan Barang <span class="text-rose-500">*</span></label>
                        <input type="text" id="unit" name="unit" value="{{ old('unit', $item->unit) }}" required
                               class="w-full px-3.5 py-2 bg-white border border-slate-300 rounded-xl text-xs text-slate-900 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    </div>

                    <div>
                        <label for="stock" class="block text-xs font-semibold text-slate-700 mb-1">Jumlah Stok <span class="text-rose-500">*</span></label>
                        <input type="number" id="stock" name="stock" value="{{ old('stock', $item->stock) }}" min="0" required
                               class="w-full px-3.5 py-2 bg-white border border-slate-300 rounded-xl text-xs text-slate-900 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    </div>

                    <div>
                        <label for="min_stock" class="block text-xs font-semibold text-slate-700 mb-1">Batas Minimum Stok (Peringatan)</label>
                        <input type="number" id="min_stock" name="min_stock" value="{{ old('min_stock', $item->min_stock) }}" min="0" required
                               class="w-full px-3.5 py-2 bg-white border border-slate-300 rounded-xl text-xs text-slate-900 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    </div>
                </div>

                <!-- Section 4: Kondisi, Status & Perolehan -->
                <div class="grid grid-cols-1 sm:grid-cols-4 gap-5 pt-4 border-t border-slate-100">
                    <div>
                        <label for="condition" class="block text-xs font-semibold text-slate-700 mb-1">Kondisi Fisik <span class="text-rose-500">*</span></label>
                        <select id="condition" name="condition" required
                                class="w-full px-3.5 py-2 bg-white border border-slate-300 rounded-xl text-xs text-slate-900 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                            @foreach($conditions as $cond)
                            <option value="{{ $cond->value }}" {{ old('condition', $item->condition->value) == $cond->value ? 'selected' : '' }}>{{ $cond->label() }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label for="status" class="block text-xs font-semibold text-slate-700 mb-1">Status Ketersediaan <span class="text-rose-500">*</span></label>
                        <select id="status" name="status" required
                                class="w-full px-3.5 py-2 bg-white border border-slate-300 rounded-xl text-xs text-slate-900 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                            @foreach($statuses as $st)
                            <option value="{{ $st->value }}" {{ old('status', $item->status->value) == $st->value ? 'selected' : '' }}>{{ $st->label() }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label for="purchase_price" class="block text-xs font-semibold text-slate-700 mb-1">Harga Perolehan (Rp)</label>
                        <input type="number" step="0.01" id="purchase_price" name="purchase_price" value="{{ old('purchase_price', $item->purchase_price) }}"
                               class="w-full px-3.5 py-2 bg-white border border-slate-300 rounded-xl text-xs text-slate-900 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    </div>

                    <div>
                        <label for="purchase_date" class="block text-xs font-semibold text-slate-700 mb-1">Tanggal Perolehan</label>
                        <input type="date" id="purchase_date" name="purchase_date" value="{{ old('purchase_date', $item->purchase_date?->format('Y-m-d')) }}"
                               class="w-full px-3.5 py-2 bg-white border border-slate-300 rounded-xl text-xs text-slate-900 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    </div>
                </div>

                <!-- Section 5: Upload Foto Barang -->
                <div class="pt-4 border-t border-slate-100">
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Foto Dokumentasi Barang</label>
                    <div class="flex items-center space-x-4">
                        <div class="w-20 h-20 rounded-2xl bg-slate-100 border-2 border-dashed border-slate-300 flex items-center justify-center overflow-hidden shrink-0">
                            <template x-if="imagePreview">
                                <img :src="imagePreview" class="w-full h-full object-cover">
                            </template>
                            <template x-if="!imagePreview">
                                <svg class="w-8 h-8 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                            </template>
                        </div>
                        <div>
                            <input type="file" id="image" name="image" accept="image/*" @change="previewFile"
                                   class="text-xs text-slate-600 file:mr-3 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100 cursor-pointer">
                            <p class="text-[11px] text-slate-400 mt-1">Unggah foto baru untuk mengganti foto yang sudah ada.</p>
                        </div>
                    </div>
                </div>

                <!-- Submit Toolbar -->
                <div class="flex items-center justify-end space-x-3 pt-6 border-t border-slate-100">
                    <a href="{{ route('items.show', $item) }}" class="px-4 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold rounded-xl transition-colors">
                        Batal
                    </a>
                    <button type="submit" class="px-6 py-2.5 bg-indigo-600 hover:bg-indigo-500 text-white text-xs font-semibold rounded-xl shadow-md shadow-indigo-600/30 transition-all">
                        Perbarui Data Barang
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-admin-layout>
