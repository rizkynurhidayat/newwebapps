<?php

namespace Database\Seeders;

use App\Enums\BatchStatus;
use App\Enums\CapaStatus;
use App\Enums\DefectSeverity;
use App\Enums\InspectionStage;
use App\Enums\IshikawaCategory;
use App\Enums\ProductionShift;
use App\Enums\UserRole;
use App\Models\CapaAction;
use App\Models\DefectCategory;
use App\Models\DefectType;
use App\Models\MachineRiskAssessment;
use App\Models\Product;
use App\Models\ProductionBatch;
use App\Models\ProductionLine;
use App\Models\User;
use App\Services\ProductionService;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class ManufacturingSeeder extends Seeder
{
    public function run(ProductionService $productionService): void
    {
        // 1. Ensure Standard Manufacturing Users Exist
        $admin = User::firstOrCreate(
            ['email' => 'admin@company.com'],
            [
                'name' => 'Bambang Soedirgo (Plant Manager)',
                'password' => Hash::make('password'),
                'role' => UserRole::Admin,
                'phone' => '081122334455',
                'department' => 'Plant Operations & Stamping Division',
                'is_active' => true,
            ]
        );

        $qc = User::firstOrCreate(
            ['email' => 'staff@company.com'],
            [
                'name' => 'Budi Santoso (Lead QC Inspector)',
                'password' => Hash::make('password'),
                'role' => UserRole::Staff,
                'phone' => '081234567890',
                'department' => 'Quality Assurance & Six Sigma QC',
                'is_active' => true,
            ]
        );

        $supervisor = User::firstOrCreate(
            ['email' => 'employee@company.com'],
            [
                'name' => 'Hendra Wijaya (Production Supervisor)',
                'password' => Hash::make('password'),
                'role' => UserRole::Employee,
                'phone' => '081987654321',
                'department' => 'Line Stamping Press & Forming',
                'is_active' => true,
            ]
        );

        // 2. Seed Products (Spesifik: Bracket Seat Leg Mobil)
        $products = [
            [
                'part_number' => 'BSL-7110-RH',
                'name' => 'Bracket Seat Leg Front RH (Kaki Kursi Depan Kanan)',
                'description' => 'Komponen struktural bracket penopang kaki kursi mobil bagian depan kanan, diproduksi melalui proses stamping press & piercing presisi.',
                'raw_material' => 'Plat Baja SPCC ketebalan 2.0 mm',
                'unit' => 'pcs',
                'defect_opportunities_per_unit' => 5, // 5 CTQ Utama
                'standard_cycle_time' => 18.00,
                'is_active' => true,
            ],
            [
                'part_number' => 'BSL-7120-LH',
                'name' => 'Bracket Seat Leg Front LH (Kaki Kursi Depan Kiri)',
                'description' => 'Komponen struktural bracket penopang kaki kursi mobil bagian depan kiri dengan toleransi geometris presisi tinggi.',
                'raw_material' => 'Plat Baja SPCC ketebalan 2.0 mm',
                'unit' => 'pcs',
                'defect_opportunities_per_unit' => 5, // 5 CTQ Utama
                'standard_cycle_time' => 18.00,
                'is_active' => true,
            ],
            [
                'part_number' => 'BSL-8210-RR',
                'name' => 'Bracket Seat Leg Rear Inner (Kaki Kursi Belakang)',
                'description' => 'Komponen bracket penguat kaki kursi bagian belakang mobil berbahan plat baja tebal tahan beban dinamis.',
                'raw_material' => 'Plat Baja SPHC ketebalan 2.3 mm',
                'unit' => 'pcs',
                'defect_opportunities_per_unit' => 5, // 5 CTQ Utama
                'standard_cycle_time' => 22.00,
                'is_active' => true,
            ],
        ];

        $productModels = [];
        foreach ($products as $p) {
            $productModels[$p['part_number']] = Product::updateOrCreate(
                ['part_number' => $p['part_number']],
                $p
            );
        }

        // 3. Seed Production Lines (Mesin Stamping Press)
        $lines = [
            [
                'line_code' => 'LINE-STAMP-01',
                'name' => 'Mesin Stamping Press 250 Ton (Blanking & Piercing)',
                'location' => 'Workshop Stamping Bay A',
                'description' => 'Lini mesin cetak tekan 250 ton untuk proses blanking potongan plat awal dan pembuatan lubang baut (piercing).',
                'status' => 'operational',
            ],
            [
                'line_code' => 'LINE-STAMP-02',
                'name' => 'Mesin Stamping Press 300 Ton (Bending & Forming)',
                'location' => 'Workshop Stamping Bay B',
                'description' => 'Lini mesin press berkapasitas 300 ton dengan die cushion hidrolik untuk pembentukan lekukan sudut bracket.',
                'status' => 'operational',
            ],
            [
                'line_code' => 'LINE-STAMP-03',
                'name' => 'Mesin Stamping Press 150 Ton (Trimming & Deburring)',
                'location' => 'Workshop Stamping Bay C',
                'description' => 'Lini pemotongan sisa tepi plat (trimming) dan penghalusan permukaan dari geram tajam.',
                'status' => 'operational',
            ],
        ];

        $lineModels = [];
        foreach ($lines as $l) {
            $lineModels[$l['line_code']] = ProductionLine::updateOrCreate(
                ['line_code' => $l['line_code']],
                $l
            );
        }

        // 4. Seed 5 Jenis Cacat CTQ (Critical to Quality) Pabrik Bracket Seat Leg
        $catStamping = DefectCategory::firstOrCreate(
            ['code' => 'CAT-STAMP'],
            [
                'name' => 'Stamping & Press Metal Defects',
                'description' => 'Kategori cacat proses cetak stamping press pada komponen plat bracket kursi mobil.',
            ]
        );

        $ctqDefects = [
            [
                'code' => 'DEF-EXC',
                'name' => 'excrap',
                'defect_category_id' => $catStamping->id,
                'severity' => DefectSeverity::Major,
                'default_5m_category' => IshikawaCategory::Machine,
                'description' => 'Sisa potongan scrap (slug mark) menempel atau tertekan pada plat produk saat proses stamping press.',
            ],
            [
                'code' => 'DEF-BLM',
                'name' => 'blank minus',
                'defect_category_id' => $catStamping->id,
                'severity' => DefectSeverity::Major,
                'default_5m_category' => IshikawaCategory::Material,
                'description' => 'Dimensi lembaran potongan blank kurang (tekor) dari batas toleransi standar gambar teknik.',
            ],
            [
                'code' => 'DEF-TRM',
                'name' => 'trim minus',
                'defect_category_id' => $catStamping->id,
                'severity' => DefectSeverity::Major,
                'default_5m_category' => IshikawaCategory::Method,
                'description' => 'Hasil pemotongan tepi (trimming) terlalu ke dalam sehingga profil bracket berkurang dari spesifikasi.',
            ],
            [
                'code' => 'DEF-DEF',
                'name' => 'deformasi',
                'defect_category_id' => $catStamping->id,
                'severity' => DefectSeverity::Critical,
                'default_5m_category' => IshikawaCategory::Machine,
                'description' => 'Deformasi bentuk seperti plat melintir, sudut bending tidak 90 derajat, atau springback berlebih.',
            ],
            [
                'code' => 'DEF-RST',
                'name' => 'karat',
                'defect_category_id' => $catStamping->id,
                'severity' => DefectSeverity::Critical,
                'default_5m_category' => IshikawaCategory::Environment,
                'description' => 'Korosi atau bercak karat pada permukaan plat besi akibat kelembaban atau pelumas anti-karat tidak merata.',
            ],
        ];

        $defectTypeModels = [];
        foreach ($ctqDefects as $df) {
            $defectTypeModels[$df['code']] = DefectType::updateOrCreate(
                ['code' => $df['code']],
                [
                    'defect_category_id' => $df['defect_category_id'],
                    'name' => $df['name'],
                    'severity' => $df['severity'],
                    'default_5m_category' => $df['default_5m_category'],
                    'is_active' => true,
                ]
            );
        }

        // 5. Seed Production Batches & QC Inspections (Past 10 Days)
        $batchConfigs = [
            [
                'batch_number' => 'LOT-202609-0001',
                'product' => 'BSL-7110-RH',
                'line' => 'LINE-STAMP-01',
                'shift' => ProductionShift::Shift1,
                'days_ago' => 9,
                'target_qty' => 500,
                'actual_qty' => 494,
                'sample_size' => 100,
                'defects' => [
                    ['code' => 'DEF-EXC', 'qty' => 4, 'cat' => IshikawaCategory::Machine, 'notes' => 'Slug scrap menempel pada die punch'],
                    ['code' => 'DEF-BLM', 'qty' => 2, 'cat' => IshikawaCategory::Material, 'notes' => 'Ujung plat terpotong miring saat shearing'],
                ],
            ],
            [
                'batch_number' => 'LOT-202609-0002',
                'product' => 'BSL-7110-RH',
                'line' => 'LINE-STAMP-01',
                'shift' => ProductionShift::Shift2,
                'days_ago' => 8,
                'target_qty' => 500,
                'actual_qty' => 495,
                'sample_size' => 100,
                'defects' => [
                    ['code' => 'DEF-EXC', 'qty' => 3, 'cat' => IshikawaCategory::Machine, 'notes' => 'Vacuum scrap ejector tersumbat gram'],
                    ['code' => 'DEF-TRM', 'qty' => 2, 'cat' => IshikawaCategory::Method, 'notes' => 'Guide pin trimming bergeser 0.3mm'],
                ],
            ],
            [
                'batch_number' => 'LOT-202609-0003',
                'product' => 'BSL-7120-LH',
                'line' => 'LINE-STAMP-02',
                'shift' => ProductionShift::Shift1,
                'days_ago' => 7,
                'target_qty' => 400,
                'actual_qty' => 392,
                'sample_size' => 80,
                'defects' => [
                    ['code' => 'DEF-DEF', 'qty' => 3, 'cat' => IshikawaCategory::Machine, 'notes' => 'Springback berlebih akibat tekanan die cushion turun'],
                    ['code' => 'DEF-EXC', 'qty' => 5, 'cat' => IshikawaCategory::Machine, 'notes' => 'Goresan slug scrap pada radius tekuk'],
                ],
            ],
            [
                'batch_number' => 'LOT-202609-0004',
                'product' => 'BSL-7120-LH',
                'line' => 'LINE-STAMP-02',
                'shift' => ProductionShift::Shift2,
                'days_ago' => 6,
                'target_qty' => 400,
                'actual_qty' => 396,
                'sample_size' => 80,
                'defects' => [
                    ['code' => 'DEF-EXC', 'qty' => 3, 'cat' => IshikawaCategory::Machine, 'notes' => 'Baret halus bekas scrap tertindih'],
                    ['code' => 'DEF-RST', 'qty' => 1, 'cat' => IshikawaCategory::Environment, 'notes' => 'Bercak oksidasi tipis di tepi plat'],
                ],
            ],
            [
                'batch_number' => 'LOT-202609-0005', // Out-of-control demonstration batch
                'product' => 'BSL-8210-RR',
                'line' => 'LINE-STAMP-01',
                'shift' => ProductionShift::Shift1,
                'days_ago' => 5,
                'target_qty' => 450,
                'actual_qty' => 432,
                'sample_size' => 100,
                'defects' => [
                    ['code' => 'DEF-EXC', 'qty' => 8, 'cat' => IshikawaCategory::Machine, 'notes' => 'Stripper die aus parah, scrap menumpuk'],
                    ['code' => 'DEF-BLM', 'qty' => 5, 'cat' => IshikawaCategory::Material, 'notes' => 'Coil feeder terselip saat feeding otomatis'],
                    ['code' => 'DEF-TRM', 'qty' => 3, 'cat' => IshikawaCategory::Method, 'notes' => 'Salah stopper setting'],
                ],
            ],
            [
                'batch_number' => 'LOT-202609-0006',
                'product' => 'BSL-7110-RH',
                'line' => 'LINE-STAMP-03',
                'shift' => ProductionShift::Shift1,
                'days_ago' => 4,
                'target_qty' => 500,
                'actual_qty' => 496,
                'sample_size' => 100,
                'defects' => [
                    ['code' => 'DEF-EXC', 'qty' => 2, 'cat' => IshikawaCategory::Machine, 'notes' => 'Bekas serpihan scrap minor'],
                    ['code' => 'DEF-DEF', 'qty' => 2, 'cat' => IshikawaCategory::Machine, 'notes' => 'Sudut tekuk melintir 1.5 derajat'],
                ],
            ],
            [
                'batch_number' => 'LOT-202609-0007',
                'product' => 'BSL-8210-RR',
                'line' => 'LINE-STAMP-02',
                'shift' => ProductionShift::Shift1,
                'days_ago' => 3,
                'target_qty' => 350,
                'actual_qty' => 346,
                'sample_size' => 70,
                'defects' => [
                    ['code' => 'DEF-EXC', 'qty' => 3, 'cat' => IshikawaCategory::Machine, 'notes' => 'Tanda slug scrap di area flange'],
                    ['code' => 'DEF-RST', 'qty' => 1, 'cat' => IshikawaCategory::Environment, 'notes' => 'Minyak anti karat kurang merata di pojok'],
                ],
            ],
            [
                'batch_number' => 'LOT-202609-0008',
                'product' => 'BSL-7110-RH',
                'line' => 'LINE-STAMP-01',
                'shift' => ProductionShift::Shift2,
                'days_ago' => 2,
                'target_qty' => 500,
                'actual_qty' => 497,
                'sample_size' => 100,
                'defects' => [
                    ['code' => 'DEF-TRM', 'qty' => 2, 'cat' => IshikawaCategory::Method, 'notes' => 'Pemotongan trimming kurang 0.2mm'],
                    ['code' => 'DEF-EXC', 'qty' => 1, 'cat' => IshikawaCategory::Machine, 'notes' => 'Slug mark ringan'],
                ],
            ],
            [
                'batch_number' => 'LOT-202609-0009',
                'product' => 'BSL-7120-LH',
                'line' => 'LINE-STAMP-02',
                'shift' => ProductionShift::Shift1,
                'days_ago' => 1,
                'target_qty' => 400,
                'actual_qty' => 397,
                'sample_size' => 80,
                'defects' => [
                    ['code' => 'DEF-EXC', 'qty' => 2, 'cat' => IshikawaCategory::Machine, 'notes' => 'Scrap tertindih di permukaan luar'],
                    ['code' => 'DEF-BLM', 'qty' => 1, 'cat' => IshikawaCategory::Material, 'notes' => 'Panjang lembaran kurang 0.5mm'],
                ],
            ],
            [
                'batch_number' => 'LOT-202609-0010',
                'product' => 'BSL-7110-RH',
                'line' => 'LINE-STAMP-01',
                'shift' => ProductionShift::Shift1,
                'days_ago' => 0,
                'target_qty' => 500,
                'actual_qty' => 496,
                'sample_size' => 100,
                'defects' => [
                    ['code' => 'DEF-EXC', 'qty' => 3, 'cat' => IshikawaCategory::Machine, 'notes' => 'Sisa scrap terikut pada plat'],
                    ['code' => 'DEF-DEF', 'qty' => 1, 'cat' => IshikawaCategory::Machine, 'notes' => 'Deformasi minor pada lubang mounting'],
                ],
            ],
        ];

        foreach ($batchConfigs as $b) {
            $prod = $productModels[$b['product']];
            $ln = $lineModels[$b['line']];
            $prodDate = Carbon::today()->subDays($b['days_ago']);

            $batch = ProductionBatch::updateOrCreate(
                ['batch_number' => $b['batch_number']],
                [
                    'product_id' => $prod->id,
                    'production_line_id' => $ln->id,
                    'supervisor_id' => $supervisor->id,
                    'production_date' => $prodDate,
                    'shift' => $b['shift'],
                    'target_qty' => $b['target_qty'],
                    'actual_qty' => $b['actual_qty'],
                    'status' => BatchStatus::Completed,
                    'notes' => 'Batch produksi stamping Bracket Seat Leg shift '.$b['shift']->value,
                ]
            );

            // Record inspection via ProductionService to accurately compute Six Sigma formulas
            $defectsPayload = [];
            $totalDefectUnits = 0;
            foreach ($b['defects'] as $df) {
                $type = $defectTypeModels[$df['code']] ?? null;
                if ($type) {
                    $defectsPayload[] = [
                        'defect_type_id' => $type->id,
                        'defect_qty' => $df['qty'],
                        'root_cause_category' => $df['cat']->value,
                        'root_cause_notes' => $df['notes'],
                    ];
                    $totalDefectUnits += $df['qty'];
                }
            }

            // Check if inspection already exists
            if (! $batch->qualityInspections()->exists()) {
                $productionService->recordInspection([
                    'production_batch_id' => $batch->id,
                    'inspector_id' => $qc->id,
                    'inspection_time' => $prodDate->copy()->setHour(14)->setMinute(30),
                    'inspection_stage' => InspectionStage::InProcess->value,
                    'sample_size_inspected' => $b['sample_size'],
                    'defective_units_qty' => min($b['sample_size'], $totalDefectUnits),
                    'notes' => 'Inspeksi 5 CTQ batch stamping '.$b['batch_number'],
                    'defects' => $defectsPayload,
                ]);
            }
        }

        // 6. Seed CAPA Actions (Tindakan Korektif untuk Cacat Dominan & Out of Control)
        $excrapDefect = $defectTypeModels['DEF-EXC'] ?? null;
        if ($excrapDefect) {
            CapaAction::firstOrCreate(
                ['capa_number' => 'CAPA-202609-0001'],
                [
                    'defect_type_id' => $excrapDefect->id,
                    'assigned_to_user_id' => $supervisor->id,
                    'title' => 'Pengurangan Cacat excrap Akibat Slug Scrap Tertindih di Mesin Press 250T',
                    'problem_statement' => 'Cacat excrap mendominasi 46% dari total temuan cacat pada produk Bracket Seat Leg mobil.',
                    'root_cause_analysis' => '5-Why: Kenapa scrap tertindih plat? Scrap tidak jatuh ke saluran pembuangan. Kenapa tidak jatuh? Daya hisap vacuum ejector berkurang. Kenapa berkurang? Selang pneumatic vacuum tersumbat serbuk gram dan filter udara kotor selama 2 bulan tanpa pembersihan.',
                    'corrective_action' => 'Mengganti selang vacuum ejector baru, membersihkan die clearance, dan menyetel ulang sudut hembusan air blow pada die stripper.',
                    'preventive_action' => 'Membuat jadwal Total Productive Maintenance (TPM) harian untuk pemeriksaan filter vacuum dan pembersihan serbuk gram die setiap akhir shift.',
                    'target_completion_date' => Carbon::today()->addDays(7),
                    'actual_completion_date' => Carbon::today()->subDay(),
                    'status' => CapaStatus::Implemented,
                    'verification_notes' => 'Pengamatan 2 hari pasca perbaikan menunjukkan penurunan signifikan cacat excrap dari 8 unit menjadi 1-2 unit per lot sampel.',
                ]
            );
        }

        $blankMinus = $defectTypeModels['DEF-BLM'] ?? null;
        if ($blankMinus) {
            CapaAction::firstOrCreate(
                ['capa_number' => 'CAPA-202609-0002'],
                [
                    'defect_type_id' => $blankMinus->id,
                    'assigned_to_user_id' => $supervisor->id,
                    'title' => 'Tindakan Korektif Cacat blank minus Akibat Pergeseran NC Feeder Coil',
                    'problem_statement' => 'Ditemukan cacat blank minus (dimensi lembaran tekor) yang menyebabkan batch LOT-202609-0005 melampaui batas kendali UCL (Out of Control).',
                    'root_cause_analysis' => '5-Why: Kenapa blank minus? Panjang langkah pemotongan feeder plat berkurang 1.2mm. Kenapa berkurang? Roller penggerak NC feeder mengalami slip. Kenapa slip? Tekanan pneumatik roll gripper drop dari 5 bar menjadi 3.2 bar akibat kebocoran seal regulator.',
                    'corrective_action' => 'Mengganti seal regulator pneumatik feeder roll dan melakukan kalibrasi panjang langkah feeding menggunakan dial gauge.',
                    'preventive_action' => 'Memasang pressure switch indikator dengan alarm otomatis jika tekanan pneumatik NC feeder drop di bawah 4.5 bar.',
                    'target_completion_date' => Carbon::today()->addDays(5),
                    'actual_completion_date' => null,
                    'status' => CapaStatus::InProgress,
                    'verification_notes' => null,
                ]
            );
        }

        // 7. Seed Data Modul Analisa Resiko Kerja Mesin Stamping Press (Poin 4-6 fitur.jpeg)
        $risksData = [
            [
                'hazard_code' => 'BHY-001',
                'hazard_name' => 'Titik Jepit Antara Die Upper dan Lower Mesin Press',
                'machine_area' => 'Mesin Stamping Press 250 Ton - Area Cetakan (Die)',
                'risk_description' => 'Tangan atau jari operator terjepit hantaman die saat memasukkan plat bahan baku atau mengeluarkan produk bracket secara manual.',
                'likelihood' => 3, // Sedang
                'severity' => 5,   // Bencana / Cacat Permanen
                'control_measures' => 'Pemasangan Safety Light Curtain (Sensor Tirai Optik Inframerah) yang otomatis menghentikan mesin seketika saat terhalang, penggunaan Tombol Dua Tangan (Two-Hand Control Button), dan penyediaan tongkat magnet pengambil plat.',
                'pic' => 'Hendra Wijaya (Supervisor)',
                'status' => 'active',
            ],
            [
                'hazard_code' => 'BHY-002',
                'hazard_name' => 'Scrap / Serpihan Logam Terpental Saat Blanking & Trimming',
                'machine_area' => 'Mesin Stamping Press 150 Ton & 250 Ton',
                'risk_description' => 'Potongan sisa plat tajam (excrap) terlempar akibat gaya kejut pukulan punch press dan mengenai mata atau wajah operator.',
                'likelihood' => 4, // Sering
                'severity' => 3,   // Cedera Sedang
                'control_measures' => 'Pemasangan pelindung mika akrilik transparan (Safety Guard) di sekeliling area die dan kewajiban mutlak APD Kacamata Safety (Goggles) & Face Shield.',
                'pic' => 'Budi Santoso (QC / HSE Officer)',
                'status' => 'active',
            ],
            [
                'hazard_code' => 'BHY-003',
                'hazard_name' => 'Kebisingan Ekstrem (> 90 dB) Akibat Hantaman Mesin Stamping',
                'machine_area' => 'Workshop Stamping Bay A, B, & C',
                'risk_description' => 'Paparan suara dentuman mekanis intensitas tinggi secara terus menerus mengakibatkan penurunan pendengaran permanen (Noise Induced Hearing Loss).',
                'likelihood' => 5, // Sangat Sering
                'severity' => 2,   // Cedera Ringan / Kumulatif
                'control_measures' => 'Pemasangan bantalan peredam getaran (anti-vibration pad) pada pondasi mesin press, rotasi jam kerja operator setiap 4 jam, dan kewajiban penggunaan Ear Muff / Ear Plug standar NRR 25 dB.',
                'pic' => 'HSE Plant Officer',
                'status' => 'active',
            ],
            [
                'hazard_code' => 'BHY-004',
                'hazard_name' => 'Ceceran Oli Pelumas Hidrolik di Sekitar Mesin Press',
                'machine_area' => 'Lantai Area Kerja Mesin Press 300 Ton',
                'risk_description' => 'Operator terpeleset dan terjatuh menabrak rangka mesin atau tumpukan material plat besi berat.',
                'likelihood' => 2, // Jarang
                'severity' => 3,   // Cedera Sedang (Dislokasi / Memar)
                'control_measures' => 'Pemasangan drip pan penampung tetesan oli hidrolik, inspeksi harian kebocoran fitting selang hidrolik, dan pembersihan rutin menggunakan absorbent pad / serbuk gergaji.',
                'pic' => 'Tim Maintenance Mesin',
                'status' => 'controlled',
            ],
            [
                'hazard_code' => 'BHY-005',
                'hazard_name' => 'Tepi Lembaran Plat Baja (Raw Material SPCC) Yang Tajam',
                'machine_area' => 'Area Loading Plat & Uncoiler Feeder',
                'risk_description' => 'Luka sayat / robek pada telapak tangan dan jari operator saat mengangkat dan memindahkan lembaran plat bahan baku bracket.',
                'likelihood' => 4, // Sering
                'severity' => 2,   // Cedera Ringan
                'control_measures' => 'Kewajiban penggunaan Sarung Tangan Anti-Sayat (Kevlar Cut-Resistant Gloves Level 5) dan alat bantu pemindah plat bermagnet (Magnetic Sheet Lifter).',
                'pic' => 'Hendra Wijaya (Supervisor)',
                'status' => 'controlled',
            ],
            [
                'hazard_code' => 'BHY-006',
                'hazard_name' => 'Hubungan Singkat / Sengatan Listrik Panel Kontrol Mesin 300T',
                'machine_area' => 'Panel Daya Listrik Mesin Stamping Press 300 Ton',
                'risk_description' => 'Sengatan listrik (electric shock) bertegangan 380V atau percikan api kebakaran panel listrik utama.',
                'likelihood' => 1, // Sangat Jarang
                'severity' => 4,   // Cedera Berat / Fatal
                'control_measures' => 'Pemberian gembok pengaman panel (Lockout/Tagout - LOTO), grounding panel tahanan < 5 ohm, pemeliharaan berkala thermovisi panel, dan penyediaan APAR gas CO2 di dekat panel.',
                'pic' => 'Teknisi Listrik Pabrik',
                'status' => 'controlled',
            ],
        ];

        foreach ($risksData as $risk) {
            MachineRiskAssessment::updateOrCreate(
                ['hazard_code' => $risk['hazard_code']],
                $risk
            );
        }
    }
}
