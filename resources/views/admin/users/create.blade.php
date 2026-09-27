@extends('layouts.admin')

@section('content')
<div class="max-w-2xl mx-auto space-y-6">
    <div>
        <div class="flex items-center space-x-2 text-xs text-slate-500 mb-1">
            <a href="{{ route('admin.users.index') }}" class="hover:text-slate-700">Manajemen Pengguna</a>
            <span>/</span>
            <span class="text-slate-800 font-medium">Tambah Akun Baru</span>
        </div>
        <h1 class="text-2xl font-bold tracking-tight text-slate-900">Pendaftaran Pengguna Baru</h1>
        <p class="text-sm text-slate-500">Tambahkan akun karyawan, atur penugasan divisi, dan tentukan peran wewenang (Role) sistem.</p>
    </div>

    <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-6">
        <form method="POST" action="{{ route('admin.users.store') }}" class="space-y-4">
            @csrf

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Nama Lengkap Karyawan <span class="text-rose-500">*</span></label>
                    <input type="text" name="name" value="{{ old('name') }}" required placeholder="Contoh: Ahmad Fauzi" 
                           class="w-full text-xs rounded-lg border-slate-200 focus:border-emerald-500 focus:ring-emerald-500 @error('name') border-rose-400 @enderror">
                    @error('name') <span class="text-[11px] text-rose-500 mt-1 block">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Alamat Email Login <span class="text-rose-500">*</span></label>
                    <input type="email" name="email" value="{{ old('email') }}" required placeholder="Contoh: ahmad@company.com" 
                           class="w-full text-xs rounded-lg border-slate-200 focus:border-emerald-500 focus:ring-emerald-500 @error('email') border-rose-400 @enderror">
                    @error('email') <span class="text-[11px] text-rose-500 mt-1 block">{{ $message }}</span> @enderror
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Kata Sandi (Password) <span class="text-rose-500">*</span></label>
                    <input type="password" name="password" required minlength="6" placeholder="Minimal 6 karakter" 
                           class="w-full text-xs rounded-lg border-slate-200 focus:border-emerald-500 focus:ring-emerald-500 @error('password') border-rose-400 @enderror">
                    @error('password') <span class="text-[11px] text-rose-500 mt-1 block">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Peran (Role) Hak Akses <span class="text-rose-500">*</span></label>
                    <select name="role" required class="w-full text-xs rounded-lg border-slate-200 focus:border-emerald-500 focus:ring-emerald-500 @error('role') border-rose-400 @enderror">
                        @foreach($roles as $role)
                            <option value="{{ $role->value }}" {{ old('role') === $role->value ? 'selected' : '' }}>
                                {{ $role->label() }}
                            </option>
                        @endforeach
                    </select>
                    @error('role') <span class="text-[11px] text-rose-500 mt-1 block">{{ $message }}</span> @enderror
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Departemen / Divisi</label>
                    <input type="text" name="department" value="{{ old('department') }}" placeholder="Contoh: Quality Assurance & Control" 
                           class="w-full text-xs rounded-lg border-slate-200 focus:border-emerald-500 focus:ring-emerald-500">
                    @error('department') <span class="text-[11px] text-rose-500 mt-1 block">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Nomor Telepon / WhatsApp</label>
                    <input type="text" name="phone" value="{{ old('phone') }}" placeholder="Contoh: 08123456789" 
                           class="w-full text-xs rounded-lg border-slate-200 focus:border-emerald-500 focus:ring-emerald-500">
                    @error('phone') <span class="text-[11px] text-rose-500 mt-1 block">{{ $message }}</span> @enderror
                </div>
            </div>

            <div class="flex items-center space-x-2 pt-2">
                <input type="checkbox" name="is_active" value="1" id="is_active" {{ old('is_active', '1') == '1' ? 'checked' : '' }} 
                       class="rounded border-slate-300 text-emerald-600 focus:ring-emerald-500">
                <label for="is_active" class="text-xs text-slate-700">Akun aktif dan diizinkan login ke sistem</label>
            </div>

            <div class="pt-4 border-t border-slate-100 flex items-center justify-end space-x-2">
                <a href="{{ route('admin.users.index') }}" class="px-4 py-2 text-xs font-semibold text-slate-600 hover:text-slate-800">
                    Batal
                </a>
                <button type="submit" class="px-5 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-semibold rounded-lg shadow-sm transition-colors">
                    Daftarkan Pengguna
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
