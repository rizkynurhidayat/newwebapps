# SPESIFIKASI & PEDOMAN PROYEK (PROJECT SPECIFICATION)
## Sistem Pemantauan Produksi & Pengendalian Cacat Produk Berbasis Six Sigma (DMAIC)

Dokumen ini adalah acuan utama (*single source of truth*) arsitektur, ruang lingkup, standar teknologi, formula matematis, dan batasan teknis bagi pengembang dan **AI Agent** yang berkontribusi pada repositori ini. Semua AI Agent **wajib membaca dan mematuhi** panduan dalam dokumen ini agar hasil pengembangan tidak melenceng.

---

## 1. Ringkasan Proyek (Project Overview)
- **Nama Aplikasi**: Sistem Informasi Pemantauan Produksi & Manajemen Cacat Barang (Manufacturing Production & Six Sigma Quality Control System)
- **Tujuan**: Menyediakan platform terpadu berbasis web bagi perusahaan manufaktur (PT) untuk mencatat hasil produksi per batch/lot, memonitor dan mengkategorisasi cacat produk (*defects*), menganalisis akar masalah menggunakan metode **Six Sigma (DMAIC)**, serta mengendalikan proses agar tingkat cacat menurun dan tingkat kualitas (*Sigma Level*) meningkat.
- **Tipe Pengguna (Roles)**:
  1. **Plant Manager / Super Admin**: Memantau dashboard eksekutif Six Sigma, mengelola otorisasi sistem, evaluasi performa mutu pabrik antar lini.
  2. **Quality Control (QC) Inspector / QA**: Menginput data hasil pemeriksaan sampel/batch, mencatat jenis dan jumlah cacat fisik, mengunggah dokumentasi temuan, serta memverifikasi status kelulusan batch.
  3. **Production Supervisor / Engineer**: Membuat jadwal dan mencatat batch produksi, memantau output real-time lini produksi, serta menindaklanjuti tindakan perbaikan cacat (**CAPA**).
  4. **Operator Produksi**: Melihat target output dan instruksi standar kualitas (CTQ).

---

## 2. Tech Stack & Environment
- **Platform Base**: PHP 8.3+ (Web Application)
- **Backend Framework**: **Laravel 12**
- **Database**: **MySQL** (Default Database: `newwebapps`, Testing Database: `newwebapps_testing`, Host: `127.0.0.1`, Port: `3306`, Laragon Environment)
- **Frontend Template**: **Blade Templating Engine**
- **Styling & CSS**: **Tailwind CSS v4** (dikompilasi melalui `@tailwindcss/vite`)
- **Build Tool**: **Vite 8**
- **Interaktivitas Frontend**: **Alpine.js** & **Chart.js** (untuk visualisasi interaktif Diagram Pareto 80/20 dan SPC Control Chart)
- **Testing Framework**: **Pest PHP** (`pestphp/pest`)
- **Code Formatter & Style**: **Laravel Pint** (`vendor/bin/pint --format agent`)
- **AI Agent Suite**: **Laravel Boost** (Tools MCP terintegrasi, Pest, Artisan, DB Inspector)

---

## 3. Ruang Lingkup Proyek & Metodologi Six Sigma (DMAIC)

Sistem mengadopsi siklus peningkatan kualitas **DMAIC (Define - Measure - Analyze - Improve - Control)**:

### 3.1 [DEFINE] Konfigurasi Master Data & Standar Mutu
1. **Katalog Produk Manufaktur (`products`)**:
   - Kode Part / SKU unik (Part Number)
   - Nama Produk & Satuan unit (Pcs, Box, Set)
   - *Defect Opportunities per Unit ($O$)*: Jumlah titik kritis peluang cacat pada 1 unit produk (Critical to Quality / CTQ).
   - Waktu siklus standar (*Standard Cycle Time*).
