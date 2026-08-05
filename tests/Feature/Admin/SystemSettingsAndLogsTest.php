<?php

use App\Models\ActivityLog;
use App\Models\SystemSetting;
use App\Models\User;
use Database\Seeders\RoleAndPermissionSeeder;

beforeEach(function () {
    $this->seed(RoleAndPermissionSeeder::class);

    $this->adminUser = User::factory()->create(['user_type' => 'admin']);
    $this->adminUser->assignRole('Admin');
});

test('admin can access activity logs view', function () {
    ActivityLog::log('Test Event', 'Testing audit trail logging system', $this->adminUser);

    $response = $this->actingAs($this->adminUser)
        ->get(route('admin.activity-logs.index'));

    $response->assertStatus(200);
    $response->assertSee('Audit Trail &amp; Activity Logs', false);
    $response->assertSee('Testing audit trail logging system');
});

test('admin can access and update system settings', function () {
    $response = $this->actingAs($this->adminUser)
        ->get(route('admin.settings.index'));

    $response->assertStatus(200);
    $response->assertSee('Global System Configuration');

    $updateResponse = $this->actingAs($this->adminUser)
        ->put(route('admin.settings.update'), [
            'hospital_name' => 'Metro General Hospital',
            'hospital_tagline' => 'Excellence in Patient Care',
            'contact_email' => 'admin@metrohospital.com',
            'contact_phone' => '+880 1800-555019',
            'emergency_hotline' => '+880 1611-999999',
            'hospital_address' => '100 Medical Plaza, NY',
            'currency_symbol' => '৳',
            'tax_percentage' => 5.50,
            'default_consultation_fee' => 120.00,
            'payment_mode' => 'sandbox',
            'appointment_cancellation_hours' => 12,
            'default_slot_duration' => 30,
            'max_booking_advance_days' => 60,
            'auto_confirm_appointments' => '1',
            'enable_email_notifications' => '1',
            'enable_sms_alerts' => '1',
            'enable_patient_chat' => '1',
            'prescription_header_notice' => 'Digital Prescription',
        ]);

    $updateResponse->assertRedirect(route('admin.settings.index'));
    expect(SystemSetting::get('hospital_name'))->toBe('Metro General Hospital');
    expect(SystemSetting::get('contact_email'))->toBe('admin@metrohospital.com');
    expect(SystemSetting::get('default_consultation_fee'))->toBe('120');
});
