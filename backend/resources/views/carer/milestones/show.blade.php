<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div class="flex items-start gap-6">
                <a href="{{ route('carer.milestones.index') }}"
                   class="inline-flex items-center gap-2 rounded-xl border border-slate-200 bg-white px-4 py-2 text-sm font-medium text-slate-700 shadow-sm hover:bg-slate-50
                          dark:bg-slate-900/40 dark:text-slate-200 dark:border-slate-700">
                    ← {{ __('carer.back') }}
                </a>
                <div>
                    <h2 class="text-2xl font-semibold text-slate-900 dark:text-slate-100">
                        {{ $child->first_name }} {{ $child->last_name }} — {{ __('carer.milestones') }}
                    </h2>
                    <p class="text-sm text-slate-600 dark:text-slate-300 mt-1">
                        {{ __('carer.age') }}: {{ $child->age_in_months }} {{ __('carer.months') }} ·
                        <span class="inline-flex items-center rounded-full px-2 py-0.5 text-xs font-semibold bg-blue-100 text-blue-800 dark:bg-blue-900/40 dark:text-blue-200">
                            {{ \App\Models\Milestone::AGE_RANGES[$ageRange] ?? $ageRange }}
                        </span>
                    </p>
                </div>
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8 space-y-6">

            @if(session('success'))
                <div class="rounded-2xl border border-emerald-200 bg-emerald-50/30 p-4 text-emerald-800 dark:border-emerald-900/60 dark:bg-emerald-950/10 dark:text-emerald-100">
                    {{ session('success') }}
                </div>
            @endif

            <x-allergy-alert :child="$child" />
            <x-medical-alert :child="$child" />

            @php
                $categoryColours = [
                    'wellbeing'           => ['bg' => 'bg-green-100',  'text' => 'text-green-800',  'bar' => 'bg-green-500',  'border' => 'border-green-200'],
                    'identity-belonging'  => ['bg' => 'bg-blue-100',   'text' => 'text-blue-800',   'bar' => 'bg-blue-500',   'border' => 'border-blue-200'],
                    'communicating'       => ['bg' => 'bg-purple-100', 'text' => 'text-purple-800', 'bar' => 'bg-purple-500', 'border' => 'border-purple-200'],
                    'exploring-thinking'  => ['bg' => 'bg-amber-100',  'text' => 'text-amber-800',  'bar' => 'bg-amber-500',  'border' => 'border-amber-200'],
                ];
            @endphp

            <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                @foreach(\App\Models\Milestone::CATEGORIES as $key => $label)
                    @php
                        $p = $progress[$key];
                        $c = $categoryColours[$key];
                    @endphp
                    <div class="rounded-2xl border {{ $c['border'] }} {{ $c['bg'] }} p-4 shadow-sm">
                        <p class="text-xs font-semibold {{ $c['text'] }} uppercase tracking-wide mb-2">
                            {{ __('carer.category_' . str_replace('-', '_', $key)) }}
                        </p>
                        <p class="text-2xl font-bold {{ $c['text'] }}">
                            {{ $p['achieved'] }}<span class="text-base font-normal">/{{ $p['total'] }}</span>
                        </p>
                        <div class="mt-2 h-2 rounded-full bg-white/60">
                            <div class="h-2 rounded-full {{ $c['bar'] }}" style="width: {{ $p['percentage'] }}%"></div>
                        </div>
                        <p class="text-xs {{ $c['text'] }} mt-1">
                            {{ $p['percentage'] }}% {{ __('carer.achieved') }}
                        </p>
                    </div>
                @endforeach
            </div>

            <div class="rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-slate-800 dark:bg-slate-950/40">
                <div class="flex flex-wrap gap-1 p-3 border-b border-slate-200 dark:border-slate-800">
                    <a href="{{ route('carer.milestones.show', $child) }}"
                       class="rounded-lg px-4 py-2 text-sm font-medium transition
                              {{ !$categoryFilter ? 'bg-blue-600 text-white' : 'text-slate-600 hover:bg-slate-100 dark:text-slate-300 dark:hover:bg-slate-800' }}">
                        {{ __('carer.all') }}
                    </a>

                    @foreach(\App\Models\Milestone::CATEGORIES as $key => $label)
                        <a href="{{ route('carer.milestones.show', [$child, 'category' => $key]) }}"
                           class="rounded-lg px-4 py-2 text-sm font-medium transition
                                  {{ $categoryFilter === $key ? 'bg-blue-600 text-white' : 'text-slate-600 hover:bg-slate-100 dark:text-slate-300 dark:hover:bg-slate-800' }}">
                            {{ __('carer.category_' . str_replace('-', '_', $key)) }}
                        </a>
                    @endforeach
                </div>

                <div class="p-5 space-y-8">
                    @forelse($milestones as $category => $categoryMilestones)
                        @php $c = $categoryColours[$category]; @endphp
                        <div>
                            <h3 class="text-base font-semibold text-slate-900 dark:text-slate-100 mb-3 flex items-center gap-2">
                                <span class="inline-block w-3 h-3 rounded-full {{ $c['bar'] }}"></span>
                                {{ __('carer.category_' . str_replace('-', '_', $category)) }}
                            </h3>

                            <div class="space-y-3">
                                @foreach($categoryMilestones as $milestone)
                                    @php $achieved = in_array($milestone->id, $achievedIds); @endphp
                                    <div class="rounded-xl border p-4
                                                {{ $achieved ? 'border-green-300 bg-green-50 dark:border-green-900/60 dark:bg-green-950/10' : 'border-slate-200 bg-white dark:border-slate-700 dark:bg-slate-900/20' }}">
                                        <div class="flex items-start gap-4">

                                            <div class="mt-0.5 flex-shrink-0">
                                                @if($achieved)
                                                    <div class="w-6 h-6 bg-green-500 rounded-full flex items-center justify-center">
                                                        <svg class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 20 20">
                                                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                                        </svg>
                                                    </div>
                                                @else
                                                    <div class="w-6 h-6 border-2 border-slate-300 dark:border-slate-600 rounded-full"></div>
                                                @endif
                                            </div>

                                            <div class="flex-1 min-w-0">
                                                <p class="font-semibold text-slate-900 dark:text-slate-100">{{ $milestone->name }}</p>

                                                @if($milestone->description)
                                                    <p class="text-sm text-slate-500 dark:text-slate-400 mt-0.5">{{ $milestone->description }}</p>
                                                @endif

                                                @if($achieved)
                                                    @php $data = $achievedData[$milestone->id] ?? null; @endphp
                                                    @if($data)
                                                        <div class="mt-2 text-sm text-green-700 dark:text-green-400">
                                                            {{ __('carer.observed_by') }} {{ $data->observer->name ?? __('carer.unknown') }}
                                                            {{ __('carer.on') }} {{ $data->observed_at->format('d M Y') }}
                                                            @if($data->notes)
                                                                <br><span class="text-slate-500 italic">"{{ $data->notes }}"</span>
                                                            @endif
                                                        </div>
                                                    @endif

                                                    <form method="POST" action="{{ route('carer.milestones.toggle', [$child, $milestone]) }}" class="mt-2">
                                                        @csrf
                                                        <button type="submit" class="text-xs text-red-600 hover:text-red-800 underline">
                                                            {{ __('carer.remove_observation') }}
                                                        </button>
                                                    </form>
                                                @else
                                                    <form method="POST" action="{{ route('carer.milestones.toggle', [$child, $milestone]) }}"
                                                          class="mt-3" x-data="{ showNotes: false }">
                                                        @csrf
                                                        <div class="flex items-center gap-2 flex-wrap">
                                                            <button type="submit"
                                                                    class="inline-flex items-center rounded-lg px-3 py-1.5 text-sm font-semibold text-white
                                                                           bg-gradient-to-r from-blue-600 via-blue-700 to-indigo-700 hover:brightness-110 shadow-sm">
                                                                {{ __('carer.mark_as_observed') }}
                                                            </button>
                                                            <button type="button" @click="showNotes = !showNotes"
                                                                    class="text-sm text-slate-500 hover:text-slate-700 dark:text-slate-400 underline">
                                                                {{ __('carer.add_note') }}
                                                            </button>
                                                        </div>
                                                        <div x-show="showNotes" x-cloak class="mt-2">
                                                            <textarea name="notes" rows="2"
                                                                      placeholder="{{ __('carer.observation_notes_placeholder') }}"
                                                                      class="w-full text-sm rounded-lg border-slate-300 shadow-sm focus:border-blue-500 focus:ring-blue-500
                                                                             dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100"></textarea>
                                                        </div>
                                                    </form>
                                                @endif
                                            </div>

                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @empty
                        <p class="text-slate-500 dark:text-slate-400 text-sm">{{ __('carer.no_milestones_age_range') }}</p>
                    @endforelse
                </div>
            </div>

        </div>
    </div>
</x-app-layout>