@extends('layouts.admin')

@section('content')
<div class="max-w-xl mx-auto space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold tracking-tight text-slate-900">Tambah Jenis Cacat</h1>
            <p class="text-sm text-slate-500 mt-1">Daftarkan jenis ketidaksesuaian produk untuk formulir QC.</p>
        </div>
        <a href="{{ route('admin.defects.index') }}" class="text-xs font-semibold text-slate-500 hover:text-slate-700">&larr; Kembali</a>
    </div>

    <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-6">
        <form action="{{ route('admin.defects.store') }}" method="POST" class="space-y-4">
            @csrf

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Kode Cacat <span class="text-rose-500">*</span></label>
                    <input type="text" name="code" value="{{ old('code') }}" required placeholder="e.g. DEF-POR" 
                           class="w-full text-xs rounded-lg border-slate-200 focus:border-emerald-500 focus:ring-emerald-500">
                    @error('code') <span class="text-[11px] text-rose-500">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Kategori Mutu <span class="text-rose-500">*</span></label>
                    <select name="defect_category_id" class="w-full text-xs rounded-lg border-slate-200 focus:border-emerald-500 focus:ring-emerald-500" required>
                        <option value="">-- Pilih Kategori --</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}" {{ old('defect_category_id') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                        @endforeach
                    </select>
                    @error('defect_category_id') <span class="text-[11px] text-rose-500">{{ $message }}</span> @enderror
                </div>
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Nama Cacat / Defect <span class="text-rose-500">*</span></label>
                <input type="text" name="name" value="{{ old('name') }}" required placeholder="e.g. Rongga Udara Coran (Porosity)" 
                       class="w-full text-xs rounded-lg border-slate-200 focus:border-emerald-500 focus:ring-emerald-500">
                @error('name') <span class="text-[11px] text-rose-500">{{ $message }}</span> @enderror
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Tingkat Keparahan (Severity) <span class="text-rose-500">*</span></label>
                    <select name="severity" class="w-full text-xs rounded-lg border-slate-200 focus:border-emerald-500 focus:ring-emerald-500" required>
                        <option value="minor" {{ old('severity') === 'minor' ? 'selected' : '' }}>Minor (Cacat Kosmetik/Ringan)</option>
                        <option value="major" {{ old('severity') === 'major' ? 'selected' : '' }}>Major (Mempengaruhi Fungsi)</option>
                        <option value="critical" {{ old('severity') === 'critical' ? 'selected' : '' }}>Critical (Reject Total/Bahaya)</option>
                    </select>
                    @error('severity') <span class="text-[11px] text-rose-500">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Kategori Standar 5M+1E <span class="text-rose-500">*</span></label>
                    <select name="default_5m_category" class="w-full text-xs rounded-lg border-slate-200 focus:border-emerald-500 focus:ring-emerald-500" required>
                        <option value="machine" {{ old('default_5m_category') === 'machine' ? 'selected' : '' }}>Machine (Mesin/Tooling)</option>
                        <option value="man" {{ old('default_5m_category') === 'man' ? 'selected' : '' }}>Man (Operator/Skill)</option>
                        <option value="method" {{ old('default_5m_category') === 'method' ? 'selected' : '' }}>Method (SOP/Prosedur)</option>
                        <option value="material" {{ old('default_5m_category') === 'material' ? 'selected' : '' }}>Material (Bahan Baku)</option>
                        <option value="measurement" {{ old('default_5m_category') === 'measurement' ? 'selected' : '' }}>Measurement (Alat Ukur)</option>
                        <option value="environment" {{ old('default_5m_category') === 'environment' ? 'selected' : '' }}>Environment (Lingkungan)</option>
                    </select>
                    @error('default_5m_category') <span class="text-[11px] text-rose-500">{{ $message }}</span> @enderror
                </div>
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Deskripsi & Gejala Visual Cacat</label>
                <textarea name="description" rows="3" class="w-full text-xs rounded-lg border-slate-200 focus:border-emerald-500 focus:ring-emerald-500" placeholder="Karakteristik visual cacat, cara mengenali saat inspeksi...">{{ old('description') }}</textarea>
                @error('description') <span class="text-[11px] text-rose-500">{{ $message }}</span> @enderror
            </div>

            <div class="pt-4 border-t border-slate-100 flex items-center justify-end space-x-2">
                <a href="{{ route('admin.defects.index') }}" class="px-4 py-2 text-xs font-medium text-slate-600 hover:text-slate-800">Batal</a>
                <button type="submit" class="px-5 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-semibold rounded-lg shadow-sm transition-colors">
                    Simpan Jenis Cacat
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
