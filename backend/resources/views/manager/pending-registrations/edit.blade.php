<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-semibold text-2xl text-slate-900 dark:text-slate-100 leading-tight">
                    {{ __('manager.edit_pending_registration') }}
                </h2>
                <p class="text-sm text-slate-600 dark:text-slate-300 mt-1">{{ $user->name }}</p>
            </div>
            <a href="{{ route('manager.pending-registrations.show', $user) }}"
               class="inline-flex items-center justify-center rounded-xl px-3 py-1.5 text-sm font-semibold
                      bg-slate-100 text-slate-700 border border-slate-200 hover:bg-slate-200">
                ← {{ __('manager.back') }}
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <form method="POST" action="{{ route('manager.pending-registrations.update', $user) }}"
                  class="rounded-2xl border border-slate-200 bg-white shadow-sm p-6 space-y-6
                         dark:border-slate-800 dark:bg-slate-950/40">
                @csrf
                @method('PUT')

                <div>
                    <h3 class="text-base font-semibold text-slate-900 dark:text-slate-100 mb-3">
                        {{ __('manager.applicant_details') }}
                    </h3>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <x-input-label for="name" :value="__('manager.name')" />
                            <x-text-input id="name" name="name" type="text" class="mt-1 block w-full"
                                          :value="old('name', $user->name)" required />
                            <x-input-error :messages="$errors->get('name')" class="mt-2" />
                        </div>
                        <div>
                            <x-input-label for="email" :value="__('manager.email')" />
                            <x-text-input id="email" name="email" type="email" class="mt-1 block w-full"
                                          :value="old('email', $user->email)" required />
                            <x-input-error :messages="$errors->get('email')" class="mt-2" />
                        </div>
                        <div>
                            <x-input-label for="phone" :value="__('auth.register_phone')" />
                            <x-text-input id="phone" name="phone" type="tel" class="mt-1 block w-full"
                                          :value="old('phone', $user->phone)" required />
                            <x-input-error :messages="$errors->get('phone')" class="mt-2" />
                        </div>
                        <div>
                            <x-input-label for="address" :value="__('auth.register_address')" />
                            <textarea id="address" name="address" rows="2" required
                                      class="mt-1 block w-full rounded-md border-gray-300 shadow-sm text-gray-900">{{ old('address', $user->address) }}</textarea>
                            <x-input-error :messages="$errors->get('address')" class="mt-2" />
                        </div>
                    </div>

                    <div class="mt-4">
                        <x-input-label for="registration_notes" :value="$user->role === 'carer' ? __('auth.register_carer_experience') : __('auth.register_additional_notes')" />
                        <textarea id="registration_notes" name="registration_notes" rows="3"
                                  class="mt-1 block w-full rounded-md border-gray-300 shadow-sm text-gray-900">{{ old('registration_notes', $user->registration_notes) }}</textarea>
                        <x-input-error :messages="$errors->get('registration_notes')" class="mt-2" />
                    </div>
                </div>

                @if ($user->role === 'parent')
                    <div class="border-t border-slate-200 pt-6">
                        <h3 class="text-base font-semibold text-slate-900 dark:text-slate-100 mb-3">
                            {{ __('auth.register_child_details') }}
                        </h3>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <x-input-label for="child_first_name" :value="__('manager.first_name')" />
                                <x-text-input id="child_first_name" name="child_first_name" type="text"
                                              class="mt-1 block w-full"
                                              :value="old('child_first_name', $child?->first_name)" required />
                                <x-input-error :messages="$errors->get('child_first_name')" class="mt-2" />
                            </div>
                            <div>
                                <x-input-label for="child_last_name" :value="__('manager.last_name')" />
                                <x-text-input id="child_last_name" name="child_last_name" type="text"
                                              class="mt-1 block w-full"
                                              :value="old('child_last_name', $child?->last_name)" required />
                                <x-input-error :messages="$errors->get('child_last_name')" class="mt-2" />
                            </div>
                            <div>
                                <x-input-label for="child_dob" :value="__('manager.date_of_birth')" />
                                <x-text-input id="child_dob" name="child_dob" type="date"
                                              class="mt-1 block w-full"
                                              :value="old('child_dob', optional($child?->dob)->format('Y-m-d'))" required />
                                <x-input-error :messages="$errors->get('child_dob')" class="mt-2" />
                            </div>
                            <div>
                                <x-input-label for="child_room_id" :value="__('manager.room')" />
                                <select id="child_room_id" name="child_room_id"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm text-gray-900">
                                    <option value="">{{ __('manager.unassigned_option') }}</option>
                                    @foreach ($rooms as $room)
                                        <option value="{{ $room->id }}"
                                            @selected(old('child_room_id', $child?->room_id) == $room->id)>
                                            {{ $room->name }}
                                        </option>
                                    @endforeach
                                </select>
                                <x-input-error :messages="$errors->get('child_room_id')" class="mt-2" />
                            </div>
                            <div class="sm:col-span-2">
                                <x-input-label for="child_allergies" :value="__('manager.allergies')" />
                                <x-text-input id="child_allergies" name="child_allergies" type="text"
                                              class="mt-1 block w-full"
                                              :value="old('child_allergies', $child?->allergies)" />
                                <x-input-error :messages="$errors->get('child_allergies')" class="mt-2" />
                            </div>
                            <div class="sm:col-span-2">
                                <x-input-label for="child_medical_notes" :value="__('manager.medical_notes')" />
                                <textarea id="child_medical_notes" name="child_medical_notes" rows="2"
                                          class="mt-1 block w-full rounded-md border-gray-300 shadow-sm text-gray-900">{{ old('child_medical_notes', $child?->medical_notes) }}</textarea>
                                <x-input-error :messages="$errors->get('child_medical_notes')" class="mt-2" />
                            </div>
                        </div>
                    </div>
                @endif

                @if ($user->role === 'carer')
                    <div class="border-t border-slate-200 pt-6">
                        <h3 class="text-base font-semibold text-slate-900 dark:text-slate-100 mb-3">
                            {{ __('manager.room_assignment_optional') }}
                        </h3>
                        <p class="text-xs text-slate-500 mb-2">{{ __('manager.room_assignment_hint') }}</p>
                        <select name="room_id"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm text-gray-900">
                            <option value="">{{ __('manager.unassigned_option') }}</option>
                            @foreach ($rooms as $room)
                                <option value="{{ $room->id }}" @selected(old('room_id') == $room->id)>
                                    {{ $room->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                @endif

                <div class="flex items-center gap-3 border-t border-slate-200 pt-6">
                    <button type="submit"
                            class="inline-flex items-center justify-center rounded-xl px-4 py-2 font-semibold text-white
                                   bg-gradient-to-r from-blue-600 via-blue-700 to-indigo-700 hover:brightness-110">
                        {{ __('manager.save') }}
                    </button>
                    <a href="{{ route('manager.pending-registrations.show', $user) }}"
                       class="inline-flex items-center justify-center rounded-xl px-4 py-2 font-semibold
                              bg-slate-100 text-slate-700 border border-slate-200 hover:bg-slate-200">
                        {{ __('manager.cancel') }}
                    </a>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
