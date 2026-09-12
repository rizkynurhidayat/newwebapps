<?php

use App\Enums\BatchStatus;
use App\Enums\CapaStatus;
use App\Enums\UserRole;
use App\Models\DefectCategory;
use App\Models\DefectType;
use App\Models\Product;
use App\Models\ProductionBatch;
use App\Models\ProductionLine;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->admin = User::factory()->create([
        'role' => UserRole::Admin,
        'is_active' => true,
    ]);

    $this->qc = User::factory()->create([
        'role' => UserRole::Staff,
        'is_active' => true,
    ]);

    $this->product = Product::create([
        'part_number' => 'PART-BRK-99',
        'name' => 'Brake Caliper Assembly',
        'unit' => 'pcs',
        'defect_opportunities_per_unit' => 6,
        'standard_cycle_time' => 50.0,
        'is_active' => true,
    ]);

    $this->line = ProductionLine::create([
        'line_code' => 'LINE-CNC-99',
        'name' => 'CNC Milling Cell 99',
        'status' => 'operational',
    ]);

    $this->cat = DefectCategory::create([
        'code' => 'CAT-DIM-99',
        'name' => 'Dimension Quality',
    ]);

    $this->defectType = DefectType::create([
        'defect_category_id' => $this->cat->id,
        'code' => 'DEF-OVS-99',
        'name' => 'Bore Oversize',
        'severity' => 'major',
        'default_5m_category' => 'machine',
        'is_active' => true,
    ]);
});

test('user can create a new production batch via web route', function () {
    $response = $this->actingAs($this->admin)->post(route('admin.batches.store'), [
        'product_id' => $this->product->id,
        'production_line_id' => $this->line->id,
        'production_date' => now()->toDateString(),
        'shift' => 'Shift 1',
        'target_qty' => 400,
        'notes' => 'Batch uji kualitas',
    ]);

    $response->assertRedirect();
    $this->assertDatabaseHas('production_batches', [
        'product_id' => $this->product->id,
        'target_qty' => 400,
        'status' => BatchStatus::InProduction,
    ]);
});

test('qc inspector can submit inspection report with defect items and six sigma calculation', function () {
    $batch = ProductionBatch::create([
        'batch_number' => 'LOT-TEST-999',
        'product_id' => $this->product->id,
        'production_line_id' => $this->line->id,
        'supervisor_id' => $this->admin->id,
        'production_date' => now()->toDateString(),
        'shift' => 'Shift 1',
        'target_qty' => 500,
        'status' => BatchStatus::InProduction,
    ]);

    $response = $this->actingAs($this->qc)->post(route('admin.inspections.store'), [
        'production_batch_id' => $batch->id,
        'inspection_stage' => 'in_process',
        'inspection_time' => now()->toDateTimeString(),
        'sample_size_inspected' => 100,
        'defective_units_qty' => 4,
        'defects' => [
            [
                'defect_type_id' => $this->defectType->id,
                'defect_qty' => 4,
                'root_cause_category' => 'machine',
                'root_cause_notes' => 'Tooling offset',
            ],
        ],
    ]);

    $response->assertRedirect();
    $this->assertDatabaseHas('quality_inspections', [
        'production_batch_id' => $batch->id,
        'sample_size_inspected' => 100,
        'defective_units_qty' => 4,
        'total_defects_count' => 4,
    ]);
});

test('preview calculation api returns accurate six sigma metrics', function () {
    $response = $this->actingAs($this->qc)->postJson(route('admin.inspections.calculate-preview'), [
        'sample_size' => 100,
        'defective_units' => 4,
        'total_defects' => 4,
        'opportunities_per_unit' => 6,
    ]);

    $response->assertOk()
        ->assertJsonStructure(['dpu', 'dpo', 'dpmo', 'yield', 'sigma_level']);

    $data = $response->json();
    expect($data['dpu'])->toEqual(0.04);
    expect($data['yield'])->toEqual(96);
    expect((float) $data['sigma_level'])->toBeGreaterThan(3.90);
});

test('can record capa action via web form', function () {
    $response = $this->actingAs($this->admin)->post(route('admin.capa.store'), [
        'defect_type_id' => $this->defectType->id,
        'assigned_to_user_id' => $this->qc->id,
        'title' => 'Kalibrasi Spindle CNC untuk Menghilangkan Oversize',
        'problem_statement' => 'Ditemukan cacat oversize pada diameter silinder rem',
        'root_cause_analysis' => '5-Why: Spindle thermal expansion compensation gagal',
        'corrective_action' => 'Reset offset kalibrasi sensor temperatur',
        'preventive_action' => 'Pemeriksaan harian temperatur pendingin oli',
        'target_completion_date' => now()->addDays(5)->toDateString(),
        'status' => 'open',
    ]);

    $response->assertRedirect();
    $this->assertDatabaseHas('capa_actions', [
        'defect_type_id' => $this->defectType->id,
        'title' => 'Kalibrasi Spindle CNC untuk Menghilangkan Oversize',
        'status' => CapaStatus::Open,
    ]);
});
