@extends('layouts.admin')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <div class="flex items-center space-x-2">
                <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold bg-purple-100 text-purple-800">
                    Otorisasi & Sistem
                </span>
                <span class="text-xs text-slate-400">&bull;</span>
                <span class="text-xs text-slate-500 font-medium">Khusus Super Administrator</span>
            </div>
            <h1 class="text-2xl font-bold tracking-tight text-slate-900 mt-1">Manajemen Pengguna & Hak Akses</h1>
            <p class="text-sm text-slate-500 mt-0.5">Kelola akun karyawan pabrik, peran operasional (Super Admin, QC Inspector, Supervisor), dan status otorisasi akun.</p>
        </div>
        <a href="{{ route('admin.users.create') }}" class="inline-flex items-center px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-semibold rounded-lg shadow-sm shadow-emerald-500/20 transition-colors">
            <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            + Tambah Pengguna Baru
        </a>
    </div>

    <!-- Summary KPI Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-sm">
            <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Total Akun Terdaftar</span>
            <div class="text-2xl font-extrabold text-slate-900 font-mono mt-1">{{ $summary['total'] }}</div>
            <span class="text-[11px] text-slate-500">Karyawan & Manajemen</span>
        </div>

        <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-sm">
            <span class="text-xs font-bold uppercase tracking-wider text-purple-600">Super Administrator</span>
            <div class="text-2xl font-extrabold text-purple-700 font-mono mt-1">{{ $summary['admins'] }}</div>
            <span class="text-[11px] text-slate-500">Plant Manager (Akses Penuh)</span>
        </div>

        <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-sm">
            <span class="text-xs font-bold uppercase tracking-wider text-emerald-600">QC Inspector (Staff QA)</span>
            <div class="text-2xl font-extrabold text-emerald-700 font-mono mt-1">{{ $summary['staff_qc'] }}</div>
            <span class="text-[11px] text-slate-500">Pemeriksaan & Uji Six Sigma</span>
        </div>

        <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-sm">
            <span class="text-xs font-bold uppercase tracking-wider text-blue-600">Production Supervisor</span>
            <div class="text-2xl font-extrabold text-blue-700 font-mono mt-1">{{ $summary['supervisors'] }}</div>
            <span class="text-[11px] text-slate-500">Lot Batch, CAPA, & K3 Mesin</span>
        </div>
    </div>

    <!-- Filter & Search Bar -->
    <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-sm">
        <form method="GET" action="{{ route('admin.users.index') }}" class="grid grid-cols-1 sm:grid-cols-3 gap-3">
            <div>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama, email, departemen..." class="w-full text-xs rounded-lg border-slate-200 focus:border-emerald-500 focus:ring-emerald-500">
            </div>

            <div>
                <select name="role" class="w-full text-xs rounded-lg border-slate-200 focus:border-emerald-500 focus:ring-emerald-500">
                    <option value="">-- Semua Peran (Role) --</option>
                    <option value="admin" {{ request('role') === 'admin' ? 'selected' : '' }}>Plant Manager / Super Admin</option>
                    <option value="staff" {{ request('role') === 'staff' ? 'selected' : '' }}>Quality Control (QC) Inspector</option>
                    <option value="employee" {{ request('role') === 'employee' ? 'selected' : '' }}>Production Supervisor / Staff</option>
                </select>
            </div>

            <div class="flex items-center space-x-2">
                <button type="submit" class="w-full py-2 bg-slate-900 hover:bg-slate-800 text-white text-xs font-semibold rounded-lg transition-colors">
                    Filter Pengguna
                </button>
                @if(request()->anyFilled(['search', 'role']))
                    <a href="{{ route('admin.users.index') }}" class="px-3 py-2 text-xs text-slate-500 hover:text-slate-700">Reset</a>
                @endif
            </div>
        </form>
    </div>

    <!-- Users Table -->
    <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-xs">
                <thead>
                    <tr class="bg-slate-50 text-[11px] font-semibold uppercase text-slate-500 border-b border-slate-200">
                        <th class="py-3 px-4">Pengguna</th>
                        <th class="py-3 px-4">Peran (Role)</th>
                        <th class="py-3 px-4">Departemen / Divisi</th>
                        <th class="py-3 px-4">Kontak Telepon</th>
                        <th class="py-3 px-4 text-center">Status Akun</th>
                        <th class="py-3 px-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($users as $user)
                        <tr class="hover:bg-slate-50/80 transition-colors {{ $user->id === auth()->id() ? 'bg-purple-50/20' : '' }}">
                            <td class="py-3 px-4">
                                <div class="flex items-center space-x-3">
                                    <div class="w-9 h-9 rounded-full bg-slate-800 text-emerald-400 flex items-center justify-center font-bold text-xs font-mono">
                                        {{ strtoupper(substr($user->name, 0, 2)) }}
                                    </div>
                                    <div>
                                        <div class="font-bold text-slate-900 flex items-center space-x-1.5">
                                            <span>{{ $user->name }}</span>
                                            @if($user->id === auth()->id())
                                                <span class="px-1.5 py-0.2 rounded text-[9px] font-bold bg-purple-100 text-purple-800">Anda</span>
                                            @endif
                                        </div>
                                        <div class="text-[11px] text-slate-400 font-mono">{{ $user->email }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="py-3 px-4">
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[11px] font-medium {{ $user->role->badgeColor() }}">
                                    {{ $user->role->label() }}
                                </span>
                            </td>
                            <td class="py-3 px-4 text-slate-700">
                                {{ $user->department ?? 'Umum' }}
                            </td>
                            <td class="py-3 px-4 text-slate-600 font-mono">
                                {{ $user->phone ?? '-' }}
                            </td>
                            <td class="py-3 px-4 text-center">
                                @if($user->id === auth()->id())
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-emerald-100 text-emerald-800">
                                        Aktif (Sesi Ini)
                                    </span>
                                @else
                                    <form method="POST" action="{{ route('admin.users.toggle-status', $user) }}" class="inline">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold transition-colors {{ $user->is_active ? 'bg-emerald-100 text-emerald-800 hover:bg-rose-100 hover:text-rose-800' : 'bg-slate-100 text-slate-600 hover:bg-emerald-100 hover:text-emerald-800' }}" title="Klik untuk mengubah status">
                                            {{ $user->is_active ? 'Aktif' : 'Nonaktif' }}
                                        </button>
                                    </form>
                                @endif
                            </td>
                            <td class="py-3 px-4 text-right space-x-2 whitespace-nowrap">
                                <a href="{{ route('admin.users.edit', $user) }}" class="text-xs font-semibold text-emerald-600 hover:text-emerald-700">
                                    Edit
                                </a>

                                @if($user->id !== auth()->id())
                                    <form method="POST" action="{{ route('admin.users.destroy', $user) }}" class="inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus akun [{{ $user->name }}]?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-xs font-semibold text-rose-600 hover:text-rose-700">
                                            Hapus
                                        </button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-8 text-center text-slate-400">Tidak ada data pengguna yang sesuai.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($users->hasPages())
            <div class="p-4 border-t border-slate-200">
                {{ $users->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
