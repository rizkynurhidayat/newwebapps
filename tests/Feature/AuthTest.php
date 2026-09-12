<?php

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('user can authenticate with valid credentials', function () {
    $user = User::factory()->create([
        'email' => 'staff@company.com',
        'password' => bcrypt('password123'),
        'role' => UserRole::Staff,
        'is_active' => true,
    ]);

    $response = $this->post(route('login.submit'), [
        'email' => 'staff@company.com',
        'password' => 'password123',
    ]);

    $response->assertRedirect(route('dashboard'));
    $this->assertAuthenticatedAs($user);
});

test('user cannot authenticate with invalid password', function () {
    $user = User::factory()->create([
        'email' => 'staff@company.com',
        'password' => bcrypt('password123'),
        'role' => UserRole::Staff,
    ]);

    $response = $this->post(route('login.submit'), [
        'email' => 'staff@company.com',
        'password' => 'wrongpassword',
    ]);

    $response->assertSessionHasErrors('email');
    $this->assertGuest();
});

test('inactive user cannot authenticate', function () {
    User::factory()->create([
        'email' => 'inactive@company.com',
        'password' => bcrypt('password123'),
        'is_active' => false,
    ]);

    $response = $this->post(route('login.submit'), [
        'email' => 'inactive@company.com',
        'password' => 'password123',
    ]);

    $response->assertSessionHasErrors('email');
    $this->assertGuest();
});

test('authenticated user can logout', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->post(route('logout'));

    $response->assertRedirect(route('login'));
    $this->assertGuest();
});

test('unauthenticated user cannot access dashboard', function () {
    $response = $this->get(route('dashboard'));

    $response->assertRedirect(route('login'));
});
