<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-semibold text-slate-900 dark:text-slate-100">
                    {{ $child->first_name }} {{ $child->last_name }} — {{ __('parent.milestones') }}
                </h2>
                <p class="text-sm text-slate-600 dark:text-slate-300 mt-1">
                    {{ __('parent.age') }}: {{ $child->age_in_months }} {{ __('parent.months') }} ·
                    <span class="inline-flex items-center rounded-full px-2 py-0.5 text-xs font-semibold bg-blue-100 text-blue-800 dark:bg-blue-900/40 dark:text-blue-200">
                        {{ \App\Models\Milestone::AGE_RANGES[$ageRange] ?? $ageRange }}
                    </span>
                    · {{ $overallProgress['achieved'] }}/{{ $overallProgress['total'] }} {{ __('parent.milestones_observed') }}
                </p>
            </div>

            <a href="{{ route('parent.children.index') }}"
               class="inline-flex items-center rounded-full px-3 py-1 text-xs font-medium bg-slate-50 text-slate-700 border border-slate-200 hover:bg-slate-100">
                ← {{ __('parent.children') }}
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8 space-y-6">

            {{-- Overall Progress Bar --}}
            <div class="rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-slate-800 dark:bg-slate-950/40 p-5">
                <div class="flex items-center justify-between mb-2">
                    <h3 class="text-sm font-semibold text-slate-700 dark:text-slate-200 uppercase tracking-wide">
                        {{ __('parent.overall_progress') }}
                    </h3>
                    <span class="text-sm font-bold text-slate-900 dark:text-slate-100">
                        {{ $overallProgress['achieved'] }} / {{ $overallProgress['total'] }} — {{ $overallProgress['percentage'] }}%
                    </span>
                </div>
                <div class="h-3 bg-slate-100 dark:bg-slate-800 rounded-full overflow-hidden">
                    <div class="h-full bg-gradient-to-r from-blue-500 to-indigo-500 rounded-full transition-all"
                         style="width: {{ $overallProgress['percentage'] }}%"></div>
                </div>
            </div>

            {{-- Progress Cards per Theme --}}
            @php
                $colours = [
                    'wellbeing'          => ['bg' => 'bg-green-500',  'light' => 'bg-green-100',  'text' => 'text-green-700',  'border' => 'border-green-200'],
                    'identity-belonging' => ['bg' => 'bg-blue-500',   'light' => 'bg-blue-100',   'text' => 'text-blue-700',   'border' => 'border-blue-200'],
                    'communicating'      => ['bg' => 'bg-purple-500', 'light' => 'bg-purple-100', 'text' => 'text-purple-700', 'border' => 'border-purple-200'],
                    'exploring-thinking' => ['bg' => 'bg-amber-500',  'light' => 'bg-amber-100',  'text' => 'text-amber-700',  'border' => 'border-amber-200'],
                ];
            @endphp

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                @foreach($progress as $category => $data)
                    @php $c = $colours[$category] ?? ['bg' => 'bg-gray-500', 'light' => 'bg-gray-100', 'text' => 'text-gray-700', 'border' => 'border-gray-200']; @endphp
                    <div class="rounded-2xl border {{ $c['border'] }} bg-white shadow-sm dark:bg-slate-950/40 p-5">
                        <h3 class="font-semibold {{ $c['text'] }} mb-3">
                            {{ __('parent.category_' . str_replace('-', '_', $category)) }}
                        </h3>
                        <div class="flex items-center gap-3">
                            <div class="flex-1 h-3 {{ $c['light'] }} rounded-full overflow-hidden">
                                <div class="{{ $c['bg'] }} h-full rounded-full transition-all" style="width: {{ $data['percentage'] }}%"></div>
                            </div>
                            <span class="text-sm font-semibold text-slate-700 dark:text-slate-200">
                                {{ $data['achieved'] }}/{{ $data['total'] }}
                            </span>
                        </div>
                        <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">
                            {{ $data['percentage'] }}% {{ __('parent.observed') }}
                        </p>
                    </div>
                @endforeach
            </div>

            {{-- Milestone List per Category --}}
            <div class="rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-slate-800 dark:bg-slate-950/40">
                <div class="px-5 py-4 border-b border-slate-200 dark:border-slate-800">
                    <h3 class="text-base font-semibold text-slate-900 dark:text-slate-100">
                        {{ __('parent.all_milestones') }}
                    </h3>
                    <p class="text-sm text-slate-600 dark:text-slate-300 mt-1">
                        {{ __('parent.milestones_for_age_range', [
                            'range' => \App\Models\Milestone::AGE_RANGES[$ageRange] ?? $ageRange,
                        ]) }}
                    </p>
                </div>

                <div class="p-5 space-y-8">
                    @forelse($milestones as $category => $categoryMilestones)
                        @php $c = $colours[$category] ?? ['bg' => 'bg-gray-500', 'light' => 'bg-gray-100', 'text' => 'text-gray-700', 'border' => 'border-gray-200']; @endphp
                        <div>
                            <h4 class="text-sm font-semibold {{ $c['text'] }} uppercase tracking-wide mb-3 flex items-center gap-2">
                                <span class="inline-block w-2.5 h-2.5 rounded-full {{ $c['bg'] }}"></span>
                                {{ __('parent.category_' . str_replace('-', '_', $category)) }}
                            </h4>

                            <div class="space-y-2">
                                @foreach($categoryMilestones as $milestone)
                                    @php $achieved = in_array($milestone->id, $achievedIds); @endphp
                                    <div class="flex items-start gap-3 rounded-xl p-3
                                                {{ $achieved ? 'bg-green-50 dark:bg-green-950/10' : 'bg-slate-50 dark:bg-slate-900/20' }}">

                                        <div class="mt-0.5 flex-shrink-0">
                                            @if($achieved)
                                                <div class="w-5 h-5 bg-green-500 rounded-full flex items-center justify-center">
                                                    <svg class="w-3 h-3 text-white" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                                    </svg>
                                                </div>
                                            @else
                                                <div class="w-5 h-5 border-2 border-slate-300 dark:border-slate-600 rounded-full"></div>
                                            @endif
                                        </div>

                                        <div class="flex-1 min-w-0">
                                            <p class="text-sm font-medium {{ $achieved ? 'text-slate-900 dark:text-slate-100' : 'text-slate-500 dark:text-slate-400' }}">
                                                {{ $milestone->name }}
                                            </p>
                                            @if($achieved)
                                                @php $data = $achievedData[$milestone->id] ?? null; @endphp
                                                @if($data)
                                                    <p class="text-xs text-green-600 dark:text-green-400 mt-0.5">
                                                        {{ __('parent.observed_on') }} {{ $data->observed_at->format('d M Y') }}
                                                        {{ __('parent.by') }} {{ $data->observer->name ?? __('parent.a_carer') }}
                                                        @if($data->notes)
                                                            · <span class="italic">"{{ $data->notes }}"</span>
                                                        @endif
                                                    </p>
                                                @endif
                                            @endif
                                        </div>

                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @empty
                        <p class="text-slate-500 dark:text-slate-400 text-sm">
                            {{ __('parent.no_milestones_for_age') }}
                        </p>
                    @endforelse
                </div>
            </div>

        </div>
    </div>
</x-app-layout>