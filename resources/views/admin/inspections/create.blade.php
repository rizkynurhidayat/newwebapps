@extends('layouts.admin')

@section('content')
<div class="max-w-4xl mx-auto space-y-6" x-data="inspectionForm()">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold tracking-tight text-slate-900">Input Pemeriksaan Kualitas (QC)</h1>
            <p class="text-sm text-slate-500 mt-1">Formulir sampling mutu dengan kalkulator Six Sigma instan (*Real-Time DPU, DPMO & Sigma Level*).</p>
        </div>
        <a href="{{ route('admin.inspections.index') }}" class="text-xs font-semibold text-slate-500 hover:text-slate-700">&larr; Kembali</a>
    </div>

    <!-- Real-time Six Sigma Live Metric Preview Card -->
    <div class="bg-gradient-to-r from-slate-900 to-slate-800 rounded-2xl p-5 text-white shadow-lg border border-slate-700">
        <div class="flex items-center justify-between border-b border-slate-700/80 pb-3 mb-4">
            <div class="flex items-center space-x-2">
                <span class="w-2.5 h-2.5 rounded-full bg-emerald-400 animate-ping"></span>
                <span class="text-xs font-bold uppercase tracking-wider text-emerald-400">Six Sigma Engine Preview (Real-Time)</span>
            </div>
            <span class="text-xs font-mono text-slate-400">{{ $nextInspectionNumber }}</span>
        </div>

        <div class="grid grid-cols-2 sm:grid-cols-5 gap-4 text-center">
            <div class="p-3 bg-slate-800/60 rounded-xl border border-slate-700/50">
                <span class="text-[10px] text-slate-400 uppercase font-semibold block">Total Cacat (D)</span>
                <span class="text-xl font-extrabold text-white mt-1 block font-mono" x-text="totalDefectsCount">0</span>
                <span class="text-[10px] text-slate-400">titik cacat</span>
            </div>

            <div class="p-3 bg-slate-800/60 rounded-xl border border-slate-700/50">
                <span class="text-[10px] text-slate-400 uppercase font-semibold block">DPU (Defects/Unit)</span>
                <span class="text-xl font-extrabold text-white mt-1 block font-mono" x-text="calculatedDpu">0.00</span>
                <span class="text-[10px] text-slate-400">D / N</span>
            </div>

            <div class="p-3 bg-slate-800/60 rounded-xl border border-slate-700/50">
                <span class="text-[10px] text-slate-400 uppercase font-semibold block">DPMO</span>
                <span class="text-xl font-extrabold text-purple-300 mt-1 block font-mono" x-text="calculatedDpmo">0</span>
                <span class="text-[10px] text-slate-400">Per 1 Juta Peluang</span>
            </div>

            <div class="p-3 bg-slate-800/60 rounded-xl border border-slate-700/50">
                <span class="text-[10px] text-slate-400 uppercase font-semibold block">Process Yield</span>
                <span class="text-xl font-extrabold text-emerald-400 mt-1 block font-mono" x-text="calculatedYield + '%'">100%</span>
                <span class="text-[10px] text-slate-400">Unit Lulus / N</span>
            </div>

            <div class="col-span-2 sm:col-span-1 p-3 bg-slate-800/60 rounded-xl border border-slate-700/50">
                <span class="text-[10px] text-slate-400 uppercase font-semibold block">Sigma Level</span>
                <div class="flex items-center justify-center space-x-1 mt-1">
                    <span class="text-2xl font-black text-amber-400 font-mono" x-text="calculatedSigma">6.00</span>
                    <span class="text-xs font-bold text-amber-300">&sigma;</span>
                </div>
                <span class="text-[10px] font-semibold" :class="calculatedSigma >= 4.0 ? 'text-emerald-400' : (calculatedSigma >= 3.0 ? 'text-amber-400' : 'text-rose-400')" x-text="calculatedSigma >= 4.0 ? 'Kualitas Prima' : (calculatedSigma >= 3.0 ? 'Perlu Perhatian' : 'Kritis / Reject')"></span>
            </div>
        </div>
    </div>

    <!-- Main Inspection Input Form -->
    <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-6">
        <form action="{{ route('admin.inspections.store') }}" method="POST" class="space-y-6">
            @csrf

            <!-- Section 1: Batch & Stage Selection -->
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div class="sm:col-span-2">
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Pilih Batch / Lot Produksi <span class="text-rose-500">*</span></label>
                    <select name="production_batch_id" x-model="selectedBatchId" @change="updateBatchInfo()" class="w-full text-xs rounded-lg border-slate-200 focus:border-emerald-500 focus:ring-emerald-500" required>
                        <option value="">-- Pilih Lot Produksi --</option>
                        @foreach($batches as $b)
                            <option value="{{ $b->id }}" data-opportunities="{{ $b->product?->defect_opportunities_per_unit ?? 5 }}" data-product="{{ $b->product?->name }}" {{ (old('production_batch_id') ?? $selectedBatch?->id) == $b->id ? 'selected' : '' }}>
                                {{ $b->batch_number }} - {{ $b->product?->name }} ({{ $b->productionLine?->line_code }})
                            </option>
                        @endforeach
                    </select>
                    @error('production_batch_id') <span class="text-[11px] text-rose-500">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Tahap Pemeriksaan QC <span class="text-rose-500">*</span></label>
                    <select name="inspection_stage" class="w-full text-xs rounded-lg border-slate-200 focus:border-emerald-500 focus:ring-emerald-500" required>
                        @foreach($stages as $stg)
                            <option value="{{ $stg->value }}" {{ old('inspection_stage', 'in_process') === $stg->value ? 'selected' : '' }}>
                                {{ $stg->label() }}
                            </option>
                        @endforeach
                    </select>
                    @error('inspection_stage') <span class="text-[11px] text-rose-500">{{ $message }}</span> @enderror
                </div>
            </div>

            <!-- Section 2: Sampling Parameters & Inspection Period -->
            <div class="p-4 bg-slate-50 rounded-xl border border-slate-100 space-y-4">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Ukuran Sampel Diperiksa (N) <span class="text-rose-500">*</span></label>
                        <input type="number" name="sample_size_inspected" x-model.number="sampleSize" @input="recalculate()" min="1" required 
                               class="w-full text-xs rounded-lg border-slate-200 focus:border-emerald-500 focus:ring-emerald-500 bg-white">
                        <span class="text-[10px] text-slate-400 mt-1 block">Total unit sampel fisik yang diuji</span>
                        @error('sample_size_inspected') <span class="text-[11px] text-rose-500">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Jumlah Unit Ditemukan Cacat <span class="text-rose-500">*</span></label>
                        <input type="number" name="defective_units_qty" x-model.number="defectiveUnits" @input="recalculate()" min="0" :max="sampleSize" required 
                               class="w-full text-xs rounded-lg border-slate-200 focus:border-emerald-500 focus:ring-emerald-500 bg-white">
                        <span class="text-[10px] text-slate-400 mt-1 block">Jumlah unit fisik yang tidak lolos QC</span>
                        @error('defective_units_qty') <span class="text-[11px] text-rose-500">{{ $message }}</span> @enderror
                    </div>
                </div>

                <!-- Waktu Pemeriksaan: Bulan & Minggu (1-4) -->
                <div class="border-t border-slate-200/60 pt-3">
                    <div class="flex items-center justify-between mb-2">
                        <label class="block text-xs font-bold text-slate-800 uppercase tracking-wider">
                            Waktu Pemeriksaan (Periode QC) <span class="text-rose-500">*</span>
                        </label>
                        <span class="text-[11px] text-slate-400">Pilih bulan dan minggu pelaksanaan inspeksi</span>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-[11px] font-medium text-slate-600 mb-1">Tahun <span class="text-rose-500">*</span></label>
                            <select name="inspection_year" class="w-full text-xs rounded-lg border-slate-200 focus:border-emerald-500 focus:ring-emerald-500 bg-white" required>
                                @foreach(range(now()->year - 1, now()->year + 1) as $yr)
                                    <option value="{{ $yr }}" {{ old('inspection_year', now()->year) == $yr ? 'selected' : '' }}>
                                        {{ $yr }}
                                    </option>
                                @endforeach
                            </select>
                            @error('inspection_year') <span class="text-[11px] text-rose-500">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-[11px] font-medium text-slate-600 mb-1">Bulan Pemeriksaan <span class="text-rose-500">*</span></label>
                            <select name="inspection_month" class="w-full text-xs rounded-lg border-slate-200 focus:border-emerald-500 focus:ring-emerald-500 bg-white" required>
                                @php
                                    $monthList = [
                                        1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
                                        5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
                                        9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
                                    ];
                                @endphp
                                @foreach($monthList as $mNum => $mName)
                                    <option value="{{ $mNum }}" {{ old('inspection_month', now()->month) == $mNum ? 'selected' : '' }}>
                                        {{ $mName }}
                                    </option>
                                @endforeach
                            </select>
                            @error('inspection_month') <span class="text-[11px] text-rose-500">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-[11px] font-medium text-slate-600 mb-1">Pilihan Minggu Keberapa (1-4) <span class="text-rose-500">*</span></label>
                            <select name="inspection_week" class="w-full text-xs rounded-lg border-slate-200 focus:border-emerald-500 focus:ring-emerald-500 bg-white" required>
                                @php
                                    $curWeek = min(4, (int) ceil(now()->day / 7));
                                @endphp
                                <option value="1" {{ old('inspection_week', $curWeek) == 1 ? 'selected' : '' }}>Minggu ke-1 (Hari 1 - 7)</option>
                                <option value="2" {{ old('inspection_week', $curWeek) == 2 ? 'selected' : '' }}>Minggu ke-2 (Hari 8 - 14)</option>
                                <option value="3" {{ old('inspection_week', $curWeek) == 3 ? 'selected' : '' }}>Minggu ke-3 (Hari 15 - 21)</option>
                                <option value="4" {{ old('inspection_week', $curWeek) == 4 ? 'selected' : '' }}>Minggu ke-4 (Hari 22 - 28/31)</option>
                            </select>
                            @error('inspection_week') <span class="text-[11px] text-rose-500">{{ $message }}</span> @enderror
                        </div>
                    </div>
                </div>
            </div>

            <!-- Section 3: Itemized Defect Breakdown (Detail Cacat) -->
            <div class="space-y-3">
                <div class="flex items-center justify-between">
                    <div>
                        <h2 class="text-xs font-bold uppercase tracking-wider text-slate-800">Daftar Rincian Cacat Fisik Ditemukan</h2>
                        <p class="text-[11px] text-slate-500">Catat setiap jenis cacat spesifik dan analisis faktor Ishikawa (5M+1E).</p>
                    </div>
                    <button type="button" @click="addDefectRow()" class="px-3 py-1.5 bg-emerald-50 text-emerald-700 hover:bg-emerald-100 border border-emerald-200 text-xs font-semibold rounded-lg transition-colors">
                        + Tambah Baris Cacat
                    </button>
                </div>

                <div class="space-y-3">
                    <template x-for="(defect, index) in defectRows" :key="index">
                        <div class="p-3 bg-slate-50 rounded-lg border border-slate-200 grid grid-cols-1 sm:grid-cols-12 gap-3 items-start">
                            <div class="sm:col-span-4">
                                <label class="block text-[11px] font-medium text-slate-600 mb-1">Jenis Cacat</label>
                                <select :name="`defects[${index}][defect_type_id]`" x-model="defect.defect_type_id" class="w-full text-xs rounded-lg border-slate-200 bg-white" required>
                                    <option value="">-- Pilih Jenis Cacat --</option>
                                    @foreach($defectTypes as $dt)
                                        <option value="{{ $dt->id }}">{{ $dt->code }} - {{ $dt->name }} ({{ ucfirst($dt->severity->value) }})</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="sm:col-span-2">
                                <label class="block text-[11px] font-medium text-slate-600 mb-1">Jumlah (Qty)</label>
                                <input type="number" :name="`defects[${index}][defect_qty]`" x-model.number="defect.defect_qty" @input="recalculate()" min="1" class="w-full text-xs rounded-lg border-slate-200 bg-white" required>
                            </div>

                            <div class="sm:col-span-3">
                                <label class="block text-[11px] font-medium text-slate-600 mb-1">Kategori 5M+1E</label>
                                <select :name="`defects[${index}][root_cause_category]`" x-model="defect.root_cause_category" class="w-full text-xs rounded-lg border-slate-200 bg-white" required>
                                    <option value="machine">Machine (Mesin)</option>
                                    <option value="man">Man (Operator)</option>
                                    <option value="method">Method (Metode)</option>
                                    <option value="material">Material (Bahan)</option>
                                    <option value="measurement">Measurement (Alat Ukur)</option>
                                    <option value="environment">Environment (Lingkungan)</option>
                                </select>
                            </div>

                            <div class="sm:col-span-2">
                                <label class="block text-[11px] font-medium text-slate-600 mb-1">Catatan</label>
                                <input type="text" :name="`defects[${index}][root_cause_notes]`" x-model="defect.root_cause_notes" placeholder="Lokasi goresan, dll" class="w-full text-xs rounded-lg border-slate-200 bg-white">
                            </div>

                            <div class="sm:col-span-1 pt-6 text-right">
                                <button type="button" @click="removeDefectRow(index)" class="p-1.5 text-rose-500 hover:text-rose-700 hover:bg-rose-50 rounded">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </template>

                    <div x-show="defectRows.length === 0" class="p-4 border border-dashed border-slate-200 rounded-lg text-center text-xs text-slate-400">
                        Tidak ada cacat yang dilaporkan. Klik tombol <span class="font-semibold text-emerald-600">+ Tambah Baris Cacat</span> jika menemukan cacat fisik pada sampel.
                    </div>
                </div>
            </div>

            <!-- Notes & Batch Completion Checkbox -->
            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Catatan Pemeriksaan Tambahan</label>
                <textarea name="notes" rows="2" class="w-full text-xs rounded-lg border-slate-200 focus:border-emerald-500 focus:ring-emerald-500" placeholder="Kondisi pengujian, instrumen uji yang dipakai...">{{ old('notes') }}</textarea>
            </div>

            <div class="flex items-center space-x-2 pt-2">
                <input type="checkbox" name="complete_batch" value="1" id="complete_batch" class="rounded border-slate-300 text-emerald-600 focus:ring-emerald-500">
                <label for="complete_batch" class="text-xs text-slate-700 font-medium">Tandai lot produksi ini telah selesai (*Batch Completed*)</label>
            </div>

            <div class="pt-4 border-t border-slate-100 flex items-center justify-end space-x-2">
                <a href="{{ route('admin.inspections.index') }}" class="px-4 py-2 text-xs font-medium text-slate-600 hover:text-slate-800">Batal</a>
                <button type="submit" class="px-5 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-semibold rounded-lg shadow-sm transition-colors">
                    Simpan Hasil Pemeriksaan
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
function inspectionForm() {
    return {
        selectedBatchId: '{{ old('production_batch_id') ?? ($selectedBatch?->id ?? '') }}',
        opportunitiesPerUnit: {{ $selectedBatch?->product?->defect_opportunities_per_unit ?? 5 }},
        sampleSize: {{ old('sample_size_inspected', 100) }},
        defectiveUnits: {{ old('defective_units_qty', 0) }},
        defectRows: [],

        totalDefectsCount: 0,
        calculatedDpu: '0.0000',
        calculatedDpmo: '0.00',
        calculatedYield: '100.0',
        calculatedSigma: '6.00',

        init() {
            this.updateBatchInfo();
            this.recalculate();
        },

        updateBatchInfo() {
            const selectEl = document.querySelector('select[name="production_batch_id"]');
            if (selectEl && selectEl.selectedIndex >= 0) {
                const opt = selectEl.options[selectEl.selectedIndex];
                if (opt && opt.dataset.opportunities) {
                    this.opportunitiesPerUnit = parseInt(opt.dataset.opportunities) || 5;
                }
            }
            this.recalculate();
        },

        addDefectRow() {
            this.defectRows.push({
                defect_type_id: '',
                defect_qty: 1,
                root_cause_category: 'machine',
                root_cause_notes: ''
            });
            if (this.defectiveUnits === 0) {
                this.defectiveUnits = 1;
            }
            this.recalculate();
        },

        removeDefectRow(index) {
            this.defectRows.splice(index, 1);
            this.recalculate();
        },

        recalculate() {
            const n = Math.max(1, this.sampleSize || 1);
            const passed = Math.max(0, n - (this.defectiveUnits || 0));

            // Sum itemized defects
            let d = 0;
            if (this.defectRows.length > 0) {
                d = this.defectRows.reduce((acc, row) => acc + (parseInt(row.defect_qty) || 0), 0);
            } else {
                d = this.defectiveUnits || 0;
            }
            this.totalDefectsCount = d;

            // DPU = D / N
            const dpu = d / n;
            this.calculatedDpu = dpu.toFixed(4);

            // Yield = (passed / N) * 100
            const y = (passed / n) * 100;
            this.calculatedYield = y.toFixed(1);

            // DPO = D / (N * O)
            const o = Math.max(1, this.opportunitiesPerUnit || 5);
            const dpo = d / (n * o);
            const dpmo = dpo * 1000000;
            this.calculatedDpmo = Math.round(dpmo).toLocaleString('id-ID');

            // Sigma Level with 1.5 sigma shift approximation
            if (dpmo <= 0 || d === 0) {
                this.calculatedSigma = '6.00';
            } else if (dpo >= 0.999) {
                this.calculatedSigma = '0.00';
            } else {
                // Approximate Sigma
                const p = 1.0 - dpo;
                const z = this.invNorm(p);
                const sigma = Math.max(0, Math.min(6.0, z + 1.5));
                this.calculatedSigma = sigma.toFixed(2);
            }
        },

        // Fast Acklam / Hastings normal inverse approximation for client-side preview
        invNorm(p) {
            if (p <= 0) return -6;
            if (p >= 1) return 6;
            const a1 = -3.969683028665376e+01, a2 = 2.209460984245205e+02, a3 = -2.759285104469687e+02;
            const a4 = 1.383577518672690e+02, a5 = -3.066479806614716e+01, a6 = 2.506628277459239e+00;
            const b1 = -5.447609879822406e+01, b2 = 1.615858368580409e+02, b3 = -1.556989798598866e+02;
            const b4 = 6.680131188771972e+01, b5 = -1.328068155288572e+01;
            const q = p - 0.5;
            const r = q * q;
            return (((((a1*r+a2)*r+a3)*r+a4)*r+a5)*r+a6)*q / (((((b1*r+b2)*r+b3)*r+b4)*r+b5)*r+1);
        }
    };
}
</script>
@endpush
@endsection
