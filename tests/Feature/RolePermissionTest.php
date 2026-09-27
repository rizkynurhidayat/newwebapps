<?php

use App\Enums\UserRole;
use App\Models\Product;
use App\Models\ProductionBatch;
use App\Models\ProductionLine;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->admin = User::factory()->create([
        'name' => 'Super Admin Test',
        'role' => UserRole::Admin,
        'is_active' => true,
    ]);

    $this->qcStaff = User::factory()->create([
        'name' => 'QC Staff Test',
        'role' => UserRole::Staff,
        'is_active' => true,
    ]);

    $this->supervisor = User::factory()->create([
        'name' => 'Supervisor Test',
        'role' => UserRole::Employee,
        'is_active' => true,
    ]);

    $this->product = Product::create([
        'part_number' => 'BSL-TEST-01',
        'name' => 'Bracket Seat Leg Test',
        'unit' => 'pcs',
        'defect_opportunities_per_unit' => 5,
        'standard_cycle_time' => 15.0,
        'is_active' => true,
    ]);

    $this->line = ProductionLine::create([
        'line_code' => 'LINE-STMP-TEST',
        'name' => 'Lini Stamping Test',
        'status' => 'operational',
    ]);

    $this->batch = ProductionBatch::create([
        'batch_number' => 'BATCH-TEST-001',
        'product_id' => $this->product->id,
        'production_line_id' => $this->line->id,
        'supervisor_id' => $this->supervisor->id,
        'target_qty' => 500,
        'actual_qty' => 500,
        'production_date' => now()->toDateString(),
        'status' => 'in_production',
    ]);
});

test('qc staff can access inspection create but cannot access batch create or user management', function () {
    // QC Staff can access inspections create
    $this->actingAs($this->qcStaff)
        ->get(route('admin.inspections.create'))
        ->assertOk();

    // QC Staff cannot access batch create (Forbidden 403)
    $this->actingAs($this->qcStaff)
        ->get(route('admin.batches.create'))
        ->assertForbidden();

    // QC Staff cannot access user management (Forbidden 403)
    $this->actingAs($this->qcStaff)
        ->get(route('admin.users.index'))
        ->assertForbidden();

    // QC Staff cannot create master data products (Forbidden 403)
    $this->actingAs($this->qcStaff)
        ->get(route('admin.products.create'))
        ->assertForbidden();
});

test('production supervisor can create batches but cannot create inspections or access user management', function () {
    // Supervisor can access batch create
    $this->actingAs($this->supervisor)
        ->get(route('admin.batches.create'))
        ->assertOk();

    // Supervisor can access risks create
    $this->actingAs($this->supervisor)
        ->get(route('admin.risks.create'))
        ->assertOk();

    // Supervisor cannot create inspections (Forbidden 403)
    $this->actingAs($this->supervisor)
        ->get(route('admin.inspections.create'))
        ->assertForbidden();

    // Supervisor cannot access user management (Forbidden 403)
    $this->actingAs($this->supervisor)
        ->get(route('admin.users.index'))
        ->assertForbidden();

    // Supervisor cannot access master data lines create (Forbidden 403)
    $this->actingAs($this->supervisor)
        ->get(route('admin.lines.create'))
        ->assertForbidden();
});

test('super admin can access and manage users', function () {
    // Super admin can access user list
    $this->actingAs($this->admin)
        ->get(route('admin.users.index'))
        ->assertOk()
        ->assertSee('Manajemen Pengguna');

    // Super admin can create new user
    $response = $this->actingAs($this->admin)
        ->post(route('admin.users.store'), [
            'name' => 'Operator Baru',
            'email' => 'operator.baru@pt-seatleg.com',
            'password' => 'secret12345',
            'password_confirmation' => 'secret12345',
            'role' => UserRole::Staff->value,
            'department' => 'Quality Control',
            'phone' => '081234567890',
            'is_active' => true,
        ]);

    $response->assertRedirect(route('admin.users.index'));
    $this->assertDatabaseHas('users', [
        'email' => 'operator.baru@pt-seatleg.com',
        'role' => UserRole::Staff->value,
    ]);

    // Super admin can toggle user status
    $targetUser = User::where('email', 'operator.baru@pt-seatleg.com')->first();
    $toggleResponse = $this->actingAs($this->admin)
        ->patch(route('admin.users.toggle-status', $targetUser));

    $toggleResponse->assertRedirect();
    $this->assertFalse((bool) $targetUser->fresh()->is_active);
});

test('super admin cannot deactivate or delete their own account', function () {
    // Attempt self-deactivation via toggle
    $toggleResponse = $this->actingAs($this->admin)
        ->patch(route('admin.users.toggle-status', $this->admin));

    $toggleResponse->assertRedirect(route('admin.users.index'));
    $toggleResponse->assertSessionHas('error');
    $this->assertTrue((bool) $this->admin->fresh()->is_active);

    // Attempt self-deletion
    $deleteResponse = $this->actingAs($this->admin)
        ->delete(route('admin.users.destroy', $this->admin));

    $deleteResponse->assertRedirect(route('admin.users.index'));
    $deleteResponse->assertSessionHas('error');
    $this->assertDatabaseHas('users', ['id' => $this->admin->id]);
});

test('deactivated user is logged out by role middleware', function () {
    $deactivatedUser = User::factory()->create([
        'role' => UserRole::Staff,
        'is_active' => false,
    ]);

    $response = $this->actingAs($deactivatedUser)
        ->get(route('admin.inspections.create'));

    $response->assertRedirect(route('login'));
    $this->assertGuest();
});
