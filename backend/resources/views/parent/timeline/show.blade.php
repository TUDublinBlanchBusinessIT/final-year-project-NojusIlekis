<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
            <div>
                <h2 class="text-2xl font-semibold text-slate-900 dark:text-slate-100">
                    {{ $child->first_name }} {{ $child->last_name }} — {{ __('parent.daily_timeline') }}
                </h2>
                <p class="text-sm text-slate-600 dark:text-slate-300 mt-1">
                    {{ \Carbon\Carbon::parse($date)->format('l, d F Y') }}
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
                    {{ __('parent.view') }}
                </button>

                <a href="{{ route('parent.children.timeline', $child) }}"
                   class="inline-flex items-center justify-center rounded-xl px-4 py-2 text-sm font-semibold
                          border border-slate-300 text-slate-700 bg-white hover:bg-slate-50
                          dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100 dark:hover:bg-slate-800">
                    {{ __('parent.today') }}
                </a>
            </form>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8 space-y-6">

            {{-- Allergy Alert --}}
            <x-allergy-alert :child="$child" />
            <x-medical-alert :child="$child" />

            {{-- Timeline --}}
            <div class="relative">
                <div class="absolute left-7 top-0 bottom-0 w-0.5 bg-slate-200 dark:bg-slate-700"></div>

                @forelse($events as $event)
                    @php
                        $colours = [
                            'attendance' => [
                                'bg'     => 'bg-blue-500',
                                'light'  => 'bg-blue-50 dark:bg-blue-950/30',
                                'border' => 'border-blue-200 dark:border-blue-800/60',
                                'label'  => 'text-blue-700 dark:text-blue-300',
                                'icon'   => '👋',
                            ],
                            'meal' => [
                                'bg'     => 'bg-amber-500',
                                'light'  => 'bg-amber-50 dark:bg-amber-950/30',
                                'border' => 'border-amber-200 dark:border-amber-800/60',
                                'label'  => 'text-amber-700 dark:text-amber-300',
                                'icon'   => '🍽️',
                            ],
                            'sleep' => [
                                'bg'     => 'bg-purple-500',
                                'light'  => 'bg-purple-50 dark:bg-purple-950/30',
                                'border' => 'border-purple-200 dark:border-purple-800/60',
                                'label'  => 'text-purple-700 dark:text-purple-300',
                                'icon'   => '😴',
                            ],
                            'report' => [
                                'bg'     => 'bg-teal-500',
                                'light'  => 'bg-teal-50 dark:bg-teal-950/30',
                                'border' => 'border-teal-200 dark:border-teal-800/60',
                                'label'  => 'text-teal-700 dark:text-teal-300',
                                'icon'   => '📝',
                            ],
                            'medication' => [
                                'bg'     => 'bg-red-500',
                                'light'  => 'bg-red-50 dark:bg-red-950/30',
                                'border' => 'border-red-200 dark:border-red-800/60',
                                'label'  => 'text-red-700 dark:text-red-300',
                                'icon'   => '💊',
                            ],
                            'incident' => [
                                'bg'     => 'bg-rose-600',
                                'light'  => 'bg-rose-50 dark:bg-rose-950/30',
                                'border' => 'border-rose-300 dark:border-rose-800/60',
                                'label'  => 'text-rose-700 dark:text-rose-300',
                                'icon'   => '🚨',
                            ],
                            'note' => [
                                'bg'     => 'bg-slate-500',
                                'light'  => 'bg-slate-50 dark:bg-slate-900/40',
                                'border' => 'border-slate-200 dark:border-slate-700',
                                'label'  => 'text-slate-600 dark:text-slate-300',
                                'icon'   => '📋',
                            ],
                        ];

                        $type = $event['type'] ?? 'note';
                        $c    = $colours[$type] ?? $colours['note'];
                    @endphp

                    <div class="relative flex items-start mb-6 pl-4">
                        <div class="absolute left-3 w-9 h-9 {{ $c['bg'] }} rounded-full flex items-center justify-center text-base shadow-md z-10 -translate-x-1/2 flex-shrink-0">
                            {{ $c['icon'] }}
                        </div>

                        <div class="ml-10 flex-1 {{ $c['light'] }} border {{ $c['border'] }} rounded-2xl p-4 shadow-sm hover:shadow-md transition-shadow">
                            <div class="flex items-center justify-between mb-1">
                                <span class="text-xs font-semibold uppercase tracking-wide {{ $c['label'] }}">
                                    {{ ucfirst(str_replace('_', ' ', $type)) }}
                                </span>
                                <span class="text-xs text-slate-400 dark:text-slate-500">
                                    {{ $event['timestamp']->format('g:i A') }}
                                </span>
                            </div>

                            <h4 class="font-semibold text-slate-900 dark:text-slate-100">
                                {{ $event['title'] }}
                            </h4>

                            @if(!empty($event['details']))
                                <p class="text-sm text-slate-600 dark:text-slate-300 mt-1">
                                    {{ $event['details'] }}
                                </p>
                            @endif
                        </div>
                    </div>

                @empty
                    <div class="text-center py-16">
                        <div class="text-6xl mb-4">🌟</div>
                        <h3 class="text-lg font-semibold text-slate-600 dark:text-slate-300">{{ __('parent.no_updates_day') }}</h3>
                        <p class="text-slate-400 dark:text-slate-500 mt-1 text-sm">
                            {{ __('parent.check_back') }}
                        </p>
                    </div>
                @endforelse
            </div>

        </div>
    </div>
</x-app-layout>