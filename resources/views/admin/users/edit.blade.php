@extends('layouts.admin')

@section('content')
<div class="max-w-2xl mx-auto space-y-6">
    <div>
        <div class="flex items-center space-x-2 text-xs text-slate-500 mb-1">
            <a href="{{ route('admin.users.index') }}" class="hover:text-slate-700">Manajemen Pengguna</a>
            <span>/</span>
            <span class="text-slate-800 font-medium">Edit [{{ $user->name }}]</span>
        </div>
        <h1 class="text-2xl font-bold tracking-tight text-slate-900">Perbarui Profil & Hak Akses Pengguna</h1>
        <p class="text-sm text-slate-500">Ubah peran otorisasi, data departemen, atau atur ulang kata sandi login karyawan.</p>
    </div>

    @if($user->id === auth()->id())
        <div class="p-3.5 rounded-xl bg-purple-50 border border-purple-200 text-purple-800 text-xs flex items-center space-x-2">
            <svg class="w-4 h-4 text-purple-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <span>Anda sedang menyunting akun Anda sendiri. Peran dan status aktif dikunci demi keamanan sistem.</span>
        </div>
    @endif

    <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-6">
        <form method="POST" action="{{ route('admin.users.update', $user) }}" class="space-y-4">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Nama Lengkap Karyawan <span class="text-rose-500">*</span></label>
                    <input type="text" name="name" value="{{ old('name', $user->name) }}" required 
                           class="w-full text-xs rounded-lg border-slate-200 focus:border-emerald-500 focus:ring-emerald-500 @error('name') border-rose-400 @enderror">
                    @error('name') <span class="text-[11px] text-rose-500 mt-1 block">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Alamat Email Login <span class="text-rose-500">*</span></label>
                    <input type="email" name="email" value="{{ old('email', $user->email) }}" required 
                           class="w-full text-xs rounded-lg border-slate-200 focus:border-emerald-500 focus:ring-emerald-500 @error('email') border-rose-400 @enderror">
                    @error('email') <span class="text-[11px] text-rose-500 mt-1 block">{{ $message }}</span> @enderror
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Kata Sandi Baru (Opsional)</label>
                    <input type="password" name="password" minlength="6" placeholder="Kosongkan jika tidak diubah" 
                           class="w-full text-xs rounded-lg border-slate-200 focus:border-emerald-500 focus:ring-emerald-500 @error('password') border-rose-400 @enderror">
                    <p class="text-[10px] text-slate-400 mt-1">Isi hanya jika ingin mereset password akun.</p>
                    @error('password') <span class="text-[11px] text-rose-500 mt-1 block">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Peran (Role) Hak Akses <span class="text-rose-500">*</span></label>
                    @if($user->id === auth()->id())
                        <input type="hidden" name="role" value="{{ $user->role->value }}">
                        <input type="text" disabled value="{{ $user->role->label() }}" class="w-full text-xs rounded-lg border-slate-200 bg-slate-100 text-slate-500 cursor-not-allowed">
                    @else
                        <select name="role" required class="w-full text-xs rounded-lg border-slate-200 focus:border-emerald-500 focus:ring-emerald-500 @error('role') border-rose-400 @enderror">
                            @foreach($roles as $role)
                                <option value="{{ $role->value }}" {{ old('role', $user->role->value) === $role->value ? 'selected' : '' }}>
                                    {{ $role->label() }}
                                </option>
                            @endforeach
                        </select>
                        @error('role') <span class="text-[11px] text-rose-500 mt-1 block">{{ $message }}</span> @enderror
                    @endif
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Departemen / Divisi</label>
                    <input type="text" name="department" value="{{ old('department', $user->department) }}" 
                           class="w-full text-xs rounded-lg border-slate-200 focus:border-emerald-500 focus:ring-emerald-500">
                    @error('department') <span class="text-[11px] text-rose-500 mt-1 block">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Nomor Telepon / WhatsApp</label>
                    <input type="text" name="phone" value="{{ old('phone', $user->phone) }}" 
                           class="w-full text-xs rounded-lg border-slate-200 focus:border-emerald-500 focus:ring-emerald-500">
                    @error('phone') <span class="text-[11px] text-rose-500 mt-1 block">{{ $message }}</span> @enderror
                </div>
            </div>

            <div class="flex items-center space-x-2 pt-2">
                @if($user->id === auth()->id())
                    <input type="hidden" name="is_active" value="1">
                    <input type="checkbox" checked disabled class="rounded border-slate-300 text-emerald-600 cursor-not-allowed">
                    <label class="text-xs text-slate-500">Status akun aktif (Akun yang sedang login)</label>
                @else
                    <input type="checkbox" name="is_active" value="1" id="is_active" {{ old('is_active', $user->is_active ? '1' : '0') == '1' ? 'checked' : '' }} 
                           class="rounded border-slate-300 text-emerald-600 focus:ring-emerald-500">
                    <label for="is_active" class="text-xs text-slate-700">Akun aktif dan diizinkan login ke sistem</label>
                @endif
            </div>

            <div class="pt-4 border-t border-slate-100 flex items-center justify-end space-x-2">
                <a href="{{ route('admin.users.index') }}" class="px-4 py-2 text-xs font-semibold text-slate-600 hover:text-slate-800">
                    Batal
                </a>
                <button type="submit" class="px-5 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-semibold rounded-lg shadow-sm transition-colors">
                    Simpan Perubahan Akun
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
