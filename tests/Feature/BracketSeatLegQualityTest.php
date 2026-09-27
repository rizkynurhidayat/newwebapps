<?php

use App\Enums\DefectSeverity;
use App\Enums\InspectionStage;
use App\Enums\IshikawaCategory;
use App\Enums\ProductionShift;
use App\Enums\UserRole;
use App\Models\DefectCategory;
use App\Models\DefectType;
use App\Models\Product;
use App\Models\ProductionBatch;
use App\Models\ProductionLine;
use App\Models\User;
use App\Services\ProductionService;
use App\Services\SixSigmaCalculatorService;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->user = User::factory()->create([
        'role' => UserRole::Admin,
        'is_active' => true,
    ]);

    // Product Bracket Seat Leg
    $this->product = Product::create([
        'part_number' => 'BSL-7110-RH',
        'name' => 'Bracket Seat Leg Front RH',
        'raw_material' => 'Plat Baja SPCC ketebalan 2.0 mm',
        'unit' => 'pcs',
        'defect_opportunities_per_unit' => 5,
        'standard_cycle_time' => 18.0,
        'is_active' => true,
    ]);

    $this->line = ProductionLine::create([
        'line_code' => 'LINE-STAMP-01',
        'name' => 'Mesin Stamping Press 250 Ton',
        'status' => 'operational',
    ]);

    $this->cat = DefectCategory::create([
        'code' => 'CAT-STAMP',
        'name' => 'Stamping Defects',
    ]);

    // 5 CTQs
    $this->ctqs = [
        'excrap' => DefectType::create(['defect_category_id' => $this->cat->id, 'code' => 'DEF-EXC', 'name' => 'excrap', 'severity' => DefectSeverity::Major, 'default_5m_category' => IshikawaCategory::Machine]),
        'blank_minus' => DefectType::create(['defect_category_id' => $this->cat->id, 'code' => 'DEF-BLM', 'name' => 'blank minus', 'severity' => DefectSeverity::Major, 'default_5m_category' => IshikawaCategory::Material]),
        'trim_minus' => DefectType::create(['defect_category_id' => $this->cat->id, 'code' => 'DEF-TRM', 'name' => 'trim minus', 'severity' => DefectSeverity::Major, 'default_5m_category' => IshikawaCategory::Method]),
        'deformasi' => DefectType::create(['defect_category_id' => $this->cat->id, 'code' => 'DEF-DEF', 'name' => 'deformasi', 'severity' => DefectSeverity::Critical, 'default_5m_category' => IshikawaCategory::Machine]),
        'karat' => DefectType::create(['defect_category_id' => $this->cat->id, 'code' => 'DEF-RST', 'name' => 'karat', 'severity' => DefectSeverity::Critical, 'default_5m_category' => IshikawaCategory::Environment]),
    ];
});

test('stores product with raw_material for bracket seat leg manufacturing', function () {
    expect($this->product->raw_material)->toBe('Plat Baja SPCC ketebalan 2.0 mm');
    expect($this->product->defect_opportunities_per_unit)->toBe(5);

    $this->assertDatabaseHas('products', [
        'part_number' => 'BSL-7110-RH',
        'raw_material' => 'Plat Baja SPCC ketebalan 2.0 mm',
    ]);
});

test('records quality inspection on 5 CTQ defect types and calculates six sigma metrics', function () {
    $service = app(ProductionService::class);

    $batch = ProductionBatch::create([
        'batch_number' => $service->generateBatchNumber(),
        'product_id' => $this->product->id,
        'production_line_id' => $this->line->id,
        'supervisor_id' => $this->user->id,
        'production_date' => now(),
        'shift' => ProductionShift::Shift1,
        'target_qty' => 500,
        'actual_qty' => 495,
    ]);

    $inspection = $service->recordInspection([
        'production_batch_id' => $batch->id,
        'inspector_id' => $this->user->id,
        'inspection_time' => now(),
        'inspection_stage' => InspectionStage::InProcess->value,
        'sample_size_inspected' => 100,
        'defective_units_qty' => 5,
        'defects' => [
            ['defect_type_id' => $this->ctqs['excrap']->id, 'defect_qty' => 3, 'root_cause_category' => 'machine', 'root_cause_notes' => 'Slug scrap menempel'],
            ['defect_type_id' => $this->ctqs['blank_minus']->id, 'defect_qty' => 1, 'root_cause_category' => 'material', 'root_cause_notes' => 'Tekor 0.5mm'],
            ['defect_type_id' => $this->ctqs['deformasi']->id, 'defect_qty' => 1, 'root_cause_category' => 'machine', 'root_cause_notes' => 'Melintir bending'],
        ],
    ]);

    expect($inspection->sample_size_inspected)->toBe(100);
    expect($inspection->total_defects_count)->toBe(5);
    // DPU = 5 / 100 = 0.05
    expect((float) $inspection->dpu)->toBe(0.05);
    // Opportunities = 100 * 5 = 500 => DPO = 5 / 500 = 0.01 => DPMO = 10,000
    expect((float) $inspection->dpmo)->toBe(10000.0);
    expect((float) $inspection->yield_percentage)->toBe(95.0);
    expect((float) $inspection->sigma_level)->toBeGreaterThan(3.70);
});

test('six sigma calculator generates automated conclusion per flowchart step 9 and 10', function () {
    $calculator = app(SixSigmaCalculatorService::class);

    $pareto = [
        'total_defects' => 20,
        'vital_few_count' => 1,
        'items' => [
            [
                'defect_code' => 'DEF-EXC',
                'defect_name' => 'excrap',
                'severity' => 'major',
                'count' => 12,
                'percentage' => 60.0,
                'cumulative_percentage' => 60.0,
                'is_vital_few' => true,
            ],
            [
                'defect_code' => 'DEF-BLM',
                'defect_name' => 'blank minus',
                'severity' => 'major',
                'count' => 8,
                'percentage' => 40.0,
                'cumulative_percentage' => 100.0,
                'is_vital_few' => false,
            ],
        ],
    ];

    $spc = [
        'ucl' => 0.08,
        'cl' => 0.04,
        'lcl' => 0.00,
        'points' => [
            ['id' => 1, 'p' => 0.03, 'out_of_control' => false],
            ['id' => 2, 'p' => 0.05, 'out_of_control' => false],
        ],
    ];

    $conclusion = $calculator->generateConclusion(3.85, $pareto, $spc);

    expect($conclusion['sigma_eval']['level'])->toBe(3.85);
    expect($conclusion['dominant_defect']['name'])->toBe('excrap');
    expect($conclusion['dominant_defect']['percentage'])->toBe(60.0);
    expect($conclusion['control_status']['is_in_control'])->toBeTrue();
    expect($conclusion['recommendations'])->not->toBeEmpty();
});

test('analytics page displays automated conclusion and 5 CTQ data', function () {
    $response = $this->actingAs($this->user)->get(route('admin.analytics.index'));
    $response->assertOk();
    $response->assertSee('Kesimpulan Kualitas & Output Laporan Pengendalian Mutu', false);
    $response->assertSee('Cacat Dominan (Vital Few)');
    $response->assertSee('Status Kendali Proses (p-Chart)');
});
