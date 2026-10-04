# ENTITY-RELATIONSHIP DIAGRAM (ERD)
## Sistem Pemantauan Produksi & Pengendalian Cacat Produk (Six Sigma DMAIC)
### Kasus Manufaktur: Bracket Seat Leg Mobil & Analisa Resiko Mesin Stamping Press

Dokumen ini memuat diagram **Entity-Relationship Diagram (ERD)** yang disederhanakan (*simple and business-focused*), khusus mencakup entitas proses bisnis manufaktur, pengujian mutu Six Sigma, tindakan perbaikan (CAPA), dan keselamatan kerja (K3) mesin press.

> [!NOTE]
> **Pengecualian Tabel Internal Framework**:
> Sesuai standar pemodelan basis data proses bisnis, tabel-tabel bawaan framework Laravel (seperti `migrations`, `cache`, `cache_locks`, `jobs`, `job_batches`, `failed_jobs`, `sessions`, dan `password_reset_tokens`) **tidak diikutsertakan** dalam diagram ini agar model konseptual dan logis tetap ringkas dan terfokus pada fungsionalitas aplikasi.

---

## 1. Visualisasi Diagram ERD (Mermaid)

```mermaid
erDiagram
    USERS ||--o{ PRODUCTION_BATCHES : "mengawasi (supervisor)"
    USERS ||--o{ QUALITY_INSPECTIONS : "memeriksa (inspector)"
    USERS ||--o{ CAPA_ACTIONS : "ditugaskan_ke (pic)"

    PRODUCTS ||--o{ PRODUCTION_BATCHES : "diproduksi_pada"
    PRODUCTION_LINES ||--o{ PRODUCTION_BATCHES : "diproses_di"

    PRODUCTION_BATCHES ||--o{ QUALITY_INSPECTIONS : "diuji_pada"

    QUALITY_INSPECTIONS ||--o{ INSPECTION_DEFECTS : "memiliki_temuan"
    DEFECT_TYPES ||--o{ INSPECTION_DEFECTS : "ditemukan_sebagai"
    DEFECT_CATEGORIES ||--o{ DEFECT_TYPES : "mengelompokkan"

    DEFECT_TYPES ||--o{ CAPA_ACTIONS : "memicu_perbaikan"
    PRODUCTION_LINES ||--o{ MACHINE_RISK_ASSESSMENTS : "dianalisis_bahaya"

    USERS {
        bigint id PK
        string name "Nama Pengguna"
        string role "Admin / Supervisor / QC Staff"
        string department "Departemen Kerja"
    }

    PRODUCTS {
        bigint id PK
        string part_number "Nomor Part Unik"
        string name "Nama Part (Bracket Seat Leg)"
        string raw_material "Bahan Baku Plat Baja"
        int defect_opportunities "Peluang Cacat (CTQ)"
    }

    PRODUCTION_LINES {
        bigint id PK
        string line_code "Kode Lini Mesin"
        string name "Nama Mesin Stamping"
        string status "Status Operasional"
    }

    PRODUCTION_BATCHES {
        bigint id PK
        string batch_number "Nomor Lot (LOT-YYYYMM-XXXX)"
        bigint product_id FK "Relasi ke Produk"
        bigint production_line_id FK "Relasi ke Lini Mesin"
        bigint supervisor_id FK "Relasi ke Supervisor"
        date production_date "Tanggal Produksi"
        string shift "Shift Kerja (1 / 2 / 3)"
        int target_qty "Target Produksi (Pcs)"
        int actual_qty "Realisasi Output Lulus"
        string status "Draft / In Production / Completed"
    }

    QUALITY_INSPECTIONS {
        bigint id PK
        string inspection_number "Nomor QC (QC-YYYYMM-XXXX)"
        bigint production_batch_id FK "Relasi ke Batch"
        bigint inspector_id FK "Relasi ke Petugas QC"
        int inspection_year "Tahun Pemeriksaan"
        int inspection_month "Bulan Pemeriksaan (1-12)"
        int inspection_week "Minggu Pemeriksaan (1-4)"
        datetime inspection_time "Waktu Pemeriksaan Riil"
        string inspection_stage "Incoming / In-Process / Final QA"
        int sample_size_inspected "Ukuran Sampel (N)"
        int defective_units_qty "Unit Cacat (Defective)"
        decimal dpu "Defects Per Unit"
        decimal dpmo "Defects Per Million Opps"
        decimal sigma_level "Tingkat Sigma (Sigma Level)"
        decimal yield_percentage "Process Yield %"
        string result_status "Passed / Conditional / Rejected"
    }

    DEFECT_CATEGORIES {
        bigint id PK
        string code "Kode Kategori (Visual/Dimensi)"
        string name "Nama Kategori"
    }

    DEFECT_TYPES {
        bigint id PK
        bigint defect_category_id FK "Relasi ke Kategori"
        string code "Kode Cacat (DEF-XXX)"
        string name "Nama Cacat (excrap/blank minus)"
        string severity "Minor / Major / Critical"
        string default_5m_category "Kategori Awal 5M+1E"
    }

    INSPECTION_DEFECTS {
        bigint id PK
        bigint quality_inspection_id FK "Relasi ke Inspeksi QC"
        bigint defect_type_id FK "Relasi ke Jenis Cacat"
        int defect_qty "Jumlah Titik Cacat"
        string root_cause_category "Faktor 5M+1E (Man/Machine/dll)"
        string root_cause_notes "Catatan Akar Masalah"
    }

    CAPA_ACTIONS {
        bigint id PK
        string capa_number "Nomor Tiket (CAPA-YYYYMM-XXXX)"
        bigint defect_type_id FK "Relasi ke Jenis Cacat Dominan"
        bigint assigned_to_user_id FK "Relasi ke PIC Perbaikan"
        string title "Judul Tindakan Perbaikan"
        text corrective_action "Tindakan Korektif (Corrective)"
        text preventive_action "Tindakan Pencegahan (Preventive)"
        string status "Open / In Progress / Closed"
    }

    MACHINE_RISK_ASSESSMENTS {
        bigint id PK
        string hazard_code "Kode Bahaya (HAZ-XXX)"
        string hazard_name "Nama Bahaya Mesin Press"
        string machine_area "Area Bahaya Mesin"
        int likelihood "Kemungkinan Terjadi (1-5)"
        int severity "Tingkat Keparahan (1-5)"
        int risk_score "Skor Resiko (L x S)"
        string risk_level "Low / Medium / High / Extreme"
        string status "Status Pengendalian Resiko"
    }
```

