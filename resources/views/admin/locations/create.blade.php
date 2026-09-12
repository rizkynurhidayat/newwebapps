<x-admin-layout>
    <x-slot name="header">Tambah Lokasi / Ruangan Baru</x-slot>

    <div class="max-w-2xl mx-auto py-6">
        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs p-6 sm:p-8">
            <div class="mb-6 pb-4 border-b border-slate-100 flex items-center justify-between">
                <div>
                    <h2 class="text-base font-bold text-slate-900">Form Tambah Lokasi</h2>
                    <p class="text-xs text-slate-500">Tambahkan ruangan atau gudang baru untuk penempatan aset</p>
                </div>
                <a href="{{ route('locations.index') }}" class="text-xs text-slate-600 hover:text-slate-900 font-medium">&larr; Kembali</a>
            </div>

            <form method="POST" action="{{ route('locations.store') }}" class="space-y-4">
                @csrf

                <div>
                    <label for="code" class="block text-xs font-semibold text-slate-700 mb-1">Kode Ruangan / Lokasi (Unik)</label>
                    <input type="text" id="code" name="code" value="{{ old('code') }}" required placeholder="Contoh: GDG-01, IT-SRV, OPS-02"
                           class="w-full px-3.5 py-2 bg-white border border-slate-300 rounded-xl text-xs text-slate-900 uppercase focus:outline-none focus:ring-2 focus:ring-indigo-500">
                </div>

                <div>
                    <label for="name" class="block text-xs font-semibold text-slate-700 mb-1">Nama Lokasi / Ruangan</label>
                    <input type="text" id="name" name="name" value="{{ old('name') }}" required placeholder="Contoh: Gudang Logistik Utama Lt. 1"
                           class="w-full px-3.5 py-2 bg-white border border-slate-300 rounded-xl text-xs text-slate-900 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                </div>

                <div>
                    <label for="pic_name" class="block text-xs font-semibold text-slate-700 mb-1">Penanggung Jawab (PIC Ruangan)</label>
                    <input type="text" id="pic_name" name="pic_name" value="{{ old('pic_name') }}" placeholder="Nama staf PIC..."
                           class="w-full px-3.5 py-2 bg-white border border-slate-300 rounded-xl text-xs text-slate-900 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                </div>

                <div>
                    <label for="description" class="block text-xs font-semibold text-slate-700 mb-1">Keterangan Tambahan (Opsional)</label>
                    <textarea id="description" name="description" rows="3" placeholder="Informasi detail mengenai posisi ruangan/kunci..."
                              class="w-full px-3.5 py-2 bg-white border border-slate-300 rounded-xl text-xs text-slate-900 focus:outline-none focus:ring-2 focus:ring-indigo-500">{{ old('description') }}</textarea>
                </div>

                <div class="flex items-center justify-end space-x-3 pt-4 border-t border-slate-100">
                    <a href="{{ route('locations.index') }}" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold rounded-xl transition-colors">
                        Batal
                    </a>
                    <button type="submit" class="px-5 py-2 bg-indigo-600 hover:bg-indigo-500 text-white text-xs font-semibold rounded-xl shadow-sm shadow-indigo-600/30 transition-all">
                        Simpan Lokasi
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-admin-layout>
