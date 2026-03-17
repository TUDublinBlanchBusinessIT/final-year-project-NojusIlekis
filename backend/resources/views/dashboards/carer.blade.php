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

        <!-- Allergy Quick Reference -->
        @if(isset($rooms) && $rooms->isNotEmpty())
            @php $hasAnyAllergies = $rooms->contains(fn($r) => $r->children->contains(fn($c) => $c->hasAllergies())); @endphp

            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 mb-8">
                <h3 class="text-lg font-semibold text-slate-800 mb-4">🚨 Allergy Quick Reference</h3>

                @if($hasAnyAllergies)
                    @foreach($rooms as $room)
                        <x-room-allergy-summary :room="$room" />
                    @endforeach
                @else
                    <p class="text-slate-400 text-sm">No children with allergies in your rooms.</p>
                @endif
            </div>
        @endif

        <!-- Dashboard Cards -->
        <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">

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

            <!-- Daily Updates Card -->
            <div class="bg-white rounded-2xl shadow-md hover:shadow-xl transition p-8 border border-slate-100">
                <h4 class="text-xl font-semibold text-slate-800 mb-3">
                    Daily Updates
                </h4>

                <p class="text-slate-500 mb-6">
                    Record daily updates for children.
                </p>

                <a href="{{ route('carer.daily-updates.index') }}"
                   class="inline-block bg-blue-600 text-white px-5 py-2 rounded-xl font-medium hover:bg-blue-700 transition">
                    Open Updates
                </a>
            </div>

            <!-- Medication Card -->
            <div class="bg-white rounded-2xl shadow-md hover:shadow-xl transition p-8 border border-slate-100">
                <h4 class="text-xl font-semibold text-slate-800 mb-3">
                    Medication Logs
                </h4>

                <p class="text-slate-500 mb-6">
                    Record medication given to children and keep a clear history.
                </p>

                <a href="{{ route('carer.medication.index') }}"
                    class="inline-block bg-indigo-600 text-white px-5 py-2 rounded-xl font-medium hover:bg-indigo-700 transition">
                    Open Medication Logs
                </a>
            </div>

            <!-- Incident report card -->
            <div class="rounded-2xl border border-slate-200 bg-white shadow-sm p-6
                    dark:border-slate-800 dark:bg-slate-950/40">

            <h3 class="text-lg font-semibold text-slate-900 dark:text-slate-100 mb-2">
                Incident Reports
            </h3>

            <p class="text-sm text-slate-600 dark:text-slate-300 mb-4">
                Log and review incidents that occur during the day.
            </p>

            <a href="{{ route('carer.incident-reports.index') }}"
                class="inline-flex items-center justify-center rounded-xl px-4 py-2 text-sm font-semibold text-white
                    bg-gradient-to-r from-red-600 to-red-700
                    hover:brightness-110 shadow-sm">
                View Incident Reports
            </a>

        </div>

        </div>
    </div>
</x-app-layout>