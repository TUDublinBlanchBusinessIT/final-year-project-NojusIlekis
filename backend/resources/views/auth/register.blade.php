<x-guest-layout>
    <div class="mb-4 flex justify-end">
        <form method="POST" action="{{ route('locale.switch') }}">
            @csrf

            <select
                name="locale"
                onchange="this.form.submit()"
                class="rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm text-slate-700 shadow-sm focus:border-blue-500 focus:ring-blue-500"
            >
                <option value="en" {{ app()->getLocale() === 'en' ? 'selected' : '' }}>English</option>
                <option value="pt" {{ app()->getLocale() === 'pt' ? 'selected' : '' }}>Português</option>
                <option value="pl" {{ app()->getLocale() === 'pl' ? 'selected' : '' }}>Polski</option>
                <option value="ro" {{ app()->getLocale() === 'ro' ? 'selected' : '' }}>Română</option>
            </select>
        </form>
    </div>

    <form method="POST" action="{{ route('register') }}">
        @csrf

        <!-- Name -->
        <div>
            <x-input-label class="text-white" for="name" :value="__('auth.name')" />
            <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name')" required autofocus autocomplete="name" />
            <x-input-error :messages="$errors->get('name')" class="mt-2" />
        </div>

        <!-- Email Address -->
        <div class="mt-4">
            <x-input-label class="text-white" for="email" :value="__('auth.email')" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Password -->
        <div class="mt-4">
            <x-input-label class="text-white" for="password" :value="__('auth.password_label')" />
            <x-text-input
                id="password"
                class="block mt-1 w-full"
                type="password"
                name="password"
                required
                autocomplete="new-password"
            />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Confirm Password -->
        <div class="mt-4">
            <x-input-label class="text-white" for="password_confirmation" :value="__('auth.confirm_password')" />
            <x-text-input
                id="password_confirmation"
                class="block mt-1 w-full"
                type="password"
                name="password_confirmation"
                required
                autocomplete="new-password"
            />
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <!-- Role -->
        <div class="mt-4">
            <x-input-label class="text-white" for="role" :value="__('auth.role')" />

            <select
                id="role"
                name="role"
                class="block mt-1 w-full rounded-md border-gray-300 bg-white text-gray-900 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                style="color:#111827;"
                required
            >
                <option value="parent">{{ __('auth.parent') }}</option>
                <option value="carer">{{ __('auth.carer') }}</option>
                <option value="manager">{{ __('auth.manager') }}</option>
            </select>

            <x-input-error :messages="$errors->get('role')" class="mt-2" />
        </div>

        <div class="flex items-center justify-end mt-4">
            <a
                class="underline text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800"
                href="{{ route('login') }}"
            >
                {{ __('auth.already_registered') }}
            </a>

            <x-primary-button class="ms-4">
                {{ __('auth.register') }}
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>