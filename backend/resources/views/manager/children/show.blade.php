<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-semibold text-2xl text-slate-900 dark:text-slate-100 leading-tight">
                    {{ $child->first_name }} {{ $child->last_name }}
                </h2>
                <p class="text-sm text-slate-600 dark:text-slate-300 mt-1">
                    Child Profile
                </p>
            </div>

            <div class="flex items-center gap-2">
                <a href="{{ route('manager.children.edit', $child) }}"
                   class="inline-flex items-center justify-center gap-2 rounded-xl px-4 py-2 font-semibold text-white
                          bg-gradient-to-r from-blue-600 via-blue-700 to-indigo-700
                          shadow-sm shadow-blue-500/20
                          hover:shadow-md hover:shadow-blue-500/30 hover:brightness-110
                          focus:outline-none focus:ring-4 focus:ring-blue-200
                          active:translate-y-[1px]">
                    Edit
                </a>

                <form method="POST" action="{{ route('manager.children.destroy', $child) }}"
                      onsubmit="return confirm('Delete {{ addslashes($child->first_name . ' ' . $child->last_name) }}? This cannot be undone.')">
                    @csrf
                    @method('DELETE')
                    <button type="submit"
                            class="inline-flex items-center justify-center rounded-xl px-4 py-2 text-sm font-semibold
                                   bg-red-50 text-red-700 border border-red-200
                                   hover:bg-red-100
                                   dark:bg-red-950/30 dark:text-red-300 dark:border-red-800/60 dark:hover:bg-red-950/50
                                   focus:outline-none focus:ring-4 focus:ring-red-200">
                        Delete
                    </button>
                </form>

                <a href="{{ route('manager.children.index') }}"
                   class="inline-flex items-center rounded-full px-3 py-1 text-xs font-medium
                          bg-slate-50 text-slate-700 border border-slate-200 hover:bg-slate-100
                          dark:bg-slate-900/40 dark:text-slate-200 dark:border-slate-700/60">
                    Back
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8 space-y-6">

            @if (session('success'))
                <div class="rounded-2xl border border-green-200 bg-green-50 px-4 py-3 text-green-700
                            dark:border-green-900/60 dark:bg-green-950/30 dark:text-green-200">
                    {{ session('success') }}
                </div>
            @endif

            {{-- Details Card --}}
            <div class="rounded-2xl border border-slate-200 bg-white shadow-sm
                        dark:border-slate-800 dark:bg-slate-950/40 overflow-hidden">
                <div class="px-5 py-4 border-b border-slate-200 dark:border-slate-800">
                    <h3 class="text-base font-semibold text-slate-900 dark:text-slate-100">Details</h3>
                </div>
                <div class="p-6 grid grid-cols-1 sm:grid-cols-2 gap-5 text-sm">
                    <div class="space-y-3 text-slate-800 dark:text-slate-200">
                        <p>
                            <span class="font-semibold text-slate-600 dark:text-slate-400">Full Name:</span>
                            {{ $child->first_name }} {{ $child->last_name }}
                        </p>
                        <p>
                            <span class="font-semibold text-slate-600 dark:text-slate-400">Date of Birth:</span>
                            {{ $child->dob ? $child->dob->format('d M Y') : '—' }}
                        </p>
                        <p>
                            <span class="font-semibold text-slate-600 dark:text-slate-400">Room:</span>
                            @if ($child->room)
                                {{ $child->room->name }}
                            @else
                                <span class="inline-flex items-center rounded-full px-2 py-0.5 text-xs font-medium
                                             bg-amber-50 text-amber-700 border border-amber-200
                                             dark:bg-amber-950/30 dark:text-amber-300 dark:border-amber-800/60">
                                    Unassigned
                                </span>
                            @endif
                        </p>
                    </div>
                    <div class="space-y-3 text-slate-800 dark:text-slate-200">
                        <div>
                            <span class="font-semibold text-slate-600 dark:text-slate-400">Allergies:</span>
                            <p class="mt-1">{{ $child->allergies ?: 'None' }}</p>
                        </div>
                        <div>
                            <span class="font-semibold text-slate-600 dark:text-slate-400">Medical Notes:</span>
                            <p class="mt-1">{{ $child->medical_notes ?: 'None' }}</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Parents --}}
            <div class="rounded-2xl border border-slate-200 bg-white shadow-sm
                        dark:border-slate-800 dark:bg-slate-950/40 overflow-hidden">
                <div class="px-5 py-4 border-b border-slate-200 dark:border-slate-800">
                    <h3 class="text-base font-semibold text-slate-900 dark:text-slate-100">Linked Parents</h3>
                </div>
                <div class="p-6">
                    @forelse ($child->parents as $parent)
                        <div class="flex items-center justify-between py-2 border-b border-slate-100 dark:border-slate-800 last:border-0">
                            <div class="text-sm text-slate-800 dark:text-slate-200">
                                <span class="font-medium">{{ $parent->name }}</span>
                                <span class="text-slate-500 dark:text-slate-400 ml-2">{{ $parent->email }}</span>
                            </div>
                            <div class="flex items-center gap-2">
                                @if ($parent->pivot->relationship_type)
                                    <span class="inline-flex items-center rounded-full px-2 py-0.5 text-xs font-medium
                                                 bg-slate-100 text-slate-700 border border-slate-200
                                                 dark:bg-slate-900/40 dark:text-slate-200 dark:border-slate-700/60">
                                        {{ ucfirst($parent->pivot->relationship_type) }}
                                    </span>
                                @endif
                                @if ($parent->pivot->legal_guardian)
                                    <span class="inline-flex items-center rounded-full px-2 py-0.5 text-xs font-medium
                                                 bg-indigo-50 text-indigo-700 border border-indigo-200
                                                 dark:bg-indigo-950/30 dark:text-indigo-300 dark:border-indigo-800/60">
                                        Legal Guardian
                                    </span>
                                @endif
                            </div>
                        </div>
                    @empty
                        <p class="text-sm text-slate-600 dark:text-slate-300">No parents linked to this child.</p>
                    @endforelse
                </div>
            </div>

            {{-- Recent Attendance --}}
            <div class="rounded-2xl border border-slate-200 bg-white shadow-sm
                        dark:border-slate-800 dark:bg-slate-950/40 overflow-hidden">
                <div class="px-5 py-4 border-b border-slate-200 dark:border-slate-800">
                    <h3 class="text-base font-semibold text-slate-900 dark:text-slate-100">Recent Attendance</h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full text-sm">
                        <thead>
                            <tr class="bg-slate-50 dark:bg-slate-900/60">
                                <th class="text-left px-5 py-3 text-xs font-semibold uppercase tracking-wider text-slate-600 dark:text-slate-300">Date</th>
                                <th class="text-left px-5 py-3 text-xs font-semibold uppercase tracking-wider text-slate-600 dark:text-slate-300">Status</th>
                                <th class="text-left px-5 py-3 text-xs font-semibold uppercase tracking-wider text-slate-600 dark:text-slate-300">Room</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-200 dark:divide-slate-800">
                            @forelse ($recentAttendances as $attendance)
                                <tr>
                                    <td class="px-5 py-3 text-slate-800 dark:text-slate-200">
                                        {{ $attendance->date->format('d M Y') }}
                                    </td>
                                    <td class="px-5 py-3">
                                        <span class="inline-flex items-center rounded-full px-2 py-0.5 text-xs font-medium
                                                     {{ $attendance->status === 'present'
                                                        ? 'bg-green-50 text-green-700 border border-green-200 dark:bg-green-950/30 dark:text-green-300 dark:border-green-800/60'
                                                        : 'bg-red-50 text-red-700 border border-red-200 dark:bg-red-950/30 dark:text-red-300 dark:border-red-800/60' }}">
                                            {{ ucfirst($attendance->status) }}
                                        </span>
                                    </td>
                                    <td class="px-5 py-3 text-slate-700 dark:text-slate-200">
                                        {{ $attendance->room->name ?? '—' }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="px-5 py-4 text-slate-600 dark:text-slate-300">No attendance records.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Recent Daily Reports --}}
            <div class="rounded-2xl border border-slate-200 bg-white shadow-sm
                        dark:border-slate-800 dark:bg-slate-950/40 overflow-hidden">
                <div class="px-5 py-4 border-b border-slate-200 dark:border-slate-800">
                    <h3 class="text-base font-semibold text-slate-900 dark:text-slate-100">Recent Daily Reports</h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full text-sm">
                        <thead>
                            <tr class="bg-slate-50 dark:bg-slate-900/60">
                                <th class="text-left px-5 py-3 text-xs font-semibold uppercase tracking-wider text-slate-600 dark:text-slate-300">Date</th>
                                <th class="text-left px-5 py-3 text-xs font-semibold uppercase tracking-wider text-slate-600 dark:text-slate-300">Carer</th>
                                <th class="text-left px-5 py-3 text-xs font-semibold uppercase tracking-wider text-slate-600 dark:text-slate-300">Excerpt</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-200 dark:divide-slate-800">
                            @forelse ($recentDailyReports as $report)
                                <tr>
                                    <td class="px-5 py-3 text-slate-800 dark:text-slate-200">
                                        {{ \Carbon\Carbon::parse($report->date)->format('d M Y') }}
                                    </td>
                                    <td class="px-5 py-3 text-slate-700 dark:text-slate-200">
                                        {{ $report->carer->name ?? '—' }}
                                    </td>
                                    <td class="px-5 py-3 text-slate-700 dark:text-slate-200">
                                        {{ Str::limit($report->daily_report, 80) }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="px-5 py-4 text-slate-600 dark:text-slate-300">No daily reports.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Recent Medication Logs --}}
            <div class="rounded-2xl border border-slate-200 bg-white shadow-sm
                        dark:border-slate-800 dark:bg-slate-950/40 overflow-hidden">
                <div class="px-5 py-4 border-b border-slate-200 dark:border-slate-800">
                    <h3 class="text-base font-semibold text-slate-900 dark:text-slate-100">Recent Medication Logs</h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full text-sm">
                        <thead>
                            <tr class="bg-slate-50 dark:bg-slate-900/60">
                                <th class="text-left px-5 py-3 text-xs font-semibold uppercase tracking-wider text-slate-600 dark:text-slate-300">Date</th>
                                <th class="text-left px-5 py-3 text-xs font-semibold uppercase tracking-wider text-slate-600 dark:text-slate-300">Medication</th>
                                <th class="text-left px-5 py-3 text-xs font-semibold uppercase tracking-wider text-slate-600 dark:text-slate-300">Dosage</th>
                                <th class="text-left px-5 py-3 text-xs font-semibold uppercase tracking-wider text-slate-600 dark:text-slate-300">Carer</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-200 dark:divide-slate-800">
                            @forelse ($recentMedicationLogs as $log)
                                <tr>
                                    <td class="px-5 py-3 text-slate-800 dark:text-slate-200">
                                        {{ \Carbon\Carbon::parse($log->date)->format('d M Y') }}
                                    </td>
                                    <td class="px-5 py-3 text-slate-700 dark:text-slate-200">
                                        {{ $log->medication_name }}
                                    </td>
                                    <td class="px-5 py-3 text-slate-700 dark:text-slate-200">
                                        {{ $log->dosage }}
                                    </td>
                                    <td class="px-5 py-3 text-slate-700 dark:text-slate-200">
                                        {{ $log->carer->name ?? '—' }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-5 py-4 text-slate-600 dark:text-slate-300">No medication logs.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
