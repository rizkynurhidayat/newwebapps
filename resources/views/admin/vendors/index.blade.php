<x-admin-layout>
    <x-slot name="header">Master Data Vendor & Supplier</x-slot>

    <div class="space-y-6 pt-2">
        <!-- Header & Action Toolbar -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h2 class="text-xl font-bold text-slate-900 tracking-tight">Vendor & Supplier</h2>
                <p class="text-xs text-slate-500 mt-0.5">Daftar mitra penyedia pengadaan barang dan servis perangkat perusahaan</p>
            </div>
            <a href="{{ route('vendors.create') }}" 
               class="inline-flex items-center px-4 py-2 bg-indigo-600 hover:bg-indigo-500 text-white text-xs font-semibold rounded-xl shadow-sm shadow-indigo-600/30 transition-all">
                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Tambah Vendor
            </a>
        </div>

        <!-- Search Bar -->
        <div class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-xs">
            <form method="GET" action="{{ route('vendors.index') }}" class="flex items-center gap-3">
                <div class="relative flex-1">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    </div>
                    <input type="text" name="search" value="{{ $search }}" placeholder="Cari nama vendor, kontak PIC, atau nomor telepon..." 
                           class="w-full pl-9 pr-4 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-900 focus:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                </div>
                <button type="submit" class="px-4 py-2 bg-slate-900 text-white text-xs font-semibold rounded-xl hover:bg-slate-800 transition-colors">
                    Cari
                </button>
                @if($search)
                <a href="{{ route('vendors.index') }}" class="px-3 py-2 bg-slate-100 text-slate-600 text-xs font-semibold rounded-xl hover:bg-slate-200 transition-colors">
                    Reset
                </a>
                @endif
            </form>
        </div>

        <!-- Table Data -->
        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="bg-slate-50 text-slate-500 uppercase tracking-wider font-semibold border-b border-slate-100">
                        <tr>
                            <th class="px-6 py-3.5">Nama Vendor</th>
                            <th class="px-6 py-3.5">Kontak PIC</th>
                            <th class="px-6 py-3.5">Telepon & Email</th>
                            <th class="px-6 py-3.5">Alamat</th>
                            <th class="px-6 py-3.5">Barang Terkait</th>
                            <th class="px-6 py-3.5 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($vendors as $ven)
                        <tr class="hover:bg-slate-50/70 transition-colors">
                            <td class="px-6 py-4 font-bold text-slate-900">{{ $ven->name }}</td>
                            <td class="px-6 py-4 text-slate-700 font-medium">{{ $ven->contact_name ?? '-' }}</td>
                            <td class="px-6 py-4">
                                <p class="text-slate-800 font-semibold">{{ $ven->phone ?? '-' }}</p>
                                <p class="text-[11px] text-slate-400">{{ $ven->email ?? '-' }}</p>
                            </td>
                            <td class="px-6 py-4 text-slate-500 max-w-xs truncate">{{ $ven->address ?? '-' }}</td>
                            <td class="px-6 py-4">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-slate-100 text-slate-800">
                                    {{ $ven->items_count }} item
                                </span>
                            </td>
                            <td class="px-6 py-4 text-right space-x-2">
                                <a href="{{ route('vendors.edit', $ven) }}" class="inline-flex items-center px-2.5 py-1.5 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-lg text-xs font-medium transition-colors">
                                    Edit
                                </a>
                                <form method="POST" action="{{ route('vendors.destroy', $ven) }}" class="inline-block" onsubmit="return confirm('Apakah Anda yakin ingin menghapus vendor ini?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="inline-flex items-center px-2.5 py-1.5 bg-rose-50 hover:bg-rose-100 text-rose-600 rounded-lg text-xs font-medium transition-colors">
                                        Hapus
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center text-slate-400">
                                Tidak ada data vendor yang ditemukan.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($vendors->hasPages())
            <div class="px-6 py-4 border-t border-slate-100">
                {{ $vendors->links() }}
            </div>
            @endif
        </div>
    </div>
</x-admin-layout>
