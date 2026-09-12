@extends('layouts.admin')

@section('content')
<div class="max-w-3xl mx-auto space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <div class="flex items-center space-x-3">
                <h1 class="text-2xl font-bold tracking-tight text-slate-900">{{ $capa->capa_number }}</h1>
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold {{ $capa->status->badgeColor() }}">
                    {{ $capa->status->label() }}
                </span>
            </div>
            <p class="text-sm text-slate-500 mt-1">Target Penyelesaian: {{ $capa->target_completion_date?->format('d F Y') }} &bull; PIC: {{ $capa->assignedTo?->name }}</p>
        </div>
        <div class="flex items-center space-x-2">
            <a href="{{ route('admin.capa.edit', $capa) }}" class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-semibold rounded-lg shadow-sm">
                Edit & Update Status
            </a>
            <a href="{{ route('admin.capa.index') }}" class="px-3 py-2 bg-white hover:bg-slate-50 text-slate-700 border border-slate-200 text-xs font-semibold rounded-lg shadow-sm">
                &larr; Kembali
            </a>
        </div>
    </div>

    <!-- Details Card -->
    <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-6 space-y-6">
        <div>
            <span class="text-xs text-slate-400 font-semibold uppercase tracking-wider block">Judul Program Perbaikan Mutu</span>
            <h2 class="text-lg font-bold text-slate-900 mt-1">{{ $capa->title }}</h2>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 p-4 bg-slate-50 rounded-xl border border-slate-100 text-xs">
            <div>
                <span class="text-slate-400 block font-medium">Target Cacat:</span>
                <span class="font-bold text-slate-900">[{{ $capa->defectType?->code }}] {{ $capa->defectType?->name }}</span>
            </div>
            <div>
                <span class="text-slate-400 block font-medium">Kategori Keparahan:</span>
                <span class="font-bold uppercase text-slate-900">{{ $capa->defectType?->severity->value }}</span>
            </div>
            <div>
                <span class="text-slate-400 block font-medium">Kategori 5M+1E:</span>
                <span class="font-bold text-slate-900">{{ $capa->defectType?->default_5m_category->label() }}</span>
            </div>
        </div>

        <div class="space-y-4 text-xs">
            <div class="p-4 rounded-xl border border-blue-100 bg-blue-50/30">
                <span class="font-bold text-blue-900 uppercase text-[11px] block">1. Pernyataan Masalah (Define)</span>
                <p class="text-slate-800 mt-1 leading-relaxed">{{ $capa->problem_statement }}</p>
            </div>

            <div class="p-4 rounded-xl border border-purple-100 bg-purple-50/30">
                <span class="font-bold text-purple-900 uppercase text-[11px] block">2. Analisis Akar Masalah (Analyze - 5-Why)</span>
                <p class="text-slate-800 mt-1 leading-relaxed whitespace-pre-line">{{ $capa->root_cause_analysis }}</p>
            </div>

            <div class="p-4 rounded-xl border border-emerald-100 bg-emerald-50/30">
                <span class="font-bold text-emerald-900 uppercase text-[11px] block">3. Tindakan Korektif (Improve)</span>
                <p class="text-slate-800 mt-1 leading-relaxed">{{ $capa->corrective_action }}</p>
            </div>

            <div class="p-4 rounded-xl border border-amber-100 bg-amber-50/30">
                <span class="font-bold text-amber-900 uppercase text-[11px] block">4. Tindakan Pencegahan Berulang (Control)</span>
                <p class="text-slate-800 mt-1 leading-relaxed">{{ $capa->preventive_action }}</p>
            </div>

            @if($capa->verification_notes)
                <div class="p-4 rounded-xl border border-slate-200 bg-slate-50">
                    <span class="font-bold text-slate-700 uppercase text-[11px] block">5. Catatan Verifikasi Hasil & Efektivitas Mutu</span>
                    <p class="text-slate-800 mt-1 leading-relaxed">{{ $capa->verification_notes }}</p>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
