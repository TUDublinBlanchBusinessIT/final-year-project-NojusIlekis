<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-semibold text-2xl text-slate-900 dark:text-slate-100 leading-tight">
                    {{ __('manager.tasks_summary') }}
                </h2>
                <p class="text-sm text-slate-600 dark:text-slate-300 mt-1">
                    {{ __('manager.tasks_report_desc') }}
                </p>
            </div>
            <span class="inline-flex items-center gap-2 rounded-full px-3 py-1 text-xs font-medium
                         bg-indigo-50 text-indigo-700 border border-indigo-200
                         dark:bg-indigo-950/40 dark:text-indigo-200 dark:border-indigo-900/60">
                {{ __('manager.manager_view') }}
            </span>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8">

            {{-- Filters --}}
            <div class="rounded-2xl border border-slate-200 bg-white shadow-sm
                        dark:border-slate-800 dark:bg-slate-950/40 overflow-hidden">
                <div class="px-5 py-4 border-b border-slate-200 dark:border-slate-800">
                    <h3 class="text-base font-semibold text-slate-900 dark:text-slate-100">{{ __('manager.filters') }}</h3>
                    <p class="text-sm text-slate-600 dark:text-slate-300 mt-1">{{ __('manager.choose_date_room') }}</p>
                </div>

                <div class="p-5">
                    <form method="GET" action="{{ route('manager.reports.tasks') }}"
                          class="grid grid-cols-1 sm:grid-cols-3 gap-4 items-end">
                        <div>
                            <label class="block text-sm font-medium text-slate-700 dark:text-slate-200 mb-1">{{ __('manager.date') }}</label>
                            <input type="date" name="date" value="{{ $date }}"
                                   class="w-full rounded-xl border border-slate-300 bg-white px-3 py-2 text-slate-900
                                          focus:outline-none focus:ring-4 focus:ring-blue-200 focus:border-blue-500
                                          dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100
                                          dark:focus:ring-blue-900/40 dark:focus:border-blue-400">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-slate-700 dark:text-slate-200 mb-1">{{ __('manager.room') }}</label>
                            <select name="room_id"
                                    class="w-full rounded-xl border border-slate-300 bg-white px-3 py-2 text-slate-900
                                           focus:outline-none focus:ring-4 focus:ring-blue-200 focus:border-blue-500
                                           dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100
                                           dark:focus:ring-blue-900/40 dark:focus:border-blue-400">
                                <option value="">{{ __('manager.all_rooms') }}</option>
                                @foreach ($rooms as $room)
                                    <option value="{{ $room->id }}" @selected((string)$roomId === (string)$room->id)>
                                        {{ $room->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <button type="submit"
                                class="group inline-flex w-full items-center justify-center gap-2 rounded-xl px-4 py-2 font-semibold text-white
                                       bg-gradient-to-r from-blue-600 via-blue-700 to-indigo-700
                                       shadow-sm shadow-blue-500/20
                                       hover:shadow-md hover:shadow-blue-500/30 hover:brightness-110
                                       focus:outline-none focus:ring-4 focus:ring-blue-200
                                       active:translate-y-[1px]
                                       dark:shadow-blue-900/30 dark:focus:ring-blue-900/40">
                            <span>{{ __('manager.load') }}</span>
                        </button>
                    </form>
                </div>
            </div>

            {{-- Totals --}}
            <div class="mt-6 grid grid-cols-1 sm:grid-cols-5 gap-4">
                <div class="rounded-2xl border border-slate-200 bg-white p-5 dark:border-slate-800 dark:bg-slate-950/40">
                    <div class="text-xs text-slate-500 dark:text-slate-400">{{ __('manager.children') }}</div>
                    <div class="text-2xl font-bold text-slate-900 dark:text-slate-100">{{ $childrenCount }}</div>
                </div>

                <div class="rounded-2xl border border-emerald-200 bg-emerald-50/30 p-5 dark:border-emerald-900/60 dark:bg-emerald-950/10">
                    <div class="text-xs text-emerald-700 dark:text-emerald-200">{{ __('manager.updates_done') }}</div>
                    <div class="text-2xl font-bold text-emerald-800 dark:text-emerald-100">{{ $updatesDone }}</div>
                </div>

                <div class="rounded-2xl border border-red-200 bg-red-50/30 p-5 dark:border-red-900/60 dark:bg-red-950/10">
                    <div class="text-xs text-red-700 dark:text-red-200">{{ __('manager.missing') }}</div>
                    <div class="text-2xl font-bold text-red-800 dark:text-red-100">{{ $updatesMissing }}</div>
                </div>

                <div class="rounded-2xl border border-slate-200 bg-slate-50 p-5 dark:border-slate-800 dark:bg-slate-900/30">
                    <div class="text-xs text-slate-600 dark:text-slate-300">{{ __('manager.meals_filled') }}</div>
                    <div class="text-2xl font-bold text-slate-900 dark:text-slate-100">{{ $mealsDone }}</div>
                </div>

                <div class="rounded-2xl border border-slate-200 bg-slate-50 p-5 dark:border-slate-800 dark:bg-slate-900/30">
                    <div class="text-xs text-slate-600 dark:text-slate-300">{{ __('manager.sleep_filled') }}</div>
                    <div class="text-2xl font-bold text-slate-900 dark:text-slate-100">{{ $sleepDone }}</div>
                </div>
            </div>

            {{-- Table --}}
            <div class="mt-6 rounded-2xl border border-slate-200 bg-white shadow-sm
                        dark:border-slate-800 dark:bg-slate-950/40 overflow-hidden">
                <div class="px-5 py-4 border-b border-slate-200 dark:border-slate-800">
                    <h3 class="text-base font-semibold text-slate-900 dark:text-slate-100">{{ __('manager.per_child') }}</h3>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full">
                        <thead>
                            <tr class="bg-slate-50 dark:bg-slate-900/60">
                                <th class="text-left px-5 py-3 text-xs font-semibold uppercase tracking-wider text-slate-600 dark:text-slate-300">{{ __('manager.child') }}</th>
                                <th class="text-left px-5 py-3 text-xs font-semibold uppercase tracking-wider text-slate-600 dark:text-slate-300">{{ __('manager.room') }}</th>
                                <th class="text-left px-5 py-3 text-xs font-semibold uppercase tracking-wider text-slate-600 dark:text-slate-300">{{ __('manager.update') }}</th>
                                <th class="text-left px-5 py-3 text-xs font-semibold uppercase tracking-wider text-slate-600 dark:text-slate-300">{{ __('manager.meals') }}</th>
                                <th class="text-left px-5 py-3 text-xs font-semibold uppercase tracking-wider text-slate-600 dark:text-slate-300">{{ __('manager.sleep') }}</th>
                                <th class="text-left px-5 py-3 text-xs font-semibold uppercase tracking-wider text-slate-600 dark:text-slate-300">{{ __('manager.notes') }}</th>
                            </tr>
                        </thead>

                        <tbody class="divide-y divide-slate-200 dark:divide-slate-800">
                            @forelse ($children as $child)
                                @php $u = $updates->get($child->id); @endphp
                                <tr class="hover:bg-slate-50 dark:hover:bg-slate-900/30">
                                    <td class="px-5 py-4 font-semibold text-slate-900 dark:text-slate-100">
                                        {{ $child->first_name }} {{ $child->last_name }}
                                    </td>
                                    <td class="px-5 py-4 text-slate-700 dark:text-slate-200">{{ $child->room?->name }}</td>

                                    @php
                                        $yes = "bg-emerald-50 text-emerald-700 border border-emerald-200 dark:bg-emerald-950/40 dark:text-emerald-200 dark:border-emerald-900/60";
                                        $no  = "bg-red-50 text-red-700 border border-red-200 dark:bg-red-950/40 dark:text-red-200 dark:border-red-900/60";
                                    @endphp

                                    <td class="px-5 py-4">
                                        <span class="inline-flex items-center rounded-full px-2 py-0.5 {{ $u ? $yes : $no }}">
                                            {{ $u ? __('manager.done') : __('manager.missing') }}
                                        </span>
                                    </td>
                                    <td class="px-5 py-4">
                                        <span class="inline-flex items-center rounded-full px-2 py-0.5 {{ filled($u?->meals) ? $yes : $no }}">
                                            {{ filled($u?->meals) ? __('manager.filled') : '—' }}
                                        </span>
                                    </td>
                                    <td class="px-5 py-4">
                                        <span class="inline-flex items-center rounded-full px-2 py-0.5 {{ filled($u?->sleep) ? $yes : $no }}">
                                            {{ filled($u?->sleep) ? __('manager.filled') : '—' }}
                                        </span>
                                    </td>
                                    <td class="px-5 py-4">
                                        <span class="inline-flex items-center rounded-full px-2 py-0.5 {{ filled($u?->notes) ? $yes : $no }}">
                                            {{ filled($u?->notes) ? __('manager.filled') : '—' }}
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="6" class="px-5 py-6 text-slate-600 dark:text-slate-300">{{ __('manager.no_children_found') }}</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>