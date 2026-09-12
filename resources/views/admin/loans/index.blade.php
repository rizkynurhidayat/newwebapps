<x-admin-layout>
    <x-slot name="header">Daftar Transaksi Peminjaman Aset</x-slot>

    <div class="space-y-6 pt-2" x-data="{ 
        returnModal: false, 
        selectedLoanId: null, 
        selectedLoanCode: '', 
        selectedItemName: '',
        openReturnModal(id, code, name) {
            this.selectedLoanId = id;
            this.selectedLoanCode = code;
            this.selectedItemName = name;
            this.returnModal = true;
        }
    }">
        <!-- Header & Action Toolbar -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h2 class="text-xl font-bold text-slate-900 tracking-tight">Peminjaman & Pengembalian Aset</h2>
                <p class="text-xs text-slate-500 mt-0.5">Kelola alur permohonan, persetujuan, dan pengembalian barang operasional</p>
            </div>
            <a href="{{ route('loans.create') }}" 
               class="inline-flex items-center px-4 py-2 bg-indigo-600 hover:bg-indigo-500 text-white text-xs font-semibold rounded-xl shadow-sm shadow-indigo-600/30 transition-all">
                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Buat Permohonan Pinjam
            </a>
        </div>

        <!-- Filter & Search Toolbar -->
        <div class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-xs">
            <form method="GET" action="{{ route('loans.index') }}" class="flex flex-col sm:flex-row items-center gap-3">
                <div class="relative flex-1 w-full">
                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    </div>
                    <input type="text" name="search" value="{{ $search }}" placeholder="Cari kode peminjaman, nama barang, atau peminjam..." 
                           class="w-full pl-10 pr-4 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-900 focus:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                </div>

                <div class="flex items-center space-x-2 w-full sm:w-auto">
                    <select name="status" class="px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-800 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        <option value="">Semua Status</option>
                        @foreach($statuses as $st)
                        <option value="{{ $st->value }}" {{ $status == $st->value ? 'selected' : '' }}>{{ $st->label() }}</option>
                        @endforeach
                    </select>
                    <button type="submit" class="px-4 py-2 bg-slate-900 text-white text-xs font-semibold rounded-xl hover:bg-slate-800 transition-colors">
                        Filter
                    </button>
                    @if($status || $search)
                    <a href="{{ route('loans.index') }}" class="px-3 py-2 bg-slate-100 text-slate-600 text-xs font-semibold rounded-xl hover:bg-slate-200 transition-colors">
                        Reset
                    </a>
                    @endif
                </div>
            </form>
        </div>

        <!-- Table Loans -->
        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="bg-slate-50 text-slate-500 uppercase tracking-wider font-semibold border-b border-slate-100">
                        <tr>
                            <th class="px-6 py-3.5">Kode Transaksi</th>
                            <th class="px-6 py-3.5">Nama Barang</th>
                            <th class="px-6 py-3.5">Peminjam</th>
                            <th class="px-6 py-3.5">Tgl Pinjam</th>
                            <th class="px-6 py-3.5">Batas Kembali</th>
                            <th class="px-6 py-3.5">Status</th>
                            <th class="px-6 py-3.5 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($loans as $loan)
                        <tr class="hover:bg-slate-50/70 transition-colors">
                            <td class="px-6 py-4 font-mono font-bold text-slate-900">{{ $loan->loan_code }}</td>
                            <td class="px-6 py-4">
                                <a href="{{ route('items.show', $loan->item) }}" class="font-bold text-indigo-600 hover:text-indigo-800 block">
                                    {{ $loan->item->name }}
                                </a>
                                <span class="text-[11px] text-slate-500">{{ $loan->quantity }} {{ $loan->item->unit }}</span>
                            </td>
                            <td class="px-6 py-4">
                                <p class="font-semibold text-slate-800">{{ $loan->borrower->name }}</p>
                                <p class="text-[11px] text-slate-400">{{ $loan->borrower->department ?? '-' }}</p>
                            </td>
                            <td class="px-6 py-4 text-slate-600">{{ $loan->loan_date->format('d/m/Y') }}</td>
                            <td class="px-6 py-4">
                                <span class="text-slate-800 font-semibold {{ ($loan->status === \App\Enums\LoanStatus::Dipinjam && now()->gt($loan->due_date)) ? 'text-rose-600 font-bold' : '' }}">
                                    {{ $loan->due_date->format('d/m/Y') }}
                                </span>
                                @if($loan->status === \App\Enums\LoanStatus::Dipinjam && now()->gt($loan->due_date))
                                <span class="block text-[10px] text-rose-600 font-bold">Terlambat!</span>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-semibold {{ $loan->status->badgeColor() }}">
                                    {{ $loan->status->label() }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-right space-x-1.5 whitespace-nowrap">
                                @if(auth()->user()->canManageInventory())
                                    @if($loan->status === \App\Enums\LoanStatus::Diajukan)
                                    <!-- Tombol Setujui -->
                                    <form method="POST" action="{{ route('loans.approve', $loan) }}" class="inline-block" onsubmit="return confirm('Setujui peminjaman barang ini?');">
                                        @csrf
                                        <button type="submit" class="inline-flex items-center px-2.5 py-1.5 bg-emerald-50 hover:bg-emerald-100 text-emerald-700 rounded-lg text-xs font-medium transition-colors">
                                            Setujui
                                        </button>
                                    </form>
                                    <!-- Tombol Tolak -->
                                    <form method="POST" action="{{ route('loans.reject', $loan) }}" class="inline-block" onsubmit="return confirm('Tolak permohonan peminjaman ini?');">
                                        @csrf
                                        <button type="submit" class="inline-flex items-center px-2.5 py-1.5 bg-rose-50 hover:bg-rose-100 text-rose-700 rounded-lg text-xs font-medium transition-colors">
                                            Tolak
                                        </button>
                                    </form>
                                    @elseif($loan->status === \App\Enums\LoanStatus::Dipinjam)
                                    <!-- Tombol Proses Pengembalian -->
                                    <button type="button" 
                                            @click="openReturnModal({{ $loan->id }}, '{{ $loan->loan_code }}', '{{ addslashes($loan->item->name) }}')"
                                            class="inline-flex items-center px-3 py-1.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg text-xs font-semibold shadow-xs transition-colors">
                                        Kembalikan
                                    </button>
                                    @endif
                                @endif

                                @if($loan->status === \App\Enums\LoanStatus::Kembali)
                                <span class="text-[11px] text-slate-400 italic">Dikembalikan {{ $loan->return_date?->format('d/m/Y') }}</span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center text-slate-400">
                                Tidak ada data transaksi peminjaman.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($loans->hasPages())
            <div class="px-6 py-4 border-t border-slate-100">
                {{ $loans->links() }}
            </div>
            @endif
        </div>

        <!-- Modal Verifikasi Pengembalian Barang (Alpine.js) -->
        <div x-show="returnModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs" style="display: none;">
            <div class="bg-white rounded-2xl border border-slate-200 shadow-2xl max-w-md w-full p-6 sm:p-7" @click.away="returnModal = false">
                <div class="flex items-center justify-between pb-3 border-b border-slate-100 mb-4">
                    <h3 class="text-base font-bold text-slate-900">Verifikasi Pengembalian Barang</h3>
                    <button type="button" @click="returnModal = false" class="text-slate-400 hover:text-slate-600">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                <form method="POST" :action="`/loans/${selectedLoanId}/return`" class="space-y-4">
                    @csrf
                    <div>
                        <p class="text-xs text-slate-500">Kode Peminjaman:</p>
                        <p class="text-sm font-mono font-bold text-slate-900" x-text="selectedLoanCode"></p>
                        <p class="text-xs text-indigo-600 font-semibold mt-0.5" x-text="selectedItemName"></p>
                    </div>

                    <div>
                        <label for="return_condition" class="block text-xs font-semibold text-slate-700 mb-1">Kondisi Barang Saat Dikembalikan <span class="text-rose-500">*</span></label>
                        <select id="return_condition" name="return_condition" required
                                class="w-full px-3.5 py-2 bg-white border border-slate-300 rounded-xl text-xs text-slate-900 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                            @foreach($conditions as $cond)
                            <option value="{{ $cond->value }}">{{ $cond->label() }}</option>
                            @endforeach
                        </select>
                        <p class="text-[11px] text-slate-400 mt-1">Jika kondisi rusak, status barang otomatis beralih ke "Dalam Perbaikan".</p>
                    </div>

                    <div>
                        <label for="return_notes" class="block text-xs font-semibold text-slate-700 mb-1">Catatan Pengembalian / Pengecekan</label>
                        <textarea id="return_notes" name="return_notes" rows="3" placeholder="Kelengkapan aksesoris, catatan fisik, dll..."
                                  class="w-full px-3.5 py-2 bg-white border border-slate-300 rounded-xl text-xs text-slate-900 focus:outline-none focus:ring-2 focus:ring-indigo-500"></textarea>
                    </div>

                    <div class="flex items-center justify-end space-x-2.5 pt-4 border-t border-slate-100">
                        <button type="button" @click="returnModal = false" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold rounded-xl">
                            Batal
                        </button>
                        <button type="submit" class="px-5 py-2 bg-indigo-600 hover:bg-indigo-500 text-white text-xs font-semibold rounded-xl shadow-sm shadow-indigo-600/30">
                            Konfirmasi Pengembalian
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-admin-layout>
