<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-4">
            <img src="{{ asset('images/image.png') }}"
                 alt="SnugBug Logo"
                 class="h-12 w-12 object-contain">

            <div>
                <h2 class="text-2xl font-bold text-slate-800 text-white">
                    SnugBug
                </h2>
                <p class="text-sm text-slate-500">
                    Snug updates for Bug size humans.
                </p>
            </div>
        </div>
    </x-slot>

    <div class="py-10 px-6 max-w-6xl mx-auto">

        <!-- Welcome Section -->
        <div class="mb-10">
            <h3 class="text-3xl font-bold text-slate-800 text-white">
                Welcome back, {{ auth()->user()->name }} 👋
            </h3>
            <p class="text-slate-500 mt-2">
                Here's a quick access panel to manage today’s activities.
            </p>
        </div>

        <!-- Dashboard Cards -->
        <div class="grid md:grid-cols-2 gap-8">

            <!-- Attendance Card -->
            <div class="bg-white rounded-2xl shadow-md hover:shadow-xl transition p-8 border border-slate-100">
                <h4 class="text-xl font-semibold text-slate-800 mb-3">
                    Attendance
                </h4>

                <p class="text-slate-500 mb-6">
                    Track and manage children's attendance for the day.
                </p>

                <a href="{{ route('carer.attendance.index') }}"
                   class="inline-block bg-blue-600 text-white px-5 py-2 rounded-xl font-medium hover:bg-blue-700 transition">
                    Open Attendance
                </a>
            </div>

            <!-- Daily Reports Card -->
            <div class="bg-white rounded-2xl shadow-md hover:shadow-xl transition p-8 border border-slate-100">
                <h4 class="text-xl font-semibold text-slate-800 mb-3">
                    Daily Reports
                </h4>

                <p class="text-slate-500 mb-6">
                    Write updates, upload media, and record daily activity.
                </p>

                <a href="{{ route('carer.daily-reports.index') }}"
                   class="inline-block bg-sky-600 text-white px-5 py-2 rounded-xl font-medium hover:bg-sky-700 transition">
                    Open Reports
                </a>
            </div>

        </div>
    </div>
</x-app-layout>