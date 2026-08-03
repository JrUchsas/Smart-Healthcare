<?php

use App\Models\Department;
use App\Models\User;
use Database\Seeders\RoleAndPermissionSeeder;

beforeEach(function () {
    $this->seed(RoleAndPermissionSeeder::class);

    $this->admin = User::factory()->create([
        'user_type' => 'admin',
    ]);
    $this->admin->assignRole('Admin');

    $this->department = Department::create([
        'name' => 'Neurology',
        'slug' => 'neurology',
        'is_active' => true,
    ]);
});

test('admin can view specialization index', function () {
    $response = $this->actingAs($this->admin)->get(route('admin.specializations.index'));

    $response->assertStatus(200);
});

test('admin can create a medical specialization', function () {
    $response = $this->actingAs($this->admin)->post(route('admin.specializations.store'), [
        'department_id' => $this->department->id,
        'name' => 'Pediatric Neurology',
        'description' => 'Child brain care',
        'is_active' => 1,
    ]);

    $response->assertRedirect(route('admin.specializations.index'));
    $this->assertDatabaseHas('specializations', [
        'name' => 'Pediatric Neurology',
        'slug' => 'pediatric-neurology',
        'department_id' => $this->department->id,
    ]);
});
