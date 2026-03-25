<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
            <div>
                <h2 class="text-2xl font-semibold text-slate-900 dark:text-slate-100">
                    {{ $child->first_name }} {{ $child->last_name }} Timeline
                </h2>
                <p class="text-sm text-slate-600 dark:text-slate-300 mt-1">
                    Date: {{ \Carbon\Carbon::parse($date)->format('d M Y') }}
                </p>
            </div>

            <form method="GET" action="{{ route('parent.children.timeline', $child) }}" class="flex items-center gap-2">
                <input type="date"
                       name="date"
                       value="{{ $date }}"
                       class="rounded-xl border border-slate-300 bg-white px-3 py-2 text-sm text-slate-900
                              focus:outline-none focus:ring-4 focus:ring-blue-200 focus:border-blue-500
                              dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100">

                <button type="submit"
                        class="inline-flex items-center justify-center rounded-xl px-4 py-2 text-sm font-semibold text-white
                               bg-gradient-to-r from-blue-600 via-blue-700 to-indigo-700
                               shadow-sm shadow-blue-500/20 hover:brightness-110 focus:outline-none focus:ring-4 focus:ring-blue-200">
                    View
                </button>

                <a href="{{ route('parent.children.timeline', $child) }}"
                   class="inline-flex items-center justify-center rounded-xl px-4 py-2 text-sm font-semibold
                          border border-slate-300 text-slate-700 bg-white hover:bg-slate-50
                          dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100 dark:hover:bg-slate-800">
                    Today
                </a>
            </form>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-4">
            @forelse($events as $event)
                <div class="rounded-2xl border border-slate-200 bg-white shadow-sm p-5
                            dark:border-slate-800 dark:bg-slate-950/40">
                    <p class="text-xs text-slate-500 dark:text-slate-400">
                        {{ $event['timestamp']->format('d M Y H:i') }}
                    </p>

                    <h3 class="mt-1 text-base font-semibold text-slate-900 dark:text-slate-100">
                        {{ $event['title'] }}
                    </h3>

                    <p class="mt-2 text-sm text-slate-700 dark:text-slate-300">
                        {{ $event['details'] }}
                    </p>

                    <p class="mt-2 text-xs text-slate-400">
                        Type: {{ ucfirst($event['type']) }}
                    </p>
                </div>
            @empty
                <div class="rounded-2xl border border-slate-200 bg-white shadow-sm p-6
                            dark:border-slate-800 dark:bg-slate-950/40">
                    <p class="text-sm text-slate-600 dark:text-slate-300">
                        No timeline events found for this date.
                    </p>
                </div>
            @endforelse
        </div>
    </div>
</x-app-layout>