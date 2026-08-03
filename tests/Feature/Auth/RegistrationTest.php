<?php

use Database\Seeders\RoleAndPermissionSeeder;

beforeEach(function () {
    $this->seed(RoleAndPermissionSeeder::class);
});

test('patient registration screen can be rendered', function () {
    $response = $this->get('/register/patient');

    $response->assertStatus(200);
});

test('doctor registration screen can be rendered', function () {
    $response = $this->get('/register/doctor');

    $response->assertStatus(200);
});

test('new patient can register via patient portal', function () {
    $response = $this->post('/register/patient', [
        'name' => 'Patient User',
        'email' => 'patient.portal@example.com',
        'password' => 'password',
        'password_confirmation' => 'password',
    ]);

    $this->assertAuthenticated();
    $this->assertDatabaseHas('users', [
        'email' => 'patient.portal@example.com',
        'user_type' => 'patient',
    ]);
    $this->assertDatabaseHas('patients', [
        'user_id' => auth()->id(),
    ]);

    $response->assertRedirect(route('dashboard', absolute: false));
});

test('new doctor can register via doctor portal', function () {
    $response = $this->post('/register/doctor', [
        'name' => 'Dr. Robert Blake',
        'email' => 'doctor.portal@example.com',
        'qualification' => 'MBBS, MD',
        'phone' => '+1 (555) 345-6789',
        'password' => 'password',
        'password_confirmation' => 'password',
    ]);

    $this->assertAuthenticated();
    $this->assertDatabaseHas('users', [
        'email' => 'doctor.portal@example.com',
        'user_type' => 'doctor',
    ]);
    $this->assertDatabaseHas('doctors', [
        'user_id' => auth()->id(),
        'qualification' => 'MBBS, MD',
    ]);

    $response->assertRedirect(route('dashboard', absolute: false));
});
