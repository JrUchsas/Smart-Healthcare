<?php

use App\Models\Department;
use App\Models\Doctor;
use App\Models\Specialization;
use App\Models\User;
use Database\Seeders\RoleAndPermissionSeeder;

beforeEach(function () {
    $this->seed(RoleAndPermissionSeeder::class);

    $this->admin = User::factory()->create(['user_type' => 'admin']);
    $this->admin->assignRole('Admin');

    $this->dept = Department::create(['name' => 'Cardiology', 'slug' => 'cardiology', 'is_active' => true]);
    $this->spec = Specialization::create(['department_id' => $this->dept->id, 'name' => 'Pediatric Cardiology', 'slug' => 'pediatric-cardiology', 'is_active' => true]);

    $this->doctorUser = User::factory()->create(['user_type' => 'doctor']);
    $this->doctorUser->assignRole('Doctor');
    $this->doctor = Doctor::create([
        'user_id' => $this->doctorUser->id,
        'consultation_fee' => 50.00,
        'experience_years' => 3,
        'is_available' => true,
    ]);
});

test('admin can view doctor management index page', function () {
    $response = $this->actingAs($this->admin)->get(route('admin.doctors.index'));

    $response->assertStatus(200);
});

test('admin can assign department and specialization to doctor', function () {
    $response = $this->actingAs($this->admin)->put(route('admin.doctors.update', $this->doctor), [
        'department_id' => $this->dept->id,
        'specialization_id' => $this->spec->id,
        'qualification' => 'MBBS, MD',
        'experience_years' => 7,
        'consultation_fee' => 120.00,
        'phone' => '+1 555-999-8888',
        'bio' => 'Experienced cardiologist',
        'is_available' => 1,
    ]);

    $response->assertRedirect(route('admin.doctors.index'));

    $this->assertDatabaseHas('doctors', [
        'id' => $this->doctor->id,
        'department_id' => $this->dept->id,
        'specialization_id' => $this->spec->id,
        'consultation_fee' => 120.00,
    ]);
});

test('admin can fetch specializations by department via AJAX', function () {
    $response = $this->actingAs($this->admin)->get(route('admin.departments.specializations', $this->dept));

    $response->assertStatus(200)
        ->assertJson([
            'success' => true,
        ]);
});
