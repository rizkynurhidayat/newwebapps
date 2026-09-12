# Sistem Pemantauan Produksi & Pengendalian Cacat Produk (Six Sigma DMAIC)

Aplikasi berbasis web untuk memonitor proses produksi barang di sebuah perusahaan manufaktur (PT) dan menurunkan tingkat cacat (*defect*) menggunakan metode **Six Sigma (DMAIC: Define, Measure, Analyze, Improve, Control)**.

---

## 🌟 Fitur Utama

- **1. Define (Spesifikasi & Master Data)**
  - Katalog Produk & nomor part (Part Number) lengkap dengan penentuan *Critical to Quality (CTQ)* dan jumlah peluang cacat (*Defect Opportunities per Unit*).
  - Manajemen Lini Produksi & Mesin perakitan.
  - Taksonomi Cacat: Kategori cacat (Visual, Dimensi, Fungsional, Material, Packaging) dan jenis cacat spesifik dengan tingkat keparahan (*Minor, Major, Critical*).
- **2. Measure (Pencatatan Batch & Perhitungan Metrik Six Sigma)**
  - Pencatatan Batch/Lot Produksi berdasarkan tanggal, shift (Shift 1/2/3), target qty vs realisasi.
  - Formulir Inspeksi Kualitas (QC Inspection): Ukuran sampel ($N$), jumlah lolos, jumlah unit cacat, dan total titik cacat ($D$).
  - Perhitungan matematis otomatis:
    - **DPU (Defects Per Unit)**: $D / N$
    - **DPO (Defects Per Opportunity)**: $D / (N \times O)$
    - **DPMO (Defects Per Million Opportunities)**: $DPO \times 1.000.000$
    - **Process Yield (%)**: $(N_{passed} / N) \times 100\%$
    - **Sigma Level**: Tingkat mutu kualitas (1.0 $\sigma$ s/d 6.0 $\sigma$ dengan $1.5\sigma$ shift).
- **3. Analyze (Analisis Akar Masalah)**
  - **Diagram Pareto (80/20 Rule)** interaktif untuk mengidentifikasi 20% jenis cacat yang menyumbang 80% permasalahan mutu.
  - **Diagram Fishbone / Ishikawa (5M+1E)**: Pengelompokan akar penyebab berdasarkan faktor *Man, Machine, Method, Material, Measurement, Environment*.
- **4. Improve (Tindakan Korektif & Preventif / CAPA)**
  - Dokumentasi tindakan penanggulangan cacat (Corrective and Preventive Actions / CAPA) dengan metode 5-Why Analysis, penanggung jawab (PIC), target tanggal, dan verifikasi efektivitas.
- **5. Control (Statistical Process Control / SPC)**
  - Grafik kendali kualitas **p-Chart** untuk memantau proporsi cacat per batch beserta batas kendali atas (*Upper Control Limit / UCL*), garis tengah (*Center Line / CL*), dan batas kendali bawah (*Lower Control Limit / LCL*).

---

## 🛠️ Tech Stack

- **Framework**: Laravel 12 (PHP 8.3+)
- **Database**: MySQL (`newwebapps`)
- **Frontend**: Blade + Tailwind CSS v4 + Alpine.js + Chart.js
- **Testing**: Pest PHP
- **Code Formatter**: Laravel Pint

---

## 🚀 Cara Menjalankan Aplikasi

1. **Jalankan Laragon / MySQL**.
2. **Setup Database & Migrasi**:
   ```bash
   php artisan migrate
   php artisan db:seed
   ```
3. **Build Frontend Assets**:
   ```bash
   npm run build
   ```
4. **Jalankan Server Lokal**:
   ```bash
   php artisan serve
   ```
   Buka browser di: `http://127.0.0.1:8000`

---

## 🧪 Menjalankan Pengujian Otomatis

```bash
php artisan test --compact
```
