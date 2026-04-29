<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                    {{ $child->first_name }} {{ $child->last_name }}
                </h2>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                    {{ __('parent.child_profile_history') }}
                </p>
            </div>

            <a href="{{ route('parent.children.index') }}"
               class="inline-flex items-center px-4 py-2 bg-gray-200 dark:bg-gray-700 text-gray-800 dark:text-gray-200 rounded-md hover:bg-gray-300 dark:hover:bg-gray-600 transition">
                {{ __('parent.back_to_children') }}
            </a>
        </div>
    </x-slot>

    @php
        $roomLabel = $child->room?->name
            ?? $child->room?->room_name
            ?? ($child->room ? __('parent.assigned_room') : __('parent.no_room'));

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
                            {{ __('parent.profile_details') }}
                        </h3>

                        <div class="space-y-3 text-sm">
                            <p class="text-gray-700 dark:text-gray-300">
                                <span class="font-semibold">{{ __('parent.full_name') }}:</span>
                                {{ $child->first_name }} {{ $child->last_name }}
                            </p>

                            <p class="text-gray-700 dark:text-gray-300">
                                <span class="font-semibold">{{ __('parent.date_of_birth') }}:</span>
                                {{ $child->dob ? $child->dob->format('d M Y') : __('parent.not_recorded') }}'
                            </p>

                            <p class="text-gray-700 dark:text-gray-300">
                                <span class="font-semibold">{{ __('parent.room') }}:</span>
                                {{ $roomLabel }}
                            </p>
                        </div>
                    </div>

                    <div>
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">
                            {{ __('parent.health_notes') }}
                        </h3>

                        <div class="space-y-4 text-sm">
                            <div>
                                <p class="font-semibold text-gray-800 dark:text-gray-200">{{ __('parent.allergies') }}</p>
                                @if($child->allergyArray())
                                    <div class="flex flex-wrap gap-2 mt-2">
                                        @foreach($child->allergyArray() as $allergy)
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800 dark:bg-red-900/40 dark:text-red-300">
                                                {{ $allergy }}
                                            </span>
                                        @endforeach
                                    </div>
                                @else
                                    <p class="text-gray-700 dark:text-gray-300 mt-1">{{ __('parent.no_allergies') }}</p>
                                @endif
                            </div>

                            <div>
                                <p class="font-semibold text-gray-800 dark:text-gray-200">{{ __('parent.medical_notes') }}</p>
                                <p class="text-gray-700 dark:text-gray-300 mt-1">
                                    {{ $child->medical_notes ?: __('parent.no_medical_notes') }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Allergy Alert Banner --}}
            <x-allergy-alert :child="$child" />
            <x-medical-alert :child="$child" />

            {{-- Milestone Progress Summary --}}
            <div class="bg-white dark:bg-gray-800 shadow sm:rounded-lg p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">{{ __('parent.milestones') }}</h3>
                    <a href="{{ route('parent.milestones.show', $child) }}"
                       class="text-sm text-blue-600 hover:text-blue-800 dark:text-blue-400 font-medium">
                        {{ __('parent.view_all') }}
                    </a>
                </div>

                @php
                    $overallProgress = $child->milestoneProgress();
                    $milestoneCategories = [
                        'wellbeing'          => ['label' => 'Well-being',          'colour' => 'green'],
                        'identity-belonging' => ['label' => 'Identity & Belonging', 'colour' => 'blue'],
                        'communicating'      => ['label' => 'Communicating',        'colour' => 'purple'],
                        'exploring-thinking' => ['label' => 'Exploring & Thinking', 'colour' => 'amber'],
                    ];
                @endphp

                @if($overallProgress['total'] > 0)
                    <div class="mb-4">
                        <div class="flex items-center justify-between text-sm mb-1">
                            <span class="text-gray-600 dark:text-gray-300">{{ __('parent.overall_progress') }}</span>
                            <span class="font-semibold text-gray-800 dark:text-gray-100">
                                {{ $overallProgress['achieved'] }}/{{ $overallProgress['total'] }} ({{ $overallProgress['percentage'] }}%)
                            </span>
                        </div>
                        <div class="h-3 bg-gray-100 dark:bg-gray-700 rounded-full overflow-hidden">
                            <div class="h-full bg-blue-600 rounded-full transition-all"
                                 style="width: {{ $overallProgress['percentage'] }}%"></div>
                        </div>
                    </div>

                    <div class="space-y-2">
                        @foreach($milestoneCategories as $key => $cat)
                            @php $p = $child->milestoneProgress($key); @endphp
                            @if($p['total'] > 0)
                                <div class="flex items-center gap-3 text-sm">
                                    <span class="text-gray-500 dark:text-gray-400 w-36 truncate">{{ $cat['label'] }}</span>
                                    <div class="flex-1 h-2 bg-gray-100 dark:bg-gray-700 rounded-full overflow-hidden">
                                        <div class="h-full bg-{{ $cat['colour'] }}-500 rounded-full"
                                             style="width: {{ $p['percentage'] }}%"></div>
                                    </div>
                                    <span class="text-gray-500 dark:text-gray-400 w-10 text-right">{{ $p['achieved'] }}/{{ $p['total'] }}</span>
                                </div>
                            @endif
                        @endforeach
                    </div>
                @else
                    <p class="text-gray-500 dark:text-gray-400 text-sm">{{ __('parent.no_milestones') }}.</p>
                @endif
            </div>

            {{-- Tab Navigation --}}
            <div class="flex flex-wrap gap-2">
                <a href="{{ route('parent.children.show', $child) }}"
                   class="px-4 py-2 rounded-md text-sm font-medium {{ $tabClasses('profile') }}">
                    {{ __('parent.profile') }}
                </a>

                <a href="{{ route('parent.children.show', ['child' => $child, 'tab' => 'updates']) }}"
                   class="px-4 py-2 rounded-md text-sm font-medium {{ $tabClasses('updates') }}">
                    {{ __('parent.daily_updates') }}
                </a>

                <a href="{{ route('parent.children.show', ['child' => $child, 'tab' => 'attendance']) }}"
                   class="px-4 py-2 rounded-md text-sm font-medium {{ $tabClasses('attendance') }}">
                    {{ __('parent.attendance') }}
                </a>

                <a href="{{ route('parent.children.show', ['child' => $child, 'tab' => 'reports']) }}"
                   class="px-4 py-2 rounded-md text-sm font-medium {{ $tabClasses('reports') }}">
                    {{ __('parent.daily_reports') }}
                </a>

                <a href="{{ route('parent.children.show', ['child' => $child, 'tab' => 'medication']) }}"
                   class="px-4 py-2 rounded-md text-sm font-medium {{ $tabClasses('medication') }}">
                    {{ __('parent.medication_logs') }}
                </a>
            </div>

            @if ($activeTab === 'profile')
                <div class="bg-white dark:bg-gray-800 shadow sm:rounded-lg p-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">
                        {{ __('parent.overview') }}
                    </h3>

                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                        <div class="p-4 rounded-lg bg-gray-50 dark:bg-gray-700">
                            <p class="text-sm text-gray-500 dark:text-gray-300">{{ __('parent.daily_updates') }}</p>
                            <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $dailyUpdates->count() }}</p>
                        </div>

                        <div class="p-4 rounded-lg bg-gray-50 dark:bg-gray-700">
                            <p class="text-sm text-gray-500 dark:text-gray-300">{{ __('parent.attendance_records') }}</p>
                            <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $attendanceRecords->count() }}</p>
                        </div>

                        <div class="p-4 rounded-lg bg-gray-50 dark:bg-gray-700">
                            <p class="text-sm text-gray-500 dark:text-gray-300">{{ __('parent.daily_reports') }}</p>
                            <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $dailyReports->count() }}</p>
                        </div>

                        <div class="p-4 rounded-lg bg-gray-50 dark:bg-gray-700">
                            <p class="text-sm text-gray-500 dark:text-gray-300">{{ __('parent.medication_logs') }}</p>
                            <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $medicationLogs->count() }}</p>
                        </div>
                    </div>
                </div>
            @endif

            @if ($activeTab === 'updates')
                <div class="bg-white dark:bg-gray-800 shadow sm:rounded-lg p-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">
                        {{ __('parent.daily_updates') }}
                    </h3>

                    @if ($dailyUpdates->isEmpty())
                        <p class="text-gray-600 dark:text-gray-300">{{ __('parent.no_daily_updates') }}</p>
                    @else
                        <div class="overflow-x-auto">
                            <table class="min-w-full text-sm text-gray-900 dark:text-gray-100">
                                <thead>
                                    <tr class="border-b dark:border-gray-700 align-top text-gray-800 dark:text-gray-100">
                                        <th class="py-3 pr-4">{{ __('parent.date') }}</th>
                                        <th class="py-3 pr-4">{{ __('parent.meals') }}</th>
                                        <th class="py-3 pr-4">{{ __('parent.sleep') }}</th>
                                        <th class="py-3 pr-4">{{ __('parent.notes') }}</th>
                                        <th class="py-3">{{ __('parent.created_by') }}</th>
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
                       {{ __('parent.attendance_history') }} 
                    </h3>

                    @if ($attendanceRecords->isEmpty())
                        <p class="text-gray-600 dark:text-gray-300">{{ __('parent.no_attendance') }}</p>
                    @else
                        <div class="overflow-x-auto">
                            <table class="min-w-full text-sm text-gray-900 dark:text-gray-100">
                                <thead>
                                    <tr class="border-b dark:border-gray-700 align-top text-gray-800 dark:text-gray-100">
                                        <th class="py-3 pr-4">{{ __('parent.date') }}</th>
                                        <th class="py-3 pr-4">{{ __('parent.status') }}</th>
                                        <th class="py-3 pr-4">{{ __('parent.check_in') }}</th>
                                        <th class="py-3 pr-4">{{ __('parent.check_out') }}</th>
                                        <th class="py-3 pr-4">{{ __('parent.room') }}</th>
                                        <th class="py-3">{{ __('parent.recorded_by') }}</th>
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
                        {{ __('parent.daily_reports') }}
                    </h3>

                    @if ($dailyReports->isEmpty())
                        <p class="text-gray-600 dark:text-gray-300">{{ __('parent.no_reports') }}</p>
                    @else
                        <div class="space-y-4">
                            @foreach ($dailyReports as $report)
    <div class="border dark:border-gray-700 rounded-lg p-4">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-2 mb-3">
            <p class="font-semibold text-gray-900 dark:text-gray-100">
                {{ $report->date ? \Carbon\Carbon::parse($report->date)->format('d M Y') : __('parent.no_date') }}'
            </p>
            <p class="text-sm text-gray-600 dark:text-gray-400">
                {{ __('parent.carer') }}: {{ $report->carer?->name ?: '—' }} |
                {{ __('parent.media_items') }}: {{ $report->mediaUpdates->count() }}
            </p>
        </div>

        <p class="text-gray-700 dark:text-gray-300 whitespace-pre-line">
            {{ $report->daily_report ?:  __('parent.no_written_report') }}' 
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
                                 alt="{{ __('parent.daily_report_media') }}"
                                 class="w-full h-auto rounded-lg object-cover">
                        @elseif ($isVideo)
                            <video controls class="w-full rounded-lg">
                                <source src="{{ $url }}">
                                {{ __('parent.video_not_supported') }}
                            </video>
                        @else
                            <a href="{{ $url }}"
                               target="_blank"
                               class="text-blue-600 dark:text-blue-400 hover:underline">
                                {{ __('parent.view_attachment') }}
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
                        {{ __('parent.medication_logs') }}
                    </h3>

                    @if ($medicationLogs->isEmpty())
                        <p class="text-gray-600 dark:text-gray-300">{{ __('parent.no_medication') }}</p>
                    @else
                        <div class="overflow-x-auto">
                            <table class="min-w-full text-sm text-gray-900 dark:text-gray-100">
                                <thead>
                                    <tr class="border-b dark:border-gray-700 align-top text-gray-800 dark:text-gray-100">
                                        <th class="py-3 pr-4">{{ __('parent.date') }}</th>
                                        <th class="py-3 pr-4">{{ __('parent.time') }}</th>
                                        <th class="py-3 pr-4">{{ __('parent.medication_label') }}</th>
                                        <th class="py-3 pr-4">{{ __('parent.dosage') }}</th>
                                        <th class="py-3 pr-4">{{ __('parent.notes') }}</th>
                                        <th class="py-3">{{ __('parent.carer') }}</th>
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