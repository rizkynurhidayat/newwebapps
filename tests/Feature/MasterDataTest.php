<?php

use App\Enums\UserRole;
use App\Models\DefectCategory;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->admin = User::factory()->create([
        'role' => UserRole::Admin,
        'is_active' => true,
    ]);
});

test('admin can create manufacturing product with ctq opportunities', function () {
    $response = $this->actingAs($this->admin)->post(route('admin.products.store'), [
        'part_number' => 'PART-TEST-09',
        'name' => 'Brake Master Cylinder',
        'description' => 'Komponen rem hidrolik',
        'unit' => 'pcs',
        'defect_opportunities_per_unit' => 6,
        'standard_cycle_time' => 35.5,
    ]);

    $response->assertRedirect(route('admin.products.index'));
    $this->assertDatabaseHas('products', [
        'part_number' => 'PART-TEST-09',
        'name' => 'Brake Master Cylinder',
        'defect_opportunities_per_unit' => 6,
    ]);
});

test('product part number must be unique', function () {
    Product::create([
        'part_number' => 'PART-DUP-01',
        'name' => 'Original Part',
        'unit' => 'pcs',
        'defect_opportunities_per_unit' => 5,
    ]);

    $response = $this->actingAs($this->admin)->post(route('admin.products.store'), [
        'part_number' => 'PART-DUP-01',
        'name' => 'Duplicate Part',
        'unit' => 'pcs',
        'defect_opportunities_per_unit' => 5,
    ]);

    $response->assertSessionHasErrors('part_number');
});

test('admin can create production line and defect type', function () {
    $lineResponse = $this->actingAs($this->admin)->post(route('admin.lines.store'), [
        'line_code' => 'LINE-STMP-01',
        'name' => 'Stamping Press Line 1',
        'location' => 'Workshop Stamping',
        'status' => 'operational',
    ]);
    $lineResponse->assertRedirect(route('admin.lines.index'));
    $this->assertDatabaseHas('production_lines', ['line_code' => 'LINE-STMP-01']);

    $cat = DefectCategory::create(['code' => 'CAT-TEST', 'name' => 'Test Category']);

    $defectResponse = $this->actingAs($this->admin)->post(route('admin.defects.store'), [
        'defect_category_id' => $cat->id,
        'code' => 'DEF-MICRO-01',
        'name' => 'Micro Crack',
        'severity' => 'critical',
        'default_5m_category' => 'material',
    ]);
    $defectResponse->assertRedirect(route('admin.defects.index'));
    $this->assertDatabaseHas('defect_types', ['code' => 'DEF-MICRO-01']);
});
