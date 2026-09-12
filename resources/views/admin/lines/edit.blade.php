@extends('layouts.admin')

@section('content')
<div class="max-w-xl mx-auto space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold tracking-tight text-slate-900">Edit Lini: {{ $line->line_code }}</h1>
            <p class="text-sm text-slate-500 mt-1">Perbarui data lokasi dan status operasional lini produksi.</p>
        </div>
        <a href="{{ route('admin.lines.index') }}" class="text-xs font-semibold text-slate-500 hover:text-slate-700">&larr; Kembali</a>
    </div>

    <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-6">
        <form action="{{ route('admin.lines.update', $line) }}" method="POST" class="space-y-4">
            @csrf
            @method('PUT')

            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Kode Lini / Mesin <span class="text-rose-500">*</span></label>
                <input type="text" name="line_code" value="{{ old('line_code', $line->line_code) }}" required 
                       class="w-full text-xs rounded-lg border-slate-200 focus:border-emerald-500 focus:ring-emerald-500">
                @error('line_code') <span class="text-[11px] text-rose-500">{{ $message }}</span> @enderror
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Nama Lini / Mesin <span class="text-rose-500">*</span></label>
                <input type="text" name="name" value="{{ old('name', $line->name) }}" required 
                       class="w-full text-xs rounded-lg border-slate-200 focus:border-emerald-500 focus:ring-emerald-500">
                @error('name') <span class="text-[11px] text-rose-500">{{ $message }}</span> @enderror
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Lokasi Pabrik / Workshop</label>
                <input type="text" name="location" value="{{ old('location', $line->location) }}" 
                       class="w-full text-xs rounded-lg border-slate-200 focus:border-emerald-500 focus:ring-emerald-500">
                @error('location') <span class="text-[11px] text-rose-500">{{ $message }}</span> @enderror
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Status Operasional <span class="text-rose-500">*</span></label>
                <select name="status" class="w-full text-xs rounded-lg border-slate-200 focus:border-emerald-500 focus:ring-emerald-500" required>
                    <option value="operational" {{ old('status', $line->status) === 'operational' ? 'selected' : '' }}>Operational (Siap Pakai)</option>
                    <option value="maintenance" {{ old('status', $line->status) === 'maintenance' ? 'selected' : '' }}>Maintenance (Perawatan Mesin)</option>
                    <option value="inactive" {{ old('status', $line->status) === 'inactive' ? 'selected' : '' }}>Inactive (Nonaktif)</option>
                </select>
                @error('status') <span class="text-[11px] text-rose-500">{{ $message }}</span> @enderror
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Deskripsi & Catatan Spesifikasi</label>
                <textarea name="description" rows="3" class="w-full text-xs rounded-lg border-slate-200 focus:border-emerald-500 focus:ring-emerald-500">{{ old('description', $line->description) }}</textarea>
                @error('description') <span class="text-[11px] text-rose-500">{{ $message }}</span> @enderror
            </div>

            <div class="pt-4 border-t border-slate-100 flex items-center justify-end space-x-2">
                <a href="{{ route('admin.lines.index') }}" class="px-4 py-2 text-xs font-medium text-slate-600 hover:text-slate-800">Batal</a>
                <button type="submit" class="px-5 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-semibold rounded-lg shadow-sm transition-colors">
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
