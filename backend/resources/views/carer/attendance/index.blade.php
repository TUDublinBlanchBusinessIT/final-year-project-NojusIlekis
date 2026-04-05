<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">

            <!-- Left side (Back + Title) -->
            <div class="flex items-start gap-6">

                <!-- Back Button -->
                <a href="{{ route('carer.dashboard') }}"
                   class="inline-flex items-center gap-2 rounded-xl border border-slate-200 bg-white px-4 py-2 text-sm font-medium text-slate-700 shadow-sm hover:bg-slate-50 transition
                          dark:bg-slate-900/40 dark:text-slate-200 dark:border-slate-700">
                    ← Back to Dashboard
                </a>

                <!-- Title + Description -->
                <div>
                    <h2 class="font-semibold text-2xl text-slate-900 dark:text-slate-100 leading-tight">
                        Attendance
                    </h2>
                    <p class="text-sm text-slate-600 dark:text-slate-300 mt-1">
                        Select a room and date, then mark each child present or absent.
                    </p>
                </div>

            </div>

            <!-- Right side badge -->
            <span class="inline-flex items-center gap-2 rounded-full px-3 py-1 text-xs font-medium
                         bg-blue-50 text-blue-700 border border-blue-200
                         dark:bg-blue-950/40 dark:text-blue-200 dark:border-blue-900/60">
                Carer View
            </span>

        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8">

            @if (session('success'))
                <div class="mb-5 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-emerald-800
                            dark:border-emerald-900/50 dark:bg-emerald-950/40 dark:text-emerald-200">
                    <div class="flex items-center justify-between">
                        <span>{{ session('success') }}</span>
                        <span class="text-xs opacity-80">Saved</span>
                    </div>
                </div>
            @endif

            {{-- Filter Card --}}
            <div class="rounded-2xl border border-slate-200 bg-white shadow-sm
                        dark:border-slate-800 dark:bg-slate-950/40">
                <div class="px-5 py-4 border-b border-slate-200 dark:border-slate-800">
                    <h3 class="text-base font-semibold text-slate-900 dark:text-slate-100">Filters</h3>
                    <p class="text-sm text-slate-600 dark:text-slate-300 mt-1">Load children for a room and date.</p>
                </div>

                <div class="p-5">
                    <form method="GET" action="{{ route('carer.attendance.index') }}"
                          class="grid grid-cols-1 sm:grid-cols-3 gap-4 items-end">
                        <div>
                            <label class="block text-sm font-medium text-slate-700 dark:text-slate-200 mb-1">Date</label>
                            <input type="date" name="date" value="{{ $date }}"
                                   class="w-full rounded-xl border border-slate-300 bg-white px-3 py-2 text-slate-900
                                          focus:outline-none focus:ring-4 focus:ring-blue-200 focus:border-blue-500
                                          dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100
                                          dark:focus:ring-blue-900/40 dark:focus:border-blue-400">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-slate-700 dark:text-slate-200 mb-1">Room</label>
                            <select name="room_id"
                                    class="w-full rounded-xl border border-slate-300 bg-white px-3 py-2 text-slate-900
                                           focus:outline-none focus:ring-4 focus:ring-blue-200 focus:border-blue-500
                                           dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100
                                           dark:focus:ring-blue-900/40 dark:focus:border-blue-400">
                                <option value="">Select room...</option>
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
                            <span>Load</span>
                            <svg class="h-4 w-4 opacity-90 group-hover:translate-x-0.5 transition-transform"
                                 viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                <path fill-rule="evenodd"
                                      d="M10.293 15.707a1 1 0 010-1.414L13.586 11H4a1 1 0 110-2h9.586l-3.293-3.293a1 1 0 111.414-1.414l5 5a1 1 0 010 1.414l-5 5a1 1 0 01-1.414 0z"
                                      clip-rule="evenodd" />
                            </svg>
                        </button>
                    </form>
                </div>
            </div>

            {{-- Attendance Table Card --}}
            @if ($roomId)
                <div class="mt-6 rounded-2xl border border-slate-200 bg-white shadow-sm
                            dark:border-slate-800 dark:bg-slate-950/40 overflow-hidden">

                    <div class="px-5 py-4 border-b border-slate-200 dark:border-slate-800 flex flex-wrap items-center justify-between gap-3">
                        <div>
                            <h3 class="text-base font-semibold text-slate-900 dark:text-slate-100">Children</h3>
                            <p class="text-sm text-slate-600 dark:text-slate-300 mt-1">
                                Mark each child and save. Defaults to <span class="font-medium text-blue-700 dark:text-blue-300">Present</span>.
                            </p>
                        </div>

                        <div class="inline-flex items-center gap-2 rounded-xl px-3 py-2
                                    bg-blue-50 border border-blue-200 text-blue-700 text-sm font-medium
                                    dark:bg-blue-950/40 dark:border-blue-900/60 dark:text-blue-200">
                            <span>Date:</span>
                            <span class="font-semibold">{{ $date }}</span>
                        </div>
                    </div>

                    <form method="POST" action="{{ route('carer.attendance.store') }}">
                        @csrf
                        <input type="hidden" name="date" value="{{ $date }}">
                        <input type="hidden" name="room_id" value="{{ $roomId }}">

                        <div class="overflow-x-auto">
                            <table class="min-w-full">
                                <thead>
                                    <tr class="bg-slate-50 dark:bg-slate-900/60">
                                        <th class="text-left px-5 py-3 text-xs font-semibold uppercase tracking-wider text-slate-600 dark:text-slate-300">
                                            Child
                                        </th>
                                        <th class="text-left px-5 py-3 text-xs font-semibold uppercase tracking-wider text-slate-600 dark:text-slate-300">
                                            Status
                                        </th>
                                    </tr>
                                </thead>

                                <tbody class="divide-y divide-slate-200 dark:divide-slate-800">
                                    @forelse ($children as $child)
                                        @php
                                            $status = optional($existing->get($child->id))->status ?? 'present';
                                        @endphp

                                        <tr class="hover:bg-blue-50/60 dark:hover:bg-blue-950/20 transition-colors">
                                            <td class="px-5 py-4">
                                                <div class="flex items-center gap-3">
                                                    <div class="h-9 w-9 rounded-xl bg-gradient-to-br from-blue-600 to-indigo-700
                                                                text-white flex items-center justify-center text-sm font-semibold
                                                                shadow-sm shadow-blue-500/20">
                                                        {{ strtoupper(substr($child->first_name, 0, 1)) }}{{ strtoupper(substr($child->last_name, 0, 1)) }}
                                                    </div>
                                                    <div>
                                                        <div class="font-semibold text-slate-900 dark:text-slate-100">
                                                            {{ $child->first_name }} {{ $child->last_name }}
                                                        </div>
                                                        <div class="text-xs text-slate-500 dark:text-slate-400">
                                                            ID: {{ $child->id }}
                                                        </div>
                                                        @if($child->hasAllergies())
                                                            <span class="mt-1 inline-flex items-center bg-red-100 text-red-700 text-xs font-semibold px-2 py-0.5 rounded-full"
                                                                  title="Allergies: {{ $child->allergyList() }}">
                                                                ⚠️ {{ $child->allergyList() }}
                                                            </span>
                                                        @endif
                                                        <a href="{{ route('carer.milestones.show', $child) }}"
                                                           class="mt-1 inline-flex items-center text-blue-600 hover:text-blue-800 text-xs font-medium">
                                                            Milestones
                                                        </a>
                                                    </div>
                                                </div>
                                            </td>

                                            <td class="px-5 py-4">
                                                <select name="attendance[{{ $child->id }}]"
                                                        class="w-44 rounded-xl border px-3 py-2 text-sm font-medium
                                                               border-slate-300 bg-white text-slate-900
                                                               focus:outline-none focus:ring-4 focus:ring-blue-200 focus:border-blue-500
                                                               dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100
                                                               dark:focus:ring-blue-900/40 dark:focus:border-blue-400">
                                                    <option value="present" @selected($status === 'present')>Present</option>
                                                    <option value="absent" @selected($status === 'absent')>Absent</option>
                                                </select>

                                                <div class="mt-2 text-xs">
                                                    @if ($status === 'present')
                                                        <span class="inline-flex items-center rounded-full px-2 py-0.5
                                                                    bg-emerald-50 text-emerald-700 border border-emerald-200
                                                                    dark:bg-emerald-950/40 dark:text-emerald-200 dark:border-emerald-900/60">
                                                            Ready
                                                        </span>
                                                    @else
                                                        <span class="incline-flex items-center rounded-full px-2 py-0.5
                                                        bg-red-50 text-red-700 border border-red-200
                                                        dark:bg-red-950/40 dark:text-red-200 dark:border-red-900/60">
                                                            Marked Absent
                                                        </span>
                                                    @endif
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="2" class="px-5 py-6 text-slate-600 dark:text-slate-300">
                                                No children found for this room.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        <div class="px-5 py-4 border-t border-slate-200 dark:border-slate-800 flex flex-col sm:flex-row gap-3 sm:items-center sm:justify-between">
                            <div class="text-sm text-slate-600 dark:text-slate-300">
                                Tip: You can save multiple times — it updates existing records.
                            </div>

                            <button type="submit"
                                    class="group inline-flex items-center justify-center gap-2 rounded-xl px-5 py-2.5 font-semibold text-white
                                           bg-gradient-to-r from-blue-600 via-indigo-700 to-blue-800
                                           shadow-sm shadow-blue-500/20
                                           hover:shadow-md hover:shadow-blue-500/30 hover:brightness-110
                                           focus:outline-none focus:ring-4 focus:ring-blue-200
                                           active:translate-y-[1px]
                                           dark:shadow-blue-900/30 dark:focus:ring-blue-900/40">
                                <svg class="h-4 w-4 opacity-90 group-hover:scale-105 transition-transform"
                                     viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                    <path d="M17 3a1 1 0 00-1-1H4a2 2 0 00-2 2v12a2 2 0 002 2h12a2 2 0 002-2V3zM5 4h10v4H5V4zm0 12v-6h10v6H5z"/>
                                </svg>
                                <span>Save Attendance</span>
                            </button>
                        </div>
                    </form>
                </div>
            @endif

        </div>
    </div>
</x-app-layout>
