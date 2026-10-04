<?php

use App\Enums\DefectSeverity;
use App\Enums\InspectionResult;
use App\Enums\InspectionStage;
use App\Enums\IshikawaCategory;
use App\Enums\ProductionShift;
use App\Enums\UserRole;
use App\Models\DefectCategory;
use App\Models\DefectType;
use App\Models\Product;
use App\Models\ProductionBatch;
use App\Models\ProductionLine;
use App\Models\QualityInspection;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->inspector = User::factory()->create([
        'role' => UserRole::Staff,
        'department' => 'Quality Control',
        'is_active' => true,
    ]);

    $this->product = Product::create([
        'part_number' => 'PRD-TEST-01',
        'name' => 'Bracket Support Leg RH',
        'unit' => 'pcs',
        'defect_opportunities_per_unit' => 5,
        'standard_cycle_time' => 15.0,
        'is_active' => true,
    ]);

    $this->line = ProductionLine::create([
        'line_code' => 'LINE-STAMP-01',
        'name' => 'Mesin Stamping Press',
        'status' => 'operational',
    ]);

    $this->batch = ProductionBatch::create([
        'batch_number' => 'LOT-202610-0001',
        'product_id' => $this->product->id,
        'production_line_id' => $this->line->id,
        'supervisor_id' => $this->inspector->id,
        'production_date' => now(),
        'shift' => ProductionShift::Shift1,
        'target_qty' => 500,
        'status' => 'in_production',
    ]);

    $this->defectCategory = DefectCategory::create([
        'code' => 'CAT-DIM',
        'name' => 'Dimensi',
    ]);

    $this->defectType = DefectType::create([
        'defect_category_id' => $this->defectCategory->id,
        'code' => 'DEF-001',
        'name' => 'Goresan Permukaan',
        'severity' => DefectSeverity::Minor,
        'default_5m_category' => IshikawaCategory::Machine,
        'is_active' => true,
    ]);
});

test('qc create page renders month dropdown and week 1-4 selection options', function () {
    $response = $this->actingAs($this->inspector)->get(route('admin.inspections.create'));

    $response->assertOk();
    $response->assertSee('Waktu Pemeriksaan (Periode QC)');
    $response->assertSee('Bulan Pemeriksaan');
    $response->assertSee('Pilihan Minggu Keberapa (1-4)');
    $response->assertSee('Minggu ke-1 (Hari 1 - 7)');
    $response->assertSee('Minggu ke-2 (Hari 8 - 14)');
    $response->assertSee('Minggu ke-3 (Hari 15 - 21)');
    $response->assertSee('Minggu ke-4 (Hari 22 - 28/31)');
    $response->assertSee('Januari');
    $response->assertSee('Oktober');
    $response->assertSee('Desember');
});

test('stores quality inspection with month dropdown and week selection successfully', function () {
    $response = $this->actingAs($this->inspector)->post(route('admin.inspections.store'), [
        'production_batch_id' => $this->batch->id,
        'inspection_year' => 2026,
        'inspection_month' => 10,
        'inspection_week' => 2,
        'inspection_stage' => InspectionStage::InProcess->value,
        'sample_size_inspected' => 100,
        'defective_units_qty' => 2,
        'notes' => 'Pemeriksaan sampling rutin minggu kedua Oktober',
        'defects' => [
            [
                'defect_type_id' => $this->defectType->id,
                'defect_qty' => 2,
                'root_cause_category' => 'machine',
                'root_cause_notes' => 'Roller gesek berlebih',
            ],
        ],
    ]);

    $inspection = QualityInspection::latest('id')->first();
    expect($inspection)->not->toBeNull();

    $response->assertRedirect(route('admin.inspections.show', $inspection));

    $this->assertDatabaseHas('quality_inspections', [
        'id' => $inspection->id,
        'production_batch_id' => $this->batch->id,
        'inspection_year' => 2026,
        'inspection_month' => 10,
        'inspection_week' => 2,
        'sample_size_inspected' => 100,
        'defective_units_qty' => 2,
    ]);

    // Week 2 corresponds to day 8
    expect($inspection->inspection_time->format('Y-m-d'))->toBe('2026-10-08');
    expect($inspection->period_label)->toBe('Bulan Oktober 2026 (Minggu ke-2)');
    expect($inspection->period_short_label)->toBe('Okt 2026 (M2)');
});

test('validates inspection month and week input bounds', function () {
    // Month > 12 should fail
    $this->actingAs($this->inspector)
        ->post(route('admin.inspections.store'), [
            'production_batch_id' => $this->batch->id,
            'inspection_year' => 2026,
            'inspection_month' => 13,
            'inspection_week' => 2,
            'inspection_stage' => InspectionStage::InProcess->value,
            'sample_size_inspected' => 50,
            'defective_units_qty' => 0,
        ])
        ->assertSessionHasErrors(['inspection_month']);

    // Week > 4 should fail
    $this->actingAs($this->inspector)
        ->post(route('admin.inspections.store'), [
            'production_batch_id' => $this->batch->id,
            'inspection_year' => 2026,
            'inspection_month' => 5,
            'inspection_week' => 5,
            'inspection_stage' => InspectionStage::InProcess->value,
            'sample_size_inspected' => 50,
            'defective_units_qty' => 0,
        ])
        ->assertSessionHasErrors(['inspection_week']);
});

test('period label is displayed on inspection show and index pages', function () {
    $inspection = QualityInspection::create([
        'inspection_number' => 'QC-202610-9999',
        'production_batch_id' => $this->batch->id,
        'inspector_id' => $this->inspector->id,
        'inspection_time' => '2026-10-15 09:00:00',
        'inspection_year' => 2026,
        'inspection_month' => 10,
        'inspection_week' => 3,
        'inspection_stage' => InspectionStage::InProcess,
        'sample_size_inspected' => 100,
        'passed_qty' => 100,
        'defective_units_qty' => 0,
        'total_defects_count' => 0,
        'dpu' => 0,
        'dpo' => 0,
        'dpmo' => 0,
        'sigma_level' => 6.00,
        'yield_percentage' => 100.00,
        'result_status' => InspectionResult::Passed,
    ]);

    $showResponse = $this->actingAs($this->inspector)->get(route('admin.inspections.show', $inspection));
    $showResponse->assertOk();
    $showResponse->assertSee('Bulan Oktober 2026 (Minggu ke-3)');

    $indexResponse = $this->actingAs($this->inspector)->get(route('admin.inspections.index'));
    $indexResponse->assertOk();
    $indexResponse->assertSee('Okt 2026 (M3)');
});
