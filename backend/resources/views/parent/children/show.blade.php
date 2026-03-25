<x-app-layout>
    <x-slot name="header">
<<<<<<< HEAD
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                    {{ $child->first_name }} {{ $child->last_name }}
                </h2>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                    Child profile and history
                </p>
            </div>

            <a href="{{ route('parent.children.index') }}"
               class="inline-flex items-center px-4 py-2 bg-gray-200 dark:bg-gray-700 text-gray-800 dark:text-gray-200 rounded-md hover:bg-gray-300 dark:hover:bg-gray-600 transition">
                Back to My Children
=======
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-semibold text-slate-900 dark:text-slate-100">
                    {{ $child->first_name }} {{ $child->last_name }}
                </h2>
                <p class="text-sm text-slate-600 dark:text-slate-300 mt-1">
                    {{ $child->age_in_months }} months old
                    @if($child->room)
                        · {{ $child->room->name }}
                    @endif
                </p>
            </div>
            <a href="{{ route('parent.children.index') }}"
               class="inline-flex items-center rounded-full px-3 py-1 text-xs font-medium bg-slate-50 text-slate-700 border border-slate-200 hover:bg-slate-100">
                ← My Children
>>>>>>> cf022aeca2727a2cf4f4d1f657d7c8f60f10ca7c
            </a>
        </div>
    </x-slot>

