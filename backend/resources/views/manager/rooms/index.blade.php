<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-semibold text-2xl text-slate-900 dark:text-slate-100 leading-tight">
                    {{ __('manager.rooms_title') }}
                </h2>
                <p class="text-sm text-slate-600 dark:text-slate-300 mt-1">
                    {{ __('manager.rooms_index_desc') }}
                </p>
            </div>

            <a href="{{ route('manager.rooms.create') }}"
               class="inline-flex items-center justify-center gap-2 rounded-xl px-4 py-2 font-semibold text-white
                      bg-gradient-to-r from-blue-600 via-blue-700 to-indigo-700
                      shadow-sm shadow-blue-500/20
                      hover:shadow-md hover:shadow-blue-500/30 hover:brightness-110
                      focus:outline-none focus:ring-4 focus:ring-blue-200
                      active:translate-y-[1px]
                      dark:shadow-blue-900/30 dark:focus:ring-blue-900/40">
                {{ __('manager.rooms_create') }}
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8 space-y-6">

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

            {{-- Search --}}
            <div class="rounded-2xl border border-slate-200 bg-white shadow-sm
                        dark:border-slate-800 dark:bg-slate-950/40 overflow-hidden">
                <div class="p-5">
                    <form method="GET" action="{{ route('manager.rooms.index') }}"
                          class="flex flex-col sm:flex-row gap-3">
                        <div class="flex-1">
                            <input type="text"
                                   name="search"
                                   value="{{ request('search') }}"
                                   placeholder="{{ __('manager.rooms_search_placeholder') }}"
                                   class="w-full rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-slate-900
                                          focus:outline-none focus:ring-4 focus:ring-blue-200 focus:border-blue-500
                                          dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100" />
                        </div>

                        <button type="submit"
                                class="inline-flex items-center justify-center gap-2 rounded-xl px-4 py-2.5 font-semibold text-white
                                       bg-gradient-to-r from-blue-600 via-blue-700 to-indigo-700
                                       shadow-sm shadow-blue-500/20
                                       hover:shadow-md hover:shadow-blue-500/30 hover:brightness-110
                                       focus:outline-none focus:ring-4 focus:ring-blue-200
                                       active:translate-y-[1px]">
                            {{ __('manager.search') }}
                        </button>

                        @if (request('search'))
                            <a href="{{ route('manager.rooms.index') }}"
                               class="inline-flex items-center justify-center rounded-xl px-4 py-2.5 text-sm font-medium
                                      bg-slate-50 text-slate-700 border border-slate-200 hover:bg-slate-100
                                      dark:bg-slate-900/40 dark:text-slate-200 dark:border-slate-700/60">
                                {{ __('manager.clear') }}
                            </a>
                        @endif
                    </form>
                </div>
            </div>

            {{-- Table --}}
            <div class="rounded-2xl border border-slate-200 bg-white shadow-sm
                        dark:border-slate-800 dark:bg-slate-950/40 overflow-hidden">
                <div class="px-5 py-4 border-b border-slate-200 dark:border-slate-800">
                    <h3 class="text-base font-semibold text-slate-900 dark:text-slate-100">
                        {{ __('manager.rooms_all') }}
                        <span class="ml-2 text-sm font-normal text-slate-500 dark:text-slate-400">
                            ({{ $rooms->total() }})
                        </span>
                    </h3>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full">
                        <thead>
                            <tr class="bg-slate-50 dark:bg-slate-900/60">
                                <th class="text-left px-5 py-3 text-xs font-semibold uppercase tracking-wider text-slate-600 dark:text-slate-300">
                                    {{ __('manager.rooms_name') }}
                                </th>
                                <th class="text-left px-5 py-3 text-xs font-semibold uppercase tracking-wider text-slate-600 dark:text-slate-300">
                                    {{ __('manager.rooms_age_band') }}
                                </th>
                                <th class="text-left px-5 py-3 text-xs font-semibold uppercase tracking-wider text-slate-600 dark:text-slate-300">
                                    {{ __('manager.rooms_occupancy') }}
                                </th>
                                <th class="text-left px-5 py-3 text-xs font-semibold uppercase tracking-wider text-slate-600 dark:text-slate-300">
                                    {{ __('manager.actions') }}
                                </th>
                            </tr>
                        </thead>

                        <tbody class="divide-y divide-slate-200 dark:divide-slate-800">
                            @forelse ($rooms as $room)
                                @php
                                    $atCapacity = $room->capacity !== null && $room->children_count >= $room->capacity;
                                @endphp
                                <tr class="hover:bg-slate-50 dark:hover:bg-slate-900/30">
                                    <td class="px-5 py-4 font-medium text-slate-900 dark:text-slate-100">
                                        {{ $room->name }}
                                    </td>

                                    <td class="px-5 py-4 text-slate-700 dark:text-slate-200">
                                        {{ $room->age_band ?: '—' }}
                                    </td>

                                    <td class="px-5 py-4">
                                        <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-semibold
                                                     {{ $atCapacity
                                                        ? 'bg-red-50 text-red-700 border border-red-200 dark:bg-red-950/30 dark:text-red-300 dark:border-red-800/60'
                                                        : 'bg-slate-100 text-slate-700 border border-slate-200 dark:bg-slate-800 dark:text-slate-200 dark:border-slate-700' }}">
                                            {{ $room->children_count }} / {{ $room->capacity ?? '—' }}
                                        </span>
                                    </td>

                                    <td class="px-5 py-4">
                                        <div class="flex items-center gap-2">
                                            <a href="{{ route('manager.rooms.show', $room) }}"
                                               class="inline-flex items-center justify-center rounded-xl px-3 py-1.5 text-xs font-semibold text-white
                                                      bg-gradient-to-r from-blue-600 via-blue-700 to-indigo-700
                                                      shadow-sm shadow-blue-500/20
                                                      hover:brightness-110 focus:outline-none focus:ring-4 focus:ring-blue-200
                                                      active:translate-y-[1px]">
                                                {{ __('manager.view') }}
                                            </a>

                                            <a href="{{ route('manager.rooms.edit', $room) }}"
                                               class="inline-flex items-center justify-center rounded-xl px-3 py-1.5 text-xs font-semibold
                                                      bg-slate-100 text-slate-700 border border-slate-200
                                                      hover:bg-slate-200
                                                      dark:bg-slate-800 dark:text-slate-200 dark:border-slate-700 dark:hover:bg-slate-700
                                                      focus:outline-none focus:ring-4 focus:ring-slate-200">
                                                {{ __('manager.rooms_edit') }}
                                            </a>

                                            <form method="POST" action="{{ route('manager.rooms.destroy', $room) }}"
                                                  onsubmit="return confirm('{{ __('manager.rooms_delete_confirm', ['name' => addslashes($room->name)]) }}')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit"
                                                        class="inline-flex items-center justify-center rounded-xl px-3 py-1.5 text-xs font-semibold
                                                               bg-red-50 text-red-700 border border-red-200
                                                               hover:bg-red-100
                                                               dark:bg-red-950/30 dark:text-red-300 dark:border-red-800/60 dark:hover:bg-red-950/50
                                                               focus:outline-none focus:ring-4 focus:ring-red-200">
                                                    {{ __('manager.rooms_delete') }}
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-5 py-8 text-center text-slate-600 dark:text-slate-300">
                                        {{ __('manager.rooms_none_found') }}
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if ($rooms->hasPages())
                    <div class="px-5 py-4 border-t border-slate-200 dark:border-slate-800">
                        {{ $rooms->links() }}
                    </div>
                @endif
            </div>

        </div>
    </div>
</x-app-layout>
