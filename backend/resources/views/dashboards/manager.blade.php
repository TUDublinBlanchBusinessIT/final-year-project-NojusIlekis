<x-app-layout>
    @php
        $pendingPayments = $pendingPayments ?? collect([]);
        $pendingPaymentCount = $pendingPaymentCount ?? 0;
        $totalIncidents = $totalIncidents ?? 0;
        $openIncidents = $openIncidents ?? 0;
        $reviewedIncidents = $reviewedIncidents ?? 0;
        $closedIncidents = $closedIncidents ?? 0;
        $highSeverity = $highSeverity ?? 0;
        $mediumSeverity = $mediumSeverity ?? 0;
        $lowSeverity = $lowSeverity ?? 0;
        $incidentTrend = $incidentTrend ?? [];
        $overdueIncidents = $overdueIncidents ?? collect([]);
        $filters = $filters ?? [
            'start_date' => now()->subDays(6)->toDateString(),
            'end_date' => now()->toDateString(),
            'room_id' => null,
        ];

        $kpiOverall = $kpiOverall ?? ($kpi ?? [
            'rangeLabel' => __('manager.last_7_days'),
            'present' => 0,
            'absent' => 0,
            'rate' => 0,
        ]);

        $rooms = $rooms ?? collect([]);
        $kpiRoom = $kpiRoom ?? null;
        $kpiPerRoom = $kpiPerRoom ?? collect([]);

        $trendPresent = $attendanceChart['present'] ?? [];
        $trendAbsent  = $attendanceChart['absent'] ?? [];
        $hasTrendData = collect($trendPresent)->sum() > 0 || collect($trendAbsent)->sum() > 0;
    @endphp

    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-semibold text-2xl text-slate-900 dark:text-slate-100 leading-tight">
                    {{ __('manager.dashboard_title') }}
                </h2>
                <p class="text-sm text-slate-600 dark:text-slate-300 mt-1">
                    {{ __('manager.dashboard_description') }}
                </p>
            </div>

            <span class="inline-flex items-center gap-2 rounded-full px-3 py-1 text-xs font-medium
                         bg-indigo-50 text-indigo-700 border border-indigo-200
                         dark:bg-indigo-950/40 dark:text-indigo-200 dark:border-indigo-900/60">
                {{ __('manager.role') }}: {{ auth()->user()->role }}
            </span>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8 space-y-6">

            {{-- Welcome card --}}
            <div class="rounded-2xl border border-slate-200 bg-white shadow-sm
                        dark:border-slate-800 dark:bg-slate-950/40 overflow-hidden">
                <div class="p-6">
                    <p class="text-slate-700 dark:text-slate-200">
                        {{ __('manager.welcome') }}, <span class="font-semibold">{{ auth()->user()->name }}</span>.
                    </p>
                    <p class="text-sm text-slate-600 dark:text-slate-300 mt-1">
                        {{ __('manager.dashboard_hint') }}
                    </p>
                </div>
            </div>

            {{-- Pending Payments Card --}}
            @if($pendingPaymentCount > 0)
                <div class="bg-amber-50 rounded-2xl border border-amber-200 shadow-sm p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-semibold text-amber-800">
                            💳 {{ __('manager.pending_payments') }}
                            <span class="ml-2 inline-flex items-center justify-center w-6 h-6 bg-amber-500 text-white text-xs font-bold rounded-full">
                                {{ $pendingPaymentCount }}
                            </span>
                        </h3>
                        <a href="{{ route('manager.invoices.index') }}" class="text-sm text-amber-700 hover:underline">{{ __('manager.view_all_invoices') }} →</a>
                    </div>

                    <div class="space-y-3">
                        @foreach($pendingPayments as $payment)
                            <a href="{{ route('manager.invoices.show', $payment) }}"
                               class="flex items-center justify-between bg-white rounded-xl p-4 border border-amber-100 hover:border-amber-300 transition">
                                <div>
                                    <p class="font-medium text-gray-800">
                                        {{ $payment->parent->first_name ?? $payment->parent->name ?? __('manager.parent') }} {{ $payment->parent->last_name ?? '' }}
                                    </p>
                                    <p class="text-sm text-gray-500">
                                        {{ $payment->child->first_name ?? '' }} {{ $payment->child->last_name ?? '' }} —
                                        {{ __('manager.invoice') }} #{{ $payment->id }}
                                    </p>
                                </div>
                                <div class="text-right">
                                    <p class="font-semibold text-gray-800">€{{ number_format($payment->total, 2) }}</p>
                                    <p class="text-xs text-gray-400">{{ $payment->payment_submitted_at->diffForHumans() }}</p>
                                </div>
                            </a>
                        @endforeach
                    </div>
                </div>
            @endif

            {{-- Overdue Incidents Alert --}}
            @if($overdueIncidents->count() > 0)
                <div class="bg-red-50 rounded-2xl border-2 border-red-300 shadow-sm p-6">
                    <div class="flex items-center gap-3 mb-4">
                        <div class="w-10 h-10 bg-red-500 rounded-full flex items-center justify-center text-white text-xl">⚠️</div>
                        <div>
                            <h3 class="text-lg font-bold text-red-800">{{ $overdueIncidents->count() }} Overdue Incident{{ $overdueIncidents->count() > 1 ? 's' : '' }}</h3>
                            <p class="text-sm text-red-600">These incidents have been open for 10+ days and need immediate attention.</p>
                        </div>
                    </div>

                    <div class="space-y-3">
                        @foreach($overdueIncidents as $incident)
                            <a href="{{ route('manager.reports.incidents') }}"
                               class="flex items-center justify-between bg-white rounded-xl p-4 border {{ $incident->daysOpen() >= 14 ? 'border-red-400' : 'border-red-200' }} hover:border-red-400 transition">
                                <div>
                                    <p class="font-semibold text-gray-800">{{ $incident->title }}</p>
                                    <p class="text-sm text-gray-500">
                                        {{ $incident->child->first_name ?? '' }} {{ $incident->child->last_name ?? '' }} —
                                        {{ ucfirst($incident->severity) }} severity
                                    </p>
                                </div>
                                <div class="text-right">
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-bold {{ $incident->daysOpen() >= 14 ? 'bg-red-200 text-red-900' : 'bg-amber-100 text-amber-800' }}">
                                        {{ $incident->daysOpen() }} days open
                                    </span>
                                    <p class="text-xs text-gray-400 mt-1">{{ \Carbon\Carbon::parse($incident->incident_date)->format('d M Y') }}</p>
                                </div>
                            </a>
                        @endforeach
                    </div>
                </div>
            @endif

            {{-- Incident Statistics --}}
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">🚨 Incident Overview</h3>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                    <div class="text-center p-3 bg-red-50 rounded-xl">
                        <p class="text-2xl font-bold text-red-600">{{ $openIncidents }}</p>
                        <p class="text-xs text-gray-500 mt-1">Open</p>
                    </div>
                    <div class="text-center p-3 bg-amber-50 rounded-xl">
                        <p class="text-2xl font-bold text-amber-600">{{ $reviewedIncidents }}</p>
                        <p class="text-xs text-gray-500 mt-1">Under Review</p>
                    </div>
                    <div class="text-center p-3 bg-green-50 rounded-xl">
                        <p class="text-2xl font-bold text-green-600">{{ $closedIncidents }}</p>
                        <p class="text-xs text-gray-500 mt-1">Closed</p>
                    </div>
                    <div class="text-center p-3 bg-gray-50 rounded-xl">
                        <p class="text-2xl font-bold text-gray-700">{{ $totalIncidents }}</p>
                        <p class="text-xs text-gray-500 mt-1">Total</p>
                    </div>
                </div>

                @if($highSeverity > 0 || $mediumSeverity > 0)
                    <div class="mt-4 pt-4 border-t border-slate-100">
                        <p class="text-sm font-medium text-gray-600 mb-2">Open by Severity:</p>
                        <div class="flex gap-3 flex-wrap">
                            @if($highSeverity > 0)
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-semibold bg-red-100 text-red-800">
                                    🔴 {{ $highSeverity }} High
                                </span>
                            @endif
                            @if($mediumSeverity > 0)
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-semibold bg-amber-100 text-amber-800">
                                    🟡 {{ $mediumSeverity }} Medium
                                </span>
                            @endif
                            @if($lowSeverity > 0)
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-semibold bg-green-100 text-green-800">
                                    🟢 {{ $lowSeverity }} Low
                                </span>
                            @endif
                        </div>
                    </div>
                @endif
            </div>

            {{-- Attendance KPIs + Filters --}}
            <div class="rounded-2xl border border-slate-200 bg-white shadow-sm
                        dark:border-slate-800 dark:bg-slate-950/40 overflow-hidden">
                <div class="p-6">
                    <div class="flex flex-col gap-4">
                        <div class="flex items-start justify-between gap-4">
                            <div>
                                <h3 class="text-lg font-semibold text-slate-900 dark:text-slate-100">
                                    {{ __('manager.attendance_kpis') }}
                                </h3>
                                <p class="text-sm text-slate-600 dark:text-slate-300 mt-1">
                                    {{ __('manager.range') }}: {{ $kpiOverall['rangeLabel'] ?? __('manager.last_7_days') }}
                                </p>
                            </div>

                            <a href="{{ route('manager.dashboard') }}"
                               class="inline-flex items-center rounded-full px-3 py-1 text-xs font-medium
                                      bg-slate-50 text-slate-700 border border-slate-200
                                      hover:bg-slate-100
                                      dark:bg-slate-900/40 dark:text-slate-200 dark:border-slate-700/60">
                                {{ __('manager.clear') }}
                            </a>
                        </div>

                        <form id="kpiFilterForm" method="GET" action="{{ route('manager.dashboard') }}"
                              class="grid grid-cols-1 md:grid-cols-4 gap-3">
                            <div>
                                <label class="text-xs font-medium text-slate-600 dark:text-slate-300">{{ __('manager.start_date') }}</label>
                                <input type="date" name="start_date" value="{{ $filters['start_date'] ?? '' }}"
                                    class="mt-1 w-full rounded-xl border-slate-200 text-slate-900 dark:border-slate-800 dark:bg-slate-950/40 dark:text-white [color-scheme:dark]" />
                            </div>

                            <div>
                                <label class="text-xs font-medium text-slate-600 dark:text-slate-300">{{ __('manager.end_date') }}</label>
                                <input type="date" name="end_date" value="{{ $filters['end_date'] ?? '' }}"
                                        class="mt-1 w-full rounded-xl border-slate-200 text-slate-900 dark:border-slate-800 dark:bg-slate-950/40 dark:text-white [color-scheme:dark]" />
                            </div>

                            <div>
                                <label class="text-xs font-medium text-slate-600 dark:text-slate-300">{{ __('manager.room') }}</label>
                                <select name="room_id"
                                        class="mt-1 w-full rounded-xl border-slate-200 text-slate-900 dark:border-slate-800 dark:bg-slate-950/40 dark:text-white dark:[color-scheme:dark]">
                                    <option value="">{{ __('manager.all_rooms') }}</option>
                                    @foreach($rooms as $room)
                                        <option value="{{ $room->id }}"
                                            @selected(($filters['room_id'] ?? null) == $room->id)>
                                            {{ $room->name ?? ('Room #' . $room->id) }}
                                        </option>
                                    @endforeach
                            </select>
                            </div>

                            <div class="flex items-end">
                                <button id="applyFiltersBtn" type="submit"
                                        class="w-full inline-flex items-center justify-center gap-2 rounded-xl px-4 py-2 font-semibold text-white
                                               bg-gradient-to-r from-blue-600 via-blue-700 to-indigo-700
                                               shadow-sm shadow-blue-500/20
                                               hover:shadow-md hover:shadow-blue-500/30 hover:brightness-110
                                               focus:outline-none focus:ring-4 focus:ring-blue-200
                                               active:translate-y-[1px]
                                               dark:shadow-blue-900/30 dark:focus:ring-blue-900/40">
                                    {{ __('manager.apply') }}
                                </button>
                            </div>
                        </form>
                    </div>

                    <div class="mt-5">
                        <p class="text-sm font-semibold text-slate-800 dark:text-slate-200 mb-3">
                            {{ __('manager.overall_all_rooms') }}
                        </p>

                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                            <div class="rounded-xl border border-slate-200 p-4 dark:border-slate-800">
                                <p class="text-sm text-slate-600 dark:text-slate-300">{{ __('manager.present') }}</p>
                                <p class="mt-1 text-2xl font-semibold text-slate-900 dark:text-slate-100">
                                    {{ $kpiOverall['present'] ?? 0 }}
                                </p>
                            </div>

                            <div class="rounded-xl border border-slate-200 p-4 dark:border-slate-800">
                                <p class="text-sm text-slate-600 dark:text-slate-300">{{ __('manager.absent') }}</p>
                                <p class="mt-1 text-2xl font-semibold text-slate-900 dark:text-slate-100">
                                    {{ $kpiOverall['absent'] ?? 0 }}
                                </p>
                            </div>

                            <div class="rounded-xl border border-slate-200 p-4 dark:border-slate-800">
                                <p class="text-sm text-slate-600 dark:text-slate-300">{{ __('manager.attendance_rate') }}</p>
                                <p class="mt-1 text-2xl font-semibold text-slate-900 dark:text-slate-100">
                                    {{ $kpiOverall['rate'] ?? 0 }}%
                                </p>
                            </div>
                        </div>
                    </div>

                    @if(!empty($kpiRoom))
                        <div class="mt-6">
                            <p class="text-sm font-semibold text-slate-800 dark:text-slate-200 mb-3">
                                {{ __('manager.selected_room') }}: {{ $kpiRoom['room_name'] ?? ('Room #' . ($kpiRoom['room_id'] ?? '')) }}
                            </p>

                            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                                <div class="rounded-xl border border-slate-200 p-4 dark:border-slate-800">
                                    <p class="text-sm text-slate-600 dark:text-slate-300">{{ __('manager.present') }}</p>
                                    <p class="mt-1 text-2xl font-semibold text-slate-900 dark:text-slate-100">
                                        {{ $kpiRoom['present'] ?? 0 }}
                                    </p>
                                </div>

                                <div class="rounded-xl border border-slate-200 p-4 dark:border-slate-800">
                                    <p class="text-sm text-slate-600 dark:text-slate-300">{{ __('manager.absent') }}</p>
                                    <p class="mt-1 text-2xl font-semibold text-slate-900 dark:text-slate-100">
                                        {{ $kpiRoom['absent'] ?? 0 }}
                                    </p>
                                </div>

                                <div class="rounded-xl border border-slate-200 p-4 dark:border-slate-800">
                                    <p class="text-sm text-slate-600 dark:text-slate-300">{{ __('manager.attendance_rate') }}</p>
                                    <p class="mt-1 text-2xl font-semibold text-slate-900 dark:text-slate-100">
                                        {{ $kpiRoom['rate'] ?? 0 }}%
                                    </p>
                                </div>
                            </div>
                        </div>
                    @endif

                    <div class="mt-7">
                        <p class="text-sm font-semibold text-slate-800 dark:text-slate-200 mb-3">
                            {{ __('manager.room_breakdown') }}
                        </p>

                        <div class="overflow-x-auto rounded-xl border border-slate-200 dark:border-slate-800">
                            <table class="min-w-full text-sm">
                                <thead class="bg-slate-50 dark:bg-slate-900/40">
                                    <tr class="text-left">
                                        <th class="px-4 py-3 font-semibold text-slate-700 dark:text-slate-200">{{ __('manager.room') }}</th>
                                        <th class="px-4 py-3 font-semibold text-slate-700 dark:text-slate-200">{{ __('manager.present') }}</th>
                                        <th class="px-4 py-3 font-semibold text-slate-700 dark:text-slate-200">{{ __('manager.absent') }}</th>
                                        <th class="px-4 py-3 font-semibold text-slate-700 dark:text-slate-200">{{ __('manager.rate') }}</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-200 dark:divide-slate-800">
                                    @forelse($kpiPerRoom as $row)
                                        <tr>
                                            <td class="px-4 py-3 text-slate-800 dark:text-slate-200">
                                                {{ $row['room_name'] ?? ('Room #' . ($row['room_id'] ?? '')) }}
                                            </td>
                                            <td class="px-4 py-3 text-slate-800 dark:text-slate-200">
                                                {{ $row['present'] ?? 0 }}
                                            </td>
                                            <td class="px-4 py-3 text-slate-800 dark:text-slate-200">
                                                {{ $row['absent'] ?? 0 }}
                                            </td>
                                            <td class="px-4 py-3 text-slate-800 dark:text-slate-200">
                                                {{ $row['rate'] ?? 0 }}%
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" class="px-4 py-4 text-slate-600 dark:text-slate-300">
                                                {{ __('manager.no_room_data') }}
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

                </div>
            </div>

            {{-- Attendance Trend Chart --}}
            <div class="rounded-2xl border border-slate-200 bg-white shadow-sm
                        dark:border-slate-800 dark:bg-slate-950/40 overflow-hidden">
                <div class="p-6">
                    <div class="flex items-start justify-between gap-4">
                        <div>
                            <h3 class="text-lg font-semibold text-slate-900 dark:text-slate-100">
                                {{ __('manager.attendance_trend') }}
                            </h3>
                            <p class="text-sm text-slate-600 dark:text-slate-300 mt-1">
                                {{ !empty($filters['room_id']) ? __('manager.filtered_room') : __('manager.all_rooms') }}
                                ({{ __('manager.present') }} vs {{ __('manager.absent') }} {{ __('manager.per_day') }})
                            </p>
                        </div>
                    </div>

                    <div class="mt-5 rounded-xl border border-slate-200 bg-white p-4 h-64 sm:h-72
                                dark:border-slate-800 dark:bg-slate-950/20">
                        <canvas id="attendanceTrendChart"></canvas>
                    </div>

                    @if(!$hasTrendData)
                        <div class="mt-4 rounded-xl border border-slate-200 bg-slate-50 p-4 text-sm text-slate-700
                                    dark:border-slate-800 dark:bg-slate-900/40 dark:text-slate-200">
                            {{ __('manager.no_attendance_records') }}{{ !empty($filters['room_id']) ? ' ' . __('manager.in_selected_room') : '' }}.
                            {{ __('manager.try_different_range') }}{{ empty($filters['room_id']) ? '' : ' ' . __('manager.or_select_all_rooms') }}.
                        </div>
                    @else
                        <p class="mt-3 text-xs text-slate-500 dark:text-slate-400">
                            {{ __('manager.filter_tip') }}
                        </p>
                    @endif
                </div>
            </div>

            {{-- Quick actions --}}
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

                {{-- Attendance Summary --}}
                <div class="rounded-2xl border border-slate-200 bg-white shadow-sm
                            dark:border-slate-800 dark:bg-slate-950/40 overflow-hidden">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold text-slate-900 dark:text-slate-100">
                            {{ __('manager.attendance_summary') }}
                        </h3>
                        <p class="text-sm text-slate-600 dark:text-slate-300 mt-1">
                            {{ __('manager.attendance_summary_desc') }}
                        </p>

                        <a href="{{ route('manager.reports.attendance') }}"
                           class="mt-4 inline-flex items-center justify-center gap-2 rounded-xl px-4 py-2 font-semibold text-white
                                  bg-gradient-to-r from-blue-600 via-blue-700 to-indigo-700
                                  shadow-sm shadow-blue-500/20
                                  hover:shadow-md hover:shadow-blue-500/30 hover:brightness-110
                                  focus:outline-none focus:ring-4 focus:ring-blue-200
                                  active:translate-y-[1px]
                                  dark:shadow-blue-900/30 dark:focus:ring-blue-900/40">
                            {{ __('manager.view_attendance') }}
                        </a>
                    </div>
                </div>

                {{-- Tasks Summary (Daily Updates) --}}
                <div class="rounded-2xl border border-slate-200 bg-white shadow-sm
                            dark:border-slate-800 dark:bg-slate-950/40 overflow-hidden">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold text-slate-900 dark:text-slate-100">
                            {{ __('manager.tasks_summary') }}
                        </h3>
                        <p class="text-sm text-slate-600 dark:text-slate-300 mt-1">
                            {{ __('manager.tasks_summary_desc') }}
                        </p>

                        <a href="{{ route('manager.reports.tasks') }}"
                           class="mt-4 inline-flex items-center justify-center gap-2 rounded-xl px-4 py-2 font-semibold text-white
                                  bg-gradient-to-r from-blue-600 via-blue-700 to-indigo-700
                                  shadow-sm shadow-blue-500/20
                                  hover:shadow-md hover:shadow-blue-500/30 hover:brightness-110
                                  focus:outline-none focus:ring-4 focus:ring-blue-200
                                  active:translate-y-[1px]
                                  dark:shadow-blue-900/30 dark:focus:ring-blue-900/40">
                            {{ __('manager.view_tasks') }}
                        </a>
                    </div>
                </div>

                {{-- Daily Reports Summary --}}
                <div class="rounded-2xl border border-slate-200 bg-white shadow-sm
                            dark:border-slate-800 dark:bg-slate-950/40 overflow-hidden">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold text-slate-900 dark:text-slate-100">
                            {{ __('manager.daily_reports') }}
                        </h3>
                        <p class="text-sm text-slate-600 dark:text-slate-300 mt-1">
                            {{ __('manager.daily_reports_desc') }}
                        </p>

                        <a href="{{ route('manager.reports.daily-reports.index') }}"
                           class="mt-4 inline-flex items-center justify-center gap-2 rounded-xl px-4 py-2 font-semibold text-white
                                  bg-gradient-to-r from-blue-600 via-blue-700 to-indigo-700
                                  shadow-sm shadow-blue-500/20
                                  hover:shadow-md hover:shadow-blue-500/30 hover:brightness-110
                                  focus:outline-none focus:ring-4 focus:ring-blue-200
                                  active:translate-y-[1px]
                                  dark:shadow-blue-900/30 dark:focus:ring-blue-900/40">
                            {{ __('manager.view_daily_reports') }}
                        </a>
                    </div>
                </div>

                {{-- Medication Logs --}}
                <div class="rounded-2xl border border-slate-200 bg-white shadow-sm
                            dark:border-slate-800 dark:bg-slate-950/40 overflow-hidden">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold text-slate-900 dark:text-slate-100">
                            {{ __('manager.medication_logs') }}
                        </h3>
                        <p class="text-sm text-slate-600 dark:text-slate-300 mt-1">
                            {{ __('manager.medication_logs_desc') }}
                        </p>

                        <a href="{{ route('manager.reports.medication.index') }}"
                           class="mt-4 inline-flex items-center justify-center gap-2 rounded-xl px-4 py-2 font-semibold text-white
                                  bg-gradient-to-r from-blue-600 via-blue-700 to-indigo-700
                                  shadow-sm shadow-blue-500/20
                                  hover:shadow-md hover:shadow-blue-500/30 hover:brightness-110
                                  focus:outline-none focus:ring-4 focus:ring-blue-200
                                  active:translate-y-[1px]
                                  dark:shadow-blue-900/30 dark:focus:ring-blue-900/40">
                            {{ __('manager.view_medication') }}
                        </a>
                    </div>
                </div>

                {{-- Invoices --}}
                <div class="rounded-2xl border border-slate-200 bg-white shadow-sm
                            dark:border-slate-800 dark:bg-slate-950/40 overflow-hidden">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold text-slate-900 dark:text-slate-100">
                            {{ __('manager.invoices') }}
                        </h3>
                        <p class="text-sm text-slate-600 dark:text-slate-300 mt-1">
                            {{ __('manager.invoices_desc') }}
                        </p>

                        <a href="{{ route('manager.invoices.index') }}"
                           class="mt-4 inline-flex items-center justify-center gap-2 rounded-xl px-4 py-2 font-semibold text-white
                                  bg-gradient-to-r from-blue-600 via-blue-700 to-indigo-700
                                  shadow-sm shadow-blue-500/20
                                  hover:shadow-md hover:shadow-blue-500/30 hover:brightness-110
                                  focus:outline-none focus:ring-4 focus:ring-blue-200
                                  active:translate-y-[1px]
                                  dark:shadow-blue-900/30 dark:focus:ring-blue-900/40">
                            {{ __('manager.view_invoices') }}
                        </a>
                    </div>
                </div>

                {{-- Incident Reports --}}
                <div class="rounded-2xl border border-slate-200 bg-white shadow-sm p-6
                            dark:border-slate-800 dark:bg-slate-950/40">
                    <h3 class="text-lg font-semibold text-slate-900 dark:text-slate-100 mb-2">
                        {{ __('manager.incident_reports') }}
                    </h3>

                    <p class="text-sm text-slate-600 dark:text-slate-300 mb-4">
                        {{ __('manager.incident_reports_desc') }}
                    </p>

                    <a href="{{ route('manager.reports.incidents') }}"
                       class="inline-flex items-center justify-center rounded-xl px-4 py-2 text-sm font-semibold text-white
                              bg-gradient-to-r from-red-600 to-red-700
                              hover:brightness-110 shadow-sm">
                        {{ __('manager.view_incidents') }}
                    </a>
                </div>

                {{-- Parent Enquiries --}}
                <div class="rounded-2xl border border-slate-200 bg-white shadow-sm
                            dark:border-slate-800 dark:bg-slate-950/40 overflow-hidden">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold text-slate-900 dark:text-slate-100">
                            {{ __('manager.parent_enquiries') }}
                        </h3>
                        <p class="text-sm text-slate-600 dark:text-slate-300 mt-1">
                            {{ __('manager.parent_enquiries_desc') }}
                        </p>

                        <a href="{{ route('manager.messages.index') }}"
                           class="mt-4 inline-flex items-center justify-center gap-2 rounded-xl px-4 py-2 font-semibold text-white
                                  bg-gradient-to-r from-blue-600 via-blue-700 to-indigo-700
                                  shadow-sm shadow-blue-500/20
                                  hover:shadow-md hover:shadow-blue-500/30 hover:brightness-110
                                  focus:outline-none focus:ring-4 focus:ring-blue-200
                                  active:translate-y-[1px]
                                  dark:shadow-blue-900/30 dark:focus:ring-blue-900/40">
                            {{ __('manager.view_enquiries') }}
                        </a>
                    </div>
                </div>

            </div>

        </div>
    </div>

    <script>
        (function () {
            const form = document.getElementById('kpiFilterForm');
            const btn = document.getElementById('applyFiltersBtn');
            if (!form || !btn) return;

            form.addEventListener('submit', function () {
                btn.disabled = true;
                btn.dataset.originalText = btn.innerText;
                btn.innerText = 'Loading...';
            });
        })();
    </script>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        (function () {
            const labels = @json($attendanceChart['labels'] ?? []);
            const presentData = @json($attendanceChart['present'] ?? []);
            const absentData = @json($attendanceChart['absent'] ?? []);
            const incidentMap = @json($incidentTrend);

            const el = document.getElementById('attendanceTrendChart');
            if (!el) return;

            if (el.dataset.chartInit === '1') return;
            el.dataset.chartInit = '1';

            const total = presentData.reduce((a,b) => a + b, 0) + absentData.reduce((a,b) => a + b, 0);
            if (labels.length === 0 || total === 0) return;

            const ctx = el.getContext('2d');

            const incidentData = labels.map(d => incidentMap[d] || 0);

            new Chart(ctx, {
                type: 'bar',
                data: {
                    labels,
                    datasets: [
                        {
                            label: 'Present',
                            data: presentData,
                            backgroundColor: 'rgba(34, 197, 94, 0.6)',
                            borderRadius: 4,
                            yAxisID: 'y',
                        },
                        {
                            label: 'Absent',
                            data: absentData,
                            backgroundColor: 'rgba(239, 68, 68, 0.6)',
                            borderRadius: 4,
                            yAxisID: 'y',
                        },
                        {
                            label: 'Incidents',
                            data: incidentData,
                            type: 'line',
                            borderColor: 'rgba(245, 158, 11, 1)',
                            backgroundColor: 'rgba(245, 158, 11, 0.1)',
                            fill: true,
                            tension: 0.3,
                            yAxisID: 'y1',
                            pointRadius: 4,
                        },
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    interaction: { mode: 'index', intersect: false },
                    plugins: {
                        legend: {
                            display: true,
                            position: 'top',
                            labels: { boxWidth: 10, boxHeight: 10 }
                        },
                        tooltip: { enabled: true }
                    },
                    scales: {
                        x: {
                            grid: { display: false },
                            ticks: { maxRotation: 0, autoSkip: true }
                        },
                        y: {
                            beginAtZero: true,
                            ticks: { precision: 0 },
                            title: { display: true, text: 'Children' },
                            grid: { drawBorder: false }
                        },
                        y1: {
                            beginAtZero: true,
                            position: 'right',
                            ticks: { precision: 0 },
                            title: { display: true, text: 'Incidents' },
                            grid: { drawOnChartArea: false }
                        }
                    }
                }
            });
        })();
    </script>
</x-app-layout>