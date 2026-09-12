<?php

use App\Models\DefectCategory;
use App\Models\DefectType;
use App\Services\SixSigmaCalculatorService;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->calculator = app(SixSigmaCalculatorService::class);
});

test('calculates accurate dpu, dpo, dpmo, yield, and sigma level', function () {
    // Sample Size = 100, Passed = 95, Defective Units = 5, Total Defects = 5, Opportunities = 5
    $metrics = $this->calculator->calculateMetrics(
        sampleSize: 100,
        passedQty: 95,
        defectsCount: 5,
        opportunitiesPerUnit: 5
    );

    // DPU = 5 / 100 = 0.05
    expect($metrics['dpu'])->toBe(0.05);

    // Total opportunities = 100 * 5 = 500
    // DPO = 5 / 500 = 0.010000
    expect($metrics['dpo'])->toBe(0.01);

    // DPMO = 0.01 * 1,000,000 = 10,000
    expect($metrics['dpmo'])->toBe(10000.0);

    // Yield = (95 / 100) * 100 = 95.0%
    expect($metrics['yield'])->toBe(95.0);

    // Sigma Level for DPMO 10,000 with 1.5 sigma shift is approx 3.83
    expect($metrics['sigma_level'])->toBeGreaterThan(3.70)
        ->toBeLessThan(4.00);
});

test('returns 6.00 sigma level when zero defects occur', function () {
    $metrics = $this->calculator->calculateMetrics(
        sampleSize: 100,
        passedQty: 100,
        defectsCount: 0,
        opportunitiesPerUnit: 5
    );

    expect($metrics['dpmo'])->toBe(0.0);
    expect($metrics['yield'])->toBe(100.0);
    expect($metrics['sigma_level'])->toBe(6.00);
});

test('computes pareto 80/20 distribution with vital few identification', function () {
    $cat = DefectCategory::create(['code' => 'CAT-TEST', 'name' => 'Testing']);
    $typeA = DefectType::create(['defect_category_id' => $cat->id, 'code' => 'DEF-A', 'name' => 'Major Scratch']);
    $typeB = DefectType::create(['defect_category_id' => $cat->id, 'code' => 'DEF-B', 'name' => 'Micro Crack']);
    $typeC = DefectType::create(['defect_category_id' => $cat->id, 'code' => 'DEF-C', 'name' => 'Burrs']);

    $defects = collect([
        (object) ['defect_type_id' => $typeA->id, 'defect_qty' => 70, 'defectType' => $typeA],
        (object) ['defect_type_id' => $typeB->id, 'defect_qty' => 20, 'defectType' => $typeB],
        (object) ['defect_type_id' => $typeC->id, 'defect_qty' => 10, 'defectType' => $typeC],
    ]);

    $pareto = $this->calculator->calculatePareto($defects);

    expect($pareto['total_defects'])->toBe(100);
    expect($pareto['items'][0]['defect_code'])->toBe('DEF-A');
    expect($pareto['items'][0]['percentage'])->toBe(70.0);
    expect($pareto['items'][0]['is_vital_few'])->toBeTrue();
    expect($pareto['items'][1]['defect_code'])->toBe('DEF-B');
});

test('computes spc p-chart control limits properly', function () {
    $inspections = collect([
        (object) ['id' => 1, 'inspection_number' => 'QC-1', 'sample_size_inspected' => 100, 'defective_units_qty' => 4, 'inspection_time' => now()],
        (object) ['id' => 2, 'inspection_number' => 'QC-2', 'sample_size_inspected' => 100, 'defective_units_qty' => 5, 'inspection_time' => now()],
        (object) ['id' => 3, 'inspection_number' => 'QC-3', 'sample_size_inspected' => 100, 'defective_units_qty' => 6, 'inspection_time' => now()],
    ]);

    $spc = $this->calculator->calculateSpcControlChart($inspections);

    // Total inspected = 300, total defects = 15 => p_bar = 0.05
    expect($spc['cl'])->toBe(0.05);
    expect($spc['ucl'])->toBeGreaterThan(0.05);
    expect($spc['lcl'])->toBeLessThan(0.05);
    expect($spc['points'])->toHaveCount(3);
});
