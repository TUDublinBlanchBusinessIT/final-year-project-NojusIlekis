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

    <div class="mb-6">
        <h1 class="text-2xl font-semibold tracking-tight text-white">
            Sign in to SnugBug
        </h1>
        <p class="mt-1 text-sm text-slate-600">
            Welcome back — please sign in to continue.
        </p>
    </div>

    <div class="rounded-2xl border border-slate-200 bg-white shadow-sm">
        <div class="p-6 sm:p-7">
            <x-auth-session-status class="mb-4" :status="session('status')" />

            <form method="POST" action="{{ route('login') }}" class="space-y-5">
                @csrf

                <div>
                    <x-input-label for="email" :value="__('Email')" />
                    <x-text-input
                        id="email"
                        class="mt-1 block w-full"
                        type="email"
                        name="email"
                        :value="old('email')"
                        required
                        autofocus
                        autocomplete="username"
                        placeholder="you@example.com"
                    />
                    <x-input-error :messages="$errors->get('email')" class="mt-2" />
                </div>

                <div>
                    <x-input-label for="password" :value="__('Password')" />
                    <x-text-input
                        id="password"
                        class="mt-1 block w-full"
                        type="password"
                        name="password"
                        required
                        autocomplete="current-password"
                        placeholder="••••••••"
                    />
                    <x-input-error :messages="$errors->get('password')" class="mt-2" />
                </div>

                <div class="flex items-center justify-between">
                    <label for="remember_me" class="inline-flex items-center">
                        <input
                            id="remember_me"
                            type="checkbox"
                            class="rounded border-slate-300 text-blue-600 shadow-sm focus:ring-blue-500"
                            name="remember"
                        >
                        <span class="ms-2 text-sm text-slate-600">
                            {{ __('Remember me') }}
                        </span>
                    </label>

                    @if (Route::has('password.request'))
                        <a class="text-sm font-medium text-blue-700 hover:text-blue-800"
                           href="{{ route('password.request') }}">
                            {{ __('Forgot password?') }}
                        </a>
                    @endif
                </div>

                <x-primary-button class="w-full justify-center">
                    {{ __('Sign in') }}
                </x-primary-button>
            </form>
        </div>

        <div class="border-t border-slate-200 px-6 py-4 text-center text-sm text-slate-600">
            Don’t have an account?
            @if (Route::has('register'))
                <a class="font-medium text-blue-700 hover:text-blue-800"
                   href="{{ route('register') }}">
                    Create one
                </a>
            @endif
        </div>
    </div>
</x-guest-layout>