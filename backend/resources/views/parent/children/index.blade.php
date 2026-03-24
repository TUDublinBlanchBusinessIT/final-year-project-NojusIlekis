<x-app-layout>
    <x-slot name="header">
        <div>
            <h2 class="text-2xl font-semibold text-slate-900 dark:text-slate-100">My Children</h2>
            <p class="text-sm text-slate-600 dark:text-slate-300 mt-1">
                View profiles, daily updates, and milestone progress for each child.
            </p>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-slate-800 dark:bg-slate-950/40 overflow-hidden">

                <div class="divide-y divide-slate-200 dark:divide-slate-800">
                    @forelse($children as $child)
                        @php $mp = $child->milestoneProgress(); @endphp
                        <a href="{{ route('parent.children.show', $child) }}"
                           class="flex items-center justify-between px-5 py-4 hover:bg-slate-50 dark:hover:bg-slate-900/30 transition">
                            <div class="flex items-center gap-4">
                                <div class="w-10 h-10 rounded-full bg-gradient-to-br from-blue-600 via-blue-700 to-indigo-700
                                            flex items-center justify-center text-sm font-semibold text-white shadow-sm flex-shrink-0">
                                    {{ strtoupper(substr($child->first_name, 0, 1)) }}{{ strtoupper(substr($child->last_name, 0, 1)) }}
                                </div>
                                <div>
                                    <p class="font-medium text-slate-900 dark:text-slate-100">
                                        {{ $child->first_name }} {{ $child->last_name }}
                                    </p>
                                    <p class="text-xs text-slate-500 dark:text-slate-400">
                                        {{ $child->age_in_months }} months
                                        @if($child->room)
                                            · {{ $child->room->name }}
                                        @endif
                                        @if($mp['total'] > 0)
                                            · {{ $mp['percentage'] }}% milestones
                                        @endif
                                    </p>
                                </div>
                            </div>
                            <svg class="w-5 h-5 text-slate-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                            </svg>
                        </a>
                    @empty
                        <div class="px-5 py-8 text-center text-sm text-slate-500 dark:text-slate-400">
                            No children linked to your account yet.
                        </div>
                    @endforelse
                </div>

            </div>
        </div>
    </div>
</x-app-layout>
