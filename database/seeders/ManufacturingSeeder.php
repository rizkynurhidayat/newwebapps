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
                'department' => 'Plant Operations & Management',
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
                'department' => 'Quality Assurance & Control (QA/QC)',
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
                'department' => 'Manufacturing Assembly Line 1',
                'is_active' => true,
            ]
        );

        // 2. Seed Products with CTQ Opportunities
        $products = [
            [
                'part_number' => 'PART-ECM-01',
                'name' => 'Electronic Control Module (ECM-V4)',
                'description' => 'Modul pengendali sirkuit elektronik utama untuk transmisi kendaraan bermotor.',
                'unit' => 'pcs',
                'defect_opportunities_per_unit' => 8, // 8 critical inspection points
                'standard_cycle_time' => 45.00,
                'is_active' => true,
            ],
            [
                'part_number' => 'PART-BRK-02',
                'name' => 'Brake Caliper Double Piston',
                'description' => 'Kaliper rem hidrolik presisi tinggi berbahan aluminium alloy tahan panas.',
                'unit' => 'pcs',
                'defect_opportunities_per_unit' => 6,
                'standard_cycle_time' => 60.00,
                'is_active' => true,
            ],
            [
                'part_number' => 'PART-INJ-03',
                'name' => 'Common Rail Fuel Injector Nozzle',
                'description' => 'Nozel injeksi bahan bakar diesel presisi mikro dengan toleransi mikron.',
                'unit' => 'pcs',
                'defect_opportunities_per_unit' => 5,
                'standard_cycle_time' => 30.00,
                'is_active' => true,
            ],
            [
                'part_number' => 'PART-RTR-04',
                'name' => 'Alternator Stator & Rotor Core',
                'description' => 'Inti kumparan rotor tembaga berlapis isolasi untuk sistem kelistrikan industri.',
                'unit' => 'pcs',
                'defect_opportunities_per_unit' => 7,
                'standard_cycle_time' => 50.00,
                'is_active' => true,
            ],
        ];

        $productModels = [];
        foreach ($products as $p) {
            $productModels[$p['part_number']] = Product::firstOrCreate(
                ['part_number' => $p['part_number']],
                $p
            );
        }

        // 3. Seed Production Lines
        $lines = [
            [
                'line_code' => 'LINE-SMT-01',
                'name' => 'Surface Mount Technology (SMT) Line 1',
                'location' => 'Gedung A, Lantai 1 (Cleanroom 10k)',
                'description' => 'Lini penempatan komponen elektronika otomatis berkecepatan tinggi.',
                'status' => 'operational',
            ],
            [
                'line_code' => 'LINE-ASM-02',
                'name' => 'Final Assembly & Integration Line',
                'location' => 'Gedung A, Lantai 2',
                'description' => 'Lini perakitan mekanis dan pengujian fungsional terintegrasi.',
                'status' => 'operational',
            ],
            [
                'line_code' => 'LINE-CNC-03',
                'name' => 'CNC 5-Axis Precision Machining Cell',
                'location' => 'Workshop Machining B',
                'description' => 'Mesin pemotong dan pembubut logam presisi tinggi.',
                'status' => 'operational',
            ],
            [
                'line_code' => 'LINE-PNT-04',
                'name' => 'Automated Powder Coating Line',
                'location' => 'Workshop Finishing C',
                'description' => 'Lini pelapisan cat anti karat elektrostatik otomatis.',
                'status' => 'operational',
            ],
        ];

        $lineModels = [];
        foreach ($lines as $l) {
            $lineModels[$l['line_code']] = ProductionLine::firstOrCreate(
                ['line_code' => $l['line_code']],
                $l
            );
        }

        // 4. Seed Defect Categories & Defect Types
        $categoriesData = [
            'CAT-VIS' => [
                'name' => 'Visual Defects',
                'description' => 'Cacat tampak luar yang terdeteksi secara optik atau penglihatan.',
                'types' => [
                    [
                        'code' => 'DEF-SCR',
                        'name' => 'Goresan Permukaan (Surface Scratch)',
                        'severity' => DefectSeverity::Minor,
                        'default_5m_category' => IshikawaCategory::Machine,
                    ],
                    [
                        'code' => 'DEF-BUR',
                        'name' => 'Sisa Geram Logam (Burrs / Flash)',
                        'severity' => DefectSeverity::Minor,
                        'default_5m_category' => IshikawaCategory::Machine,
                    ],
                    [
                        'code' => 'DEF-CLR',
                        'name' => 'Warna Tidak Rata (Discoloration)',
                        'severity' => DefectSeverity::Minor,
                        'default_5m_category' => IshikawaCategory::Environment,
                    ],
                ],
            ],
            'CAT-DIM' => [
                'name' => 'Dimension & Geometry',
                'description' => 'Penyimpangan ukuran geometris dan toleransi mikrometer.',
                'types' => [
                    [
                        'code' => 'DEF-OVS',
                        'name' => 'Dimensi Berlebih (Oversize)',
                        'severity' => DefectSeverity::Major,
                        'default_5m_category' => IshikawaCategory::Measurement,
                    ],
                    [
                        'code' => 'DEF-UDS',
                        'name' => 'Dimensi Kurang (Undersize)',
                        'severity' => DefectSeverity::Major,
                        'default_5m_category' => IshikawaCategory::Machine,
                    ],
                    [
                        'code' => 'DEF-WRP',
                        'name' => 'Bentuk Melengkung (Warping / Bend)',
                        'severity' => DefectSeverity::Major,
                        'default_5m_category' => IshikawaCategory::Method,
                    ],
                ],
            ],
            'CAT-FNC' => [
                'name' => 'Functional & Electrical',
                'description' => 'Kegagalan fungsi kelistrikan, hidrolik, atau mekanikal.',
                'types' => [
                    [
                        'code' => 'DEF-COLD',
                        'name' => 'Solder Dingin / Retak (Cold Solder Joint)',
                        'severity' => DefectSeverity::Critical,
                        'default_5m_category' => IshikawaCategory::Method,
                    ],
                    [
                        'code' => 'DEF-SHRT',
                        'name' => 'Korsleting Listrik (Short Circuit)',
                        'severity' => DefectSeverity::Critical,
                        'default_5m_category' => IshikawaCategory::Man,
                    ],
                    [
                        'code' => 'DEF-LEAK',
                        'name' => 'Kebocoran Tekanan (Pressure Leakage)',
                        'severity' => DefectSeverity::Critical,
                        'default_5m_category' => IshikawaCategory::Material,
                    ],
                ],
            ],
            'CAT-MAT' => [
                'name' => 'Material Integrity',
                'description' => 'Cacat bahan baku seperti pori-pori, retak internal, atau kontaminasi.',
                'types' => [
                    [
                        'code' => 'DEF-CRK',
                        'name' => 'Retak Mikro (Micro Crack)',
                        'severity' => DefectSeverity::Critical,
                        'default_5m_category' => IshikawaCategory::Material,
                    ],
                    [
                        'code' => 'DEF-POR',
                        'name' => 'Pori Coran / Rongga Udara (Porosity)',
                        'severity' => DefectSeverity::Major,
                        'default_5m_category' => IshikawaCategory::Material,
                    ],
                ],
            ],
        ];

        $defectTypeModels = [];
        foreach ($categoriesData as $catCode => $cat) {
            $catModel = DefectCategory::firstOrCreate(
                ['code' => $catCode],
                ['name' => $cat['name'], 'description' => $cat['description']]
            );

            foreach ($cat['types'] as $type) {
                $defectTypeModels[$type['code']] = DefectType::firstOrCreate(
                    ['code' => $type['code']],
                    [
                        'defect_category_id' => $catModel->id,
                        'name' => $type['name'],
                        'severity' => $type['severity'],
                        'default_5m_category' => $type['default_5m_category'],
                        'is_active' => true,
                    ]
                );
            }
        }

        // 5. Seed Production Batches & QC Inspections (Past 10 Days)
        $batchConfigs = [
            [
                'batch_number' => 'LOT-202609-0001',
                'product' => 'PART-ECM-01',
                'line' => 'LINE-SMT-01',
                'shift' => ProductionShift::Shift1,
                'days_ago' => 9,
                'target_qty' => 500,
                'actual_qty' => 492,
                'sample_size' => 100,
                'defects' => [
                    ['code' => 'DEF-SCR', 'qty' => 5, 'cat' => IshikawaCategory::Machine, 'notes' => 'Gesekan ujung feeder robotik'],
                    ['code' => 'DEF-COLD', 'qty' => 3, 'cat' => IshikawaCategory::Method, 'notes' => 'Suhu pre-heating zone 3 menurun 5 derajat'],
                ],
            ],
            [
                'batch_number' => 'LOT-202609-0002',
                'product' => 'PART-ECM-01',
                'line' => 'LINE-SMT-01',
                'shift' => ProductionShift::Shift2,
                'days_ago' => 8,
                'target_qty' => 500,
                'actual_qty' => 496,
                'sample_size' => 100,
                'defects' => [
                    ['code' => 'DEF-SCR', 'qty' => 3, 'cat' => IshikawaCategory::Machine, 'notes' => 'Gesekan conveyor tray'],
                    ['code' => 'DEF-COLD', 'qty' => 1, 'cat' => IshikawaCategory::Method, 'notes' => 'Fluks solder mengering'],
                ],
            ],
            [
                'batch_number' => 'LOT-202609-0003',
                'product' => 'PART-BRK-02',
                'line' => 'LINE-CNC-03',
                'shift' => ProductionShift::Shift1,
                'days_ago' => 7,
                'target_qty' => 300,
                'actual_qty' => 288,
                'sample_size' => 80,
                'defects' => [
                    ['code' => 'DEF-BUR', 'qty' => 8, 'cat' => IshikawaCategory::Machine, 'notes' => 'Mata pisau milling aus'],
                    ['code' => 'DEF-OVS', 'qty' => 4, 'cat' => IshikawaCategory::Measurement, 'notes' => 'Zero-point caliper bergeser'],
                ],
            ],
            [
                'batch_number' => 'LOT-202609-0004',
                'product' => 'PART-BRK-02',
                'line' => 'LINE-ASM-02',
                'shift' => ProductionShift::Shift2,
                'days_ago' => 6,
                'target_qty' => 300,
                'actual_qty' => 295,
                'sample_size' => 80,
                'defects' => [
                    ['code' => 'DEF-SCR', 'qty' => 4, 'cat' => IshikawaCategory::Machine, 'notes' => 'Klem perakitan tergores'],
                    ['code' => 'DEF-LEAK', 'qty' => 1, 'cat' => IshikawaCategory::Material, 'notes' => 'O-ring segel sobek mikro'],
                ],
            ],
            [
                'batch_number' => 'LOT-202609-0005',
                'product' => 'PART-INJ-03',
                'line' => 'LINE-CNC-03',
                'shift' => ProductionShift::Shift1,
                'days_ago' => 5,
                'target_qty' => 400,
                'actual_qty' => 390,
                'sample_size' => 100,
                'defects' => [
                    ['code' => 'DEF-SCR', 'qty' => 6, 'cat' => IshikawaCategory::Machine, 'notes' => 'Baret halus pada bodi luar'],
                    ['code' => 'DEF-COLD', 'qty' => 4, 'cat' => IshikawaCategory::Method, 'notes' => 'Solder nozzle kontak tidak merata'],
                ],
            ],
            [
                'batch_number' => 'LOT-202609-0006',
                'product' => 'PART-ECM-01',
                'line' => 'LINE-SMT-01',
                'shift' => ProductionShift::Shift3,
                'days_ago' => 4,
                'target_qty' => 500,
                'actual_qty' => 497,
                'sample_size' => 100,
                'defects' => [
                    ['code' => 'DEF-SCR', 'qty' => 2, 'cat' => IshikawaCategory::Machine, 'notes' => 'Baret ringan'],
                    ['code' => 'DEF-SHRT', 'qty' => 1, 'cat' => IshikawaCategory::Man, 'notes' => 'Pemasangan jumper miring oleh operator shift malam'],
                ],
            ],
            [
                'batch_number' => 'LOT-202609-0007',
                'product' => 'PART-RTR-04',
                'line' => 'LINE-ASM-02',
                'shift' => ProductionShift::Shift1,
                'days_ago' => 3,
                'target_qty' => 350,
                'actual_qty' => 345,
                'sample_size' => 70,
                'defects' => [
                    ['code' => 'DEF-SCR', 'qty' => 3, 'cat' => IshikawaCategory::Machine, 'notes' => 'Baret permukaan casing'],
                    ['code' => 'DEF-BUR', 'qty' => 2, 'cat' => IshikawaCategory::Machine, 'notes' => 'Sisa geram di alur pasak'],
                ],
            ],
            [
                'batch_number' => 'LOT-202609-0008',
                'product' => 'PART-ECM-01',
                'line' => 'LINE-SMT-01',
                'shift' => ProductionShift::Shift1,
                'days_ago' => 2,
                'target_qty' => 500,
                'actual_qty' => 498,
                'sample_size' => 100,
                'defects' => [
                    ['code' => 'DEF-COLD', 'qty' => 2, 'cat' => IshikawaCategory::Method, 'notes' => 'Pin header ic dingin'],
                ],
            ],
            [
                'batch_number' => 'LOT-202609-0009',
                'product' => 'PART-BRK-02',
                'line' => 'LINE-CNC-03',
                'shift' => ProductionShift::Shift2,
                'days_ago' => 1,
                'target_qty' => 300,
                'actual_qty' => 296,
                'sample_size' => 60,
                'defects' => [
                    ['code' => 'DEF-SCR', 'qty' => 2, 'cat' => IshikawaCategory::Machine, 'notes' => 'Scratch bodi'],
                    ['code' => 'DEF-UDS', 'qty' => 2, 'cat' => IshikawaCategory::Machine, 'notes' => 'Diameter silinder piston kurang 0.02mm'],
                ],
            ],
            [
                'batch_number' => 'LOT-202609-0010',
                'product' => 'PART-ECM-01',
                'line' => 'LINE-SMT-01',
                'shift' => ProductionShift::Shift1,
                'days_ago' => 0,
                'target_qty' => 500,
                'actual_qty' => 495,
                'sample_size' => 100,
                'defects' => [
                    ['code' => 'DEF-SCR', 'qty' => 3, 'cat' => IshikawaCategory::Machine, 'notes' => 'Goresan minor tray'],
                    ['code' => 'DEF-COLD', 'qty' => 2, 'cat' => IshikawaCategory::Method, 'notes' => 'Solder dingin IC power'],
                ],
            ],
        ];

        foreach ($batchConfigs as $b) {
            $prod = $productModels[$b['product']];
            $ln = $lineModels[$b['line']];
            $prodDate = Carbon::today()->subDays($b['days_ago']);

            $batch = ProductionBatch::firstOrCreate(
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
                    'notes' => 'Batch produksi reguler shift '.$b['shift']->value,
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
                    'notes' => 'Inspeksi berkala lot produksi '.$b['batch_number'],
                    'defects' => $defectsPayload,
                ]);
            }
        }

        // 6. Seed CAPA Actions (Improve & Control)
        $coldSolder = $defectTypeModels['DEF-COLD'] ?? null;
        if ($coldSolder) {
            CapaAction::firstOrCreate(
                ['capa_number' => 'CAPA-202609-0001'],
                [
                    'defect_type_id' => $coldSolder->id,
                    'assigned_to_user_id' => $supervisor->id,
                    'title' => 'Pengurangan Cacat Solder Dingin (Cold Solder Joint) di Lini SMT 1',
                    'problem_statement' => 'Ditemukan rata-rata 2-4 unit solder dingin per batch 100 sampel pada produk Electronic Control Module (ECM-V4).',
                    'root_cause_analysis' => '5-Why: Kenapa solder dingin? Suhu pre-heat turun. Kenapa turun? Elemen pemanas zone 3 aus. Kenapa tidak terdeteksi? Sensor thermocouple belum dikalibrasi selama 6 bulan.',
                    'corrective_action' => 'Mengganti heating element pada Reflow Oven Zone 3 dan melakukan kalibrasi suhu ulang menggunakan profiler KIC.',
                    'preventive_action' => 'Menambahkan jadwal preventive maintenance mingguan untuk verifikasi profil termal oven reflow.',
                    'target_completion_date' => Carbon::today()->addDays(5),
                    'actual_completion_date' => Carbon::today()->subDay(),
                    'status' => CapaStatus::Implemented,
                    'verification_notes' => 'Hasil batch 2 hari terakhir menunjukkan penurunan cacat solder dari 4 unit menjadi 0-2 unit.',
                ]
            );
        }

        $scratch = $defectTypeModels['DEF-SCR'] ?? null;
        if ($scratch) {
            CapaAction::firstOrCreate(
                ['capa_number' => 'CAPA-202609-0002'],
                [
                    'defect_type_id' => $scratch->id,
                    'assigned_to_user_id' => $supervisor->id,
                    'title' => 'Eliminasi Goresan Permukaan Akibat Gesekan Tray Conveyor',
                    'problem_statement' => 'Goresan permukaan (surface scratch) konsisten menjadi jenis cacat nomor 1 pada diagram Pareto (menyumbang 48% total defect).',
                    'root_cause_analysis' => 'Tray penampung berbahan metal keras bergesekan langsung dengan bodi aluminium komponen saat pergerakan conveyor cepat.',
                    'corrective_action' => 'Memasang lapisan pelindung silikon anti-gores (ESD soft padding) pada seluruh jig penampung conveyor.',
                    'preventive_action' => 'Membuat standar SOP penanganan material (handling procedure) dan checklist penggantian busa pelindung setiap bulan.',
                    'target_completion_date' => Carbon::today()->addDays(10),
                    'actual_completion_date' => null,
                    'status' => CapaStatus::InProgress,
                    'verification_notes' => null,
                ]
            );
        }
    }
}
