@extends('layouts.admin')

@section('content')
<div class="max-w-3xl mx-auto space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold tracking-tight text-slate-900">Update Tiket CAPA: {{ $capa->capa_number }}</h1>
            <p class="text-sm text-slate-500 mt-1">Perbarui status implementasi, tanggal realisasi, dan catatan verifikasi mutu.</p>
        </div>
        <a href="{{ route('admin.capa.show', $capa) }}" class="text-xs font-semibold text-slate-500 hover:text-slate-700">&larr; Kembali</a>
    </div>

    <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-6">
        <form action="{{ route('admin.capa.update', $capa) }}" method="POST" class="space-y-4">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Target Jenis Cacat <span class="text-rose-500">*</span></label>
                    <select name="defect_type_id" class="w-full text-xs rounded-lg border-slate-200 focus:border-emerald-500 focus:ring-emerald-500" required>
                        @foreach($defectTypes as $dt)
                            <option value="{{ $dt->id }}" {{ old('defect_type_id', $capa->defect_type_id) == $dt->id ? 'selected' : '' }}>
                                [{{ $dt->code }}] {{ $dt->name }} ({{ ucfirst($dt->severity->value) }})
                            </option>
                        @endforeach
                    </select>
                    @error('defect_type_id') <span class="text-[11px] text-rose-500">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Penanggung Jawab (PIC) <span class="text-rose-500">*</span></label>
                    <select name="assigned_to_user_id" class="w-full text-xs rounded-lg border-slate-200 focus:border-emerald-500 focus:ring-emerald-500" required>
                        @foreach($users as $u)
                            <option value="{{ $u->id }}" {{ old('assigned_to_user_id', $capa->assigned_to_user_id) == $u->id ? 'selected' : '' }}>
                                {{ $u->name }} ({{ $u->department ?? 'Staff' }})
                            </option>
                        @endforeach
                    </select>
                    @error('assigned_to_user_id') <span class="text-[11px] text-rose-500">{{ $message }}</span> @enderror
                </div>
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Judul Tindakan Perbaikan <span class="text-rose-500">*</span></label>
                <input type="text" name="title" value="{{ old('title', $capa->title) }}" required 
                       class="w-full text-xs rounded-lg border-slate-200 focus:border-emerald-500 focus:ring-emerald-500">
                @error('title') <span class="text-[11px] text-rose-500">{{ $message }}</span> @enderror
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">1. Pernyataan Masalah (Define) <span class="text-rose-500">*</span></label>
                <textarea name="problem_statement" rows="2" class="w-full text-xs rounded-lg border-slate-200 focus:border-emerald-500 focus:ring-emerald-500" required>{{ old('problem_statement', $capa->problem_statement) }}</textarea>
                @error('problem_statement') <span class="text-[11px] text-rose-500">{{ $message }}</span> @enderror
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">2. Analisis Akar Masalah (Analyze - 5-Why) <span class="text-rose-500">*</span></label>
                <textarea name="root_cause_analysis" rows="3" class="w-full text-xs rounded-lg border-slate-200 focus:border-emerald-500 focus:ring-emerald-500" required>{{ old('root_cause_analysis', $capa->root_cause_analysis) }}</textarea>
                @error('root_cause_analysis') <span class="text-[11px] text-rose-500">{{ $message }}</span> @enderror
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">3. Tindakan Korektif (Improve) <span class="text-rose-500">*</span></label>
                    <textarea name="corrective_action" rows="3" class="w-full text-xs rounded-lg border-slate-200 focus:border-emerald-500 focus:ring-emerald-500" required>{{ old('corrective_action', $capa->corrective_action) }}</textarea>
                    @error('corrective_action') <span class="text-[11px] text-rose-500">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">4. Tindakan Pencegahan (Control) <span class="text-rose-500">*</span></label>
                    <textarea name="preventive_action" rows="3" class="w-full text-xs rounded-lg border-slate-200 focus:border-emerald-500 focus:ring-emerald-500" required>{{ old('preventive_action', $capa->preventive_action) }}</textarea>
                    @error('preventive_action') <span class="text-[11px] text-rose-500">{{ $message }}</span> @enderror
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 p-4 bg-slate-50 rounded-xl border border-slate-100">
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Status CAPA <span class="text-rose-500">*</span></label>
                    <select name="status" class="w-full text-xs rounded-lg border-slate-200 focus:border-emerald-500 focus:ring-emerald-500" required>
                        @foreach($statuses as $st)
                            <option value="{{ $st->value }}" {{ old('status', $capa->status->value) === $st->value ? 'selected' : '' }}>
                                {{ $st->label() }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Target Tanggal Selesai <span class="text-rose-500">*</span></label>
                    <input type="date" name="target_completion_date" value="{{ old('target_completion_date', $capa->target_completion_date?->format('Y-m-d')) }}" required 
                           class="w-full text-xs rounded-lg border-slate-200 focus:border-emerald-500 focus:ring-emerald-500 bg-white">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Realisasi Tanggal Selesai</label>
                    <input type="date" name="actual_completion_date" value="{{ old('actual_completion_date', $capa->actual_completion_date?->format('Y-m-d')) }}" 
                           class="w-full text-xs rounded-lg border-slate-200 focus:border-emerald-500 focus:ring-emerald-500 bg-white">
                </div>
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Catatan Verifikasi Hasil & Efektivitas Mutu</label>
                <textarea name="verification_notes" rows="2" class="w-full text-xs rounded-lg border-slate-200 focus:border-emerald-500 focus:ring-emerald-500" placeholder="Hasil evaluasi berkala setelah tindakan perbaikan diterapkan...">{{ old('verification_notes', $capa->verification_notes) }}</textarea>
            </div>

            <div class="pt-4 border-t border-slate-100 flex items-center justify-end space-x-2">
                <a href="{{ route('admin.capa.show', $capa) }}" class="px-4 py-2 text-xs font-medium text-slate-600 hover:text-slate-800">Batal</a>
                <button type="submit" class="px-5 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-semibold rounded-lg shadow-sm transition-colors">
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
