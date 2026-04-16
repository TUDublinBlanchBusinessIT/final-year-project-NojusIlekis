<x-app-layout>
    <x-slot name="header">
        <div>
            <h2 class="text-2xl font-semibold text-slate-900 dark:text-slate-100">{{ __('carer.milestones') }}</h2>
            <p class="text-sm text-slate-600 dark:text-slate-300 mt-1">
                {{ __('carer.milestones_intro') }}
            </p>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-slate-800 dark:bg-slate-950/40 overflow-hidden">
                <div class="px-5 py-4 border-b border-slate-200 dark:border-slate-800">
                    <h3 class="text-base font-semibold text-slate-900 dark:text-slate-100">{{ __('carer.children_in_rooms') }}</h3>
                </div>

                <div class="divide-y divide-slate-200 dark:divide-slate-800">
                    @forelse($children as $child)
                        <a href="{{ route('carer.milestones.show', $child) }}"
                           class="flex items-center justify-between px-5 py-4 hover:bg-slate-50 dark:hover:bg-slate-900/30 transition">
                            <div class="flex items-center gap-4">
                                <div class="w-10 h-10 rounded-full bg-gradient-to-br from-blue-600 via-blue-700 to-indigo-700
                                            flex items-center justify-center text-sm font-semibold text-white shadow-sm">
                                    {{ strtoupper(substr($child->first_name, 0, 1)) }}{{ strtoupper(substr($child->last_name, 0, 1)) }}
                                </div>
                                <div>
                                    <p class="font-medium text-slate-900 dark:text-slate-100">
                                        {{ $child->first_name }} {{ $child->last_name }}
                                    </p>
                                    <p class="text-xs text-slate-500 dark:text-slate-400">
                                        {{ $child->age_in_months }} {{ __('carer.months') }} ·
                                        {{ \App\Models\Milestone::AGE_RANGES[$child->age_range] ?? $child->age_range }}
                                        @if($child->room)
                                            · {{ $child->room->name }}
                                        @endif
                                    </p>
                                </div>
                            </div>
                            <svg class="w-5 h-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                            </svg>
                        </a>
                    @empty
                        <div class="px-5 py-8 text-center text-slate-500 dark:text-slate-400">
                            {{ __('carer.no_children_active_rooms') }}
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</x-app-layout>