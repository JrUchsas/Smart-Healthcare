<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Doctor;
use App\Models\Patient;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;
use Spatie\Permission\Models\Role;

class RegisteredUserController extends Controller
{
    /**
     * Display Patient registration view.
     */
    public function createPatient(): View
    {
        return view('auth.register-patient');
    }

    /**
     * Display Doctor registration view.
     */
    public function createDoctor(): View
    {
        return view('auth.register-doctor');
    }

    /**
     * Handle incoming Patient registration request.
     */
    public function storePatient(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'user_type' => 'patient',
            'is_active' => true,
        ]);

        Role::firstOrCreate(['name' => 'Patient', 'guard_name' => 'web']);
        $user->assignRole('Patient');
        Patient::firstOrCreate(['user_id' => $user->id]);

        event(new Registered($user));

        Auth::login($user);

        return redirect(route('dashboard', absolute: false));
    }

    /**
     * Handle incoming Doctor registration request.
     */
    public function storeDoctor(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'qualification' => ['nullable', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:30'],
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'user_type' => 'doctor',
            'is_active' => false, // Pending Admin Verification
        ]);

        Role::firstOrCreate(['name' => 'Doctor', 'guard_name' => 'web']);
        $user->assignRole('Doctor');

        Doctor::firstOrCreate(
            ['user_id' => $user->id],
            [
                'qualification' => $request->input('qualification', 'MBBS'),
                'phone' => $request->input('phone'),
                'is_available' => false, // Pending Admin Verification
            ]
        );

        event(new Registered($user));

        Auth::login($user);

        return redirect(route('dashboard', absolute: false));
    }
}
