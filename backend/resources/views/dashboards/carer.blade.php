<x-app-layout>


    <div class="py-10 px-6 max-w-6xl mx-auto">

        @if (session('success'))
            <div class="rounded-2xl border border-green-200 bg-green-50 px-4 py-3 mb-6 text-green-800">
                {{ session('success') }}
            </div>
        @endif

        @php
            $activeClockIn = auth()->user()->currentClockIn();
        @endphp
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 mb-6">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-lg font-semibold text-gray-800">⏰ {{ __('staff.your_shift') }}</h3>
                    @if($activeClockIn)
                        <p class="text-sm text-gray-500 mt-1">
                            {{ __('staff.clocked_in_at') }} {{ $activeClockIn->clocked_in_at->format('H:i') }}
                            · {{ __('staff.duration') }}: {{ $activeClockIn->durationLabel() }}
                        </p>
                    @else
                        <p class="text-sm text-gray-500 mt-1">{{ __('staff.not_clocked_in_message') }}</p>
                    @endif
                </div>
                @if($activeClockIn)
                    <form method="POST" action="{{ route('carer.clock-out') }}">
                        @csrf
                        <button type="submit" class="px-6 py-3 bg-red-600 text-white font-semibold rounded-xl hover:bg-red-700 transition">
                            {{ __('staff.clock_out') }}
                        </button>
                    </form>
                @else
                    <form method="POST" action="{{ route('carer.clock-in') }}">
                        @csrf
                        <button type="submit" class="px-6 py-3 bg-gradient-to-r from-green-600 to-emerald-600 text-white font-semibold rounded-xl hover:opacity-90 transition">
                            {{ __('staff.clock_in') }}
                        </button>
                    </form>
                @endif
            </div>
        </div>

        <!-- Welcome Section -->
        <div class="mb-10">
            <h3 class="text-3xl font-bold text-slate-800 text-white">
                {{ __('carer.welcome_back') }}, {{ auth()->user()->name }} 👋
            </h3>
            <p class="text-slate-500 mt-2">
                {{ __('carer.quick_access') }}
            </p>
        </div>

        <!-- Allergy Quick Reference -->
        @if(isset($rooms) && $rooms->isNotEmpty())
            @php $hasAnyAllergies = $rooms->contains(fn($r) => $r->children->contains(fn($c) => $c->hasAllergies())); @endphp

            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 mb-8">
                <h3 class="text-lg font-semibold text-slate-800 mb-4">🚨 {{ __('carer.allergy_reference') }}</h3>

                @if($hasAnyAllergies)
                    @foreach($rooms as $room)
                        <x-room-allergy-summary :room="$room" />
                    @endforeach
                @else
                    <p class="text-slate-400 text-sm">{{ __('carer.no_allergies') }}</p>
                @endif
            </div>
        @endif

        <!-- Dashboard Cards -->
        <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">

            <!-- Attendance -->
            <div class="bg-white rounded-2xl shadow-md hover:shadow-xl transition p-8 border border-slate-100">
                <h4 class="text-xl font-semibold text-slate-800 mb-3">
                    {{ __('carer.attendance') }}
                </h4>

                <p class="text-slate-500 mb-6">
                    {{ __('carer.attendance_desc') }}
                </p>

                <a href="{{ route('carer.attendance.index') }}"
                   class="inline-block bg-blue-600 text-white px-5 py-2 rounded-xl font-medium hover:bg-blue-700 transition">
                    {{ __('carer.open_attendance') }}
                </a>
            </div>

            <!-- Daily Reports -->
            <div class="bg-white rounded-2xl shadow-md hover:shadow-xl transition p-8 border border-slate-100">
                <h4 class="text-xl font-semibold text-slate-800 mb-3">
                    {{ __('carer.daily_reports') }}
                </h4>

                <p class="text-slate-500 mb-6">
                    {{ __('carer.daily_reports_desc') }}
                </p>

                <a href="{{ route('carer.daily-reports.index') }}"
                   class="inline-block bg-sky-600 text-white px-5 py-2 rounded-xl font-medium hover:bg-sky-700 transition">
                    {{ __('carer.open_reports') }}
                </a>
            </div>

            <!-- Daily Updates -->
            <div class="bg-white rounded-2xl shadow-md hover:shadow-xl transition p-8 border border-slate-100">
                <h4 class="text-xl font-semibold text-slate-800 mb-3">
                    {{ __('carer.daily_updates') }}
                </h4>

                <p class="text-slate-500 mb-6">
                    {{ __('carer.daily_updates_desc') }}
                </p>

                <a href="{{ route('carer.daily-updates.index') }}"
                   class="inline-block bg-blue-600 text-white px-5 py-2 rounded-xl font-medium hover:bg-blue-700 transition">
                    {{ __('carer.open_updates') }}
                </a>
            </div>

            <!-- Medication -->
            <div class="bg-white rounded-2xl shadow-md hover:shadow-xl transition p-8 border border-slate-100">
                <h4 class="text-xl font-semibold text-slate-800 mb-3">
                    {{ __('carer.medication_logs') }}
                </h4>

                <p class="text-slate-500 mb-6">
                    {{ __('carer.medication_desc') }}
                </p>

                <a href="{{ route('carer.medication.index') }}"
                    class="inline-block bg-indigo-600 text-white px-5 py-2 rounded-xl font-medium hover:bg-indigo-700 transition">
                    {{ __('carer.open_medication') }}
                </a>
            </div>

            <!-- Incident Reports -->
            <div class="rounded-2xl border border-slate-200 bg-white shadow-sm p-6
                    dark:border-slate-800 dark:bg-slate-950/40">

                <h3 class="text-lg font-semibold text-slate-900 dark:text-slate-100 mb-2">
                    {{ __('carer.incident_reports') }}
                </h3>

                <p class="text-sm text-slate-600 dark:text-slate-300 mb-4">
                    {{ __('carer.incident_desc') }}
                </p>

                <a href="{{ route('carer.incident-reports.index') }}"
                    class="inline-flex items-center justify-center rounded-xl px-4 py-2 text-sm font-semibold text-white
                        bg-gradient-to-r from-red-600 to-red-700
                        hover:brightness-110 shadow-sm">
                    {{ __('carer.view_incidents') }}
                </a>

            </div>

        </div>
    </div>
</x-app-layout>