@extends('layouts.admin')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <!-- Breadcrumb & Title -->
    <div>
        <div class="flex items-center space-x-2 text-xs text-slate-500 mb-1">
            <a href="{{ route('admin.risks.index') }}" class="hover:text-slate-700">Analisa Resiko Mesin Press</a>
            <span>/</span>
            <span class="text-slate-800 font-medium">Tambah Analisa Bahaya Baru</span>
        </div>
        <h1 class="text-2xl font-bold tracking-tight text-slate-900">Input Data Analisa Resiko Bahaya (K3)</h1>
        <p class="text-sm text-slate-500">Poin 5: Masukkan nama bahaya, kemungkinan (likelihood), dan keparahan (severity). Sistem otomatis menghitung Risk = Likelihood &times; Severity dan menentukan kategori resiko.</p>
    </div>

    <!-- Interactive Risk Assessment Form -->
    <div x-data="{
        likelihood: {{ old('likelihood', 3) }},
        severity: {{ old('severity', 3) }},
        get score() {
            return this.likelihood * this.severity;
        },
        get level() {
            const s = this.score;
            if (s <= 4) return { label: 'Rendah (Low)', class: 'bg-emerald-100 text-emerald-800 border-emerald-300', text: 'Risiko dapat diterima dengan SOP standar.' };
            if (s <= 9) return { label: 'Sedang (Medium)', class: 'bg-amber-100 text-amber-800 border-amber-300', text: 'Perlu pengendalian teknis dan checklist harian.' };
            if (s <= 15) return { label: 'Tinggi (High)', class: 'bg-orange-100 text-orange-800 border-orange-300', text: 'Wajib dipasang sensor safety device & APD khusus.' };
            return { label: 'Ekstrem (Extreme)', class: 'bg-rose-100 text-rose-800 border-rose-300 font-bold', text: 'BAHAYA KRITIS! Mesin tidak boleh dioperasikan tanpa interlock pengaman mutlak.' };
        }
    }" class="bg-white rounded-xl border border-slate-200 shadow-sm p-6 space-y-6">

        <form method="POST" action="{{ route('admin.risks.store') }}" class="space-y-6">
            @csrf

            <!-- Section 1: Informasi Dasar Bahaya -->
            <div class="border-b border-slate-100 pb-5">
                <h3 class="text-sm font-bold text-slate-900 mb-4 flex items-center">
                    <span class="w-6 h-6 rounded-full bg-emerald-100 text-emerald-800 inline-flex items-center justify-center text-xs mr-2 font-mono">1</span>
                    Identitas Bahaya & Area Mesin Stamping Press
                </h3>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Kode Bahaya</label>
                        <input type="text" name="hazard_code" value="{{ old('hazard_code', $generatedCode) }}" class="w-full text-xs rounded-lg border-slate-200 font-mono focus:border-emerald-500 focus:ring-emerald-500 @error('hazard_code') border-rose-400 @enderror" placeholder="BHY-001">
                        @error('hazard_code') <span class="text-[11px] text-rose-600 mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <div class="sm:col-span-2">
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Nama Bahaya Mekanis / Operasional <span class="text-rose-500">*</span></label>
                        <input type="text" name="hazard_name" value="{{ old('hazard_name') }}" required class="w-full text-xs rounded-lg border-slate-200 focus:border-emerald-500 focus:ring-emerald-500 @error('hazard_name') border-rose-400 @enderror" placeholder="Contoh: Titik Jepit Antara Die Upper dan Lower Mesin Press">
                        @error('hazard_name') <span class="text-[11px] text-rose-600 mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <div class="sm:col-span-3">
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Area / Komponen Mesin Press <span class="text-rose-500">*</span></label>
                        <input type="text" name="machine_area" value="{{ old('machine_area', 'Mesin Stamping Press 250 Ton - Area Die & Feeder') }}" required class="w-full text-xs rounded-lg border-slate-200 focus:border-emerald-500 focus:ring-emerald-500 @error('machine_area') border-rose-400 @enderror" placeholder="Lini Mesin Stamping Press 250T, 300T, atau Die Stripper">
                        @error('machine_area') <span class="text-[11px] text-rose-600 mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <div class="sm:col-span-3">
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Deskripsi Dampak Risiko Potensial <span class="text-rose-500">*</span></label>
                        <textarea name="risk_description" rows="3" required class="w-full text-xs rounded-lg border-slate-200 focus:border-emerald-500 focus:ring-emerald-500 @error('risk_description') border-rose-400 @enderror" placeholder="Deskripsikan cedera fisik atau kerugian operasional yang mungkin terjadi (contoh: Fraktur/remuk jari operator akibat hantaman punch die)...">{{ old('risk_description') }}</textarea>
                        @error('risk_description') <span class="text-[11px] text-rose-600 mt-1 block">{{ $message }}</span> @enderror
                    </div>
                </div>
            </div>

            <!-- Section 2: Penilaian Likelihood & Severity (Kalkulator Live) -->
            <div class="border-b border-slate-100 pb-5">
                <h3 class="text-sm font-bold text-slate-900 mb-4 flex items-center">
                    <span class="w-6 h-6 rounded-full bg-emerald-100 text-emerald-800 inline-flex items-center justify-center text-xs mr-2 font-mono">2</span>
                    Parameter Penilaian Risiko ($Risk = Likelihood \times Severity$)
                </h3>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                    <!-- Likelihood Selector -->
                    <div class="p-4 rounded-xl bg-slate-50 border border-slate-200 space-y-3">
                        <div class="flex items-center justify-between">
                            <label class="text-xs font-bold text-slate-800 uppercase tracking-wide">a. Likelihood / Kemungkinan (L)</label>
                            <span class="text-lg font-black text-slate-900 font-mono" x-text="likelihood"></span>
                        </div>
                        <input type="range" min="1" max="5" step="1" x-model.number="likelihood" name="likelihood" class="w-full accent-emerald-600 cursor-pointer">
                        <div class="text-[11px] text-slate-500 space-y-1">
                            <div :class="likelihood == 1 ? 'font-bold text-emerald-700' : 'text-slate-400'">1 = Sangat Jarang (Pernah terjadi < 1 kali setahun)</div>
                            <div :class="likelihood == 2 ? 'font-bold text-emerald-700' : 'text-slate-400'">2 = Jarang (1-2 kali setahun)</div>
                            <div :class="likelihood == 3 ? 'font-bold text-emerald-700' : 'text-slate-400'">3 = Sedang (1 kali per bulan)</div>
                            <div :class="likelihood == 4 ? 'font-bold text-emerald-700' : 'text-slate-400'">4 = Sering (1 kali per minggu)</div>
                            <div :class="likelihood == 5 ? 'font-bold text-emerald-700' : 'text-slate-400'">5 = Sangat Sering (Terjadi hampir setiap shift/hari)</div>
                        </div>
                    </div>

                    <!-- Severity Selector -->
                    <div class="p-4 rounded-xl bg-slate-50 border border-slate-200 space-y-3">
                        <div class="flex items-center justify-between">
                            <label class="text-xs font-bold text-slate-800 uppercase tracking-wide">b. Severity / Keparahan (S)</label>
                            <span class="text-lg font-black text-slate-900 font-mono" x-text="severity"></span>
                        </div>
                        <input type="range" min="1" max="5" step="1" x-model.number="severity" name="severity" class="w-full accent-rose-600 cursor-pointer">
                        <div class="text-[11px] text-slate-500 space-y-1">
                            <div :class="severity == 1 ? 'font-bold text-rose-700' : 'text-slate-400'">1 = Sangat Ringan (Luka gores kecil, tidak perlu P3K)</div>
                            <div :class="severity == 2 ? 'font-bold text-rose-700' : 'text-slate-400'">2 = Ringan (Perlu P3K, dapat langsung lanjut bekerja)</div>
                            <div :class="severity == 3 ? 'font-bold text-rose-700' : 'text-slate-400'">3 = Sedang (Perawatan medis klinik, hilang hari kerja 1-3 hari)</div>
                            <div :class="severity == 4 ? 'font-bold text-rose-700' : 'text-slate-400'">4 = Berat (Fraktur/patah tulang, rawat inap intensif)</div>
                            <div :class="severity == 5 ? 'font-bold text-rose-700' : 'text-slate-400'">5 = Bencana / Fatal (Cacat tetap, amputasi, atau kematian)</div>
                        </div>
                    </div>
                </div>

                <!-- Live Auto-Calculation Box -->
                <div class="mt-4 p-4 rounded-xl border border-slate-200 bg-emerald-50/40 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                    <div class="flex items-center space-x-3">
                        <div class="w-12 h-12 rounded-xl bg-white border border-slate-200 shadow-sm flex items-center justify-center font-mono font-black text-2xl text-slate-900" x-text="score">
                        </div>
                        <div>
                            <span class="text-[10px] uppercase font-bold text-slate-400 block">Hasil Perhitungan Otomatis:</span>
                            <span class="text-sm font-bold text-slate-900 block">
                                Risk Score = <span x-text="likelihood"></span> (L) &times; <span x-text="severity"></span> (S) = <span x-text="score" class="text-emerald-700"></span>
                            </span>
                            <span class="text-xs text-slate-500" x-text="level.text"></span>
                        </div>
                    </div>

                    <div class="text-right">
                        <span class="text-[10px] uppercase font-bold text-slate-400 block mb-1">Kategori Risiko Otomatis:</span>
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold border shadow-sm" :class="level.class" x-text="level.label"></span>
                    </div>
                </div>
            </div>

            <!-- Section 3: Tindakan Mitigasi & PIC -->
            <div>
                <h3 class="text-sm font-bold text-slate-900 mb-4 flex items-center">
                    <span class="w-6 h-6 rounded-full bg-emerald-100 text-emerald-800 inline-flex items-center justify-center text-xs mr-2 font-mono">3</span>
                    Rencana Pengendalian & Mitigasi Risiko K3
                </h3>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div class="sm:col-span-3">
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Tindakan Pengendalian / Pengaman (Safety Measures)</label>
                        <textarea name="control_measures" rows="3" class="w-full text-xs rounded-lg border-slate-200 focus:border-emerald-500 focus:ring-emerald-500" placeholder="Pemasangan Safety Sensor Light Curtain, Tombol Dua Tangan (Two-Hand Control), SOP Sarung Tangan Kevlar, dll...">{{ old('control_measures') }}</textarea>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Penanggung Jawab (PIC)</label>
                        <input type="text" name="pic" value="{{ old('pic', auth()->user()->name) }}" class="w-full text-xs rounded-lg border-slate-200 focus:border-emerald-500 focus:ring-emerald-500" placeholder="Nama Supervisor / Officer K3">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Status Pengendalian</label>
                        <select name="status" class="w-full text-xs rounded-lg border-slate-200 focus:border-emerald-500 focus:ring-emerald-500">
                            <option value="active" {{ old('status') === 'active' ? 'selected' : '' }}>Aktif / Dalam Pemantauan</option>
                            <option value="controlled" {{ old('status') === 'controlled' ? 'selected' : '' }}>Terkendali (Mitigasi Terpasang)</option>
                            <option value="closed" {{ old('status') === 'closed' ? 'selected' : '' }}>Selesai / Ditutup</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="pt-4 border-t border-slate-100 flex items-center justify-end space-x-3">
                <a href="{{ route('admin.risks.index') }}" class="px-4 py-2 border border-slate-200 text-slate-700 hover:bg-slate-50 text-xs font-semibold rounded-lg transition-colors">
                    Batal
                </a>
                <button type="submit" class="px-5 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-semibold rounded-lg shadow-sm shadow-emerald-500/20 transition-colors">
                    Simpan Analisa Bahaya & Resiko
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
