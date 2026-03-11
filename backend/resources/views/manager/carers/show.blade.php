<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-semibold text-2xl text-slate-900 dark:text-slate-100 leading-tight">
                    {{ $carerUser->name }}
                </h2>
                <p class="text-sm text-slate-600 dark:text-slate-300 mt-1">
                    Carer Profile
                </p>
            </div>

            <div class="flex items-center gap-2">
                <a href="{{ route('manager.carers.edit', $carerUser) }}"
                   class="inline-flex items-center justify-center gap-2 rounded-xl px-4 py-2 font-semibold text-white
                          bg-gradient-to-r from-blue-600 via-blue-700 to-indigo-700
                          shadow-sm shadow-blue-500/20
                          hover:shadow-md hover:shadow-blue-500/30 hover:brightness-110
                          focus:outline-none focus:ring-4 focus:ring-blue-200
                          active:translate-y-[1px]">
                    Edit
                </a>

                <form method="POST" action="{{ route('manager.carers.destroy', $carerUser) }}"
                      onsubmit="return confirm('Delete {{ addslashes($carerUser->name) }}? This cannot be undone.')">
                    @csrf
                    @method('DELETE')
                    <button type="submit"
                            class="inline-flex items-center justify-center rounded-xl px-4 py-2 text-sm font-semibold
                                   bg-red-50 text-red-700 border border-red-200
                                   hover:bg-red-100
                                   dark:bg-red-950/30 dark:text-red-300 dark:border-red-800/60 dark:hover:bg-red-950/50
                                   focus:outline-none focus:ring-4 focus:ring-red-200">
                        Delete
                    </button>
                </form>

                <a href="{{ route('manager.carers.index') }}"
                   class="inline-flex items-center rounded-full px-3 py-1 text-xs font-medium
                          bg-slate-50 text-slate-700 border border-slate-200 hover:bg-slate-100
                          dark:bg-slate-900/40 dark:text-slate-200 dark:border-slate-700/60">
                    Back
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8 space-y-6">

            @if (session('success'))
                <div class="rounded-2xl border border-green-200 bg-green-50 px-4 py-3 text-green-700
                            dark:border-green-900/60 dark:bg-green-950/30 dark:text-green-200">
                    {{ session('success') }}
                </div>
            @endif

            {{-- Details Card --}}
            <div class="rounded-2xl border border-slate-200 bg-white shadow-sm
                        dark:border-slate-800 dark:bg-slate-950/40 overflow-hidden">
                <div class="px-5 py-4 border-b border-slate-200 dark:border-slate-800">
                    <h3 class="text-base font-semibold text-slate-900 dark:text-slate-100">Details</h3>
                </div>
                <div class="p-6 space-y-3 text-sm text-slate-800 dark:text-slate-200">
                    <p>
                        <span class="font-semibold text-slate-600 dark:text-slate-400">Name:</span>
                        {{ $carerUser->name }}
                    </p>
                    <p>
                        <span class="font-semibold text-slate-600 dark:text-slate-400">Email:</span>
                        {{ $carerUser->email }}
                    </p>
                    <p>
                        <span class="font-semibold text-slate-600 dark:text-slate-400">Registered:</span>
                        {{ $carerUser->created_at->format('d M Y') }}
                    </p>
                </div>
            </div>

            {{-- Current Room Assignment --}}
            <div class="rounded-2xl border border-slate-200 bg-white shadow-sm
                        dark:border-slate-800 dark:bg-slate-950/40 overflow-hidden">
                <div class="px-5 py-4 border-b border-slate-200 dark:border-slate-800">
                    <h3 class="text-base font-semibold text-slate-900 dark:text-slate-100">Current Room Assignment</h3>
                </div>
                <div class="p-6 text-sm">
                    @php($activeRoom = $carerUser->rooms->first(fn($r) => $r->pivot->end_date === null))
                    @if ($activeRoom)
                        <p class="text-slate-800 dark:text-slate-200">
                            <span class="font-medium">{{ $activeRoom->name }}</span>
                            <span class="text-slate-500 dark:text-slate-400 ml-2">
                                Since {{ \Carbon\Carbon::parse($activeRoom->pivot->start_date)->format('d M Y') }}
                            </span>
                        </p>
                    @else
                        <span class="inline-flex items-center rounded-full px-2 py-0.5 text-xs font-medium
                                     bg-amber-50 text-amber-700 border border-amber-200
                                     dark:bg-amber-950/30 dark:text-amber-300 dark:border-amber-800/60">
                            Unassigned
                        </span>
                    @endif
                </div>
            </div>

            {{-- Room History --}}
            @php($historicalRooms = $carerUser->rooms->filter(fn($r) => $r->pivot->end_date !== null))
            @if ($historicalRooms->isNotEmpty())
                <div class="rounded-2xl border border-slate-200 bg-white shadow-sm
                            dark:border-slate-800 dark:bg-slate-950/40 overflow-hidden">
                    <div class="px-5 py-4 border-b border-slate-200 dark:border-slate-800">
                        <h3 class="text-base font-semibold text-slate-900 dark:text-slate-100">Room History</h3>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="min-w-full text-sm">
                            <thead>
                                <tr class="bg-slate-50 dark:bg-slate-900/60">
                                    <th class="text-left px-5 py-3 text-xs font-semibold uppercase tracking-wider text-slate-600 dark:text-slate-300">Room</th>
                                    <th class="text-left px-5 py-3 text-xs font-semibold uppercase tracking-wider text-slate-600 dark:text-slate-300">Start Date</th>
                                    <th class="text-left px-5 py-3 text-xs font-semibold uppercase tracking-wider text-slate-600 dark:text-slate-300">End Date</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-200 dark:divide-slate-800">
                                @foreach ($historicalRooms as $room)
                                    <tr>
                                        <td class="px-5 py-3 text-slate-800 dark:text-slate-200">{{ $room->name }}</td>
                                        <td class="px-5 py-3 text-slate-700 dark:text-slate-200">
                                            {{ \Carbon\Carbon::parse($room->pivot->start_date)->format('d M Y') }}
                                        </td>
                                        <td class="px-5 py-3 text-slate-700 dark:text-slate-200">
                                            {{ \Carbon\Carbon::parse($room->pivot->end_date)->format('d M Y') }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif

            {{-- Recent Activity --}}
            <div class="rounded-2xl border border-slate-200 bg-white shadow-sm
                        dark:border-slate-800 dark:bg-slate-950/40 overflow-hidden">
                <div class="px-5 py-4 border-b border-slate-200 dark:border-slate-800">
                    <h3 class="text-base font-semibold text-slate-900 dark:text-slate-100">Recent Activity</h3>
                </div>
                <div class="p-6 grid grid-cols-1 sm:grid-cols-3 gap-5 text-sm">
                    <div class="rounded-xl border border-slate-200 dark:border-slate-700 p-4 text-center">
                        <p class="text-2xl font-bold text-slate-900 dark:text-slate-100">{{ $dailyReportsCount }}</p>
                        <p class="mt-1 text-slate-600 dark:text-slate-400">Daily Reports</p>
                    </div>
                    <div class="rounded-xl border border-slate-200 dark:border-slate-700 p-4 text-center">
                        <p class="text-2xl font-bold text-slate-900 dark:text-slate-100">{{ $attendanceCount }}</p>
                        <p class="mt-1 text-slate-600 dark:text-slate-400">Attendance Records</p>
                    </div>
                    <div class="rounded-xl border border-slate-200 dark:border-slate-700 p-4 text-center">
                        <p class="text-2xl font-bold text-slate-900 dark:text-slate-100">{{ $medicationLogsCount }}</p>
                        <p class="mt-1 text-slate-600 dark:text-slate-400">Medication Logs</p>
                    </div>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
