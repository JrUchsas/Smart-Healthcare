<?php

namespace Database\Seeders;

use App\Models\Admin;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $adminUser = User::firstOrCreate(
            ['email' => 'admin@smarthealthcare.com'],
            [
                'name' => 'System Administrator',
                'password' => Hash::make('password'),
                'user_type' => 'admin',
                'is_active' => true,
                'email_verified_at' => now(),
            ]
        );

        // Assign Spatie role
        $adminUser->assignRole('Admin');

        // Create linked Admin profile if not exists
        Admin::firstOrCreate(
            ['user_id' => $adminUser->id],
            [
                'phone' => '+1234567890',
                'designation' => 'Super Administrator',
            ]
        );
    }
}
