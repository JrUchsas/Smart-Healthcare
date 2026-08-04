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
        $adminUser = User::updateOrCreate(
            ['email' => 'admin@smarthealthcare.com'],
            [
                'name' => 'System Administrator',
                'password' => Hash::make('admin12345'),
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
                'phone' => '+880 1712-000000',
                'designation' => 'Super Administrator',
            ]
        );
    }
}
