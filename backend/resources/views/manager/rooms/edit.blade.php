<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-2xl text-slate-900 dark:text-slate-100 leading-tight">
                {{ __('manager.rooms_edit') }}
            </h2>

            <a href="{{ route('manager.rooms.show', $room) }}"
               class="inline-flex items-center rounded-full px-3 py-1 text-xs font-medium
                      bg-slate-50 text-slate-700 border border-slate-200 hover:bg-slate-100
                      dark:bg-slate-900/40 dark:text-slate-200 dark:border-slate-700/60">
                {{ __('manager.back_to_profile') }}
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="rounded-2xl border border-slate-200 bg-white shadow-sm
                        dark:border-slate-800 dark:bg-slate-950/40 overflow-hidden">
                <div class="p-6">

                    @if ($errors->any())
                        <div class="mb-6 rounded-2xl border border-red-200 bg-red-50 px-4 py-3 text-red-700
                                    dark:border-red-900/60 dark:bg-red-950/30 dark:text-red-200">
                            <ul class="list-disc pl-5 space-y-1 text-sm">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('manager.rooms.update', $room) }}" class="space-y-5">
                        @csrf
                        @method('PUT')

                        <div>
                            <label for="name" class="block text-sm font-medium text-slate-700 dark:text-slate-200 mb-2">
                                {{ __('manager.rooms_name') }} <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="name" id="name"
                                   value="{{ old('name', $room->name) }}"
                                   class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-slate-900
                                          focus:outline-none focus:ring-4 focus:ring-blue-200 focus:border-blue-500
                                          dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100
                                          @error('name') border-red-400 @enderror">
                            @error('name')
                                <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                            <div>
                                <label for="age_band" class="block text-sm font-medium text-slate-700 dark:text-slate-200 mb-2">
                                    {{ __('manager.rooms_age_band') }}
                                </label>
                                <input type="text" name="age_band" id="age_band"
                                       value="{{ old('age_band', $room->age_band) }}"
                                       placeholder="e.g. 2-3 years"
                                       class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-slate-900
                                              focus:outline-none focus:ring-4 focus:ring-blue-200 focus:border-blue-500
                                              dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100
                                              @error('age_band') border-red-400 @enderror">
                                @error('age_band')
                                    <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="capacity" class="block text-sm font-medium text-slate-700 dark:text-slate-200 mb-2">
                                    {{ __('manager.rooms_capacity') }} <span class="text-red-500">*</span>
                                </label>
                                <input type="number" name="capacity" id="capacity" min="1"
                                       value="{{ old('capacity', $room->capacity) }}"
                                       class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-slate-900
                                              focus:outline-none focus:ring-4 focus:ring-blue-200 focus:border-blue-500
                                              dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100
                                              @error('capacity') border-red-400 @enderror">
                                @error('capacity')
                                    <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div>
                            <label for="description" class="block text-sm font-medium text-slate-700 dark:text-slate-200 mb-2">
                                {{ __('manager.rooms_description') }}
                            </label>
                            <textarea name="description" id="description" rows="3"
                                      class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-slate-900
                                             focus:outline-none focus:ring-4 focus:ring-blue-200 focus:border-blue-500
                                             dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100
                                             @error('description') border-red-400 @enderror">{{ old('description', $room->description) }}</textarea>
                            @error('description')
                                <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="flex items-center gap-3 pt-2">
                            <button type="submit"
                                    class="inline-flex items-center justify-center gap-2 rounded-xl px-5 py-2.5 font-semibold text-white
                                           bg-gradient-to-r from-blue-600 via-blue-700 to-indigo-700
                                           shadow-sm shadow-blue-500/20
                                           hover:shadow-md hover:shadow-blue-500/30 hover:brightness-110
                                           focus:outline-none focus:ring-4 focus:ring-blue-200
                                           active:translate-y-[1px]">
                                {{ __('manager.update') }}
                            </button>

                            <a href="{{ route('manager.rooms.show', $room) }}"
                               class="inline-flex items-center justify-center rounded-xl px-5 py-2.5 text-sm font-medium
                                      bg-slate-100 text-slate-700 border border-slate-200 hover:bg-slate-200
                                      dark:bg-slate-800 dark:text-slate-200 dark:border-slate-700 dark:hover:bg-slate-700
                                      focus:outline-none focus:ring-4 focus:ring-slate-200">
                                {{ __('manager.cancel') }}
                            </a>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>
