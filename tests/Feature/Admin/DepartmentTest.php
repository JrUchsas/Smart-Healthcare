<?php

use App\Models\User;
use Database\Seeders\RoleAndPermissionSeeder;

beforeEach(function () {
    $this->seed(RoleAndPermissionSeeder::class);

    $this->admin = User::factory()->create([
        'user_type' => 'admin',
    ]);
    $this->admin->assignRole('Admin');
});

test('admin can view department management index', function () {
    $response = $this->actingAs($this->admin)->get(route('admin.departments.index'));

    $response->assertStatus(200);
});

test('admin can create a medical department', function () {
    $response = $this->actingAs($this->admin)->post(route('admin.departments.store'), [
        'name' => 'Cardiology Department',
        'description' => 'Cardiovascular care and heart surgery',
        'is_active' => 1,
    ]);

    $response->assertRedirect(route('admin.departments.index'));
    $this->assertDatabaseHas('departments', [
        'name' => 'Cardiology Department',
        'slug' => 'cardiology-department',
    ]);
});

test('non admin cannot access department management', function () {
    $patient = User::factory()->create(['user_type' => 'patient']);
    $patient->assignRole('Patient');

    $response = $this->actingAs($patient)->get(route('admin.departments.index'));

    $response->assertStatus(403);
});
