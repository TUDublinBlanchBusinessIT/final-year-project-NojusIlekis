<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-semibold text-2xl text-slate-900 dark:text-slate-100 leading-tight">
                    {{ $room->name }}
                </h2>
                <p class="text-sm text-slate-600 dark:text-slate-300 mt-1">
                    {{ __('manager.rooms_profile') }}
                </p>
            </div>

            <div class="flex items-center gap-2">
                <a href="{{ route('manager.rooms.edit', $room) }}"
                   class="inline-flex items-center justify-center gap-2 rounded-xl px-4 py-2 font-semibold text-white
                          bg-gradient-to-r from-blue-600 via-blue-700 to-indigo-700
                          shadow-sm shadow-blue-500/20
                          hover:shadow-md hover:shadow-blue-500/30 hover:brightness-110
                          focus:outline-none focus:ring-4 focus:ring-blue-200
                          active:translate-y-[1px]">
                    {{ __('manager.rooms_edit') }}
                </a>

                <form method="POST" action="{{ route('manager.rooms.destroy', $room) }}"
                      onsubmit="return confirm('{{ __('manager.rooms_delete_confirm', ['name' => addslashes($room->name)]) }}')">
                    @csrf
                    @method('DELETE')
                    <button type="submit"
                            class="inline-flex items-center justify-center rounded-xl px-4 py-2 text-sm font-semibold
                                   bg-red-50 text-red-700 border border-red-200
                                   hover:bg-red-100
                                   dark:bg-red-950/30 dark:text-red-300 dark:border-red-800/60 dark:hover:bg-red-950/50
                                   focus:outline-none focus:ring-4 focus:ring-red-200">
                        {{ __('manager.rooms_delete') }}
                    </button>
                </form>

                <a href="{{ route('manager.rooms.index') }}"
                   class="inline-flex items-center rounded-full px-3 py-1 text-xs font-medium
                          bg-slate-50 text-slate-700 border border-slate-200 hover:bg-slate-100
                          dark:bg-slate-900/40 dark:text-slate-200 dark:border-slate-700/60">
                    {{ __('manager.back') }}
                </a>
            </div>
        </div>
    </x-slot>

    @php
        $childCount = $room->children->count();
        $atCapacity = $room->capacity !== null && $childCount >= $room->capacity;
    @endphp

    <div class="py-8">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8 space-y-6">

            @if (session('success'))
                <div class="rounded-2xl border border-green-200 bg-green-50 px-4 py-3 text-green-700
                            dark:border-green-900/60 dark:bg-green-950/30 dark:text-green-200">
                    {{ session('success') }}
                </div>
            @endif

            @if (session('error'))
                <div class="rounded-2xl border border-red-200 bg-red-50 px-4 py-3 text-red-700
                            dark:border-red-900/60 dark:bg-red-950/30 dark:text-red-200">
                    {{ session('error') }}
                </div>
            @endif

            @if ($atCapacity)
                <div class="rounded-2xl border border-red-200 bg-red-50 px-4 py-3 text-red-700
                            dark:border-red-900/60 dark:bg-red-950/30 dark:text-red-200">
                    {{ __('manager.rooms_at_capacity', ['count' => $childCount, 'capacity' => $room->capacity]) }}
                </div>
            @endif

            {{-- Details --}}
            <div class="rounded-2xl border border-slate-200 bg-white shadow-sm
                        dark:border-slate-800 dark:bg-slate-950/40 overflow-hidden">
                <div class="px-5 py-4 border-b border-slate-200 dark:border-slate-800">
                    <h3 class="text-base font-semibold text-slate-900 dark:text-slate-100">{{ __('manager.details') }}</h3>
                </div>
                <div class="p-6 grid grid-cols-1 sm:grid-cols-2 gap-5 text-sm">
                    <div class="space-y-3 text-slate-800 dark:text-slate-200">
                        <div>
                            <div class="text-xs uppercase tracking-wider text-slate-500 dark:text-slate-400">{{ __('manager.rooms_name') }}</div>
                            <div class="font-medium">{{ $room->name }}</div>
                        </div>
                        <div>
                            <div class="text-xs uppercase tracking-wider text-slate-500 dark:text-slate-400">{{ __('manager.rooms_age_band') }}</div>
                            <div class="font-medium">{{ $room->age_band ?: '—' }}</div>
                        </div>
                    </div>
                    <div class="space-y-3 text-slate-800 dark:text-slate-200">
                        <div>
                            <div class="text-xs uppercase tracking-wider text-slate-500 dark:text-slate-400">{{ __('manager.rooms_capacity') }}</div>
                            <div class="font-medium">
                                <span class="{{ $atCapacity ? 'text-red-600 dark:text-red-400' : '' }}">
                                    {{ $childCount }} / {{ $room->capacity ?? '—' }}
                                </span>
                            </div>
                        </div>
                    </div>

                    @if ($room->description)
                        <div class="sm:col-span-2">
                            <div class="text-xs uppercase tracking-wider text-slate-500 dark:text-slate-400">{{ __('manager.rooms_description') }}</div>
                            <div class="mt-1 whitespace-pre-line text-slate-800 dark:text-slate-200">{{ $room->description }}</div>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Children in this room --}}
            <div class="rounded-2xl border border-slate-200 bg-white shadow-sm
                        dark:border-slate-800 dark:bg-slate-950/40 overflow-hidden">
                <div class="px-5 py-4 border-b border-slate-200 dark:border-slate-800 flex items-center justify-between">
                    <h3 class="text-base font-semibold text-slate-900 dark:text-slate-100">
                        {{ __('manager.rooms_children_in_room') }}
                        <span class="ml-2 text-sm font-normal text-slate-500 dark:text-slate-400">
                            ({{ $childCount }})
                        </span>
                    </h3>
                </div>

                @if ($childCount === 0)
                    <div class="p-6 text-sm text-slate-600 dark:text-slate-300">
                        {{ __('manager.rooms_no_children') }}
                    </div>
                @else
                    <ul class="divide-y divide-slate-200 dark:divide-slate-800">
                        @foreach ($room->children as $child)
                            <li class="px-5 py-3 flex items-center justify-between">
                                <span class="text-slate-800 dark:text-slate-200">
                                    {{ $child->first_name }} {{ $child->last_name }}
                                </span>
                                <a href="{{ route('manager.children.show', $child) }}"
                                   class="text-xs text-blue-600 hover:underline dark:text-blue-400">
                                    {{ __('manager.view') }}
                                </a>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </div>

            {{-- Carers in this room --}}
            <div class="rounded-2xl border border-slate-200 bg-white shadow-sm
                        dark:border-slate-800 dark:bg-slate-950/40 overflow-hidden">
                <div class="px-5 py-4 border-b border-slate-200 dark:border-slate-800">
                    <h3 class="text-base font-semibold text-slate-900 dark:text-slate-100">
                        {{ __('manager.rooms_carers_assigned') }}
                        <span class="ml-2 text-sm font-normal text-slate-500 dark:text-slate-400">
                            ({{ $room->activeCarers->count() }})
                        </span>
                    </h3>
                </div>

                @if ($room->activeCarers->isEmpty())
                    <div class="p-6 text-sm text-slate-600 dark:text-slate-300">
                        {{ __('manager.rooms_no_carers') }}
                    </div>
                @else
                    <ul class="divide-y divide-slate-200 dark:divide-slate-800">
                        @foreach ($room->activeCarers as $carer)
                            <li class="px-5 py-3 flex items-center justify-between">
                                <span class="text-slate-800 dark:text-slate-200">
                                    {{ $carer->name }}
                                </span>
                                <span class="text-xs text-slate-500 dark:text-slate-400">{{ $carer->email }}</span>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </div>

        </div>
    </div>
</x-app-layout>
