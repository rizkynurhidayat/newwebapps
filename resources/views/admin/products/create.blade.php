@extends('layouts.admin')

@section('content')
<div class="max-w-2xl mx-auto space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold tracking-tight text-slate-900">Tambah Produk Baru</h1>
            <p class="text-sm text-slate-500 mt-1">Daftarkan part number baru dan tentukan parameter peluang cacat (*Defect Opportunities*).</p>
        </div>
        <a href="{{ route('admin.products.index') }}" class="text-xs font-semibold text-slate-500 hover:text-slate-700">&larr; Kembali</a>
    </div>

    <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-6">
        <form action="{{ route('admin.products.store') }}" method="POST" class="space-y-4">
            @csrf

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Part Number / SKU <span class="text-rose-500">*</span></label>
                    <input type="text" name="part_number" value="{{ old('part_number') }}" required placeholder="e.g. PART-ECM-05" 
                           class="w-full text-xs rounded-lg border-slate-200 focus:border-emerald-500 focus:ring-emerald-500">
                    @error('part_number') <span class="text-[11px] text-rose-500">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Nama Produk <span class="text-rose-500">*</span></label>
                    <input type="text" name="name" value="{{ old('name') }}" required placeholder="e.g. Starter Motor Assembly" 
                           class="w-full text-xs rounded-lg border-slate-200 focus:border-emerald-500 focus:ring-emerald-500">
                    @error('name') <span class="text-[11px] text-rose-500">{{ $message }}</span> @enderror
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Satuan Unit <span class="text-rose-500">*</span></label>
                    <input type="text" name="unit" value="{{ old('unit', 'pcs') }}" required 
                           class="w-full text-xs rounded-lg border-slate-200 focus:border-emerald-500 focus:ring-emerald-500">
                    @error('unit') <span class="text-[11px] text-rose-500">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Peluang Cacat (O / CTQ) <span class="text-rose-500">*</span></label>
                    <input type="number" name="defect_opportunities_per_unit" value="{{ old('defect_opportunities_per_unit', 5) }}" min="1" max="100" required 
                           class="w-full text-xs rounded-lg border-slate-200 focus:border-emerald-500 focus:ring-emerald-500">
                    <p class="text-[10px] text-slate-400 mt-1">Titik kritis inspeksi per unit</p>
                    @error('defect_opportunities_per_unit') <span class="text-[11px] text-rose-500">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Cycle Time Standar (Detik)</label>
                    <input type="number" step="0.1" name="standard_cycle_time" value="{{ old('standard_cycle_time') }}" placeholder="e.g. 45.0" 
                           class="w-full text-xs rounded-lg border-slate-200 focus:border-emerald-500 focus:ring-emerald-500">
                    @error('standard_cycle_time') <span class="text-[11px] text-rose-500">{{ $message }}</span> @enderror
                </div>
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Bahan Baku (Raw Material)</label>
                <input type="text" name="raw_material" value="{{ old('raw_material') }}" placeholder="Contoh: Plat Baja SPCC ketebalan 2.0 mm" 
                       class="w-full text-xs rounded-lg border-slate-200 focus:border-emerald-500 focus:ring-emerald-500">
                @error('raw_material') <span class="text-[11px] text-rose-500">{{ $message }}</span> @enderror
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Deskripsi & Spesifikasi Produk</label>
                <textarea name="description" rows="3" class="w-full text-xs rounded-lg border-slate-200 focus:border-emerald-500 focus:ring-emerald-500" placeholder="Keterangan material, toleransi dimensi, standar kualitas...">{{ old('description') }}</textarea>
                @error('description') <span class="text-[11px] text-rose-500">{{ $message }}</span> @enderror
            </div>

            <div class="flex items-center space-x-2">
                <input type="checkbox" name="is_active" value="1" id="is_active" {{ old('is_active', '1') == '1' ? 'checked' : '' }} 
                       class="rounded border-slate-300 text-emerald-600 focus:ring-emerald-500">
                <label for="is_active" class="text-xs text-slate-700">Produk aktif untuk dijadwalkan produksi</label>
            </div>

            <div class="pt-4 border-t border-slate-100 flex items-center justify-end space-x-2">
                <a href="{{ route('admin.products.index') }}" class="px-4 py-2 text-xs font-medium text-slate-600 hover:text-slate-800">Batal</a>
                <button type="submit" class="px-5 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-semibold rounded-lg shadow-sm transition-colors">
                    Simpan Produk
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
