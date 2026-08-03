<?php

namespace Database\Seeders;

use App\Models\Department;
use App\Models\Doctor;
use App\Models\Specialization;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class DoctorSeeder extends Seeder
{
    /**
     * Seed default Doctor user and profile.
     */
    public function run(): void
    {
        // 1. Create Cardiology department & specialization
        $department = Department::firstOrCreate(
            ['slug' => 'cardiology'],
            [
                'name' => 'Cardiology Department',
                'description' => 'Cardiovascular care, heart disease treatment, and surgery.',
                'is_active' => true,
            ]
        );

        $specialization = Specialization::firstOrCreate(
            ['slug' => 'interventional-cardiology'],
            [
                'department_id' => $department->id,
                'name' => 'Interventional Cardiology',
                'description' => 'Catheter-based treatment of structural heart diseases.',
                'is_active' => true,
            ]
        );

        // 2. Ensure Doctor Spatie role exists
        $doctorRole = Role::firstOrCreate(['name' => 'Doctor', 'guard_name' => 'web']);

        // 3. Create Default Doctor User
        $doctorUser = User::updateOrCreate(
            ['email' => 'doctor@smarthealthcare.com'],
            [
                'name' => 'Dr. Sarah Jenkins',
                'password' => Hash::make('doctor12345'),
                'user_type' => 'doctor',
                'is_active' => true,
                'email_verified_at' => now(),
            ]
        );

        $doctorUser->assignRole($doctorRole);

        // 4. Create Doctor profile record
        Doctor::updateOrCreate(
            ['user_id' => $doctorUser->id],
            [
                'department_id' => $department->id,
                'specialization_id' => $specialization->id,
                'qualification' => 'MD, FACC (Cardiovascular Medicine)',
                'experience_years' => 10,
                'consultation_fee' => 100.00,
                'phone' => '+1 (555) 234-5678',
                'bio' => 'Senior Cardiologist specializing in preventive heart care and angioplasty.',
                'is_available' => true,
            ]
        );
    }
}
