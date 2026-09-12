<x-admin-layout>
    <x-slot name="header">Tambah Kategori Baru</x-slot>

    <div class="max-w-2xl mx-auto py-6">
        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs p-6 sm:p-8">
            <div class="mb-6 pb-4 border-b border-slate-100 flex items-center justify-between">
                <div>
                    <h2 class="text-base font-bold text-slate-900">Form Tambah Kategori</h2>
                    <p class="text-xs text-slate-500">Tambahkan kelompok kategori baru untuk barang inventaris</p>
                </div>
                <a href="{{ route('categories.index') }}" class="text-xs text-slate-600 hover:text-slate-900 font-medium">&larr; Kembali</a>
            </div>

            <form method="POST" action="{{ route('categories.store') }}" class="space-y-4">
                @csrf

                <div>
                    <label for="code" class="block text-xs font-semibold text-slate-700 mb-1">Kode Kategori (Singkat & Unik)</label>
                    <input type="text" id="code" name="code" value="{{ old('code') }}" required placeholder="Contoh: ELEC, ATK, FURN"
                           class="w-full px-3.5 py-2 bg-white border border-slate-300 rounded-xl text-xs text-slate-900 uppercase focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    <p class="text-[11px] text-slate-400 mt-1">Digunakan sebagai prefix kode barang unik (misal: BRG-ELEC-0001).</p>
                </div>

                <div>
                    <label for="name" class="block text-xs font-semibold text-slate-700 mb-1">Nama Kategori</label>
                    <input type="text" id="name" name="name" value="{{ old('name') }}" required placeholder="Contoh: Elektronik & Komputer"
                           class="w-full px-3.5 py-2 bg-white border border-slate-300 rounded-xl text-xs text-slate-900 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                </div>

                <div>
                    <label for="description" class="block text-xs font-semibold text-slate-700 mb-1">Deskripsi (Opsional)</label>
                    <textarea id="description" name="description" rows="3" placeholder="Catatan singkat mengenai kategori ini..."
                              class="w-full px-3.5 py-2 bg-white border border-slate-300 rounded-xl text-xs text-slate-900 focus:outline-none focus:ring-2 focus:ring-indigo-500">{{ old('description') }}</textarea>
                </div>

                <div class="flex items-center justify-end space-x-3 pt-4 border-t border-slate-100">
                    <a href="{{ route('categories.index') }}" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold rounded-xl transition-colors">
                        Batal
                    </a>
                    <button type="submit" class="px-5 py-2 bg-indigo-600 hover:bg-indigo-500 text-white text-xs font-semibold rounded-xl shadow-sm shadow-indigo-600/30 transition-all">
                        Simpan Kategori
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-admin-layout>
