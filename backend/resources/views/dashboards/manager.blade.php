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

            {{-- Quick actions --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

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
                </div>
                   {{-- Daily Reports Summary --}}
                <div class="rounded-2xl border border-slate-200 bg-white/5 shadow-sm backdrop-blur
                        dark:border-slate-800 dark:bg-slate-950/40">
                    <div class="p-6">
                        <h3 class="text-xl font-semibold text-white">Daily Reports</h3>
                        <p class="mt-2 text-sm text-slate-300">
                            View each child’s end-of-day report and any uploaded photos/videos.
                        </p>

                        <a href="{{ route('manager.reports.daily-reports.index') }}"
                            class="mt-5 inline-flex items-center justify-center rounded-xl bg-blue-600 px-5 py-2.5 text-sm font-semibold text-white hover:bg-blue-700">
                            View Daily Reports
                        </a>
                    </div>

            </div>

        </div>
    </div>
</x-app-layout>