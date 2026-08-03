<x-guest-layout>
    <form method="POST" action="{{ route('register') }}">
        @csrf

        <!-- Account Type Selector -->
        <div class="mb-4">
            <x-input-label for="user_type" :value="__('I am registering as:')" />
            <div class="grid grid-cols-2 gap-4 mt-2">
                <label class="flex items-center p-3 border rounded-lg cursor-pointer hover:bg-gray-50 has-[:checked]:border-indigo-600 has-[:checked]:bg-indigo-50">
                    <input type="radio" name="user_type" value="patient" class="text-indigo-600 focus:ring-indigo-500" {{ old('user_type', 'patient') === 'patient' ? 'checked' : '' }}>
                    <span class="ms-2 font-semibold text-gray-700">Patient</span>
                </label>
                <label class="flex items-center p-3 border rounded-lg cursor-pointer hover:bg-gray-50 has-[:checked]:border-indigo-600 has-[:checked]:bg-indigo-50">
                    <input type="radio" name="user_type" value="doctor" class="text-indigo-600 focus:ring-indigo-500" {{ old('user_type') === 'doctor' ? 'checked' : '' }}>
                    <span class="ms-2 font-semibold text-gray-700">Doctor</span>
                </label>
            </div>
            <x-input-error :messages="$errors->get('user_type')" class="mt-2" />
        </div>

        <!-- Name -->
        <div>
            <x-input-label for="name" :value="__('Full Name')" />
            <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name')" required autofocus autocomplete="name" placeholder="John Doe" />
            <x-input-error :messages="$errors->get('name')" class="mt-2" />
        </div>

        <!-- Email Address -->
        <div class="mt-4">
            <x-input-label for="email" :value="__('Email Address')" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autocomplete="username" placeholder="name@example.com" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Password -->
        <div class="mt-4">
            <x-input-label for="password" :value="__('Password')" />

            <x-text-input id="password" class="block mt-1 w-full"
                            type="password"
                            name="password"
                            required autocomplete="new-password" />

            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Confirm Password -->
        <div class="mt-4">
            <x-input-label for="password_confirmation" :value="__('Confirm Password')" />

            <x-text-input id="password_confirmation" class="block mt-1 w-full"
                            type="password"
                            name="password_confirmation" required autocomplete="new-password" />

            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <div class="flex items-center justify-end mt-4">
            <a class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500" href="{{ route('login') }}">
                {{ __('Already registered?') }}
            </a>

            <x-primary-button class="ms-4">
                {{ __('Register') }}
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>
