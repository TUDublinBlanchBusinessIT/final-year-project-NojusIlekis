<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-semibold text-2xl text-slate-900 dark:text-slate-100 leading-tight">
                    {{ __('carer.daily_updates') }}
                </h2>
                <p class="text-sm text-slate-600 dark:text-slate-300 mt-1">
                    {{ __('carer.daily_updates_instruction') }}
                </p>
            </div>

            <a href="{{ route('carer.dashboard') }}"
               class="inline-flex items-center gap-2 rounded-xl border border-slate-200 bg-white px-4 py-2 text-sm font-medium
                      text-slate-700 shadow-sm hover:bg-slate-50
                      dark:bg-slate-900/40 dark:text-slate-200 dark:border-slate-700">
                ← {{ __('carer.back_to_dashboard') }}
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8 space-y-6">

            @if(session('success'))
                <div class="rounded-2xl border border-emerald-200 bg-emerald-50/30 p-4 text-emerald-800
                            dark:border-emerald-900/60 dark:bg-emerald-950/10 dark:text-emerald-100">
                    {{ session('success') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="rounded-2xl border border-red-200 bg-red-50/30 p-4 text-red-800
                            dark:border-red-900/60 dark:bg-red-950/10 dark:text-red-100">
                    <ul class="list-disc pl-5 space-y-1">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            {{-- Filters (GET) --}}
            <div class="rounded-2xl border border-slate-200 bg-white shadow-sm
                        dark:border-slate-800 dark:bg-slate-950/40 overflow-hidden">
                <div class="px-5 py-4 border-b border-slate-200 dark:border-slate-800">
                    <h3 class="text-base font-semibold text-slate-900 dark:text-slate-100">{{ __('carer.filters') }}</h3>
                    <p class="text-sm text-slate-600 dark:text-slate-300 mt-1">{{ __('carer.load_children_updates') }}</p>
                </div>

                <div class="p-5">
                    <form method="GET" action="{{ route('carer.daily-updates.index') }}"
                          class="grid grid-cols-1 sm:grid-cols-3 gap-4 items-end">
                        <div>
                            <label class="block text-sm font-medium text-slate-700 dark:text-slate-200 mb-1">{{ __('carer.date') }}</label>
                            <input type="date" name="date" value="{{ $date }}"
                                   class="w-full rounded-xl border border-slate-300 bg-white px-3 py-2 text-slate-900
                                          focus:outline-none focus:ring-4 focus:ring-blue-200 focus:border-blue-500
                                          dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100
                                          dark:focus:ring-blue-900/40 dark:focus:border-blue-400">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-slate-700 dark:text-slate-200 mb-1">{{ __('carer.room') }}</label>
                            <select name="room_id"
                                    class="w-full rounded-xl border border-slate-300 bg-white px-3 py-2 text-slate-900
                                           focus:outline-none focus:ring-4 focus:ring-blue-200 focus:border-blue-500
                                           dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100
                                           dark:focus:ring-blue-900/40 dark:focus:border-blue-400">
                                <option value="">{{ __('carer.select_room') }}</option>
                                @foreach($rooms as $room)
                                    <option value="{{ $room->id }}" @selected((string)$roomId === (string)$room->id)>
                                        {{ $room->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <button type="submit"
                                class="group inline-flex w-full items-center justify-center gap-2 rounded-xl px-4 py-2 font-semibold text-white
                                       bg-gradient-to-r from-blue-600 via-blue-700 to-indigo-700 shadow-sm shadow-blue-500/20
                                       hover:shadow-md hover:shadow-blue-500/30 hover:brightness-110
                                       focus:outline-none focus:ring-4 focus:ring-blue-200 dark:focus:ring-blue-900/40">
                            {{ __('carer.load') }}
                        </button>
                    </form>
                </div>
            </div>

            {{-- Updates table (POST) --}}
            @if($roomId)
                <div class="rounded-2xl border border-slate-200 bg-white shadow-sm
                            dark:border-slate-800 dark:bg-slate-950/40 overflow-hidden">
                    <div class="px-5 py-4 border-b border-slate-200 dark:border-slate-800 flex items-center justify-between">
                        <div>
                            <h3 class="text-base font-semibold text-slate-900 dark:text-slate-100">{{ __('carer.daily_updates') }}</h3>
                            <p class="text-sm text-slate-600 dark:text-slate-300 mt-1">
                                {{ __('carer.fill_fields') }} <span class="font-semibold">{{ __('carer.done') }}</span>.
                            </p>
                        </div>

                        <form method="POST" action="{{ route('carer.daily-updates.store') }}">
                            @csrf
                            <input type="hidden" name="date" value="{{ $date }}">
                            <input type="hidden" name="room_id" value="{{ $roomId }}">

                            <button type="submit"
                                    class="group inline-flex items-center justify-center gap-2 rounded-xl px-4 py-2 font-semibold text-white
                                           bg-gradient-to-r from-emerald-600 via-emerald-700 to-teal-700 shadow-sm shadow-emerald-500/20
                                           hover:shadow-md hover:shadow-emerald-500/30 hover:brightness-110
                                           focus:outline-none focus:ring-4 focus:ring-emerald-200 dark:focus:ring-emerald-900/40">
                                {{ __('carer.save_updates') }}
                            </button>
                        </form>
                    </div>

                    <form method="POST" action="{{ route('carer.daily-updates.store') }}">
                        @csrf
                        <input type="hidden" name="date" value="{{ $date }}">
                        <input type="hidden" name="room_id" value="{{ $roomId }}">

                        <div class="overflow-x-auto">
                            <table class="min-w-full">
                                <thead>
                                    <tr class="bg-slate-50 dark:bg-slate-900/60">
                                        <th class="text-left px-5 py-3 text-xs font-semibold uppercase tracking-wider text-slate-600 dark:text-slate-300">{{ __('carer.child') }}</th>
                                        <th class="text-left px-5 py-3 text-xs font-semibold uppercase tracking-wider text-slate-600 dark:text-slate-300">{{ __('carer.meals') }}</th>
                                        <th class="text-left px-5 py-3 text-xs font-semibold uppercase tracking-wider text-slate-600 dark:text-slate-300">{{ __('carer.sleep') }}</th>
                                        <th class="text-left px-5 py-3 text-xs font-semibold uppercase tracking-wider text-slate-600 dark:text-slate-300">{{ __('carer.notes') }}</th>
                                    </tr>
                                </thead>

                                <tbody class="divide-y divide-slate-200 dark:divide-slate-800">
                                    @forelse($children as $child)
                                        @php $u = $existing->get($child->id); @endphp

                                        <tr class="hover:bg-slate-50 dark:hover:bg-slate-900/30">
                                            <td class="px-5 py-4">
                                                <div class="flex items-center justify-between gap-4">
                                                    <div>
                                                        <div class="font-semibold text-slate-900 dark:text-slate-100">
                                                            {{ $child->first_name }} {{ $child->last_name }}
                                                        </div>
                                                        <div class="text-xs text-slate-500 dark:text-slate-400">
                                                            {{ __('carer.room') }}: {{ $child->room?->name ?? '—' }} • ID: {{ $child->id }}
                                                        </div>
                                                        @if($child->hasAllergies())
                                                            <span class="mt-1 inline-flex items-center bg-red-100 text-red-700 text-xs font-semibold px-2 py-0.5 rounded-full"
                                                                  title="{{ __('carer.allergies') }}: {{ $child->allergyList() }}">
                                                                ⚠️ {{ $child->allergyList() }}
                                                            </span>
                                                        @endif
                                                    </div>

                                                    @if($u)
                                                        <span class="inline-flex items-center rounded-full px-2 py-0.5 text-xs font-medium
                                                                     bg-emerald-50 text-emerald-700 border border-emerald-200
                                                                     dark:bg-emerald-950/40 dark:text-emerald-200 dark:border-emerald-900/60">
                                                            {{ __('carer.done') }}
                                                        </span>
                                                    @else
                                                        <span class="inline-flex items-center rounded-full px-2 py-0.5 text-xs font-medium
                                                                     bg-red-50 text-red-700 border border-red-200
                                                                     dark:bg-red-950/40 dark:text-red-200 dark:border-red-900/60">
                                                            {{ __('carer.missing') }}
                                                        </span>
                                                    @endif
                                                </div>
                                            </td>

                                            <td class="px-5 py-4">
                                                <input type="text" name="meals[{{ $child->id }}]" value="{{ old('meals.'.$child->id, $u?->meals) }}"
                                                       class="w-full rounded-xl border border-slate-300 bg-white px-3 py-2 text-slate-900
                                                              focus:outline-none focus:ring-4 focus:ring-blue-200 focus:border-blue-500
                                                              dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100
                                                              dark:focus:ring-blue-900/40 dark:focus:border-blue-400">
                                                @if($child->hasAllergies())
                                                    <p class="mt-1 text-xs text-red-600 font-medium">
                                                        ⚠️ {{ __('carer.allergies') }}: {{ $child->allergyList() }}
                                                    </p>
                                                @endif
                                            </td>

                                            <td class="px-5 py-4">
                                                <input type="text" name="sleep[{{ $child->id }}]" value="{{ old('sleep.'.$child->id, $u?->sleep) }}"
                                                       class="w-full rounded-xl border border-slate-300 bg-white px-3 py-2 text-slate-900
                                                              focus:outline-none focus:ring-4 focus:ring-blue-200 focus:border-blue-500
                                                              dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100
                                                              dark:focus:ring-blue-900/40 dark:focus:border-blue-400">
                                            </td>

                                            <td class="px-5 py-4">
                                                <textarea name="notes[{{ $child->id }}]" rows="2"
                                                          class="w-full rounded-xl border border-slate-300 bg-white px-3 py-2 text-slate-900
                                                                 focus:outline-none focus:ring-4 focus:ring-blue-200 focus:border-blue-500
                                                                 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100
                                                                 dark:focus:ring-blue-900/40 dark:focus:border-blue-400">{{ old('notes.'.$child->id, $u?->notes) }}</textarea>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" class="px-5 py-6 text-slate-600 dark:text-slate-300">
                                                {{ __('carer.no_children_room') }}
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                    </form>
                </div>
            @endif

        </div>
    </div>
</x-app-layout>