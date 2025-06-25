<x-guest-layout title="Sign Up">
    <form method="POST" action="{{ route('register') }}" class="space-y-6">
        @csrf

        <!-- Name -->
        <div>
            <x-input-label for="name" :value="__('Name')" class="text-lg font-medium text-gray-700" />
            <x-text-input id="name" class="block mt-2 w-full text-lg py-3" type="text" name="name" :value="old('name')" required autofocus autocomplete="name" />
            <x-input-error :messages="$errors->get('name')" class="mt-2" />
        </div>

        <!-- Email Address -->
        <div>
            <x-input-label for="email" :value="__('Email')" class="text-lg font-medium text-gray-700" />
            <x-text-input id="email" class="block mt-2 w-full text-lg py-3" type="email" name="email" :value="old('email')" required autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Password -->
        <div>
            <x-input-label for="password" :value="__('Password')" class="text-lg font-medium text-gray-700" />
            <x-text-input id="password" class="block mt-2 w-full text-lg py-3"
                            type="password"
                            name="password"
                            required autocomplete="new-password" />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Confirm Password -->
        <div>
            <x-input-label for="password_confirmation" :value="__('Confirm Password')" class="text-lg font-medium text-gray-700" />
            <x-text-input id="password_confirmation" class="block mt-2 w-full text-lg py-3"
                            type="password"
                            name="password_confirmation" required autocomplete="new-password" />
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <div class="pt-2">
            <x-primary-button class="w-full justify-center py-3 text-lg font-bold">
                {{ __('Register') }}
            </x-primary-button>
        </div>
        
        <!-- Login Link -->
        <div class="text-center pt-4 border-t border-gray-200">
            <p class="text-gray-600">
                Already have an account? 
                <a href="{{ route('login') }}" class="text-primary-500 hover:text-primary-700 font-medium">
                    Sign in here
                </a>
            </p>
        </div>
    </form>
</x-guest-layout>
