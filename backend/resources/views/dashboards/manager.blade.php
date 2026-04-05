<x-app-layout>
    @php
        $filters = $filters ?? [
            'start_date' => now()->subDays(6)->toDateString(),
            'end_date' => now()->toDateString(),
            'room_id' => null,
        ];

        $kpiOverall = $kpiOverall ?? ($kpi ?? [
            'rangeLabel' => 'Last 7 days',
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
                    Manager Dashboard
                </h2>
                <p class="text-sm text-slate-600 dark:text-slate-300 mt-1">
                    View attendance and daily update summaries by room and date.
                </p>
            </div>

            <span class="inline-flex items-center gap-2 rounded-full px-3 py-1 text-xs font-medium
                         bg-indigo-50 text-indigo-700 border border-indigo-200
                         dark:bg-indigo-950/40 dark:text-indigo-200 dark:border-indigo-900/60">
                Role: {{ auth()->user()->role }}
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
                        Welcome, <span class="font-semibold">{{ auth()->user()->name }}</span>.
                    </p>
                    <p class="text-sm text-slate-600 dark:text-slate-300 mt-1">
                        Use the reports below to monitor attendance and daily updates.
                    </p>
                </div>
            </div>

            {{-- Attendance KPIs + Filters --}}
            <div class="rounded-2xl border border-slate-200 bg-white shadow-sm
                        dark:border-slate-800 dark:bg-slate-950/40 overflow-hidden">
                <div class="p-6">
                    <div class="flex flex-col gap-4">
                        <div class="flex items-start justify-between gap-4">
                            <div>
                                <h3 class="text-lg font-semibold text-slate-900 dark:text-slate-100">
                                    Attendance KPIs
                                </h3>
                                <p class="text-sm text-slate-600 dark:text-slate-300 mt-1">
                                    Range: {{ $kpiOverall['rangeLabel'] ?? 'Last 7 days' }}
                                </p>
                            </div>

                            <a href="{{ route('manager.dashboard') }}"
                               class="inline-flex items-center rounded-full px-3 py-1 text-xs font-medium
                                      bg-slate-50 text-slate-700 border border-slate-200
                                      hover:bg-slate-100
                                      dark:bg-slate-900/40 dark:text-slate-200 dark:border-slate-700/60">
                                Clear
                            </a>
                        </div>

                        <form id="kpiFilterForm" method="GET" action="{{ route('manager.dashboard') }}"
                              class="grid grid-cols-1 md:grid-cols-4 gap-3">
                            <div>
                                <label class="text-xs font-medium text-slate-600 dark:text-slate-300">Start date</label>
                                <input type="date" name="start_date" value="{{ $filters['start_date'] ?? '' }}"
                                       class="mt-1 w-full rounded-xl border-slate-200 dark:border-slate-800 dark:bg-slate-950/40" />
                            </div>

                            <div>
                                <label class="text-xs font-medium text-slate-600 dark:text-slate-300">End date</label>
                                <input type="date" name="end_date" value="{{ $filters['end_date'] ?? '' }}"
                                       class="mt-1 w-full rounded-xl border-slate-200 dark:border-slate-800 dark:bg-slate-950/40" />
                            </div>

                            <div>
                                <label class="text-xs font-medium text-slate-600 dark:text-slate-300">Room</label>
                                <select name="room_id"
                                        class="mt-1 w-full rounded-xl border-slate-200 dark:border-slate-800 dark:bg-slate-950/40">
                                    <option value="">All rooms</option>
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
                                    Apply
                                </button>
                            </div>
                        </form>
                    </div>

                    <div class="mt-5">
                        <p class="text-sm font-semibold text-slate-800 dark:text-slate-200 mb-3">
                            Overall (All Rooms)
                        </p>

                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                            <div class="rounded-xl border border-slate-200 p-4 dark:border-slate-800">
                                <p class="text-sm text-slate-600 dark:text-slate-300">Present</p>
                                <p class="mt-1 text-2xl font-semibold text-slate-900 dark:text-slate-100">
                                    {{ $kpiOverall['present'] ?? 0 }}
                                </p>
                            </div>

                            <div class="rounded-xl border border-slate-200 p-4 dark:border-slate-800">
                                <p class="text-sm text-slate-600 dark:text-slate-300">Absent</p>
                                <p class="mt-1 text-2xl font-semibold text-slate-900 dark:text-slate-100">
                                    {{ $kpiOverall['absent'] ?? 0 }}
                                </p>
                            </div>

                            <div class="rounded-xl border border-slate-200 p-4 dark:border-slate-800">
                                <p class="text-sm text-slate-600 dark:text-slate-300">Attendance Rate</p>
                                <p class="mt-1 text-2xl font-semibold text-slate-900 dark:text-slate-100">
                                    {{ $kpiOverall['rate'] ?? 0 }}%
                                </p>
                            </div>
                        </div>
                    </div>

                    @if(!empty($kpiRoom))
                        <div class="mt-6">
                            <p class="text-sm font-semibold text-slate-800 dark:text-slate-200 mb-3">
                                Selected Room: {{ $kpiRoom['room_name'] ?? ('Room #' . ($kpiRoom['room_id'] ?? '')) }}
                            </p>

                            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                                <div class="rounded-xl border border-slate-200 p-4 dark:border-slate-800">
                                    <p class="text-sm text-slate-600 dark:text-slate-300">Present</p>
                                    <p class="mt-1 text-2xl font-semibold text-slate-900 dark:text-slate-100">
                                        {{ $kpiRoom['present'] ?? 0 }}
                                    </p>
                                </div>

                                <div class="rounded-xl border border-slate-200 p-4 dark:border-slate-800">
                                    <p class="text-sm text-slate-600 dark:text-slate-300">Absent</p>
                                    <p class="mt-1 text-2xl font-semibold text-slate-900 dark:text-slate-100">
                                        {{ $kpiRoom['absent'] ?? 0 }}
                                    </p>
                                </div>

                                <div class="rounded-xl border border-slate-200 p-4 dark:border-slate-800">
                                    <p class="text-sm text-slate-600 dark:text-slate-300">Attendance Rate</p>
                                    <p class="mt-1 text-2xl font-semibold text-slate-900 dark:text-slate-100">
                                        {{ $kpiRoom['rate'] ?? 0 }}%
                                    </p>
                                </div>
                            </div>
                        </div>
                    @endif

                    <div class="mt-7">
                        <p class="text-sm font-semibold text-slate-800 dark:text-slate-200 mb-3">
                            Room Breakdown
                        </p>

                        <div class="overflow-x-auto rounded-xl border border-slate-200 dark:border-slate-800">
                            <table class="min-w-full text-sm">
                                <thead class="bg-slate-50 dark:bg-slate-900/40">
                                    <tr class="text-left">
                                        <th class="px-4 py-3 font-semibold text-slate-700 dark:text-slate-200">Room</th>
                                        <th class="px-4 py-3 font-semibold text-slate-700 dark:text-slate-200">Present</th>
                                        <th class="px-4 py-3 font-semibold text-slate-700 dark:text-slate-200">Absent</th>
                                        <th class="px-4 py-3 font-semibold text-slate-700 dark:text-slate-200">Rate</th>
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
                                                No room attendance found in this date range.
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
                                Attendance Trend
                            </h3>
                            <p class="text-sm text-slate-600 dark:text-slate-300 mt-1">
                                {{ !empty($filters['room_id']) ? 'Filtered by selected room' : 'All rooms' }}
                                (Present vs Absent per day)
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
                            No attendance records found for this range{{ !empty($filters['room_id']) ? ' in the selected room' : '' }}.
                            Try a different date range{{ empty($filters['room_id']) ? '' : ' or select All rooms' }}.
                        </div>
                    @else
                        <p class="mt-3 text-xs text-slate-500 dark:text-slate-400">
                            Tip: Use the filters above to compare rooms and date ranges.
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
                            Attendance Summary
                        </h3>
                        <p class="text-sm text-slate-600 dark:text-slate-300 mt-1">
                            See present/absent/not marked totals and a per-child breakdown.
                        </p>

                        <a href="{{ route('manager.reports.attendance') }}"
                           class="mt-4 inline-flex items-center justify-center gap-2 rounded-xl px-4 py-2 font-semibold text-white
                                  bg-gradient-to-r from-blue-600 via-blue-700 to-indigo-700
                                  shadow-sm shadow-blue-500/20
                                  hover:shadow-md hover:shadow-blue-500/30 hover:brightness-110
                                  focus:outline-none focus:ring-4 focus:ring-blue-200
                                  active:translate-y-[1px]
                                  dark:shadow-blue-900/30 dark:focus:ring-blue-900/40">
                            View Attendance
                        </a>
                    </div>
                </div>

                {{-- Tasks Summary (Daily Updates) --}}
                <div class="rounded-2xl border border-slate-200 bg-white shadow-sm
                            dark:border-slate-800 dark:bg-slate-950/40 overflow-hidden">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold text-slate-900 dark:text-slate-100">
                            Tasks Summary (Daily Updates)
                        </h3>
                        <p class="text-sm text-slate-600 dark:text-slate-300 mt-1">
                            Track daily update completion (meals/sleep/notes) by room and date.
                        </p>

                        <a href="{{ route('manager.reports.tasks') }}"
                           class="mt-4 inline-flex items-center justify-center gap-2 rounded-xl px-4 py-2 font-semibold text-white
                                  bg-gradient-to-r from-blue-600 via-blue-700 to-indigo-700
                                  shadow-sm shadow-blue-500/20
                                  hover:shadow-md hover:shadow-blue-500/30 hover:brightness-110
                                  focus:outline-none focus:ring-4 focus:ring-blue-200
                                  active:translate-y-[1px]
                                  dark:shadow-blue-900/30 dark:focus:ring-blue-900/40">
                            View Tasks
                        </a>
                    </div>
                </div>

                {{-- Daily Reports Summary --}}
                <div class="rounded-2xl border border-slate-200 bg-white shadow-sm
                            dark:border-slate-800 dark:bg-slate-950/40 overflow-hidden">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold text-slate-900 dark:text-slate-100">
                            Daily Reports
                        </h3>
                        <p class="text-sm text-slate-600 dark:text-slate-300 mt-1">
                            View each child’s end-of-day report and any uploaded photos/videos.
                        </p>

                        <a href="{{ route('manager.reports.daily-reports.index') }}"
                           class="mt-4 inline-flex items-center justify-center gap-2 rounded-xl px-4 py-2 font-semibold text-white
                                  bg-gradient-to-r from-blue-600 via-blue-700 to-indigo-700
                                  shadow-sm shadow-blue-500/20
                                  hover:shadow-md hover:shadow-blue-500/30 hover:brightness-110
                                  focus:outline-none focus:ring-4 focus:ring-blue-200
                                  active:translate-y-[1px]
                                  dark:shadow-blue-900/30 dark:focus:ring-blue-900/40">
                            View Daily Reports
                        </a>
                    </div>
                </div>

                {{-- Medication Logs --}}
                <div class="rounded-2xl border border-slate-200 bg-white shadow-sm
                            dark:border-slate-800 dark:bg-slate-950/40 overflow-hidden">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold text-slate-900 dark:text-slate-100">
                            Medication Logs
                        </h3>
                        <p class="text-sm text-slate-600 dark:text-slate-300 mt-1">
                            Track medication records logged by carers for each child.
                        </p>

                        <a href="{{ route('manager.reports.medication.index') }}"
                           class="mt-4 inline-flex items-center justify-center gap-2 rounded-xl px-4 py-2 font-semibold text-white
                                  bg-gradient-to-r from-blue-600 via-blue-700 to-indigo-700
                                  shadow-sm shadow-blue-500/20
                                  hover:shadow-md hover:shadow-blue-500/30 hover:brightness-110
                                  focus:outline-none focus:ring-4 focus:ring-blue-200
                                  active:translate-y-[1px]
                                  dark:shadow-blue-900/30 dark:focus:ring-blue-900/40">
                            View Medication Logs
                        </a>
                    </div>
                </div>

                {{-- Invoices --}}
                <div class="rounded-2xl border border-slate-200 bg-white shadow-sm
                            dark:border-slate-800 dark:bg-slate-950/40 overflow-hidden">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold text-slate-900 dark:text-slate-100">
                            Invoices
                        </h3>
                        <p class="text-sm text-slate-600 dark:text-slate-300 mt-1">
                            Create and manage monthly invoices for children and parents.
                        </p>

                        <a href="{{ route('manager.invoices.index') }}"
                           class="mt-4 inline-flex items-center justify-center gap-2 rounded-xl px-4 py-2 font-semibold text-white
                                  bg-gradient-to-r from-blue-600 via-blue-700 to-indigo-700
                                  shadow-sm shadow-blue-500/20
                                  hover:shadow-md hover:shadow-blue-500/30 hover:brightness-110
                                  focus:outline-none focus:ring-4 focus:ring-blue-200
                                  active:translate-y-[1px]
                                  dark:shadow-blue-900/30 dark:focus:ring-blue-900/40">
                            View Invoices
                        </a>
                    </div>
                </div>

                {{-- Incident Reports --}}
                <div class="rounded-2xl border border-slate-200 bg-white shadow-sm p-6
                            dark:border-slate-800 dark:bg-slate-950/40">
                    <h3 class="text-lg font-semibold text-slate-900 dark:text-slate-100 mb-2">
                        Incident Reports
                    </h3>

                    <p class="text-sm text-slate-600 dark:text-slate-300 mb-4">
                        Review incidents reported by carers and follow up with parents if required.
                    </p>

                    <a href="{{ route('manager.reports.incidents') }}"
                       class="inline-flex items-center justify-center rounded-xl px-4 py-2 text-sm font-semibold text-white
                              bg-gradient-to-r from-red-600 to-red-700
                              hover:brightness-110 shadow-sm">
                        View Incidents
                    </a>
                </div>

                {{-- Parent Enquiries --}}
                <div class="rounded-2xl border border-slate-200 bg-white shadow-sm
                            dark:border-slate-800 dark:bg-slate-950/40 overflow-hidden">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold text-slate-900 dark:text-slate-100">
                            Parent Enquiries
                        </h3>
                        <p class="text-sm text-slate-600 dark:text-slate-300 mt-1">
                            View and reply to fee and invoice enquiries sent by parents.
                        </p>

                        <a href="{{ route('manager.messages.index') }}"
                           class="mt-4 inline-flex items-center justify-center gap-2 rounded-xl px-4 py-2 font-semibold text-white
                                  bg-gradient-to-r from-blue-600 via-blue-700 to-indigo-700
                                  shadow-sm shadow-blue-500/20
                                  hover:shadow-md hover:shadow-blue-500/30 hover:brightness-110
                                  focus:outline-none focus:ring-4 focus:ring-blue-200
                                  active:translate-y-[1px]
                                  dark:shadow-blue-900/30 dark:focus:ring-blue-900/40">
                            View Enquiries
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

            const el = document.getElementById('attendanceTrendChart');
            if (!el) return;

            if (el.dataset.chartInit === '1') return;
            el.dataset.chartInit = '1';

            const total = presentData.reduce((a,b) => a + b, 0) + absentData.reduce((a,b) => a + b, 0);
            if (labels.length === 0 || total === 0) return;

            const ctx = el.getContext('2d');

            new Chart(ctx, {
                type: 'line',
                data: {
                    labels,
                    datasets: [
                        { label: 'Present', data: presentData, tension: 0.35 },
                        { label: 'Absent', data: absentData, tension: 0.35 },
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
                            labels: {
                                boxWidth: 10,
                                boxHeight: 10,
                            }
                        },
                        tooltip: {
                            enabled: true
                        }
                    },
                    scales: {
                        x: {
                            grid: { display: false },
                            ticks: { maxRotation: 0, autoSkip: true }
                        },
                        y: {
                            beginAtZero: true,
                            ticks: { precision: 0 },
                            grid: { drawBorder: false }
                        }
                    }
                }
            });
        })();
    </script>
</x-app-layout>