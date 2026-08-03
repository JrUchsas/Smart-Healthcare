<x-guest-layout>
    <div class="mb-6 text-center">
        <span class="inline-block px-3 py-1 bg-emerald-100 text-emerald-800 text-xs font-semibold rounded-full mb-2">Medical Practitioner Portal</span>
        <h2 class="text-2xl font-bold text-gray-800">Doctor Registration</h2>
        <p class="text-sm text-gray-600 mt-1">Join our medical network and set up your consultation availability</p>
        <div class="mt-3 p-2 bg-gray-50 border border-gray-200 rounded-lg text-xs text-gray-700">
            Are you a patient looking for care? 
            <a href="{{ route('register.patient') }}" class="font-bold underline text-indigo-600 hover:text-indigo-800">Register as a Patient →</a>
        </div>
    </div>

    <form method="POST" action="{{ route('register.doctor') }}">
        @csrf

        <!-- Full Name -->
        <div>
            <x-input-label for="name" :value="__('Doctor Name')" />
            <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name')" required autofocus autocomplete="name" placeholder="Dr. Sarah Jenkins" />
            <x-input-error :messages="$errors->get('name')" class="mt-2" />
        </div>

        <!-- Email Address -->
        <div class="mt-4">
            <x-input-label for="email" :value="__('Professional Email')" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autocomplete="username" placeholder="doctor@smarthealthcare.com" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Qualification -->
        <div class="mt-4">
            <x-input-label for="qualification" :value="__('Medical Qualification')" />
            <x-text-input id="qualification" class="block mt-1 w-full" type="text" name="qualification" :value="old('qualification')" placeholder="MBBS, MD (Cardiology)" />
            <x-input-error :messages="$errors->get('qualification')" class="mt-2" />
        </div>

        <!-- Phone Number -->
        <div class="mt-4">
            <x-input-label for="phone" :value="__('Contact Phone Number')" />
            <x-text-input id="phone" class="block mt-1 w-full" type="text" name="phone" :value="old('phone')" placeholder="+1 (555) 000-0000" />
            <x-input-error :messages="$errors->get('phone')" class="mt-2" />
        </div>

        <!-- Password -->
        <div class="mt-4">
            <x-input-label for="password" :value="__('Password')" />
            <x-text-input id="password" class="block mt-1 w-full" type="password" name="password" required autocomplete="new-password" />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Confirm Password -->
        <div class="mt-4">
            <x-input-label for="password_confirmation" :value="__('Confirm Password')" />
            <x-text-input id="password_confirmation" class="block mt-1 w-full" type="password" name="password_confirmation" required autocomplete="new-password" />
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <div class="flex items-center justify-between mt-6">
            <a class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md" href="{{ route('login') }}">
                {{ __('Already registered?') }}
            </a>

            <x-primary-button class="ms-4 bg-emerald-600 hover:bg-emerald-700">
                {{ __('Register as Doctor') }}
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>