---

## 2. Kamus Relasi Antar Tabel (Relationship & Cardinality)

| No | Entitas Asal | Relasi | Entitas Tujuan | Foreign Key | Keterangan Logika Bisnis |
|:--:|---|:---:|---|---|---|
| **1** | `PRODUCTS` | **1 : N** | `PRODUCTION_BATCHES` | `product_id` | Satu part produk diproduksi dalam banyak lot/batch produksi. |
| **2** | `PRODUCTION_LINES` | **1 : N** | `PRODUCTION_BATCHES` | `production_line_id` | Satu lini/mesin stamping menjalankan banyak perintah batch. |
| **3** | `USERS` | **1 : N** | `PRODUCTION_BATCHES` | `supervisor_id` | Satu supervisor mengawasi dan menjadwalkan banyak batch produksi. |
| **4** | `PRODUCTION_BATCHES` | **1 : N** | `QUALITY_INSPECTIONS` | `production_batch_id` | Satu lot produksi dapat diuji berkala (tahap In-Process hingga Final QA). |
| **5** | `USERS` | **1 : N** | `QUALITY_INSPECTIONS` | `inspector_id` | Satu staf QC inspector melakukan banyak sesi pengujian mutu. |
| **6** | `QUALITY_INSPECTIONS` | **1 : N** | `INSPECTION_DEFECTS` | `quality_inspection_id` | Satu pengujian QC dapat menemukan beberapa item rincian titik cacat fisik. |
| **7** | `DEFECT_CATEGORIES` | **1 : N** | `DEFECT_TYPES` | `defect_category_id` | Satu kategori cacat (misal: Dimensi) membawahi banyak jenis cacat spesifik. |
| **8** | `DEFECT_TYPES` | **1 : N** | `INSPECTION_DEFECTS` | `defect_type_id` | Suatu jenis cacat tertentu dapat ditemukan di berbagai transaksi inspeksi. |
| **9** | `DEFECT_TYPES` | **1 : N** | `CAPA_ACTIONS` | `defect_type_id` | Jenis cacat dominan (hasil analisis Pareto) memicu tindakan perbaikan CAPA. |
| **10**| `USERS` | **1 : N** | `CAPA_ACTIONS` | `assigned_to_user_id` | Satu user/engineer ditugaskan sebagai PIC penyelesaian tiket CAPA. |
| **11**| `PRODUCTION_LINES` | **1 : N** | `MACHINE_RISK_ASSESSMENTS` | *Konteks Area Lini* | Mesin stamping press pada lini produksi dianalisis potensi bahaya K3-nya. |

---

## 3. Ringkasan Modul & Peran Fungsional

1. **Modul Master Data Manufaktur**:
   - `products`: Menyimpan spesifikasi teknis part otomotif (Bracket Seat Leg), bahan plat baja SPCC/SPHC, dan nilai $O$ (*Defect Opportunities per Unit*).
   - `production_lines`: Mengelola inventaris lini mesin stamping press 250 Ton.
   - `defect_categories` & `defect_types`: Mengelola taksonomi cacat dan 5 CTQ kritis (*excrap, blank minus, trim minus, deformasi, karat*).

2. **Modul Operasional & Six Sigma DMAIC**:
   - `production_batches` (*Define & Measure*): Mencatat lot kerja dan target output.
   - `quality_inspections` (*Measure*): Menyimpan sampling mutu dengan dropdown bulan & minggu keberapa (1-4), menghitung otomatis $DPU$, $DPMO$, $Yield$, dan $Sigma\ Level$.
   - `inspection_defects` (*Analyze*): Merekam temuan cacat fisik dan pengelompokan faktor diagram tulang ikan (Fishbone 5M+1E Ishikawa).
   - `capa_actions` (*Improve & Control*): Mengelola tiket tindakan pencegahan dan perbaikan berkala.

3. **Modul Keselamatan & Kesehatan Kerja (K3)**:
   - `machine_risk_assessments`: Mengidentifikasi titik jepit, bahaya scrap, dan pengendalian bahaya mesin press dengan formula matriks $Risk = Likelihood \times Severity$.
