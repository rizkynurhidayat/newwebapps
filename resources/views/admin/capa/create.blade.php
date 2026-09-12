@extends('layouts.admin')

@section('content')
<div class="max-w-3xl mx-auto space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold tracking-tight text-slate-900">Buat Tiket Tindakan Perbaikan (CAPA)</h1>
            <p class="text-sm text-slate-500 mt-1">Dokumentasikan rencana tindakan korektif dan preventif terhadap jenis cacat dominan.</p>
        </div>
        <a href="{{ route('admin.capa.index') }}" class="text-xs font-semibold text-slate-500 hover:text-slate-700">&larr; Kembali</a>
    </div>

    <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-6">
        <form action="{{ route('admin.capa.store') }}" method="POST" class="space-y-4">
            @csrf

            <div class="p-3 bg-slate-50 rounded-lg border border-slate-100 flex items-center justify-between">
                <span class="text-xs text-slate-500 font-medium">Nomor Registrasi CAPA:</span>
                <span class="font-mono text-sm font-bold text-slate-800">{{ $nextCapaNumber }}</span>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Target Jenis Cacat yang Diperbaiki <span class="text-rose-500">*</span></label>
                    <select name="defect_type_id" class="w-full text-xs rounded-lg border-slate-200 focus:border-emerald-500 focus:ring-emerald-500" required>
                        <option value="">-- Pilih Jenis Cacat --</option>
                        @foreach($defectTypes as $dt)
                            <option value="{{ $dt->id }}" {{ (old('defect_type_id') ?? $selectedDefectId) == $dt->id ? 'selected' : '' }}>
                                [{{ $dt->code }}] {{ $dt->name }} ({{ ucfirst($dt->severity->value) }})
                            </option>
                        @endforeach
                    </select>
                    @error('defect_type_id') <span class="text-[11px] text-rose-500">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Penanggung Jawab (PIC) <span class="text-rose-500">*</span></label>
                    <select name="assigned_to_user_id" class="w-full text-xs rounded-lg border-slate-200 focus:border-emerald-500 focus:ring-emerald-500" required>
                        <option value="">-- Pilih Penanggung Jawab --</option>
                        @foreach($users as $u)
                            <option value="{{ $u->id }}" {{ old('assigned_to_user_id') == $u->id ? 'selected' : '' }}>
                                {{ $u->name }} ({{ $u->department ?? 'Staff' }})
                            </option>
                        @endforeach
                    </select>
                    @error('assigned_to_user_id') <span class="text-[11px] text-rose-500">{{ $message }}</span> @enderror
                </div>
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Judul Tindakan Perbaikan <span class="text-rose-500">*</span></label>
                <input type="text" name="title" value="{{ old('title') }}" required placeholder="e.g. Modifikasi Jig Penampung Conveyor untuk Mencegah Baret" 
                       class="w-full text-xs rounded-lg border-slate-200 focus:border-emerald-500 focus:ring-emerald-500">
                @error('title') <span class="text-[11px] text-rose-500">{{ $message }}</span> @enderror
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">1. Pernyataan Masalah (Define - Problem Statement) <span class="text-rose-500">*</span></label>
                <textarea name="problem_statement" rows="2" class="w-full text-xs rounded-lg border-slate-200 focus:border-emerald-500 focus:ring-emerald-500" required placeholder="Deskripsikan masalah cacat, frekuensi kemunculan, dan dampaknya...">{{ old('problem_statement') }}</textarea>
                @error('problem_statement') <span class="text-[11px] text-rose-500">{{ $message }}</span> @enderror
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">2. Analisis Akar Masalah (Analyze - 5-Why Analysis) <span class="text-rose-500">*</span></label>
                <textarea name="root_cause_analysis" rows="3" class="w-full text-xs rounded-lg border-slate-200 focus:border-emerald-500 focus:ring-emerald-500" required placeholder="Gunakan teknik 5-Why: Kenapa terjadi? ... Kenapa? ... Kenapa?">{{ old('root_cause_analysis') }}</textarea>
                @error('root_cause_analysis') <span class="text-[11px] text-rose-500">{{ $message }}</span> @enderror
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">3. Tindakan Korektif (Improve - Corrective Action) <span class="text-rose-500">*</span></label>
                    <textarea name="corrective_action" rows="3" class="w-full text-xs rounded-lg border-slate-200 focus:border-emerald-500 focus:ring-emerald-500" required placeholder="Tindakan langsung untuk memperbaiki cacat saat ini...">{{ old('corrective_action') }}</textarea>
                    @error('corrective_action') <span class="text-[11px] text-rose-500">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">4. Tindakan Pencegahan (Control - Preventive Action) <span class="text-rose-500">*</span></label>
                    <textarea name="preventive_action" rows="3" class="w-full text-xs rounded-lg border-slate-200 focus:border-emerald-500 focus:ring-emerald-500" required placeholder="Perubahan SOP, jadwal maintenance, agar tidak terulang...">{{ old('preventive_action') }}</textarea>
                    @error('preventive_action') <span class="text-[11px] text-rose-500">{{ $message }}</span> @enderror
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Target Tanggal Selesai <span class="text-rose-500">*</span></label>
                    <input type="date" name="target_completion_date" value="{{ old('target_completion_date', now()->addDays(7)->format('Y-m-d')) }}" required 
                           class="w-full text-xs rounded-lg border-slate-200 focus:border-emerald-500 focus:ring-emerald-500">
                    @error('target_completion_date') <span class="text-[11px] text-rose-500">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Status Awal</label>
                    <select name="status" class="w-full text-xs rounded-lg border-slate-200 focus:border-emerald-500 focus:ring-emerald-500">
                        <option value="open" {{ old('status') === 'open' ? 'selected' : '' }}>Open (Baru Didaftarkan)</option>
                        <option value="in_progress" {{ old('status') === 'in_progress' ? 'selected' : '' }}>In Progress (Sedang Dikerjakan)</option>
                    </select>
                </div>
            </div>

            <div class="pt-4 border-t border-slate-100 flex items-center justify-end space-x-2">
                <a href="{{ route('admin.capa.index') }}" class="px-4 py-2 text-xs font-medium text-slate-600 hover:text-slate-800">Batal</a>
                <button type="submit" class="px-5 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-semibold rounded-lg shadow-sm transition-colors">
                    Simpan Tiket CAPA
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
