<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\SystemSetting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SystemSettingController extends Controller
{
    /**
     * Display System Settings configuration page with comprehensive clinic parameters.
     */
    public function index(): View
    {
        $settings = [
            // General Clinic Identity
            'hospital_name' => SystemSetting::get('hospital_name', 'Smart Healthcare Medical Center'),
            'hospital_tagline' => SystemSetting::get('hospital_tagline', 'Advanced Digital Patient Care & Clinical Management'),
            'contact_email' => SystemSetting::get('contact_email', 'contact@smarthealthcare.com'),
            'contact_phone' => SystemSetting::get('contact_phone', '+1 (800) 555-0199'),
            'emergency_hotline' => SystemSetting::get('emergency_hotline', '+1 (800) 911-9999'),
            'hospital_address' => SystemSetting::get('hospital_address', '742 Evergreen Medical Parkway, Suite 400, New York, NY 10001'),

            // Financial & Billing
            'currency_symbol' => SystemSetting::get('currency_symbol', '$'),
            'tax_percentage' => SystemSetting::get('tax_percentage', '0'),
            'default_consultation_fee' => SystemSetting::get('default_consultation_fee', '100.00'),
            'payment_mode' => SystemSetting::get('payment_mode', 'sandbox'),

            // Scheduling & Appointment Rules
            'appointment_cancellation_hours' => SystemSetting::get('appointment_cancellation_hours', '24'),
            'default_slot_duration' => SystemSetting::get('default_slot_duration', '30'),
            'max_booking_advance_days' => SystemSetting::get('max_booking_advance_days', '30'),
            'auto_confirm_appointments' => SystemSetting::get('auto_confirm_appointments', '1'),

            // Notifications & Features
            'enable_email_notifications' => SystemSetting::get('enable_email_notifications', '1'),
            'enable_sms_alerts' => SystemSetting::get('enable_sms_alerts', '1'),
            'enable_patient_chat' => SystemSetting::get('enable_patient_chat', '1'),
            'prescription_header_notice' => SystemSetting::get('prescription_header_notice', 'Official Digital Prescription - Valid for 30 Days from Issue Date'),
        ];

        return view('admin.settings.index', compact('settings'));
    }

    /**
     * Update system settings.
     */
    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'hospital_name' => ['required', 'string', 'max:255'],
            'hospital_tagline' => ['nullable', 'string', 'max:255'],
            'contact_email' => ['required', 'email', 'max:255'],
            'contact_phone' => ['required', 'string', 'max:50'],
            'emergency_hotline' => ['nullable', 'string', 'max:50'],
            'hospital_address' => ['nullable', 'string', 'max:500'],

            'currency_symbol' => ['required', 'string', 'max:10'],
            'tax_percentage' => ['required', 'numeric', 'min:0', 'max:100'],
            'default_consultation_fee' => ['required', 'numeric', 'min:0'],
            'payment_mode' => ['required', 'string', 'in:sandbox,live'],

            'appointment_cancellation_hours' => ['required', 'integer', 'min:1'],
            'default_slot_duration' => ['required', 'integer', 'in:15,30,45,60'],
            'max_booking_advance_days' => ['required', 'integer', 'min:1', 'max:365'],
            'auto_confirm_appointments' => ['required', 'in:0,1'],

            'enable_email_notifications' => ['required', 'in:0,1'],
            'enable_sms_alerts' => ['required', 'in:0,1'],
            'enable_patient_chat' => ['required', 'in:0,1'],
            'prescription_header_notice' => ['nullable', 'string', 'max:500'],
        ]);

        foreach ($validated as $key => $value) {
            SystemSetting::set($key, (string) $value);
        }

        ActivityLog::log('Settings Updated', 'Admin updated global system parameters and clinic configuration.', null, 'Settings');

        return redirect()->route('admin.settings.index')
            ->with('success', 'System settings updated successfully.');
    }
}