2. **Lini Produksi & Mesin (`production_lines`)**:
   - Kode & Nama Lini (misal: Line Assembly A, Line Stamping 01, Line Painting)
   - Lokasi / Bay / Workshop
   - Status operasional (Aktif, Maintenance, Nonaktif)
3. **Taksonomi Cacat Produk**:
   - **Kategori Cacat (`defect_categories`)**: Visual, Dimensi, Fungsional, Material, Kemasan/Packaging.
   - **Jenis Cacat (`defect_types`)**: Goresan (*Scratch*), Retak (*Crack*), Penyok (*Dent*), Dimensi Kurang (*Undersize*), Solder Lepas (*Cold Joint*), Warna Belang, dsb.
   - **Tingkat Keparahan (*Severity*)**: `Minor`, `Major`, `Critical`.
   - **Kategori Default Ishikawa (5M+1E)**: Man, Machine, Method, Material, Measurement, Environment.

### 3.2 [MEASURE] Pendataan Produksi & Inspeksi Mutu
1. **Batch / Lot Produksi (`production_batches`)**:
   - Nomor Lot/Batch unik (format: `LOT-YYYYMM-XXXX`)
   - Tanggal Produksi, Shift Kerja (`Shift 1`, `Shift 2`, `Shift 3`)
   - Target Qty vs Realisasi Qty Produksi
   - Status Batch: `Draft`, `In Production`, `Completed`, `Cancelled`
2. **Pemeriksaan Kualitas / QC Inspection (`quality_inspections`)**:
   - Nomor Inspeksi unik (format: `QC-YYYYMM-XXXX`)
   - Tahap Pemeriksaan: `Incoming`, `In-Process`, `Final QA`
   - Ukuran Sampel Diperiksa ($N$)
   - Jumlah Unit Lulus ($N_{passed}$) dan Jumlah Unit Cacat ($N_{defective}$)
   - Total Akumulasi Titik Cacat Ditemukan ($D$)
   - Detail temuan per jenis cacat (`inspection_defects`) beserta catatan & foto bukti.
3. **Perhitungan Matematis Otomatis (Otomasi Engine Six Sigma)**:
   - **$DPU$ (Defects Per Unit)**:
     $$DPU = \frac{D}{N}$$
   - **$DPO$ (Defects Per Opportunity)**:
     $$DPO = \frac{D}{N \times O}$$
   - **$DPMO$ (Defects Per Million Opportunities)**:
     $$DPMO = DPO \times 1.000.000$$
   - **Yield (Process Yield %)**:
     $$Yield = \frac{N_{passed}}{N} \times 100\%$$
   - **Sigma Level (Tingkat Mutu Sigma)**:
     Dihitung menggunakan konversi standar industri dengan toleransi pergeseran $1.5\sigma$ shift:
     $$\text{Sigma Level} = \text{NormSInv}(1 - DPO) + 1.5$$
     *(Contoh: DPMO $\le 3.4 \rightarrow 6.0\sigma$, DPMO $6.210 \rightarrow 4.0\sigma$, DPMO $66.807 \rightarrow 3.0\sigma$)*.

### 3.3 [ANALYZE] Analisis Akar Penyebab Cacat
1. **Diagram Pareto Cacat (Prinsip 80/20)**:
   - Mengelompokkan dan mengurutkan cacat dari frekuensi tertinggi ke terendah.
   - Menghitung persentase kontribusi dan persentase kumulatif untuk menemukan jenis cacat "vital few" (20% jenis cacat penyebab 80% kerusakan).
2. **Diagram Tulang Ikan (Ishikawa / Fishbone 5M+1E)**:
   - Matriks klasifikasi penyebab cacat:
     - **Man (Manusia)**: Kelelahan operator, kurang pelatihan, kelalaian SOP.
     - **Machine (Mesin)**: Kalibrasi aus, temperatur fluktuatif, getaran mesin.
     - **Method (Metode)**: Parameter setting tidak tepat, kecepatan conveyor berlebih.
     - **Material (Bahan)**: Kualitas bahan baku supplier tidak konsisten, cacat bawaan.
     - **Measurement (Pengukuran)**: Alat ukur tidak terkalibrasi, kesalahan paralaks.
     - **Environment (Lingkungan)**: Debu, kelembaban ruangan tinggi, penerangan kurang.

