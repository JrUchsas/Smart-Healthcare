<section>
    <header>
        <h2 class="text-lg font-medium text-gray-900">
            {{ __('Profile Information & Contact Details') }}
        </h2>

        <p class="mt-1 text-sm text-gray-600">
            {{ __("Update your personal account details, phone number, address, and medical profile information.") }}
        </p>
    </header>

    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

    <form method="post" action="{{ route('profile.update') }}" class="mt-6 space-y-6">
        @csrf
        @method('patch')

        {{-- Full Name --}}
        <div>
            <x-input-label for="name" :value="__('Full Name')" />
            <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" :value="old('name', $user->name)" required autofocus autocomplete="name" />
            <x-input-error class="mt-2" :messages="$errors->get('name')" />
        </div>

        {{-- Email Address --}}
        <div>
            <x-input-label for="email" :value="__('Email Address')" />
            <x-text-input id="email" name="email" type="email" class="mt-1 block w-full" :value="old('email', $user->email)" required autocomplete="username" />
            <x-input-error class="mt-2" :messages="$errors->get('email')" />

            @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                <div>
                    <p class="text-sm mt-2 text-gray-800">
                        {{ __('Your email address is unverified.') }}

                        <button form="send-verification" class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            {{ __('Click here to re-send the verification email.') }}
                        </button>
                    </p>

                    @if (session('status') === 'verification-link-sent')
                        <p class="mt-2 font-medium text-sm text-green-600">
                            {{ __('A new verification link has been sent to your email address.') }}
                        </p>
                    @endif
                </div>
            @endif
        </div>

        @php
            $patient = $user->patient;
            $doctor = $user->doctor;
        @endphp

        {{-- Phone Number --}}
        <div>
            <x-input-label for="phone" :value="__('Phone Number')" />
            <x-text-input id="phone" name="phone" type="text" class="mt-1 block w-full" :value="old('phone', $patient?->phone ?? $doctor?->phone ?? '')" placeholder="+880 1712-345678" />
            <x-input-error class="mt-2" :messages="$errors->get('phone')" />
        </div>

        {{-- Physical / Clinic Address --}}
        <div>
            <x-input-label for="address" :value="__('Physical / Clinic Address')" />
            <textarea id="address" name="address" rows="2" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" placeholder="Street address, city, state, zip code">{{ old('address', $patient?->address ?? $doctor?->address ?? '') }}</textarea>
            <x-input-error class="mt-2" :messages="$errors->get('address')" />
        </div>

        {{-- Patient Specific Fields --}}
        @if($user->hasRole('Patient') || $user->user_type === 'patient')
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <x-input-label for="dob" :value="__('Date of Birth')" />
                    <x-text-input id="dob" name="dob" type="date" class="mt-1 block w-full" :value="old('dob', $patient?->dob?->format('Y-m-d'))" />
                    <x-input-error class="mt-2" :messages="$errors->get('dob')" />
                </div>

                <div>
                    <x-input-label for="gender" :value="__('Gender')" />
                    <select id="gender" name="gender" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        <option value="">Select Gender</option>
                        <option value="male" {{ old('gender', $patient?->gender) === 'male' ? 'selected' : '' }}>Male</option>
                        <option value="female" {{ old('gender', $patient?->gender) === 'female' ? 'selected' : '' }}>Female</option>
                        <option value="other" {{ old('gender', $patient?->gender) === 'other' ? 'selected' : '' }}>Other</option>
                    </select>
                    <x-input-error class="mt-2" :messages="$errors->get('gender')" />
                </div>

                <div>
                    <x-input-label for="blood_group" :value="__('Blood Group')" />
                    <x-text-input id="blood_group" name="blood_group" type="text" class="mt-1 block w-full" :value="old('blood_group', $patient?->blood_group)" placeholder="O+, A+, B+, etc." />
                    <x-input-error class="mt-2" :messages="$errors->get('blood_group')" />
                </div>

                <div>
                    <x-input-label for="emergency_contact" :value="__('Emergency Contact')" />
                    <x-text-input id="emergency_contact" name="emergency_contact" type="text" class="mt-1 block w-full" :value="old('emergency_contact', $patient?->emergency_contact)" placeholder="Name & Phone" />
                    <x-input-error class="mt-2" :messages="$errors->get('emergency_contact')" />
                </div>
            </div>
        @endif

        {{-- Doctor Specific Fields --}}
        @if($user->hasRole('Doctor') || $user->user_type === 'doctor')
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <x-input-label for="qualification" :value="__('Qualification')" />
                    <x-text-input id="qualification" name="qualification" type="text" class="mt-1 block w-full" :value="old('qualification', $doctor?->qualification)" placeholder="MBBS, MD, FCPS" />
                    <x-input-error class="mt-2" :messages="$errors->get('qualification')" />
                </div>

                <div>
                    <x-input-label for="experience_years" :value="__('Years of Experience')" />
                    <x-text-input id="experience_years" name="experience_years" type="number" class="mt-1 block w-full" :value="old('experience_years', $doctor?->experience_years)" />
                    <x-input-error class="mt-2" :messages="$errors->get('experience_years')" />
                </div>

                <div>
                    <x-input-label for="consultation_fee" :value="__('Consultation Fee ($)')" />
                    <x-text-input id="consultation_fee" name="consultation_fee" type="number" step="0.01" class="mt-1 block w-full" :value="old('consultation_fee', $doctor?->consultation_fee)" />
                    <x-input-error class="mt-2" :messages="$errors->get('consultation_fee')" />
                </div>
            </div>

            <div>
                <x-input-label for="bio" :value="__('Doctor Professional Bio')" />
                <textarea id="bio" name="bio" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" placeholder="Brief description of experience, specialties, and patient care philosophy...">{{ old('bio', $doctor?->bio) }}</textarea>
                <x-input-error class="mt-2" :messages="$errors->get('bio')" />
            </div>
        @endif

        <div class="flex items-center gap-4">
            <x-primary-button>{{ __('Save Changes') }}</x-primary-button>

            @if (session('status') === 'profile-updated')
                <p
                    x-data="{ show: true }"
                    x-show="show"
                    x-transition
                    x-init="setTimeout(() => show = false, 2000)"
                    class="text-sm text-gray-600"
                >{{ __('Saved successfully.') }}</p>
            @endif
        </div>
    </form>
</section>
