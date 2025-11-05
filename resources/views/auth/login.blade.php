<x-guest-layout>
    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />
    
    <form method="POST" action="{{ route('login') }}">
        @csrf
        <div>
            <p><h3>CvSU - Tanza Grade Management System<h3></p>
            <br>
        </div>

        <!-- Email Address (Teachers/Admins Only) -->
        <div>
            <x-input-label for="login" :value="__('Email')" />
            <x-text-input id="login" class="block mt-1 w-full" type="email" name="login" :value="old('login')" required autofocus autocomplete="username" />
            <x-input-error :messages="$errors->get('login')" class="mt-2" />
            <p class="mt-1 text-sm text-gray-600">For Teachers and Admins only. Students: <a href="{{ route('student.login') }}" class="text-blue-600 underline">Login here</a></p>
        </div>

        <!-- Password -->
        <div class="mt-4">
            <x-input-label for="password" :value="__('Password')" />

            <x-text-input id="password" class="block mt-1 w-full"
                            type="password"
                            name="password"
                            required autocomplete="current-password" />

            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <div class="flex items-center justify-end mt-4">
            <x-primary-button class="ms-3">
                {{ __('Log in') }}
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>
