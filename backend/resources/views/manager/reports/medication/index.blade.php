<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-semibold text-slate-900 dark:text-slate-100">Medication Logs</h2>
                <p class="text-sm text-slate-600 dark:text-slate-300 mt-1">
                    View medication records by room and date.
                </p>
            </div>

            <a href="{{ route('manager.dashboard') }}"
               class="inline-flex items-center rounded-xl border border-slate-200 bg-white px-4 py-2 text-sm font-medium text-slate-700 shadow-sm hover:bg-slate-50
                      dark:bg-slate-900/40 dark:text-slate-200 dark:border-slate-700">
                ← Back to Dashboard
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <div class="rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-slate-800 dark:bg-slate-950/40">
                <div class="p-5">
                    <form method="GET" class="grid grid-cols-1 sm:grid-cols-3 gap-4 items-end">
                        <div>
                            <label class="block text-sm font-medium mb-1 text-slate-700 dark:text-slate-200">Date</label>
                            <input type="date" name="date" value="{{ $date }}"
                                   class="w-full rounded-xl border border-slate-300 bg-white px-3 py-2 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100">
                        </div>

                        <div>
                            <label class="block text-sm font-medium mb-1 text-slate-700 dark:text-slate-200">Room</label>
                            <select name="room_id"
                                    class="w-full rounded-xl border border-slate-300 bg-white px-3 py-2 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100">
                                <option value="">All rooms</option>
                                @foreach($rooms as $room)
                                    <option value="{{ $room->id }}" @selected((string)$roomId === (string)$room->id)>
                                        {{ $room->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <button class="rounded-xl px-4 py-2 font-semibold text-white bg-blue-600 hover:bg-blue-700">
                            Load
                        </button>
                    </form>
                </div>
            </div>

            <div class="rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-slate-800 dark:bg-slate-950/40 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full">
                        <thead class="bg-slate-50 dark:bg-slate-900/60">
                            <tr>
                                <th class="text-left px-5 py-3 text-xs font-semibold uppercase text-slate-600 dark:text-slate-300">Child</th>
                                <th class="text-left px-5 py-3 text-xs font-semibold uppercase text-slate-600 dark:text-slate-300">Room</th>
                                <th class="text-left px-5 py-3 text-xs font-semibold uppercase text-slate-600 dark:text-slate-300">Medication</th>
                                <th class="text-left px-5 py-3 text-xs font-semibold uppercase text-slate-600 dark:text-slate-300">Dosage</th>
                                <th class="text-left px-5 py-3 text-xs font-semibold uppercase text-slate-600 dark:text-slate-300">Date</th>
                                <th class="text-left px-5 py-3 text-xs font-semibold uppercase text-slate-600 dark:text-slate-300">Time</th>
                                <th class="text-left px-5 py-3 text-xs font-semibold uppercase text-slate-600 dark:text-slate-300">Carer</th>
                                <th class="text-left px-5 py-3 text-xs font-semibold uppercase text-slate-600 dark:text-slate-300">Notes</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-200 dark:divide-slate-800">
                            @forelse($logs as $log)
                                <tr class="hover:bg-slate-50 dark:hover:bg-slate-900/30">
                                    <td class="px-5 py-4 text-slate-900 dark:text-slate-100">
                                        {{ $log->child->first_name }} {{ $log->child->last_name }}
                                    </td>
                                    <td class="px-5 py-4 text-slate-600 dark:text-slate-300">
                                        {{ $log->child->room->name ?? '—' }}
                                    </td>
                                    <td class="px-5 py-4 text-slate-600 dark:text-slate-300">{{ $log->medication_name }}</td>
                                    <td class="px-5 py-4 text-slate-600 dark:text-slate-300">{{ $log->dosage }}</td>
                                    <td class="px-5 py-4 text-slate-600 dark:text-slate-300">{{ $log->date }}</td>
                                    <td class="px-5 py-4 text-slate-600 dark:text-slate-300">{{ $log->time_given }}</td>
                                    <td class="px-5 py-4 text-slate-600 dark:text-slate-300">{{ $log->carer->name ?? '—' }}</td>
                                    <td class="px-5 py-4 text-slate-600 dark:text-slate-300">{{ $log->notes ?: '—' }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="px-5 py-6 text-slate-600 dark:text-slate-300">
                                        No medication records found.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>