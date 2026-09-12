<x-admin-layout>
    <x-slot name="header">Buat Permohonan Peminjaman Aset</x-slot>

    <div class="max-w-3xl mx-auto py-6">
        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs p-6 sm:p-8">
            <div class="mb-6 pb-4 border-b border-slate-100 flex items-center justify-between">
                <div>
                    <h2 class="text-base font-bold text-slate-900">Formulir Peminjaman Barang</h2>
                    <p class="text-xs text-slate-500">Pilih barang yang berstatus tersedia untuk kebutuhan operasional</p>
                </div>
                <a href="{{ route('loans.index') }}" class="text-xs text-slate-600 hover:text-slate-900 font-medium">&larr; Kembali</a>
            </div>

            <form method="POST" action="{{ route('loans.store') }}" class="space-y-5">
                @csrf

                <div>
                    <label for="item_id" class="block text-xs font-semibold text-slate-700 mb-1">Pilih Barang yang Ingin Dipinjam <span class="text-rose-500">*</span></label>
                    <select id="item_id" name="item_id" required
                            class="w-full px-3.5 py-2.5 bg-white border border-slate-300 rounded-xl text-xs text-slate-900 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        <option value="">-- Pilih Barang Tersedia --</option>
                        @foreach($availableItems as $itm)
                        <option value="{{ $itm->id }}" {{ old('item_id') == $itm->id ? 'selected' : '' }}>
                            {{ $itm->code }} - {{ $itm->name }} (Lokasi: {{ $itm->location->name }}, Stok: {{ $itm->stock }} {{ $itm->unit }})
                        </option>
                        @endforeach
                    </select>
                </div>

                @if(auth()->user()->canManageInventory())
                <div>
                    <label for="user_id" class="block text-xs font-semibold text-slate-700 mb-1">Nama Karyawan Peminjam <span class="text-rose-500">*</span></label>
                    <select id="user_id" name="user_id" required
                            class="w-full px-3.5 py-2.5 bg-white border border-slate-300 rounded-xl text-xs text-slate-900 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        @foreach($users as $usr)
                        <option value="{{ $usr->id }}" {{ (old('user_id') ?? auth()->id()) == $usr->id ? 'selected' : '' }}>
                            {{ $usr->name }} ({{ $usr->department ?? 'Umum' }} - {{ $usr->email }})
                        </option>
                        @endforeach
                    </select>
                </div>
                @else
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Peminjam</label>
                    <input type="text" value="{{ auth()->user()->name }} ({{ auth()->user()->email }})" disabled
                           class="w-full px-3.5 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-600 cursor-not-allowed">
                </div>
                @endif

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div>
                        <label for="quantity" class="block text-xs font-semibold text-slate-700 mb-1">Jumlah Unit <span class="text-rose-500">*</span></label>
                        <input type="number" id="quantity" name="quantity" value="{{ old('quantity', 1) }}" min="1" required
                               class="w-full px-3.5 py-2 bg-white border border-slate-300 rounded-xl text-xs text-slate-900 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    </div>

                    <div>
                        <label for="loan_date" class="block text-xs font-semibold text-slate-700 mb-1">Tanggal Pinjam <span class="text-rose-500">*</span></label>
                        <input type="date" id="loan_date" name="loan_date" value="{{ old('loan_date', date('Y-m-d')) }}" required
                               class="w-full px-3.5 py-2 bg-white border border-slate-300 rounded-xl text-xs text-slate-900 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    </div>

                    <div>
                        <label for="due_date" class="block text-xs font-semibold text-slate-700 mb-1">Estimasi Pengembalian <span class="text-rose-500">*</span></label>
                        <input type="date" id="due_date" name="due_date" value="{{ old('due_date', date('Y-m-d', strtotime('+3 days'))) }}" required
                               class="w-full px-3.5 py-2 bg-white border border-slate-300 rounded-xl text-xs text-slate-900 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    </div>
                </div>

                <div>
                    <label for="notes" class="block text-xs font-semibold text-slate-700 mb-1">Keperluan / Alasan Peminjaman</label>
                    <textarea id="notes" name="notes" rows="3" placeholder="Contoh: Digunakan untuk presentasi project di kantor klien PT ABC..."
                              class="w-full px-3.5 py-2 bg-white border border-slate-300 rounded-xl text-xs text-slate-900 focus:outline-none focus:ring-2 focus:ring-indigo-500">{{ old('notes') }}</textarea>
                </div>

                <div class="flex items-center justify-end space-x-3 pt-4 border-t border-slate-100">
                    <a href="{{ route('loans.index') }}" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold rounded-xl transition-colors">
                        Batal
                    </a>
                    <button type="submit" class="px-6 py-2 bg-indigo-600 hover:bg-indigo-500 text-white text-xs font-semibold rounded-xl shadow-sm shadow-indigo-600/30 transition-all">
                        Ajukan Peminjaman
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-admin-layout>
