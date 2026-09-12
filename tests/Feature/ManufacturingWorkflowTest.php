<?php

use App\Enums\BatchStatus;
use App\Enums\CapaStatus;
use App\Enums\DefectSeverity;
use App\Enums\InspectionResult;
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
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->user = User::factory()->create([
        'role' => UserRole::Admin,
        'is_active' => true,
    ]);

    $this->product = Product::create([
        'part_number' => 'TEST-PART-01',
        'name' => 'Test Electronic Module',
        'unit' => 'pcs',
        'defect_opportunities_per_unit' => 5,
        'standard_cycle_time' => 45.0,
        'is_active' => true,
    ]);

    $this->line = ProductionLine::create([
        'line_code' => 'LINE-TEST-01',
        'name' => 'Assembly Test Line',
        'status' => 'operational',
    ]);

    $this->cat = DefectCategory::create([
        'code' => 'CAT-TEST',
        'name' => 'Visual Test',
    ]);

    $this->defectType = DefectType::create([
        'defect_category_id' => $this->cat->id,
        'code' => 'DEF-TEST-01',
        'name' => 'Surface Scratch',
        'severity' => DefectSeverity::Minor,
        'default_5m_category' => IshikawaCategory::Machine,
        'is_active' => true,
    ]);
});

test('can create a production batch and generate lot number', function () {
    $service = app(ProductionService::class);
    $lotNumber = $service->generateBatchNumber();

    expect($lotNumber)->toStartWith('LOT-');

    $batch = ProductionBatch::create([
        'batch_number' => $lotNumber,
        'product_id' => $this->product->id,
        'production_line_id' => $this->line->id,
        'supervisor_id' => $this->user->id,
        'production_date' => now(),
        'shift' => ProductionShift::Shift1,
        'target_qty' => 500,
        'status' => BatchStatus::InProduction,
    ]);

    expect($batch->id)->not->toBeNull();
    expect($batch->product->id)->toBe($this->product->id);
});

test('records quality inspection with calculated six sigma metrics atomically', function () {
    $service = app(ProductionService::class);

    $batch = ProductionBatch::create([
        'batch_number' => $service->generateBatchNumber(),
        'product_id' => $this->product->id,
        'production_line_id' => $this->line->id,
        'supervisor_id' => $this->user->id,
        'production_date' => now(),
        'shift' => ProductionShift::Shift1,
        'target_qty' => 500,
        'status' => BatchStatus::InProduction,
    ]);

    $inspection = $service->recordInspection([
        'production_batch_id' => $batch->id,
        'inspector_id' => $this->user->id,
        'inspection_time' => now(),
        'inspection_stage' => InspectionStage::InProcess->value,
        'sample_size_inspected' => 100,
        'defective_units_qty' => 5,
        'defects' => [
            [
                'defect_type_id' => $this->defectType->id,
                'defect_qty' => 5,
                'root_cause_category' => 'machine',
                'root_cause_notes' => 'Conveyor friction',
            ],
        ],
    ]);

    expect($inspection->id)->not->toBeNull();
    expect($inspection->inspection_number)->toStartWith('QC-');
    expect($inspection->dpu)->toBe('0.0500');
    expect($inspection->dpmo)->toBe('10000.00');
    expect((float) $inspection->sigma_level)->toBeGreaterThan(3.70);
    expect($inspection->yield_percentage)->toBe('95.00');
    expect($inspection->result_status)->toBe(InspectionResult::Conditional);

    $this->assertDatabaseHas('inspection_defects', [
        'quality_inspection_id' => $inspection->id,
        'defect_type_id' => $this->defectType->id,
        'defect_qty' => 5,
    ]);
});

test('can create and transition a capa action', function () {
    $service = app(ProductionService::class);
    $capaNumber = $service->generateCapaNumber();

    $capa = CapaAction::create([
        'capa_number' => $capaNumber,
        'defect_type_id' => $this->defectType->id,
        'assigned_to_user_id' => $this->user->id,
        'title' => 'Fix conveyor scratching issue',
        'problem_statement' => 'Excessive scratch defects on assembly line',
        'root_cause_analysis' => '5-Why: Worn out silicone cushions on fixture',
        'corrective_action' => 'Replace cushions with high-temp polyurethane',
        'preventive_action' => 'Monthly inspection checklist',
        'target_completion_date' => now()->addDays(7),
        'status' => CapaStatus::Open,
    ]);

    expect($capa->status)->toBe(CapaStatus::Open);

    $capa->update(['status' => CapaStatus::Implemented]);
    expect($capa->fresh()->status)->toBe(CapaStatus::Implemented);
});
