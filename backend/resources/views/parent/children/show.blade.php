<x-app-layout>
    <x-slot name="header">
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
            </a>
        </div>
    </x-slot>

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
                            <table class="min-w-full text-sm text-gray-900 dark:text-gray-100">
                                <thead>
                                    <tr class="border-b dark:border-gray-700 align-top text-gray-800 dark:text-gray-100">
                                        <th class="py-3 pr-4">Date</th>
                                        <th class="py-3 pr-4">Meals</th>
                                        <th class="py-3 pr-4">Sleep</th>
                                        <th class="py-3 pr-4">Notes</th>
                                        <th class="py-3">Created By</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($dailyUpdates as $update)
                                        <tr class="border-b dark:border-gray-700 align-top text-gray-800 dark:text-gray-100">
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
                            <table class="min-w-full text-sm text-gray-900 dark:text-gray-100">
                                <thead>
                                    <tr class="border-b dark:border-gray-700 align-top text-gray-800 dark:text-gray-100">
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
                                        <tr class="border-b dark:border-gray-700 align-top text-gray-800 dark:text-gray-100">
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

        @if ($report->mediaUpdates->isNotEmpty())
            <div class="mt-4 grid grid-cols-1 md:grid-cols-2 gap-4">
                @foreach ($report->mediaUpdates as $media)
                    @php
                        $url = \Illuminate\Support\Facades\Storage::url($media->file_path);
                        $isImage = $media->type === 'image';
                        $isVideo = $media->type === 'video';
                    @endphp

                    <div class="border dark:border-gray-700 rounded-lg p-3 bg-gray-50 dark:bg-gray-900/40">
                        @if ($isImage)
                            <img src="{{ $url }}"
                                 alt="Daily report media"
                                 class="w-full h-auto rounded-lg object-cover">
                        @elseif ($isVideo)
                            <video controls class="w-full rounded-lg">
                                <source src="{{ $url }}">
                                Your browser does not support the video tag.
                            </video>
                        @else
                            <a href="{{ $url }}"
                               target="_blank"
                               class="text-blue-600 dark:text-blue-400 hover:underline">
                                View attachment
                            </a>
                        @endif

                        @if ($media->notes)
                            <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">
                                {{ $media->notes }}
                            </p>
                        @endif
                    </div>
                @endforeach
            </div>
        @endif
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
                            <table class="min-w-full text-sm text-gray-900 dark:text-gray-100">
                                <thead>
                                    <tr class="border-b dark:border-gray-700 align-top text-gray-800 dark:text-gray-100">
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
                                        <tr class="border-b dark:border-gray-700 align-top text-gray-800 dark:text-gray-100">
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