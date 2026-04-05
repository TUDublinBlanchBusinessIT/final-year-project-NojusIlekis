<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-semibold text-slate-900 dark:text-slate-100">Daily Reports</h2>
                <p class="text-sm text-slate-600 dark:text-slate-300 mt-1">
                    View end-of-day reports and attachments by room and date.
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

            {{-- Filters --}}
            <div class="rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-slate-800 dark:bg-slate-950/40">
                <div class="px-5 py-4 border-b border-slate-200 dark:border-slate-800">
                    <h3 class="text-base font-semibold text-slate-900 dark:text-slate-100">Filters</h3>
                    <p class="text-sm text-slate-600 dark:text-slate-300 mt-1">Choose date and room.</p>
                </div>

                <div class="p-5">
                    <form method="GET" class="grid grid-cols-1 sm:grid-cols-3 gap-4 items-end">
                        <div>
                            <label class="block text-sm font-medium text-slate-700 dark:text-slate-200 mb-1">Date</label>
                            <input type="date" name="date" value="{{ $date }}"
                                   class="w-full rounded-xl border border-slate-300 bg-white px-3 py-2
                                          dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-slate-700 dark:text-slate-200 mb-1">Room</label>
                            <select name="room_id"
                                    class="w-full rounded-xl border border-slate-300 bg-white px-3 py-2
                                           dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100">
                                <option value="">Select room...</option>
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

            {{-- Results --}}
            @if($roomId)
                <div class="rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-slate-800 dark:bg-slate-950/40 overflow-hidden">
                    <div class="px-5 py-4 border-b border-slate-200 dark:border-slate-800">
                        <h3 class="text-base font-semibold text-slate-900 dark:text-slate-100">Children</h3>
                        <p class="text-sm text-slate-600 dark:text-slate-300 mt-1">
                            Status for {{ $date }}.
                        </p>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="min-w-full">
                            <thead class="bg-slate-50 dark:bg-slate-900/60">
                                <tr>
                                    <th class="text-left px-5 py-3 text-xs font-semibold uppercase tracking-wider text-slate-600 dark:text-slate-300">Child</th>
                                    <th class="text-left px-5 py-3 text-xs font-semibold uppercase tracking-wider text-slate-600 dark:text-slate-300">Status</th>
                                    <th class="text-left px-5 py-3 text-xs font-semibold uppercase tracking-wider text-slate-600 dark:text-slate-300">Saved</th>
                                    <th class="text-left px-5 py-3 text-xs font-semibold uppercase tracking-wider text-slate-600 dark:text-slate-300">Attachments</th>
                                    <th class="text-left px-5 py-3 text-xs font-semibold uppercase tracking-wider text-slate-600 dark:text-slate-300">Acknowledgement</th>
                                    <th class="text-right px-5 py-3 text-xs font-semibold uppercase tracking-wider text-slate-600 dark:text-slate-300">Action</th>
                                </tr>
                            </thead>

                            <tbody class="divide-y divide-slate-200 dark:divide-slate-800">
                                @foreach($children as $child)
                                    @php
                                        $report = $child->reportForDay ?? null;
                                        $mediaCount = $report ? $report->mediaUpdates->count() : 0;
                                    @endphp

                                    <tr class="hover:bg-slate-50 dark:hover:bg-slate-900/30">
                                        <td class="px-5 py-4 text-slate-900 dark:text-slate-100 font-medium">
                                            {{ $child->first_name }} {{ $child->last_name }}
                                        </td>

                                        <td class="px-5 py-4">
                                            @if($report)
                                                <span class="inline-flex items-center rounded-full px-2 py-1 text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200
                                                             dark:bg-emerald-950/40 dark:text-emerald-200 dark:border-emerald-900/60">
                                                    Submitted
                                                </span>
                                            @else
                                                <span class="inline-flex items-center rounded-full px-2 py-1 text-xs font-semibold bg-red-50 text-red-700 border border-red-200
                                                             dark:bg-red-950/40 dark:text-red-200 dark:border-red-900/60">
                                                    Missing
                                                </span>
                                            @endif
                                        </td>

                                        <td class="px-5 py-4 text-sm text-slate-600 dark:text-slate-300">
                                            {{ $report ? $report->created_at->format('H:i:s') : '—' }}
                                        </td>

                                        <td class="px-5 py-4 text-sm text-slate-600 dark:text-slate-300">
                                            {{ $mediaCount }}
                                        </td>

                                        <td class="px-5 py-4">
                                            @if($report && $report->acknowledgement)
                                                @if($report->acknowledgement->status === 'acknowledged')
                                                    <span class="inline-flex items-center bg-green-100 text-green-800 text-xs font-semibold px-2.5 py-1 rounded-full">
                                                        <svg class="w-3.5 h-3.5 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                                        </svg>
                                                        Acknowledged
                                                    </span>
                                                @else
                                                    <span class="inline-flex items-center bg-amber-100 text-amber-800 text-xs font-semibold px-2.5 py-1 rounded-full">
                                                        <svg class="w-3.5 h-3.5 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"/>
                                                        </svg>
                                                        Pending
                                                    </span>
                                                @endif
                                            @elseif($report)
                                                <span class="inline-flex items-center bg-gray-100 text-gray-500 text-xs font-semibold px-2.5 py-1 rounded-full">
                                                    Not Requested
                                                </span>
                                            @else
                                                <span class="text-slate-400 text-sm">—</span>
                                            @endif
                                        </td>

                                        <td class="px-5 py-4 text-right">
                                            @if($report)
                                                <a href="{{ route('manager.reports.daily-reports.show', $report) }}"
                                                   class="inline-flex items-center rounded-xl bg-blue-600 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-700">
                                                    View
                                                </a>
                                            @else
                                                <span class="text-sm text-slate-400">No report</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach

                                @if($children->isEmpty())
                                    <tr>
                                        <td colspan="6" class="px-5 py-6 text-slate-600 dark:text-slate-300">
                                            No children found for this room.
                                        </td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif

        </div>
    </div>
</x-app-layout>