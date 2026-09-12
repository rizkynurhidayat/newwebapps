<x-admin-layout>
    <x-slot name="header">Tambah Vendor / Supplier Baru</x-slot>

    <div class="max-w-2xl mx-auto py-6">
        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs p-6 sm:p-8">
            <div class="mb-6 pb-4 border-b border-slate-100 flex items-center justify-between">
                <div>
                    <h2 class="text-base font-bold text-slate-900">Form Tambah Vendor</h2>
                    <p class="text-xs text-slate-500">Tambahkan vendor penyedia barang atau mitra servis baru</p>
                </div>
                <a href="{{ route('vendors.index') }}" class="text-xs text-slate-600 hover:text-slate-900 font-medium">&larr; Kembali</a>
            </div>

            <form method="POST" action="{{ route('vendors.store') }}" class="space-y-4">
                @csrf

                <div>
                    <label for="name" class="block text-xs font-semibold text-slate-700 mb-1">Nama Perusahaan / Toko Vendor</label>
                    <input type="text" id="name" name="name" value="{{ old('name') }}" required placeholder="Contoh: PT Sumber Komputer Indonesia"
                           class="w-full px-3.5 py-2 bg-white border border-slate-300 rounded-xl text-xs text-slate-900 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="contact_name" class="block text-xs font-semibold text-slate-700 mb-1">Nama Kontak Person (PIC)</label>
                        <input type="text" id="contact_name" name="contact_name" value="{{ old('contact_name') }}" placeholder="Contoh: Bpk. Budi Santoso"
                               class="w-full px-3.5 py-2 bg-white border border-slate-300 rounded-xl text-xs text-slate-900 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    </div>

                    <div>
                        <label for="phone" class="block text-xs font-semibold text-slate-700 mb-1">Nomor Telepon / WhatsApp</label>
                        <input type="text" id="phone" name="phone" value="{{ old('phone') }}" placeholder="Contoh: 021-5551234 / 0812xxx"
                               class="w-full px-3.5 py-2 bg-white border border-slate-300 rounded-xl text-xs text-slate-900 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    </div>
                </div>

                <div>
                    <label for="email" class="block text-xs font-semibold text-slate-700 mb-1">Alamat Email Vendor</label>
                    <input type="email" id="email" name="email" value="{{ old('email') }}" placeholder="sales@vendor.com"
                           class="w-full px-3.5 py-2 bg-white border border-slate-300 rounded-xl text-xs text-slate-900 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                </div>

                <div>
                    <label for="address" class="block text-xs font-semibold text-slate-700 mb-1">Alamat Kantor / Toko</label>
                    <textarea id="address" name="address" rows="3" placeholder="Alamat lengkap vendor..."
                              class="w-full px-3.5 py-2 bg-white border border-slate-300 rounded-xl text-xs text-slate-900 focus:outline-none focus:ring-2 focus:ring-indigo-500">{{ old('address') }}</textarea>
                </div>

                <div class="flex items-center justify-end space-x-3 pt-4 border-t border-slate-100">
                    <a href="{{ route('vendors.index') }}" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold rounded-xl transition-colors">
                        Batal
                    </a>
                    <button type="submit" class="px-5 py-2 bg-indigo-600 hover:bg-indigo-500 text-white text-xs font-semibold rounded-xl shadow-sm shadow-indigo-600/30 transition-all">
                        Simpan Vendor
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-admin-layout>
