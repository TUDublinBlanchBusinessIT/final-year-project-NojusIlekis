<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-900 dark:text-white leading-tight">
            {{ __('profile.profile') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            {{-- Profile Info --}}
            <div class="p-4 sm:p-8 bg-white dark:bg-slate-950/40 shadow sm:rounded-lg">
                <div class="max-w-xl text-gray-900 dark:text-white">
                    @include('profile.partials.update-profile-information-form')
                </div>
            </div>

            {{-- Update Password --}}
            <div class="p-4 sm:p-8 bg-white dark:bg-slate-950/40 shadow sm:rounded-lg">
                <div class="max-w-xl text-gray-900 dark:text-white">
                    @include('profile.partials.update-password-form')
                </div>
            </div>

            {{-- Delete Account --}}
            <div class="p-4 sm:p-8 bg-white dark:bg-slate-950/40 shadow sm:rounded-lg">
                <div class="max-w-xl text-gray-900 dark:text-white">
                    @include('profile.partials.delete-user-form')
                </div>
            </div>

        </div>
    </div>
</x-app-layout>