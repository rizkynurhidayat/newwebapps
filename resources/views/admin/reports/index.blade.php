<x-admin-layout>
    <x-slot name="header">Laporan & Audit Inventaris</x-slot>

    <div class="space-y-6 pt-2">
        <!-- Header & Action Toolbar -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h2 class="text-xl font-bold text-slate-900 tracking-tight">Laporan & Audit Inventaris</h2>
                <p class="text-xs text-slate-500 mt-0.5">Filter data aset, histori peminjaman, mutasi, dan rekapitulasi stok periodik</p>
            </div>
            <div class="flex items-center space-x-2">
                <button onclick="window.print()" 
                        class="inline-flex items-center px-3.5 py-2 bg-white border border-slate-200 hover:bg-slate-50 text-slate-700 text-xs font-semibold rounded-xl shadow-xs transition-colors">
                    <svg class="w-4 h-4 mr-1.5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                    Cetak
                </button>
                <a href="{{ route('reports.export', request()->query()) }}" 
                   class="inline-flex items-center px-4 py-2 bg-emerald-600 hover:bg-emerald-500 text-white text-xs font-semibold rounded-xl shadow-sm shadow-emerald-600/30 transition-all">
                    <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    Ekspor Excel / CSV
                </a>
            </div>
        </div>

        <!-- Filter Panel -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs">
            <form method="GET" action="{{ route('reports.index') }}" class="space-y-4">
                <!-- Tab Tipe Laporan -->
                <div class="flex flex-wrap gap-2 pb-3 border-b border-slate-100">
                    <label class="cursor-pointer">
                        <input type="radio" name="report_type" value="items" {{ $type === 'items' ? 'checked' : '' }} onchange="this.form.submit()" class="sr-only">
                        <span class="inline-flex items-center px-3.5 py-1.5 rounded-xl text-xs font-semibold transition-colors {{ $type === 'items' ? 'bg-indigo-600 text-white shadow-xs' : 'bg-slate-100 text-slate-700 hover:bg-slate-200' }}">
                            1. Rekap Data Barang & Aset
                        </span>
                    </label>

                    <label class="cursor-pointer">
                        <input type="radio" name="report_type" value="loans" {{ $type === 'loans' ? 'checked' : '' }} onchange="this.form.submit()" class="sr-only">
                        <span class="inline-flex items-center px-3.5 py-1.5 rounded-xl text-xs font-semibold transition-colors {{ $type === 'loans' ? 'bg-indigo-600 text-white shadow-xs' : 'bg-slate-100 text-slate-700 hover:bg-slate-200' }}">
                            2. Riwayat Peminjaman Aset
                        </span>
                    </label>

                    <label class="cursor-pointer">
                        <input type="radio" name="report_type" value="mutations" {{ $type === 'mutations' ? 'checked' : '' }} onchange="this.form.submit()" class="sr-only">
                        <span class="inline-flex items-center px-3.5 py-1.5 rounded-xl text-xs font-semibold transition-colors {{ $type === 'mutations' ? 'bg-indigo-600 text-white shadow-xs' : 'bg-slate-100 text-slate-700 hover:bg-slate-200' }}">
                            3. Riwayat Mutasi Ruangan
                        </span>
                    </label>

                    <label class="cursor-pointer">
                        <input type="radio" name="report_type" value="stock" {{ $type === 'stock' ? 'checked' : '' }} onchange="this.form.submit()" class="sr-only">
                        <span class="inline-flex items-center px-3.5 py-1.5 rounded-xl text-xs font-semibold transition-colors {{ $type === 'stock' ? 'bg-indigo-600 text-white shadow-xs' : 'bg-slate-100 text-slate-700 hover:bg-slate-200' }}">
                            4. Log Aktivitas Stok
                        </span>
                    </label>
                </div>

                <!-- Baris Filter Tanggal & Kategori -->
                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-4">
                    @if(in_array($type, ['loans', 'mutations', 'stock']))
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Dari Tanggal</label>
                        <input type="date" name="start_date" value="{{ $startDate }}" 
                               class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-800 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Sampai Tanggal</label>
                        <input type="date" name="end_date" value="{{ $endDate }}" 
                               class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-800 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    </div>
                    @endif

                    @if($type === 'items')
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Kategori</label>
                        <select name="category_id" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-800 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                            <option value="">Semua Kategori</option>
                            @foreach($categories as $cat)
                            <option value="{{ $cat->id }}" {{ $categoryId == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Lokasi Ruangan</label>
                        <select name="location_id" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-800 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                            <option value="">Semua Lokasi</option>
                            @foreach($locations as $loc)
                            <option value="{{ $loc->id }}" {{ $locationId == $loc->id ? 'selected' : '' }}>{{ $loc->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    @endif

                    <div class="flex items-end">
                        <button type="submit" class="w-full py-2 px-4 bg-slate-900 hover:bg-slate-800 text-white text-xs font-semibold rounded-xl transition-colors">
                            Tampilkan Data
                        </button>
                    </div>
                </div>
            </form>
        </div>

        <!-- Tabel Hasil Laporan -->
        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
            <!-- 1. Laporan Data Barang -->
            @if($type === 'items')
            <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between bg-slate-50/50">
                <span class="text-xs font-bold text-slate-800">Total Ditemukan: {{ $items->count() }} Data Barang</span>
                <span class="text-xs font-bold text-indigo-600">Total Valuasi: Rp {{ number_format($items->sum('purchase_price'), 0, ',', '.') }}</span>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="bg-slate-50 text-slate-500 uppercase tracking-wider font-semibold border-b border-slate-100">
                        <tr>
                            <th class="px-6 py-3.5">Kode</th>
                            <th class="px-6 py-3.5">Nama Barang</th>
                            <th class="px-6 py-3.5">Kategori</th>
                            <th class="px-6 py-3.5">Lokasi</th>
                            <th class="px-6 py-3.5">Stok</th>
                            <th class="px-6 py-3.5">Kondisi</th>
                            <th class="px-6 py-3.5">Status</th>
                            <th class="px-6 py-3.5 text-right">Harga Perolehan</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($items as $item)
                        <tr>
                            <td class="px-6 py-3.5 font-mono font-bold text-slate-900">{{ $item->code }}</td>
                            <td class="px-6 py-3.5 font-semibold text-slate-800">{{ $item->name }}</td>
                            <td class="px-6 py-3.5 text-slate-600">{{ $item->category->name }}</td>
                            <td class="px-6 py-3.5 text-slate-600">{{ $item->location->name }}</td>
                            <td class="px-6 py-3.5 font-bold">{{ $item->stock }} {{ $item->unit }}</td>
                            <td class="px-6 py-3.5">
                                <span class="inline-flex px-2 py-0.5 rounded-full text-[10px] font-semibold {{ $item->condition->badgeColor() }}">
                                    {{ $item->condition->label() }}
                                </span>
                            </td>
                            <td class="px-6 py-3.5">
                                <span class="inline-flex px-2.5 py-0.5 rounded-full text-[10px] font-semibold {{ $item->status->badgeColor() }}">
                                    {{ $item->status->label() }}
                                </span>
                            </td>
                            <td class="px-6 py-3.5 text-right font-mono font-semibold text-slate-900">{{ $item->formattedPrice() }}</td>
                        </tr>
                        @empty
                        <tr><td colspan="8" class="px-6 py-12 text-center text-slate-400">Tidak ada data barang yang ditemukan.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @endif

            <!-- 2. Laporan Peminjaman -->
            @if($type === 'loans')
            <div class="px-6 py-4 border-b border-slate-100 bg-slate-50/50">
                <span class="text-xs font-bold text-slate-800">Total Transaksi: {{ $loans->count() }} Peminjaman (Periode {{ date('d/m/Y', strtotime($startDate)) }} s/d {{ date('d/m/Y', strtotime($endDate)) }})</span>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="bg-slate-50 text-slate-500 uppercase tracking-wider font-semibold border-b border-slate-100">
                        <tr>
                            <th class="px-6 py-3.5">Kode</th>
                            <th class="px-6 py-3.5">Nama Barang</th>
                            <th class="px-6 py-3.5">Peminjam</th>
                            <th class="px-6 py-3.5">Tgl Pinjam</th>
                            <th class="px-6 py-3.5">Batas Kembali</th>
                            <th class="px-6 py-3.5">Tgl Kembali</th>
                            <th class="px-6 py-3.5">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($loans as $loan)
                        <tr>
                            <td class="px-6 py-3.5 font-mono font-bold text-slate-900">{{ $loan->loan_code }}</td>
                            <td class="px-6 py-3.5 font-semibold text-slate-800">{{ $loan->item->name }}</td>
                            <td class="px-6 py-3.5 text-slate-700">{{ $loan->borrower->name }}</td>
                            <td class="px-6 py-3.5 text-slate-600">{{ $loan->loan_date->format('d/m/Y') }}</td>
                            <td class="px-6 py-3.5 text-slate-600">{{ $loan->due_date->format('d/m/Y') }}</td>
                            <td class="px-6 py-3.5 text-slate-600">{{ $loan->return_date?->format('d/m/Y') ?? '-' }}</td>
                            <td class="px-6 py-3.5">
                                <span class="inline-flex px-2.5 py-0.5 rounded-full text-[10px] font-semibold {{ $loan->status->badgeColor() }}">
                                    {{ $loan->status->label() }}
                                </span>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="7" class="px-6 py-12 text-center text-slate-400">Tidak ada data transaksi peminjaman pada periode ini.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @endif

            <!-- 3. Laporan Mutasi -->
            @if($type === 'mutations')
            <div class="px-6 py-4 border-b border-slate-100 bg-slate-50/50">
                <span class="text-xs font-bold text-slate-800">Total Transaksi: {{ $mutations->count() }} Mutasi Ruangan</span>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="bg-slate-50 text-slate-500 uppercase tracking-wider font-semibold border-b border-slate-100">
                        <tr>
                            <th class="px-6 py-3.5">Kode</th>
                            <th class="px-6 py-3.5">Nama Barang</th>
                            <th class="px-6 py-3.5">Dari Ruangan</th>
                            <th class="px-6 py-3.5">Ke Ruangan</th>
                            <th class="px-6 py-3.5">Jumlah</th>
                            <th class="px-6 py-3.5">Tanggal</th>
                            <th class="px-6 py-3.5">Penanggung Jawab</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($mutations as $mut)
                        <tr>
                            <td class="px-6 py-3.5 font-mono font-bold text-slate-900">{{ $mut->mutation_code }}</td>
                            <td class="px-6 py-3.5 font-semibold text-slate-800">{{ $mut->item->name }}</td>
                            <td class="px-6 py-3.5 text-slate-600">{{ $mut->fromLocation->name }}</td>
                            <td class="px-6 py-3.5 font-semibold text-indigo-600">{{ $mut->toLocation->name }}</td>
                            <td class="px-6 py-3.5">{{ $mut->quantity }} {{ $mut->item->unit }}</td>
                            <td class="px-6 py-3.5 text-slate-600">{{ $mut->mutation_date->format('d/m/Y') }}</td>
                            <td class="px-6 py-3.5 text-slate-700 font-medium">{{ $mut->mover->name }}</td>
                        </tr>
                        @empty
                        <tr><td colspan="7" class="px-6 py-12 text-center text-slate-400">Tidak ada riwayat mutasi pada rentang tanggal ini.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @endif

            <!-- 4. Laporan Stok -->
            @if($type === 'stock')
            <div class="px-6 py-4 border-b border-slate-100 bg-slate-50/50">
                <span class="text-xs font-bold text-slate-800">Total Log Aktivitas: {{ $stockLogs->count() }} Transaksi</span>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="bg-slate-50 text-slate-500 uppercase tracking-wider font-semibold border-b border-slate-100">
                        <tr>
                            <th class="px-6 py-3.5">Tipe</th>
                            <th class="px-6 py-3.5">Nama Barang</th>
                            <th class="px-6 py-3.5">Perubahan</th>
                            <th class="px-6 py-3.5">Sebelum</th>
                            <th class="px-6 py-3.5">Sesudah</th>
                            <th class="px-6 py-3.5">Waktu</th>
                            <th class="px-6 py-3.5">Petugas</th>
                            <th class="px-6 py-3.5">Catatan</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($stockLogs as $log)
                        <tr>
                            <td class="px-6 py-3.5">
                                <span class="inline-flex px-2 py-0.5 rounded text-[10px] font-bold uppercase {{ $log->type->badgeColor() }}">
                                    {{ $log->type->label() }}
                                </span>
                            </td>
                            <td class="px-6 py-3.5 font-semibold text-slate-800">{{ $log->item->name }}</td>
                            <td class="px-6 py-3.5 font-bold">{{ $log->quantity }} {{ $log->item->unit }}</td>
                            <td class="px-6 py-3.5 text-slate-500">{{ $log->before_stock }}</td>
                            <td class="px-6 py-3.5 font-bold text-slate-800">{{ $log->after_stock }}</td>
                            <td class="px-6 py-3.5 text-slate-600">{{ $log->created_at->format('d/m/Y H:i') }}</td>
                            <td class="px-6 py-3.5 text-slate-700 font-medium">{{ $log->creator->name ?? '-' }}</td>
                            <td class="px-6 py-3.5 text-slate-500 max-w-xs truncate">{{ $log->notes ?? '-' }}</td>
                        </tr>
                        @empty
                        <tr><td colspan="8" class="px-6 py-12 text-center text-slate-400">Tidak ada aktivitas stok pada rentang waktu ini.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @endif
        </div>
    </div>
</x-admin-layout>
