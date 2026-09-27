<?php

use App\Enums\RiskLevel;
use App\Enums\UserRole;
use App\Models\MachineRiskAssessment;
use App\Models\User;
use App\Services\RiskAssessmentService;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->user = User::factory()->create([
        'role' => UserRole::Admin,
        'is_active' => true,
    ]);
});

test('auto calculates risk score and risk level correctly on model saving', function () {
    // Low risk: 2 x 2 = 4 -> Low
    $low = MachineRiskAssessment::create([
        'hazard_name' => 'Ceceran pelumas ringan',
        'machine_area' => 'Mesin Press 150T',
        'risk_description' => 'Terpeleset ringan',
        'likelihood' => 2,
        'severity' => 2,
    ]);

    expect($low->risk_score)->toBe(4);
    expect($low->risk_level)->toBe(RiskLevel::Low);
    expect($low->hazard_code)->toStartWith('BHY-');

    // Medium risk: 3 x 3 = 9 -> Medium
    $medium = MachineRiskAssessment::create([
        'hazard_name' => 'Tepi plat tajam',
        'machine_area' => 'Feeder Coil',
        'risk_description' => 'Luka sayat jari',
        'likelihood' => 3,
        'severity' => 3,
    ]);

    expect($medium->risk_score)->toBe(9);
    expect($medium->risk_level)->toBe(RiskLevel::Medium);

    // High risk: 3 x 5 = 15 -> High
    $high = MachineRiskAssessment::create([
        'hazard_name' => 'Titik jepit die press',
        'machine_area' => 'Mesin Press 250T',
        'risk_description' => 'Fraktur jari operator',
        'likelihood' => 3,
        'severity' => 5,
    ]);

    expect($high->risk_score)->toBe(15);
    expect($high->risk_level)->toBe(RiskLevel::High);

    // Extreme risk: 4 x 5 = 20 -> Extreme
    $extreme = MachineRiskAssessment::create([
        'hazard_name' => 'Hantaman flywheel tanpa pelindung',
        'machine_area' => 'Flywheel Press 300T',
        'risk_description' => 'Fatalitas benturan',
        'likelihood' => 4,
        'severity' => 5,
    ]);

    expect($extreme->risk_score)->toBe(20);
    expect($extreme->risk_level)->toBe(RiskLevel::Extreme);
});

test('risk assessment service generates accurate dashboard KPI summary and 5x5 matrix', function () {
    MachineRiskAssessment::create([
        'hazard_name' => 'Hazard A',
        'machine_area' => 'Press 1',
        'risk_description' => 'Desc A',
        'likelihood' => 2,
        'severity' => 2, // 4 -> Low
    ]);

    MachineRiskAssessment::create([
        'hazard_name' => 'Hazard B',
        'machine_area' => 'Press 2',
        'risk_description' => 'Desc B',
        'likelihood' => 4,
        'severity' => 4, // 16 -> Extreme
    ]);

    $service = app(RiskAssessmentService::class);
    $summary = $service->getDashboardSummary();
    $matrix = $service->getRiskMatrix5x5();

    expect($summary['total_hazards'])->toBe(2);
    expect($summary['total_risks'])->toBe(2);
    expect($summary['avg_score'])->toBe(10.0);
    expect($summary['distribution']['low'])->toBe(1);
    expect($summary['distribution']['extreme'])->toBe(1);
    expect($matrix[2][2]['count'])->toBe(1);
    expect($matrix[4][4]['count'])->toBe(1);
});

test('user can view risks dashboard and create new risk assessment', function () {
    $response = $this->actingAs($this->user)->get(route('admin.risks.index'));
    $response->assertOk();
    $response->assertSee('Analisa Resiko Kerja Mesin Stamping Press');

    $createResponse = $this->actingAs($this->user)->post(route('admin.risks.store'), [
        'hazard_name' => 'Scrap terpental saat blanking',
        'machine_area' => 'Mesin Press 250 Ton',
        'risk_description' => 'Luka mata akibat partikel scrap excrap',
        'likelihood' => 4,
        'severity' => 3,
        'control_measures' => 'Goggles wajib dan safety shield mika',
        'pic' => 'QC Officer',
        'status' => 'active',
    ]);

    $createResponse->assertRedirect(route('admin.risks.index'));

    $this->assertDatabaseHas('machine_risk_assessments', [
        'hazard_name' => 'Scrap terpental saat blanking',
        'risk_score' => 12,
        'risk_level' => 'high',
    ]);
});
