<x-app-layout>
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

            {{-- Attendance KPIs --}}
            <div class="rounded-2xl border border-slate-200 bg-white shadow-sm
                        dark:border-slate-800 dark:bg-slate-950/40 overflow-hidden">
                <div class="p-6">
                    <div class="flex items-start justify-between gap-4">
                        <div>
                            <h3 class="text-lg font-semibold text-slate-900 dark:text-slate-100">
                                Attendance KPIs
                            </h3>
                            <p class="text-sm text-slate-600 dark:text-slate-300 mt-1">
                                Range: {{ $kpi['rangeLabel'] ?? 'Last 7 days' }}
                            </p>
                        </div>

                        <span class="inline-flex items-center rounded-full px-3 py-1 text-xs font-medium
                                     bg-blue-50 text-blue-700 border border-blue-200
                                     dark:bg-blue-950/40 dark:text-blue-200 dark:border-blue-900/60">
                            Last 7 Days
                        </span>
                    </div>

                    <div class="mt-5 grid grid-cols-1 sm:grid-cols-3 gap-4">
                        <div class="rounded-xl border border-slate-200 p-4 dark:border-slate-800">
                            <p class="text-sm text-slate-600 dark:text-slate-300">Present</p>
                            <p class="mt-1 text-2xl font-semibold text-slate-900 dark:text-slate-100">
                                {{ $kpi['present'] ?? 0 }}
                            </p>
                        </div>

                        <div class="rounded-xl border border-slate-200 p-4 dark:border-slate-800">
                            <p class="text-sm text-slate-600 dark:text-slate-300">Absent</p>
                            <p class="mt-1 text-2xl font-semibold text-slate-900 dark:text-slate-100">
                                {{ $kpi['absent'] ?? 0 }}
                            </p>
                        </div>

                        <div class="rounded-xl border border-slate-200 p-4 dark:border-slate-800">
                            <p class="text-sm text-slate-600 dark:text-slate-300">Attendance Rate</p>
                            <p class="mt-1 text-2xl font-semibold text-slate-900 dark:text-slate-100">
                                {{ $kpi['rate'] ?? 0 }}%
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Attendance Trend Chart (Task 2) --}}
            <div class="rounded-2xl border border-slate-200 bg-white shadow-sm
                        dark:border-slate-800 dark:bg-slate-950/40 overflow-hidden">
                <div class="p-6">
                    <div class="flex items-start justify-between gap-4">
                        <div>
                            <h3 class="text-lg font-semibold text-slate-900 dark:text-slate-100">
                                Attendance Trend
                            </h3>
                            <p class="text-sm text-slate-600 dark:text-slate-300 mt-1">
                                Present vs Absent counts per day (Last 7 Days)
                            </p>
                        </div>
                    </div>

                    <div class="mt-5 h-72">
                        <canvas id="attendanceTrendChart"></canvas>
                    </div>

                    <p class="mt-3 text-xs text-slate-500 dark:text-slate-400">
                        If the chart is empty, it usually means there are no attendance records in the selected period.
                    </p>
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

            </div>

        </div>
    </div>

    {{-- Chart.js + Trend chart script --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        (function () {
            const labels = @json($attendanceChart['labels'] ?? []);
            const presentData = @json($attendanceChart['present'] ?? []);
            const absentData = @json($attendanceChart['absent'] ?? []);

            const el = document.getElementById('attendanceTrendChart');
            if (!el) return;

            // Prevent double init
            if (el.dataset.chartInit === '1') return;
            el.dataset.chartInit = '1';

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
                    plugins: { legend: { display: true } },
                    scales: {
                        y: { beginAtZero: true, ticks: { precision: 0 } }
                    }
                }
            });
        })();
    </script>
</x-app-layout>