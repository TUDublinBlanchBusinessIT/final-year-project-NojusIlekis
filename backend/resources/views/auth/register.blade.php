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

    <div x-data="{ role: '{{ old('role', '') }}' }">
        {{-- Step 1: Choose role --}}
        <div x-show="role === ''" x-transition>
            <h2 class="text-white text-xl font-semibold mb-4">{{ __('auth.register_choose_role') }}</h2>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <button type="button"
                        @click="role = 'parent'"
                        class="rounded-2xl border border-slate-300 bg-white p-6 text-left shadow-sm hover:shadow-md hover:border-blue-400 transition">
                    <div class="text-3xl mb-2">👨‍👩‍👧</div>
                    <p class="text-lg font-semibold text-slate-900">{{ __('auth.register_as_parent') }}</p>
                    <p class="text-sm text-slate-600 mt-1">{{ __('auth.register_as_parent_desc') }}</p>
                </button>

                <button type="button"
                        @click="role = 'carer'"
                        class="rounded-2xl border border-slate-300 bg-white p-6 text-left shadow-sm hover:shadow-md hover:border-blue-400 transition">
                    <div class="text-3xl mb-2">🧑‍🏫</div>
                    <p class="text-lg font-semibold text-slate-900">{{ __('auth.register_as_carer') }}</p>
                    <p class="text-sm text-slate-600 mt-1">{{ __('auth.register_as_carer_desc') }}</p>
                </button>
            </div>

            <div class="mt-6 text-center">
                <a class="underline text-sm text-gray-300 hover:text-white" href="{{ route('login') }}">
                    {{ __('auth.already_registered') }}
                </a>
            </div>
        </div>

        {{-- Step 2: Role-specific form --}}
        <div x-show="role !== ''" x-transition>
            <form method="POST" action="{{ route('register') }}">
                @csrf
                <input type="hidden" name="role" :value="role">

                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-white text-xl font-semibold">
                        <span x-text="role === 'parent' ? @js(__('auth.register_as_parent')) : @js(__('auth.register_as_carer'))"></span>
                    </h2>
                    <button type="button" @click="role = ''" class="text-sm text-gray-300 underline hover:text-white">
                        ← {{ __('auth.register_change_role') }}
                    </button>
                </div>

                <fieldset class="rounded-2xl border border-white/20 p-4 mb-4">
                    <legend class="px-2 text-white font-semibold text-sm">{{ __('auth.register_your_details') }}</legend>

                    <div>
                        <x-input-label class="text-white" for="name" :value="__('auth.name')" />
                        <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name')" required autofocus autocomplete="name" />
                        <x-input-error :messages="$errors->get('name')" class="mt-2" />
                    </div>

                    <div class="mt-4">
                        <x-input-label class="text-white" for="email" :value="__('auth.email')" />
                        <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autocomplete="username" />
                        <x-input-error :messages="$errors->get('email')" class="mt-2" />
                    </div>

                    <div class="mt-4">
                        <x-input-label class="text-white" for="phone" :value="__('auth.register_phone')" />
                        <x-text-input id="phone" class="block mt-1 w-full" type="tel" name="phone" :value="old('phone')" required autocomplete="tel" />
                        <x-input-error :messages="$errors->get('phone')" class="mt-2" />
                    </div>

                    <div class="mt-4">
                        <x-input-label class="text-white" for="address" :value="__('auth.register_address')" />
                        <textarea id="address"
                                  name="address"
                                  rows="2"
                                  required
                                  class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-gray-900">{{ old('address') }}</textarea>
                        <x-input-error :messages="$errors->get('address')" class="mt-2" />
                    </div>

                    <div class="mt-4">
                        <x-input-label class="text-white" for="password" :value="__('auth.password_label')" />
                        <x-text-input id="password" class="block mt-1 w-full" type="password" name="password" required autocomplete="new-password" />
                        <x-input-error :messages="$errors->get('password')" class="mt-2" />
                    </div>

                    <div class="mt-4">
                        <x-input-label class="text-white" for="password_confirmation" :value="__('auth.confirm_password')" />
                        <x-text-input id="password_confirmation" class="block mt-1 w-full" type="password" name="password_confirmation" required autocomplete="new-password" />
                        <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
                    </div>
                </fieldset>

                {{-- Parent-only child details --}}
                <fieldset x-show="role === 'parent'" x-transition class="rounded-2xl border border-white/20 p-4 mb-4">
                    <legend class="px-2 text-white font-semibold text-sm">{{ __('auth.register_child_details') }}</legend>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <x-input-label class="text-white" for="child_first_name" :value="__('auth.register_child_first_name')" />
                            <x-text-input id="child_first_name" class="block mt-1 w-full" type="text" name="child_first_name" :value="old('child_first_name')" x-bind:required="role === 'parent'" />
                            <x-input-error :messages="$errors->get('child_first_name')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label class="text-white" for="child_last_name" :value="__('auth.register_child_last_name')" />
                            <x-text-input id="child_last_name" class="block mt-1 w-full" type="text" name="child_last_name" :value="old('child_last_name')" x-bind:required="role === 'parent'" />
                            <x-input-error :messages="$errors->get('child_last_name')" class="mt-2" />
                        </div>
                    </div>

                    <div class="mt-4">
                        <x-input-label class="text-white" for="child_dob" :value="__('auth.register_child_dob')" />
                        <x-text-input id="child_dob" class="block mt-1 w-full" type="date" name="child_dob" :value="old('child_dob')" x-bind:required="role === 'parent'" />
                        <x-input-error :messages="$errors->get('child_dob')" class="mt-2" />
                    </div>

                    <div class="mt-4">
                        <x-input-label class="text-white" for="child_allergies" :value="__('auth.register_child_allergies')" />
                        <x-text-input id="child_allergies" class="block mt-1 w-full" type="text" name="child_allergies" :value="old('child_allergies')" placeholder="{{ __('auth.register_child_allergies_placeholder') }}" />
                        <x-input-error :messages="$errors->get('child_allergies')" class="mt-2" />
                    </div>

                    <div class="mt-4">
                        <x-input-label class="text-white" for="child_medical_notes" :value="__('auth.register_child_medical_notes')" />
                        <textarea id="child_medical_notes"
                                  name="child_medical_notes"
                                  rows="2"
                                  placeholder="{{ __('auth.register_child_medical_notes_placeholder') }}"
                                  class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-gray-900">{{ old('child_medical_notes') }}</textarea>
                        <x-input-error :messages="$errors->get('child_medical_notes')" class="mt-2" />
                    </div>

                    <div class="mt-4">
                        <x-input-label class="text-white" for="registration_notes_parent" :value="__('auth.register_additional_notes')" />
                        <textarea id="registration_notes_parent"
                                  name="registration_notes"
                                  rows="2"
                                  x-show="role === 'parent'"
                                  class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-gray-900">{{ old('registration_notes') }}</textarea>
                        <x-input-error :messages="$errors->get('registration_notes')" class="mt-2" />
                    </div>
                </fieldset>

                {{-- Carer-only experience --}}
                <fieldset x-show="role === 'carer'" x-transition class="rounded-2xl border border-white/20 p-4 mb-4">
                    <legend class="px-2 text-white font-semibold text-sm">{{ __('auth.register_carer_experience') }}</legend>

                    <div>
                        <x-input-label class="text-white" for="registration_notes_carer" :value="__('auth.register_carer_experience_label')" />
                        <textarea id="registration_notes_carer"
                                  name="registration_notes"
                                  rows="5"
                                  x-bind:required="role === 'carer'"
                                  placeholder="{{ __('auth.register_carer_experience_placeholder') }}"
                                  class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-gray-900">{{ old('registration_notes') }}</textarea>
                        <x-input-error :messages="$errors->get('registration_notes')" class="mt-2" />
                        <p class="mt-1 text-xs text-gray-300">{{ __('auth.register_carer_experience_hint') }}</p>
                    </div>
                </fieldset>

                <div class="flex items-center justify-end mt-4">
                    <a class="underline text-sm text-gray-300 hover:text-white rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                       href="{{ route('login') }}">
                        {{ __('auth.already_registered') }}
                    </a>

                    <x-primary-button class="ms-4">
                        {{ __('auth.register') }}
                    </x-primary-button>
                </div>
            </form>
        </div>
    </div>
</x-guest-layout>
