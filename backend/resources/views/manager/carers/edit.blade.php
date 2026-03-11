<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-2xl text-slate-900 dark:text-slate-100 leading-tight">
                Edit Carer
            </h2>

            <a href="{{ route('manager.carers.show', $carerUser) }}"
               class="inline-flex items-center rounded-full px-3 py-1 text-xs font-medium
                      bg-slate-50 text-slate-700 border border-slate-200 hover:bg-slate-100
                      dark:bg-slate-900/40 dark:text-slate-200 dark:border-slate-700/60">
                Back to Profile
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

                    <form method="POST" action="{{ route('manager.carers.update', $carerUser) }}" class="space-y-5">
                        @csrf
                        @method('PUT')

                        <div>
                            <label for="name" class="block text-sm font-medium text-slate-700 dark:text-slate-200 mb-2">
                                Name <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="name" id="name"
                                   value="{{ old('name', $carerUser->name) }}"
                                   class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-slate-900
                                          focus:outline-none focus:ring-4 focus:ring-blue-200 focus:border-blue-500
                                          dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100
                                          @error('name') border-red-400 @enderror">
                            @error('name')
                                <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="email" class="block text-sm font-medium text-slate-700 dark:text-slate-200 mb-2">
                                Email <span class="text-red-500">*</span>
                            </label>
                            <input type="email" name="email" id="email"
                                   value="{{ old('email', $carerUser->email) }}"
                                   class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-slate-900
                                          focus:outline-none focus:ring-4 focus:ring-blue-200 focus:border-blue-500
                                          dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100
                                          @error('email') border-red-400 @enderror">
                            @error('email')
                                <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                            <div>
                                <label for="password" class="block text-sm font-medium text-slate-700 dark:text-slate-200 mb-2">
                                    New Password
                                </label>
                                <input type="password" name="password" id="password"
                                       class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-slate-900
                                              focus:outline-none focus:ring-4 focus:ring-blue-200 focus:border-blue-500
                                              dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100
                                              @error('password') border-red-400 @enderror">
                                <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">Leave blank to keep current password.</p>
                                @error('password')
                                    <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="password_confirmation" class="block text-sm font-medium text-slate-700 dark:text-slate-200 mb-2">
                                    Confirm New Password
                                </label>
                                <input type="password" name="password_confirmation" id="password_confirmation"
                                       class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-slate-900
                                              focus:outline-none focus:ring-4 focus:ring-blue-200 focus:border-blue-500
                                              dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100">
                            </div>
                        </div>

                        <div>
                            <label for="room_id" class="block text-sm font-medium text-slate-700 dark:text-slate-200 mb-2">
                                Room
                            </label>
                            <select name="room_id" id="room_id"
                                    class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-slate-900
                                           focus:outline-none focus:ring-4 focus:ring-blue-200 focus:border-blue-500
                                           dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100
                                           @error('room_id') border-red-400 @enderror">
                                <option value="">No Room</option>
                                @foreach ($rooms as $room)
                                    <option value="{{ $room->id }}"
                                        @selected(old('room_id', $carerUser->activeRooms->first()?->id) == $room->id)>
                                        {{ $room->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('room_id')
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
                                Update
                            </button>

                            <a href="{{ route('manager.carers.show', $carerUser) }}"
                               class="inline-flex items-center justify-center rounded-xl px-5 py-2.5 text-sm font-medium
                                      bg-slate-100 text-slate-700 border border-slate-200 hover:bg-slate-200
                                      dark:bg-slate-800 dark:text-slate-200 dark:border-slate-700 dark:hover:bg-slate-700
                                      focus:outline-none focus:ring-4 focus:ring-slate-200">
                                Cancel
                            </a>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>