<<<<<<< HEAD
    @php
        $roomLabel = $child->room?->name
            ?? $child->room?->room_name
            ?? ($child->room ? 'Assigned room' : 'No room assigned');

        $tabClasses = function ($tab) use ($activeTab) {
            return $activeTab === $tab
                ? 'bg-blue-600 text-white'
                : 'bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700';
        };
    @endphp

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <div class="bg-white dark:bg-gray-800 shadow sm:rounded-lg p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">
                            Profile details
                        </h3>

                        <div class="space-y-3 text-sm">
                            <p class="text-gray-700 dark:text-gray-300">
                                <span class="font-semibold">Full name:</span>
                                {{ $child->first_name }} {{ $child->last_name }}
                            </p>

                            <p class="text-gray-700 dark:text-gray-300">
                                <span class="font-semibold">Date of birth:</span>
                                {{ $child->dob ? $child->dob->format('d M Y') : 'Not recorded' }}
                            </p>

                            <p class="text-gray-700 dark:text-gray-300">
                                <span class="font-semibold">Room:</span>
                                {{ $roomLabel }}
                            </p>
                        </div>
                    </div>

                    <div>
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">
                            Health notes
                        </h3>

                        <div class="space-y-4 text-sm">
                            <div>
                                <p class="font-semibold text-gray-800 dark:text-gray-200">Allergies</p>
                                <p class="text-gray-700 dark:text-gray-300 mt-1">
                                    {{ $child->allergies ?: 'No allergies recorded.' }}
                                </p>
                            </div>

                            <div>
                                <p class="font-semibold text-gray-800 dark:text-gray-200">Medical notes</p>
                                <p class="text-gray-700 dark:text-gray-300 mt-1">
                                    {{ $child->medical_notes ?: 'No medical notes recorded.' }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="flex flex-wrap gap-2">
                <a href="{{ route('parent.children.show', $child) }}"
                   class="px-4 py-2 rounded-md text-sm font-medium {{ $tabClasses('profile') }}">
                    Profile
                </a>

                <a href="{{ route('parent.children.show', ['child' => $child, 'tab' => 'updates']) }}"
                   class="px-4 py-2 rounded-md text-sm font-medium {{ $tabClasses('updates') }}">
                    Daily Updates
                </a>

                <a href="{{ route('parent.children.show', ['child' => $child, 'tab' => 'attendance']) }}"
                   class="px-4 py-2 rounded-md text-sm font-medium {{ $tabClasses('attendance') }}">
                    Attendance
                </a>

                <a href="{{ route('parent.children.show', ['child' => $child, 'tab' => 'reports']) }}"
                   class="px-4 py-2 rounded-md text-sm font-medium {{ $tabClasses('reports') }}">
                    Daily Reports
                </a>

                <a href="{{ route('parent.children.show', ['child' => $child, 'tab' => 'medication']) }}"
                   class="px-4 py-2 rounded-md text-sm font-medium {{ $tabClasses('medication') }}">
                    Medication Logs
                </a>
            </div>

            @if ($activeTab === 'profile')
                <div class="bg-white dark:bg-gray-800 shadow sm:rounded-lg p-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">
                        Overview
                    </h3>

                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                        <div class="p-4 rounded-lg bg-gray-50 dark:bg-gray-700">
                            <p class="text-sm text-gray-500 dark:text-gray-300">Daily Updates</p>
                            <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $dailyUpdates->count() }}</p>
                        </div>

                        <div class="p-4 rounded-lg bg-gray-50 dark:bg-gray-700">
                            <p class="text-sm text-gray-500 dark:text-gray-300">Attendance Records</p>
                            <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $attendanceRecords->count() }}</p>
                        </div>

                        <div class="p-4 rounded-lg bg-gray-50 dark:bg-gray-700">
                            <p class="text-sm text-gray-500 dark:text-gray-300">Daily Reports</p>
                            <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $dailyReports->count() }}</p>
                        </div>

                        <div class="p-4 rounded-lg bg-gray-50 dark:bg-gray-700">
                            <p class="text-sm text-gray-500 dark:text-gray-300">Medication Logs</p>
                            <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $medicationLogs->count() }}</p>
                        </div>
                    </div>
                </div>
            @endif

            @if ($activeTab === 'updates')
                <div class="bg-white dark:bg-gray-800 shadow sm:rounded-lg p-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">
                        Daily Updates
                    </h3>

                    @if ($dailyUpdates->isEmpty())
                        <p class="text-gray-600 dark:text-gray-300">No daily updates recorded yet.</p>
                    @else
                        <div class="overflow-x-auto">
                            <table class="min-w-full text-sm">
                                <thead>
                                    <tr class="border-b dark:border-gray-700 text-left">
                                        <th class="py-3 pr-4">Date</th>
                                        <th class="py-3 pr-4">Meals</th>
                                        <th class="py-3 pr-4">Sleep</th>
                                        <th class="py-3 pr-4">Notes</th>
                                        <th class="py-3">Created By</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($dailyUpdates as $update)
                                        <tr class="border-b dark:border-gray-700 align-top">
                                            <td class="py-3 pr-4">{{ $update->date ? $update->date->format('d M Y') : '—' }}</td>
                                            <td class="py-3 pr-4">{{ $update->meals ?: '—' }}</td>
                                            <td class="py-3 pr-4">{{ $update->sleep ?: '—' }}</td>
                                            <td class="py-3 pr-4">{{ $update->notes ?: '—' }}</td>
                                            <td class="py-3">{{ $update->createdBy?->name ?: '—' }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            @endif

            @if ($activeTab === 'attendance')
                <div class="bg-white dark:bg-gray-800 shadow sm:rounded-lg p-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">
                        Attendance History
                    </h3>

                    @if ($attendanceRecords->isEmpty())
                        <p class="text-gray-600 dark:text-gray-300">No attendance records recorded yet.</p>
                    @else
                        <div class="overflow-x-auto">
                            <table class="min-w-full text-sm">
                                <thead>
                                    <tr class="border-b dark:border-gray-700 text-left">
                                        <th class="py-3 pr-4">Date</th>
                                        <th class="py-3 pr-4">Status</th>
                                        <th class="py-3 pr-4">Check In</th>
                                        <th class="py-3 pr-4">Check Out</th>
                                        <th class="py-3 pr-4">Room</th>
                                        <th class="py-3">Recorded By</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($attendanceRecords as $attendance)
                                        <tr class="border-b dark:border-gray-700">
                                            <td class="py-3 pr-4">{{ $attendance->date ? $attendance->date->format('d M Y') : '—' }}</td>
                                            <td class="py-3 pr-4">{{ ucfirst($attendance->status ?? '—') }}</td>
                                            <td class="py-3 pr-4">{{ $attendance->check_in_at ? $attendance->check_in_at->format('H:i') : '—' }}</td>
                                            <td class="py-3 pr-4">{{ $attendance->check_out_at ? $attendance->check_out_at->format('H:i') : '—' }}</td>
                                            <td class="py-3 pr-4">{{ $attendance->room?->name ?? $attendance->room?->room_name ?? '—' }}</td>
                                            <td class="py-3">{{ $attendance->recordedBy?->name ?: '—' }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            @endif

            @if ($activeTab === 'reports')
                <div class="bg-white dark:bg-gray-800 shadow sm:rounded-lg p-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">
                        Daily Reports
                    </h3>

                    @if ($dailyReports->isEmpty())
                        <p class="text-gray-600 dark:text-gray-300">No daily reports recorded yet.</p>
                    @else
                        <div class="space-y-4">
                            @foreach ($dailyReports as $report)
                                <div class="border dark:border-gray-700 rounded-lg p-4">
                                    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-2 mb-3">
                                        <p class="font-semibold text-gray-900 dark:text-gray-100">
                                            {{ $report->date ? \Carbon\Carbon::parse($report->date)->format('d M Y') : 'No date' }}
                                        </p>
                                        <p class="text-sm text-gray-600 dark:text-gray-400">
                                            Carer: {{ $report->carer?->name ?: '—' }} |
                                            Media items: {{ $report->mediaUpdates->count() }}
                                        </p>
                                    </div>

                                    <p class="text-gray-700 dark:text-gray-300 whitespace-pre-line">
                                        {{ $report->daily_report ?: 'No written report.' }}
                                    </p>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            @endif

            @if ($activeTab === 'medication')
                <div class="bg-white dark:bg-gray-800 shadow sm:rounded-lg p-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">
                        Medication Logs
                    </h3>

                    @if ($medicationLogs->isEmpty())
                        <p class="text-gray-600 dark:text-gray-300">No medication logs recorded yet.</p>
                    @else
                        <div class="overflow-x-auto">
                            <table class="min-w-full text-sm">
                                <thead>
                                    <tr class="border-b dark:border-gray-700 text-left">
                                        <th class="py-3 pr-4">Date</th>
                                        <th class="py-3 pr-4">Time</th>
                                        <th class="py-3 pr-4">Medication</th>
                                        <th class="py-3 pr-4">Dosage</th>
                                        <th class="py-3 pr-4">Notes</th>
                                        <th class="py-3">Carer</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($medicationLogs as $log)
                                        <tr class="border-b dark:border-gray-700 align-top">
                                            <td class="py-3 pr-4">
                                                {{ $log->date ? \Carbon\Carbon::parse($log->date)->format('d M Y') : '—' }}
                                            </td>
                                            <td class="py-3 pr-4">{{ $log->time_given ?: '—' }}</td>
                                            <td class="py-3 pr-4">{{ $log->medication_name ?: '—' }}</td>
                                            <td class="py-3 pr-4">{{ $log->dosage ?: '—' }}</td>
                                            <td class="py-3 pr-4">{{ $log->notes ?: '—' }}</td>
                                            <td class="py-3">{{ $log->carer?->name ?: '—' }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
=======
    <div class="py-8">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8 space-y-6">

            {{-- Milestone Progress Summary Card --}}
            <div class="rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-slate-800 dark:bg-slate-950/40 p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-base font-semibold text-slate-900 dark:text-slate-100">Development Milestones</h3>
                    <a href="{{ route('parent.milestones.show', $child) }}"
                       class="text-sm text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300 font-medium">
                        View All →
                    </a>
                </div>

                @php
                    $overallProgress = $child->milestoneProgress();
                    $categories = [
                        'wellbeing'          => ['label' => 'Well-being',           'colour' => 'green'],
                        'identity-belonging' => ['label' => 'Identity & Belonging',  'colour' => 'blue'],
                        'communicating'      => ['label' => 'Communicating',         'colour' => 'purple'],
                        'exploring-thinking' => ['label' => 'Exploring & Thinking',  'colour' => 'amber'],
                    ];
                @endphp

                @if($overallProgress['total'] > 0)
                    <div class="mb-4">
                        <div class="flex items-center justify-between text-sm mb-1">
                            <span class="text-slate-600 dark:text-slate-300">Overall Progress</span>
                            <span class="font-semibold text-slate-800 dark:text-slate-100">
                                {{ $overallProgress['achieved'] }}/{{ $overallProgress['total'] }} ({{ $overallProgress['percentage'] }}%)
                            </span>
                        </div>
                        <div class="h-3 bg-slate-100 dark:bg-slate-800 rounded-full overflow-hidden">
                            <div class="h-full bg-gradient-to-r from-blue-500 to-indigo-600 rounded-full transition-all"
                                 style="width: {{ $overallProgress['percentage'] }}%"></div>
                        </div>
                    </div>

                    <div class="space-y-2">
                        @foreach($categories as $key => $cat)
                            @php $p = $child->milestoneProgress($key); @endphp
                            @if($p['total'] > 0)
                                <div class="flex items-center gap-3">
                                    <span class="text-xs text-slate-500 dark:text-slate-400 w-36 truncate">{{ $cat['label'] }}</span>
                                    <div class="flex-1 h-2 bg-{{ $cat['colour'] }}-100 dark:bg-{{ $cat['colour'] }}-950/30 rounded-full overflow-hidden">
                                        <div class="h-full bg-{{ $cat['colour'] }}-500 rounded-full"
                                             style="width: {{ $p['percentage'] }}%"></div>
                                    </div>
                                    <span class="text-xs text-slate-500 dark:text-slate-400 w-10 text-right">{{ $p['achieved'] }}/{{ $p['total'] }}</span>
                                </div>
                            @endif
                        @endforeach
                    </div>
                @else
                    <p class="text-slate-400 dark:text-slate-500 text-sm">No milestones available for this age range yet.</p>
                @endif
            </div>

            {{-- Tab Navigation --}}
            <div class="rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-slate-800 dark:bg-slate-950/40 overflow-hidden">
                <div class="flex border-b border-slate-200 dark:border-slate-800 overflow-x-auto">
                    @foreach(['profile' => 'Profile', 'updates' => 'Daily Updates', 'attendance' => 'Attendance', 'reports' => 'Reports', 'medication' => 'Medication'] as $tabKey => $tabLabel)
                        <a href="{{ route('parent.children.show', [$child, 'tab' => $tabKey]) }}"
                           class="px-5 py-3 text-sm font-medium whitespace-nowrap border-b-2 transition
                                  {{ $activeTab === $tabKey
                                     ? 'border-blue-600 text-blue-600 dark:text-blue-400 dark:border-blue-400'
                                     : 'border-transparent text-slate-500 hover:text-slate-700 dark:text-slate-400 dark:hover:text-slate-200' }}">
                            {{ $tabLabel }}
                        </a>
                    @endforeach
                </div>

                {{-- Profile Tab --}}
                @if($activeTab === 'profile')
                    <div class="p-6 space-y-4">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <p class="text-xs font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-400 mb-1">Full Name</p>
                                <p class="text-sm text-slate-900 dark:text-slate-100">{{ $child->first_name }} {{ $child->last_name }}</p>
                            </div>
                            <div>
                                <p class="text-xs font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-400 mb-1">Date of Birth</p>
                                <p class="text-sm text-slate-900 dark:text-slate-100">
                                    {{ $child->dob ? \Carbon\Carbon::parse($child->dob)->format('d M Y') : '—' }}
                                </p>
                            </div>
                            <div>
                                <p class="text-xs font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-400 mb-1">Age</p>
                                <p class="text-sm text-slate-900 dark:text-slate-100">{{ $child->age_in_months }} months</p>
                            </div>
                            <div>
                                <p class="text-xs font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-400 mb-1">Room</p>
                                <p class="text-sm text-slate-900 dark:text-slate-100">{{ $child->room->name ?? 'Unassigned' }}</p>
                            </div>
                        </div>

                        @if($child->allergies)
                            <div>
                                <p class="text-xs font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-400 mb-1">Allergies</p>
                                <p class="text-sm text-slate-900 dark:text-slate-100">{{ $child->allergies }}</p>
                            </div>
                        @endif

                        @if($child->medical_notes)
                            <div>
                                <p class="text-xs font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-400 mb-1">Medical Notes</p>
                                <p class="text-sm text-slate-900 dark:text-slate-100">{{ $child->medical_notes }}</p>
                            </div>
                        @endif
                    </div>

                {{-- Daily Updates Tab --}}
                @elseif($activeTab === 'updates')
                    <div class="divide-y divide-slate-200 dark:divide-slate-800">
                        @forelse($dailyUpdates as $update)
                            <div class="p-5 space-y-2">
                                <div class="flex items-center justify-between">
                                    <p class="text-sm font-semibold text-slate-900 dark:text-slate-100">
                                        {{ \Carbon\Carbon::parse($update->date)->format('d M Y') }}
                                    </p>
                                    <span class="text-xs text-slate-500 dark:text-slate-400">
                                        by {{ $update->createdBy->name ?? '—' }}
                                    </span>
                                </div>
                                @if($update->meals)
                                    <p class="text-xs text-slate-600 dark:text-slate-300"><span class="font-medium">Meals:</span> {{ $update->meals }}</p>
                                @endif
                                @if($update->sleep)
                                    <p class="text-xs text-slate-600 dark:text-slate-300"><span class="font-medium">Sleep:</span> {{ $update->sleep }}</p>
                                @endif
                                @if($update->notes)
                                    <p class="text-xs text-slate-600 dark:text-slate-300"><span class="font-medium">Notes:</span> {{ $update->notes }}</p>
                                @endif
                            </div>
                        @empty
                            <div class="px-5 py-8 text-center text-sm text-slate-500 dark:text-slate-400">
                                No daily updates recorded yet.
                            </div>
                        @endforelse
                    </div>

                {{-- Attendance Tab --}}
                @elseif($activeTab === 'attendance')
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
                                @forelse($attendanceRecords as $att)
                                    <tr class="hover:bg-slate-50 dark:hover:bg-slate-900/30">
                                        <td class="px-5 py-3 text-slate-700 dark:text-slate-200">
                                            {{ \Carbon\Carbon::parse($att->date)->format('d M Y') }}
                                        </td>
                                        <td class="px-5 py-3">
                                            <span class="inline-flex items-center rounded-full px-2 py-0.5 text-xs font-semibold
                                                {{ $att->status === 'present'
                                                   ? 'bg-green-50 text-green-700 border border-green-200 dark:bg-green-950/30 dark:text-green-300 dark:border-green-800/60'
                                                   : 'bg-red-50 text-red-700 border border-red-200 dark:bg-red-950/30 dark:text-red-300 dark:border-red-800/60' }}">
                                                {{ ucfirst($att->status) }}
                                            </span>
                                        </td>
                                        <td class="px-5 py-3 text-slate-700 dark:text-slate-200">
                                            {{ $att->room->name ?? '—' }}
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="px-5 py-8 text-center text-sm text-slate-500 dark:text-slate-400">
                                            No attendance records yet.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                {{-- Reports Tab --}}
                @elseif($activeTab === 'reports')
                    <div class="divide-y divide-slate-200 dark:divide-slate-800">
                        @forelse($dailyReports as $report)
                            <div class="p-5 space-y-2">
                                <div class="flex items-center justify-between">
                                    <p class="text-sm font-semibold text-slate-900 dark:text-slate-100">
                                        {{ \Carbon\Carbon::parse($report->date)->format('d M Y') }}
                                    </p>
                                    <span class="text-xs text-slate-500 dark:text-slate-400">
                                        by {{ $report->carer->name ?? '—' }}
                                    </span>
                                </div>
                                @if($report->daily_report)
                                    <p class="text-sm text-slate-700 dark:text-slate-300">{{ $report->daily_report }}</p>
                                @endif
                            </div>
                        @empty
                            <div class="px-5 py-8 text-center text-sm text-slate-500 dark:text-slate-400">
                                No daily reports yet.
                            </div>
                        @endforelse
                    </div>

                {{-- Medication Tab --}}
                @elseif($activeTab === 'medication')
                    <div class="overflow-x-auto">
                        <table class="min-w-full text-sm">
                            <thead>
                                <tr class="bg-slate-50 dark:bg-slate-900/60">
                                    <th class="text-left px-5 py-3 text-xs font-semibold uppercase tracking-wider text-slate-600 dark:text-slate-300">Date</th>
                                    <th class="text-left px-5 py-3 text-xs font-semibold uppercase tracking-wider text-slate-600 dark:text-slate-300">Medication</th>
                                    <th class="text-left px-5 py-3 text-xs font-semibold uppercase tracking-wider text-slate-600 dark:text-slate-300">Dosage</th>
                                    <th class="text-left px-5 py-3 text-xs font-semibold uppercase tracking-wider text-slate-600 dark:text-slate-300">Given By</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-200 dark:divide-slate-800">
                                @forelse($medicationLogs as $log)
                                    <tr class="hover:bg-slate-50 dark:hover:bg-slate-900/30">
                                        <td class="px-5 py-3 text-slate-700 dark:text-slate-200">
                                            {{ \Carbon\Carbon::parse($log->date)->format('d M Y') }}
                                            @if($log->time_given)
                                                <span class="text-slate-400 ml-1">{{ $log->time_given }}</span>
                                            @endif
                                        </td>
                                        <td class="px-5 py-3 font-medium text-slate-900 dark:text-slate-100">{{ $log->medication_name }}</td>
                                        <td class="px-5 py-3 text-slate-700 dark:text-slate-200">{{ $log->dosage ?? '—' }}</td>
                                        <td class="px-5 py-3 text-slate-700 dark:text-slate-200">{{ $log->carer->name ?? '—' }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="px-5 py-8 text-center text-sm text-slate-500 dark:text-slate-400">
                                            No medication records yet.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                @endif

            </div>

        </div>
    </div>
</x-app-layout>
>>>>>>> cf022aeca2727a2cf4f4d1f657d7c8f60f10ca7c