### 3.4 [IMPROVE] Tindakan Korektif & Preventif (CAPA)
1. **Modul CAPA (`capa_actions`)**:
   - Nomor CAPA unik (format: `CAPA-YYYYMM-XXXX`)
   - Objek jenis cacat yang ditargetkan
   - Pernyataan Masalah (*Problem Statement*)
   - Analisis Akar Masalah (*5-Why Analysis*)
   - Rencana Tindakan Perbaikan (*Corrective Action*)
   - Rencana Tindakan Pencegahan Berulang (*Preventive Action*)
   - Penanggung Jawab (PIC) & Target Tanggal Penyelesaian
   - Status: `Open`, `In Progress`, `Implemented`, `Verified`, `Closed`

### 3.5 [CONTROL] Pengendalian Proses Statistik (SPC p-Chart)
1. **Statistical Process Control (p-Chart)**:
   - Memantau proporsi cacat ($p_i$) per batch produksi secara kronologis.
   - Garis Tengah (*Center Line / CL*): $\bar{p} = \frac{\sum d_i}{\sum n_i}$
   - Batas Kendali Atas (*Upper Control Limit / UCL*):
     $$UCL = \bar{p} + 3 \sqrt{\frac{\bar{p}(1-\bar{p})}{\bar{n}}}$$
   - Batas Kendali Bawah (*Lower Control Limit / LCL*):
     $$LCL = \max\left(0, \bar{p} - 3 \sqrt{\frac{\bar{p}(1-\bar{p})}{\bar{n}}}\right)$$
   - Peringatan instan (*Out-of-Control Warning*) jika ada batch yang proporsi cacatnya melampaui UCL.

---

## 4. Batasan Teknis & Aturan Baku untuk AI Agent (Strict Guardrails)

AI Agent **DILARANG MELENCENG** dari pedoman berikut:

### 4.1 Arsitektur & Prinsip Pengembangan ("The Laravel Way")
- Gunakan perintah artisan: `php artisan make:model`, `make:controller`, `make:migration`, `make:request`, `make:seeder`.
- Gunakan **Form Request** untuk semua validasi data input.
- Gunakan **Resource Controller** dengan konvensi RESTful standar.
- Gunakan **Database Transactions (`DB::transaction`)** pada saat menyimpan hasil inspeksi QC beserta baris temuan cacatnya dan pembaruan status batch.
- Seluruh logika kalkulasi Six Sigma (DPU, DPMO, Sigma, Pareto, Control Limits) diisolasi dalam **Service Class** tersendiri: `App\Services\SixSigmaCalculatorService`.

### 4.2 Batasan Frontend & UI
- **Wajib menggunakan Blade + Tailwind CSS v4 + Alpine.js**.
- Diagram interaktif (Pareto Chart & p-Chart SPC) menggunakan library **Chart.js** via canvas sederhana tanpa ketergantungan framework SPA berat.
- Gunakan tema pabrik/industrial modern yang bersih (indikator warna: Hijau untuk Passed/High Sigma $\ge 4.0\sigma$, Kuning untuk Conditional/Warning $3.0 - 3.9\sigma$, Merah untuk Rejected/Critical $< 3.0\sigma$).

### 4.3 Kualitas & Testing
- Setiap file PHP yang dibuat/dimodifikasi wajib diformat dengan:
  ```bash
  vendor/bin/pint --format agent
  ```
- Seluruh logika Six Sigma dan alur inspeksi wajib diuji dengan **Pest PHP**:
  ```bash
  php artisan test --compact
  ```
- Setiap perkembangan pekerjaan wajib dicatat secara teratur di [PROGRESS.md](file:///c:/laragon/www/newwebapps/PROGRESS.md).
