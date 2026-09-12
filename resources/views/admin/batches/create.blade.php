@extends('layouts.admin')

@section('content')
<div class="max-w-2xl mx-auto space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold tracking-tight text-slate-900">Jadwalkan Lot Produksi Baru</h1>
            <p class="text-sm text-slate-500 mt-1">Buat nomor batch baru untuk perintah kerja produksi manufaktur.</p>
        </div>
        <a href="{{ route('admin.batches.index') }}" class="text-xs font-semibold text-slate-500 hover:text-slate-700">&larr; Kembali</a>
    </div>

    <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-6">
        <form action="{{ route('admin.batches.store') }}" method="POST" class="space-y-4">
            @csrf

            <div class="p-3 bg-slate-50 rounded-lg border border-slate-100 flex items-center justify-between">
                <span class="text-xs text-slate-500 font-medium">Nomor Lot Otomatis:</span>
                <span class="font-mono text-sm font-bold text-slate-800">{{ $nextBatchNumber }}</span>
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Pilih Produk <span class="text-rose-500">*</span></label>
                <select name="product_id" class="w-full text-xs rounded-lg border-slate-200 focus:border-emerald-500 focus:ring-emerald-500" required>
                    <option value="">-- Pilih Part Produk --</option>
                    @foreach($products as $prod)
                        <option value="{{ $prod->id }}" {{ old('product_id') == $prod->id ? 'selected' : '' }}>
                            {{ $prod->part_number }} - {{ $prod->name }} ({{ $prod->defect_opportunities_per_unit }} Peluang Cacat / CTQ)
                        </option>
                    @endforeach
                </select>
                @error('product_id') <span class="text-[11px] text-rose-500">{{ $message }}</span> @enderror
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Lini Produksi / Mesin <span class="text-rose-500">*</span></label>
                    <select name="production_line_id" class="w-full text-xs rounded-lg border-slate-200 focus:border-emerald-500 focus:ring-emerald-500" required>
                        <option value="">-- Pilih Lini Mesin --</option>
                        @foreach($lines as $ln)
                            <option value="{{ $ln->id }}" {{ old('production_line_id') == $ln->id ? 'selected' : '' }}>
                                {{ $ln->line_code }} - {{ $ln->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('production_line_id') <span class="text-[11px] text-rose-500">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Shift Kerja <span class="text-rose-500">*</span></label>
                    <select name="shift" class="w-full text-xs rounded-lg border-slate-200 focus:border-emerald-500 focus:ring-emerald-500" required>
                        @foreach($shifts as $s)
                            <option value="{{ $s->value }}" {{ old('shift') == $s->value ? 'selected' : '' }}>
                                {{ $s->label() }}
                            </option>
                        @endforeach
                    </select>
                    @error('shift') <span class="text-[11px] text-rose-500">{{ $message }}</span> @enderror
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Tanggal Produksi <span class="text-rose-500">*</span></label>
                    <input type="date" name="production_date" value="{{ old('production_date', now()->format('Y-m-d')) }}" required 
                           class="w-full text-xs rounded-lg border-slate-200 focus:border-emerald-500 focus:ring-emerald-500">
                    @error('production_date') <span class="text-[11px] text-rose-500">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Target Output (Unit) <span class="text-rose-500">*</span></label>
                    <input type="number" name="target_qty" value="{{ old('target_qty', 500) }}" min="1" required 
                           class="w-full text-xs rounded-lg border-slate-200 focus:border-emerald-500 focus:ring-emerald-500">
                    @error('target_qty') <span class="text-[11px] text-rose-500">{{ $message }}</span> @enderror
                </div>
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Catatan Tambahan & Instruksi Khusus</label>
                <textarea name="notes" rows="2" class="w-full text-xs rounded-lg border-slate-200 focus:border-emerald-500 focus:ring-emerald-500" placeholder="Parameter khusus perakitan, nomor PO supplier komponen...">{{ old('notes') }}</textarea>
                @error('notes') <span class="text-[11px] text-rose-500">{{ $message }}</span> @enderror
            </div>

            <div class="pt-4 border-t border-slate-100 flex items-center justify-end space-x-2">
                <a href="{{ route('admin.batches.index') }}" class="px-4 py-2 text-xs font-medium text-slate-600 hover:text-slate-800">Batal</a>
                <button type="submit" class="px-5 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-semibold rounded-lg shadow-sm transition-colors">
                    Buat Batch Produksi
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
